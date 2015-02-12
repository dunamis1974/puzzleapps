<?php

global $UPDATE_SERVER, $MODULEDIR, $COREROOT;


if ($_GET["doupdate"]) {
    $_BODY .= "##Starting update ...##<br />";
    $_BODY .= "##Downloading file ...##<br />";
    $f = fopen("{$UPDATE_SERVER}core/core.zip", "r");
    $fp = fopen("{$GLOBALS["RUNNINGNDIR"]}tmp/core.zip", 'w');
    while(!feof($f)) {
        $data = fread($f,8192);
        fwrite($fp, $data);
        $_BODY .= ".";
    }
    $_BODY .= " ##done##<br />";
    
    fclose($fp);
    fclose($f);
    
    $_BODY .= "##Deploying the new version ...##";
    $zip = new dUnzip2("{$GLOBALS["RUNNINGNDIR"]}tmp/core.zip");

    $zip->getList();
    $zip->unzipAll($COREROOT);
    $_BODY .= " ##done##<br />";
} else {
    $_BODY .= isUpdatable() . "<br /><br />" . isUpdatableComponents();
} 
?>