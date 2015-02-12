<?php

/**
 * ODDs
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 */
class ODDs
{

    /**
     * ODDs::ODDs()
     * 
     * @return 
     */
    function __construct()
    {
        $this->XML = new XML();
        $this->dtdlist = $this->get_odd("dtd.list");
    }

    /**
     * ODDs::odd_exists()
     * 
     * @param  $file 
     * @return 
     */
    function odd_exists($file)
    {
        if (file_exists($file))
            return true; else
            return false;
    }

    /**
     * ODDs::add_odd()
     * 
     * @param  $values 
     * @return 
     */
    function add_odd($values)
    {
        $this->DTDN = "dtd.list";
        $FOLDER = $this->odd_type($this->DTDN);
        $file = $FOLDER . "/" . $this->DTDN . ".xml";
        $this->DTD = $this->XML->toArray($file);
        $ELEMENTS = $this->DTD[0]["data"][0]["data"][0]["data"];
        // Test if dtd is not created
        foreach ($ELEMENTS as $ELM)
            if ($ELM["attributes"]["name"] == $values["dtd"])
                return;
            
        // Add ODD to ODD list
        $last = count($ELEMENTS);
        $ELEMENTS[$last]["element_name"] = "dtd";
        $ELEMENTS[$last]["attributes"]["type"] = ($values["type"])?$values["type"]:"user";
        $ELEMENTS[$last]["attributes"]["name"] = $values["dtd"];
        $ELEMENTS[$last]["attributes"]["edit"] = ($values["edit"])?$values["edit"]:"1";
        $ELEMENTS[$last]["attributes"]["pool"] = ($values["pool"])?$values["pool"]:"0";
        $ELEMENTS[$last]["attributes"]["lang"] = ($values["lang"])?$values["lang"]:"0";
        if ($values["module"])
            $ELEMENTS[$last]["attributes"]["module"] = $values["module"];
        $ELEMENTS[$last]["data"][0] = array(
            "element_name" => "title", "data" => $values["title"]
        );
        $ELEMENTS[$last]["data"][1] = array(
            "element_name" => "description", "data" => $values["description"]
        );
        
        $this->DTD[0]["data"][0]["data"][0]["data"] = $ELEMENTS;
        $this->save();
        
        $this->dtdlist = $this->get_odd("dtd.list");
        
        if ($values["type"] == "user" || ! $values["type"]) {
            $FOLDER = $this->odd_type("template_odd");
            $file = $FOLDER . "/template_odd.xml";
            
            $FOLDER = $this->odd_type($values["dtd"]);
            $newfile = $FOLDER . "/" . $values["dtd"] . ".xml";
            copy($file, $newfile);
        }
        
        return;
    }

    /**
     * ODDs::get_odd()
     * 
     * @param  $DTD
     * @return 
     */
    function get_odd($DTD)
    {
        $FOLDER = $this->odd_type($DTD);
        $file = $FOLDER . "/" . $DTD . ".xml";
        if ($this->odd_exists($file)) {
            $this->DTD = $this->XML->toArray($file);
            return $this->DTD[0]["data"][0]["data"][0]["data"];
        }
        return null;
    }

    /**
     * ODDs::odd_type()
     * 
     * @param  $DTD 
     * @return 
     */
    function odd_type($DTD)
    {
        global $COREROOT, $CONFIG_DIR;
        $end = count($this->dtdlist);
        if ($DTD == "dtd.list")
            return $CONFIG_DIR . "odd";
        for($i = 0; $i < $end; $i++) {
            if (($this->dtdlist[$i]["attributes"]["name"] == $DTD) && ($this->dtdlist[$i]["attributes"]["type"] == "user")) {
                return $CONFIG_DIR . "odd";
            } else if (($this->dtdlist[$i]["attributes"]["name"] == $DTD) && ($this->dtdlist[$i]["attributes"]["type"] == "module")) {
                return $COREROOT . "modules/" . $this->dtdlist[$i]["attributes"]["module"] . "/odd";
            }
        }
        return $COREROOT . "odd";
    }
    
    /**
     * ODDs::odd_multilang()
     * 
     * @param $DTD
     * @return boolean
     */
    function odd_multilang ($DTD)
    {
        global $COREROOT, $CONFIG_DIR;
        
        if ($DTD == "dtd.list")
            return false;

        $end = count($this->dtdlist);
        for($i = 0; $i < $end; $i++) {
            if (($this->dtdlist[$i]["attributes"]["name"] == $DTD)) {
                if ($this->dtdlist[$i]["attributes"]["lang"] == 1) {
                    return true;
                }
                return false;
            }
        }
        
        return false;
    }

