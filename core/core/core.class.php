<?php

/**
 * CORE
 *
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public
 */

class CORE extends PERMISSIONS
{

    public $_CLASS = __CLASS__;

    /**
     * CORE class constructor
     * If you give him argument they will be passed to load function
     * and new CORE object will be replased with the loaded one
     *
     * CORE::__construct()
     *
     * @return object
     */
    function __construct()
    {
        $numargs = func_num_args();
        
        if ($numargs == 1) {
            $arg = func_get_arg(0);
            $O = $this->load($arg);
            $this->_assgin_object($O);
        } else if ($numargs == 2) {
            $arg = func_get_arg(0);
            $arg1 = func_get_arg(1);
            $O = $this->load($arg, $arg1);
            $this->_assgin_object($O);
        }
    }

    /**
     * CORE::load()
     *
     * @param  $param
     * @param unknown $data
     * @return object
     * @access public
     */
    function load(&$param, $data = null)
    {
        global $DTD;
        $NAME = $this->_CLASS;
        $o = new $NAME();
        
        if (is_numeric($param)) {
            $o->_load_main($param);
            if ($o->_object != '') {
                $o->_load_dtd();
                $o->_preloadMainValues($data);
                $o->_load_data();
            }
        } else if (is_object($param)) {
            if ($param->_object != '') {
                $o->_populate_data($param);
                $o->_objectname = $DTD->get_object_name($param->_object);
                $o->_load_dtd();
                $o->_load_data();
            }
        } else if (is_array($param)) {
            $o->_preloadMainValues($param);
            $o->_object = $DTD->get_object_id($o->_objectname);
            $o->_load_dtd();
            $o->_doXML_data($param);
            $o->_arrayData = $param;
        } else if (! is_numeric($param)) {
            $o->_object = $DTD->get_object_id($param);
            $o->_objectname = $param;
            $o->_load_dtd();
        }
        
        if ($data) {
            $o->_doXML_data($data);
            $o->_arrayData = $data;
        }
        
        return $o;
    }

    /**
     * Loading main values into current object
     *
     * CORE::_preloadMainValues()
     *
     * @param  $val
     * @return NULL
     * @access private
     */
    function _preloadMainValues($val)
    {
        $val = (array)$val;
        $key = array_keys($val);
        $end = count($key);
        for($i = 0; $i < $end; $i++) {
            if ($key[$i]{0} == "_") {
                $this->$key[$i] = $val[$key[$i]];
            }
        }
    }

    /**
     * Loading values into current object
     *
     * CORE::_preloadValues()
     *
     * @param  $val
     * @return NULL
     * @access private
     */
    function _preloadValues($val)
    {
        unset($val["id"], $val["dosql"], $val["_object"]);
        $key = array_keys($this->_dtd);
        $end = count($key);
        for($i = 0; $i < $end; $i++)
            if (($this->_dtd[$key[$i]]["field"] != "title") && ($this->_dtd[$key[$i]]["field"] != "submit"))
                $this->$key[$i] = $val[$key[$i]];
    }

    /**
     * Loads object main elements -- "ids" table
     *
     * CORE::_load_main()
     *
     * @param  $param
     * @return NULL
     * @access private
     */
    function _load_main($param)
    {
        global $DTD, $DB, $PLATFORMID;
        $sql = "SELECT * FROM global WHERE id = '$param'";
        if ($PLATFORMID)
            $sql .= " AND " . TQT . "_platform" . TQT . " = '" . $PLATFORMID . "'";
        $data = $DB->getRow($sql);
        $this->_populate_data($data);
        $this->_objectname = $DTD->get_object_name($this->_object);
    }

    /**
     * CORE::_load_data()
     *
     * @param  $param
     * @return NULL
     * @access private
     */
    function _load_data()
    {
        global $DB, $DTD, $DEFAULTLANGUAGE, $CURRENTLANGUAGE;
        
        $add = null;
        
        $sql = "SELECT * FROM data WHERE gid = '" . $this->id . "'";
        
        $data = $DB->getAll($sql);
        $end = count($data);
        
        $_tag = $this->_objectname;
        
        for($i = 0; $i < $end; $i++) {
            if (strtoupper($this->_CLASS) == "DATA") {
                $XML = $this->html_entity_decode($data[$i]->data, ENT_QUOTES, "UTF-8");
                $XML .= $this->_object_data();
            } else {
                $XML = "<$_tag parentid='" . $this->_parent . "' zone='" . $this->_zone . "' id='" . $this->id . "'$add>\n";
                $XML .= $this->html_entity_decode($data[$i]->data, ENT_QUOTES, "UTF-8");
                $XML .= $this->_object_data();
                $XML .= "</$_tag>\n";
            }
            $name = "data_" . $data[$i]->langid;
            $this->$name = $XML;
            
            $is_ml = $DTD->is_object_multilingual($this->_object);
            
            if (($is_ml) && ($data[$i]->langid == $CURRENTLANGUAGE)) {
                $this->data = $XML;
            } else if ((! $is_ml) && ($data[$i]->langid == $DEFAULTLANGUAGE)) {
                $this->data = $XML;
            }
        }
    }

    /**
     * CORE::_object_data()
     *
     * @return
     */
    function _object_data()
    {
        global $DB;
        
        if (strtoupper($this->_CLASS) == "PERSON") {
            $sql = "SELECT * FROM " . TQT . "users" . TQT . " WHERE " . TQT . "gid" . TQT . " = '" . $this->id . "'";
            $data = $DB->getAll($sql);
            $XML .= "<_username>" . $data[0]->username . "</_username>\n";
            $XML .= "<_password>" . $data[0]->password . "</_password>\n";
            $XML .= "<_group>" . $data[0]->group . "</_group>\n";
        }
        $XML .= "<_module>" . $this->_module . "</_module>\n";
        $XML .= "<_xslt>" . $this->_xslt . "</_xslt>\n";
        //if ($this->_w == 0) $XML .= "<_hidden>1</_hidden>\n";
        return $XML;
    }

