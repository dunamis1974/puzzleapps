<?php

global $DTD;

$_BODY .= "
<div id=\"navcontainer\" align=\"center\">
    <ul id=\"navlist\">
";

$end = count($DTD->dtdlist);
for ($i = 0; $i < $end; $i++) {
    if ($DTD->dtdlist[$i]["attributes"]["pool"] == 1) {
        if ($_GET["view"] == $DTD->dtdlist[$i]["attributes"]["name"]) {
            $_BODY .= "<li><a href=\"" . BuildLinkGet() . "&view=" . $DTD->dtdlist[$i]["attributes"]["name"] . "\" id=\"current\">&nbsp;&nbsp;##" . $DTD->dtdlist[$i]["data"][0]["data"] . "##&nbsp;&nbsp;</a></li>";
        } else {
            $_BODY .= "<li><a href=\"" . BuildLinkGet() . "&view=" . $DTD->dtdlist[$i]["attributes"]["name"] . "\">&nbsp;&nbsp;##" . $DTD->dtdlist[$i]["data"][0]["data"] . "##&nbsp;&nbsp;</a></li>";
        }
        
    }
}
$_BODY .= "
    </ul>
</div>";

if ($_GET["view"]) {
    $_BODY .= "<p><a href=\"javascript: popup('" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=add&odd=" . $_GET["view"] . "', 600, 500);\">##Add object##</a></p>";
    $_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"100%\">";
    $_BODY .= "<tr class=\"title__\"><td class=\"\" width=\"10\">&nbsp;</td><td class=\"\"><b>ID</b></td><td class=\"\"><b>##Title##</b></td><td class=\"\">&nbsp;</td></tr>";
    
    $_DATA = $CORE->getAllOfType($_GET["view"]);

    $ELM = GetDTDElements($_DATA[0]->_dtd);

    for ($i = 0; $i < count($_DATA); $i++) {
        $_BODY .= "
        <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
            <td width=\"10\"><img align=\"middle\"  id=\"img_" . $_DATA[$i]->id . "\" src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" onclick=\"ShowHide('" . $_DATA[$i]->id . "');\"></td>
            <td width=\"10\"><nobr>[" . $_DATA[$i]->id . "]</nobr></td>
            <td width=\"*\"><nobr>" . substr(strip_tags(preg_replace(array("[<!\[CDATA\[]", "[\]\]>]"), "", $_DATA[$i]->data)), 0, 20) . " ... </nobr></td>
            <td width=\"80\" align=\"center\"><nobr>
            <a href=\"javascript: popup('" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=edit&id=" . $_DATA[$i]->id . "', 600, 500);\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##edit##\" /></a> 
            <a href=\"" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=delete&id=" . $_DATA[$i]->id . "\" onclick=\"return confirm('##Do you want to delete this object?##');\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a>
            <a href=\"javascript: popup('" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=move&id=" . $_DATA[$i]->id . "', 600, 500);\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/2rightarrow.png\" border=0 width=\"16\" height=\"16\" title=\"##move object##\" /></a> 
            </nobr></td>
        </tr>
        <tr>
            <td></td>
            <td colspan=\"3\"><div id=\"data_" . $_DATA[$i]->id . "\" style=\"display:none\">" . ViewObject($_DATA[$i], $ELM) . "</div></td>
        </tr>
        ";
    }
    
    $_BODY .= "</table>";
    $_BODY .= "<p><a href=\"javascript: popup('" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=add&odd=" . $_GET["view"] . "', 600, 500);\">##Add object##</a></p>";
}

?>