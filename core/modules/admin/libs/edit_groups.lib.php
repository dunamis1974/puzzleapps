<?php
/**
 * @version $Id: edit_groups.lib.php,v 1.2 2005/10/29 11:46:08 bobby Exp $
 */

global
    $DB,
    $CURRENTPLATFORM,
    $doFORM,
    $REMOTE_CONFIG,
    $MAIN_PERMISSIONS,
    $MOD_PERMISSIONS,
    $CUSTOM_PERMISSIONS,
    $_ADMN_MOD;

if ($_GET["add"] || $_GET["edit"]) {
    if ($_POST && (trim($_POST["groupname"]) != "" && !$_POST["groupid"])) {
        $permissions = serialize($_POST["permissions"]);
        $sql = "INSERT INTO groups (".TQT."platform".TQT.", ".TQT."groupname".TQT.", ".TQT."note".TQT.", ".TQT."permissions".TQT.") VALUES ('" . $CURRENTPLATFORM->id . "', '" . $_POST["groupname"] . "', '" . $_POST["note"] . "', '" . $permissions . "')";
        $DB->query($sql);
        echo "<script type=\"text/javascript\"> document.location.replace(\"" . BuildLinkGet() . "\"); </script>\n";
        die();
    } 
    if ($_POST && (trim($_POST["groupname"]) != "" && $_POST["groupid"])) {
        $permissions = serialize($_POST["permissions"]);
        $sql = "UPDATE ".TQT."groups".TQT." SET ".TQT."groupname".TQT." = '" . $_POST["groupname"] . "', ".TQT."note".TQT." = '" . $_POST["note"] . "', ".TQT."permissions".TQT." = '" . $permissions . "' WHERE ".TQT."platform".TQT." = '" . $CURRENTPLATFORM->id . "' and ".TQT."id".TQT." = '" . $_POST["groupid"] . "'";
        $DB->query($sql);
        echo "<script type=\"text/javascript\"> document.location.replace(\"" . BuildLinkGet() . "\"); </script>\n";
        die();
    } 
    $_BODY .= "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\">\n";
    if ($_GET["edit"]) {
        $_BODY .= "<input type=\"hidden\" name=\"groupid\" value=\"" . $_GET["edit"] . "\" />\n";
        $group = PERMISSIONS::GetGroup($_GET["edit"]);
        $__DATA = (array)$group[0];
    } 
    if ($_POST) $DATA = $_POST;
    $_BODY .= "<table width=\"100%\">\n";
    $_BODY .= "<tr><td valign=\"top\"><table>\n";
    $_BODY .= $doFORM->start("permissionsgroup", $__DATA);
    $_BODY .= "</table></td><td valign=\"top\">\n";

    /**
     * These are main permissions
     * If user can access administration edit delete .....
     */
    $_BODY .= "<b>##Main permissions##</b><p />\n";

    $_BODY .= "<table border=0 cellspacing=0>\n";
    $key = array_keys($MAIN_PERMISSIONS);
    $__CURRENT = unserialize($__DATA["permissions"]);
    for ($i = 0; $i < count($key); $i++) {
        $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td><input type=\"checkbox\" name=\"permissions[]\" value=\"" . $key[$i] . "\"". (((is_array($__CURRENT)) && (in_array($key[$i], $__CURRENT)))?" checked":"") ."></td>
                <td>##" . $MAIN_PERMISSIONS[$key[$i]]["link"] . "##</td>
            </tr>\n";
    } 
    $_BODY .= "</table>\n";
    
    if (is_array($MOD_PERMISSIONS)) {
        $_BODY .= "<br /><br /><b>##Access modules##</b><p />\n";
        $_BODY .= "<table border=0 cellspacing=0>\n";
        $key = array_keys($MOD_PERMISSIONS);
        for ($i = 0; $i < count($key); $i++) {
            $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td><input type=\"checkbox\" name=\"permissions[]\" value=\"" . $key[$i] . "\"". (((is_array($__CURRENT)) && (in_array($key[$i], $__CURRENT)))?" checked":"") ."></td>
                <td>##" . $MOD_PERMISSIONS[$key[$i]]["link"] . "##</td>
            </tr>";
            if ($MOD_PERMISSIONS[$key[$i]]["submenu"]) {
                $key2 = array_keys($MOD_PERMISSIONS[$key[$i]]["submenu"]);
                for ($s = 0; $s < count($key2); $s++) {
                    $_BODY .= "<tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                        <td>&nbsp;</td><td>
                            <table cellspacing=0>
                                <tr>
                                <td><input type=\"checkbox\" name=\"permissions[]\" value=\"" . $key2[$s] . "\"" . $key[$i] . "\"". (((is_array($__CURRENT)) && (in_array($key2[$s], $__CURRENT)))?" checked":"") ."></td>
                                <td>##" . $MOD_PERMISSIONS[$key[$i]]["submenu"][$key2[$s]]["link"] . "##</td>
                                </tr>
                            </table>
                        </td></tr>";
                }
            }
        } 
        $_BODY .= "</table>";
    } 

    if (is_array($CUSTOM_PERMISSIONS)) {
        $_BODY .= "<br /><br /><b>##Platform specifis permissions##</b><p />\n";
        $_BODY .= "<table border=0 cellspacing=0>\n";
        $key = array_keys($CUSTOM_PERMISSIONS);
        for ($i = 0; $i < count($key); $i++) {
            $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td><input type=\"checkbox\" name=\"permissions[]\" value=\"" . $key[$i] . "\"". (((is_array($__CURRENT)) && (in_array($key[$i], $__CURRENT)))?" checked":"") ."></td>
                <td>##" . $CUSTOM_PERMISSIONS[$key[$i]]["link"] . "##</td>
            </tr>";
        } 
        $_BODY .= "</table>";
    } 

    $_BODY .= "</td><td valign=\"top\">";
    $_BODY .= "<b>##Select group permissions##</b><br /><br />";
    $_BODY .= "<table border=0 cellspacing=0>";
    
    unset($REMOTE_CONFIG["home"]);
    
    $key = array_keys($REMOTE_CONFIG);
    for ($i = 0; $i < count($key); $i++) {
        if (!$REMOTE_CONFIG[$key[$i]]["noperm"]) {
            $_BODY .= "
                <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                    <td><input type=\"checkbox\" name=\"permissions[]\" value=\"" . $key[$i] . "\"" . $key[$i] . "\"". (((is_array($__CURRENT)) && (in_array($key[$i], $__CURRENT)))?" checked":"") ."></td>
                    <td>##" . $REMOTE_CONFIG[$key[$i]]["link"] . "##</td>
                </tr>";
            if ($REMOTE_CONFIG[$key[$i]]["submenu"]) {
                $key2 = array_keys($REMOTE_CONFIG[$key[$i]]["submenu"]);
                for ($s = 0; $s < count($key2); $s++) {
                    if (!$REMOTE_CONFIG[$key[$i]]["submenu"][$key2[$s]]["noperm"]) {
                        $_BODY .= "<tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                            <td>&nbsp;</td><td>
                                <table cellspacing=0>
                                    <tr>
                                    <td><input type=\"checkbox\" name=\"permissions[]\" value=\"" . $key2[$s] . "\"" . $key[$i] . "\"". (((is_array($__CURRENT)) && (in_array($key2[$s], $__CURRENT)))?" checked":"") ."></td>
                                    <td>##" . $REMOTE_CONFIG[$key[$i]]["submenu"][$key2[$s]]["link"] . "##</td>
                                    </tr>
                                </table>
                            </td></tr>";
                    } 
                }
            }
        } 
    } 
    $_BODY .= "</table></td></tr>";
    $_BODY .= "</table></form>";
} else if ($_GET["edit"]) {
} else if ($_GET["delete"]) {
    $sql = "DELETE FROM ".TQT."groups".TQT." WHERE ".TQT."platform".TQT." = '" . $CURRENTPLATFORM->id . "' AND ".TQT."id".TQT."='" . $_GET["delete"] . "'";
    $DB->query($sql);
    echo "<script type=\"text/javascript\"> document.location.replace(\"" . $doFORM->cancel_uri($_GET, 1) . "\"); </script>\n";
    die();
} else {
    $data = PERMISSIONS::GetGroups();
    $end = count($data);
    $_BODY .= "<br />\n";
    $_BODY .= "\n<p>+ <a href=\"" . BuildLinkGet() . "&add=true\">##add new group##</a></p>";
    $_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"100%\">\n";
    $_BODY .= "<tr class=\"title__\"><td class=\"\"><b>ID</b></td><td class=\"\"><b>##Title##</b></td><td class=\"\"><b>##Description##</b></td><td class=\"\">&nbsp;</td></tr>\n";
    if ($end > 0) for ($i = 0; $i < $end; $i++) {
        $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td>[ " . $data[$i]->id . " ]</td>
                <td>" . $data[$i]->groupname . "</td>
                <td>" . nl2br($data[$i]->note) . "</td>
                <td align=\"center\"><a href=\"" . BuildLinkGet() . "&edit=" . $data[$i]->id . "\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##edit##\" /></a> <a href=\"" . BuildLinkGet() . "&delete=" . $data[$i]->id . "\" onclick=\"return confirm('##Do you want to delete this group?##'); \"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a></td>
            </tr>";
    }
    $_BODY .= "\n</table>";
    $_BODY .= "\n<p>+ <a href=\"" . BuildLinkGet() . "&add=true\">##add new group##</a></p>";
}
?>