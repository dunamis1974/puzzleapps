<?php

$MODULE_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once($MODULE_DIR . "drivers/generator.inc.php");

function gsitemap_hook_after ($id, $action = false) {
    
    $XML = gsitemap_generate ();
    
    $file_name = "sitemap.xml";
    if ($handle = fopen($file_name, 'a')) {
        fwrite($handle, $XML);
        fclose($handle);
    } else {
        return false;
    }
    
    return true;
}

?>