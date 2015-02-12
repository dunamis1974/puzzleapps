<?php

function error_handler($errno, $errstr, $errfile, $errline)
{
    
    global $err, $__SQL, $_GENERAL_ERROR_EMAIL;
    
    if ($__SQL) {
        $error_msg = "\n Query: $__SQL \n";
    }
    
    $date = date("r");
    
    $error_msg .= " $errstr occured in $errfile on $errline at $date";
    
    $email_addr = $_GENERAL_ERROR_EMAIL;
    
    $log_file = "./log.txt";
    
    if ($_SERVER["HTTP_HOST"] == "localhost") {
        $email = false;
        $display = true;
        $stdlog = false;
    } else {
        $email = ($_GENERAL_ERROR_EMAIL)?true:false;
        $stdlog = false;
    }
    
    $notify = true;
    $halt_script = true;
    
    switch ($errno) {
        case E_USER_NOTICE:
        case E_NOTICE:
            $halt_script = false;
            $error = 1;
            
            $type = "\nNotice:";
            
            break;
        
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_CORE_WARNING:
        case E_WARNING:
            $halt_script = false;
            $type = "\nWarning:";
            break;
        case E_USER_ERROR:
        case E_COMPILE_ERROR:
        case E_CORE_ERROR:
        case E_ERROR:
            $type = "\nFatal Error:";
            break;
        case E_PARSE:
            $type = "\nParse Error:";
            break;
        default:
            $type = "\nUnknown Error:";
            $halt_script = false;
            $display = false;
            $stdlog = false;
            break;
    }
    
    if (($notify) && ($error != 1)) {
        $body = "$type $error_msg\n\n";
        $body .= "REQUEST_URI:\n\t" . $_SERVER["REQUEST_URI"] . "";
        if ($_POST) {
            $body .= "\n\nPOST VARIABLES:\n";
            foreach (array_keys($_POST) as $__KEY) {
                $body .= "\t" . $__KEY . " = " . $_POST[$__KEY] . "\n";
            }
        }
        
        if ($_GET) {
            $body .= "\n\nGET VARIABLES:\n";
            foreach (array_keys($_GET) as $__KEY) {
                $body .= "\t" . $__KEY . " = " . $_GET[$__KEY] . "\n";
            }
        }
        
        if ($_FILES) {
            $body .= "\n\nFILE VARIABLES:\n";
            if (is_array($_FILES)) {
                foreach (array_keys($_FILES) as $__KEYFILE) {
                    $body .= "\t" . $__KEYFILE . " = " . $_FILES[$__KEYFILE]['name'] . " | size: " . $_FILES[$__KEYFILE]['size'];
                }
            }
        }
        
        if ($email) {
            error_log($body, 1, $email_addr);
        }
        
        if ($display) {
            echo nl2br($body);
        }
        
        if ($stdlog) {
            if ($log_file == "") {
                error_log($body, 0);
            } else {
                error_log($body, 3, $log_file);
            }
        }
        $err = 1;
    }
    
    if ($halt_script) {
        exit() - 1;
    }

}

$GLOBALS["_old_error_handler"] = set_error_handler("error_handler");

?>