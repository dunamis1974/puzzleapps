<?
/**
 * This loads current platform configurations
 * They can be in XML or in PHP arrays
 */

$dir = $CONFIGDIR . "conf.d" . DIRECTORY_SEPARATOR;

if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if (!is_dir($dir . $file)) {
            if ($CONFIGURATION_TYPE == "xml") {
                // If XML configuration is used
                unset($conf);
                $TITLE = strtoupper(ereg_replace(".xml", "", $file));
                $_conf = $XML->toArray($dir . $file);
                $__end = count($_conf[0]["data"][0]["data"]);
                for ($i = 0; $i < $__end; $i++) $conf[] = $_conf[0]["data"][0]["data"][$i]["attributes"]["name"];
                $$TITLE = $conf;
            } elseif ($CONFIGURATION_TYPE == "text") {
                // If text configuration is used
                $TITLE = strtoupper(ereg_replace(".conf", "", $file));
                $fp = fopen ($dir . $file, "r");
                $$TITLE = fgetcsv($fp, 10000, ";");
                fclose($fp);
            } else {
                // If PHP configuration is used
                if (!include_once($dir . $file)) $_is_loaded = false;
            } 
        } 
    } 
    closedir($handle);
}
?>
