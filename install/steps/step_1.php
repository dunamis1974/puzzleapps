<?php
$checks = array();
$checks[] = array("PHP version >= 8.5", PHP_VERSION_ID >= 80500, PHP_VERSION);
$checks[] = array("PDO extension", extension_loaded("PDO"), extension_loaded("PDO") ? "Available" : "Not available");
$availableDrivers = extension_loaded("PDO") ? PDO::getAvailableDrivers() : array();
$supportedDrivers = array_values(array_intersect(array("mysql", "sqlite", "pgsql"), $availableDrivers));
$checks[] = array("Supported PDO database drivers", count($supportedDrivers) > 0, count($supportedDrivers) ? implode(", ", $supportedDrivers) : "None available; install a supported PDO driver");
$checks[] = array("SimpleXML", extension_loaded("simplexml"), extension_loaded("simplexml") ? "Available" : "Not available; fresh-install schema generation will not work");
$checks[] = array("XSL support", extension_loaded("xsl"), extension_loaded("xsl") ? "Available" : "Not available");
$checks[] = array("Zlib compression", extension_loaded("zlib"), extension_loaded("zlib") ? "Available" : "Not available");
$checks[] = array("File uploads", (bool)get_cfg_var("file_uploads"), get_cfg_var("file_uploads") ? "Available (max " . ini_get("upload_max_filesize") . ")" : "Disabled");
$checks[] = array("Server API", PHP_SAPI !== "cgi", PHP_SAPI);

$writablePaths = array(
    "../files" => "File uploads",
    "../log" => "Logging",
    "../css" => "CSS editing",
    "../images" => "Image editing",
    "../xslt" => "XSLT editing",
    "../tmp" => "Temporary files",
    "../config/conf.d" => "Platform configuration",
    "../core/config/conf.d" => "Database configuration",
);
foreach ($writablePaths as $path => $purpose) {
    $fullPath = __DIR__ . "/" . $path;
    $checks[] = array("Writable " . $path, is_dir($fullPath) && is_writable($fullPath), $purpose);
}
?>
<table align="center" width="500" cellspacing="0" cellpadding="3" class="">
<tr><td class="title" colspan="2">Check for Requirements</td></tr>
<?php foreach ($checks as $check): ?>
<tr>
    <td class="item"><?= htmlspecialchars($check[0], ENT_QUOTES, "UTF-8"); ?></td>
    <td align="left"><?= installerCheckResult($check[1], $check[2], $okImg, $failedImg); ?></td>
</tr>
<?php endforeach; ?>
</table>
<p>Warnings do not block installation. Resolve any unmet requirement before depending on that feature.</p>
<?php
function installerCheckResult($passed, $detail, $okImg, $failedImg) {
    $safeDetail = htmlspecialchars((string)$detail, ENT_QUOTES, "UTF-8");
    if ($passed) {
        return '<b class="ok">' . $okImg . '</b><span class="item"> (' . $safeDetail . ')</span>';
    }
    return '<b class="error">' . $failedImg . '</b><span class="error"> Warning: ' . $safeDetail . '</span>';
}
?>
