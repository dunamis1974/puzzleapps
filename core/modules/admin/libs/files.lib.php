<?php

$Files = new FILES;
// Upload file

if ($_POST) {
    $O = $Files->upload();
    $_BODY .= "<script type=\"text/javascript\"> document.location.replace(\"" . $_SYSINDEX . "?admin[]=files&folder={$_GET["parent"]}\"); </script>\n";
}

if ($_GET["delete"]) { // Delte file if requested
    
    $foo = $Files->load($_GET["delete"]);
    $foo->remove();
    $_BODY .= "<script type=\"text/javascript\"> document.location.replace(\"" . $_SYSINDEX . "?admin[]=files&folder={$_GET["folder"]}\"); </script>\n";

} else if ($_GET["addfile"]) { // Add new file/folder
    
    $_BODY .= "<div class=\"block_note_\">";
    $_BODY .= "##You can upload files with maximum size of##: " . ini_get('upload_max_filesize') . "b";
    $_BODY .= "</div><br />";
    $_BODY .= "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\"><table>";
    $_BODY .= $doFORM->start("file");
    $_BODY .= "</table></form>";
    
} else { // display files list
    
    if ($_GET["folder"]){
        $parent = $_GET["folder"];
    } else {
        $parent = $CURRENTPLATFORM->id;
    }
    
    $Obj = new CORE($parent);
    $_TREE = array_reverse($Obj->getTree($parent));
    
    $list = $Obj->getChildrenByType("file");
//    $list = $Files->listFiles();
    $end = count($list);
    
    $path[] = "<a href=\"" . $_SYSINDEX . "?admin[]=files\">root</a>";
    foreach ($_TREE AS $branch) {
        $BR = new CORE($branch);
        $data = $BR->translate_object_data();
        $path[] = "<a href=\"" . $_SYSINDEX . "?admin[]=files&folder={$BR->id}\">{$data["name"]}</a>";
    }
    
    $_BODY .= implode(" / ", $path);
    $_BODY .= "<p><a href=\"" . BuildLinkGet() . "&addfile=true&parent={$parent}\">+ ##add file / folder##</a></p>";
    $_BODY .= "<div style=\"width: 100%;\">";
    if ($_GET["plugin"]) {
        $linkadd = "&simple=true&plugin=true";
    }
    for ($i = 0; $i < $end; $i++) {
        $data = $list[$i]->translate_object_data();
        unset($a_, $_a);
        if ($data["type"] == "folder") {
            $a_ = "<a href=\"" . $_SYSINDEX . "?admin[]=files&folder={$list[$i]->id}{$linkadd}\">";
            $_a = "</a>";
        }
        
        $_FILE = "<div class=\"fmrow\">
        		<div class=\"col1fm\">{$a_}<img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/" . mime_icon($data["type"]) . "\" border=0 width=\"16\" height=\"16\" title=\"{$data["type"]}\" />{$_a}</div>
                <div class=\"col2fm\">{$a_}" . $data["name"] . "{$_a}</div>";
        $_FILE .= "<div class=\"col3fm\">";
        
        if ($_GET["plugin"]) {
            if ($data["type"] != "folder")
                $_FILE .= "<a href=\"#\" onclick=\"javascript: window.opener.document.getElementById('href').value = '{$_MEDIA_URL}{$data["tid"]}'; self.close();\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/action.png\" border=0 width=\"16\" height=\"16\" title=\"##seo download##\" /></a>";
        } else {
            if ($data["type"] != "folder")
                $_FILE .= "
                    <a href=\"" . $_SYSINDEX . "?downloadfile=" . $list[$i]->id . "\" target=\"\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/down.png\" border=0 width=\"16\" height=\"16\" title=\"##download##\" /></a>
                    <a href=\"{$_MEDIA_URL}{$data["tid"]}\" target=\"\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/action.png\" border=0 width=\"16\" height=\"16\" title=\"##seo download##\" /></a>";
            $_FILE .= "
                    <a href=\"" . BuildLinkGet() . "&folder={$parent}&delete=" . $list[$i]->id . "\" onclick=\"return confirm('##Do you want to delete this file?##'); \"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a>";
            
            if ($data["type"] != "folder")
                $_FILE .= "
                    <img src=\"./admin/images/16x16/messagebox_info.png\" align=\"middle\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"{$data["name"]}:{$data["size"]}<br />{$data["realname"]}\" />";
        }
        $_FILE .= "</div>
            </div>";
        
        if ($data["type"] == "folder") {
            $_BODY .= $_FILE;
        } else {
            $_ALLFILES .= $_FILE;
        }
    }
    
    $_BODY .= $_ALLFILES;
    $_BODY .= "</div>";
    
}

?>