    /**
     * ODDs::save()
     * 
     * @return 
     */
    function save()
    {
        $FOLDER = $this->odd_type($this->DTDN);
        $file = $FOLDER . "/" . $this->DTDN . ".xml";
        $DATA = $this->XML->array2xml($this->DTD);
        $fp = @fopen($file, "w");
        if ($fp) {
            fwrite($fp, $DATA);
            fclose($fp);
        }
        chmod($file, 0666);
    }

    /**
     * ODDs::move()
     * 
     * @param  $DTD 
     * @param  $field 
     * @param integer $go 
     * @return 
     */
    function move($DTD, $field, $goto)
    {
        $FOLDER = $this->odd_type($DTD);
        $file = $FOLDER . "/" . $DTD . ".xml";
        $this->DTD = $this->XML->toArray($file);
        $this->DTDN = $DTD;
        if (is_array($this->DTD[0]["data"][0]["data"][0]["data"][$goto])) {
            $foo = $this->DTD[0]["data"][0]["data"][0]["data"][$goto];
            $this->DTD[0]["data"][0]["data"][0]["data"][$goto] = $this->DTD[0]["data"][0]["data"][0]["data"][$field];
            $this->DTD[0]["data"][0]["data"][0]["data"][$field] = $foo;
            $this->save();
        }
    }

    /**
     * ODDs::delete_row()
     * 
     * @param  $DTD 
     * @param  $field 
     * @return 
     */
    function delete_row($DTD, $field)
    {
        $FOLDER = $this->odd_type($DTD);
        $file = $FOLDER . "/" . $DTD . ".xml";
        $this->DTD = $this->XML->toArray($file);
        $this->DTDN = $DTD;
        unset($this->DTD[0]["data"][0]["data"][0]["data"][$field]);
        $this->save();
    }

    /**
     * ODDs::edit_row()
     * 
     * @param  $DTD 
     * @param  $field 
     * @param  $val 
     * @return 
     */
    function edit_row($DTD, $field, $val)
    {
        $FOLDER = $this->odd_type($DTD);
        $file = $FOLDER . "/" . $DTD . ".xml";
        $this->DTD = $this->XML->toArray($file);
        $this->DTDN = $DTD;
        $ELEMENTS = $this->DTD[0]["data"][0]["data"][0]["data"];
        $ELEMENTS[$field]["element_name"] = $val["elementname"];
        $ELEMENTS[$field]["attributes"]["reqired"] = $val["reqired"];
        $ELEMENTS[$field]["attributes"]["lang"] = $val["lang"];
        $ELEMENTS[$field]["attributes"]["order"] = $field;
        if ($val["params"]) {
            $FOO = explode("|", $val["params"]);
            if (trim($FOO[(count($FOO) - 1)]) == '')
                unset($FOO[(count($FOO) - 1)]);
            foreach ($FOO as $PARAM) {
                $PARAMS_ = explode("=", $PARAM);
                $ATTR[$PARAMS_[0]] = $PARAMS_[1];
            }
        }
        unset($val["elementname"], $val["reqired"], $val["lang"], $val["params"], $ELEMENTS[$field]["data"]);
        foreach ($val as $KEY => $VALUE)
            if (trim($VALUE) != '') {
                unset($DATA);
                $DATA["element_name"] = $KEY;
                if (is_array($ATTR) && $KEY == "field")
                    $DATA["attributes"] = $ATTR;
                $DATA["data"] = $VALUE;
                $ELEMENTS[$field]["data"][] = $DATA;
            }
        $this->DTD[0]["data"][0]["data"][0]["data"] = $ELEMENTS;
        $this->save();
    }

    /**
     * ODDs::add_row()
     * 
     * @param  $DTD 
     * @param  $field 
     * @param  $val 
     * @return 
     */
    function add_row($DTD, $val)
    {
        $FOLDER = $this->odd_type($DTD);
        $file = $FOLDER . "/" . $DTD . ".xml";
        $this->DTD = $this->XML->toArray($file);
        $fid = count($this->DTD[0]["data"][0]["data"][0]["data"]);
        $this->edit_row($DTD, $fid, $val);
    }

    /**
     * ODDs::sys2paltform()
     * 
     * @param  $ID
     * @return 
     */
    function sys2paltform($ID)
    {
        $FOLDER = $this->odd_type("system.list");
        $file = $FOLDER . "/system.list.xml";
        $this->DTD = $this->XML->toArray($file);
        $SYS = $this->DTD[0]["data"][0]["data"][0]["data"][$ID];
        
        $FOLDER = $this->odd_type("dtd.list");
        $file = $FOLDER . "/dtd.list.xml";
        $this->DTDN = "dtd.list";
        $this->DTD = $this->XML->toArray($file);
        $this->DTD[0]["data"][0]["data"][0]["data"][] = $SYS;
        
        $this->save();
    }

