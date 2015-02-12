<?

/**
 * _link_htmltidy()
 * 
 * @return string 
 * @access private 
 */
function _link_htmltidy () {
    return "<a href=\"" . BuildLinkGet() . "&config=htmltidy\"" . (($_GET["config"] == "htmltidy")?" id=\"current\"":"") . ">##HTMLTidy##</a>";
} 

/**
 * _admin_htmltidy()
 * 
 * @return string $BODY
 * @access private 
 */
function _admin_htmltidy () {
    global $FILTERS;
    if (!class_exists("tidy")) {
        $BODY = "<b>##HTMLTidy extension is not enabled!##</b><p />";
    } else {
        $BODY = "<b>##HTMLTidy module administration##</b><p />";
        if ($_GET["action"]) {
            $FILTERS["htmltidy"] = ($_GET["action"] == "on")?true:false;
            $STATUS = ($_GET["action"] == "on")?"true":"false";
            $FILE = "htmltidy";
            $DATA = "<?\n\n\$FILTERS[\"htmltidy\"] = " . $STATUS . ";\n\n?>";
            $DONE = WriteConfigFile($FILE, $DATA);
            $BODY .= ($DONE)?"<br /><br /><b>##HTMLTidy status changed to## ##" . $_GET["action"] . "##</b><br />":"<br /><br /><b>##Error! htmltidy status is not changed!##</b><br />";
        }

        $_STATUS = (($FILTERS["htmltidy"])?"##Off##":"##On##");
        $BODY .= "<li><a href=\"" . BuildLinkGet() . "&config=htmltidy&action=" . (($FILTERS["htmltidy"])?"off":"on") . "\">##Turn htmltidy ##" . $_STATUS . "</a></li>";
    }
    return $BODY;
}

?>