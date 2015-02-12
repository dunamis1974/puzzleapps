<?php

$_BODY .= "<p>##This tool can affect the look of the system in a dramatic way.## ##Use with care.##</p><br>";
if ($edit && $update) {
    $_BODY .= "<p><b><font color=\"#FF0000\">{$edit} | {$update}</font></b></p>";
} 
if (!$_GET["edit"] or $_GET["edit"] == "list") {
    $_LIST = __get_configuration(array("list"=>"configuration"));
    $_BODY .= "
    <table summary=\"\" class=\"\" width=\"98%\">
        <tr class=\"title__\"><td class=\"\" colspan=\"2\"><nobr><b>##configuration name##</b></nobr></td><td class=\"\" align=\"center\" width=\"20\">&nbsp;</td></tr>";
    for ($i = 0; $i < count($_LIST); $i++) {
        $_BODY .= "
        <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
          <td valign=\"top\" colspan=\"2\"><a href=\"" . BuildLinkGet() . "&download=" . $_LIST[$i] . "&tpl=" . $_LIST[$i] . "\">" . $_LIST[$i] . "</a></td>
          <td valign=\"top\" align=\"center\"><nobr><a href=\"" . BuildLinkGet() . "&edit=edit&update=" . $_LIST[$i] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##edit##\" /></a></nobr></td>
        </tr>";
    }
    $_BODY .= "</table>";
    $_BODY .= "<p><p><img src=\"" . $SYSTEMIMAGES . "16x16/archive.png\" border=\"0\" /> <a href=\"" . BuildLinkGet() . "&download=all\">##download the active configuration##</a></p>";
} elseif ($_GET["edit"] == "edit") {
    $file_body = __get_configuration(array());
    $_BODY .= "<!--  Edit present configuration -->
    <form action=\"\" method=\"post\" enctype=\"multipart/form-data\">
       <p align=\"left\">Are you sure you want edit the <b><font color=\"#FF0000\">" . $_GET["update"] . "</font></b> configuration?
       <p align=\"left\"><b>Enter configuration body</b><br>
            <textarea name=\"template\" rows=25 cols=75 wrap=\"off\">$file_body</textarea>
       </p>
       <p align=\"left\">
             <input type=\"hidden\" name=\"do_edit\" value=\"" . $_GET["update"] . "\">
             <input type=\"submit\" value=\"Save\">
             <input type=\"button\" value=\"Cancel\" onclick=\"history.go(-1)\">
       </p>
    </form>";
}  elseif ($_GET["edit"] == "new") {
    $file_body = __get_configuration(array());
    $_BODY .= "<!--  Add new configuration -->
    <form action=\"\" method=\"post\" enctype=\"multipart/form-data\">

    <p align=\"left\">
        <b>Enter configuration title</b><br>
        <input type=\"text\" name=\"do_edit\" value=\"\"><p />
        <b>Enter configuration body</b><br>
        <textarea name=\"template\" rows=25 cols=75 wrap=\"off\"></textarea>
    </p>
    <p align=\"left\">
        <input type=\"submit\" value=\"Save\">
        <input type=\"button\" value=\"Cancel\" onclick=\"history.go(-1)\">
    </p>
    </form>";
}  elseif ($_GET["edit"] == "del") {
    __get_configuration(array());
} 

/**
 * __get_configuration()
 * 
 * @param $params
 * @return 
 **/
function __get_configuration ($params) {
    // global $RUNNINGNDIR;
    extract($params);
    $configuration_dir = $GLOBALS["CONFIG_DIR"] . "conf.d/";

    if ($_POST) {
        if ($_POST["do_edit"]) {
            if ($_POST['template']) {
                $name = $configuration_dir . $_POST["do_edit"]; 
                // rename($name, "$name.". $_POST["new_date"] .".arch");
                $body_ = stripslashes($_POST['template']);
                $fp = fopen ($name, "w");
                fwrite($fp, $body_);
                fclose ($fp); 
            } else {
                $error = true;
            } 

            if (!$error) {
                unset($_GET["update"]);
                unset($_GET["edit"]);
                unset($_POST["do_edit"]);
                echo "<script>history.go(-2)</script>";
                die();
            }
        } 
    } 
    if ($_GET["edit"] == "edit") {
        $file = $configuration_dir . $_GET["update"];
        $temp = fopen ($file, "rb");
        $body = fread ($temp, filesize ($file));
        fclose ($temp);
        $body = htmlspecialchars($body);
        return $body;
    } 

    if ($_GET["disable"] != "") {
        if (file_exists($configuration_dir . "/" . $_GET["disable"])) {
            rename($configuration_dir . "/" . $_GET["disable"], $configuration_dir . "/" . $_GET["disable"] . "." . time() . ".dsbl");
        } 
        unset($_GET["disable"]);
        echo "<script>history.go(-1)</script>";
        die();
    } 
    if ($_GET["edit"] == "del") {
        if (file_exists($configuration_dir . "/" . $_GET["file"])) {
            unlink($configuration_dir . "/" . $_GET["file"]);
        } 
        echo "<script>history.go(-1)</script>";
        die();
    } 

    if ($list == "configuration") {
        if ($dir = @opendir($configuration_dir)) {
            while (($file = readdir($dir)) !== false) {
                if (substr($file, -3) == "php") {
                    $tpl_list[] = "$file";
                } 
            } 
            closedir($dir);
            if (is_Array($tpl_list)) asort($tpl_list);
            return $tpl_list;
        } 
        return;
    } 

    if ($list == "drop") {
        echo "<select name=\"" . $varname . "\">";
        if ($dir = @opendir($configuration_dir)) {
            echo "<option value=\"\"" . (($value == "")?" selected":"") . "></option>";
            while (($file = readdir($dir)) !== false) {
                if ((substr($file, -3) == "configuration") && (eregi("template_", $file))) {
                    echo "<option value=\"$file\"" . (($value == "$file")?" selected":"") . ">$file</option>";
                } 
            } 
            closedir($dir);
            echo "</select>";
        } 
    } 
    return;
} 

?>