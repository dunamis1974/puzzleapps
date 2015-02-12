<table align="center" width="500" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="item">Basic tables are imported</td>
    <td align="left"><?= ((importXML2SQL())?$okImg:$failedImg); ?></td>
</tr>
<tr>
    <td class="item">Databse configuration is saved</td>
    <td align="left"><?= ((writeDBdata())?$okImg:$failedImg); ?></td>
</tr>
</table>

<center>
<br />Click next to continue!<br />
</center>


<?

function writeDBdata () {
    global $error;
    $_DBCONF = "../config/conf.d/database.conf.php";
    if ($fp = fopen($_DBCONF, "w")) {
        $_DATA .= "<?\n\n";
        $_DATA .= "\$DB_DRIVER = \"" . $_POST["driver"] . "\";\n";
        if ($_POST["host"]) $_DATA .= "\$DB_HOST = \"" . $_POST["host"] . "\";\n";
        if ($_POST["database"]) $_DATA .= "\$DB_DATABASE = \"" . $_POST["database"] . "\";\n";
        if ($_POST["dbuser"]) $_DATA .= "\$DB_USERNAME = \"" . $_POST["dbuser"] . "\";\n";
        if ($_POST["dbpass"]) $_DATA .= "\$DB_PASSWORD = \"" . $_POST["dbpass"] . "\";\n";
        
        $_DATA .= "\n?>";
        fwrite($fp, $_DATA);
        fclose ($fp);
        return true;
    }
    $error = true;
    return false;
}

function importXML2SQL () {
    global
        $COREROOT,
        $RUNNINGNDIR;
    
    require($COREROOT . "modules/database/adodb/adodb-xmlschema.inc.php");
    
    $driver = $_POST["driver"];
    $host = $_POST["host"];
    $dbuser = $_POST["dbuser"];
    $dbpass = $_POST["dbpass"];
    $database = $_POST["database"];
    
    if ($driver == "sqlite") $host = $RUNNINGNDIR . $database;
    
    $db = ADONewConnection($driver);
    $db->Connect($host, $dbuser, $dbpass, $database);
    $schema = new adoSchema($db);
    $sql = $schema->ParseSchema($COREROOT . "sql/structure.xml");
    if ($schema->ExecuteSchema()) return true;
    $error = true;
    return false;
}

?>