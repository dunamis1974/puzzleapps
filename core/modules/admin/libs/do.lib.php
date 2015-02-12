<?php

global $SYS_LANGUAGES,
       $HTMLIAREANIT,
       $_ZONES,
       $_ZONE_DESC,
       $_ZONESCONTAIN,
       $_OBJECTSCOTAIN,
       $LANGUAGES,
       $EDITLANGUAGE,
       $DEFAULTLANGUAGE,
       $DTD;

if (($_GET["do"] == "down") || ($_GET["do"] == "up")) {
    if ($_GET["parent"]) {
        $P = new CORE($_GET["parent"]);
        if (is_object($P) && (! $P->writeAccess())) {
            header('Location: ' . $_SERVER["HTTP_REFERER"]);
            die();
        }
    }
    
    $elementid = $_GET["id"];
    
    if ($_GET["do"] == "up") {
        $direction = - 1;
    } else if ($_GET["do"] == "down") {
        $direction = 1;
    }
    
    $Obj = $CURRENTPLATFORM->load($elementid);
    
    if ($_GET['parent']) {
        $parent[] = $CURRENTPLATFORM->load($_GET['parent']);
    } else {
        $parent = $Obj->getParentRelations($_GET["relation"]);
    }
    
    if ($parent) {
        $parentObject = $parent[0];
    } else {
        $parentObject = $CURRENTPLATFORM;
    }
    
    if ($_GET["zone"] && ! $zone)
        $zone = $_GET["zone"];
        
    $parentObject->changeOrder($elementid, $direction, $_GET["relation"], $zone);
    header('Location: ' . $_SERVER["HTTP_REFERER"]);
    die();
}

if (($_GET["do"] == "delete") && ($_GET["id"])) {
    $O = new CORE($_GET["id"]);
    if (is_object($O) && ($O->writeAccess())) {
        $O->delete();
    }
    
    header('Location: ' . $_SERVER["HTTP_REFERER"]);
    die();
}

if ($_POST) {
    if (($_GET["do"] == "move") && ($_POST["id"])) {
        $VALIDATED = $UFV->validate($_POST);
        $O = new CORE($VALIDATED["id"]);
        if (! is_object($O))
            $ERROR = true;
        if ($VALIDATED["new_parent"] == - 1 && ! $ERROR) {
            // Deattach from parent
            $O->removeRelation($VALIDATED["parent"]);
        } elseif ($VALIDATED["new_parent"]) {
            // Change parent
            $NEWP = new CORE($VALIDATED["new_parent"]);
            if ($NEWP->id)
                $O->changeParent($VALIDATED["parent"], $VALIDATED["new_parent"]); else
                $ERROR = true;
        }
        // Change zone
        if ($VALIDATED["new_zone"] && ! $ERROR) {
            $O->changeZone($VALIDATED["new_zone"]);
        }
    }
    
    if (($_GET["do"] == "edit") && ($_POST["id"])) {
        $VALIDATED = $UFV->validate($_POST, $_POST["_objectname"]);
        if (!$UFV->hasErrors()) {
            
            if ($_POST["_objectname"] == "person") {
                $O = new PERSON($_POST["id"], $VALIDATED);
            } else {
                $O = new CORE($_POST["id"], $VALIDATED);
            }
            /*
            echo "<pre>";
            print_r($_POST);
            print_r($_FILES);
            print_r($VALIDATED);
            die();
            */
            $O->update();
            $O->permissionsForm($O);
            $O->changeGroup($O->id, $VALIDATED["_group"]);
        }
    }
    
    if (($_GET["do"] == "add") && ($_POST["_objectname"])) {
        //make use of some iternal variables and remove tem from POST
        if ($_POST["_add_on_top"] == - 1) {
            $GLOBALS["ADDOBJECTONTOP"] = false;
            unset($_POST["_add_on_top"]);
        } elseif ($_POST["_add_on_top"] == 1) {
            $GLOBALS["ADDOBJECTONTOP"] = true;
            unset($_POST["_add_on_top"]);
        } elseif ($_POST["_add_on_top"] == "sa") {
            $GLOBALS["ADDOBJECTONTOP"] = true;
            $GLOBALS["SORTOBJECTS"] = true;
            unset($_POST["_add_on_top"]);
        }
        
        $VALIDATED = $UFV->validate($_POST, $_POST["_objectname"]);
        if (! $UFV->hasErrors()) {
            $O = new CORE($VALIDATED);
            $O->insert();
            $O->permissionsForm($O);
            $O->changeGroup($O->id, $VALIDATED["_group"]);
            // Only peopple are self owned
            if ($VALIDATED["_objectname"] == "person")
                $O->changeOwner($O->id, $O->id);
            if ($_POST["_zone"] || $_POST["parent"]) {
                if ($_POST["parent"]) {
                    $P = new CORE($_POST["parent"]);
                } else {
                    $P = $CURRENTPLATFORM;
                }
                $P->addChild($O->id, $_POST["_zone"]);
                if ($GLOBALS["SORTOBJECTS"] == true) {
                    $P->sortChildren($_POST["_zone"]);
                }
            }
        }
    }

}

