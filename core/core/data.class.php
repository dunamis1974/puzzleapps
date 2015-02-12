<?php

/**
 * DATA
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 */

class DATA extends CORE
{

    public $_CLASS = __CLASS__;

    /**
     * Get Data by Zones
     * 
     * DATA::GetZonesData()
     * 
     * @param  $id 
     * @return 
     */
    
    function GetZonesData($id)
    {
        global $_ZONES, $_ZONESEXPAND, $_ZONESDEFAULT, $_ZONESCONTAIN, $_ZONESPERMANENT, $_ZONESIGNORESELECT, $_DATA_FILTER, $PLATFORMID;
        
        $_TREE = array(
            
        );
        
        if ($id) {
            $_TREE = $this->getTree($id);
            $CURRENTOBJECT = $this->load($id);
            $this->CURRENTOBJECT = $CURRENTOBJECT;
        } else {
            $usedef = true;
            $id = $PLATFORMID;
        }
        
        /**
         * Here we will handle files
         */
        if ($CURRENTOBJECT->_objectname == "file") {
            $XML = "<file date='" . $CURRENTOBJECT->_date . "' parentid='" . $PLATFORMID . "' zone='NULL' id='" . $CURRENTOBJECT->id . "'>\n";
            $XML .= $this->html_entity_decode($CURRENTOBJECT->data, ENT_QUOTES, "UTF-8");
            $XML .= "</file>\n";
            return $XML;
        }
        
        /**
         * If this is not file
         * we will continue execution
         */
        
        if ($_GET["fltr"])
            $_FILTER = $_DATA_FILTER[$_GET["fltr"]]; else
            $_GET["fltr"] = "none";
        
        if ($CURRENTOBJECT->_module)
            $XML .= $this->AddModule($CURRENTOBJECT->_module, 1);
            
        // Add data from preloaded modules if some
        $XML .= $this->AddPreModules();
        
        $ZONES_ = $_ZONES;
        
        foreach ($_ZONESPERMANENT as $ZONE) {
            unset($ZONES_[array_search($ZONE, $ZONES_)]);
            
            if (in_array($ZONE, (array)$GLOBALS["zone_disabled"]))
                continue;
            
            $data = array(
                
            );
            if ($_FILTER["zone"] == $ZONE) {
                //echo "filter not completed for zone: " . $ZONE . "<br />";
            } else {
                $data = $this->getRelations(null, $ZONE);
            }
            if (is_array($data)) {
                foreach ($data as $O) {
                    if ($usedef && ($_ZONESDEFAULT == $ZONE)) {
                        $id = $O->id;
                        $_GET["id"] = $id;
                        if ($O->_module)
                            $XML .= $this->AddModule($O->_module, 1);
                        $usedef = null;
                        $this->CURRENTOBJECT = $O;
                    }
                    if ((in_array($O->id, $_TREE)) || ($O->id == $id) || ($O->id == $CURRENTOBJECT->id)) {
                        $add = ' tree="true"';
                        $BRANCH = $O;
                        $this->BRANCH = $BRANCH;
                        if ($O->_xslt)
                            $this->XSLT = $O->_xslt;
                    } else if (in_array($ZONE, (array)$_ZONESEXPAND)) {
                        $add = ' include="true"';
                    } else {
                        $add = null;
                    }
                    if ($O->writeAccess())
                        $add .= " canedit='1'";
                    
                    $_tag = $O->_objectname;
                    if ($_tag == "container") {
                        $XML .= $this->PrintContainer($O, $add, $_tag);
                    } else {
                        $XML .= "<$_tag date='" . $O->_date . "' parentid='" . $PLATFORMID . "' zone='" . $O->_zone . "' id='" . $O->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
                        $XML .= $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8");
                        $XML .= "</$_tag>\n";
                        if ((count($_ZONESCONTAIN[$ZONE]) > 0) && $add) {
                            $XML .= $this->RecurseZone($O, $_TREE);
                        }
                    }
                }
            }
            unset($ZONE);
        }
        
        $ZONES__ = $ZONES_;
        foreach ($ZONES_ as $ZKEY => $ZONE) {
            if (! in_array($ZONE, $ZONES__))
                continue;
            
            $data = array(
                
            );
            
            if (in_array($ZONE, (array)$GLOBALS["zone_disabled"]))
                continue;
            
            $this->OLDBRANCH = $this->BRANCH;
            
            if ((is_array($_ZONESIGNORESELECT)) && (! in_array($ZONE, $_ZONESIGNORESELECT)) && ($CURRENTOBJECT->_zone == $ZONE)) {
                $data[] = $CURRENTOBJECT;
                //$_SEL = 1;
            } else if (is_object($this->BRANCH)) {
                if ($_FILTER["zone"] == $ZONE) {
                    if ($this->BRANCH->_zone == $_FILTER["parent"]) {
                        $data = $this->BRANCH->getChildrenByTypeAndParam($_FILTER["object"], $_FILTER["type"], $_FILTER["param"]);
                    } else {
                        $data = $this->BRANCH->getByZoneTypeAndParam($_FILTER["object"], $ZONE, $_FILTER["type"], $_FILTER["param"]);
                    }
                } else {
                    $data = $this->BRANCH->getRelations(null, $ZONE);
                }
                //$_SEL = 2;
            } else {
                $PLATFORM_ = $this->load($PLATFORMID);
                if ($_FILTER["zone"] == $ZONE)
                    $data = $PLATFORM_->getByZoneTypeAndParam($_FILTER["object"], $ZONE, $_FILTER["type"], $_FILTER["param"]); else
                    $data = $PLATFORM_->getRelations(null, $ZONE);
                //$_SEL = 3;
            }
            if (is_array($data)) {
                foreach ($data as $O) {
                    if ((in_array($O->id, $_TREE)) || ($O->id == $id) || ($O->id == $CURRENTOBJECT->id)) {
                        $add = ' tree="true"';
                        $BRANCH = $O;
                        $this->BRANCH = $BRANCH;
                        if ($O->_xslt)
                            $this->XSLT = $O->_xslt;
                    } else {
                        $add = null;
                    }
                    if ($O->writeAccess())
                        $add .= " canedit='1'";
                        // get tag name (same as object name)
                    $_tag = $O->_objectname;
                    if ($_tag == "container") {
                        //echo $_SEL;
                        //if ($_SEL != 3) {
                        $_XML = $this->PrintContainer($O, $add, $_tag);
                        //} else {
                        //    $_XML = $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8");
                        //}
                        $XML .= $_XML;
                    } else {
                        $XML .= "<$_tag date='" . $O->_date . "' parentid='" . $this->OLDBRANCH->id . "' zone='" . $O->_zone . "' id='" . $O->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
                        $XML .= $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8");
                        $XML .= "</$_tag>\n";
                        if ($O->id == $CURRENTOBJECT->id) {
                            $ZONES__ = $ZONES_;
                            unset($ZONES__[$ZKEY]);
                            $XML .= $this->RecurseNormalZones($O, $ZONES__);
                        } else if ((count($_ZONESCONTAIN[$ZONE]) > 0) && $add) {
                            $XML .= $this->RecurseZone($O, $_TREE);
                        }
                    }
                }
            }
            unset($ZONE);
        }
        //die();
        return $XML;
    }

