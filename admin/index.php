<?php
if (PHP_VERSION_ID < 80500 || !extension_loaded("pdo")) {
    http_response_code(500);
    echo "This application requires PHP 8.5 or later and the PDO extension.";
    exit;
}

$__ADMIN = true;
include_once("../core/start.php");


