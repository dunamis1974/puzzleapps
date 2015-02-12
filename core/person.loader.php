<?php
/**
 * Include and create/load user object
 */

include_once ($COREROOT . "core/person.class.php");

$__PERSON = new PERSON();

$CURRENTUSER = $__PERSON->PERSONLOAD();

?>