if ((! $_POST) || ($UFV->hasErrors()) || $ERROR) {
    if ($_GET["id"] && ($_GET["id"] != "NULL")) {
        $O = $CORE->load($_GET["id"]);
        $form = $O->_objectname;
        if (! $_POST) {
            $data = $O->translate_object_data(true);
        } else {
            $data = $_POST;
        }
        $attr = $O->translate_object_atributes();
    } else if ($_GET["odd"]) {
        $form = $_GET["odd"];
    }
    
    if ($UFV->hasErrors())
        $_BODY .= $UFV->getErrors(1);
} else {
    echo "<script type=\"text/javascript\"> opener.top.location.reload(); self.close(); </script>";
    die();
}

if ($_GET["zone"])
    $zone = $_GET["zone"]; else if ($O->_zone)
    $zone = $O->_zone;

if ($_GET["parent"])
    $P = new CORE($_GET["parent"]);

if ((is_object($O) && (! $O->writeAccess())) || (is_object($P) && (! $P->writeAccess()))) {
    $_BODY .= "<br /><br /><table width=\"300\" border=\"0\" align=\"center\">\n<tr><td>";
    $_BODY .= BuildErrorMsg("##You can't edit this object!##");
    $_BODY .= "<br />";
    $_BODY .= "<center><input type=\"button\" value=\" ##Close## \" onclick=\"self.close();\"></center>";
    $_BODY .= "</td></tr></table>
    <br /><br />";
} else {
    if ($DTD->odd_multilang($odd) && count($LANGUAGES) > 0 && ($_GET["do"] == "edit" || $_GET["do"] == "add")) {
        $_BODY .= "<p align=\"center\" class=\"remotePath\">edit language: ";
        for($l = 0; $l < count($LANGUAGES); $l++) {
            if (($LANGUAGES[$l] == __get_language_abr($EDITLANGUAGE))) { // || ((! $_GET["language"]) && ($l == 0))
                $_BODY .= "<span class=\"selected__\"> [ ##" . $SYS_LANGUAGES[$LANGUAGES[$l]]["title"] . "## ]</span> ";
            } else if ($_GET["do"] != "add") {
                $_BODY .= " [ <a href=\"?admin[]=content&load=pools&view=" . $_GET["view"] . "&do=" . $_GET["do"] . "&id=" . $_GET["id"] . "&language=" . $LANGUAGES[$l] . "\" class=\"\">##" . $SYS_LANGUAGES[$LANGUAGES[$l]]["title"] . "##</a> ] ";
            }
        }
        $_BODY .= "</p>";
    } else if ($_GET["do"] == "move") {
        $_BODY .= "<p align=\"center\" class=\"remotePath\">##Move object## [{$O->id}]</p>";
    }
    
    $_BODY .= "\n<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">\n";
    
    if ($O) {
        $_BODY .= "<input type=\"hidden\" name=\"id\" value=\"" . $O->id . "\">\n";
    } else if ($_GET["elementid"]) {
        $_BODY .= "<input type=\"hidden\" name=\"id\" value=\"" . $_GET["elementid"] . "\">\n";
    }
    
    /**
     * Need to load parent take his permissions and clone them here.
     */
    if (is_object($P)) {
        //$P = new CORE($_GET["parent"]);
        $_BODY .= "<input type=\"hidden\" name=\"parent\" value=\"" . $P->id . "\">\n";
    } else {
        $P = NULL;
    }
    if ($form)
        $_BODY .= "<input type=\"hidden\" name=\"_objectname\" value=\"" . $form . "\">\n";
    if ($zone)
        $_BODY .= "<input type=\"hidden\" name=\"_zone\" value=\"" . $zone . "\">\n";
    if (($form != "person") && (is_object($P)) && (is_object($O)) && ($P->_o < 6) && ($O->_o < 6)) {
        // Test for write access
        $_err_msg = "##You can't edit this object!##";
        if ($CURRENTUSER->isAllowed("chmod"))
            $_err_msg .= "\n##You can change permissions.##";
        if ($CURRENTUSER->isAllowed("chown"))
            $_err_msg .= "\n##You can change groups.##";
        $_BODY .= "<div class=\"formrow\"><br />\n";
        $_BODY .= BuildErrorMsg($_err_msg);
        $_BODY .= "<br /></div>";
        
        $_CANTEDIT = true;
    }
    
    if ($_GET["do"] == "move") {
        $_BODY .= "<input type=\"hidden\" name=\"__secval__\" value=\"" . $doFORM->form_sec_val() . "\">\n";
        if (! $_GET["parent"]) {
            $P = $O->getParent();
            $_GET["parent"] = $P->id;
            $_BODY .= "<input type=\"hidden\" name=\"parent\" value=\"" . $P->id . "\">\n";
        }
        
        if (is_array($_OBJECTSCOTAIN)) {
            $key = array_keys($_OBJECTSCOTAIN);
            $end = count($_OBJECTSCOTAIN);
            for($i = 0; $i < $end; $i++) {
                if (in_array($O->_objectname, (array)$_OBJECTSCOTAIN[$key[$i]])) {
                    $_MOVETO = $CURRENTPLATFORM->getAllOfType($key[$i]);
                    if (is_array($_MOVETO)) {
                        foreach ($_MOVETO as $_OBJ) {
                            $DATA = $_OBJ->translate_object_data();
                            $_OPT .= "<option value=\"" . $_OBJ->id . "\"" . (($_OBJ->id == $_GET["parent"])?" selected=\"true\"":"") . ">[" . $_OBJ->id . "] " . $DATA["title"] . "</option>\n";
                        }
                    }
                }
            }
        }
        
        if (is_array($_ZONESCONTAIN)) {
            $key = array_keys($_ZONESCONTAIN);
            $end = count($_ZONESCONTAIN);
            for($i = 0; $i < $end; $i++) {
                if (in_array($O->_objectname, $_ZONESCONTAIN[$key[$i]])) {
                    $_OPTZ .= "<option value=\"" . $key[$i] . "\"" . (($O->_zone == $key[$i])?" selected=\"true\"":"") . ">" . $key[$i] . "</option>\n";
                }
            }
        }
        
        $_BODY .= "
        <div class=\"formrow\"><br />\n
        <div class=\"mandatory__\">##Select new parent##</div>
        <select name=\"new_parent\" class=\"input\">
            <option value=\"-1\">No parent</option>
            <option value=\"" . $CURRENTPLATFORM->id . "\"" . (($CURRENTPLATFORM->id == $_GET["parent"])?" selected=\"true\"":"") . ">To platform</option>
            " . $_OPT . "
        </select>
        </div>
        <div class=\"formrow\"><br />\n
        <div class=\"non_mandatory__\">##Change zone##</div>
        <select name=\"new_zone\" class=\"input\">
            <option value=\"-1\">No zone</option>
            " . $_OPTZ . "
        </select>
        </div>
        ";
    } elseif ($_GET["do"] == "delete") {
        $_BODY .= "##This will delete current object.<br>To proceed click \"Delete\". To cancel click \"Cancel\"##.<p>";
    } else {
        if ($O) {
            $doFORM->ADVANCEDADD = false;
        } else {
            $doFORM->ADVANCEDADD = true;
        }
        
        $doFORM->NOEDIT = $_CANTEDIT;
        $hide = null;
        
        /*
         * remove some advanced fields
         * if object is person
         */
        if ($form == "person")
            $doFORM->ADVANCEDADD = false;
            
        $_BODY .= $doFORM->start($form, $data, $hide);
    }
    
    $_BODY .= "<br />";
    // Display permissions forms
    if (($_GET["do"] != "delete") && ($_GET["do"] != "move")) {
        if ($O) {
            $_BODY .= $O->permissionsForm($O, false);
            $_BODY .= "<br />";
            $_BODY .= $O->groupsForm($O);
        } elseif ($P) {
            $_BODY .= $P->permissionsForm($P, false);
            $_BODY .= "<br />";
            $_BODY .= $P->groupsForm($P);
        } elseif ($CURRENTUSER) {
            $_BODY .= PERMISSIONS::permissionsForm($CURRENTPLATFORM, false);
            $_BODY .= "<br />";
            $_BODY .= $CURRENTUSER->groupsForm($CURRENTUSER);
        } else {
            $_BODY .= PERMISSIONS::permissionsForm($CURRENTPLATFORM, false);
            $_BODY .= "<br />";
            $_BODY .= PERMISSIONS::groupsForm($CURRENTPLATFORM);
        }
    }
    
    $_BODY .= "<div class=\"formrow\"><br /><br />\n";
    $_BODY .= "<span class=\"col1button\">\n";
    
    if ($_GET["do"] == "delete") {
        $_BODY .= "<input type=\"submit\" value=\" ##Delete## \">\n";
    } else {
        $_BODY .= "<input type=\"submit\" value=\" ##Submit## \">\n";
    }
    
    $_BODY .= "</span>\n";
    $_BODY .= "<span class=\"col2button\">";
    $_BODY .= "<input type=\"button\" value=\" ##Cancel## \" onclick=\"self.close();\">";
    $_BODY .= "</span>\n";
    $_BODY .= "</div>\n";
    
    $_BODY .= "\n</form>\n";
}

?>