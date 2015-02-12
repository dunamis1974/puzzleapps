<?
/**
 * Metatags administration
 *
 * @version $Id: admin.php,v 1.2 2005/10/29 11:46:09 bobby Exp $ 
 */


/**
 * _link_metatags()
 * 
 * @return string 
 * @access private 
 */
function _link_metatags () {
    return "<a href=\"" . BuildLinkGet() . "&config=metatags\"" . (($_GET["config"] == "metatags")?" id=\"current\"":"") . ">##Metatags##</a>";
} 

/**
 * _admin_metatags()
 * 
 * @return string $BODY
 * @access private 
 */
function _admin_metatags () {
    $BODY = "<p><b>##Metatags administration##</b></p>";
    $BODY .= "
    <p><a href=\"" . BuildLinkGet() . "&config=metatags&generatemeta=true\">##Generate Meta Tags Configuration##</a><br />
    <i>##NOTE:If you change the meta data you should regenerate meta tags configuration!##</i>
    </p>";
    $BODY .= _admin_metatags_main();
    return $BODY;
} 

/**
 * _admin_metatags_main()
 * 
 * @return string $menu
 * @access private 
 */
function _admin_metatags_main () {
    global
        $SYSTEMIMAGES;
    
    if (!file_exists($GLOBALS["CONFIG_DIR"] . "mod_data")) mkdir($GLOBALS["CONFIG_DIR"] . "mod_data");
    $_METATEXT = $GLOBALS["CONFIG_DIR"] . "mod_data/metatags.txt";
    $_METACONF = $GLOBALS["CONFIG_DIR"] . "conf.d/mod_metatags.conf.php";
    // Generate keywords javascript
    if ($_GET["generatemeta"]) {
        if ($fp = fopen($_METACONF, "w")) {
            $META = _meta_generate($_METATEXT);
            fwrite($fp, $META . "\n");
            fclose ($fp);
        }
        chmod($_METACONF, 0666);
    }
    
    
    // If there is some post data do this
    if ($_POST || $_GET["delete"]) {
        if (file_exists($_METATEXT)) {
            $fp = fopen($_METATEXT, "r");
            $i = 0;
            while ($data = fgetcsv($fp, 1000, "=")) {
                $_prepare[$data[0]] = $data[0] . "=" .$data[1] . "=" .$data[2];
                $i++;
            } 
            fclose ($fp);
        }
        if ($_POST) $_prepare[$_POST["name"]] = $_POST["name"] . "=" .$_POST["data"];
        if ($_GET["delete"]) unset($_prepare[$_GET["delete"]]);
        $fp = fopen ($_METATEXT, "w");
        foreach ($_prepare as $_prepared) {
            fwrite($fp, $_prepared . "\n");
        }
        fclose ($fp);
        chmod($_METATEXT, 0666);
    } 

    if (file_exists($_METATEXT)) {
        $fp = fopen($_METATEXT, "r");
        $i = 0;
        while ($data = fgetcsv($fp, 1000, "=")) {
            $_LIST[$i]["name"] = $data[0];
            $_LIST[$i]["data"] = $data[1];
            $i++;
        } 
        fclose ($fp);
    } 
    // Display list of all keywords
    $_BODY .= "
    <table class=\"\" width=\"98%\">
      <tr class=\"title__\">
        <td class=\"\"><b>##Name##</b></td>
        <td class=\"\"><b>##Meta Data##</b></td>
        <td class=\"\">&nbsp;</td>
      </tr>";
    for ($i = 0; $i < count($_LIST); $i++) {
        $_BODY .= "
        <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
            <td valign=\"top\">" . $_LIST[$i]["name"] . "</td>
            <td valign=\"top\">" . $_LIST[$i]["data"] . "</td>
            <td valign=\"top\" align=\"center\"><a href=\"" . BuildLinkGet() . "&config=metatags&delete=" . $_LIST[$i]["name"] . "\" onclick=\"return confirm('##Do you want to delete this meta data?##'); \"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"##delete##\" /></a></td>
        </tr>\n";
    } 
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\">
    <tr>
        <td><input type=\"text\" name=\"name\" /></td>
        <td><input type=\"text\" size=\"40\" name=\"data\" /></td>
        <td align=\"center\"><input type=\"submit\" value=\"##Add##\" /></td>
    </tr>
    </form>
    </table><br /><br />\n";
    $msg = "<b>##Example##:</b><br /><pre>
    Description        This site is ......
    Keywords           Add, some, keywords, ......
    Audience           Site audience
    Subject            Site subject
    robots             index, follow
    revisit-after      5 days
    </pre>";

    $_BODY .= BuildNoteMsg($msg);
    
    return $_BODY;
} 

/**
 * _meta_generate()
 * 
 * @param string $_METATEXT
 * @return string $_METADATA
 * @access private 
 */
function _meta_generate ($_METATEXT) {
    global $_TRANSFORMTARGET;
    if (file_exists($_METATEXT)) {
        $TAGEND = ($_TRANSFORMTARGET == "xml")?" /":"";
        $_METADATA .= "<?\n";
        $fp = fopen($_METATEXT, "r");
        $i = 0;
        while ($data = fgetcsv($fp, 1000, "=")) {
            $_METADATA .= "\$_HEAD .= \"<meta name=\\\"" . $data[0] . "\\\" content=\\\"" . trim($data[1]) . "\\\"{$TAGEND}>\\n\";\n";
        } 
        fclose ($fp);
        $_METADATA .= "\n?>";
    }
    
    return $_METADATA;
}

?>