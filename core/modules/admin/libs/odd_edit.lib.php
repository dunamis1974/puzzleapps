<?php
/**
 * -------------------------------------
 * Main action performed by this module
 * -------------------------------------
 * 1. Add ODD (+)
 * 2. Add field (+)
 * 3. Edit field (+)
 * 4. Delete field (+)
 * 5. Move field UP/DOWN (+)
 * 6. Convert System to User ODD (+)
 * 
 * TODO: Delete ODD (only if no data) (-)
 * TODO: Restore from System or Backup (-)
 * TODO: Create ODD backups (-)
 *
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * 
 */

global $DTD, $UFV, $_OBJECTSCOTAIN, $_OBJECTCONTROLS, $_OBJECTTEMPLATE;

// Field types description array
$_FTYPES = array (
    "module" => "List of modules",
    "text" => "Text field",
    "textarea" => "Enter plain text",
    "htmlarea" => "WYSIWYG HTML editor",
    "radio" => "Radio button",
    "checkbox" => "Check box button",
    "hidden" => "Hidden field",
    "submit" => "Submit/Resset buttons",
    "title" => "Title field",
    "file" => "Upload file/image",
    "cmsimage" => "Add image by ID",
    "password" => "Password field",
    //"time" => "Time fileds",
    //"lock_form" => "Lock form field",
    "date" => "Date field",
    "dropdown" => "Dropdown field",
    "add_top_bottom" => "On top/bottom field",
);

// Action description
$_ACTIONS = array(
    "left" => "Move left",
    "up" => "Move up",
    "edit" => "Edit object",
    "move" => "Change parent",
    //"cat" => "+ Sub-object",
    "del" => "Delete object",
    "right" => "Move right",
    "down" => "Move down",
);

// Do something if POST data
if ($_POST) {
    $VALIDATED = $UFV->validate($_POST);
    if (!$UFV->hasErrors() || $_GET["content"]) {
        $back = 1;
        if ($_GET["edit"]) {
            $FIELDID = ereg_replace("f", "", $_GET["edit"]);
            $DTD->edit_row($_GET["odd"], $FIELDID, $VALIDATED);
        } else if ($_GET["add"]) {
            if (!$_GET["odd"]) {
                $DTD->add_odd($VALIDATED);
            } else {
                $DTD->add_row($_GET["odd"], $VALIDATED);
            }
        } else if ($_GET["content"]) {
            $_OBJECTSCOTAIN[$_GET["odd"]] = $_POST["contain"];
            $_OBJECTCONTROLS[$_GET["odd"]]["controls"] = implode("|", $_POST["actions"]);
            $_OBJECTCONTROLS[$_GET["odd"]]["hide"] = $_POST["hide"];
            $data = "<?php\n\n";
            $data .= "\$_OBJECTSCOTAIN = " . Array2Text($_OBJECTSCOTAIN) . ";\n\n";
            $data .= "\$_OBJECTCONTROLS = " . Array2Text($_OBJECTCONTROLS) . ";\n\n";
            $data .= "\$_OBJECTTEMPLATE = " . Array2Text($_OBJECTTEMPLATE) . ";\n\n";
            $data .= "\n\n?>";
            WriteConfigFile("objects", $data);
            $back = 2;
        }
        $newloc = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . $doFORM->cancel_uri(NULL, $back);
        header('Location: ' . $newloc);
        die();
    }
}

if ($_GET["del"]) {
    $FIELDID = ereg_replace("f", "", $_GET["del"]);
    $DTD->delete_row($_GET["odd"], $FIELDID);
    $newloc = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . $doFORM->cancel_uri();
    header('Location: ' . $newloc);
    die();
}

if ($_GET["up"] || $_GET["down"]) {
    if ($_GET["up"]) {
        $FIELDID = ereg_replace("f", "", $_GET["up"]);
        $GOTO = $FIELDID - 1;
    } elseif ($_GET["down"]) {
        $FIELDID = ereg_replace("f", "", $_GET["down"]);
        $GOTO = $FIELDID + 1;
    }
    $DTD->move($_GET["odd"], $FIELDID, $GOTO);
    $newloc = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . $doFORM->cancel_uri();
    header('Location: ' . $newloc);
    die();
}

if ($_GET["sys2paltform"]) {
    $FIELDID = ereg_replace("o", "", $_GET["sys2paltform"]);
    $DTD->sys2paltform($FIELDID);
    $newloc = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . $doFORM->cancel_uri();
    header('Location: ' . $newloc);
    die();
}