    /**
     * Loads and sets DTD in object
     *
     * CORE::_load_dtd()
     *
     * @return NULL
     * @access private
     */
    function _load_dtd($force = false)
    {
        global $DTD;
        if (strtoupper($this->_CLASS) == "DATA" && ! $force) {
            $this->_dtd = "DTD not loded";
        } else {
            $this->_dtd = $DTD->get_odd($this->_objectname);
        }
    }

    /**
     * Insert action
     * This function will insert data into database as normal object
     *
     * CORE::insert()
     *
     * @param unknown $val
     * @return NULL
     * @access public
     */
    function insert()
    {
        global $PLATFORMID, $NEWPLATFORMID, $CURRENTPLATFORM, $CURRENTUSER, $DB;
        // if we have $val varable set we insert $val data else we insert current object
        $val = $this;
        $val = (array)$val;
        
        if ($this->_objectname != '')
            $val["_object"] = $this->_objectname;
        if (! $PLATFORMID)
            $_PLATFORMID = "'" . (getLastID("global") + 1) . "'"; else
            $_PLATFORMID = "'" . $PLATFORMID . "'";
        
        $this->_w = ($this->_w)?$this->_w:4;
        $this->_g = ($this->_g)?$this->_g:6;
        $this->_o = ($this->_o)?$this->_o:6;
        $this->_w = (! $this->_hidden)?$this->_w:0;
        $zone = (($this->_zone)?"'" . $this->_zone . "'":"NULL");
        $_workflow = (($this->_workflow)?$this->_workflow:0);
        $this->_group = (! $this->_group)?$CURRENTPLATFORM->_group:$this->_group;
        
        // insert in main elements in "ids" table
        $sql = "
        INSERT INTO " . TQT . "global" . TQT . " (" . TQT . "_owner" . TQT . ", " . TQT . "_object" . TQT . ", " . TQT . "_date" . TQT . ", " . TQT . "_zone" . TQT . ", " . TQT . "_platform" . TQT . ", " . TQT . "_workflow" . TQT . ", " . TQT . "_module" . TQT . ", " . TQT . "_xslt" . TQT . ", " . TQT . "_group" . TQT . ", " . TQT . "_o" . TQT . ", " . TQT . "_g" . TQT . ", " . TQT . "_w" . TQT . ")
        VALUES
        ('" . (($CURRENTUSER->id)?$CURRENTUSER->id:0) . "', '" . $this->_object . "', '" . time() . "', " . $zone . ", " . $_PLATFORMID . ", '" . (($_workflow)?$_workflow:0) . "', '" . $this->_module . "', '" . $this->_xslt . "', '" . (($this->_group)?$this->_group:0) . "', '" . $this->_o . "', '" . $this->_g . "', '" . $this->_w . "')";
        
        $DB->query($sql);
        // take id from last insert
        $oid = getLastID("global");
        
        // new platform is created
        if ($NEWPLATFORMID)
            $PLATFORMID = $oid;
        
        $this->id = $oid;
        // now to insert elements
        $this->_insert_data(null, $oid);
        if (strtoupper($this->_objectname) == "PERSON") {
            $O = new PERSON($oid);
            $O->_update_person();
            // load new object
            $O = new PERSON($oid);
        } else {
            // load new object
            $O = $this->load($oid);
        }
        $this->_assgin_object($O);
        
        return;
    }

    /**
     * CORE::_insert_data()
     *
     * @param  $val
     * @param  $oid
     * @return NULL
     * @access private
     */
    function _insert_data($val, $oid)
    {
        global $EDITLANGUAGE, $DEFAULTLANGUAGE, $DB, $DTD;
        
        if (!$DTD->odd_multilang($this->_objectname)) {
                $EDITLANGUAGE = $DEFAULTLANGUAGE;
        }
        
        if (! $val) {
            $_DATA = $this->data;
            if ($this->_CLASS == "person")
                $this->_update_person();
        } else {
            $_DATA = $val->data;
            if ($val->_CLASS == "person")
                $this->_update_person($val);
        }
        
        $sql = "
        INSERT INTO data
            (gid, langid, data)
        VALUES
            ('" . $oid . "', '" . $EDITLANGUAGE . "', '" . escape_sql($_DATA) . "')";
        
        $DB->query($sql);
        
        /**
         * Now lets insert data in index tables
         */
        $this->_feed_index($oid);
    }

    /**
     * CORE::_feed_index()
     *
     * @param  $_DATA
     * @param  $_DTD
     * @return NULL
     * @access private
     */
    function _feed_index($oid = NULL, $_DATA = NULL, $_DTD = NULL)
    {
        global $EDITLANGUAGE, $DB;
        
        if (! $_DATA)
            $_DATA = $this->_arrayData;
        if (! $_DTD)
            $_DTD = $this->_dtd;
        if (! $oid)
            $oid = $this->id;
        
        if ($_DTD)
            foreach ($_DTD as $ELM) {
                if ((trim($_DATA[$ELM["element_name"]]) != '') && (trim($_DATA[$ELM["element_name"]]) != "<br />")) {
                    if ($ELM["data"][2]["data"] == "number")
                        $TBL = "elm_number"; elseif ($ELM["data"][2]["data"] == "date")
                        $TBL = "elm_date"; else
                        $TBL = "elm_text";
                    
                    $sql = "
                INSERT INTO $TBL
                    (gid, elm, data, lang)
                VALUES
                    ('" . $oid . "', '" . $ELM["element_name"] . "', '" . escape_sql($_DATA[$ELM["element_name"]]) . "', '" . $EDITLANGUAGE . "')";
                    $DB->query($sql);
                }
            }
    }

