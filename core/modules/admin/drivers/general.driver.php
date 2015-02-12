<?php
/**
 * General Driver
 * 
 */

/**
 * BuildLink()
 * 
 * @return string $PATH
 * @access public 
 */
function BuildLink ($PATH) {
    global
        $REMOTE_CONFIG;
        
    $_PATH = explode(":", $PATH);
    $end = (count($_PATH) - 1);
    $link = "?";
    for ($i = 0; $i < $end; $i++) {
        $link .= "admin[]=" . $_PATH[$i];
        if ($i < ($end - 1)) $link .= "&";
    }
    return $link;
}

/**
 * BuildLinkGet()
 * 
 * @access public 
 */
function BuildLinkGet () {
    
    $end = count($_GET["admin"]);
    
    if ($end == 0) {
        $end = 1;
        $_GET["admin"][0] = "home";
    }
    
    $link = "?";
    for ($i = 0; $i < $end; $i++) {
        $link .= "admin[]=" . $_GET["admin"][$i];
        if ($i < ($end - 1)) $link .= "&";
    }
    return $link;
}

/**
 * DoPath()
 * 
 * @return string $PATH
 * @access public 
 */
function DoPath () {
    global
        $CURRENTUSER,
        $SYSTEMIMAGES,
        $REMOTE_CONFIG,
        $HELP_SERVER;

    if ($CURRENTUSER->isAllowed("admin")) {
        $PATH_ = $_GET["admin"];
        $end = count($PATH_);
        $DATA = $REMOTE_CONFIG;
        for ($i = 0; $i < $end; $i++) {
            $LINK .= "admin[]=" . $PATH_[$i];
            if ($DATA[$PATH_[$i]]["custom"]) $LINK .= "&custom=" . $DATA[$PATH_[$i]]["custom"];
            if ($i > 0) $PATH .= " > ";
            $PATH .= "<a href=\"?" . $LINK . "\">##" . $DATA[$PATH_[$i]]["link"] . "##</a>";
            if ($i < ($end - 1)) $LINK .= "&";
            $DATA = $DATA[$PATH_[$i]]["submenu"];
        }
        
        if ($HELP_SERVER) {
            $tid = implode(".",$_GET["admin"]);
            $PATH .= "&nbsp;<a href=\"javascript:popup('" . $HELP_SERVER . $tid . "&host=" . $_SERVER["HTTP_HOST"] . "', 550, 550)\"><img src=\"" . $SYSTEMIMAGES . "16x16/help.png\" width=\"16\" height=\"16\" align=\"absmiddle\" border=\"0\" title=\"Help: $tid\" /></a>";
        }
        return $PATH;
    }
}

/**
 * GetIcon()
 * 
 * @return string
 * @access public 
 */
function GetIcon() {
    global
        $SYSTEMIMAGES,
        $REMOTE_CONFIG;
    
    $PATH_ = $_GET["admin"];
    $end = count($PATH_) - 1;
    $DATA = $REMOTE_CONFIG;
    for ($i = 0; $i < $end; $i++) {    
        $DATA = $DATA[$PATH_[$i]]["submenu"];
    }
    if ($_GET["do"]) {
        if ($_GET["do"] == "delete") return $SYSTEMIMAGES . "32x32/editdelete.png";
        return $SYSTEMIMAGES . "32x32/edit.png";
    } elseif ($DATA[$_GET["admin"][($end)]]["bicon"]) {
        return $SYSTEMIMAGES . $DATA[$_GET["admin"][($end)]]["bicon"];
    } else {
        return $SYSTEMIMAGES . "32x32/default.png";
    }
}
/**
 * ViewObject()
 * 
 * @param  $obj 
 * @param unknown $ELM 
 * @return 
 */
function ViewObject ($obj, $ELM = null) {
    $XML = new XML;

    if (!$ELM) GetDTDElements($obj->_dtd);

    $_XMLDATA = $XML->xml2array($obj->data);
    $DATA = $_XMLDATA[0]["data"][0]["data"];

    $body = "<table class=\"object__\" border=\"0\" width=\"100%\">";
    $body .= "<tr><td class=\"objecttitle__\">id:<i>" . $obj->id . "</i> zone:<i>" . $obj->_zone . "</i> object:<i>" . $obj->_objectname . "</i> date:<i>" . date("r", $obj->_date) . "</i></td></tr>";
    for ($i = 0; $i < count($DATA); $i++) {
        if ($ELM[$DATA[$i]["element_name"]]) {
            $body .= "<tr><td class=\"objecttitle__\">" . $ELM[$DATA[$i]["element_name"]] . "</td></tr>";
            //print_r($ELM);
            if ($DATA[$i]["element_name"] == "password")
                $DATA[$i]["data"] = "***************";
            $body .= "<tr><td class=\"objectvalue__\">" . $DATA[$i]["data"] . "</td></tr>";
        } 
    } 
    $body .= "</table>";

    return $body;
} 

