<?php

/**
 * SQLITE Language fixes
 *
 *
 * @package Puzzle Apps
 * @author DuNaMiS
 * @copyright Copyright (c) 2004
 * @version $Id: sqlite.driver.php,v 1.3 2006/06/14 19:29:06 bobby Exp $
 * @access public
 */

define("TQT", "\"");
define("FQT", "'");
define("OQT", "\"");

function SQL_fixNames ($O) {
    $_O = (array)$O;
    $_ANAMES = array_keys($_O);
    $end = count($_ANAMES);
    for ($i = 0; $i < $end; $i++) {
        $_name = explode(".", $_ANAMES[$i]);
        $name = (($_name[1])?$_name[1]:$_name[0]);
        $O_->$name = $_O[$_ANAMES[$i]];
    }
    return $O_;
}

function SQL_addlimit (&$SQL, $limit, $offset) {
    $sql = $sql . " LIMIT $offset, $limit";
}

function escape_sql ($sql){
    return sqlite_escape_string($sql);
}

?>