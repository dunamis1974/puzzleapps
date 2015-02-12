<?php
// #############################################
// Shiege Iseng Resize Class
// 11 March 2003
// shiegege@yahoo.com
// http://kentung.f2o.org/scripts/thumbnail/
// ###############
// Thanks to :
// Dian Suryandari <dianhau@yahoo.com>
/**
 * Sample :
 * $thumb=new thumbnail("./shiegege.jpg");        // generate image_file, set filename to resize
 * $thumb->size_width(100);                       // set width for thumbnail, or
 * $thumb->size_height(300);                      // set height for thumbnail, or
 * $thumb->size_auto(200);                        // set the biggest width or height for thumbnail
 * $thumb->jpeg_quality(75);                      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
 * $thumb->show();                                // show your thumbnail
 * $thumb->save("./huhu.jpg");                    // save your thumbnail to file
 * ----------------------------------------------
 * Note :
 * - GD must Enabled
 * - Autodetect file extension (.jpg/jpeg, .png, .gif, .wbmp)
 * but some server can't generate .gif / .wbmp file types
 * - If your GD not support 'ImageCreateTrueColor' function,
 * change one line from 'ImageCreateTrueColor' to 'ImageCreate'
 * (the position in 'show' and 'save' function)
 *
 * @version $Id: img.php,v 1.10 2005/11/11 17:30:23 bobby Exp $
 *
 */

//$basedir = $_SERVER["DOCUMENT_ROOT"] . ereg_replace("img.php", "", $_SERVER["PHP_SELF"]);

$_basedir = dirname($_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"]);
$data = explode("/", $_basedir);
$cnt = (count($data) - 2);
for ($i = 0; $i < $cnt; $i++)
    if ($data[$i])
        $basedir .= "/" . $data[$i];


$size = ($_GET["size"])?$_GET["size"]:150;
$qlt = ($_GET["qlt"])?$_GET["qlt"]:80;

if (!file_exists($basedir . "/tmp")) mkdir($basedir . "/tmp", 0777);
if (!file_exists($basedir . "/tmp/tumbs")) mkdir($basedir . "/tmp/tumbs", 0777);

$tumbs_fld = $basedir . "/tmp/tumbs/";
$image_fld = $basedir . "/files/";

if ($_GET["img"]) {
    $name = $tumbs_fld . $size . $_GET["nomag"] . $_GET["img"];
    $format = strtoupper(ereg_replace(".*\.(.*)$", "\\1", $_GET["img"]));
    header("Content-Type: image/$format");
    if (file_exists($name)) {
        header("Content-Length: " . filesize($name));
        $fp = fopen($tumbs_fld . $size . $_GET["nomag"] . $_GET["img"], 'rb');
        fpassthru($fp);
        exit;
    } else {
        $thumb=new thumbnail($image_fld . $_GET["img"]);
        $thumb->size_auto($size);
        $thumb->jpeg_quality($qlt);
        $thumb->save($name);
        $thumb->show();
    }
}

class thumbnail {
    var $img;

    function thumbnail($imgfile) {
        // detect image format
        $this->img["format"] = ereg_replace(".*\.(.*)$", "\\1", $imgfile);
        $this->img["format"] = strtoupper($this->img["format"]);
        if ($this->img["format"] == "JPG" || $this->img["format"] == "JPEG") {
            // JPEG
            $this->img["format"] = "JPEG";
            $this->img["src"] = ImageCreateFromJPEG ($imgfile);
        } elseif ($this->img["format"] == "PNG") {
            // PNG
            $this->img["format"] = "PNG";
            $this->img["src"] = ImageCreateFromPNG ($imgfile);
        } elseif ($this->img["format"] == "GIF") {
            // GIF
            $this->img["format"] = "GIF";
            $this->img["src"] = ImageCreateFromGIF ($imgfile);
        } elseif ($this->img["format"] == "WBMP") {
            // WBMP
            $this->img["format"] = "WBMP";
            $this->img["src"] = ImageCreateFromWBMP ($imgfile);
        } else {
            // DEFAULT
            echo "Not Supported File";
            exit();
        } 
        @$this->img["lebar"] = imagesx($this->img["src"]);
        @$this->img["tinggi"] = imagesy($this->img["src"]); 
        // default quality jpeg
        $this->img["quality"] = 75;
    } 

    function size_height($size = 100) {
        // height
        $this->img["tinggi_thumb"] = $size;
        @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"] / $this->img["tinggi"]) * $this->img["lebar"];
    } 

    function size_width($size = 100) {
        // width
        $this->img["lebar_thumb"] = $size;
        @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"] / $this->img["lebar"]) * $this->img["tinggi"];
    } 

    function size_auto($size = 100) {
        // size
        if ($this->img["lebar"] >= $this->img["tinggi"]) {
            $this->img["lebar_thumb"] = $size;
            @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"] / $this->img["lebar"]) * $this->img["tinggi"];
        } else {
            $this->img["tinggi_thumb"] = $size;
            @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"] / $this->img["tinggi"]) * $this->img["lebar"];
        } 
    } 

    function jpeg_quality($quality = 75) {
        // jpeg quality
        $this->img["quality"] = $quality;
    } 

    function show() {
        // show thumb
        @header("Content-Type: image/" . $this->img["format"]);

        /**
         * change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function
         */
        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
        @imagecopyresized($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
        
        if (empty($_GET["nomag"])) {
            $insert = imagecreatefromgif("./viewmag.gif");
            
            $insert_x = imagesx($insert);
            $insert_y = imagesy($insert); 
            
            
            $des_x = (imagesx($this->img["des"])-16);
            $des_y = (imagesy($this->img["des"]) - 16); 
            
            imagecopymerge($this->img["des"], $insert,$des_x,$des_y,0,0,$insert_x,$insert_y,100); 
        }
        if ($this->img["format"] == "JPG" || $this->img["format"] == "JPEG") {
            // JPEG
            imageJPEG($this->img["des"], "", $this->img["quality"]);
        } elseif ($this->img["format"] == "PNG") {
            // PNG
            imagePNG($this->img["des"]);
        } elseif ($this->img["format"] == "GIF") {
            // GIF
            imageGIF($this->img["des"]);
        } elseif ($this->img["format"] == "WBMP") {
            // WBMP
            imageWBMP($this->img["des"]);
        } 
    } 

    function save($save = "") {
        // save thumb
        if (empty($save)) $save = strtolower("./thumb." . $this->img["format"]);
        /**
         * change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function
         */
        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
        @imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

        if (empty($_GET["nomag"])) {
            $insert = imagecreatefromgif("./viewmag.gif");
            
            $insert_x = imagesx($insert);
            $insert_y = imagesy($insert); 
            
            
            $des_x = (imagesx($this->img["des"])-16);
            $des_y = (imagesy($this->img["des"]) - 16); 
            
            imagecopymerge($this->img["des"], $insert,$des_x,$des_y,0,0,$insert_x,$insert_y,100); 
        }
        
        if ($this->img["format"] == "JPG" || $this->img["format"] == "JPEG") {
            // JPEG
            imageJPEG($this->img["des"], "$save", $this->img["quality"]);
        } elseif ($this->img["format"] == "PNG") {
            // PNG
            imagePNG($this->img["des"], "$save");
        } elseif ($this->img["format"] == "GIF") {
            // GIF
            imageGIF($this->img["des"], "$save");
        } elseif ($this->img["format"] == "WBMP") {
            // WBMP
            imageWBMP($this->img["des"], "$save");
        } 
    } 
} 

?>