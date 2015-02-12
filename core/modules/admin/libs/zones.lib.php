<?php


$_BODY .= "
<i>##NOTE: This is a visual zones editor!<br />It will not include new zones automatically into XSLT templates. You will have to do this manually or use template generator.##</i><br />
";
global $_ZONES, $_ZONE_DESC, $_ZONESCONTAIN, $_ZONESPERMANENT, $_ZONESDEFAULT, $_ZONESIGNORESELECT;
global $_TITLE, $_CONTAIN, $_TOP, $_BOTTOM, $_LEFT, $_RIGHT, $_CENTER, $_TYPE;


if ($_POST) {
    $VALIDATED = $UFV->validate($_POST, "zone");
    $VALIDATED["contain"] = $_POST["contain"];
    if (!$UFV->hasErrors()) {
        if ($_GET["add"]) {
            $_ZONES[] = $VALIDATED["name"];
            $_ZONE_DESC[$VALIDATED["name"]] = array("desc" => $VALIDATED["desc"], "pos" => $VALIDATED["pos"]);
            $_ZONESCONTAIN[$VALIDATED["name"]] = $VALIDATED["contain"];
            if ($VALIDATED["type"] == "permanent") $_ZONESPERMANENT[] = $VALIDATED["name"];
            elseif ($VALIDATED["type"] == "ignoreselect") $_ZONESIGNORESELECT[] = $VALIDATED["name"];
            if ($VALIDATED["default"] == 1) $_ZONESDEFAULT = $VALIDATED["name"];
        } else if ($_GET["edit"]) {
            $mykey = array_search($_GET["edit"], $_ZONES);
            if ($_ZONES[$mykey] != $VALIDATED["name"]) {
                $_ZONES[$mykey] = $VALIDATED["name"];
                unset($_ZONE_DESC[$_GET["edit"]]);
                unset($_ZONESCONTAIN[$_GET["edit"]]);
            }
            $_ZONE_DESC[$VALIDATED["name"]] = array("desc" => $VALIDATED["desc"], "pos" => $VALIDATED["pos"]);
            $_ZONESCONTAIN[$VALIDATED["name"]] = $VALIDATED["contain"];
            if ($VALIDATED["default"] == 1) $_ZONESDEFAULT = $VALIDATED["name"];
            $mykey_ = array_search($_GET["edit"], (array)$_ZONESPERMANENT);
            if ($mykey_ !== false) unset($_ZONESPERMANENT[$mykey_]);
            $mykey_ = array_search($_GET["edit"], (array)$_ZONESIGNORESELECT);
            if ($mykey_ !== false) unset($_ZONESIGNORESELECT[$mykey_]);
            
            if ($VALIDATED["type"] == "permanent") $_ZONESPERMANENT[] = $VALIDATED["name"];
            elseif ($VALIDATED["type"] == "ignoreselect") $_ZONESIGNORESELECT[] = $VALIDATED["name"];
        }
        _write_zones_conf();
        header("Location: " . $_POST["redir"]);
        die();
    } else {
        $data = $VALIDATED;
        $_ERRORS = $UFV->getErrors(1);
    }
}

$key = array_keys($_ZONE_DESC);
$end = count($key);
for ($i = 0; $i < $end; $i++) {
    $_TITLE[$key[$i]] = $_ZONE_DESC[$key[$i]]["desc"];
    $_CONTAIN[$key[$i]] = $_ZONESCONTAIN[$key[$i]];
    switch ($_ZONE_DESC[$key[$i]]["pos"]) {
        case "top": $_TOP[] = $key[$i]; break;
        case "bottom": $_BOTTOM[] = $key[$i]; break;
        case "left": $_LEFT[] = $key[$i]; break;
        case "right": $_RIGHT[] = $key[$i]; break;
        case "center": $_CENTER[] = $key[$i]; break;
    }
}

foreach ($_ZONES as $_ZONE) {
    if (in_array($_ZONE, (array)$_ZONESPERMANENT)) $_TYPE[$_ZONE] = "permanent";
    elseif (in_array($_ZONE, (array)$_ZONESIGNORESELECT)) $_TYPE[$_ZONE] = "ignoreselect";
    else $_TYPE[$_ZONE] = "normal";
}