    function PrintContainer($O, $add, $_tag)
    {
        global $EDITLANGUAGE;
        //echo "$O, $add, $_tag";
        $DATAVAL = "data_" . $EDITLANGUAGE;
        $XML_ = "<$_tag date='" . $O->_date . "' parentid='" . $this->OLDBRANCH->id . "' zone='" . $O->_zone . "' id='" . $O->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
        $XML_ .= $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8");
        $XML_ .= "</$_tag>\n";
        $O->$DATAVAL = $XML_;
        
        $_XML = "<$_tag parentid='" . $this->OLDBRANCH->id . "' zone='" . $O->_zone . "' id='" . $O->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
        $_XML .= $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8");
        // Add data from container
        $_XML .= $this->Container($O);
        $_XML .= "</$_tag>\n";
        
        return $_XML;
    }

    /**
     * DATA::Container()
     * 
     * @param  object $Obj 
     * @return string $XML
     */
    function Container($Obj)
    {
        
        $C = $Obj->translate_object_data();
        //$Obj->dump();
        // Type POOL
        if ($C["type"] == "pool" || $C["type"] == "filterpool") {
            if ($C["type"] == "filterpool") {
                $params = explode("|", $C["filter"]);
                $data = $this->getByTypeAndParam($C["pool"], $params[0], $params[1], true);
            } else {
                $data = $this->getAllOfType($C["pool"]);
            }
            
            if ($C["random"] == 1) {
                if (count($data) < $C["count"])
                    $C["count"] = count($data);
                $random_ = array_rand($data, $C["count"]);
                
                if (($random_ != '') && (! is_array($random_)))
                    $random_ = (array)$random_;
                
                for($i = 0; $i < $C["count"]; $i++) {
                    if (is_object($data[$random_[$i]])) {
                        $random[] = $data[$random_[$i]];
                    }
                }
                if (count($random) < 1 || (! is_array($random)))
                    $random[] = $data[0];
                    // Take XML data
                //$this->dump($random);
                $data = $random;
            }
            
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                $XML .= "<{$C["pool"]} id=\"{$data[$i]->id}\">\n{$data[$i]->data}\n</{$C["pool"]}>\n";
            }
            
            return $XML;
        }
        
        // Type Object
        if ($C["type"] == "object") {
            $data = new CORE($C["object"]);
            $XML = $data->data;
            return $XML;
        }
        
        // Type PHP
        if ($C["type"] == "php" && $C["exact"]) {
            ob_start();
            eval($C["exact"]);
            
            $XML = "<result><![CDATA[" . ob_get_contents() . "]]></result>";
            ob_end_clean();
            return $XML;
        }
        
        // Type MODULE
        if ($C["type"] == "module" && $C["module"]) {
            $XML = $this->AddModule($C["module"], false);
            return $XML;
        }
        