/**
 * DownloadZIP()
 *
 * @param string $FOLDER
 * @param string $NAME
 * @return NULL
 */
function DownloadZIP ($FOLDER, $NAME="files.zip") {
    $ZIP = Modules::LoadModule("zip");
    $zipfile = new zipfile;
    if ($dir_ = @opendir($FOLDER)) {
        while (($file = readdir($dir_)) !== false) {
            if (is_file($FOLDER . $file)) {
                $print = implode ('', file($FOLDER . $file));
                $temp = fopen ($FOLDER . $file, "rb");
                $print = fread ($temp, filesize ($FOLDER . $file));
                fclose ($temp);
                $zipfile->add_file($print, $file);
            } 
        } 
        closedir($dir_);
    }
    
    header("Content-disposition: attachment; filename=$NAME");
    header("Content-type: application/zip");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $zipfile->file();
    die();
}

/**
 * BuildErrorMsg()
 *
 * @param string $msg
 * @return string $ERR_
 */
function BuildErrorMsg ($msg) {
    $_msg = ereg_replace("\n", "<li>", $msg);
    $ERR_ .= "<div class=\"block_error_\">
    <table>
    <tr>
    <td width=\"50\" align=\"center\"><span class=\"error_title__\">!</span></td>
    <td><nobr>
    <span class=\"error__\"><li>" . $_msg . "</span>
    </nobr></td></tr></table></div><p />";
    
    return $ERR_;
}

/**
 * BuildNoteMsg()
 *
 * @param string $msg
 * @return string $NOTE_
 */
function BuildNoteMsg ($msg) {
    //$_msg = ereg_replace("\n", "<li>", $msg);
    $NOTE_ .= "<div class=\"block_note_\"><span class=\"note__\">" . $msg . "</span></div><p />";
    
    return $NOTE_;
}

/**
 * CreateConfigText()
 *
 * @param $VAR
 * @return string
 */
function CreateConfigText ($VAR) {
    if (is_array($VAR)) return Array2Text($VAR);
    else return "\"$VAR\"";
}

/**
 * Array2Text()
 *
 * @param array $VAR
 * @param string $ADD
 * @return string $BODY
 */
function Array2Text ($VAR, $ADD = NULL) {
    $BODY .= "array(\n";
    foreach($VAR AS $KEY => $VAL) {
        if (!is_numeric($KEY)) $KEY = "\"" . $KEY . "\"";
        if (is_array($VAL)) {
            $BODY .= $ADD . "    " . $KEY . " => " . Array2Text($VAL, $ADD."    ") . ",\n";
        } else {
            $BODY .= $ADD . "    " . $KEY . " => \"" . $VAL . "\",\n";
        }
    }
    $BODY .= $ADD . ")";
    return $BODY;
}

/**
 * DeleteConfigFile()
 *
 * @param string $conf
 * @return boolean
 */
function DeleteConfigFile ($conf) {
    $_CONFFILE = $GLOBALS["CONFIG_DIR"] . "conf.d/" . $conf . ".conf.php";
    if (unlink($_CONFFILE))
        return true;
    else
        return false;
}

/**
 * WriteConfigFile()
 *
 * @param string $conf
 * @param string $data
 * @return boolean
 */
function WriteConfigFile ($conf, $data) {
    $_CONFFILE = $GLOBALS["CONFIG_DIR"] . "conf.d/" . $conf . ".conf.php";
    if ($fp = fopen($_CONFFILE, "w")) {
        fwrite($fp, $data);
        fclose ($fp);
        chmod($_CONFFILE, 0666);
        return true;
    } else {
        return false;
    }
}

