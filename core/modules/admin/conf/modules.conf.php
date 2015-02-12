<?php

/**
 * If you have modules that you need to be added
 * to administration area you can add them to this array
 *
 * @version $Id: modules.conf.php,v 1.2 2005/10/29 11:46:08 bobby Exp $ 
 *
 */

$_ADMIN_FILE = $GLOBALS["COREROOT"] . "modules/admin/conf/modadmin.conf.php";
include_once($_ADMIN_FILE);
/**
 * Load modules configuration
 */
//$_mod = array_keys($_ADMN_MOD);
for ($i = 0; $i < count($_ADMN_MOD); $i++) {
    $_MODULE_CONFIG = $GLOBALS["COREROOT"] . "modules/" . $_ADMN_MOD[$i] . "/config/" . $_ADMN_MOD[$i] . ".conf.php";
    if (file_exists($_MODULE_CONFIG)) include_once($_MODULE_CONFIG);
    $_MODULE_ADMIN = $GLOBALS["COREROOT"] . "modules/" . $_ADMN_MOD[$i] . "/admin/admin.php";
    if (file_exists($_MODULE_ADMIN)) include_once($_MODULE_ADMIN);
}

?>