<?php
/**
 * Database abstraction layer driver
 * i.e. peardb, adodb
 *
 * You should use adodb for installation procedure
 */
$DB_DBAL = "adodb";

/**
 * Explicit PDO configuration. null keeps the legacy $DB_DBAL selection.
 * An array activates the PDO path, e.g.:
 * array("driver" => "mysql", "host" => "localhost", "port" => 3306,
 *       "database" => "puzzle", "user" => "", "password" => "",
 *       "sqlite_path" => "", "charset" => "utf8mb4")
 * driver is one of: mysql, sqlite, pgsql
 */
$PDO_DATABASE = null;

?>