if ($_GET["move"]) {
    $end = count($_ZONE_DESC);
    $pos = $_ZONE_DESC[$_GET["edit"]]["pos"];
    switch ($pos) {
        case "top": $_MOVEIN = $_TOP; break;
        case "bottom": $_MOVEIN = $_BOTTOM; break;
        case "left": $_MOVEIN = $_LEFT; break;
        case "right": $_MOVEIN = $_RIGHT; break;
        case "center": $_MOVEIN = $_CENTER; break;
    }
    
    $mykey = array_search($_GET["edit"], $_MOVEIN);
    if (($mykey <= 0 && $_GET["move"] == "up") || ($mykey > count($_MOVEIN) && $_GET["move"] == "down")) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        die();
    }
    switch ($_GET["move"]) {
        case "up" : $newkey = ($mykey - 1); break;
        case "down" : $newkey = ($mykey + 1); break;
    }
    $foo = $_MOVEIN[$mykey];
    $_MOVEIN[$mykey] = $_MOVEIN[$newkey];
    $_MOVEIN[$newkey] = $foo;
    foreach ($_MOVEIN AS $Z) {
        $foo = $_ZONE_DESC[$Z];
        unset($_ZONE_DESC[$Z]);
        $_ZONE_DESC[$Z] = $foo;
    }
    _write_zones_conf();
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    die();
} elseif ($_GET["edit"] || $_GET["add"]) {
    $_BODY .= "
    <table width=\"300\" border=\"0\" align=\"center\">
    <tr><td>$_ERRORS</td></tr>
    <form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    if (!$_POST && $_GET["add"]) $data["pos"] = $_GET["add"];
    if (!$_POST && $_GET["edit"]) {
        $data["pos"] = $pos = $_ZONE_DESC[$_GET["edit"]]["pos"];
        $data["name"] = $pos = $_GET["edit"];
        $data["desc"] = $pos = $_ZONE_DESC[$_GET["edit"]]["desc"];
        $data["type"] = $_TYPE[$_GET["edit"]];
        $data["default"] = (($_ZONESDEFAULT == $_GET["edit"])?1:0);
        $data["contain"] = $_ZONESCONTAIN[$_GET["edit"]];
    }
    $_BODY .= "<input type=\"hidden\" name=\"redir\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />";
    $_BODY .= $doFORM->start("zone", $data);
    $_BODY .= "
    <tr><td><br />
    <input type=\"submit\" value=\" ##Submit## \">
    </td></tr>
    </form>
    </table>";
} elseif ($_GET["delete"]) {
    foreach ($_ZONES AS $Z) {
        if ($Z != $_GET["delete"]) $_ZONES_[] = $Z;
    }
    $_ZONES = $_ZONES_;
    unset($_ZONE_DESC[$_GET["delete"]]);
    unset($_ZONESCONTAIN[$_GET["delete"]]);
    if (is_array($_ZONESPERMANENT)) foreach ($_ZONESPERMANENT AS $ZP) {
        if ($ZP != $_GET["delete"]) $_ZONESPERMANENT_[] = $ZP;
    }
    $_ZONESPERMANENT = $_ZONESPERMANENT_;
    if (is_array($_ZONESIGNORESELECT)) foreach ($_ZONESIGNORESELECT AS $ZI) {
        if ($ZI != $_GET["delete"]) $_ZONESIGNORESELECT_[] = $ZI;
    }
    $_ZONESIGNORESELECT = $_ZONESIGNORESELECT_;
    if ($_ZONESDEFAULT == $_GET["delete"]) $_ZONESDEFAULT = "";
    // Write zones configuration into file
    _write_zones_conf();
    // Do redirect
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    die();
} else {
    // display zones
    $_BODY .= "
    <style>
    table.zones {
        font-size: 10px;
        font-family: Arial, Helvetica, sans-serif;
        border: 1px #666666 solid;
    }
    
    table.zones td.top {
        background-color: #C0C0FF;
        border: 1px #666666 solid;
    }
    
    table.zones td.bottom {
        background-color: #C0C0FF;
        border: 1px #666666 solid;
    }
    
    table.zones td.left {
        background-color: #C0FFC0;
        border: 1px #666666 solid;
    }
    table.zones td.right {
        background-color: #C0FFC0;
        border: 1px #666666 solid;
    }
    
    table.zones td.center {
        background-color: #FFC0C0;
        border: 1px #666666 solid;
    }
    
    table.zones div {
        border: 1px #666666 solid;
        padding : 4px 4px 4px 4px;
        margin : 4px 4px 4px 4px;
    }
    
    table.zones div.default {
        border: 1px #FF0000 solid;
        padding : 4px 4px 4px 4px;
        margin : 4px 4px 4px 4px;
    }
    
    </style>
    <br />
    <table width=\"90%\" height=\"300\" border=\"0\" class=\"zones\" align=\"center\">
        <tr>
            <td colspan=\"3\" class=\"top\" height=\"30\" valign=\"top\">" . write_top_zones() . "<a href=\"" . $_SERVER["REQUEST_URI"] . "&add=top\"><img src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" align=\"middle\" title=\"##Add new zone in the top area##\" border=\"0\" /></a></td>
        </tr>
        <tr>
            <td width=\"20%\" class=\"left\" height=\"*\" valign=\"top\">" . write_left_zones() . "<a href=\"" . $_SERVER["REQUEST_URI"] . "&add=left\"><img src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" align=\"middle\" title=\"##Add new zone in the left area##\" border=\"0\" /></a></td>
            <td width=\"60%\" class=\"center\" height=\"*\" valign=\"top\">" . write_center_zones() . "<a href=\"" . $_SERVER["REQUEST_URI"] . "&add=center\"><img src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" align=\"middle\" title=\"##Add new zone in the center area##\" border=\"0\" /></a></td>
            <td width=\"20%\" class=\"right\" height=\"*\" valign=\"top\">" . write_right_zones() . "<a href=\"" . $_SERVER["REQUEST_URI"] . "&add=right\"><img src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" align=\"middle\" title=\"##Add new zone in the right area##\" border=\"0\" /></a></td>
        </tr>
        <tr><td colspan=\"3\" class=\"bottom\" height=\"30\" valign=\"top\">" . write_bottom_zones() . "<a href=\"" . $_SERVER["REQUEST_URI"] . "&add=bottom\"><img src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" align=\"middle\" title=\"##Add new zone in the bottom area##\" border=\"0\" /></a></td></tr>
    </table>
    <br /><br />
    ";

}

