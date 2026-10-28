<?php
/**
 * DATABASE
 * This class extends PDO::DB
 * This is debug hack, nothing more.
 * 
 * @package Puzzle Apps
 * @author DuNaMiS 
 * @copyright Copyright (c) 2015
 * @version 0.1
 * @access public 
 */

class DATABASE {
    
    private $fetchMode = PDO::FETCH_OBJ;
    public $conn;
    public $start_time;
    public $end_time;
    public $waste = 0;
    public $total_time = 0;
    public $sqldebug = "";
    public $sqlNo = 1;

    const DRIVERS = array("mysql", "sqlite", "pgsql");
    
    /**
     * DATABASE::__construct()
     * 
     * @param array $config driver, host, port, database, user, password, sqlite_path, charset
     * @throws DatabaseException
     */
    function __construct (array $config) {
        $this->start_time = getmicrotime();
        $driver = isset($config["driver"]) ? $config["driver"] : "";
        if (!is_string($driver) || !in_array($driver, self::DRIVERS, true)) {
            throw new DatabaseException("Unsupported database engine '" . (is_scalar($driver) ? $driver : gettype($driver)) . "'. Supported engines: " . implode(", ", self::DRIVERS) . ".");
        }
        if (!in_array($driver, PDO::getAvailableDrivers(), true)) {
            throw new DatabaseException("The PDO driver 'pdo_{$driver}' is not available in this PHP installation.");
        }
        $dsn = $this->_buildDsn($driver, $config);
        $user = isset($config["user"]) && $config["user"] !== "" ? (string)$config["user"] : null;
        $pass = isset($config["password"]) && $config["password"] !== "" ? (string)$config["password"] : null;
        try {
            $this->conn = new PDO($dsn, $user, $pass, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ));
        } catch (PDOException $e) {
            throw new DatabaseException("Unable to connect to the {$driver} database.", 0, $e);
        }
        $this->_debug_sql("Initiate DB connection!", "DATABASE");
    } 

    /**
     * Builds the PDO DSN from discrete fields. Credentials are never part of the DSN.
     *
     * @param string $driver
     * @param array $config
     * @return string
     * @throws DatabaseException
     */
    private function _buildDsn ($driver, array $config) {
        if ($driver == "sqlite") {
            $path = isset($config["sqlite_path"]) ? (string)$config["sqlite_path"] : "";
            if ($path === "") {
                throw new DatabaseException("The sqlite configuration requires 'sqlite_path'.");
            }
            if ($path !== ":memory:") {
                $isAbsolute = $path[0] === "/" || $path[0] === "\\" || preg_match('/^[A-Za-z]:[\\\\\/]/', $path);
                if (!$isAbsolute) {
                    global $RUNNINGNDIR;
                    $path = $RUNNINGNDIR . $path;
                }
            }
            return "sqlite:" . $path;
        }

        $name = isset($config["database"]) ? (string)$config["database"] : "";
        $host = isset($config["host"]) ? (string)$config["host"] : "";
        if ($host === "" || $name === "") {
            throw new DatabaseException("The {$driver} configuration requires 'host' and 'database'.");
        }
        foreach (array("host" => $host, "database" => $name) as $field => $value) {
            if (strpbrk($value, ";=") !== false) {
                throw new DatabaseException("The '{$field}' value contains characters that are not allowed.");
            }
        }
        $defaultPort = $driver == "mysql" ? 3306 : 5432;
        $port = isset($config["port"]) && $config["port"] !== "" ? $config["port"] : $defaultPort;
        if (!is_numeric($port) || (int)$port < 1 || (int)$port > 65535 || (string)(int)$port !== (string)$port) {
            throw new DatabaseException("The {$driver} 'port' must be an integer between 1 and 65535.");
        }
        $charset = isset($config["charset"]) && $config["charset"] !== "" ? (string)$config["charset"] : ($driver == "mysql" ? "utf8mb4" : "UTF8");
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $charset)) {
            throw new DatabaseException("The 'charset' value contains characters that are not allowed.");
        }
        if ($driver == "mysql") {
            return "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
        }
        return "pgsql:host={$host};port={$port};dbname={$name};options='--client_encoding={$charset}'";
    }

    /**
     * DATABASE::getAll()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function getAll($sql, array $params = array()) {
        return $this->fetchAll($sql, $params);
    } 

    /**
     * Temporary alias for fetchRow().
     */
    function getRow($sql, array $params = array()) {
        return $this->fetchRow($sql, $params);
    } 

    /**
     * Temporary alias for execute().
     */
    function query($sql, array $params = array()) {
        return $this->execute($sql, $params);
    } 

    /**
     * Temporary alias for fetchValue().
     */
    function GetOne($sql, array $params = array()) {
        return $this->fetchValue($sql, $params);
    } 

    /**
     * Prepares and runs a statement with bound values.
     *
     * @param string $sql
     * @param array $params
     * @param string $label debug label
     * @return PDOStatement
     * @throws DatabaseException
     */
    private function _run($sql, array $params, $label) {
        $this->start_time = getmicrotime();
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            $this->_debug_sql($sql, $label);
            throw new DatabaseException("Database query failed.", 0, $e);
        }
        $this->_debug_sql($sql, $label);
        return $stmt;
    }

    /**
     * @return array list of associative rows
     * @throws DatabaseException
     */
    function fetchAll($sql, array $params = array()) {
        return $this->_run($sql, $params, "fetchAll")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array|null one associative row, or null when there is none
     * @throws DatabaseException
     */
    function fetchRow($sql, array $params = array()) {
        $row = $this->_run($sql, $params, "fetchRow")->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /**
     * @return mixed first column of the first row; null when no row or SQL NULL
     * @throws DatabaseException
     */
    function fetchValue($sql, array $params = array()) {
        $stmt = $this->_run($sql, $params, "fetchValue");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        return $row === false ? null : $row[0];
    }

    /**
     * @return int affected rows
     * @throws DatabaseException
     */
    function execute($sql, array $params = array()) {
        return $this->_run($sql, $params, "execute")->rowCount();
    }

    /**
     * @throws DatabaseException
     */
    function begin() {
        try {
            $this->conn->beginTransaction();
        } catch (PDOException $e) {
            throw new DatabaseException("Unable to begin the database transaction.", 0, $e);
        }
    }

    /**
     * @throws DatabaseException
     */
    function commit() {
        try {
            $this->conn->commit();
        } catch (PDOException $e) {
            throw new DatabaseException("Unable to commit the database transaction.", 0, $e);
        }
    }

    /**
     * @throws DatabaseException
     */
    function rollback() {
        try {
            $this->conn->rollBack();
        } catch (PDOException $e) {
            throw new DatabaseException("Unable to roll back the database transaction.", 0, $e);
        }
    }

    /**
     * Generated id of the last insert on this connection.
     * PostgreSQL uses LASTVAL() so no sequence name is needed.
     *
     * @return string|null
     * @throws DatabaseException
     */
    function lastId(): ?string {
        try {
            if ($this->conn->getAttribute(PDO::ATTR_DRIVER_NAME) == "pgsql") {
                $id = $this->conn->query("SELECT LASTVAL()")->fetchColumn();
            } else {
                $id = $this->conn->lastInsertId();
            }
        } catch (PDOException $e) {
            throw new DatabaseException("Unable to read the last generated id.", 0, $e);
        }
        return ($id === false || $id === null || $id === "") ? null : (string)$id;
    }


    /**
     * DATABASE::limitQuery()
     * 
     * @param  $sql 
     * @param  $opt 
     * @param  $opt2 
     * @return array $data
     * @access publis
     */
    function limitQuery($sql, $opt, $opt2) {
        $this->start_time = getmicrotime();
        $sql = $sql .  " LIMIT $opt2 OFFSET $opt";
        $query = $this->conn->query($sql);
        $data = $query->fetchAll($this->fetchMode);
        $this->_debug_sql($sql, "limitQuery");
        return $data;
    } 

    /**
     * DATABASE::_debug_sql()
     * 
     * @param  $sql 
     * @return NULL 
     * @access private 
     */
    function _debug_sql ($sql, $F) {
        global $__SQL;
        $__SQL = $sql;
        if ($_GET["sqldebug"] == "query") {
            if ($this->end_time) {
                $prev_time = round(($this->start_time - $this->end_time), 5);
                $this->waste += $prev_time;
            } 
            $this->end_time = getmicrotime();
            $ex_time = round(($this->end_time - $this->start_time), 5);
            $this->total_time += $ex_time;
            $this->sqldebug .= "___________<br /><b>" . $this->sqlNo . ".</b> " . $F . ": <pre style=\"font-size: 10px; font-family:Verdana, Arial, Helvetica, sans-serif;\">" . $sql . "</pre><br /> Query executed for: " . $ex_time . " s. Previews query was executed " . (($prev_time)?$prev_time:0) . " s. ago<br />";
            $this->sqlNo++;
        }
    }
    
    /**
     * DATABASE::_do_global_sql()
     * 
     * @param  $sql 
     * @return NULL 
     * @access private 
     */
    function _do_global_sql ($sql) {
        global $__SQL;
        $__SQL = $sql;
    }
} 