function CleanTempFiles ($_CACHEDIR = NULL) {
    if (!$_CACHEDIR) $_CACHEDIR = $GLOBALS["RUNNINGNDIR"] . "tmp";
    if (file_exists($CACHEDIR_)) {
        if ($handle = opendir($CACHEDIR_)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") { 
                    if (!is_dir($CACHEDIR_ . "/" . $file)) {
                        $n++;
                        unlink($CACHEDIR_ . "/" . $file);
                    } else {
                        $n += CleanTempFiles($CACHEDIR_ . "/" . $file);
                    }
                }
            } 
            closedir($handle);
        } 
    }
    return $n;
}

function SizeTempFiles (&$size, $_CACHEDIR = NULL) {
    if (!$_CACHEDIR) {
        $_CACHEDIR = $GLOBALS["RUNNINGNDIR"] . "tmp";
    }
    if (file_exists($CACHEDIR_)) {
        if ($handle = opendir($CACHEDIR_)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($CACHEDIR_ . "/" . $file)) {
                        $n++;
                        $size += filesize($CACHEDIR_ . "/" . $file);
                    } else {
                        $n += SizeTempFiles($size, $CACHEDIR_ . "/" . $file);
                    }
                }
            } 
            closedir($handle);
        } 
    }
    return $n;
}

/*
 * This function gets the current version from the update server
 */
function isUpdatable () {
    global $UPDATE_SERVER, $_PAS_CORE_VERSION;
    
    if (!$UPDATE_SERVER) {
        $txt = "##You have no settings for an UPDATE server. You can contact your webmaster to fix this for you!##";
    } else {
        $txt = "<b>##Core:##</b><hr />";
        $version = trim(implode("", file("{$UPDATE_SERVER}core/version")));
        if ($version == $_PAS_CORE_VERSION) {
            $txt .= "##Your version is up to date## - {$_PAS_CORE_VERSION}";
        } else {
            
            $txt .= "##There is an update avaliable.##<br />";
            $txt .= "##Your version is:## {$_PAS_CORE_VERSION}<br />";
            $txt .= "##Avaliable version is:## {$version}<br /><br />";
            
            $txt .= "##Update:## <a href=\"index.php?admin[]=general&admin[]=doupdate&doupdate=1\">##initiate##</a><br /><br />";
        }
        $_LOGFILE = $GLOBALS["RUNNINGNDIR"] . "tmp/ChangeLog";
        if (!file_exists($_LOGFILE)) {
            $changelog = implode("", file("{$UPDATE_SERVER}core/ChangeLog"));
            file_put_contents($_LOGFILE, $changelog);
        } else if (file_exists($_LOGFILE) && date("Ymd", filemtime($_LOGFILE)) == date("Ymd")) {
            $changelog = implode("", file($_LOGFILE));
        } else {
            $changelog = implode("", file("{$UPDATE_SERVER}core/ChangeLog"));
            file_put_contents($_LOGFILE, $changelog);
        }
        $txt .= "<br /><br />##ChangeLog##<hr />";
        $txt .= "<div class=\"changelog__\"><pre>";
        $txt .= wordwrap(htmlentities($changelog), 55, "\n");
        $txt .= "</pre></div>";
    }
    return $txt;
}
function isUpdatableComponents () {
    global $UPDATE_SERVER, $_PAS_CORE_VERSION;
    
    if ($UPDATE_SERVER) {
        $txt = "<b>##Components:##</b><hr />";
        $txt .= "##Your version is up to date##";
    }
    return $txt;
}


function mime_icon ($case) {
    if (ereg("image", $case)) {
        $case = "image";
    } 
    switch ($case) {
    case "text/plain": $icon = "text.png";
        break;
    case "image": $icon = "image.png";
        break;
    case "application/zip": $icon = "archive.png";
        break;
    case "application/x-msdownload": $icon = "binary.png";
        break;
    case "application/pdf": $icon = "pdf.png";
        break;
    case "application/msword": $icon = "doc.png";
        break;
    case "text/html": $icon = "html.png";
        break;
    case "application/vnd.ms-excel": $icon = "spreadsheet.png";
        break;
    case "audio/mid": $icon = "midi.png";
        break;
    case "audio/mpeg": $icon = "cdtrack.png";
        break;
    case "application/x-gzip": $icon = "archive.png";
        break;
    case "application/x-java-archive": $icon = "archive.png";
        break;
    case "folder": $icon = "folder.png";
        break;
    default: $icon = "binary.png";
        break;
    } 
    return $icon;
} 

?>