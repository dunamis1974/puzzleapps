<?php
global $PDO_DATABASE;

if (!is_array($PDO_DATABASE)) {
    include_once($this->MODULEDIR . "DatabaseException.php");
    throw new DatabaseException('Database is not configured. Set $PDO_DATABASE in core/config/conf.d/database.conf.php or run the installer.');
}

$this->LoadDriver("pdodb");
$GLOBALS["DB"] = new DATABASE($PDO_DATABASE);
