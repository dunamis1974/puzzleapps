<?php

global $CURRENTPLATFORM, $_WEBSTAT, $_WEBSTATLOG;
extract($_GET);

if ($_WEBSTATLOG == true) {
    if (!is_object($_WEBSTAT)) $_WEBSTAT = Modules::LoadModule("webstat");
    ob_start();
    $_WEBSTAT->LoadLib("show");
    $_BODY .= ob_get_contents();
    ob_end_clean();
} else {
    $_BODY .= "<b>##Web statistic module not activated!##</b>";
}

?>