<?php
global $DTD, $UFV, $_TRANSFORM, $_XSLTCOMMAND, $_TEXTID, $_TRANSFORMTARGET;

if (!$_GET["pref"]) {
    $_GET["pref"] = "platform";
    $_SERVER["REQUEST_URI"] .= "&pref=platform";
}

if ($_POST) {
    $VALIDATED = $UFV->validate($_POST);
    if (!$UFV->hasErrors()) {
        if ($_GET["pref"] == "platform") {
            $CURRENTPLATFORM->updateDescription($VALIDATED["descrption"]);
        } elseif ($_GET["pref"] == "transform") {
            $data = "<?php\n\n";
            $data .= "\$_TRANSFORM = \"" . $VALIDATED["transform"] . "\";\n";
            $data .= "\$_TRANSFORMTARGET = \"" . $VALIDATED["target"] . "\";\n";
            if ($VALIDATED["transform"] == "command") {
                if (!empty($VALIDATED["command"]))
                    $data .= "\$_XSLTCOMMAND = \"" . $VALIDATED["command"] . "\";\n";
            }
            $data .= "\n\n?>";
            WriteConfigFile ("transform", $data);
        } elseif ($_GET["pref"] == "tid") {
            $data = "<?php\n\n";
            $data .= "\$_TEXTID = \"" . $VALIDATED["tid"] . "\";\n";
            $data .= "\n\n?>";
            WriteConfigFile ("browse", $data);
        }
        $newloc = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        header('Location: ' . $newloc);
        die();
    } else {
        $_ERR = $UFV->getErrors(1);
    }
}

$_BODY .= "
<div id=\"navcontainer\" align=\"center\">
    <ul id=\"navlist\">
        <li><a href=\"index.php?admin[]=general&admin[]=preferences&pref=platform\"" . (($_GET["pref"] == "platform")?" id=\"current\"":"") . ">##Platform##</a></li>
        <li><a href=\"index.php?admin[]=general&admin[]=preferences&pref=transform\"" . (($_GET["pref"] == "transform")?" id=\"current\"":"") . ">##XSLT transform##</a></li>
        <li><a href=\"index.php?admin[]=general&admin[]=preferences&pref=tid\"" . (($_GET["pref"] == "tid")?" id=\"current\"":"") . ">##Use textual ID##</a></li>
        <li><a href=\"index.php?admin[]=general&admin[]=preferences&pref=perm\"" . (($_GET["pref"] == "perm")?" id=\"current\"":"") . ">##Custom perm.##</a></li>
        <li><a href=\"index.php?admin[]=general&admin[]=preferences&pref=tmp\"" . (($_GET["pref"] == "tmp")?" id=\"current\"":"") . ">##Temp files##</a></li>
    </ul>
</div>
";

if ($_ERR) $_BODY .= $_ERR;

if ($_GET["pref"] == "platform") {
    if ($VALIDATED) {
        $data = $VALIDATED;
    } else {
        $data["name"] = $CURRENTPLATFORM->name;
        $data["descrption"] = $CURRENTPLATFORM->descrption;
    }
    //$_BODY .= "\n<b>##Platform options##</b><br /><br />\n";
    $_BODY .= "<table width=\"80%\" border=\"0\" class=\"\">";
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= "<input type=\"hidden\" name=\"id\" value=\"" . $CURRENTPLATFORM->id . "\" />";
    $_BODY .= $doFORM->start("platform", $data);
    $_BODY .= "</table><br /><br />";
    $_BODY .= "\n</form>\n";
    $_BODY .= "<div class=\"block_note_\">
    <b>##NOTE##</b><br />
    ##You can't edit your platform name, this will break the installation!##<br />
    </div><br />
    ";

} elseif ($_GET["pref"] == "transform") {
    $data["transform"] = $_TRANSFORM;
    $data["command"] = $_XSLTCOMMAND;
    $data["target"] = $_TRANSFORMTARGET;
    $_BODY .= "<table width=\"80%\" border=\"0\" class=\"\">";
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= "<input type=\"hidden\" name=\"id\" value=\"" . $CURRENTPLATFORM->id . "\" />";
    $_BODY .= $doFORM->start("transformation", $data);
    $_BODY .= "</table><br /><br />";
    $_BODY .= "\n</form>\n";
    $_BODY .= "<div class=\"block_note_\">
    <b>##Transformation method##</b><br /><br />
    ##Configure transformation method for your website.##<br />
    ##There are tree different metheods.##<br />
    1. ##mod_xslt2 - in this case Apache will handle the XML transformation.##<br />
    2. ##PHP - XSLT for PHP4 and XSL for PHP5 (auto detected)##<br />
    3. ##Commandline transformer## (Sablotron: sabcmd %xsl %xml)<br />
    &nbsp;&nbsp;&nbsp;&nbsp;##Mark XML and XSL file names with %xml and %xsl##
    </div><br />
    ";


} elseif ($_GET["pref"] == "tid") {
    $data["tid"] = $_TEXTID;
    $_BODY .= "<table width=\"80%\" border=\"0\" class=\"\">";
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= "<input type=\"hidden\" name=\"id\" value=\"" . $CURRENTPLATFORM->id . "\" />";
    $_BODY .= $doFORM->start("textid", $data);
    $_BODY .= "</table><br /><br />";
    $_BODY .= "\n</form>\n";
    $_BODY .= "<div class=\"block_note_\">
    <b>##Browse by ... (textual ID)##</b><br /><br />
    ##Here you can configure the textual ID (TID) if you need to browse via TID and not only by normal ID.##<br />
    ##You can make your url to look like this <i>textid/textid2</i> and the use <i>mod_rewrite</i> to convert it to <i>index.php?tid=textid2</i>.##<br />
    </div><br />";


} elseif ($_GET["pref"] == "perm") {
    $_BODY .= "
    <b>##Custom platform permissions##</b><br /><br />
    ##Under developement.##<br />
    ";

} elseif ($_GET["pref"] == "tmp") {
    if ($_GET["clean"]) CleanTempFiles();
    $size = 0;
    $n = SizeTempFiles($size);
    $_BODY .= "
    <b>##Temporary files administration##</b><br /><br />
    ##Files in your <b>PAS tmp</b> folder##: " . (($n)?$n:0) . "<br />
    ##Summary syze is##: $size Kb<br /><br />";
    
    if ($n > 0) $_BODY .= "Click <a href=\"index.php?admin[]=general&admin[]=preferences&pref=tmp&clean=true\">here</a> to clean temporary and cached files.";
}



$_BODY .= "
<br /><br />
";
?>