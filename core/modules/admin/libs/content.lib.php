<?php
if ($_GET["load"]) {
    include($THISDIR . $_GET["load"] . ".lib.php");
} else {
    $_BODY .= DoMenu("content");
}
?>