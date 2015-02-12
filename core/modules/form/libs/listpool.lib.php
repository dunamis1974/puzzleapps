<?php

function build_listpool ($name, $data, $val = 0) {
    global $CURRENTPLATFORM;
    $OBJECTS = $CURRENTPLATFORM->getAllOfType($name);
    $_SECIMG = "<select name=\"{$name}\" class=\"input\" id=\"{$name}\">\n";
    $_SECIMG .= "<option></option>\n";
    
    foreach ($OBJECTS AS $O) {
        $TXT = $O->translate_object_data();
        if ($TXT[$data]) {
            $_SECIMG .= "<option value='{$O->id}'" . (($val == $O->id)?" selected='selected'":"") . ">##{$TXT[$data]}##</option>\n";
        }
    }
        
    $_SECIMG .= "</select>";
    
    
    return $_SECIMG;
}

function validate_listpool ($value) {
    global $RUNNINGNDIR;
    
    return 0;
}

?>