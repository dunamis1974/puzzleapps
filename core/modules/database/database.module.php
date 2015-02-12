<?php
global
    $DB_DBAL,
    $DB_DRIVER,
    $DB_HOST,
    $DB_USERNAME,
    $DB_PASSWORD,
    $DB_DATABASE,
    $RUNNINGNDIR;

if (!$DB_DRIVER) $DB_DRIVER = "mysql";

if (!$DB_HOST) return;

$this->LoadDriver($DB_DBAL);
$this->LoadDriver($DB_DRIVER);

if ($DB_DRIVER == "sqlite") {
    $path = urlencode($RUNNINGNDIR . $DB_HOST); //urlencode
    $dsn = "$DB_DRIVER://$path";
} else {
    $dsn = "$DB_DRIVER://$DB_USERNAME:$DB_PASSWORD@$DB_HOST/$DB_DATABASE";
}
// $GLOBALS["DB"] = DB::connect($dsn);
$GLOBALS["DB"] = new DATABASE($dsn);

$GLOBALS["DB"]->setFetchMode(DB_FETCHMODE_OBJECT);

/**
 * getLastID()
 * 
 * @param  $table 
 * @param string $id 
 * @return number $lastID
 */
function getLastID($table, $id = "id") {
    return $GLOBALS["DB"]->GetOne("SELECT MAX($id) FROM $table");
} 

?>
