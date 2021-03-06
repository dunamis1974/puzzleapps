<?php
require_once $this->MODULEDIR . 'adodb/adodb.inc.php';


/**
 * DATABASE
 * This class extends enable Puzzle Apps to work with ADODB
 * This is debug hack, nothing more.
 * 
 * @package Puzzle Apps
 * @author DuNaMiS 
 * @copyright Copyright (c) 2004
 * @version 0.1
 * @access public 
 */
class DATABASE {
    /**
     * DATABASE::DATABASE()
     * 
     * @param  $dsn
     * @return NULL
     */
    function DATABASE ($dsn) {
        global $DB_DRIVER;
        
        $this->start_time = getmicrotime();
        $this->_do_global_sql("Initiate DB connection!");
        $this->conn = NewADOConnection($dsn);
        $this->conn->debug = false;
        if ($DB_DRIVER == "mysql") {
            $sql = "SET NAMES 'UTF8'";
            $this->conn->Execute($sql);
        }
        $this->_debug_sql("Initiate DB connection!", "DATABASE");
    } 

    /**
     * DATABASE::getAll()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function getAll($sql) {
        $this->start_time = getmicrotime();
        $this->_do_global_sql($sql);
        $data_ = $this->conn->GetAll($sql);
        if (is_array($data_)) {
            foreach($data_ AS $KEY => $VAL) $data[$KEY] = (object)$VAL;
        }
        return $data;
    } 

    /**
     * DATABASE::getRow()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function getRow($sql) {
        $this->start_time = getmicrotime();
        $this->_do_global_sql($sql);
        $rs = $this->conn->Execute($sql);
        if ($rs) $data = $rs->FetchObj($sql);
        $this->_debug_sql($sql, "getRow");
        return $data;
    }

    /**
     * DATABASE::query()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function query($sql) {
        $this->start_time = getmicrotime();
        $this->_do_global_sql($sql);
        $rs = $this->conn->Execute($sql);
        $this->_debug_sql($sql, "query");
        return $data;
    }

    /**
     * DATABASE::GetOne()
     * 
     * @param  $sql 
     * @return array $data
     * @access publis
     */
    function GetOne($sql) {
        $this->start_time = getmicrotime();
        $this->_do_global_sql($sql);
        $data = $this->conn->GetOne($sql);
        $this->_debug_sql($sql, "GetOne");
        return $data;
    } 

    /**
     * DATABASE::setFetchMode()
     * 
     * @param  $F 
     * @return NULL 
     * @access publis
     */
    function setFetchMode ($F) {
        //$this->conn->setFetchMode($F);
        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    } 

    /**
     * DATABASE::_debug_sql()
     * 
     * @param  $sql 
     * @return NULL 
     * @access private 
     */
    function _debug_sql ($sql, $F) {
        if ($_GET["sqldebug"] == "query") {
            if ($this->end_time) {
                $prev_time = round(($this->start_time - $this->end_time), 5);
                $this->waste += $prev_time;
            } 
            $this->end_time = getmicrotime();
            $ex_time = round(($this->end_time - $this->start_time), 5);
            $this->total_time += $ex_time;
            $this->sqldebug .= "___________<br /><b>" . $this->sqlNo . ".</b> " . $F . ": " . $sql . "<br /> Query executed for: " . $ex_time . " s. Previews query was executed " . (($prev_time)?$prev_time:0) . " s. ago<br />";
            $this->sqlNo++;
        } 
    }
    
    /**
     * DATABASE::_do_global_sql()
     * 
     * @param  $sql 
     * @return NULL 
     * @access private 
     */
    function _do_global_sql ($sql) {
        global $__SQL;
        $__SQL = $sql;
    }
} 

?>