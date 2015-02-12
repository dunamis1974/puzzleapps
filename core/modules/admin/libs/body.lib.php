<?php

$_BODY .= "<br />";
$THISDIR = dirname(__FILE__) . DIRECTORY_SEPARATOR;
if ($_GET["do"]) {
    include($THISDIR . "do.lib.php");
} else if ($_GET["custom"]) {
    $end = count($_GET["admin"]);
    $INCLUDE = $THISDIR . "../../" . $_GET["custom"] . "/admin/" . $_GET["admin"][($end - 1)] . ".lib.php";
    include($INCLUDE);
} else {
    $end = count($_GET["admin"]);
    if (file_exists($THISDIR . $_GET["admin"][($end - 1)] . ".lib.php")) {
        include($THISDIR . $_GET["admin"][($end - 1)] . ".lib.php");
    } else {
        include($THISDIR . "home.lib.php");
    }
}

?>