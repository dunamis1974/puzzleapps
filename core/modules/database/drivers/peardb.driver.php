<?php

require_once 'DB.php';

/**
 * DATABASE
 * This class extends PEAR::DB
 * This is debug hack, nothing more.
 * 
 * @package Puzzle Apps
 * @author DuNaMiS 
 * @copyright Copyright (c) 2004
 * @version 0.1
 * @access public 
 */
class DATABASE extends DB {
    /**
     * DATABASE::DATABASE()
     * 
     * @param  $dsn 
     * @return NULL 
     */
    function DATABASE ($dsn) {
        $this->start_time = getmicrotime();
        $this->_do_global_sql("Initiate DB connection!");
        $this->conn = DB::connect($dsn);
        $sql = "SET NAMES 'UTF8'";
        $this->conn->query($sql);
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
        $data = $this->conn->getAll($sql);
        $this->_debug_sql($sql, "getAll");
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
        $data = $this->conn->getRow($sql);
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
        $data = $this->conn->query($sql);
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
        $this->conn->setFetchMode($F);
    }

    /**
     * DATABASE::_debug_sql()
     * 
     * @param  $sql 
     * @return NULL 
     * @access private 
     */
    function _debug_sql ($sql, $F) {
        global $__SQL;
        $__SQL = $sql;
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