function write_top_zones() {
    global $_TOP, $_TITLE, $_TYPE, $_CONTAIN, $_ZONESDEFAULT, $SYSTEMIMAGES;
    $zones = count($_TOP);
    for ($i = 0; $i < $zones; $i++) {
        if ($_ZONESDEFAULT == $_TOP[$i]) $class = " class=\"default\"";
        $ZONE .= "<div$class>";
        $ZONE .= "<b>[" . $_TOP[$i] . "]</b> " . str_replace("_", " ", $_TITLE[$_TOP[$i]]) . "; ";
        $ZONE .= "<b>##type##:</b> " . $_TYPE[$_TOP[$i]] . "; ";
        $ZONE .= "<b>##contains##:</b> " . implode(" | ", $_CONTAIN[$_TOP[$i]]);
        $ZONE .= "<br /><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_TOP[$i] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" title=\"##Edit zone##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&delete=" . $_TOP[$i] . "\" onclick=\"return confirm('##Do you want to delete " . $_TOP[$i] . " zone?##'); \"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" title=\"##Delete zone##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_TOP[$i] . "&move=up\"><img src=\"" . $SYSTEMIMAGES . "16x16/top.png\" title=\"##Move zone up##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_TOP[$i] . "&move=down\"><img src=\"" . $SYSTEMIMAGES . "16x16/bottom.png\" title=\"##Move zone down##\" border=\"0\" align=\"middle\" /></a>";
        $ZONE .= "</div>";
    }
    if (!$ZONE) $ZONE = "<b>##Empty##</b>";
    return $ZONE;
}

