<?php
require_once $COREROOT . "modules/database/DatabaseEnv.php";
$environmentConfig = null;
$environmentError = null;
try {
    $environmentConfig = databaseConfigFromEnvironment();
} catch (DatabaseException $exception) {
    $environmentError = $exception->getMessage();
}
$availableDrivers = extension_loaded("PDO") ? PDO::getAvailableDrivers() : array();
$supportedDrivers = array_values(array_intersect(array("mysql", "sqlite", "pgsql"), $availableDrivers));
?>
<table align="center" width="500" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="title" colspan="2">Write database configuration</td>
</tr>
<?php if ($environmentError !== null): $error = true; ?>
<tr>
    <td class="item" colspan="2"><span class="error"><?= htmlspecialchars($environmentError, ENT_QUOTES, "UTF-8"); ?></span></td>
</tr>
<?php elseif ($environmentConfig !== null): ?>
<tr>
    <td class="item" colspan="2">
        The database is configured through DB_* environment variables. Continue to the next step to create the schema.<br />
        Driver: <?= htmlspecialchars($environmentConfig["driver"], ENT_QUOTES, "UTF-8"); ?><br />
        <?php if ($environmentConfig["driver"] === "sqlite"): ?>
        Database: <?= htmlspecialchars($environmentConfig["sqlite_path"], ENT_QUOTES, "UTF-8"); ?>
        <?php else: ?>
        Host: <?= htmlspecialchars($environmentConfig["host"], ENT_QUOTES, "UTF-8"); ?><br />
        Port: <?= htmlspecialchars((string)$environmentConfig["port"], ENT_QUOTES, "UTF-8"); ?><br />
        Database: <?= htmlspecialchars($environmentConfig["database"], ENT_QUOTES, "UTF-8"); ?>
        <?php endif; ?>
    </td>
</tr>
<?php else: ?>
<tr>
    <td class="item" colspan="2">
        Select a PDO database driver available in this PHP installation and enter its connection settings.<br />
        The database and user must already exist. For SQLite, provide the database file path (or :memory:).
    </td>
</tr>
<tr>
    <td class="item" valign="top" align="right">Database driver</td>
    <td align="left">
        <?php if (!$supportedDrivers): ?>
            <span class="error">No supported PDO driver is available. Install pdo_mysql, pdo_sqlite or pdo_pgsql and reload.</span>
        <?php else: ?>
            <?php foreach ($supportedDrivers as $index => $driver): ?>
                <label><input type="radio" name="driver" value="<?= htmlspecialchars($driver, ENT_QUOTES, "UTF-8"); ?>"<?= $index === 0 ? " checked" : ""; ?> /> <?= $driver === "mysql" ? "MySQL" : ($driver === "pgsql" ? "PostgreSQL" : "SQLite"); ?></label><br />
            <?php endforeach; ?>
        <?php endif; ?>
    </td>
</tr>
<tr><td align="right" class="item">Host</td><td><input type="text" name="host" /></td></tr>
<tr><td align="right" class="item">Port</td><td><input type="text" name="port" /></td></tr>
<tr><td align="right" class="item">Database name</td><td><input type="text" name="database" /></td></tr>
<tr><td align="right" class="item">Database user</td><td><input type="text" name="user" /></td></tr>
<tr><td align="right" class="item">Database password</td><td><input type="password" name="password" /></td></tr>
<tr><td align="right" class="item">SQLite path</td><td><input type="text" name="sqlite_path" /></td></tr>
<tr><td align="right" class="item">Character set</td><td><input type="text" name="charset" value="" placeholder="Driver default" /></td></tr>
<?php endif; ?>
</table>
