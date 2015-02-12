<?php

$CONFIGDIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . "conf.d" . DIRECTORY_SEPARATOR;
if ($handle = opendir($CONFIGDIR)) {
    while (false !== ($file = readdir($handle))) {
        if (! is_dir($CONFIGDIR . $file)) {
            if (! include_once ($CONFIGDIR . $file))
                $_is_loaded = false;
        }
    }
    closedir($handle);
}

?>