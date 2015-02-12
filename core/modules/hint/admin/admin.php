<?php

/**
 * _link_hint()
 *
 * @return string
 * @access private
 */
function _link_hint () {
    return "<a href=\"" . BuildLinkGet() . "&config=hint\"" . (($_GET["config"] == "hint")?" id=\"current\"":"") . ">##HTMLBaloon##</a>";
}

/**
 * _admin_hint()
 *
 * @return string $BODY
 * @access private
 */
function _admin_hint () {
    global $FILTERS;
    $BODY = "<b>##HTMLBaloon module administration##</b><p />";
    if ($_GET["action"]) {
        $FILTERS["hint"] = ($_GET["action"] == "on")?true:false;
        $STATUS = ($_GET["action"] == "on")?"true":"false";
        $FILE = "hint";
        $DATA = "<?php\n\n\$FILTERS[\"hint\"] = " . $STATUS . ";\n\n?>";
        $DONE = WriteConfigFile($FILE, $DATA);
        $BODY .= ($DONE)?"<br /><br /><b>##HTMLBaloon status changed to## ##" . $_GET["action"] . "##</b><br />":"<br /><br /><b>##Error! hint status is not changed!##</b><br />";
    }

    $_STATUS = (($FILTERS["hint"])?"Off":"On");
    $BODY .= "<li><a href=\"" . BuildLinkGet() . "&config=hint&action=" . (($FILTERS["hint"])?"off":"on") . "\">##Turn hint " . trim($_STATUS) . "##</a></li>";

    return $BODY;
}

?>