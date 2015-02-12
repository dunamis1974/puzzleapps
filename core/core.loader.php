<?php
// Load permissions class
include_once ($COREROOT . "core/permissions.class.php");

// Make CORE object
include_once ($COREROOT . "core/core.class.php");
$CORE = new CORE();

// Load crypting class
include_once ($COREROOT . "core/crypto.class.php");

// Load translation class
include_once ($COREROOT . "core/translate.class.php");
$TRANSLATE = new Translate();

// Load current platform
include_once ($COREROOT . "platform.loader.php");

// Load user
include_once ($COREROOT . "person.loader.php");

// Load other objects
include_once ($COREROOT . "core/file.class.php");

// Load data class
include_once ($COREROOT . "core/data.class.php");
$DATA = new DATA();

?>