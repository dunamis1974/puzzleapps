<?php

global $_ADMN_MOD;

$_ADMN_MOD = (array)$_ADMN_MOD;

if ($CURRENTUSER->isSuperUser()) $_scan = "<li><a href=\"" . BuildLinkGet() . "&config=_scan_\"" . (($_GET["config"] == "_scan_")?" id=\"current\"":"") . " title=\"##Detect if new modules are added to the core##\"><b>##Scan modules##</b></a></li>";

$_BODY .= "
<div id=\"navcontainer\" align=\"center\">
    <ul id=\"navlist\">
    $_scan
";
for ($i = 0; $i < count($_ADMN_MOD); $i++) {
    if ($CURRENTUSER->isAllowed($_ADMN_MOD[$i])) {
        $_MOD_FUNCTION_LINK = "_link_" . $_ADMN_MOD[$i];
        if (function_exists($_MOD_FUNCTION_LINK)) $_BODY .= "<li><nobr>" . $_MOD_FUNCTION_LINK() . "</nobr></li>";
    }
}

$_BODY .= "
    </ul>
</div>";

if ($_GET["config"] && $_GET["config"] != "_scan_") {
    $_MOD_FUNCTION_ADMIN = "_admin_" . $_GET["config"];
    $_BODY .= "<p>" . $_MOD_FUNCTION_ADMIN() . "</p>";
} else if ($_GET["config"] == "_scan_") {
    $_BODY .= "<p><b>##Scanning modules ...##</b></p>";
    $_BODY .= _ModScan();
    
}

function _ModScan () {
    global $_ADMN_MOD;
    $_SCAN_FLD = $GLOBALS["COREROOT"] . "modules/";
    if ($handle = opendir($_SCAN_FLD)) {
        while (false !== ($file = readdir($handle))) {
            if (is_dir($_SCAN_FLD . "/" . $file) && ($file != ".") && ($file != "..") && ($file != "CVS")) {
                if (file_exists($_SCAN_FLD . $file . "/admin/admin.php")) {
                    $_BODY .= "&nbsp;&nbsp;&nbsp;" . $file . " ...... " . ((in_array($file, $_ADMN_MOD))?"OK":"ERR") . "<br />";
                    $_ARRAY[] = $file;
                }
            }
        }
    }
    closedir($handle);
    $TXT = "<?php\n\n\$_ADMN_MOD = " . Array2Text($_ARRAY) . ";\n\n?>";
    $_ADMIN_FILE = $GLOBALS["COREROOT"] . "modules/admin/conf/modadmin.conf.php";
    if ($fp = fopen($_ADMIN_FILE, "w")) {
        fwrite($fp, $TXT);
        fclose ($fp);
        $_BODY .= "<br />##Configuration saved.##<br /><br />";
    } else {
        $_BODY .= "<br />##Configuration not saved!##<br /><br />";
    }
    
    return $_BODY;
}
?>