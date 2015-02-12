<?php

global $DTDDRIVER, $DTD;

// take driver option.
if (!$DTDDRIVER) $DTDDRIVER = "xmldtd";

// Include proper driver
$this->LoadDriver($DTDDRIVER);

if (!is_object($DTD)) $DTD = new ODDs;

?>