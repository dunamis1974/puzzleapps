<?php
?>
<table align="center" width="500" cellspacing="0" cellpadding="3" class="">
<tr><td class="title" colspan="2">Create database schema</td></tr>
<tr><td class="item" colspan="2">
    Database settings are validated, saved as a PDO configuration, and used to create the tables and indexes from the canonical schema.
</td></tr>
<tr><td class="item" colspan="2">
<?php
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $databaseConfig = installerValidateDatabaseSettings($_POST);
        require_once $COREROOT . "modules/database/DatabaseException.php";
        require_once $COREROOT . "modules/database/drivers/pdodb.driver.php";
        require_once __DIR__ . "/../lib/schema.php";

        $database = new DATABASE($databaseConfig);
        installerCreateSchema($database, $databaseConfig["driver"], $COREROOT . "sql/structure.xml");
        installerWriteDatabaseConfig($databaseConfig);
        $PDO_DATABASE = $databaseConfig;
        $GLOBALS["DB"] = $database;
        echo '<b class="ok">' . $okImg . '</b> Schema created and PDO configuration saved.';
    } catch (InvalidArgumentException $exception) {
        $error = true;
        echo '<span class="error">' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, "UTF-8") . '</span>';
    } catch (Throwable $exception) {
        $error = true;
        echo '<span class="error">' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, "UTF-8") . '</span>';
    }
} else {
    $error = true;
    echo '<span class="error">Submit the database settings from Step 2 to continue.</span>';
}
?>
</td></tr>
</table>
<?php
function installerValidateDatabaseSettings(array $input) {
    foreach (array("driver", "host", "port", "database", "user", "password", "sqlite_path", "charset") as $field) {
        if (array_key_exists($field, $input) && !is_string($input[$field])) {
            throw new InvalidArgumentException("Database settings must be text values.");
        }
    }
    $drivers = array("mysql", "sqlite", "pgsql");
    $driver = isset($input["driver"]) ? $input["driver"] : "";
    if (!in_array($driver, $drivers, true)) {
        throw new InvalidArgumentException("Select a supported database driver.");
    }
    if (!extension_loaded("PDO") || !in_array($driver, PDO::getAvailableDrivers(), true)) {
        throw new InvalidArgumentException("The selected PDO driver is not available in this PHP installation.");
    }

    $config = array(
        "driver" => $driver,
        "host" => isset($input["host"]) ? trim($input["host"]) : "",
        "port" => "",
        "database" => isset($input["database"]) ? trim($input["database"]) : "",
        "user" => isset($input["user"]) ? $input["user"] : "",
        "password" => isset($input["password"]) ? $input["password"] : "",
        "sqlite_path" => isset($input["sqlite_path"]) ? trim($input["sqlite_path"]) : "",
        "charset" => isset($input["charset"]) ? trim($input["charset"]) : "",
    );

    if ($driver === "sqlite") {
        if ($config["sqlite_path"] === "") {
            throw new InvalidArgumentException("Provide a SQLite database path or :memory:.");
        }
        if ($config["charset"] === "") {
            $config["charset"] = "UTF8";
        }
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $config["charset"])) {
            throw new InvalidArgumentException("Character set may contain only letters, numbers, underscores, and hyphens.");
        }
        return $config;
    }

    foreach (array("host" => $config["host"], "database" => $config["database"]) as $field => $value) {
        if ($value === "") {
            throw new InvalidArgumentException("Provide both a database host and database name.");
        }
        if (strpbrk($value, ";=") !== false) {
            throw new InvalidArgumentException("Host and database name cannot contain semicolons or equals signs.");
        }
    }
    $defaultPort = $driver === "mysql" ? 3306 : 5432;
    $port = isset($input["port"]) ? trim($input["port"]) : "";
    if ($port === "") {
        $port = (string)$defaultPort;
    }
    if (!preg_match('/^[0-9]+$/D', $port) || (int)$port < 1 || (int)$port > 65535 || (string)(int)$port !== $port) {
        throw new InvalidArgumentException("Port must be an integer between 1 and 65535.");
    }
    $config["port"] = (int)$port;
    if ($config["charset"] === "") {
        $config["charset"] = $driver === "mysql" ? "utf8mb4" : "UTF8";
    }
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $config["charset"])) {
        throw new InvalidArgumentException("Character set may contain only letters, numbers, underscores, and hyphens.");
    }
    return $config;
}

function installerWriteDatabaseConfig(array $config) {
    $target = dirname(__DIR__, 2) . "/core/config/conf.d/database.conf.php";
    $contents = "<?php\n\n\$PDO_DATABASE = " . var_export($config, true) . ";\n";
    $written = file_put_contents($target, $contents, LOCK_EX);
    if ($written === false || $written !== strlen($contents)) {
        throw new RuntimeException("Unable to write the database configuration.");
    }
}
?>