function write_left_zones() {
    global $_LEFT, $_TITLE, $_TYPE, $_CONTAIN, $_ZONESDEFAULT, $SYSTEMIMAGES;
    $zones = count($_LEFT);
    for ($i = 0; $i < $zones; $i++) {
        if ($_ZONESDEFAULT == $_TOP[$i]) $class = " class=\"default\"";
        $ZONE .= "<div$class>";
        $ZONE .= "<b>[" . $_LEFT[$i] . "]</b> " . str_replace("_", " ", $_TITLE[$_LEFT[$i]]) . "<br />";
        $ZONE .= "<b>##type##:</b> " . $_TYPE[$_LEFT[$i]] . "<br />";
        $ZONE .= "<b>##contains##:</b> " . implode(" | ", $_CONTAIN[$_LEFT[$i]]) . "<br />";
        $ZONE .= "<br /><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_LEFT[$i] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" title=\"##Edit zone##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&delete=" . $_LEFT[$i] . "\" onclick=\"return confirm('##Do you want to delete " . $_LEFT[$i] . " zone?##'); \" align=\"middle\"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" title=\"##Delete zone##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_LEFT[$i] . "&move=up\"><img src=\"" . $SYSTEMIMAGES . "16x16/top.png\" title=\"##Move zone up##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_LEFT[$i] . "&move=down\"><img src=\"" . $SYSTEMIMAGES . "16x16/bottom.png\" title=\"##Move zone down##\" border=\"0\" align=\"middle\" /></a>";
        $ZONE .= "</div>";
    }
    if (!$ZONE) $ZONE = "<b>##Empty##</b>";
    return $ZONE;
}

function write_center_zones() {
    global $_CENTER, $_TITLE, $_TYPE, $_CONTAIN, $_ZONESDEFAULT, $SYSTEMIMAGES;
    $zones = count($_CENTER);
    for ($i = 0; $i < $zones; $i++) {
        if ($_ZONESDEFAULT == $_TOP[$i]) $class = " class=\"default\"";
        $ZONE .= "<div$class>";
        $ZONE .= "<b>[" . $_CENTER[$i] . "]</b> " . str_replace("_", " ", $_TITLE[$_CENTER[$i]]) . "<br />";
        $ZONE .= "<b>##type##:</b> " . $_TYPE[$_CENTER[$i]] . "<br />";
        $ZONE .= "<b>##contains##:</b> " . implode(" | ", $_CONTAIN[$_CENTER[$i]]) . "<br />";
        $ZONE .= "<br /><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_CENTER[$i] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" title=\"##Edit zone##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&delete=" . $_CENTER[$i] . "\" onclick=\"return confirm('##Do you want to delete " . $_CENTER[$i] . " zone?##'); \" align=\"middle\"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" title=\"##Delete zone##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_CENTER[$i] . "&move=up\"><img src=\"" . $SYSTEMIMAGES . "16x16/top.png\" title=\"##Move zone up##\" border=\"0\" align=\"middle\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_CENTER[$i] . "&move=down\"><img src=\"" . $SYSTEMIMAGES . "16x16/bottom.png\" title=\"##Move zone down##\" border=\"0\" align=\"middle\" /></a>";
        $ZONE .= "</div>";
    }
    if (!$ZONE) $ZONE = "<b>##Empty##</b>";
    return $ZONE;
}

