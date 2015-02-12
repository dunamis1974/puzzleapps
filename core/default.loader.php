<?php
/**
 * Load default modules
 */

$_MOD_FOO = new Modules();

$_ERRORHANLER = $_MOD_FOO->LoadModule("errorlog");

if ($_JPCACHE)
    $_CACHE = $_MOD_FOO->LoadModule("jpcache");

if ($_WEBSTATLOG)
    $_WEBSTAT = $_MOD_FOO->LoadModule("webstat");
    
/*
 * ToDo: create module dependencies
 */
//if ($_WRAPPER)
    $_WRAPPER = $_MOD_FOO->LoadModule("wrapper");

$_DATABASE = $_MOD_FOO->LoadModule("database");
$_DTD = $_MOD_FOO->LoadModule("dtd");
$_FORM = $_MOD_FOO->LoadModule("form");
$_DATE = $_MOD_FOO->LoadModule("date");
$_LANG = $_MOD_FOO->LoadModule("language");

?>