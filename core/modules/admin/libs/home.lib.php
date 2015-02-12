<?php

$_BODY .= "<table width=\"100%\" border=\"0\"><tr><td width=\"60%\" valign=\"top\">";
$_BODY .= DoMenu("home");
$_BODY .= "</td>";


$_BODY .= "<td width=\"40%\" valign=\"top\" align=\"right\">";
include($THISDIR . "panels.lib.php");
$_BODY .= "</tr></table>";

?>