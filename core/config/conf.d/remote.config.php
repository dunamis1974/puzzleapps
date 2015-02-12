<?php

if ($__ADMIN == true || !empty($_GET["admin"])) {

    $REMOTE_SEPARATOR = "<br>";
    $REMOTE_LINESTART = "<div class=\"main_buttons__\">";
    $REMOTE_LINEEND = "</div>";
    
    $REMOTE_CONFIG["home"] = array(
        "link" => "Shortcuts",
        "custom" => false,
        "bicon" => "32x32/home.png",
        "submenu" => array(
            "edit_me" => array("link" => "Edit My Profile", "icon" => "16x16/personal.png", "bicon" => "32x32/personal.png"),
            "pools" => array("link" => "Data Pools", "icon" => "16x16/folder.png", "bicon" => "32x32/folder.png"),
    		"files" => array("link" => "File manager", "icon" => "16x16/folder.png", "bicon" => "32x32/folder.png"),
    		"modules" => array("link" => "Configure modules", "icon" => "16x16/action.png", "bicon" => "32x32/action.png"),
            "css" => array("link" => "Platform style sheets (CSS)", "icon" => "16x16/css.png", "bicon" => "32x32/css.png"),
            "images" => array("link" => "Platform images", "icon" => "16x16/image.png", "bicon" => "32x32/image.png"),
            "languages" => array("link" => "Platform languages", "icon" => "16x16/locale.png", "bicon" => "32x32/locale.png"),
            "zones" => array("link" => "Platform zones definition", "icon" => "16x16/zones.png", "bicon" => "32x32/zones.png"),
            
            "preview" => array("target" => "_blank", "url" => "./", "link" => "View/Edit Site", "icon" => "16x16/preview.png", "bicon" => "32x32/preview.png"),
            "logout" => array("url" => "{$_SYSINDEX}?do=logout", "link" => "Logout", "icon" => "16x16/logout.png", "bicon" => "32x32/logout.png"),
        ),
    );
    
    $REMOTE_CONFIG["general"] = array(
        "link" => "General",
        "custom" => false,
        "bicon" => "32x32/configure.png",
        "submenu" => array(
            "preferences" => array("link" => "Platform configuration", "icon" => "16x16/configure.png", "bicon" => "32x32/configure.png"),
            "defgroup" => array("link" => "Platform default group", "icon" => "16x16/group.png", "bicon" => "32x32/group.png"),
            "configfiles" => array("link" => "Edit config files", "icon" => "16x16/edit.png", "bicon" => "32x32/edit.png"),
            "zones" => array("link" => "Platform zones definition", "icon" => "16x16/zones.png", "bicon" => "32x32/zones.png"),
            "languages" => array("link" => "Platform languages", "icon" => "16x16/locale.png", "bicon" => "32x32/locale.png"),
            "odd_edit" => array("link" => "Edit objects", "icon" => "16x16/edit.png", "bicon" => "32x32/edit.png"), // (Object Description Documents)
            "modules" => array("link" => "Configure modules", "icon" => "16x16/action.png", "bicon" => "32x32/action.png"),
    		"doupdate" => array("link" => "Online update", "icon" => "16x16/down.png", "bicon" => "32x32/down.png"),
            "credits" => array("link" => "Credits", "icon" => "16x16/messagebox_info.png", "bicon" => "32x32/messagebox_info.png"),
        ),
    );
    
    $REMOTE_CONFIG["content"] = array(
        "link" => "Content",
        "custom" => false,
        "submenu" => array(
            //"paginating" => array("link" => "Content Pagination", "icon" => "16x16/folder.png", "bicon" => "32x32/folder.png"),
            "pools" => array("link" => "Data pools", "icon" => "16x16/folder.png", "bicon" => "32x32/folder.png"),
        ),
    );
    
    $REMOTE_CONFIG["files"] = array(
        "link" => "File manager",
        "custom" => false,
        "bicon" => "32x32/folder.png",
    );
    
    $REMOTE_CONFIG["templates"] = array(
        "link" => "Templates",
        "custom" => false,
        "bicon" => "32x32/edit.png",
        "submenu" => array(
            "xslt" => array("link" => "Platform templates (XSLT)", "icon" => "16x16/html.png", "bicon" => "32x32/html.png"),
            "css" => array("link" => "Platform style sheets (CSS)", "icon" => "16x16/css.png", "bicon" => "32x32/css.png"),
            "images" => array("link" => "Platform images", "icon" => "16x16/image.png", "bicon" => "32x32/image.png"),
        ),
    );
    
    $REMOTE_CONFIG["people"] = array(
        "link" => "People/Groups",
        "custom" => false,
        "bicon" => "32x32/group.png",
        "submenu" => array(
            "edit_me" => array("link" => "Edit My Profile", "icon" => "16x16/personal.png", "bicon" => "32x32/personal.png"),
            "edit_people" => array("link" => "Add/Edit People", "icon" => "16x16/group.png", "bicon" => "32x32/group.png"),
            "edit_groups" => array("link" => "Add/Edit user groups", "icon" => "16x16/group.png", "bicon" => "32x32/group.png"),
        ),
    );
    

}
?>