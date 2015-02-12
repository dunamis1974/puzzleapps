<?php
/**
 * Language module
 * This module is preloaded by default
 */

global
    $LANGUAGES,
    $SYS_LANGUAGES,
    $CURRENTLANGUAGE,
    $CURRENTLANGUAGELT,
    $CURRENTENCODING,
    $DEFAULTLANGUAGE,
    $EDITLANGUAGE,
    $_TRANSLATION;

/**
 * Change current session language,
 * if request by GET has been made.
 */
if ($_GET["lang"]) {
    $_SESSION["USERLANGUAGE"] = $SYS_LANGUAGES[$_GET["lang"]]["id"];
    if ($GLOBALS["__ADMIN"] || $GLOBALS["_LANG_BACK"]) header('Location: ' . $_SERVER["HTTP_REFERER"]);
} 

/**
 * Set current session default language
 */
if (!$_SESSION["USERLANGUAGE"]) {
    $_SESSION["USERLANGUAGE"] = $SYS_LANGUAGES[$LANGUAGES[0]]["id"];
} 

/**
 * Set current session language
 */
$CURRENTLANGUAGE = $_SESSION["USERLANGUAGE"];

/**
 * Set current language letter
 */
for ($l = 0; $l < count($LANGUAGES); $l++) {
    if ($SYS_LANGUAGES[$LANGUAGES[$l]]["id"] == $CURRENTLANGUAGE) {
        $CURRENTLANGUAGELT = $LANGUAGES[$l];
        break;
    } 
}

$DEFAULTLANGUAGE = $SYS_LANGUAGES[$LANGUAGES[0]]["id"];

/**
 * Set encoding for current language
 */
if ($_GET["do"] == "add") {
    $_GET["language"] = __get_language_abr($DEFAULTLANGUAGE); 
    $CURRENTENCODING = $SYS_LANGUAGES[$_GET["language"]]["encoding"];
    $EDITLANGUAGE = $SYS_LANGUAGES[$_GET["language"]]["id"];
} else if ($_GET["language"] && $_GET["do"] != "add") {
    $CURRENTENCODING = $SYS_LANGUAGES[$_GET["language"]]["encoding"];
    $EDITLANGUAGE = $SYS_LANGUAGES[$_GET["language"]]["id"];
} else {
    $CURRENTENCODING = $SYS_LANGUAGES[$CURRENTLANGUAGELT]["encoding"];
    $EDITLANGUAGE = $SYS_LANGUAGES[$CURRENTLANGUAGELT]["id"];
}


/**
 * This function takes current platform language files
 * and parse them into an array
 */

$_TRANSLATION = __parse_lang();

function __parse_lang () {
    global $CURRENTLANGUAGELT, $COREROOT;

    /**
     * Parse system translations
     */
    $file = $COREROOT . "config/lang/" . $CURRENTLANGUAGELT . ".lang";
    if (file_exists($file)) {
        $list = file($file);
        foreach ($list AS $line) {
            list($key, $val) = explode("=", trim($line));
            $array[$key] = $val;
        }
    } 

    /**
     * Parse custom translations
     */
    $file = "./config/lang/" . $CURRENTLANGUAGELT . ".lang";
    if (file_exists($file)) {
        $list = file($file);
        foreach ($list AS $line) {
            list($key, $val) = explode("=", trim($line));
            $array[$key] = $val;
        }
    }
    
    return $array;
}

function __get_language_abr ($lang_id) {
    global $SYS_LANGUAGES;
    foreach ($SYS_LANGUAGES AS $ABR => $DATA)
        if ($DATA["id"] == $lang_id)
            break;
    return $ABR;
}

?>