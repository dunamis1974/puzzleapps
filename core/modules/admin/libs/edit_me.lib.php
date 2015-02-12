<?php

global
    $CURRENTUSER,
    $DTD;

$_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"100%\">";
$_BODY .= "<tr class=\"title__\"><td width=\"10\">&nbsp;</td><td><b>ID</b></td><td><b>##Name##</b></td><td>##Groups##</td><td>&nbsp;</td></tr>";

$DATA = new PERSON($CURRENTUSER->id);

$ELM = GetDTDElements($DATA->_dtd);

$_BODY .= "
    <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
        <td width=\"10\"><img align=\"middle\" id=\"img_" . $DATA->id . "\" src=\"" . $SYSTEMIMAGES . "16x16/minus.png\" onclick=\"ShowHide('" . $DATA->id . "');\"></td>
        <td width=\"10\"><nobr>[" . $DATA->id . "]</nobr></td>
        <td width=\"*\"><nobr>" . substr(strip_tags(preg_replace(array("[<!\[CDATA\[]", "[\]\]>]"), "", $DATA->data)), 0, 20) . " ... </nobr></td>
        <td width=\"*\">" . PERMISSIONS::GetUserGroups($DATA->id) . "</td>
        <td width=\"80\" align=\"center\"><nobr><a href=\"javascript: popup('" . BuildLinkGet() . "&view=person&do=edit&id=" . $DATA->id . "', 600, 500);\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##edit##\" /></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan=\"3\"><div id=\"data_" . $DATA->id . "\">" . ViewObject($DATA, $ELM) . "</div></td>
    </tr>
    ";

$_BODY .= "</table>";
?>