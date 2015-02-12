<?php

$MODULEDIR = $this->MODULEDIR;

// Include Universal Form Validator
include_once ($MODULEDIR . "drivers/ufv.driver.php");

if (! is_object($GLOBALS["UFV"]))
    $GLOBALS["UFV"] = new Validator();
    
// Include form builder class
include_once ($MODULEDIR . "drivers/formbuilder.driver.php");

if (! is_object($GLOBALS["doFORM"]))
    $GLOBALS["doFORM"] = new FormBuilder();

?>
