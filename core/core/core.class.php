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
    public $_arrayData;
    public $_date;
    public $_dtd;
    public $_g;
    public $_group;
    public $_hidden;
    public $_module;
    public $_o;
    public $_object;
    public $_objectname;
    public $_owner;
    public $_parent;
    public $_platform;
    public $_w;
    public $_workflow;
    public $_xslt;
    public $_zone;
    public $data;
    public $id;

    // DTD-defined fields are stored here rather than as undeclared properties.
    public $_dynamicData = array();

    function __get($name)
    {
        return array_key_exists($name, $this->_dynamicData) ? $this->_dynamicData[$name] : null;
    }

    function __set($name, $value)
    {
        $this->_dynamicData[$name] = $value;
    }

    function __isset($name)
    {
        return isset($this->_dynamicData[$name]);
    }

    function __unset($name)
    {
        unset($this->_dynamicData[$name]);
    }

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
            if ($key[$i][0] == "_") {
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
        $sql = "SELECT * FROM global WHERE id = ?";
        $params = array($param);
        if ($PLATFORMID) {
            $platform = $DB->quoteIdentifier("_platform", "column");
            $sql .= " AND $platform = ?";
            $params[] = $PLATFORMID;
        }
        $data = $DB->fetchRow($sql, $params);
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
        $rows = $DB->fetchAll("SELECT * FROM data WHERE gid = ?", array($this->id));
        $_tag = $this->_objectname;
        foreach ($rows as $row) {
            if (strtoupper($this->_CLASS) == "DATA") {
                $xml = $this->html_entity_decode($row["data"]);
                $xml .= $this->_object_data();
            } else {
                $xml = "<$_tag parentid='" . $this->_parent . "' zone='" . $this->_zone . "' id='" . $this->id . "'>\n";
                $xml .= $this->html_entity_decode($row["data"]);
                $xml .= $this->_object_data();
                $xml .= "</$_tag>\n";
            }
            $name = "data_" . $row["langid"];
            $this->$name = $xml;
            $is_ml = $DTD->is_object_multilingual($this->_object);
            if (($is_ml && $row["langid"] == $CURRENTLANGUAGE) || (!$is_ml && $row["langid"] == $DEFAULTLANGUAGE)) {
                $this->data = $xml;
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
        $xml = "";
        if (strtoupper($this->_CLASS) == "PERSON") {
            $row = $DB->fetchRow("SELECT * FROM users WHERE gid = ?", array($this->id));
            if ($row !== null) {
                $xml .= "<_username>" . $row["username"] . "</_username>\n";
                $xml .= "<_password>" . $row["password"] . "</_password>\n";
                $xml .= "<_group>" . $row["_group"] . "</_group>\n";
            }
        }
        $xml .= "<_module>" . $this->_module . "</_module>\n";
        $xml .= "<_xslt>" . $this->_xslt . "</_xslt>\n";
        return $xml;
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
        global $PLATFORMID, $NEWPLATFORMID, $CURRENTUSER, $CURRENTPLATFORM, $DB;
        $val = (array)$this;
        if ($this->_objectname != '') {
            $val["_object"] = $this->_objectname;
        }
        $this->_w = $this->_w ? $this->_w : 4;
        $this->_g = $this->_g ? $this->_g : 6;
        $this->_o = $this->_o ? $this->_o : 6;
        $this->_w = !$this->_hidden ? $this->_w : 0;
        $workflow = $this->_workflow ? $this->_workflow : 0;
        $this->_group = !$this->_group ? $CURRENTPLATFORM->_group : $this->_group;
        $columns = array("_owner", "_object", "_date", "_zone", "_platform", "_workflow", "_module", "_xslt", "_group", "_o", "_g", "_w");
        $quoted = array();
        foreach ($columns as $column) {
            $quoted[] = $DB->quoteIdentifier($column, "column");
        }
        $sql = "INSERT INTO global (" . implode(", ", $quoted) . ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $DB->begin();
        try {
            $DB->execute($sql, array(
                $CURRENTUSER->id ? $CURRENTUSER->id : 0,
                $this->_object,
                time(),
                $this->_zone ? $this->_zone : null,
                $PLATFORMID ? $PLATFORMID : 0,
                $workflow,
                $this->_module === null ? "" : $this->_module,
                $this->_xslt === null ? "" : $this->_xslt,
                $this->_group ? $this->_group : 0,
                $this->_o,
                $this->_g,
                $this->_w,
            ));
            $oid = $DB->lastId();
            if (!$PLATFORMID || $NEWPLATFORMID) {
                $platformColumn = $DB->quoteIdentifier("_platform", "column");
                $DB->execute("UPDATE global SET $platformColumn = ? WHERE id = ?", array($oid, $oid));
            }
            $this->id = $oid;
            $this->_insert_data(null, $oid);
            if (strtoupper($this->_objectname) == "PERSON") {
                $person = new PERSON($oid);
                $person->_update_person();
            }
            $DB->commit();
            if ($NEWPLATFORMID) {
                $PLATFORMID = $oid;
            }
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
        }
        if (strtoupper($this->_objectname) == "PERSON") {
            $object = new PERSON($oid);
        } else {
            $object = $this->load($oid);
        }
        $this->_assgin_object($object);
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
        if (!$val) {
            $_DATA = $this->data;
            if ($this->_CLASS == "person") {
                $this->_update_person();
            }
        } else {
            $_DATA = $val->data;
            if ($val->_CLASS == "person") {
                $this->_update_person($val);
            }
        }
        $DB->execute("INSERT INTO data (gid, langid, data) VALUES (?, ?, ?)", array($oid, $EDITLANGUAGE, $_DATA === null ? "" : $_DATA));
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
        if (!$_DATA) {
            $_DATA = $this->_arrayData;
        }
        if (!$_DTD) {
            $_DTD = $this->_dtd;
        }
        if (!$oid) {
            $oid = $this->id;
        }
        if ($_DTD) {
            foreach ($_DTD as $ELM) {
                $value = isset($_DATA[$ELM["element_name"]]) ? $_DATA[$ELM["element_name"]] : null;
                if (trim((string)$value) === '' || trim((string)$value) === "<br />") {
                    continue;
                }
                $type = $ELM["data"][2]["data"];
                if ($type == "number") {
                    $tableName = "elm_number";
                } elseif ($type == "date") {
                    $tableName = "elm_date";
                } else {
                    $tableName = "elm_text";
                }
                $table = $DB->quoteIdentifier($tableName, "table");
                $DB->execute("INSERT INTO $table (gid, elm, data, lang) VALUES (?, ?, ?, ?)", array($oid, $ELM["element_name"], $value, $EDITLANGUAGE));
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
        global $DB;
        if ($val) {
            $id = $val["id"];
            $data = $val;
        } else {
            $id = $this->id;
            $data = $this;
        }
        FILES::_update_file($id, $data);
        $DB->begin();
        try {
            $this->_update_object($data);
            $this->_delete_data($id);
            $this->_insert_data($data, $id);
            if (strtoupper($this->_objectname) == "PERSON") {
                $person = new PERSON($id);
                $person->_update_person();
            }
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
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
        if (!is_array($val)) {
            settype($val, "array");
        }
        $now = time();
        $date = $DB->quoteIdentifier("_date", "column");
        $workflow = $DB->quoteIdentifier("_workflow", "column");
        $module = $DB->quoteIdentifier("_module", "column");
        $xslt = $DB->quoteIdentifier("_xslt", "column");
        $group = $DB->quoteIdentifier("_group", "column");
        $DB->execute("UPDATE global SET $date = ?, $workflow = ?, $module = ?, $xslt = ?, $group = ? WHERE id = ?", array($now, $val["_workflow"] === null ? "" : $val["_workflow"], $val["_module"] === null ? "" : $val["_module"], $val["_xslt"] === null ? "" : $val["_xslt"], $val["_group"] === null ? "" : $val["_group"], $val["id"]));
        $this->_update_parent($val["id"], $now);
        $this->_execute_after_hooks($val["id"], "update");
    }
    
	/**
     * CORE::_update_parent()
     * 
     * This function updates the modifikation time on the parent object if some.
     *
     * @param  $id
     * @param  $now
     * @return NULL
     * @access private
     */
    function _update_parent($id, $now)
    {
        global $DB;
        $parents = $DB->fetchAll("SELECT parentid FROM relations WHERE targetid = ? AND relationtype IS NULL", array($id));
        $date = $DB->quoteIdentifier("_date", "column");
        foreach ($parents as $parent) {
            $DB->execute("UPDATE global SET $date = ? WHERE id = ?", array($now, $parent["parentid"]));
        }
    }
    
    /**
     * CORE::_execute_after_hooks()
     * 
     * Execute hooks that are intended to run after object update
     * 
     * @param $oid
     * @return unknown_type
     */
    function _execute_after_hooks ($oid, $action = false) {
        global $_HOOKS_AFTER;
        
        foreach ($_HOOKS_AFTER AS $HOOK) {
            $function = "{$HOOK}_hook_after";
            $mod = dirname ( __FILE__ ) . "/../modules/{$HOOK}/{$HOOK}.hook.php";
            
            include_once($mod);
            $done = $function($oid, $action);
        }
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
        global $DB;
        if (!$_id) {
            $id = $this->id;
        } elseif (is_object($_id)) {
            $id = $_id->id;
        } else {
            $id = $_id;
        }
        if (!$id) {
            return false;
        }
        if (!$this->candelete($id)) {
            return true;
        }
        $DB->begin();
        try {
            $this->_update_parent($id, time());
            $this->_execute_after_hooks($id, "delete");
            FILES::delete_files($id);
            $this->_delete_ids($id);
            $this->_delete_data($id, 1);
            $this->_delete_relations($id);
            if (strtoupper($this->_objectname) == "PERSON") {
                $this->_delete_user();
            }
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
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
        $DB->execute("DELETE FROM global WHERE id = ?", array($id));
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
        if (!$id) {
            return false;
        }
        if ($all == false) {
            $DB->execute("DELETE FROM data WHERE gid = ? AND langid = ?", array($id, $EDITLANGUAGE));
        } else {
            $DB->execute("DELETE FROM data WHERE gid = ?", array($id));
        }
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
        if (!$oid) {
            $oid = $this->id;
        }
        foreach (array("elm_text", "elm_date", "elm_number") as $tableName) {
            $table = $DB->quoteIdentifier($tableName, "table");
            if ($all == false) {
                $DB->execute("DELETE FROM $table WHERE gid = ? AND lang = ?", array($oid, $EDITLANGUAGE));
            } else {
                $DB->execute("DELETE FROM $table WHERE gid = ?", array($oid));
            }
        }
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
        $DB->execute("DELETE FROM relations WHERE targetid = ?", array($id));
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
        if (!$id) {
            $id = $this->id;
        }
        $sql = $DB->paginate("SELECT * FROM relations WHERE parentid = ?", 1, 0);
        return count($DB->fetchAll($sql, array($id))) === 0;
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

        $pid = $this->id != '' ? $this->id : $PLATFORMID;
        $params = array($pid);
        $where = $relation ? "rel.relationtype = ?" : "rel.relationtype IS NULL";
        if ($relation) {
            $params[] = $relation;
        }
        if ($zone) {
            $params[] = $zone;
        }
        $filterParams = array();
        $filter = $this->buildFilter("glb", $filterParams);
        $params = array_merge($params, $filterParams);
        $order = $DB->quoteIdentifier("_order", "column");
        $sql = "SELECT glb.* FROM relations rel, global glb WHERE rel.parentid = ? AND $where AND rel.targetid = glb.id";
        if ($zone) {
            $sql .= " AND glb._zone = ?";
        }
        $sql .= $filter . " ORDER BY rel.$order ASC";
        $rows = $DB->fetchAll($sql, $params);
        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->load($row);
        }
        return $result;
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

        $sql = "SELECT rel.parentid FROM relations rel, global glb WHERE rel.targetid = ? AND rel.relationtype IS NULL AND rel.targetid = glb.id AND glb._platform = ?";
        $row = $DB->fetchRow($sql, array($this->id, $CURRENTPLATFORM->id));
        if ($row !== null) {
            $data = $this->load($row["parentid"]);
        }
        return is_object($data) ? $data : $CURRENTPLATFORM;
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
        global $DB;
        $id = is_object($_id) ? $_id->id : $_id;
        $DB->begin();
        try {
            if (!$this->_add_relation($id, $zone, null, false)) {
                $DB->rollback();
                return false;
            }
            $this->_update_parent($id, time());
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
        }
        $this->_execute_after_hooks($id, "addchild");
        return true;
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
    function _add_relation($id, $zone, $rel = null, $transaction = true)
    {
        global $ADDOBJECTONTOP, $DB;
        if ($transaction) {
            $DB->begin();
        }
        try {
            if ($rel) {
                $order = $ADDOBJECTONTOP == true ? 0 : $this->_get_last_order($zone, $rel);
            } else {
                $order = $ADDOBJECTONTOP == true ? 0 : $this->_get_last_order($zone);
            }
            $relationOrder = $DB->quoteIdentifier("_order", "column");
            if ($rel) {
                $sql = "INSERT INTO relations (parentid, targetid, relationtype, $relationOrder) VALUES (?, ?, ?, ?)";
                $done = $DB->execute($sql, array($this->id, $id, $rel, $order));
            } else {
                $sql = "INSERT INTO relations (parentid, targetid, relationtype, $relationOrder) VALUES (?, ?, NULL, ?)";
                $done = $DB->execute($sql, array($this->id, $id, $order));
            }
            $this->_reorder_children($zone, $rel);
            if ($transaction) {
                $DB->commit();
            }
            return $done > 0;
        } catch (DatabaseException $e) {
            if ($transaction) {
                $DB->rollback();
            }
            throw $e;
        }
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
        global $_ZONESSORT, $DB;
        if (!empty($_ZONESSORT[$zone])) {
            return false;
        }
        $DB->begin();
        try {
            $this->_reorder_children($zone, $rel, true);
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
        }
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
        if (!empty($_ZONESSORT[$zone])) {
            $abc = true;
        }
        $params = array($zone, $this->id);
        $where = "";
        if ($rel) {
            $where = " AND rel.relationtype = ?";
            $params[] = $rel;
        } else {
            $where = " AND rel.relationtype IS NULL";
        }
        $sql = "SELECT rel.targetid, rel.parentid FROM relations rel, global glb";
        if ($abc) {
            $sql .= ", data d";
        }
        $sql .= " WHERE glb._zone = ? AND rel.targetid = glb.id AND rel.parentid = ?$where";
        if ($abc) {
            $sql .= " AND d.gid = rel.targetid ORDER BY d.data ASC";
        } else {
            $order = $DB->quoteIdentifier("_order", "column");
            $sql .= " ORDER BY rel.$order ASC";
        }
        $rows = $DB->fetchAll($sql, $params);
        $relationOrder = $DB->quoteIdentifier("_order", "column");
        foreach ($rows as $index => $row) {
            if ($rel) {
                $DB->execute("UPDATE relations SET $relationOrder = ? WHERE targetid = ? AND parentid = ? AND relationtype = ?", array($index + 1, $row["targetid"], $row["parentid"], $rel));
            } else {
                $DB->execute("UPDATE relations SET $relationOrder = ? WHERE targetid = ? AND parentid = ? AND relationtype IS NULL", array($index + 1, $row["targetid"], $row["parentid"]));
            }
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
        $params = array($zone, $this->id);
        $where = "rel.relationtype IS NULL";
        if ($rel) {
            $where = "rel.relationtype = ?";
            $params[] = $rel;
        }
        $order = $DB->quoteIdentifier("_order", "column");
        $sql = $DB->paginate("SELECT rel.$order FROM relations rel, global glb WHERE glb._zone = ? AND rel.targetid = glb.id AND rel.parentid = ? AND $where ORDER BY rel.$order DESC", 1, 0);
        $last = $DB->fetchValue($sql, $params);
        return $last === null ? 1 : $last + 1;
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
        global $DB;
        $id = is_object($_id) ? $_id->id : $_id;
        $DB->begin();
        try {
            $this->_delete_relation($id);
            $this->_update_parent($id, time());
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
        }
        $this->_execute_after_hooks($id, "removechild");
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
        if ($relation) {
            $DB->execute("DELETE FROM relations WHERE targetid = ? AND parentid = ? AND relationtype = ?", array($this->id, $id, $relation));
        } else {
            $DB->execute("DELETE FROM relations WHERE targetid = ? AND parentid = ? AND relationtype IS NULL", array($this->id, $id));
        }
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
        global $DB;
        $id = is_object($parent) ? $parent->id : $parent;
        $PO = is_object($newparent) ? $newparent : new CORE($newparent);
        $DB->begin();
        try {
            $this->_update_parent($this->id, time());
            $this->_delete_relation($id);
            $result = $PO->_add_relation($this->id, null, null, false);
            if (!$result) {
                $DB->rollback();
                return false;
            }
            $this->_update_parent($this->id, time());
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
        }
        $this->_execute_after_hooks($this->id, "changeparent");
        return true;
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
        $date = $DB->quoteIdentifier("_date", "column");
        $zoneColumn = $DB->quoteIdentifier("_zone", "column");
        $DB->execute("UPDATE global SET $date = ?, $zoneColumn = ? WHERE id = ?", array(time(), $zone === null ? "" : $zone, $this->id));
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
    function changeOrder($objectid, $direction, $relationtype = null, $zone = null)
    {
        global $DB;
        if (!$direction) {
            return false;
        }
        $params = array($this->id);
        $where = "";
        if ($zone) {
            $where .= " AND glb._zone = ?";
            $params[] = $zone;
        }
        if ($relationtype) {
            $where .= " AND relations.relationtype = ?";
            $params[] = $relationtype;
        } else {
            $where .= " AND relations.relationtype IS NULL";
        }
        $order = $DB->quoteIdentifier("_order", "column");
        $sql = "SELECT relations.targetid, relations.parentid, relations.$order FROM global glb, relations WHERE relations.parentid = ?$where AND glb.id = relations.targetid ORDER BY relations.$order ASC";
        $objects = $DB->fetchAll($sql, $params);
        $index = null;
        foreach ($objects as $i => $row) {
            if ($row["targetid"] == $objectid) {
                $index = $i;
                break;
            }
        }
        $adjacentIndex = $index === null ? null : $index + $direction;
        if ($adjacentIndex === null || !isset($objects[$index], $objects[$adjacentIndex])) {
            return false;
        }
        $current = $objects[$index];
        $adjacent = $objects[$adjacentIndex];
        $DB->begin();
        try {
            if ($relationtype) {
                $sql = "UPDATE relations SET $order = ? WHERE parentid = ? AND targetid = ? AND relationtype = ?";
                $DB->execute($sql, array($adjacent["_order"], $this->id, $current["targetid"], $relationtype));
                $DB->execute($sql, array($current["_order"], $this->id, $adjacent["targetid"], $relationtype));
            } else {
                $sql = "UPDATE relations SET $order = ? WHERE parentid = ? AND targetid = ? AND relationtype IS NULL";
                $DB->execute($sql, array($adjacent["_order"], $this->id, $current["targetid"]));
                $DB->execute($sql, array($current["_order"], $this->id, $adjacent["targetid"]));
            }
            $this->_reorder_children($zone, $relationtype);
            $this->_update_parent($objectid, time());
            $DB->commit();
        } catch (DatabaseException $e) {
            $DB->rollback();
            throw $e;
        }
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
        if ($data instanceof CORE) {
            $properties = get_object_vars($data);
            $dynamicData = $properties["_dynamicData"];
            unset($properties["_dynamicData"]);
            foreach ($properties as $key => $value) {
                if ($value === null) {
                    unset($properties[$key]);
                }
            }
            $data = array_merge($properties, $dynamicData);
        } else {
            $data = (array)$data;
        }
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
            if ($data_ && ($key_[0] != "_") && ($key_ != "id")) {
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
        $DB->execute("DELETE FROM users WHERE gid = ?", array($this->id));
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
        $rows = $DB->fetchAll("SELECT rel.targetid AS id FROM relations rel WHERE rel.parentid = ?", array($this->id));
        return $this->_load_from_array($rows);
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
        $sql = "SELECT * FROM global WHERE _owner = ? AND _platform = ? AND _object = ?";
        $rows = $DB->fetchAll($sql, array($owner, $PLATFORMID, $typeid));
        return $this->_load_from_array($rows);
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
        $typeid = $DTD->get_object_id($type);
        $filterParams = array();
        $filter = $this->buildFilter("global", $filterParams);
        $sql = "SELECT * FROM global WHERE _object = ? AND _platform = ?" . $filter;
        $rows = $DB->fetchAll($sql, array_merge(array($typeid, $PLATFORMID), $filterParams));
        return $this->_load_from_array($rows);
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
        $field = ($field_[0] == "number" || $this->_dtd[$typeid]["field"] == "date") ? "number" : "text";
        $table = $DB->quoteIdentifier("elm_" . $field, "table");
        $params = array($this->_object, $zone, $type, $param, $CURRENTLANGUAGE);
        $filter = "";
        if ($PLATFORMID) {
            $filterParams = array();
            $filter = " AND global._platform = ?" . $this->buildFilter("global", $filterParams);
            $params[] = $PLATFORMID;
            $params = array_merge($params, $filterParams);
        }
        $sql = "SELECT DISTINCT global.* FROM global, $table, data WHERE global._object = ? AND global._zone = ? AND global.id = $table.gid AND $table.elm = ? AND $table.data = ? AND data.langid = ? AND global.id = data.gid$filter";
        $rows = $DB->fetchAll($sql, $params);
        if (!$load) {
            return $rows;
        }
        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->load($row);
        }
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
        $params = array($PLATFORMID, $this->id, $DTD->get_object_id($_DTD));
        $filterParams = array();
        $filter = $this->buildFilter("glb", $filterParams);
        $sql = "SELECT glb.* FROM global glb, relations WHERE glb._platform = ? AND relations.parentid = ? AND relations.relationtype IS NULL AND glb.id = relations.targetid AND glb._object = ?" . $filter;
        $rows = $DB->fetchAll($sql, array_merge($params, $filterParams));
        if (!$load) {
            return $rows;
        }
        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->load($row);
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
        $this->_objectname = $_DTD;
        $this->_object = $DTD->get_object_id($_DTD);
        $this->_load_dtd(true);
        $field_ = explode(":", $this->_dtd[$typeid]["data"][2]["data"]);
        $field = ($field_[0] == "number" || $this->_dtd[$typeid]["field"] == "date") ? "number" : "text";
        $table = $DB->quoteIdentifier("elm_" . $field, "table");
        $order = $DB->quoteIdentifier("_order", "column");
        $filterParams = array();
        $filter = $this->buildFilter("glb", $filterParams);
        $sql = "SELECT DISTINCT glb.* FROM global glb, $table, relations WHERE glb._platform = ? AND relations.parentid = ? AND relations.relationtype IS NULL AND glb.id = relations.targetid AND glb._object = ? AND glb.id = $table.gid AND $table.elm = ? AND $table.data = ?" . $filter . " ORDER BY relations.$order ASC";
        $rows = $DB->fetchAll($sql, array_merge(array($PLATFORMID, $pid, $this->_object, $type, $param), $filterParams));
        if (!$load) {
            return $rows;
        }
        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->load($row);
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
        $filterParams = array();
        $filter = $this->buildFilter("global", $filterParams);
        $sql = "SELECT global.* FROM global, elm_text WHERE global.id = elm_text.gid AND elm_text.elm = ? AND elm_text.data = ? AND global._platform = ?" . $filter;
        $rows = $DB->fetchAll($sql, array_merge(array($type, $param, $PLATFORMID), $filterParams));
        if (!$load) {
            return $rows;
        }
        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->load($row);
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
        $field = ($field_[0] == "number" || $this->_dtd[$typeid]["field"] == "date") ? "number" : "text";
        $table = $DB->quoteIdentifier("elm_" . $field, "table");
        $filterParams = array();
        $filter = "";
        $params = array($this->_object, $type, $param);
        if ($PLATFORMID) {
            $filter = " AND global._platform = ?" . $this->buildFilter("global", $filterParams);
            $params[] = $PLATFORMID;
            $params = array_merge($params, $filterParams);
        }
        $sql = "SELECT global.* FROM global, $table WHERE global.id = $table.gid AND global._object = ? AND $table.elm = ? AND $table.data = ?$filter ORDER BY global.id DESC";
        $rows = $DB->fetchAll($sql, $params);
        if (!$load) {
            return $rows;
        }
        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->load($row);
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
        if (!$param || !$value) {
            return null;
        }
        $column = $DB->quoteIdentifier($param, "column");
        $filterParams = array();
        $filter = $this->buildFilter("global", $filterParams);
        $sql = "SELECT * FROM global WHERE $column = ? AND _platform = ?" . $filter;
        $rows = $DB->fetchAll($sql, array_merge(array($value, $PLATFORMID), $filterParams));
        return $this->_load_from_array($rows);
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
        if (!$startid) {
            $startid = $this->id;
        }
        $result = array();
        $nowid = $startid;
        while ($nowid != $CURRENTPLATFORM->id && $nowid) {
            $result[] = $nowid;
            $params = array($nowid);
            $where = "";
            if ($relationtype) {
                $where = " AND rel.relationtype = ?";
                $params[] = $relationtype;
            } else {
                $where = " AND rel.relationtype IS NULL";
            }
            $order = $DB->quoteIdentifier("_order", "column");
            $sql = "SELECT rel.parentid FROM relations rel WHERE rel.targetid = ?$where ORDER BY rel.$order ASC";
            $nowid = $DB->fetchValue($sql, $params);
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
        if (!$startid) {
            $startid = $this->id;
        }
        $result = array();
        $nowid = $startid;
        while ($nowid != $CURRENTPLATFORM->id && $nowid) {
            $result[] = $nowid;
            $order = $DB->quoteIdentifier("_order", "column");
            $sql = "SELECT rel.parentid FROM relations rel WHERE rel.targetid = ? ORDER BY rel.$order ASC";
            $nowid = $DB->fetchValue($sql, array($nowid));
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
    function buildFilter($tbl, &$params = array())
    {
        global $CURRENTUSER, $DB;
        if (!in_array($tbl, array("global", "glb"), true)) {
            throw new DatabaseException("The permissions filter table alias is not allowed.");
        }
        if (is_object($CURRENTUSER) && $CURRENTUSER->isSuperUser()) {
            return "";
        }
        $owner = $DB->quoteIdentifier("_owner", "column");
        $write = $DB->quoteIdentifier("_w", "column");
        $group = $DB->quoteIdentifier("_group", "column");
        $read = $DB->quoteIdentifier("_g", "column");
        $conditions = array();
        if ($CURRENTUSER->_loggedin === true) {
            $conditions[] = "$tbl.$owner = ?";
            $params[] = $CURRENTUSER->id;
        }
        $conditions[] = "$tbl.$write > 0";
        $groups = isset($CURRENTUSER->_permissions["_groups"]) && is_array($CURRENTUSER->_permissions["_groups"])
            ? $CURRENTUSER->_permissions["_groups"]
            : array();
        foreach ($groups as $groupId) {
            $conditions[] = "($tbl.$group = ? AND $tbl.$read > 0)";
            $params[] = $groupId;
        }
        return " AND (" . implode(" OR ", $conditions) . ")";
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