        // Type Category
        if ($C["type"] == "category" || $C["type"] == "filtercategory") {
            $O = new CORE($C["object"]);
            if ($C["type"] == "filtercategory") {
                $params = explode("|", $C["filter"]);
                $data = $O->getChildrenByTypeAndParam($C["pool"], $params[0], $params[1], true);
            } else {
                $data = $O->getRelations(NULL, $C["onlyzone"]);
            }
            
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                if ($C["count"] && ($i >= $C["count"]))
                    break;
                $XML .= $data[$i]->data;
            }
            return $XML;
        }
        // Type RSS
        if ($C["type"] == "rss" || $C["type"] == "rdf" || $C["type"] == "atom") {
            if ($C["url"]) {
                $lines = file($C["url"]);
                $end = count($lines);
                for($i = 0; $i < $end; $i++) {
                    if (! eregi("<\?xml", $lines[$i]))
                        $XML .= $lines[$i];
                }
                $XML = utf8_encode($XML);
                return $XML;
            }
        }
        return null;
    }

    /**
     * DATA::RecurseNormalZones()
     * 
     * @param  $Obj 
     * @param  $ZONES_ 
     * @return 
     */
    function RecurseNormalZones(&$Obj, &$ZONES)
    {
        $XML = NULL;
        foreach ($ZONES as $ZKEY => $ZONE) {
            $data = $Obj->getRelations(null, $ZONE);
            //CORE::dump($Obj->id);
            if (is_array($data)) {
                foreach ($data as $O) {
                    if ($Obj->id == $O->id)
                        break;
                    
                    if ($O->writeAccess())
                        $add = " canedit='1'";
                    $_tag = $O->_objectname;
                    $XML .= "<$_tag date='" . $O->_date . "' first=\"true\" parentid='" . $Obj->id . "' zone='" . $ZONE . "' id='" . $O->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
                    $XML .= $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8") . "\n";
                    $XML .= "</$_tag>\n";
                }
                unset($ZONES[$ZKEY]);
            }
        }
        return $XML;
    }

    /**
     * DATA::RecurseZone()
     * 
     * @param  $Obj 
     * @param  $_TREE 
     * @return 
     */
    function RecurseZone($Obj, $_TREE)
    {
        global $_ZONESEXPAND;
        //echo "--------------------------<br />";
        //CORE::dump($Obj->id);
        //echo "--------------------------<br />";
        $data = $Obj->getRelations(null, $Obj->_zone);
        if (is_array($data)) {
            foreach ($data as $O) {
                if ((in_array($O->id, $_TREE)) || ($O->id == $Obj->id)) {
                    // echo $O->id . "<br />";
                    $add = ' tree="true"';
                    $BRANCH = $O;
                    $this->BRANCH = $BRANCH;
                    if ($O->_xslt)
                        $this->XSLT = $O->_xslt;
                } else if (in_array($O->_zone, (array)$_ZONESEXPAND)) {
                    $add = ' include="true"';
                } else {
                    $add = null;
                }
                if ($O->writeAccess())
                    $add .= " canedit='1'";
                $_tag = $O->_objectname;
                $XML .= "<$_tag date='" . $O->_date . "' parentid='" . $Obj->id . "' zone='" . $O->_zone . "' id='" . $O->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
                $XML .= $this->html_entity_decode($O->data, ENT_QUOTES, "UTF-8") . "\n";
                $XML .= "</$_tag>\n";
                if ($add) {
                    // $XML .=  (added for bulgarian in properties)
                    // if everything is ok this comment should be deleted
                    $XML .= $this->RecurseZone($O, $_TREE);
                }
            }
            return $XML;
        } else {
            return null;
        }
    }

    /**
     * This fuction colects data from DB and compiles her into XML
     * Data reading is (will be) based on platform configuration
     * and objects configuration.
     * 
     * DATA::GetData()
     * 
     * @param  $id 
     * @param  $parent 
     * @return string string $XML
     * @access public 
     */
    function GetData($id = null, $parent = null)
    {
        global $_OBJECTSCOTAIN, $PLATFORMID, $CURRENTLANGUAGE, $DTD, $DB;
        
        $_TREE = array(
            
        );
        
        if (! $id && $parent) {
            $selected = $parent;
            $_TREE = $this->getTree($selected);
        } else if ($id) {
            $_TREE = $this->getTree($id);
        }
        if (! $id && ! $parent)
            $usedef = true;
        if (! $id)
            $id = $PLATFORMID;
        
        $sql = "
        SELECT g.*, d.*
        FROM
            global g, data d, relations r
        WHERE
            r.parentid = '$id' AND
            g.id = r.targetid AND
            d.gid = g.id AND
            d.langid = '$CURRENTLANGUAGE'
        ORDER BY r.`order`
        ";
        $data = $DB->getAll($sql);
        $end = count($data);
        for($i = 0; $i < $end; $i++) {
            if ($usedef && $i == 0)
                $selected = $data[$i]->id;
            if ((in_array($data[$i]->id, $_TREE)) || ($data[$i]->id == $selected))
                $add = ' tree="true"'; else
                $add = null;
            
            $_tag = $DTD->get_object_name($data[$i]->_object);
            $XML .= "<$_tag date='" . $data[$i]->_date . "' parentid='" . $id . "' zone='" . $data[$i]->_zone . "' id='" . $data[$i]->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>\n";
            $XML .= $this->html_entity_decode($data[$i]->data, ENT_QUOTES, "UTF-8") . "\n";
            $XML .= "</$_tag>\n";
            if (($data[$i]->id == $selected) && (count($_OBJECTSCOTAIN[$_tag]) > 0)) {
                $XML .= $this->GetData($data[$i]->id);
            }
        }
        return $XML;
    }

    /**
     * This fuction colects data from DB and compiles her into XML
     * Data reading is (will be) based on platform configuration
     * and objects configuration.
     * 
     * DATA::GetAllData()
     * 
     * @param  $id 
     * @param  $parent 
     * @return string string $XML
     * @access public 
     */
    function GetAllData($id = null, $parent = null)
    {
        global $_OBJECTSCOTAIN, $PLATFORMID, $CURRENTLANGUAGE, $DTD, $DB;
        
        $_TREE = array(
            
        );
        
        if (! $id && $parent) {
            $selected = $parent;
            $_TREE = $this->getTree($selected);
        } else if ($id) {
            $_TREE = $this->getTree($id);
        }
        if (! $id && ! $parent)
            $usedef = true;
        if (! $id)
            $id = $PLATFORMID;
        
        $sql = "
        SELECT g.*, d.*
        FROM
            global g, data d, relations r
        WHERE
            r.parentid = '$id' AND
            g.id = r.targetid AND
            d.gid = g.id AND
            d.langid = '$CURRENTLANGUAGE'
        ORDER BY r.`order`
        ";
        $data = $DB->getAll($sql);
        $end = count($data);
        for($i = 0; $i < $end; $i++) {
            if ($usedef && $i == 0)
                $selected = $data[$i]->id;
            if ((in_array($data[$i]->id, $_TREE)) || ($data[$i]->id == $selected))
                $add = ' tree="true"'; else
                $add = null;
            
            $_tag = $DTD->get_object_name($data[$i]->_object);
            $XML .= "<$_tag date='" . $data[$i]->_date . "' parentid='" . $id . "' zone='" . $data[$i]->_zone . "' id='" . $data[$i]->id . "' _o='" . $O->_o . "' _g='" . $O->_g . "' _w='" . $O->_w . "'$add>";
            $XML .= $this->html_entity_decode($data[$i]->data, ENT_QUOTES, "UTF-8");
            $XML .= "</$_tag>\n";
            if ((count($_OBJECTSCOTAIN[$_tag]) > 0)) {
                $XML .= $this->GetData($data[$i]->id);
            }
        }
        return $XML;
    }

    /**
     * 1. Colect XML data from DB
     * 2. Transform data from XML to HTML
     * 3. Send HTML to user
     * DATA::DataColector()
     * 
     * @return 
     */
    function DataColector()
    {
        global $CURRENTUSER, $CURRENTLANGUAGE, $CURRENTENCODING, $CURRENTPLATFORM, $PLATFORMID, $XSLTROOT, $_TEXTID, $_PREDATA, $_ZONES, $_ZONEALWAYS, $_ZONESPERMANENT, $_OBJECTTEMPLATE, $_OBJECTCONTROLS, $_GENERAL_MOD_XSLT;
        
        $GLOBALS["time_xml_start"] = getmicrotime();
        
        $THISUSER = $this->load($CURRENTUSER->id);
        
        if ($_GET["tid"]) {
            $data = $CURRENTPLATFORM->getByExactValue($_TEXTID, $_GET["tid"]);
            if (count($data) > 0) {
                $ID = $data[0]->id;
            }
        } else {
            $ID = $_GET["id"];
        }
        
        if ($CURRENTPLATFORM->writeAccess())
            $add .= " canedit='1'";
        
        $xml = "<platform id=\"" . $PLATFORMID . "\" _o='" . $CURRENTPLATFORM->_o . "' _g='" . $CURRENTPLATFORM->_g . "' _w='" . $CURRENTPLATFORM->_w . "'$add>\n";
        if ($CURRENTUSER->testAccess()) {
            $xml .= $this->GetZonesData($ID);
        } else {
            $xml .= $this->AddModule("my", false);
        }
        $xml .= "<currentuser>\n";
        $xml .= $THISUSER->data;
        $xml .= "</currentuser>\n";
        $xml .= $_PREDATA;
        
        $xml .= "<internal>\n";
        if ($CURRENTUSER->isAllowed("edit") || ($CURRENTUSER->isAllowed("delete")) || ($CURRENTUSER->isAllowed("move"))) {
            $xml .= "<adminmode>1</adminmode>\n";
        } else {
            $xml .= "<adminmode>0</adminmode>\n";
        }
        $xml .= "<lang>" . $CURRENTLANGUAGE . "</lang>\n";
        foreach ($_GET as $key => $value)
            if ($value)
                $xml .= "<get$key>" . $value . "</get$key>\n";
        $xml .= "<head><![CDATA[" . $GLOBALS["_HEAD"] . "]]></head>";
        $xml .= "</internal>\n";
        $xml .= "</platform>\n";
        
        $GLOBALS["time_xml_end"] = getmicrotime();
        
        /**
         * This is style sheet.
         * Later she can be dynamicaly changed!
         */
        
        if ($_GET["xslt"]) {
            $XSLDOC = $_GET["xslt"];
        } else if ($this->MXSLT) {
            $XSLDOC = $this->MXSLT;
        } else if ($this->XSLT) {
            $XSLDOC = $this->XSLT;
        } else if (! empty($_OBJECTTEMPLATE[$this->CURRENTOBJECT->_objectname])) {
            $XSLDOC = $_OBJECTTEMPLATE[$this->CURRENTOBJECT->_objectname];
        } else {
            $XSLDOC = "index.xsl";
        }
        $lang_abr = __get_language_abr($CURRENTLANGUAGE);
        if (file_exists("./xslt/{$lang_abr}_{$XSLDOC}"))
            $XSLDOC = "{$lang_abr}_{$XSLDOC}";
            /*
         * Solving some language problem (only one time experienced)
         */
        if (! $CURRENTENCODING)
            $CURRENTENCODING = "UTF-8";
        
        if (($GLOBALS["_TRANSFORM"] == "mod-xslt") && (! $CURRENTUSER->isAllowed("admin")) && (! $_GET["debug"])) {
            $xml = "<?xml-stylesheet type=\"text/xsl\" href=\"./xslt/" . $XSLDOC . "\"?>\n" . $xml;
            $xml = "<?xml version=\"1.0\" encoding=\"$CURRENTENCODING\"?>\n" . $xml;
            header("Content-Type: {$_GENERAL_MOD_XSLT}; charset=$CURRENTENCODING");
            die($xml);
        } else {
            $xml = "<?xml version=\"1.0\" encoding=\"$CURRENTENCODING\"?>\n" . $xml;
        }
        
        $xsl_ = file($XSLTROOT . $XSLDOC);
        $xsl = implode("", $xsl_);
        
        /**
         * Create admin controls
         * if user has permissions
         */
        if (($CURRENTUSER->_loggedin) || ($noadmin == true)) {
            $ZONES_ = $_ZONES;
            foreach ($_ZONESPERMANENT as $ZONE) {
                unset($ZONES_[array_search($ZONE, $ZONES_)]);
                $controls = $this->doControls($ZONE, array(
                    "controls" => "new", "zone" => $ZONE, "hide" => "yes", "cat" => $ZONE, "edit" => "zone"
                ));
                foreach ($controls as $KEY => $ROW) {
                    $replace = $KEY;
                    $xsl = ereg_replace($replace, $ROW, $xsl);
                }
            }
            
            foreach ($ZONES_ as $ZONE) {
                if (is_object($this->BRANCH)) {
                    $controls = $this->doControls($ZONE, array(
                        "controls" => "other", "zone" => $ZONE, "hide" => "no", "parent" => $this->BRANCH->id, "cat" => "NULL", "edit" => "zone"
                    ));
                } else if ($_ZONEALWAYS[$ZONE] == true) {
                    $controls = $this->doControls($ZONE, array(
                        "controls" => "other", "zone" => $ZONE, "hide" => "no", "parent" => $PLATFORMID, "cat" => "NULL", "edit" => "zone"
                    ));
                }
                
                foreach ($controls as $KEY => $ROW) {
                    $replace = $KEY;
                    $xsl = ereg_replace($replace, $ROW, $xsl);
                }
            }
            
            foreach ($_OBJECTCONTROLS as $KEY => $OBJ) {
                $controls = $this->doControls("object", array(
                    "controls" => $OBJ["controls"], "zone" => "{@zone}", "odd" => $KEY, "hide" => $OBJ["hide"], "cat" => "@id", "parent" => "{@parentid}", "edit" => "object"
                ));
                $keys = array_keys($controls);
                $xsl = ereg_replace($keys[0], $controls[$keys[0]], $xsl);
            }
            
            $controls = $this->doControls("body", array(
                "controls" => "admin|logout", "hide" => "no", "hideid" => true, "edit" => "body"
            ));
            
            //print_r($controls);
            $keys = array_keys($controls);
            $xsl = ereg_replace("</body>", $controls[$keys[0]] . "\n</body>", $xsl);
        } else {
            $xsl = $this->CleanXSL($xsl);
        }
        
        /*
         * Add some more information to the template
         * /

        if ($CURRENTUSER->isAllowed("edit") || ($CURRENTUSER->isAllowed("delete")) || ($CURRENTUSER->isAllowed("move"))) $xsl = ereg_replace("<xsl:param name=\"adminmode\" select=\"0\" />", "<xsl:param name=\"adminmode\" select=\"1\" />", $xsl);
        $_FOR_MORE = "<xsl:param name=\"lang\" select=\"" . $CURRENTLANGUAGE . "\" />\n";
        foreach ($_GET AS $key => $value) if ($value) $_FOR_MORE .= "<xsl:param name=\"get$key\" select=\"'" . $value . "'\" />\n";

        $xsl = ereg_replace("<!--MORE-->", $_FOR_MORE, $xsl);
        */
        
        /**
         * some debug only if admin
         */
        if (($_GET["debug"] == "xml") && ($CURRENTUSER->isAllowed("admin"))) {
            header("Content-Type: application/xml; charset=UTF-8");
            die($xml);
        }
        if (($_GET["debug"] == "xsl") && ($CURRENTUSER->isAllowed("admin"))) {
            header("Content-Type: application/xsl; charset=UTF-8");
            die($xsl);
        }
        
        $GLOBALS["time_prep_end"] = getmicrotime();
        
        //$xml = utf8_encode($xml);
        

        $HTML = $this->XSLAL($xml, $xsl);
        
        $HTML = $this->ApplyFilters($HTML);
        
        return $HTML;
    }

    /**
     * cleaning XSL from admin tags
     * 
     * DATA::CleanXSL()
     * 
     * @param  $xsl 
     * @return string $xsl
     * @access private 
     */
    function CleanXSL($xsl)
    {
        global $ADMIN_ADV_CONTROLS;
        
        if ($ADMIN_ADV_CONTROLS)
            $xsl = preg_replace(array(
                '/( pas="[a-z0-9-_]*")/i', '/( pasobject="[a-z0-9-_]*")/i'
            ), array(
                "", ""
            ), $xsl);
        
        return $xsl;
    }

    /**
     * cleaning XSL from admin tags
     * 
     * DATA::CleanXSL()
     * 
     * @param  $xsl 
     * @return string $xsl
     * @access private 
     */
    function ApplyFilters($html)
    {
        global $FILTERS;
        
        if (count($FILTERS) > 0) {
            foreach ($FILTERS as $fltr => $val) {
                if ($val == true) {
                    Modules::LoadModule($fltr);
                    $function = "filter_" . $fltr;
                    $html = $function($html);
                }
            }
        }
        
        return $html;
    }

    /**
     * XSL Abstraction layer
     * 
     * DATA::XSLAL()
     * 
     * @param  $xml 
     * @param  $xsl 
     * @return string $HTML
     * @access public 
     */
    function XSLAL($xml, $xsl)
    {
        $v = (int)phpversion();
        if ($GLOBALS["_TRANSFORM"] == "command") {
            
            // Create temp file name
            if (! file_exists("tmp"))
                mkdir("tmp");
            $tmpname = "./tmp/" . md5(session_id() . microtime());
            // Create temp XML file
            $_xml = $tmpname . ".xml";
            $fp = fopen($_xml, "w");
            fwrite($fp, $xml);
            fclose($fp);
            // Create temp XSL file
            $_xsl = $tmpname . ".xsl";
            $fp = fopen($_xsl, "w");
            fwrite($fp, $xsl);
            fclose($fp);
            // Do transformation
            $files = array(
                $_xsl, $_xml
            );
            $strings = array(
                "%xsl", "%xml"
            );
            $cmd = str_replace($strings, $files, $GLOBALS["_XSLTCOMMAND"]);
            $HTML = `$cmd`;
            //exec("sabcmd $_xsl $_xml", $resultArray);
            //reset($resultArray);
            //$HTML = implode("\n", $resultArray);
            unlink($_xml);
            unlink($_xsl);
        } elseif ($GLOBALS["_TRANSFORM"] == "domxml") {
            $xmldoc = domxml_open_mem($xml);
            $xsldoc = domxml_xslt_stylesheet($xsl);
            $result = $xsldoc->process($xmldoc);
            $HTML = $xsldoc->result_dump_mem($result);
        } else {
            if ($v < 5) {
                $arguments = array(
                    '/_xml' => $xml, '/_xsl' => $xsl
                );
                $xh = xslt_create();
                $HTML = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', null, $arguments);
                xslt_free($xh);
            } else {
                $dom_xml = new DomDocument();
                $dom_xml->loadXML($xml);
                
                $dom_xsl = new DomDocument();
                $dom_xsl->loadXML($xsl);
                
                $proc = new xsltprocessor();
                $proc->importStyleSheet($dom_xsl);
                $HTML = $proc->transformToXML($dom_xml);
            }
        }
        return $HTML;
    }

    /**
     * 
     * DATA::AddModule()
     * 
     * @param  string $mod 
     * @param  bool $custom 
     * @return string $XML
     * @access private 
     */
    function AddModule($mod, $custom = true)
    {
        global $MODULES, $TRANSLATE;
        
        if ($mod == "NULL")
            return null;
        
        if ($custom) {
            $_TOLOAD = $MODULES[$mod]["mod"];
            $_PARAMS = $MODULES[$mod]["params"];
        } else {
            $_TOLOAD = $mod;
            $_PARAMS = null;
        }
        
        $MODULE = Modules::LoadModule($_TOLOAD, $_PARAMS);
        
        $_MODDATA = $MODULE->_BODY;
        $_MODCAT = $MODULE->_CAT;
        $_MODTXT = $MODULE->_TXT;
        $_MODXML = $MODULE->_XML;
        $XML .= "<module name=\"{$mod}\">
        <category><![CDATA[";
        $XML .= $TRANSLATE->Go($_MODCAT);
        $XML .= "]]></category>
        <text><![CDATA[";
        $XML .= $TRANSLATE->Go($_MODTXT);
        $XML .= "]]></text>
        <data><![CDATA[";
        $XML .= $TRANSLATE->Go($_MODDATA);
        $XML .= "]]></data>
        <xml>";
        $XML .= $TRANSLATE->Go($_MODXML);
        $XML .= "</xml>
        </module>\n";
        
        if (! empty($MODULE->_XSLT))
            $this->MXSLT = $MODULE->_XSLT;
        
        return $XML;
    }

    /**
     * 
     * DATA::AddPreModules()
     * 
     * @return string $XML
     * @access private 
     */
    function AddPreModules()
    {
        global $TRANSLATE, $_PREMOD;
        
        foreach ((array)$_PREMOD as $mod => $MODULE) {
            //$this->dump($MODULE);
            $_MODDATA = $MODULE->_BODY;
            $_MODCAT = $MODULE->_CAT;
            $_MODTXT = $MODULE->_TXT;
            $_MODXML = $MODULE->_XML;
            $XML .= "<module name=\"{$mod}\">
            <category><![CDATA[";
            $XML .= $TRANSLATE->Go($_MODCAT);
            $XML .= "]]></category>
            <text><![CDATA[";
            $XML .= $TRANSLATE->Go($_MODTXT);
            $XML .= "]]></text>
            <data><![CDATA[";
            $XML .= $TRANSLATE->Go($_MODDATA);
            $XML .= "]]></data>
            <xml>";
            $XML .= $TRANSLATE->Go($_MODXML);
            $XML .= "</xml>
            </module>\n";
            
            if (! empty($MODULE->_XSLT))
                $this->MXSLT = $MODULE->_XSLT;
        }
        
        return $XML;
    }

    /**
     * DATA::doControls()
     * 
     * Controls abstraction.
     * If advanced controls are set ADVControls method will be used
     * else standart Controls function will be used.
     * 
     * @param  $params 
     * @return string
     * @access private 
     */
    function doControls($ZONE, $params)
    {
        global $ADMIN_ADV_CONTROLS, $_ZONESCONTAIN;
        
        if ($ADMIN_ADV_CONTROLS) {
            if ($params["edit"] != "object") {
                $replace = "pas=\"" . $ZONE . "\"" . (($ZONE == "body")?"":" />");
                if (count($_ZONESCONTAIN[$ZONE]) > 0)
                    $params["odd"] = implode("|", $_ZONESCONTAIN[$ZONE]);
                $controls[$replace] = (($ZONE == "body")?"":" />") . $this->ADVControls($params);
            } else {
                $replace = "pasobject=\"" . $params["odd"] . "\"";
                $controls[$replace] = $this->ADVControls($params);
            }
        } else {
            if ($params["edit"] != "object") {
                foreach ($_ZONESCONTAIN[$ZONE] as $ODD) {
                    $replace = "<!--" . $ZONE . ":" . $ODD . "-->";
                    $params["odd"] = $ODD;
                    $controls[$replace] = $this->Controls($params);
                }
            } else {
                $replace = "<!--OBJECT:" . $params["odd"] . "-->";
                $controls[$replace] = $this->Controls($params);
            }
        }
        return $controls;
    }

    /**
     * DATA::ADVControls()
     * 
     * Uses contextual menue to access controls
     * This will give more freedom in website design using DIV and CSS
     * 
     * @param  $params 
     * @return string
     * @access private 
     */
    function ADVControls($params)
    {
        //global $CURRENTUSER, $CURRENTPLATFORM, $SYSTEMIMAGES, $TRANSLATE, $_SYSINDEX;
        
        extract($params);
        
        if (is_object($cat))
            $id = $cat->id; else if (is_string($cat))
            $id = $cat; else if ($_GET["id"])
            $id = $_GET["id"];
        if ($edit == "body")
            return "<div edit=\"body\" actions=\"admin|logout\" class=\"admin__\"></div>";
        if ($edit == "zone")
            return "<div zone=\"{$zone}\" edit=\"zone\" actions=\"{$odd}\" parent=\"{$parent}\" class=\"admin_zone__\"></div>";
        if ($edit == "object")
            return "edit=\"{{$cat}}\" type=\"{$odd}\" zone=\"{$zone}\" parent=\"{$parent}\" actions=\"{$controls}\" hidden=\"{$hidden}\"";
        //"controls" => $OBJ["controls"], "zone" => "{@zone}", "odd" => $KEY, "hide" => $OBJ["hide"], "cat" => "@id", "parent" => "{@parentid}", "edit" => "object"

        return null;
    }

    /**
     * DATA::Controls()
     * 
     * @param  $params 
     * @return string
     * @access private 
     */
    function Controls($params)
    {
        global $CURRENTUSER, $SYSTEMIMAGES, $TRANSLATE, $_SYSINDEX;
        
        extract($params);
        
        if (is_object($cat))
            $id = $cat->id; else if (is_string($cat))
            $id = $cat; else if ($_GET["id"])
            $id = $_GET["id"];
        
        if ($id{0} == "@") {
            $titleid = "<xsl:value-of select=\"" . $id . "\" />";
            $id = "{" . $id . "}";
        } else {
            $titleid = $id;
        }
        
        if ($id)
            $element = "&amp;id=" . $id;
        $relation = $rel;
        if (($cat->_objecttypename == "reference") || ($relation)) {
            $relation_ = "&amp;relation=" . $relation;
        }
        
        if ($odd) {
            $add_ = $odd;
            $odd = "&amp;odd=$odd";
        } else if ($odd_soft) {
            $odd = "&amp;odd=" . $cat->$odd_soft;
        }
        if ($list)
            $list_ = "&amp;list=$list&amp;name=$name&amp;value=$value";
        if ($parent)
            $parent_ = "&amp;parent=$parent";
        
        if (($CURRENTUSER->_loggedin) || ($noadmin == true)) {
            // this is menu head
            $_act = explode("|", $controls);
            $end = count($_act);
            for($i = 0; $i < $end; $i++) {
                $act = $_act[$i];
                
                if (($act == "container") && ($ignore == "cat")) {
                    unset($element);
                }
                // add new element to zone
                if (($CURRENTUSER->isAllowed("add")) && ($act == "new")) {
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add&amp;zone=$zone$odd$parent_', 570, 550)\" class=\"nadmin__\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##new##") . " " . $TRANSLATE->Go("##" . $add_ . "##") . "\" /></a>";
                    $titleid = $zone;
                }
                
                if (($CURRENTUSER->isAllowed("add")) && ($act == "other")) {
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add$element&amp;zone=$zone$odd$parent_', 570, 550)\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##" . $add_ . "##") . "\" /></a>";
                    $titleid = $zone;
                }
                // preferences link
                if (($CURRENTUSER->isAllowed("admin")) && ($act == "admin"))
                    $menu .= "<a href=\"javascript:popupnormal('./{$_SYSINDEX}?admin[]=home', 680, 500)\" class=\"nadmin__\" target=\"_top\"><img src=\"" . $SYSTEMIMAGES . "admin.png\" border=\"0\" width=\"68\" height=\"11\" title=\"" . $TRANSLATE->Go("##administration##") . "\" /></a>";
                
                if (($CURRENTUSER->isAllowed("add")) && ($act == "ref"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add$element$odd$relation_$parent_', 570, 550)\" class=\"nadmin__\">" . $TRANSLATE->Go("##+ref##") . "</a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "rel"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=addrel$element$relation_$list_$odd$parent_', 570, 550)\" class=\"nadmin__\">" . $TRANSLATE->Go("##+R##") . "</a>";
                
                if (($CURRENTUSER->isAllowed("move")) && ($act == "up"))
                    $menu .= "<a href=\"./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=up$element&amp;zone=$zone&amp;relation=$rel$parent_$odd\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "up.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##move up##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("move")) && ($act == "left"))
                    $menu .= "<a href=\"./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=up$element&amp;zone=$zone&amp;relation=$rel$parent_$odd\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "left.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##move left##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("move")) && ($act == "down"))
                    $menu .= "<a href=\"./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=down$element&amp;zone=$zone&amp;relation=$rel$parent_$odd\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "down.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##move down##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("move")) && ($act == "right"))
                    $menu .= "<a href=\"./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=down$element&amp;zone=$zone&amp;relation=$rel$parent_$odd\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "right.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##move right##") . "\" /></a>";
                
                if (($CURRENTUSER->isAllowed("edit")) && ($act == "edit"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=edit$element$odd', 570, 550)\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "edit.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##edit##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("delete")) && ($act == "del"))
                    $menu .= "<a href=\"./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=delete$element$odd\" class=\"nadmin__\" onmouseover=\"cpre()\" onclick=\"return confirm('" . $TRANSLATE->Go("##Do you want to delete this object?##") . "');\"><img src=\"" . $SYSTEMIMAGES . "delete.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##delete##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "cat"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add&amp;id=NULL&amp;zone=$zone$odd&amp;parent={@id}', 570, 550)\" class=\"nadmin__\" onmouseover=\"cpre()\"><nobr><img src=\"" . $SYSTEMIMAGES . "cat.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##+ cat##") . "\" /></nobr></a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "text"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add$element&amp;zone=$zone$odd', 570, 550)\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##+ text##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "prod"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add$element&amp;zone=$zone$odd', 570, 550)\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##+ product##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("parent")) && ($act == "move"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=move$element&amp;parent=$parent', 570, 550)\" class=\"nadmin__\" onmouseover=\"cpre()\"><img src=\"" . $SYSTEMIMAGES . "move.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##move object##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "news"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add&amp;zone=$zone$odd$element', 570, 550)\" class=\"nadmin__\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##+ event##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "subcat"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add&amp;zone=$zone$odd$element', 570, 550)\" class=\"nadmin__\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##sub cat##") . "\" /></a>";
                if (($CURRENTUSER->isAllowed("add")) && ($act == "container"))
                    $menu .= "<a href=\"javascript:popup('./{$_SYSINDEX}?admin[]=content&amp;admin[]=pools&amp;do=add&amp;zone=$zone$odd$element', 570, 550)\" class=\"nadmin__\"><img src=\"" . $SYSTEMIMAGES . "obj.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##+ container##") . "\" /></a>";
                
                if (($CURRENTUSER->isAllowed("admin")) && ($act == "logout"))
                    $menu .= "<a href=\"./?do=logout&amp;back=true\" class=\"nadmin__\" target=\"_top\"><img src=\"" . $SYSTEMIMAGES . "logout.png\" border=\"0\" width=\"37\" height=\"11\" title=\"" . $TRANSLATE->Go("##logout##") . "\" /></a>";
            }
            
            if (count($_act) > 1 && ! $hideid) {
                $title .= "<font color=\"#808080\" style=\"font-family:Arial,Verdana,Helvetica;font-size:10px;\"> ";
                $title .= "{<xsl:value-of select=\"@_o\" /><xsl:value-of select=\"@_g\" /><xsl:value-of select=\"@_w\" />}";
                $title .= "<b><xsl:if test=\"@_w = '0'\"><i>hidden:</i></xsl:if> ";
                $title .= "id: [ $titleid ] </b></font><br />";
            }
            if ($main)
                $id = $id . $odd;
            if ($hide == "no") {
                if (($CURRENTUSER->isAllowed("admin") && (($act == "logout") || ($act == "admin"))) || $CURRENTUSER->isAllowed("edit") || $CURRENTUSER->isAllowed("delete") || $CURRENTUSER->isAllowed("move") || $CURRENTUSER->isAllowed("add") || $CURRENTUSER->isAllowed("parent")) {
                    if ($act == "other")
                        return $title . $menu; else
                        return "<xsl:if test=\"@canedit = '1'\">" . $title . $menu . "</xsl:if>";
                }
                return;
            }
            $left = "<a onmouseout=\"preparetoclose(); \" href=\"javascript:show_div('Ltop_$id')\" class=\"nadminimg__\"><img src=\"" . $SYSTEMIMAGES . "baloon.png\" border=\"0\" width=\"11\" height=\"11\" title=\"" . $TRANSLATE->Go("##edit controles##") . "\"/></a>"; // <font face=\"Wingdings\">!</font> - <img src=\"" . $SYSTEMIMAGES . "circle_small.gif\" border=\"0\" width=\"15\" height=\"10\" />
            $_menu_start = "<div align=\"left\" onmouseover=\"cpre()\" onmouseout=\"preparetoclose()\" id=\"Ltop_$id\" style=\"background-color: #ffffff; position: absolute; width:122; height:130 z-index: 10; visibility:hidden; border-color: #FF0000 #FF0000 #FF0000 #FF0000; border-style: solid; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-radius: 4px; -moz-border-radius: 4px\"><table><tr><td>";
            $_menu_end = "</td></tr></table></div>";
            $_menu = $title . $menu;
            
            if (($CURRENTUSER->isAllowed("admin") && (($act == "logout") || ($act == "admin"))) || $CURRENTUSER->isAllowed("edit") || $CURRENTUSER->isAllowed("delete") || $CURRENTUSER->isAllowed("move") || $CURRENTUSER->isAllowed("add") || $CURRENTUSER->isAllowed("parent")) {
                return "<div style=\"position: absolute; width=7; height:13; z-index:5\"><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td>$left</td><td>$_menu_start$_menu$_menu_end</td></tr></table></div>";
                //return "<xsl:if test=\"@canedit = '1'\"><div style=\"position: absolute; width=7; height:13; z-index:5\"><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td>$left</td><td>$_menu_start$_menu$_menu_end</td></tr></table></div></xsl:if>";
            }
        }
        return null;
    }
}

?>