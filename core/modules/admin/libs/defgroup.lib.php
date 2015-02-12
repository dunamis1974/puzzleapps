<?php
/**
 * This script will help you to set the default
 *
 * 1. platform group
 * 2. platform permissions
 *
 */

$_BODY .= "<center><br /><br />
<b>##Default platform group##</b><br />
<i>##NOTE: This is default group.<br />This group os loaded if user is not logged##</i>
<br />\n";
$_BODY .= $CURRENTPLATFORM->groupsForm($CURRENTPLATFORM);
$_BODY .= "\n</center>";

$_BODY .= "<center><br /><br /><br />
<b>##Default platform permissions##</b><br />
<i>##NOTE: This are default pemissions matrix for default group##</i>
<br />\n";
$_BODY .= $CURRENTPLATFORM->permissionsForm($CURRENTPLATFORM);
$_BODY .= "\n</center>
<br />";

?>