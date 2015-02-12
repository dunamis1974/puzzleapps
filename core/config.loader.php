<?php

$CONFIG_DIR = $RUNNINGNDIR . "config/";

include_once ($COREROOT . "config/loader.config.php");

include_once ($CONFIG_DIR . "platform.config.php");

if ($SUB_PLATFORM) {
    $CONFIG_DIR = $DEFAULTFILEROOT . $SUB_PLATFORM . "config/";
    $FILEROOT = $DEFAULTFILEROOT . $SUB_PLATFORM . "files/";
}

$SYSTEMIMAGES = "./admin/images/";

?>