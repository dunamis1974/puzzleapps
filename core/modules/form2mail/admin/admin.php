<?
/*
 * @version $Id: admin.php,v 1.2 2005/10/29 11:46:09 bobby Exp $ 
 */
 
/**
 * _link_form2mail()
 * 
 * @return string 
 * @access private 
 */
function _link_form2mail () {
    return "<a href=\"" . BuildLinkGet() . "&config=form2mail\"" . (($_GET["config"] == "form2mail")?" id=\"current\"":"") . ">##Form2Mail##</a>";
} 

/**
 * _admin_form2mail()
 * 
 * @return string $_BODY
 * @access private 
 */
function _admin_form2mail () {
    global
        $FORM2MAIL,
        $MODULES,
        $PRELOADMODULES,
        $CURRENTPLATFORM,
        $CURRENTUSER,
        $SYSTEMIMAGES,
        $FILEROOT,
        $DTD;
    
    $IND = 1;

    if ($_GET["configure"] && !$_POST) {
        $MSG .= "<form action=\"\" method=\"POST\" enctype=\"multipart/form-data\">
        <b>##Enter TO email##</b><br />
        <input type=\"text\" name=\"to\" class=\"input\" value=\"" . $FORM2MAIL["to"] . "\" /><br /><br />
        <b>##Enter ODD email field##</b><br />
        <input type=\"text\" name=\"from\" class=\"input\" value=\"" . $FORM2MAIL["from"] . "\" /><br /><br />
        <b>##Enter ODD subject field##</b><br />
        <input type=\"text\" name=\"subject\" class=\"input\" value=\"" . $FORM2MAIL["subject"] . "\" /><br /><br />
        <b>##Enter ODD description##</b><br />
        <input type=\"text\" name=\"odd\" class=\"input\" value=\"" . $FORM2MAIL["odd"] . "\" /><br /><br />
        <br /><br /><input type=\"submit\" class=\"input\" value=\"##Save configuration##\" /><br /><br /></form>";
    } else if ($_GET["configure"] && $_POST) {
        // Create config file
        $data = "<?\n\n";
        foreach ($_POST AS $KEY => $VAL) {
            if (trim($_POST[$KEY]) == "") $_ERR = true;
            $data .= "\$FORM2MAIL[\"".$KEY."\"] = \"" . addslashes($VAL) . "\";\n\n";
        }
        $data .= "\n\n?>";

        if ($_ERR) {
            $MSG = "##Error!## ##Plaese click 'Back' and enter correct data!##";
            return $MSG;
        }
        $DONE = WriteConfigFile("mod_form2mail", $data);

        // Add module to modules configuration
        if (!$MODULES["f2m1"]) {
            $MODULES["f2m1"] = array("mod" => "form2mail", "title" => "Form2Mail");
            $data = "<?\n\n";
            $data .= "\$MODULES = " . Array2Text($MODULES) . ";\n\n";
            if ($PRELOADMODULES) $data .= "\$PRELOADMODULES = " . Array2Text($PRELOADMODULES) . ";";
            $data .= "\n\n?>";
            WriteConfigFile ("modules", $data);
        }

        $MSG .= ($DONE)?"<br /><b>##Form2Mail configuration is saved.##</b><br /><br />":"<br /><b>##Error! Form2Mail configuration is not saved.##</b><br /><br />";
        $MSG .= "<a href=\"index.php?admin[]=general&admin[]=modules&config=form2mail\">##Click here to continue.##</a><br /><br />";
    } else {
        if (!$FORM2MAIL)
            $MSG = "<b>##Form2Mail module is not configured.##</b><br />";
        $MSG .= "<a href=\"index.php?admin[]=general&admin[]=modules&config=form2mail&configure=true\">##If you want to configure this module click here.##</a>";
        $MSG .= "<br /><br /><br />
        <b>##Email is send to:##</b> " . $FORM2MAIL["to"] . "<br />
        <b>##Email is send from (field):##</b> " . $FORM2MAIL["from"] . "<br />
        <b>##Email subject (field):##</b> " . $FORM2MAIL["subject"] . "<br />
        <b>##Form is build from ODD file:##</b> " . $FORM2MAIL["odd"] . "<br /><br />
        ";
    }
    return $MSG;
}

?>