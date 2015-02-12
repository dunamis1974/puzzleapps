<?php
$_BODY .= "<p>##This tool can affect the look of the system in a dramatic way.## ##Use with care.##</p><br>";
if (!$_GET["edit"] or $_GET["edit"] == "list") {
    $_LIST = __get_img(array("list" => "IMG"));
    $_BODY .= "
    <table class=\"\" width=\"98%\">
      <tr class=\"title__\">
        <td class=\"\">&nbsp;</td>
        <td class=\"\"><b>##Image##</b></td>
        <td class=\"\"><b>##Size##</b></td>
        <td class=\"\"><b>##Date##</b></td>
        <td class=\"\">&nbsp;</td>
      </tr>";
    for ($i = 0; $i < count($_LIST); $i++) {
        $IMG = __get_img(array("prop" => $_LIST[$i]));
        $_BODY .= "
        <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
            <td><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/image.png\" width=16 height=16 border=0></td>
            <td><a href=\"javascript:popup('../images/" . $_LIST[$i] . "', 300, 300)\">" . $_LIST[$i] . "</a></td>
            <td align=right>" . $IMG["size"] . " KB</td>
            <td>" . $IMG["date"] . "</td>
            <td align=\"center\"><a href=\"" . BuildLinkGet() . "&delete=" . $_LIST[$i] . "\" onclick=\"return confirm('##Do you want to delete this file?##'); \"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a></td>
        </tr>";
    } 
    $_BODY .= "</table>";
    $_BODY .= "<p><p><a href=\"" . BuildLinkGet() . "&edit=new\">+ ##new image##</a></p>
    <p><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/archive.png\" border=\"0\" /> <a href=\"" . BuildLinkGet() . "&download=all\">##download all images##</a></p>";
} elseif ($_GET["edit"] == "new") {
    $_BODY .= __get_img(array());
    $_BODY .= "<!--  Add new CSS -->
    <form action=\"\" method=\"post\" enctype=\"multipart/form-data\">

    <p align=\"left\">
        <b>Select Image</b><br>
        <input type=\"file\" name=\"template\">
        <input type=\"hidden\" name=\"upload\" value=\"true\">
    </p>
    <p align=\"left\">
        <input type=\"submit\" value=\"Save\">
        <input type=\"button\" value=\"Cancel\" onclick=\"history.go(-1)\">
    </p>
    </form>";
} elseif ($_GET["edit"] == "del") {
    __get_img(array());
} 

/**
 * __get_img()
 * 
 * @param  $params 
 * @return 
 */
function __get_img ($params) {
    // global $RUNNINGNDIR;
    extract($params);
    $img_dir = $GLOBALS["RUNNINGNDIR"] . "images/";

    if (!file_exists($img_dir)) {
        if (mkdir ($img_dir, 0755)) {
            // echo "Images directory is created!";
        } 
    } 

    if ($_GET["download"] == "all") {
        DownloadZIP($img_dir, "images.zip");
        return;
    } 

    if ($_POST) {
        if ($_POST["upload"]) {
            if ($_FILES['template']['size'] <= 0) {
                $msg = "Please upload Image!";
                $error = true;
            } else {
                $name = $_FILES['template']['name'];
            } 
            if (($name) && (!ereg('[A-Za-z0-9-]', $name))) {
                $msg = "##Please enter corect Image name!##";
                $error = true;
            } 
            if (strlen($name) > 25) {
                $msg = "##Please enter corect Image name length!##";
                $error = true;
            } 
            if (!$error) {
                copy($_FILES['template']['tmp_name'], $img_dir . "$name");
                unlink($_FILES['template']['tmp_name']);
                echo "<script>history.go(-2)</script>";
                die();
            } else {
                unset($_POST["do_new"]);
                return "<font color='#ff0000'><b>$msg</b></font>";
            } 
        } 
    } 

    if ($_GET["delete"] != '') {
        if (file_exists($img_dir . "/" . $_GET["delete"])) {
            unlink($img_dir . "/" . $_GET["delete"]);
        } 
        echo "<script>history.go(-1)</script>";
        die();
    } 

    if ($prop) {
        $date = date("r", filemtime($img_dir . $prop));
        $size = round((filesize($img_dir . $prop) / 1024), 2);
        $IMG["date"] = $date;
        $IMG["size"] = $size;

        return $IMG;
    } 

    if ($list == "IMG") {
        if ($dir = @opendir($img_dir)) {
            while (($file = readdir($dir)) !== false) {
                if ((substr($file, -3) != "css") && (substr($file, -3) != ".js") && (!is_dir($file))) {
                    $images_list[] = "$file";
                } 
            } 
            closedir($dir);
        } 
        if (is_Array($images_list)) asort($images_list);
        return $images_list;
    } 

    if ($list == "drop") {
        echo "<select name=\"" . $varname . "\">";
        if ($dir = @opendir($img_dir)) {
            echo "<option value=\"\"" . (($value == "")?" selected":"") . "></option>";
            while (($file = readdir($dir)) !== false) {
                if ((substr($file, -3) == "css") && (eregi("template_", $file))) {
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