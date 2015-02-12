<?php
global $LANGUAGES, $CURRENTPLATFORM, $PLATFORMID, $SYS_LANGUAGES;

$_BODY .= "\n<br />##This is the language manager of your web site.##<br /><br />\n";
if ($_GET["edit"] == "new") {
    if ($_POST) {
        $data = "<?php\n\n\$LANGUAGES = array(\n";
        for ($i = 0; $i < count($LANGUAGES); $i++) $data .= "    \"" . $LANGUAGES[$i] . "\",\n";
        for ($i = 0; $i < count($_POST["addlang"]); $i++) $data .= "    \"" . $_POST["addlang"][$i] . "\",\n";
        $data .= ");\n\n?>";
        WriteConfigFile("languages", $data);
        header("Location: " . $_POST["redir"]);
        die();
    }
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= "<input type=\"hidden\" name=\"redir\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" />";
    $_BODY .= "<table summary=\"\" class=\"bordertable\" width=\"100%\">";
    $_BODY .= "<tr class=\"title__\"><td width=\"16\">&nbsp;</td><td>##Name##</td><td>##Encodding##</td></tr>";
    foreach ($SYS_LANGUAGES AS $key => $arr) {
        if (!in_array($key, $LANGUAGES)) {
            $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td width=\"16\">
                    <input type=\"checkbox\" name=\"addlang[]\" value=\"" . $key . "\" />
                </td>
                <td width=\"*\">
                    " . $arr["title"] . "
                </td>
                <td width=\"16\">
                    " . $arr["encoding"] . "
                </td>
            </tr>";
        }
    }
    
    $_BODY .= "</table>";
    $_BODY .= "<br /><input type=\"submit\" value=\" ##Submit## \">";
    $_BODY .= "</form>";

} else if ($_GET["edit"]) {
} else if ($_GET["delete"]) {
    $data = "<?php\n\n\$LANGUAGES = array(\n";
    for ($i = 0; $i < count($LANGUAGES); $i++) if ($LANGUAGES[$i] != $_GET["delete"]) $data .= "    \"" . $LANGUAGES[$i] . "\",\n";
    $data .= ");\n\n?>";
    WriteConfigFile("languages", $data);
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    die();
} else {
    $_BODY .= "
      <p><a href=\"" . BuildLinkGet() . "&edit=new\">+ ##add language##</a></p>
      <table summary=\"\" class=\"bordertable\" width=\"100%\">
      <tr class=\"title__\"><td class=\"bordertable\" colspan=\"2\"><nobr><b>##Language name##</b></nobr></td><td class=\"bordertable\" align=\"center\" width=\"100\">&nbsp;</td></tr>
    ";
    $key = array_keys($LANGUAGES);
    for ($i = 0; $i < count($key); $i++) {
        $_BODY .= "
              <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td valign=\"top\" width=\"100\">[" . $key[$i] . "]&nbsp;" . $LANGUAGES[$key[$i]] . "</td>
                <td valign=\"top\" width=\"*\">" . $SYS_LANGUAGES[$LANGUAGES[$key[$i]]]["title"] . "</td>
                <td valign=\"top\" align=\"center\" width=\"100\"><nobr><a href=\"" . BuildLinkGet() . "&edit=" . $LANGUAGES[$key[$i]] . "\"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##edit translation##\" /></a> " . ((count($key) > 1)?"<a href=\"" . BuildLinkGet() . "&delete=" . $LANGUAGES[$key[$i]] . "\" onclick=\"return confirm('##Do you want to remove this language?##'); \"><img align=\"middle\" src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a>":"") . "</nobr></td>
              </tr>  
            ";
    } 
    $_BODY .= "
      </table>
      <p><a href=\"" . BuildLinkGet() . "&edit=new\">+ ##add language##</a></p>
    ";
}

?>
