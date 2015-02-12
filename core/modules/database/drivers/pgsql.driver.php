<?php

/**
 * PGSQL Language fixes
 *
 *
 * @package Puzzle Apps
 * @author DuNaMiS
 * @copyright Copyright (c) 2004
 * @version $Id: pgsql.driver.php,v 1.3 2006/06/14 19:29:06 bobby Exp $
 * @access public
 */

define("TQT", "\"");
define("FQT", "'");
define("OQT", "\"");

function SQL_fixNames ($O) {
    return $O;
}

function SQL_addlimit (&$SQL, $limit, $offset) {
    $sql = $sql . " LIMIT $limit OFFSET $offset";
}

function escape_sql ($sql){
    return pg_escape_string($sql);
}
?>