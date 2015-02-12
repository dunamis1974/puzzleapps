<?php
/**
 * Include and create/load platform object
 */

include_once ($COREROOT . "core/platform.class.php");

$__PLATFORM = new PLATFORM();

$CURRENTPLATFORM = $__PLATFORM->LoadPlatform();
$PLATFORMID = $CURRENTPLATFORM->id;

?>