    /**
     * Update object
     *
     * CORE::update()
     *
     * @todo need to create history record
     * @param boolean $val
     * @return NULL
     * @access public
     */
    function update($val = false)
    {
        if ($val) {
            $id = $val["id"];
            $data = $val;
        } else {
            $id = $this->id;
            $data = $this;
        }
        
        FILES::_update_file($id, $data);
        $this->_update_object($data);
        $this->_delete_data($id);
        $this->_insert_data($data, $id);
        if (strtoupper($this->_objectname) == "PERSON") {
            $O = new PERSON($id);
            $O->_update_person();
        }
    }

    /**
     * CORE::_update_object()
     *
     * @param  $val
     * @return NULL
     * @access private
     */
    function _update_object($val)
    {
        global $DB;
        
        if (! is_array($val))
            settype($val, "array");
        
        $sql = "UPDATE " . TQT . "global" . TQT . " SET " . TQT . "_date" . TQT . " = '" . time() . "', " . TQT . "_workflow" . TQT . " = '" . $val["_workflow"] . "', " . TQT . "_module" . TQT . " = '" . $val["_module"] . "', " . TQT . "_xslt" . TQT . " = '" . $val["_xslt"] . "', " . TQT . "_group" . TQT . " = '" . $val["_group"] . "' WHERE " . TQT . "id" . TQT . "='" . $val["id"] . "'";
        $DB->query($sql);
    }

    /**
     * Delete object
     *
     * CORE::delete()
     *
     * @todo need to create history record
     * @param $_id
     * @return NULL
     * @access public
     */
    function delete($_id = null)
    {
        if (! $_id)
            $id = $this->id; else if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        
        if (! $id)
            return false;
        
        if ($this->candelete($id)) {
            FILES::_delete_files($id);
            $this->_delete_ids($id);
            $this->_delete_data($id, 1);
            $this->_delete_relations($id);
            if (strtoupper($this->_objectname) == "PERSON")
                $this->_delete_user();
        }
        return true;
    }

    /**
     * CORE::_delete_ids()
     *
     * @param integer $id
     * @return NULL
     * @access private
     */
    function _delete_ids($id = false)
    {
        global $DB;
        $sql = "DELETE FROM global WHERE id = '$id'";
        $DB->query($sql);
    }

    /**
     * CORE::_delete_data()
     *
     * @param integer $id
     * @param boolean $all
     * @return NULL
     * @access private
     */
    function _delete_data($id = false, $all = false)
    {
        global $EDITLANGUAGE, $DEFAULTLANGUAGE, $DB, $DTD;
        
        if (!$DTD->odd_multilang($this->_objectname)) {
                $EDITLANGUAGE = $DEFAULTLANGUAGE;
        }
        
        if (! $id)
            return false;
        
        $sql = "DELETE FROM data WHERE gid = '$id'";
        if ($all == false) {
            $sql .= " AND langid='$EDITLANGUAGE'";
        }
        $DB->query($sql);
        
        $this->_clean_index($id, $all);
        
        return true;
    }

    /**
     * CORE::_clean_index()
     *
     * @param  $_DATA
     * @param  $_DTD
     * @return NULL
     * @access private
     */
    function _clean_index($oid = NULL, $all = false)
    {
        global $EDITLANGUAGE, $DB;
        
        if (! $oid)
            $oid = $this->id;
        if ($all == false)
            $lang = " AND lang='$EDITLANGUAGE'";
        
        $sql = "DELETE FROM elm_text WHERE gid = '$oid'" . $lang . ";";
        $DB->query($sql);
        $sql = "DELETE FROM elm_date WHERE gid = '$oid'" . $lang . ";";
        $DB->query($sql);
        $sql = "DELETE FROM elm_number WHERE gid = '$oid'" . $lang . ";";
        $DB->query($sql);
    }

    /**
     * CORE::_delete_relations()
     *
     * @param integer $id
     * @return NULL
     * @access private
     */
    function _delete_relations($id = false)
    {
        global $DB;
        $sql = "DELETE FROM relations WHERE targetid = '$id'";
        $DB->query($sql);
    }

    /**
     * CORE::candelete()
     *
     * @param integer $id
     * @return boolean
     * @access public
     */
    function candelete($id = false)
    {
        global $DB;
        // To be fixed
        // return true;
        if (! $id)
            $id = $this->id;
        
        $sql = "SELECT * FROM relations WHERE parentid='$id'";
        SQL_addlimit($sql, 1, 0);
        
        $data = $DB->getAll($sql);
        if (count($data) > 0)
            return false;
        
        return true;
    }

    /**
     * CORE::forceDeleteWithRelations()
     *
     * @return NULL
     * @access public
     */
    function forceDeleteWithRelations()
    {
        return false;
    }