function write_right_zones() {
    global $_RIGHT, $_TITLE, $_TYPE, $_CONTAIN, $_ZONESDEFAULT, $SYSTEMIMAGES;
    $zones = count($_RIGHT);
    for ($i = 0; $i < $zones; $i++) {
        if ($_ZONESDEFAULT == $_TOP[$i]) $class = " class=\"default\"";
        $ZONE .= "<div$class>";
        $ZONE .= "<b>[" . $_RIGHT[$i] . "]</b> " . str_replace("_", " ", $_TITLE[$_RIGHT[$i]]) . "<br />";
        $ZONE .= "<b>##type##:</b> " . $_TYPE[$_RIGHT[$i]] . "<br />";
        $ZONE .= "<b>##contains##:</b> " . implode(" | ", $_CONTAIN[$_RIGHT[$i]]) . "<br />";
        $ZONE .= "<br /><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_RIGHT[$i] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" align=\"middle\" title=\"##Edit zone##\" border=\"0\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&delete=" . $_RIGHT[$i] . "\" onclick=\"return confirm('##Do you want to delete " . $_RIGHT[$i] . " zone?##'); \"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" align=\"middle\" title=\"##Delete zone##\" border=\"0\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_RIGHT[$i] . "&move=up\"><img src=\"" . $SYSTEMIMAGES . "16x16/top.png\" align=\"middle\" title=\"##Move zone up##\" border=\"0\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_RIGHT[$i] . "&move=down\"><img src=\"" . $SYSTEMIMAGES . "16x16/bottom.png\" align=\"middle\" title=\"##Move zone down##\" border=\"0\" /></a>";
        $ZONE .= "</div>";
    }
    if (!$ZONE) $ZONE = "<b>##Empty##</b>";
    return $ZONE;
}

function write_bottom_zones() {
    global $_BOTTOM, $_TITLE, $_TYPE, $_CONTAIN, $_ZONESDEFAULT, $SYSTEMIMAGES;
    $zones = count($_BOTTOM);
    for ($i = 0; $i < $zones; $i++) {
        if ($_ZONESDEFAULT == $_TOP[$i]) $class = " class=\"default\"";
        $ZONE .= "<div$class>";
        $ZONE .= "<b>[" . $_BOTTOM[$i] . "]</b> " . str_replace("_", " ", $_TITLE[$_BOTTOM[$i]]) . "; ";
        $ZONE .= "<b>##type##:</b> " . $_TYPE[$_BOTTOM[$i]] . "; ";
        $ZONE .= "<b>##contains##:</b> " . implode(" | ", $_CONTAIN[$_BOTTOM[$i]]);
        $ZONE .= "<br /><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_BOTTOM[$i] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" align=\"middle\" title=\"##Edit zone##\" border=\"0\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&delete=" . $_BOTTOM[$i] . "\" onclick=\"return confirm('##Do you want to delete " . $_BOTTOM[$i] . " zone?##'); \"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" align=\"middle\" title=\"##Delete zone##\" border=\"0\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_BOTTOM[$i] . "&move=up\"><img src=\"" . $SYSTEMIMAGES . "16x16/top.png\" align=\"middle\" title=\"##Move zone up##\" border=\"0\" /></a><a href=\"" . $_SERVER["REQUEST_URI"] . "&edit=" . $_BOTTOM[$i] . "&move=down\"><img src=\"" . $SYSTEMIMAGES . "16x16/bottom.png\" align=\"middle\" title=\"##Move zone down##\" border=\"0\" /></a>";
        $ZONE .= "</div>";
    }
    if (!$ZONE) $ZONE = "<b>##Empty##</b>";
    return $ZONE;
}

function _zones_generate () {
    global $_ZONES, $_ZONE_DESC, $_ZONESCONTAIN, $_ZONESPERMANENT, $_ZONESDEFAULT, $_ZONESIGNORESELECT, $SYSTEMIMAGES;
    
    $_ZBODY .= "<?php\n\n";
    $_ZBODY .= "\n\$_ZONES = " . CreateConfigText($_ZONES) . ";\n";
    $_ZBODY .= "\n\$_ZONE_DESC = " . CreateConfigText($_ZONE_DESC) . ";\n";
    $_ZBODY .= "\n\$_ZONESCONTAIN = " . CreateConfigText($_ZONESCONTAIN) . ";\n";
    $_ZBODY .= "\n\$_ZONESPERMANENT = " . CreateConfigText($_ZONESPERMANENT) . ";\n";
    $_ZBODY .= "\n\$_ZONESDEFAULT = " . CreateConfigText($_ZONESDEFAULT) . ";\n";
    $_ZBODY .= "\n\$_ZONESIGNORESELECT = " . CreateConfigText($_ZONESIGNORESELECT) . ";\n";
    $_ZBODY .= "\n\n?>";
    
    return $_ZBODY;
}

function _write_zones_conf () {
    $_ZONES_TEXT = _zones_generate();
    WriteConfigFile("zones", $_ZONES_TEXT);
}

?>