    /**
     * ODDs::sys2user()
     * 
     * @param  $ID
     * @return 
     */
    function sys2user($ID)
    {
        
        $FOLDER = $this->odd_type("dtd.list");
        $file = $FOLDER . "/dtd.list.xml";
        $this->DTD = $this->XML->toArray($file);
        
        $SYSODD = $this->DTD[0]["data"][0]["data"][0]["data"][$ID]["attributes"]["name"];
        $FOLDER = $this->odd_type($SYSODD);
        $file = $FOLDER . "/" . $SYSODD . ".xml";
        $SYSDATA = $this->XML->toArray($file);
        
        // Do move
        $this->DTDN = "dtd.list";
        $this->DTD[0]["data"][0]["data"][0]["data"][$ID]["attributes"]["type"] = "user";
        $this->save();
        $this->dtdlist = $this->get_odd("dtd.list");
        $this->DTDN = $SYSODD;
        $this->DTD = $SYSDATA;
        $this->save();
    }

    /**
     * ODDs::_DTDlist()
     * 
     * @return 
     */
    function _DTDlist()
    {
        $FOLDER = $this->odd_type("dtd.list");
        $file = $FOLDER . "/dtd.list.xml";
        $this->dtdList = $this->XML->toArray($file);
    }

    /**
     * ODDs::getSystem()
     * 
     * @return 
     */
    function getSystem()
    {
        if (! is_array($this->dtdList))
            $this->_DTDlist();
        return $this->dtdList["system"];
    }

    /**
     * ODDs::get_field_id()
     * 
     * @param  $_dtd 
     * @param  $field 
     * @return 
     */
    function get_field_id($_dtd, $field)
    {
        if (! is_array($_dtd))
            $DTD = $this->get_odd($_dtd);
        $end = count($DTD);
        for($i = 0; $i < $end; $i++) {
            if ($DTD[$i]["element_name"] == $field) {
                return "$i";
            }
        }
        return null;
    }

    /**
     * ODDs::get_field_name()
     * 
     * @param  $_dtd 
     * @param  $field 
     * @return 
     */
    function get_field_name($_dtd, $field)
    {
        if (! is_array($_dtd))
            $DTD = $this->get_odd($_dtd);
        $end = count($DTD);
        for($i = 0; $i < $end; $i++) {
            if ($DTD[$i]["element_name"] == $field) {
                return $i;
            }
        }
        return null;
    }

    /**
     * ODDs::get_object_name()
     * 
     * @param  $id 
     * @return 
     */
    function get_object_name($id)
    {
        return $this->dtdlist[$id]["attributes"]["name"];
    }

    /**
     * ODDs::get_object_id()
     * 
     * @param  $name 
     * @return 
     */
    function get_object_id($name)
    {
        $end = count($this->dtdlist);
        for($i = 0; $i < $end; $i++) {
            if ($this->dtdlist[$i]["attributes"]["name"] == $name) {
                return $i;
            }
        }
        return null;
    }

    /**
     * ODDs::is_object_multilingual()
     * 
     * @param int $id 
     * @return boolean 
     * @access public 
     */
    function is_object_multilingual($id = null)
    {
        if ($this->dtdlist[$id]["attributes"]["lang"]) {
            return true;
        }
        return false;
    }
}

/**
 * GetDTDElements()
 * 
 * @param  $DTD 
 * @return 
 */
function GetDTDElements($DTD)
{
    if (! is_array($DTD))
        return null;
    
    for($i = 0; $i < count($DTD); $i++) {
        if (($DTD[$i]["data"][1]["data"] != "title") && $DTD[$i]["data"][1]["data"] != "hidden") {
            $ELM[$DTD[$i]["element_name"]] = $DTD[$i]["data"][0]["data"];
        }
    }
    return $ELM;
}

/**
 * GetDTDFieldType()
 * 
 * @param  $DTD 
 * @return 
 */
function GetDTDFieldType($DTD)
{
    if (! is_array($DTD))
        return null;
    
    for($i = 0; $i < count($DTD); $i++) {
        if (($DTD[$i]["data"][1]["data"] != "title") && $DTD[$i]["data"][1]["data"] != "hidden") {
            $ELM[$DTD[$i]["element_name"]] = $DTD[$i]["data"][1]["data"];
        }
    }
    
    return $ELM;
}

?>