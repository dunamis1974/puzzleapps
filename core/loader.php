<?php

/**
 * This file loads the whole system
 */

/**
 * getmicrotime()
 * 
 * @return string 
 * @access public 
 */
function getmicrotime()
{
    if ($_GET["debug"] || $_GET["sqldebug"]) {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    } else {
        return 0;
    }
}

$time_start = getmicrotime();

$COREROOT = dirname(__FILE__) . DIRECTORY_SEPARATOR;

$_ADD_PATH = (($_DOINSTALL)?"../":"");

$RUNNINGNDIR = $_SERVER["DOCUMENT_ROOT"] . substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"], '/')) . "/" . $_ADD_PATH;
if (substr($RUNNINGNDIR, strlen($RUNNINGNDIR) - 6) == 'admin/')
    $RUNNINGNDIR = substr($RUNNINGNDIR, 0, strrpos($RUNNINGNDIR, 'admin/'));
$DEFAULTFILEROOT = $RUNNINGNDIR;

define("LOGDATA", $_SERVER["DOCUMENT_ROOT"] . $_ADD_PATH);

$PLATFORM_PATH = getcwd() . $_ADD_PATH;

$FILEROOT = $DEFAULTFILEROOT . "files/";

/*
 * This should remove the PHPSESSID from URL
 * If it is not working try this:
 * ini_set('url_rewriter.tags', '');
 */
ini_set('url_rewriter.tags', '');

/*
 * And now starting the session
 */
session_start();

include_once ($COREROOT . "module.loader.php");

include_once ($COREROOT . "config.loader.php");

include_once ($COREROOT . "default.loader.php");

include_once ($COREROOT . "core.loader.php");

include_once ($COREROOT . "filepresenter.loader.php");

include_once ($COREROOT . "pre.loader.php");

$DEFAULTXSLTROOT = $RUNNINGNDIR . $_GENERAL_XSL_FOLDER;
$XSLTROOT = $DEFAULTXSLTROOT;

/**
 * This code redirects to site root if user is logged
 */
if ($REDIRECT || $_POST["back"] || $_GET["back"]) {
    $dir_ = explode("?", $_SERVER["REQUEST_URI"]);
    $dir = $dir_[0];
    if (($_POST["back"] == "true") || ($_GET["back"] == "true")) {
        // This will return You to the previews page
        $location = $_SERVER["HTTP_REFERER"];
    } else {
        // This will return You to the home page of the system
        $location = "http://" . $_SERVER["HTTP_HOST"] . "" . $dir;
    }
    // Do redirect
    header("Location:$location");
    die();
}

?>