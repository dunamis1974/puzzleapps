<?
$_is_loaded = true;

// Using XML dtds
$DTDDRIVER = "xmldtd";

// What is the type of configuration?
// text, xml, php
$CONFIGURATION_TYPE = "php";

// Add objects on ....
$ADDOBJECTONTOP = true;

// Load all other configs of modules and ....
$CONFIGDIR = dirname(__FILE__) . DIRECTORY_SEPARATOR;
if (file_exists($CONFIGDIR . "loader.config.php")) include_once($CONFIGDIR . "loader.config.php");

// DONE!!! config is loaded
if ($_is_loaded == true) $configuration_is_loaded = true;

?>