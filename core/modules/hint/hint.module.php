<?php

global $COREROOT, $RUNNINGNDIR, $__ADMIN, $_DEBUG;

$MODULEDIR = $this->MODULEDIR;

function filter_hint($html)
{
    global $CORE;
    
    $_DATA = $CORE->getAllOfType("hint");
    
    foreach ((array)$_DATA as $HINT_) {
        $array_hint = $HINT_->_xml_to_array();
        $replace = $array_hint[0]["data"][0]["data"][0]["data"];
        $with = htmlspecialchars("{$array_hint[0]["data"][0]["data"][1]["data"]}:{$array_hint[0]["data"][0]["data"][2]["data"]}", ENT_COMPAT, "UTF-8");
        $html = hint_replace_all($replace, $html, $with);
    }
    $js_data = "
    <script type=\"text/javascript\" src=\"./admin/hint/prototype/prototype.js\"></script>
    <script type=\"text/javascript\" src=\"./admin/hint/scriptaculous/scriptaculous.js\"></script>
    <script type=\"text/javascript\" src=\"./admin/hint/HelpBalloon.js\"></script>
    <script type=\"text/javascript\" src=\"./admin/hint/InitHelpBalloon.js\"></script>";
    $html = str_replace("</head>", $js_data . "\n</head>", $html);
    
    return $html;
}

function hint_replace_all($spl, $txt, $hint)
{
    $pieces = explode($spl, $txt);
    $id = md5($spl);
    
    $num = count($pieces);
    for($i = 0; $i < $num - 1; $i++) {
        $pieces[$i] = $pieces[$i] . "<span id=\"{$id}{$i}\" class=\"hint\" title=\"{$hint}\">{$spl}</span>";
    }
    
    $txt = implode("", $pieces);
    return $txt;
}

?>