    /**
     * CORE::isLoaded()
     *
     * @return biilean
     * @access public
     */
    function isLoaded()
    {
        if ($this->id != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * CORE::insertOrUpdate()
     *
     * @return NULL
     * @access public
     */
    function insertOrUpdate()
    {
        if ($this->isLoaded()) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * CORE::getChildren()
     *
     * @return array
     * @access public
     */
    function getChildren()
    {
        $data = $this->_get_relations();
        return $data;
    }

    /**
     * CORE::getRelations()
     *
     * @param varchar $relation
     * @param varchar $zone
     * @return array
     * @access public
     */
    function getRelations($relation = null, $zone = null)
    {
        $data = $this->_get_relations($zone, $relation);
        
        return $data;
    }

    /**
     * CORE::_get_relations()
     *
     * @param varchar $zone
     * @param varchar $relation
     * @return array
     * @access private
     */
    function _get_relations($zone = null, $relation = null)
    {
        global $PLATFORMID, $DB;
        
        if ($this->id != '') {
            $pid = $this->id;
        } else {
            $pid = $PLATFORMID;
        }
        
        $_more_sql = $this->buildFilter("glb");
        
        if ($zone) {
            $sql = "
            SELECT glb.* FROM " . TQT . "relations" . TQT . " rel, " . TQT . "global" . TQT . " glb
            WHERE
              rel.parentid = '$pid' and
              rel.relationtype" . (($relation)?" = '$relation'":" is NULL") . " and
              rel.targetid=glb.id and
              glb._zone='$zone'
              $_more_sql
            ORDER BY rel." . OQT . "_order" . OQT . " ASC";
        } else {
            $sql = "
            SELECT glb.* FROM " . TQT . "relations" . TQT . " rel, " . TQT . "global" . TQT . " glb
            WHERE
              relationtype" . (($relation)?" = '$relation'":" is NULL") . " and
              parentid='" . $pid . "' and
              glb.id = rel.targetid
              $_more_sql
            ORDER BY " . OQT . "_order" . OQT . " ASC";
        }
        
        $_data = $DB->getAll($sql);
        
        $end = count($_data);
        for($i = 0; $i < $end; $i++) {
            $names = SQL_fixNames($_data[$i]);
            $data[] = $this->load($names);
        }
        return $data;
    }

    /**
     * CORE::getParent()
     *
     * @return object
     * @access public
     */
    function getParent()
    {
        global $DB, $CURRENTPLATFORM;
        
        $sql = "
          SELECT rel.parentid FROM " . TQT . "relations" . TQT . " rel, " . TQT . "global" . TQT . " glb
          WHERE
            rel.targetid='" . $this->id . "' and
            rel.relationtype is NULL and
            rel.targetid=glb.id and
            glb._platform='" . $CURRENTPLATFORM->id . "'
          ";
        $_data = $DB->getAll($sql);
        if (count($_data) > 0) {
            $data_ = SQL_fixNames($_data[0]);
            $data = $this->load($data_->parentid);
        }
        if (! is_object($data))
            return $CURRENTPLATFORM;
        return $data;
    }

    /**
     * CORE::getAllParents()
     *
     * @param  $_id
     * @return array
     * @access public
     */
    function getAllParents($_id)
    {
        if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        
        return $id;
    }

    /**
     * CORE::getParentRelations()
     *
     * @param  $_id
     * @param  $relation
     * @return array
     * @access public
     */
    function getParentRelations($_id, $relation = null)
    {
        $relation = $relation;
        
        if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        
        return $id;
    }

    /**
     * CORE::addChild()
     *
     * @param  $_id
     * @return boolean
     * @access public
     */
    function addChild($_id, $zone)
    {
        if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        if ($this->_add_relation($id, $zone))
            return true;
        return false;
    }

    /**
     * CORE::addRelation()
     *
     * @param  $_id
     * @param  $type
     * @return boolean
     * @access public
     */
    function addRelation($_id, $type, $zone)
    {
        if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        if ($this->_add_relation($id, $zone, $type))
            return true;
        
        return false;
    }

    /**
     * CORE::_add_relation()
     *
     * @param  $id
     * @param unknown $rel
     * @return boolean
     * @access private
     */
    function _add_relation($id, $zone, $rel = null)
    {
        global $ADDOBJECTONTOP, $DB;
        
        if ($rel) {
            if ($ADDOBJECTONTOP == true) {
                $order = '0';
            } else {
                $order = $this->_get_last_order($zone, $rel);
            }
            $relation = "'$rel'";
            $relation = $relation;
        } else {
            if ($ADDOBJECTONTOP == true)
                $order = '0'; else
                $order = $this->_get_last_order($zone);
            $relation = "NULL";
        }
        $sql = "INSERT INTO relations (" . TQT . "parentid" . TQT . " , " . TQT . "targetid" . TQT . " , " . TQT . "relationtype" . TQT . ", " . TQT . "_order" . TQT . ") VALUES ('" . $this->id . "', '$id', $relation, $order);";
        $done = $DB->query($sql);
        $this->_reorder_children($zone, $rel);
        if ($done)
            return true;
        
        return false;
    }

    /**
     * CORE::sortChildren()
     *
     * @param  string $zone
     * @param  boolean $rel
     * @return boolean
     * @access public
     */
    function sortChildren($zone, $rel = false)
    {
        global $_ZONESSORT;
        
        if (! empty($_ZONESSORT[$zone]))
            return false;
        
        $this->_reorder_children($zone, $rel, true);
        
        return true;
    }

    /**
     * CORE::_reorder_children()
     *
     * @param  $relation
     * @return boolean
     * @access private
     */
    function _reorder_children($zone, $rel, $abc = false)
    {
        global $DB, $_ZONESSORT;
        
        if (! empty($_ZONESSORT[$zone]))
            $abc = true;
        
        if ($rel)
            $relation = "and relationtype = '$rel'"; else
            $relation = "and relationtype is NULL";
        
        if ($abc) {
            $sql = "SELECT rel.* FROM " . TQT . "relations" . TQT . " rel, " . TQT . "global" . TQT . " glb, " . TQT . "data" . TQT . " d
                  WHERE
                      glb._zone = '" . $zone . "' and
                      rel.targetid = glb.id and
                      rel.parentid = '" . $this->id . "' and
                      d.gid = rel.targetid
                      $relation
                  ORDER BY d." . OQT . "data" . OQT . " ASC";
        } else {
            $sql = "SELECT rel.* FROM " . TQT . "relations" . TQT . " rel, " . TQT . "global" . TQT . " glb
                  WHERE
                      glb._zone = '" . $zone . "' and
                      rel.targetid = glb.id and
                      rel.parentid = '" . $this->id . "'
                      $relation
                  ORDER BY rel." . OQT . "_order" . OQT . " ASC";
        }
        $_data = $DB->getAll($sql);
        
        $end = count($_data);
        $sql = null;
        for($i = 0; $i < $end; $i++) {
            $_data[$i] = SQL_fixNames($_data[$i]);
            $sql = "
            UPDATE " . TQT . "relations" . TQT . " SET " . TQT . "_order" . TQT . " = '" . ($i + 1) . "'
            WHERE
            " . TQT . "targetid" . TQT . "='" . $_data[$i]->targetid . "' and
            " . TQT . "parentid" . TQT . "='" . $_data[$i]->parentid . "'
            $relation;";
            $DB->query($sql);
        }
        return true;
    }

    /**
     * CORE::_get_last_order()
     *
     * @param text $relation
     * @return integer
     * @access private
     */
    function _get_last_order($zone, $rel = null)
    {
        global $DB;
        
        if ($rel)
            $relation = "and relationtype = '$rel'"; else
            $relation = "and relationtype is NULL";
        
        $sql = "
          SELECT rel._order as " . TQT . "last" . TQT . "
            FROM " . TQT . "relations" . TQT . " rel, " . TQT . "global" . TQT . " glb
              WHERE
              glb._zone = '" . $zone . "' and
              rel.targetid = glb.id and
              rel.parentid = '" . $this->id . "'
              $relation
            ORDER BY rel." . OQT . "_order" . OQT . " DESC";
        
        SQL_addlimit($sql, 1, 0);
        
        $_data = $DB->getRow($sql);
        $last = $_data->last + 1;
        return $last;
    }

    /**
     * CORE::removeChild()
     *
     * @param  $_id
     * @return NULL
     * @access public
     */
    function removeChild($_id)
    {
        if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        $this->_delete_relation($id);
    }

    /**
     * CORE::removeRelation()
     *
     * @param  $_id
     * @param string $relation
     * @return NULL
     * @access public
     */
    function removeRelation($_id, $relation = '')
    {
        if (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        $this->_delete_relation($id, $relation);
    }

    /**
     * CORE::_delete_relation()
     *
     * @param  $id
     * @param boolean $relation
     * @return NULL
     * @access private
     */
    function _delete_relation($id, $relation = false)
    {
        global $DB;
        $pid = $this->id;
        $sql = "DELETE FROM relations WHERE targetid = '$pid' and parentid = '$id' and relationtype" . (($relation)?" = '$relation'":" is NULL") . "";
        $DB->query($sql);
        
        return true;
    }

    /**
     * CORE::changeParent()
     *
     * @param  $parent
     * @param  $newparent
     * @return boolean
     * @access public
     */
    function changeParent($parent, $newparent)
    {
        if (is_object($parent)) {
            $id = $parent->id;
        } else {
            $id = $parent;
        }
        $this->_delete_relation($id);
        if (is_object($newparent)) {
            $PO = $newparent;
        } else {
            $PO = new CORE($newparent);
        }
        
        $result = $PO->_add_relation($this->id);
        
        if ($result)
            return true;
        
        return false;
    }

    /**
     * CORE::changeZone()
     *
     * @param  $zone
     * @return boolean
     * @access public
     */
    function changeZone($zone)
    {
        global $DB;
        
        $sql = "UPDATE " . TQT . "global" . TQT . " SET " . TQT . "_date" . TQT . " = '" . time() . "', " . TQT . "_zone" . TQT . " = '" . $zone . "' WHERE " . TQT . "id" . TQT . "='" . $this->id . "'";
        $DB->query($sql);
        
        return true;
    }

    /**
     * This function moves up and down objects
     * CORE::changeOrder()
     *
     * @param  $objectid
     * @param  $direction
     * @param unknown $relationtype
     * @param unknown $zone
     * @return boolean
     * @access public
     */
    function changeOrder($objectid, $direction, $relationtype = null, $zone)
    {
        global $DB;
        if (! $direction)
            return false;
        $sql = "
          SELECT relations.*
            FROM " . TQT . "global" . TQT . " glb , " . TQT . "relations" . TQT . "
              WHERE
                relations.parentid = '" . $this->id . "' AND
                " . (($zone)?"glb._zone = '" . $zone . "' AND":"") . "
                " . (($relationtype)?"relations.relationtype = '" . $relationtype . "' AND":"") . "
                glb.id = relations.targetid
                ORDER BY relations." . OQT . "_order" . OQT . " ASC";
        
        $objects = $DB->getAll($sql);
        
        $end = count($objects);
        for($i = 0; $i < $end; $i++) {
            $objects[$i] = SQL_fixNames($objects[$i]);
            if ($objects[$i]->targetid == $objectid)
                break;
        }
        
        if (! is_object($objects[$i + $direction]))
            return false;
        
        if ($relationtype)
            $relationtypesql = " and relationtype='$relationtype'"; else
            $relationtypesql = " and relationtype is NULL";
        
        $sql = "UPDATE relations SET " . TQT . "_order" . TQT . "='" . $objects[$i + $direction]->_order . "' WHERE parentid='" . $this->id . "' and targetid='" . $objects[$i]->targetid . "'$relationtypesql";
        $DB->query($sql);
        $sql = "UPDATE relations SET " . TQT . "_order" . TQT . "='" . $objects[$i]->_order . "' WHERE parentid='" . $this->id . "' and targetid='" . $objects[$i + $direction]->targetid . "'$relationtypesql";
        $DB->query($sql);
        
        $this->_reorder_children($zone, $relationtype);
        
        return true;
    }

    /**
     * CORE::_populate_data()
     *
     * @param  $data
     * @return
     * @access private
     */
    function _populate_data($data)
    {
        $data = (array)$data;
        // $_val = array_values($data);
        $_key = array_keys($data);
        $_end = count($_key);
        for($i = 0; $i < $_end; $i++) {
            $key_ = trim($_key[$i]);
            $this->$key_ = trim($data[$key_]);
            unset($key_);
        }
    }

    /**
     * CORE::_doXML_data()
     *
     * @param  $data
     * @return
     * @access private
     */
    function _doXML_data($data)
    {
        $data = (array)$data;
        $_key = array_keys($data);
        $_end = count($_key);
        for($i = 0; $i < $_end; $i++) {
            $key_ = trim($_key[$i]);
            $data_ = trim($data[$key_]);
            if ($data_ && ($key_{0} != "_") && ($key_ != "id")) {
                $XML .= "<" . $key_ . "><![CDATA[" . $data_ . $xend_ . "]]></" . $key_ . ">\n";
            }
            unset($key_, $data_);
        }
        $this->data = $XML;
    }

    /**
     * CORE::_load_from_array()
     *
     * @param  $array
     * @return array
     * @access private
     */
    function _load_from_array($array)
    {
        if (! is_array($array))
            return null;
        
        $end = count($array);
        $result = null;
        for($i = 0; $i < $end; $i++) {
            $result[$i] = $this->load($array[$i]);
        }
        return $result;
    }
    
    /**
     * CORE::_delete_user()
     *
     * @return NULL
     * @access rpivate
     */
    function _delete_user()
    {
        global $DB;
        // Delete old values
        $sql = "DELETE FROM " . TQT . "users" . TQT . " WHERE " . TQT . "gid" . TQT . " = '" . $this->id . "'";
        $DB->query($sql);
    }

    /**
     * CORE::getListOfReferencingObjects()
     *
     * @return array
     * @access public
     */
    function getListOfReferencingObjects()
    {
        global $DB;
        $sql = "SELECT rel.targetid AS id FROM relations rel WHERE rel.parentid = '" . $this->id . "'";
        
        $data = $DB->getAll($sql);
        
        $result = $this->_load_from_array($data);
        return $result;
    }

    /**
     * CORE::getByTypeAndOwner()
     *
     * @param  $type
     * @param  $owner
     * @return array
     * @access public
     */
    function getByTypeAndOwner($type, $owner)
    {
        global $DB, $DTD, $PLATFORMID;
        
        $typeid = $DTD->get_object_id($type);
        
        $sql = "
            SELECT * FROM global
            WHERE
            " . TQT . "_owner" . TQT . " = '$owner' and
            " . TQT . "_platform" . TQT . "='$PLATFORMID' and
            " . TQT . "_object" . TQT . " = '$typeid'";
        
        $data = $DB->getAll($sql);
        $result = $this->_load_from_array($data);
        return $result;
    }

    /**
     * CORE::getAllOfType()
     *
     * @param  $type
     * @return array $result
     * @access public
     */
    function getAllOfType($type)
    {
        global $DB, $DTD, $PLATFORMID;
        
        $_more_sql = $this->buildFilter(TQT . "global" . TQT);
        
        if (is_numeric($type))
            $typeid = $type;
        
        $typeid = $DTD->get_object_id($type);
        
        $sql = "
          SELECT * FROM " . TQT . "global" . TQT . "
          WHERE
            " . TQT . "_object" . TQT . " = '$typeid' and
            " . TQT . "_platform" . TQT . "='$PLATFORMID' $_more_sql";
        
        $data = $DB->getAll($sql);
        // return $data;
        $result = $this->_load_from_array($data);
        return $result;
    }

    /**
     * CORE::getByZoneTypeAndParam()
     *
     * @param  $_DTD
     * @param  $zone
     * @param  $type
     * @param  $param
     * @param boolean $load
     * @return array $result
     * @access public
     */
    function getByZoneTypeAndParam($_DTD, $zone, $type, $param, $load = true)
    {
        global $DB, $DTD, $PLATFORMID, $CURRENTLANGUAGE;
        $typeid = $DTD->get_field_id($_DTD, $type);
        $this->_objectname = $_DTD;
        $this->_object = $DTD->get_object_id($_DTD);
        $this->_load_dtd(true);
        $field_ = explode(":", $this->_dtd[$typeid]["data"][2]["data"]);
        if ($field_[0] == "number") {
            $field = "number";
        } else if ($this->_dtd[$typeid]["field"] == "date") {
            $field = "number";
        } else {
            $field = "text";
        }
        $sql = "
          SELECT DISTINCT (" . TQT . "global" . TQT . ".id), " . TQT . "global" . TQT . ".* FROM " . TQT . "global" . TQT . ", " . TQT . "elm_" . $field . "" . TQT . ", " . TQT . "data" . TQT . "
          WHERE
            (" . TQT . "global" . TQT . "._object='" . $this->_object . "')
            and (" . TQT . "global" . TQT . "._zone='" . $zone . "')
            and (" . TQT . "global" . TQT . ".id = " . TQT . "elm_" . $field . "" . TQT . ".gid)
            and (" . TQT . "elm_" . $field . "" . TQT . ".elm = '$type')
            and (" . TQT . "elm_" . $field . "" . TQT . ".data = '$param')
            and (" . TQT . "data" . TQT . "." . TQT . "langid" . TQT . " = '{$CURRENTLANGUAGE}' and " . TQT . "global" . TQT . ".id = " . TQT . "data" . TQT . "." . TQT . "gid" . TQT . ")
        ";
        
        if ($PLATFORMID) {
            $sql .= "\n and (" . TQT . "global" . TQT . "._platform='$PLATFORMID')";
            $sql .= $this->buildFilter("global");
        }
        $sql .= ";";
        //die($sql);
        $data = $DB->getAll($sql);
        
        if ($load == false) {
            $result = $data;
        } else {
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                $data[$i] = SQL_fixNames($data[$i]);
                $result[$i] = $this->load($data[$i]);
            }
        }
        // $this->dump($result);
        return $result;
    }

    /**
     * CORE::getChildrenByType()
     *
     * @param  $_DTD
     * @param boolean $load
     * @return array $result
     * @access public
     */
    
    function getChildrenByType($_DTD, $load = true)
    {
        global $DB, $DTD, $PLATFORMID;
        
        $pid = $this->id;
        $_more_sql = $this->buildFilter("glb");
        $_object = $DTD->get_object_id($_DTD);
        
        $sql = "
          SELECT glb.* FROM " . TQT . "global" . TQT . " glb, relations
          WHERE
            glb._platform = '$PLATFORMID' and
            relations.parentid = '$pid' and
            relations.relationtype is NULL and
            glb.id = relations.targetid and
            (glb._object='" . $_object . "')
            $_more_sql;
        ";
        
        $data = $DB->getAll($sql);
        
        if ($load == false) {
            $result = $data;
        } else {
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                $data[$i] = SQL_fixNames($data[$i]);
                $result[$i] = $this->load($data[$i]);
            }
        }
        return $result;
    }

    /**
     * CORE::getChildrenByTypeAndParam()
     *
     * @param  $_DTD
     * @param  $type
     * @param  $param
     * @param boolean $load
     * @return array $result
     * @access public
     */
    
    function getChildrenByTypeAndParam($_DTD, $type, $param, $load = true)
    {
        global $DB, $DTD, $PLATFORMID;
        
        $pid = $this->id;
        $typeid = $DTD->get_field_id($_DTD, $type);
        $_more_sql = $this->buildFilter("glb");
        $this->_objectname = $_DTD;
        $this->_object = $DTD->get_object_id($_DTD);
        $this->_load_dtd(true); //true
        $field_ = explode(":", $this->_dtd[$typeid]["data"][2]["data"]);
        if ($field_[0] == "number") {
            $field = "number";
        } else if ($this->_dtd[$typeid]["field"] == "date") {
            $field = "number";
        } else {
            $field = "text";
        }
        
        // I should find another way to do this query
        // and not using DISTINCT
        $sql = "
          SELECT DISTINCT glb.* FROM " . TQT . "global" . TQT . " glb, elm_" . $field . ", relations
          WHERE
            glb._platform = '$PLATFORMID' and
            relations.parentid = '$pid' and
            relations.relationtype is NULL and
            glb.id = relations.targetid and
            (glb._object='" . $this->_object . "') and
            (glb.id = elm_" . $field . ".gid) and
            (elm_" . $field . ".elm = '$type') and
            (elm_" . $field . ".data = '$param')
            $_more_sql
            ORDER BY relations." . OQT . "_order" . OQT . " ASC;
        ";
        //die($sql);
        $data = $DB->getAll($sql);
        if ($load == false) {
            $result = $data;
        } else {
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                $data[$i] = SQL_fixNames($data[$i]);
                $result[$i] = $this->load($data[$i]);
            }
        }
        return $result;
    }

    /**
     * CORE::getByParam()
     *
     * @param  $_DTD
     * @param  $type
     * @param  $param
     * @param boolean $load
     * @return array
     * @access public
     */
    function getByExactValue($type, $param, $load = true)
    {
        global $DB, $PLATFORMID;
        
        $_more_sql = $this->buildFilter(TQT . "global" . TQT);
        
        $sql = "SELECT " . TQT . "global" . TQT . ".* FROM " . TQT . "global" . TQT . ", " . TQT . "elm_text" . TQT . "
          WHERE
            (" . TQT . "global" . TQT . ".id = " . TQT . "elm_text" . TQT . ".gid)
            AND (" . TQT . "elm_text" . TQT . ".elm = '$type' AND " . TQT . "elm_text" . TQT . ".data = '$param')
            AND (" . TQT . "global" . TQT . "._platform='$PLATFORMID')
            $_more_sql";
        $data = $DB->getAll($sql);
        if ($load == false) {
            $result = $data;
        } else {
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                $data[$i] = SQL_fixNames($data[$i]);
                $result[$i] = $this->load($data[$i]);
            }
        }
        return $result;
    }

