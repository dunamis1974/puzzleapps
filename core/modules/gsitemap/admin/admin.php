<?php

$MODULE_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once($MODULE_DIR . "../drivers/generator.inc.php");

/**
 * _link_gsitemap()
 *
 * @return string
 * @access private
 */
function _link_gsitemap () {
    return "<a href=\"" . BuildLinkGet() . "&config=gsitemap\"" . (($_GET["config"] == "gsitemap")?" id=\"current\"":"") . ">##Google Sitemap##</a>";
}

/**
 * _admin_gsitemap()
 *
 * @return string $_BODY
 * @access private
 */
function _admin_gsitemap () {
    global
        $SYS_LANGUAGES,
        $_TEXTID,
        $EDITLANGUAGE,
        $LANGUAGES,
        $COREROOT,
        $CURRENTPLATFORM,
        $MOD_GSITEMAP,
        $_dtd_types;
        
    if ($_GET["configure"] && !$_POST) {
        $_BODY .= "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">
            <b>##SEO On?##:</b><br />
            <input type=\"radio\" name=\"seo\" value=\"1\" " . (($MOD_GSITEMAP["seo"] == 1)?"checked=\"1\"":"") . " /> On&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"seo\" value=\"0\" " . (($MOD_GSITEMAP["seo"] == 1)?"checked=\"1\"":"") . " /> Off<br /><br />
            <b>##Objects included into sitemap##</b> <br /><i>##(default is 'category' or can be 'category,text, ....')##</i><br />
            <input type=\"text\" name=\"odd\" class=\"input\" value=\"" . (($MOD_GSITEMAP["odd"])?$MOD_GSITEMAP["odd"]:"category") . "\" /><br /><br />
            <br /><br /><input type=\"submit\" class=\"input\" value=\"##Save configuration##\" /><br /><br /></form>";
    } else if ($_GET["configure"] && $_POST) {
        // Create config file
        $data = "<?\n\n";
        foreach ($_POST AS $KEY => $VAL) {
            if (trim($_POST[$KEY]) == "") $_ERR = true;
            $data .= "\$MOD_GSITEMAP[\"".$KEY."\"] = \"" . addslashes($VAL) . "\";\n\n";
        }
        
        $data .= "\$_HOOKS_AFTER[] = \"gsitemap\";\n\n";
        
        $data .= "\n\n?>";

        if ($_ERR) {
            $_BODY = "##Error!## ##Plaese click 'Back' and enter correct data!##";
            return $_BODY;
        }
        $DONE = WriteConfigFile("mod_gsitemap", $data);

        $_BODY .= ($DONE)?"<br /><b>##Google Sitemap configuration is saved.##</b><br /><br />":"<br /><b>##Error! Google Sitemap configuration was not saved.##</b><br /><br />";
        $_BODY .= "<a href=\"index.php?admin[]=general&admin[]=modules&config=gsitemap\">##Click here to continue.##</a><br /><br />";
    } else {
        if ($_GET["generate"]) {
            $_BODY .= (gsitemap_action($_GET["generate"]))?"##Sitemap generated##":"##We have a problem generating the sitemap##";
        }
        
        if ($MOD_GSITEMAP) {
            $_BODY .= "<br /><a href=\"index.php?admin[]=general&admin[]=modules&config=gsitemap&generate=local\">##Generate Google Sitemap##</a><br />";
            $_BODY .= "<a href=\"index.php?admin[]=general&admin[]=modules&config=gsitemap&generate=download\">##Download Google Sitemap##</a>";
        } else {
            $_BODY .= "##Please edit module configuration!##<br />";
        }
        $_BODY .= "<div class=\"block_note_\"><b>##Current configuration##</b> | ";
        $_BODY .= "<a href=\"index.php?admin[]=general&admin[]=modules&config=gsitemap&configure=true\">##Edit Configuration##</a><br />";
        $_BODY .= "<b>##SEO On?##:</b> " . (($MOD_GSITEMAP["seo"] == 1)?"##Yes##":"##No##") . "<br /><b>##Objects included into sitemap##:</b> {$MOD_GSITEMAP["odd"]}<br />";
        $_BODY .= "</div><br />";

    }
    
    return $_BODY;
}

function gsitemap_action ($todo) {
    global
        $SYS_LANGUAGES,
        $_TEXTID,
        $EDITLANGUAGE,
        $LANGUAGES,
        $COREROOT,
        $CURRENTPLATFORM,
        $MOD_GSITEMAP,
        $_dtd_types;
        
    $XML = gsitemap_generate();
    $file_name = "sitemap.xml";
    if ($todo == "local") {
        if ($handle = fopen($file_name, 'a')) {
            fwrite($handle, $XML);
            fclose($handle);
        } else {
            return false;
        }
    } else {
        header("Content-disposition: attachment; filename=$file_name");
        header("Content-type: text/xml");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $XML;
        die();
    }
    return true;
}

?>