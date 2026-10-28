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
    function getAll($sql) {
        $this->start_time = getmicrotime();
        $query = $this->conn->query($sql);
        $data = $query->fetchAll($this->fetchMode);
        $this->_debug_sql($sql, "getAll");
        return $data;
    } 

    /**
     * DATABASE::getRow()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function getRow($sql) {
        $this->start_time = getmicrotime();
        $query = $this->conn->query($sql);
        $data = $query->fetch($this->fetchMode);
        $this->_debug_sql($sql, "getRow");
        return $data;
    } 

    /**
     * DATABASE::query()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function query($sql) {
        $this->start_time = getmicrotime();
        $data = $this->conn->query($sql);
        $this->_debug_sql($sql, "query");
        return $data;
    } 

    /**
     * DATABASE::GetOne()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function GetOne($sql) {
        $this->start_time = getmicrotime();
        $query = $this->conn->query($sql);
        $data = $query->fetch();
        $this->_debug_sql($sql, "GetOne");
        return $data[0];
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
     * DATABASE::setFetchMode()
     * 
     * @param  $F 
     * @return NULL 
     * @access publis
     */
    function setFetchMode ($F) {
    	// Do nothing
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
