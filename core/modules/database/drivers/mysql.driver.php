<?php

/**
 * MYSQL Language fixes
 *
 *
 * @package Puzzle Apps
 * @author DuNaMiS
 * @copyright Copyright (c) 2004
 * @version $Id: mysql.driver.php,v 1.5 2006/06/15 12:33:24 bobby Exp $
 * @access public
 */

define("TQT", "`");
define("FQT", "'");
define("OQT", "`");

function SQL_fixNames ($O) {
    return $O;
}

function SQL_addlimit (&$SQL, $limit, $offset) {
    $sql = $sql . " LIMIT $offset, $limit";
}

function escape_sql ($sql){
    global $DB;
    // Use mysqli_real_escape_string with connection for proper escaping
    if (isset($DB) && isset($DB->conn) && isset($DB->conn->_connectionID) && $DB->conn->_connectionID) {
        return mysqli_real_escape_string($DB->conn->_connectionID, $sql);
    }
    // Fallback to basic escaping if no connection is available
    return addslashes($sql);
}

?>