if ($_GET["sys2user"]) {
    $FIELDID = ereg_replace("o", "", $_GET["sys2user"]);
    $DTD->sys2user($FIELDID);
    $newloc = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . $doFORM->cancel_uri();
    header('Location: ' . $newloc);
    die();
}

if (!$_GET["odd"]) {
    if ($_GET["add"]) {
        if ($VALIDATED) $data = $VALIDATED;
        $_BODY .= "<b>##Add new ODD file##</b><br /><br />\n";
        $_BODY .= "<table width=\"80%\" border=\"0\" class=\"\">";
        $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
        $_BODY .= $doFORM->start("odd_new", $data);
        $_BODY .= "</table><br /><br />";
        $_BODY .= "\n</form>\n";
    } else {
        $_BODY .= "<b>##List of ODD files##</b><br /><br />\n<table width=\"98%\">
        <tr class=\"title__\">
            <td width=\"100\"><acronym title=\"##This should not be changed##\">##Name##</acronym></td>
            <td width=\"*\">##Title##</td>
            <td width=\"*\">##Description##</td>
            <td width=\"40\">##Type##</td>
            <td width=\"80\">##Action##</td>
        </tr>
        ";
        foreach($DTD->dtdlist AS $__KEY => $__DTD){
            unset($_starta, $_enda);
            $link = "<a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $__DTD["attributes"]["name"] . "&content=o" . $__KEY . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/folder.png\" border=0 width=\"16\" height=\"16\" title=\"##This object can conatain .... (other objects)##\" align=\"middle\" /></a>&nbsp;";
            if ($__DTD["attributes"]["type"] == "user") {
                $link .= "<a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $__DTD["attributes"]["name"] . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"##Edit##\" align=\"middle\" /></a>\n";
            } elseif ($__DTD["attributes"]["name"] != "platform") {
                $link .= "<a href=\"?admin[]=general&admin[]=odd_edit&sys2user=o" . $__KEY . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/redo.png\" border=0 width=\"16\" height=\"16\" title=\"##Convert to user ODD##\" align=\"middle\" /></a>\n";
            } else {
                $link .= "<img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" border=0 width=\"16\" height=\"16\" title=\"##This ODD can't be edited nor converted.##\" align=\"middle\" />\n";
            }
            $USED[$__DTD["attributes"]["name"]] = "o" . $__KEY;
            $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td>" . $__DTD["attributes"]["name"] . "</td>
                <td>" . $_starta . $__DTD["data"][0]["data"] . $_enda . "</td>
                <td>" . $__DTD["data"][1]["data"] . "</td>
                <td>" . $__DTD["attributes"]["type"] . "</td>
                <td align=\"center\">$link</td>
            </tr>\n";
        }
        $_BODY .= "</table>\n<br />\n<a href=\"?admin[]=general&admin[]=odd_edit&add=true\">+ ##Add new ODD##</a>\n<br /><br />\n";
        $SYSTEM = $DTD->get_odd("system.list");

        $_BODY .= "<b>##List of System ODD files##</b><br />\n<i>##NOTE##: ##You can convert any of this ODD files to user type so you can edit it.##</i>\n<br /><br />\n";
        $_BODY .= "<b>##List of ODD files##</b><br /><br />\n<table width=\"98%\">
        <tr class=\"title__\">
            <td width=\"100\"><acronym title=\"##This should not be changed##\">##Name##</acronym></td>
            <td width=\"*\">##Title##</td>
            <td width=\"*\">##Description##</td>
            <td width=\"80\">##Action##</td>
        </tr>
        ";
        foreach($SYSTEM AS $SYSKEY => $SYSODD) {
            if ($USED[$SYSODD["attributes"]["name"]] != '') {
                $link = "<img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" border=0 width=\"16\" height=\"16\" title=\"##This ODD is used.##\" align=\"middle\" />\n";
            } else {
                $link = "<a href=\"?admin[]=general&admin[]=odd_edit&sys2paltform=o" . $SYSKEY . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" border=0 width=\"16\" height=\"16\" title=\"##Add current ODD to platform##\" align=\"middle\" /></a>\n";
            }
            $_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td>" . $SYSODD["attributes"]["name"] . "</td>
                <td>" . $SYSODD["data"][0]["data"] . "</td>
                <td>" . $SYSODD["data"][1]["data"] . "</td>
                <td align=\"center\">$link</td>
            </tr>\n";
        }
        $_BODY .= "</table>\n<br />\n";
    }
} else if ($_GET["add"]) {
    $_DTD = $_GET["odd"];
    if ($VALIDATED) $data = $VALIDATED;
    $_BODY .= "<b>##Add new  field to ODD## '" . $_DTD . "'</b><br /><br />\n";
    $_BODY .= "<table width=\"80%\" border=\"0\" class=\"\">";
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= $doFORM->start("odd_row", $data);
    $_BODY .= "\n</form>\n";
    $_BODY .= "</table><br /><br />";
    $_BODY .= "<div class=\"block_note_\">" . _ODDLegend() . "</div><br /><br />";

} else if ($_GET["edit"]) {
    $_DTD = $_GET["odd"];
    $DTDDATA = $DTD->get_odd($_DTD);
    $FIELDID = ereg_replace("f", "", $_GET["edit"]);
    $FIELD = $DTDDATA[$FIELDID];
    if ($VALIDATED) {
        $data = $VALIDATED;
    } else {
        $FDATA = ParseODDdata($FIELD["data"]);
        $data["elementname"] = $FIELD["element_name"];
        $data["formfield"] = $FDATA["formfield"];
        $data["description"] = $FDATA["description"];
        $data["field"] = $FDATA["field"];
        $data["lang"] = $FIELD["attributes"]["lang"];
        $data["reqired"] = $FIELD["attributes"]["reqired"];
        $data["drop"] = $FDATA["drop"];
        $data["validate"] = $FDATA["validate"];
        $data["params"] = $FDATA["params"];
        $data["quiz"] = $FDATA["quiz"];
        $data["top"] = $FDATA["top"];
    }
    $_BODY .= "<b>##Edit field##  '" . $DTDDATA[$FIELDID]["data"][0]["data"] . "' ##in## '" . $_DTD . "'</b><br /><br />\n";
    $_BODY .= "<table width=\"80%\" border=\"0\" class=\"\">";
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= $doFORM->start("odd_row", $data);
    $_BODY .= "\n</form>\n";
    $_BODY .= "</table><br /><br />";
    $_BODY .= "<div class=\"block_note_\">" . _ODDLegend() . "</div><br /><br />";
} else if ($_GET["content"]) {
    $_DTD = $_GET["odd"];
    $_BODY .= "<b>##Edit object content##  '" . $_DTD . "'</b><br /><br />";
    $_BODY .= "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    $_BODY .= "<b>##Select objects:##</b><br />";
    foreach ($DTD->dtdlist AS $KEY => $VAL) {
        $checked = (in_array($VAL["attributes"]["name"], (array)$_OBJECTSCOTAIN[$_DTD]))?" checked":"";
        $_BODY .= "<nobr><input type=\"checkbox\" name=\"contain[]\" value=\"" . $VAL["attributes"]["name"] . "\"$checked />&nbsp;" . $VAL["data"][0]["data"] . "</nobr>&nbsp;&nbsp;<br />";
    }
    $_BODY .= "<br /><br /><b>##Slect actions:##</b><br />";
    $CTRL = explode("|", $_OBJECTCONTROLS[$_DTD]["controls"]);
    foreach ($_ACTIONS AS $KEY => $VAL) {
        $checked = (in_array($KEY, $CTRL))?" checked":"";
        $_BODY .= "<nobr><input type=\"checkbox\" name=\"actions[]\" value=\"" . $KEY . "\"$checked />&nbsp;" . $VAL . "</nobr>&nbsp;&nbsp;<br />";
    }
    
    $_BODY .= "<br /><br /><b>##Is actions menu hidden?:##</b><br />";
    $_BODY .= "<nobr><input type=\"radio\" name=\"hide\" value=\"yes\"" . (($_OBJECTCONTROLS[$_DTD]["hide"] == "yes")?" checked":"") . " />&nbsp;Yes&nbsp;&nbsp;<input type=\"radio\" name=\"hide\" value=\"no\"" . (($_OBJECTCONTROLS[$_DTD]["hide"] == "no")?" checked":"") . " />&nbsp;No</nobr>";
    $_BODY .= "\n<br /><br /><input type=\"submit\" value=\"Submit\" /></form>\n";
    $_BODY .= "<br /><br /><br /><div class=\"block_note_\"><b>##Configuration hints##</b><br />
    &nbsp;&nbsp;##You are required to configure objects that will be used in the website!##<br />
    &nbsp;&nbsp;##You do not have to configure 'platform', form2mail, gallery etc..##<br />
    </div><br /><br />";

} else {
    $_DTD = $_GET["odd"];
    $DTDDATA = $DTD->get_odd($_DTD);
    $_BODY .= "<b>##Edit ODD file##  '" . $_DTD . "'</b><br /><br />
    <table width=\"98%\">
    <tr class=\"title__\">
        <td width=\"100\"><acronym title=\"##This should not be changed##\">##Name##</acronym></td>
        <td width=\"*\">##Title##</td>
        <td width=\"20\"><acronym title=\"##Field is required##\">##Rq.##</acronym></td>
        <td width=\"20\"><acronym title=\"##Field is multilingual##\">##Ln.##</acronym></td>
        <td width=\"150\">##Type##</td>
        <td width=\"80\">##Action##</td>
    </tr>
    ";
    
    if (is_array($DTDDATA)) foreach ($DTDDATA AS $KEY => $FIELD) {
        $_BODY .= "
        <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
            <td>" . $FIELD["element_name"] . "</td>
            <td>##" . $FIELD["data"][0]["data"] . "##</td>
            <td>" . (($FIELD["attributes"]["reqired"] == 1)?"<img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />":"<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
            <td>" . (($FIELD["attributes"]["lang"] == 1)?"<img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />":"<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
            <td>##" . $_FTYPES[$FIELD["data"][1]["data"]] . "##</td>";
            $_BODY .= "
            <td align=\"center\">
            <a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $_DTD . "&up=f" . $KEY . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/top.png\" border=0 width=\"16\" height=\"16\" title=\"move up\" align=\"middle\" /></a>
            <a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $_DTD . "&edit=f" . $KEY . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/edit.png\" border=0 width=\"16\" height=\"16\" title=\"edit\" align=\"middle\" /></a>
            <a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $_DTD . "&del=f" . $KEY . "\" onclick=\"return confirm('##Do you want to delete current field?##'); \"><img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" border=0 width=\"16\" height=\"16\" title=\"delete\" align=\"middle\" /></a>
            <a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $_DTD . "&down=f" . $KEY . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/bottom.png\" border=0 width=\"16\" height=\"16\" title=\"move down\" align=\"middle\" /></a>
            </td>
            ";
        $_BODY .= "
        </tr>";
    }
    $_BODY .= "</table><br />\n<a href=\"?admin[]=general&admin[]=odd_edit&odd=" . $_DTD . "&add=true\">+ ##Add new field##</a>\n<br /><br />
    <b>##Form preview##</b><br />
    <i>##NOTE##: ##In normal situation you do not need to add submit buttons in ODD.##<br />##WYSIWYG HTML editor is not visualized for speed.##</i><br /><br />
    <table width=\"80%\" border=\"0\" class=\"bordertable__\">
    ";

    $_BODY .= $doFORM->start($_DTD);
    $_BODY .= "</table><br /><br /><br />";
}


