<?php

/**
 * LangControls()
 * 
 * @return string $controls
 * @access public 
 */

function LangControls () {
    global
    $LANGUAGES,
    $SYS_LANGUAGES,
    $CURRENTLANGUAGE,
    $doFORM;
    
    unset($_GET["lang"]);
    $NEW_URI = $doFORM->cancel_uri();

    for ($l = 0; $l < count($LANGUAGES); $l++) {
        if ($SYS_LANGUAGES[$LANGUAGES[$l]]["id"] == $CURRENTLANGUAGE) {
            $controls .= "<span class=\"selected__\"> [ ##" . $SYS_LANGUAGES[$LANGUAGES[$l]]["title"] . "## ]</span> ";
        } else {
            $controls .= " [ <a href=\"" . BuildLinkGet() . "&lang=" . $LANGUAGES[$l] . "\" class=\"\">##" . $SYS_LANGUAGES[$LANGUAGES[$l]]["title"] . "##</a> ] ";
        } 
    } 
    return $controls;
} 

?>