    /**
     * CORE::getByTypeAndParam()
     *
     * @param  $_DTD
     * @param  $type
     * @param  $param
     * @param boolean $load
     * @return array
     * @access public
     */
    function getByTypeAndParam($_DTD, $type, $param, $load = true)
    {
        global $DB, $DTD, $PLATFORMID;
        $typeid = $DTD->get_field_id($_DTD, $type);
        $this->_objectname = $_DTD;
        $this->_object = $DTD->get_object_id($this->_objectname);
        $this->_load_dtd(true);
        $field_ = explode(":", $this->_dtd[$typeid]["data"][2]["data"]);
        
        $_more_sql = $this->buildFilter(TQT . "global" . TQT);
        
        if ($field_[0] == "number") {
            $field = "number";
        } else if ($this->_dtd[$typeid]["field"] == "date") {
            $field = "number";
        } else {
            $field = "text";
        }
        $sql = "
          SELECT " . TQT . "global" . TQT . ".* FROM " . TQT . "global" . TQT . ", elm_" . $field . "
          WHERE
            (global.id = elm_" . $field . ".gid)
            and (" . TQT . "global" . TQT . "._object='" . $this->_object . "')
            and (" . TQT . "elm_" . $field . "" . TQT . ".elm = '$type')
            and (" . TQT . "elm_" . $field . "" . TQT . ".data = '$param') ";
        
        if ($PLATFORMID) {
            $sql .= "\n and (" . TQT . "global" . TQT . "._platform='$PLATFORMID')";
            $sql .= $_more_sql;
        }
        $sql .= " ORDER BY " . TQT . "global" . TQT . "." . TQT . "id" . TQT . " DESC;";
        
        $data = $DB->getAll($sql);
        if ($load == false) {
            $result = $data;
        } else {
            $end = count($data);
            for($i = 0; $i < $end; $i++) {
                $data[$i] = SQL_fixNames($data[$i]);
                $result[$i] = $this->load($data[$i]);
            }
        }
        return $result;
    }

