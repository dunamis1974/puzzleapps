<?php
/**
 * Preload modules.
 * Can be used for shopping cart etc...
 */

if ($PRELOADMODULES) {
    foreach ($PRELOADMODULES as $__MODULE) {
        $_PREMOD[$__MODULE] = $_MOD_FOO->LoadModule($__MODULE);
    }
}
?>