<?php
/**
 * @version $Id: paginating.lib.php,v 1.2 2005/10/29 11:46:08 bobby Exp $ 
 */

global
    $_OBJECTSCOTAIN,
    $_ZONESPERMANENT,
    $_ZONES;


//print_r($_ZONES);
for ($i = 0; $i < count($_ZONESPERMANENT); $i++) {
    $xml .= $DATA->GetAllData();
}

$_BODY .= "<pre>" . htmlentities($xml) . "</pre>";

?>