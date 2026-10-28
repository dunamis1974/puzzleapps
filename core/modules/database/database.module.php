<?php
global $PDO_DATABASE;

include_once($this->MODULEDIR . "DatabaseEnv.php");
$environmentDatabaseConfig = databaseConfigFromEnvironment();
if (is_array($environmentDatabaseConfig)) {
    $PDO_DATABASE = $environmentDatabaseConfig;
}

if (!is_array($PDO_DATABASE)) {
    include_once($this->MODULEDIR . "DatabaseException.php");
    throw new DatabaseException('Database is not configured. Set $PDO_DATABASE in core/config/conf.d/database.conf.php, configure the DB_* environment variables, or run the installer.');
}

$this->LoadDriver("pdodb");
$GLOBALS["DB"] = new DATABASE($PDO_DATABASE);
