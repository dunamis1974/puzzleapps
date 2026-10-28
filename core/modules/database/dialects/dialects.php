<?php
/**
 * SQL dialects for the PDO DATABASE adapter.
 *
 * Dialects only produce SQL syntax (paging, identifier quoting). Row values
 * are never handled here; callers bind them as prepared-statement parameters.
 */

abstract class DatabaseDialect
{
    /** Table names from core/sql/structure.xml. */
    const TABLES = array(
        "data", "elm_date", "elm_number", "elm_text", "global",
        "groups", "platform", "relations", "users", "users_in_groups",
    );

    /** Column names from core/sql/structure.xml. */
    const COLUMNS = array(
        "_date", "_g", "_group", "_module", "_o", "_object", "_order",
        "_owner", "_platform", "_w", "_workflow", "_xslt", "_zone",
        "data", "descrption", "elm", "gid", "groupname", "hash", "id",
        "lang", "langid", "name", "note", "parentid", "password",
        "permissions", "platform", "relationtype", "targetid", "userid",
        "username",
    );

    /**
     * Validates limit and offset without any coercion: both must be true
     * non-negative integers because they become SQL syntax, not bound values.
     */
    protected function assertPaging($limit, $offset)
    {
        if (!is_int($limit) || !is_int($offset) || $limit < 0 || $offset < 0) {
            throw new DatabaseException("Paging limit and offset must be non-negative integers.");
        }
    }

    /**
     * @throws DatabaseException when $kind or $name is not allowlisted
     */
    public function assertIdentifier($name, $kind)
    {
        if ($kind === "table") {
            $allowed = self::TABLES;
        } elseif ($kind === "column") {
            $allowed = self::COLUMNS;
        } else {
            throw new DatabaseException("Unknown identifier kind.");
        }
        if (!is_string($name) || !in_array($name, $allowed, true)) {
            throw new DatabaseException("The {$kind} identifier is not allowed.");
        }
    }

    public function quoteIdentifier($name, $kind)
    {
        $this->assertIdentifier($name, $kind);
        return $this->quote($name);
    }

    abstract public function paginate($sql, $limit, $offset);

    abstract protected function quote($name);
}

class MysqlDialect extends DatabaseDialect
{
    public function paginate($sql, $limit, $offset)
    {
        $this->assertPaging($limit, $offset);
        return $sql . " LIMIT " . $offset . ", " . $limit;
    }

    protected function quote($name)
    {
        return "`" . $name . "`";
    }
}

class SqliteDialect extends DatabaseDialect
{
    public function paginate($sql, $limit, $offset)
    {
        $this->assertPaging($limit, $offset);
        return $sql . " LIMIT " . $limit . " OFFSET " . $offset;
    }

    protected function quote($name)
    {
        return '"' . $name . '"';
    }
}

class PgsqlDialect extends DatabaseDialect
{
    public function paginate($sql, $limit, $offset)
    {
        $this->assertPaging($limit, $offset);
        return $sql . " LIMIT " . $limit . " OFFSET " . $offset;
    }

    protected function quote($name)
    {
        return '"' . $name . '"';
    }
}
