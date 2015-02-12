<?php
/**
 * General interface driver
 */

/**
 * DoMenu()
 * 
 * @param  $params 
 * @return string $string
 * @access public 
 */
function DoMenu ($sub = null) {
    global
        $REMOTE_SEPARATOR,
        $REMOTE_LINESTART,
        $REMOTE_LINEEND,
        $REMOTE_CONFIG,
        $REMOTE_HOME,
        $CURRENTUSER,
        $SYSTEMIMAGES;
    
    /*if ($sub = "shortcuts") {
        $MENU = $REMOTE_HOME[$sub]["submenu"];
    } else */
    if ($sub) {
        $MENU = $REMOTE_CONFIG[$sub]["submenu"];
    } else {
        $MENU = $REMOTE_CONFIG;
    }

    $end = count($MENU);
    $key = array_keys($MENU);
    for ($i = 0; $i < $end; $i++) {
        if ($MENU[$key[$i]] == "separator") {
            $string .= $REMOTE_LINESTART . $REMOTE_SEPARATOR . $REMOTE_LINEEND;
        } else {
            if ($CURRENTUSER->isAllowed($key[$i]) || $key[$i] == "logout" || $key[$i] == "preview") {
                if ($sub && $MENU[$key[$i]]["url"]) {
                    $module = $MENU[$key[$i]]["url"];
                } else if ($sub) {
                    $module = "?admin[]=" . $_GET["admin"][0] . "&admin[]=" . $key[$i];
                } else {
                    $module = "?admin[]=" . (($MENU[$key[$i]]["custom"] == true)?"custom&admin[]=" . $key[$i]:$key[$i]);
                }
                $target = "";
                if ($MENU[$key[$i]]["target"]) {
                    $target = "target=\"{$MENU[$key[$i]]["target"]}\"";
                }
                $add_img = "";
                if ($MENU[$key[$i]]["bicon"])
                    $add_img = "<div class=\"main_buttons_img__\"><img src=\"{$SYSTEMIMAGES}{$MENU[$key[$i]]["bicon"]}\" border=\"0\" /><br /><img src=\"{$SYSTEMIMAGES}button_shadow.png\" border=\"0\" /></div>";
                $string .= "{$REMOTE_LINESTART}<div class=\"t\"><div class=\"b\"><div class=\"l\"><div class=\"r\"><div class=\"bl\"><div class=\"br\"><div class=\"tl\"><div class=\"tr\"><div class=\"content\"><a href=\"{$module}\"{$target} title=\"##{$MENU[$key[$i]]["link"]}##\">{$add_img}##{$MENU[$key[$i]]["link"]}##</a></div></div></div></div></div></div></div></div></div>{$REMOTE_LINEEND}";
            } 
        } 
    } 
    return $string;
}

/**
 * BuildJSMenu()
 * 
 * @param  $DATA 
 * @param integer $level 
 * @param unknown $submenu 
 * @return string $MENU
 * @access public 
 */
function BuildJSMenu($PATH = null, $level = 1) {
    global
    $REMOTE_CONFIG,
    $SYSTEMIMAGES,
    $CURRENTUSER; 
    
    $DATA = $REMOTE_CONFIG;
    
    if ($PATH) {
        $_PATH = explode(":", $PATH);
        $end = (count($_PATH) - 1);
        for ($i = 0; $i < $end; $i++) {
            $DATA = $DATA[$_PATH[$i]]["submenu"];
        }
    }
    $key = array_keys($DATA);
    $end = count($key);
    for ($i = 0; $i < $end; $i++) {
        if ($CURRENTUSER->isAllowed($key[$i])) {
            $PATH_ .= $PATH . $key[$i] . ":";
            $LINK = BuildLink($PATH_);
            if ($DATA[$key[$i]]["custom"]) $LINK .= "&custom=" . $DATA[$key[$i]]["custom"];
            if ($DATA[$key[$i]]["icon"]) {
                $_IMG = "'<img src=\"" . $SYSTEMIMAGES . $DATA[$key[$i]]["icon"] . "\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" />'";
            } else {
                $_IMG = "null";
            }
            $MENU .= str_repeat("\t", $level) . "[$_IMG, '##" . $DATA[$key[$i]]["link"] . "##', '" . $LINK . "', '_self', '##" . $DATA[$key[$i]]["link"] . "##'";
            if (is_array($DATA[$key[$i]]["submenu"])) {
                $MENU .= ",\n" . BuildJSMenu($PATH_, ($level + 1), $key[$i]);
            } 
            $MENU .= "]";
            if ($i < ($end - 1)) $MENU .= ",";
            $MENU .= "\n";
        } 
        $PATH_ = null;
    } 

    return $MENU;
} 

?>