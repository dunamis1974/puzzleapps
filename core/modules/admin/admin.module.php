<?php

global 
    $CORE, $DATA, 
    $TRANSLATE, 
    $CURRENTPLATFORM, $PLATFORMID, $CURRENTENCODING, 
    $CURRENTUSER, 
    $SYSTEMIMAGES, 
    $doFORM, $UFV, $XML, 
    $LANGUAGES, $SYS_LANGUAGES, $CURRENTLANGUAGE,
    $REMOTE_SEPARATOR, $REMOTE_LINESTART, $REMOTE_LINEEND, $REMOTE_CONFIG, $_REMOTE_TYPE, 
    $_SYSINDEX, 
    $_ADMN_MOD, 
    $_MEDIA_URL,
    $DB;

if (! $CURRENTUSER->isAllowed("admin")) {
    if (! preg_match('/admin\//', $_SERVER["PHP_SELF"])) {
        die("<script> document.location.replace('./admin/" . (($_SERVER["QUERY_STRING"] != '')?"{$_SYSINDEX}?{$_SERVER["QUERY_STRING"]}":"") . "'); </script>");
    }
    include ("login.inc.php");
    die();
}

if (preg_match('/admin\//', $_SERVER["PHP_SELF"])) {
    die("<script> document.location.replace('" . (($_SERVER["QUERY_STRING"] != '')?"../?{$_SERVER["QUERY_STRING"]}":"../{$_SYSINDEX}?admin[]=home") . "'); </script>");
}

include_once ($MODULEDIR . "conf/modules.conf.php");
include ($MODULEDIR . "drivers/general.driver.php");

include ($MODULEDIR . "drivers/dUnzip2.driver.php");

include ($MODULEDIR . "drivers/interface.driver.php");

include ($MODULEDIR . "drivers/lang.driver.php");

if (count($_GET["do"]) || $_GET["simple"]) {
    include ($MODULEDIR . "libs/top_normal.lib.php"); 
} else {
    include ($MODULEDIR . "libs/top.lib.php");
}

include ($MODULEDIR . "libs/body.lib.php");

include ($MODULEDIR . "libs/bottom.lib.php");

echo $TRANSLATE->Go($_BODY);

?>