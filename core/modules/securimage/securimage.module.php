<?php

global $COREROOT, $RUNNINGNDIR, $__ADMIN, $_SECIMG, $_SECFIELD;

$_SECIMG = "<img src=\"/admin/securimage/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>\" id=\"secimg\" /><br />";

$_SECFIELD = "<input type=\"text\" name=\"seccode\" size=\"5\" class=\"val\" id=\"seccode\" />";
$_SECFIELD .= "<a href=\"securimage/securimage_play.php\"><img src=\"images/16x16/sound.png\" border=\"0\" align=\"top\" hspace=\"3\" /></a>";
$_SECFIELD .= " <a href=\"#\" onclick=\"document.getElementById('secimg').src = 'securimage/securimage_show.php?sid=' + Math.random(); return false;\"><img src=\"./images/16x16/reload.png\" border=\"0\" align=\"top\" /></a>";

?>