    /**
     * CORE::SelectByGlobalParam()
     *
     * @param  $param
     * @param  $value
     * @return $result
     */
    function SelectByGlobalParam($param, $value)
    {
        global $DB, $PLATFORMID;
        
        if (! $param || ! $value)
            return null;
        
        $_more_sql = $this->buildFilter(TQT . "global" . TQT);
        
        $sql = "
          SELECT * FROM global
          WHERE
            $param = '$value' and
            _platform = '$PLATFORMID' $_more_sql";
        
        $data = $DB->getAll($sql);
        // return $data;
        $result = $this->_load_from_array($data);
        return $result;
    }

    /**
     * CORE::getTree()
     *
     * @param unknown $startid
     * @param integer $relationtype
     * @return array
     * @access public
     */
    function getTree($startid = null, $relationtype = 0)
    {
        global $CURRENTPLATFORM, $DB;
        if (! $startid)
            $startid = $this->id;
        
        if ($relationtype) {
            $relsql = " rel.relationtype='" . $relationtype . "' ";
        } else {
            $relsql = " rel.relationtype is NULL ";
        }
        $result = array(
            
        );
        $nowid = $startid;
        $stillgoing = true;
        while ($nowid != $CURRENTPLATFORM->id && $stillgoing) {
            $result[] = $nowid;
            $sql = "select rel.parentid from relations rel where rel.targetid=" . $nowid . " AND " . $relsql . " ORDER BY rel." . OQT . "_order" . OQT . " ASC";
            $nowid = $DB->GetOne($sql);
            if (! $nowid) {
                $stillgoing = false;
            }
        }
        
        return $result;
    }

