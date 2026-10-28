<?php
require_once dirname(__DIR__, 2) . "/core/modules/database/DatabaseException.php";
require_once dirname(__DIR__, 2) . "/core/modules/database/dialects/dialects.php";

function installSchemaValidateIdentifier($name, $kind) {
    if (!is_string($name) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $name)) {
        throw new InvalidArgumentException("Invalid schema {$kind} identifier.");
    }
}

function installSchemaDialect($driver) {
    $dialects = array(
        "mysql" => "MysqlDialect",
        "sqlite" => "SqliteDialect",
        "pgsql" => "PgsqlDialect",
    );
    if (!isset($dialects[$driver])) {
        throw new InvalidArgumentException("Unsupported schema database driver.");
    }
    $class = $dialects[$driver];
    return new $class();
}

function installSchemaQuoteIndex($name, $driver) {
    installSchemaValidateIdentifier($name, "index");
    $quote = $driver === "mysql" ? "`" : '"';
    return $quote . $name . $quote;
}

function installSchemaDefault($field, $type) {
    if (!isset($field->DEFAULT)) {
        return null;
    }
    $value = isset($field->DEFAULT[0]["value"]) ? (string)$field->DEFAULT[0]["value"] : "";
    if (strtoupper($value) === "NULL") {
        return "NULL";
    }
    if ($type === "I") {
        if (!preg_match('/^-?(?:0|[1-9][0-9]*)$/D', $value)) {
            throw new InvalidArgumentException("Integer schema defaults must be integer literals.");
        }
        return $value;
    }
    return "'" . str_replace("'", "''", $value) . "'";
}

function installSchemaStatements($driver, $schemaPath) {
    $dialect = installSchemaDialect($driver);
    if (!is_file($schemaPath) || !is_readable($schemaPath)) {
        throw new RuntimeException("The canonical schema file is not readable.");
    }
    $xml = @simplexml_load_file($schemaPath, "SimpleXMLElement", LIBXML_NONET | LIBXML_NOBLANKS);
    if ($xml === false || $xml->getName() !== "schema") {
        throw new RuntimeException("The canonical schema XML is invalid.");
    }

    $statements = array();
    $indexes = array();
    $tableNames = array();
    foreach ($xml->table as $table) {
        $tableName = (string)$table["name"];
        installSchemaValidateIdentifier($tableName, "table");
        $dialect->assertIdentifier($tableName, "table");
        if (isset($tableNames[$tableName])) {
            throw new InvalidArgumentException("Duplicate schema table name.");
        }
        $tableNames[$tableName] = true;
        $quotedTable = $dialect->quoteIdentifier($tableName, "table");
        $definitions = array();
        $fields = array();
        $primaryKeys = 0;

        foreach ($table->field as $field) {
            $fieldName = (string)$field["name"];
            $type = strtoupper((string)$field["type"]);
            installSchemaValidateIdentifier($fieldName, "field");
            $dialect->assertIdentifier($fieldName, "column");
            if (isset($fields[$fieldName])) {
                throw new InvalidArgumentException("Duplicate schema field name.");
            }
            $fields[$fieldName] = true;
            if (!in_array($type, array("I", "C", "TEXT", "DATE"), true)) {
                throw new InvalidArgumentException("Unsupported schema field type.");
            }

            $isKey = isset($field->KEY);
            $autoIncrement = isset($field->AUTOINCREMENT);
            if ($autoIncrement && (!$isKey || $type !== "I")) {
                throw new InvalidArgumentException("Auto-increment fields must be integer primary keys.");
            }
            if ($isKey) {
                $primaryKeys++;
                if ($primaryKeys > 1) {
                    throw new InvalidArgumentException("Composite primary keys are not supported by this schema.");
                }
            }

            if ($autoIncrement) {
                if ($driver === "mysql") {
                    $sqlType = "INTEGER PRIMARY KEY AUTO_INCREMENT";
                } elseif ($driver === "pgsql") {
                    $sqlType = "SERIAL PRIMARY KEY";
                } else {
                    $sqlType = "INTEGER PRIMARY KEY AUTOINCREMENT";
                }
            } else {
                if ($type === "I") {
                    $sqlType = "INTEGER";
                } elseif ($type === "C") {
                    $size = (string)$field["size"];
                    if (!preg_match('/^[1-9][0-9]*$/D', $size) || (int)$size > 65535) {
                        throw new InvalidArgumentException("Character fields require a valid positive size.");
                    }
                    $sqlType = "VARCHAR(" . (int)$size . ")";
                } elseif ($type === "TEXT") {
                    $sqlType = "TEXT";
                } else {
                    $sqlType = $driver === "sqlite" ? "TEXT" : "DATE";
                }
                if ($isKey) {
                    $sqlType .= " PRIMARY KEY";
                }
                if (isset($field->NOTNULL)) {
                    $sqlType .= " NOT NULL";
                }
                $default = installSchemaDefault($field, $type);
                if ($default !== null) {
                    $sqlType .= " DEFAULT " . $default;
                }
            }
            $definitions[] = $dialect->quoteIdentifier($fieldName, "column") . " " . $sqlType;
        }
        if (!$definitions) {
            throw new InvalidArgumentException("Schema tables must define at least one field.");
        }
        $statements[] = array(
            "object" => "table " . $tableName,
            "sql" => "CREATE TABLE " . $quotedTable . " (" . implode(", ", $definitions) . ")",
        );

        foreach ($table->index as $index) {
            $indexName = (string)$index["name"];
            installSchemaValidateIdentifier($indexName, "index");
            $columns = array();
            foreach ($index->col as $column) {
                $columnName = (string)$column;
                installSchemaValidateIdentifier($columnName, "index column");
                if (!isset($fields[$columnName])) {
                    throw new InvalidArgumentException("An index references a field missing from its parent table.");
                }
                $columns[] = $dialect->quoteIdentifier($columnName, "column");
            }
            if (!$columns) {
                throw new InvalidArgumentException("Schema indexes must reference at least one field.");
            }
            $indexes[] = array(
                "object" => "index " . $indexName,
                "sql" => "CREATE INDEX " . installSchemaQuoteIndex($indexName, $driver) . " ON " . $quotedTable . " (" . implode(", ", $columns) . ")",
            );
        }
    }
    if (!$statements) {
        throw new RuntimeException("The canonical schema contains no tables.");
    }
    return array_merge($statements, $indexes);
}

function installerCreateSchema($database, $driver, $schemaPath) {
    foreach (installSchemaStatements($driver, $schemaPath) as $statement) {
        try {
            $database->execute($statement["sql"]);
        } catch (Throwable $exception) {
            throw new RuntimeException("Failed to create schema " . $statement["object"] . ".", 0, $exception);
        }
    }
}
?>
