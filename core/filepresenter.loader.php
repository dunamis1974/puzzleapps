<?php

if ($_GET["getfile"]) {
    $filename = $_GET["filename"];
    if (! $filename)
        $filename = "file";
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header("Content-type: application/octetstream");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    readfile($FILEROOT . $_GET["getfile"], "r+");
    
    die();
}

if ($_GET["downloadfile"]) {
    if ($_GET["tid"]) {
        $data = $CURRENTPLATFORM->getByExactValue($_TEXTID, $_GET["tid"]);
        if (count($data) > 0) {
            $ID = $data[0]->id;
        }
    } else {
        $ID = $_GET["downloadfile"];
    }
    $O = new FILES($ID);
    $FILE = $O->translate_object_data();
    $filename = $FILEROOT . $FILE["file"];
    $filesize = filesize($filename);
    if ($FILE["file"]) {
        header("Content-type: {$FILE["type"]}");
        header("Content-Length: " . $filesize);
        header("Content-Disposition: attachment; filename=\"" . $FILE["realname"] . "\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($filename, "r+");
        die();
    } else {
        die("Invalid file");
    }
}

if ($_GET["sysimg"]) {
    if ($_GET["script"]) {
        $file = $COREROOT . "data" . DIRECTORY_SEPARATOR . $_GET["script"] . DIRECTORY_SEPARATOR . $_GET["sysimg"];
        $sysimg = $_GET["sysimg"];
    } else if ($_GET["module"]) {
        $file = $COREROOT . "modules" . DIRECTORY_SEPARATOR . $_GET["module"] . DIRECTORY_SEPARATOR . $_GET["sysimg"];
        $sysimg = $_GET["sysimg"];
    } else {
        $file = $COREROOT . "data" . DIRECTORY_SEPARATOR . "_system" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . $_GET["sysimg"];
        $sysimg = $_GET["sysimg"];
    }
    if ($_GET["inc"]) {
        include ($file);
    } else {
        header("Content-disposition: filename=" . $sysimg . "");
        //header("Content-type: image/image");
        //header("Pragma: no-cache");
        //header("Expires: 0");
        readfile($file, "r+");
    }
    die();
}

?>