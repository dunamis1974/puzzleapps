<?php
/**
 * This is puzzle wrapper module
 * Here we are colectiong methods and classes that
 * help to integrate external applications
 */

function wrapp_puzzle_capture ($buffer) {
    $GLOBALS["WRAPPER_BODY"] .= $buffer;
}

?>