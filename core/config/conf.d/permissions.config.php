<?php
/*
 * Main permissions
 * 
 * Edit/Delete/Access etc....
 * They ca be assigned to groups and respectively to users
 * 
 */

$MAIN_PERMISSIONS = array(
    "admin" => array("link" => "Access administration"),
    "edit" => array("link" => "Edit objects"),
    "delete" => array("link" => "Delete objects"),
    "move" => array("link" => "Move objects"),
    "parent" => array("link" => "Change parent"),
    "add" => array("link" => "Add objects"),
    "chmod" => array("link" => "Change object permissions"),
    "chown" => array("link" => "Change object ownership"),
);

?>