function _ODDLegend () {
    $BODY = "<b>-------------- LEGEND --------------</b><br /><br />
    <li>Validation rules:<br />
    <i>none</i> - ##None (Use it for WYSIWYG fields)##<br />
    <i>email</i> - ##Email## (*@*.*)<br />
    <i>www</i> - ##URL address## (www.*.*)<br />
    <i>number</i> - ##Numbers only## (0-9)<br />
    <i>text</i> - ##Text## (A-Z a-z 0-9)<br />
    <i>date</i> - ##Date## (yyyy-mm-dd)<br />
    <i>time</i> - ##Time## (hh:mm)<br /><br />
    <li>##Extra params##<br />
    ##The format of this parameters is: key=value|key1=value1.##<br />
    <i>##NOTE##: ##Do not use ' or \"##<br /></i>
    ";
    return $BODY;
}

function ParseODDdata($DATA) {
    foreach((array)$DATA AS $DESC) {
        $FOO[$DESC["element_name"]] = $DESC["data"];
        if ($DESC["element_name"] == "field" && is_array($DESC["attributes"])) {
            foreach($DESC["attributes"] AS $KEY => $VAL) {
                $PARAM .= $KEY . "=" . $VAL . "|";
            }
            $FOO["params"] = $PARAM;
        }
    }
    return $FOO;
}

?>