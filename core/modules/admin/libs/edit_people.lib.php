<?php

global $DTD;

if (($_GET["togroup"] == "true") && ($_POST)) {
    /**
     * First remove user from all groups
     */
    $sql = "DELETE FROM users_in_groups WHERE ".TQT."userid".TQT." = '" . $_GET["id"] . "'";
    $DB->query($sql);

    /**
     * Now add user to new groups
     */
    for ($i = 0; $i < count($_POST["group"]); $i++) {
        $sql = "INSERT INTO users_in_groups (".TQT."userid".TQT.", ".TQT."_group".TQT.") VALUES ('" . $_GET["id"] . "', '" . $_POST["group"][$i] . "')";
        $DB->query($sql);
    } 

    /**
     * and go home
     */
    //echo "<script type=\"text/javascript\"> document.location.replace(\"" . BuildLinkGet() . "\"); </script>\n";
    echo "<script type=\"text/javascript\"> opener.top.location.reload(); self.close(); </script>";
    die();
} 

if ($_GET["togroup"] == "true") {
    $_GROUPS = PERMISSIONS::GetGroups();
    $_PERSON = @new PERSON($_GET["id"]);
    $_P_DATA = $_PERSON->translate_object_data();
    $_BODY .= "
    <p>##Select groups to assign selected user.##<br />
    ##Use 'Ctrl' key to select multiple groups.##
    </p>
    ##User##:[" . $_GET["id"] . "] " . $_P_DATA["firstname"] . " " . $_P_DATA["lastname"] . "<p />
    ##Groups##:<br />
    <form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n
    <select name=\"group[]\" multiple size=\"10\"  style=\"width: 200px;\">";
    for ($i = 0; $i < count($_GROUPS); $i++) {
        $_BODY .= "<option value=\"" . $_GROUPS[$i]->id . "\">" . $_GROUPS[$i]->groupname . "</option>\n";
    }
    $_BODY .= "
    </select>
    <table border=0><tr><td NOWRAP valign=\"top\" style=\"font-face:Arial,Verdana,Helvetica;font-size:12px\">\n";
    if ($_GET["do"] == "delete") {
        $_BODY .= "<input type=\"submit\" value=\" ##Delete## \">\n";
    } else {
        $_BODY .= "<input type=\"submit\" value=\" ##Submit## \">\n";
    } 
    $_BODY .= "</td><td>\n";
    $_BODY .= "<input type=\"button\" value=\" ##Cancel## \" onclick=\"self.close();\">";
    $_BODY .= "</td></tr></table>\n";

    $_BODY .= "\n</form>\n";
} else {
    $_BODY .= "<p>+ <a href=\"javascript: popup('" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=add&odd=person', 600, 500);\">##add person##</a></p>";
    $_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"100%\">";
    $_BODY .= "<tr class=\"title__\"><td width=\"10\">&nbsp;</td><td><b>ID</b></td><td><b>##Name##</b></td><td>##Groups##</td><td>&nbsp;</td></tr>";

    $_PERSON = @new PERSON;
    $_DATA = $_PERSON->getAllOfType("person");

    $ELM = GetDTDElements($_DATA[0]->_dtd);

    for ($i = 0; $i < count($_DATA); $i++) {
        $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td width=\"10\"><img align=\"middle\" id=\"img_" . $_DATA[$i]->id . "\" src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" onclick=\"ShowHide('" . $_DATA[$i]->id . "');\"></td>
                <td width=\"10\"><nobr>[" . $_DATA[$i]->id . "]</nobr></td>
                <td width=\"*\"><nobr>" . substr(strip_tags(preg_replace(array("[<!\[CDATA\[]", "[\]\]>]"), "", $_DATA[$i]->data)), 0, 20) . " ... </nobr></td>
                <td width=\"*\">" . PERMISSIONS::GetUserGroups($_DATA[$i]->id) . "</td>
                <td width=\"80\" align=\"center\"><nobr>
                <a href=\"javascript: popup('" . BuildLinkGet() . "&nomenu=true&togroup=true&id=" . $_DATA[$i]->id . "', 600, 500);\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/group.png\" border=0 width=\"16\" height=\"16\" title=\"##group##\" /></a> 
                <a href=\"javascript: popup('" . BuildLinkGet() . "&view=person&do=edit&id=" . $_DATA[$i]->id . "', 600, 500);\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##edit##\" /></a> 
                <a href=\"" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=delete&id=" . $_DATA[$i]->id . "\" onclick=\"return confirm('##Do you want to delete this object?##');\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a>
                </nobr></td>
            </tr>
            <tr>
                <td></td>
                <td colspan=\"3\"><div id=\"data_" . $_DATA[$i]->id . "\" style=\"display:none\">" . ViewObject($_DATA[$i], $ELM) . "</div></td>
            </tr>
            ";
    } 

    $_BODY .= "</table>";
    $_BODY .= "<p>+ <a href=\"javascript: popup('" . BuildLinkGet() . "&view=" . $_GET["view"] . "&do=add&odd=person', 600, 500);\">##add person##</a></p>";
} 

?>