    /**
     * CORE::getFullTree()
     *
     * @param unknown $startid
     * @return array
     * @access public
     */
    function getFullTree($startid = null)
    {
        global $CURRENTPLATFORM, $DB;
        if (! $startid)
            $startid = $this->id;
        $result = array(
            
        );
        $nowid = $startid;
        $stillgoing = true;
        while ($nowid != $CURRENTPLATFORM->id && $stillgoing) {
            $result[] = $nowid;
            
            $sql = "select rel.parentid from relations rel where rel.targetid=" . $nowid . " ORDER BY rel." . OQT . "_order" . OQT . " ASC";
            
            $nowid = $DB->GetOne($sql);
            
            if (! $nowid)
                $stillgoing = false;
        }
        return $result;
    }

    /**
     * CORE::translate_object_data()
     *
     * @return array $RETURN
     * @access public
     */
    function translate_object_data($ADDPRIMARY = null)
    {
        global $SYS_LANGUAGES, $LANGUAGES, $EDITLANGUAGE;
        
        if (! $this->_arraydata)
            $this->_xml_to_array();
        $ARRAY = $this->_arraydata[0]["data"][0]["data"];
        //$this->dump($ARRAY);
        for($i = 0; $i < count($ARRAY); $i++) {
            $RETURN[$ARRAY[$i]["element_name"]] = $ARRAY[$i]["data"];
        }
        if ($ADDPRIMARY && ($SYS_LANGUAGES[$LANGUAGES[0]]["id"] != $EDITLANGUAGE)) {
            $this->_xml_to_array($SYS_LANGUAGES[$LANGUAGES[0]]["id"]);
            $ARRAY = $this->_arraydata[0]["data"][0]["data"];
            for($i = 0; $i < count($ARRAY); $i++) {
                
                $RETURN_[$ARRAY[$i]["element_name"]] = $ARRAY[$i]["data"];
            }
            $RETURN["_primary"] = $RETURN_;
        }
        return $RETURN;
    }

    /**
     * CORE::translate_object_atributes()
     *
     * @return array $RETURN
     * @access public
     */
    function translate_object_atributes()
    {
        if (! $this->_arraydata)
            $this->_xml_to_array();
        
        return $this->_arraydata[0]["data"][0]["attributes"];
    }

    /**
     * CORE::_xml_to_array()
     *
     * @return array
     * @access private
     */
    function _xml_to_array($LANG = null)
    {
        global $XML, $DTD, $EDITLANGUAGE, $DEFAULTLANGUAGE;
        
        $is_ml = $DTD->is_object_multilingual($this->_object);
        if (! $is_ml) {
            $name = "data_" . $DEFAULTLANGUAGE;
        } else if ($LANG) {
            $name = "data_" . $LANG;
        } else {
            $name = "data_" . $EDITLANGUAGE;
        }
        $this->_arraydata = $XML->xml2array($this->$name);
        
        return $this->_arraydata;
    }

    /**
     * CORE::_assgin_object()
     *
     * @param  $O
     * @return NULL
     * @access private
     */
    function _assgin_object($O)
    {
        $Obj = (array)$O;
        $end = count($Obj);
        $key = array_keys($Obj);
        for($i = 0; $i < $end; $i++) {
            $this->$key[$i] = $Obj[$key[$i]];
        }
    }

    /**
     * Dump function
     * Can be used to dump current or any other object
     *
     * CORE::dump()
     *
     * @param unknown $value
     * @return NULL
     * @access public
     */
    function dump($value = null)
    {
        if (! $value)
            $value = $this;
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    }

    /**
     * Current function will create *NIX like permissions SQL filter
     *
     * CORE::buildFilter()
     *
     * @param unknown $value
     * @return NULL
     * @access public
     */
    function buildFilter($tbl)
    {
        global $CURRENTUSER;
        if (is_object($CURRENTUSER) && $CURRENTUSER->isSuperUser())
            return null;
        if ($CURRENTUSER->_loggedin === true)
            $_more .= "$tbl._owner = '" . $CURRENTUSER->id . "'";
        $_more .= ((trim($_more) != '')?" or ":"") . "($tbl._w > '0')";
        
        $end = count($CURRENTUSER->_permissions["_groups"]);
        for($i = 0; $i < $end; $i++) {
            $_more .= ((trim($_more) != '')?" or ":"") . "(($tbl._group = '" . $CURRENTUSER->_permissions["_groups"][$i] . "') and ($tbl._g > '0'))";
        }
        
        if (trim($_more) != '')
            $_more = " and (" . $_more . ")";
        
        return $_more;
    }

    function html_entity_decode($val)
    {
        return str_replace(array(
            "&quot;"
        ), array(
            "\""
        ), $val);
    }

}

?>