<?php
/**
 * Form2Mail module
 * @version $Id: form2mail.module.php,v 1.3 2006/05/25 07:33:00 bobby Exp $ 
 */
global
    $FORM2MAIL,
    $CORE,
    $DATA,
    $CURRENTPLATFORM,
    $CURRENTENCODING,
    $CURRENTUSER,
    $SYSTEMIMAGES,
    $doFORM,
    $UFV,
    $XML;

/**
 * __Form2Email()
 * 
 * @param  $DATA 
 * @return string $_BODY
 */
function __Form2Email ($DATA) {
    global
    $CURRENTPLATFORM,
    $FORM2MAIL,
    $CURRENTENCODING; 
    // Build msg. body
    $key = array_keys($DATA);
    $end = count($key);
    for ($i = 0; $i < $end; $i++) {
        $TEXT .= $key[$i] . ": " . $DATA[$key[$i]] . "\n";
    } 
    // Load mail module and send msg
    
    $_MAIL = Modules::LoadModule("smtp");
    
    $smtp = new SMTP($FORM2MAIL["smtp"], $FORM2MAIL["port"], false, 5);
    $smtp->auth($FORM2MAIL["smtpuser"], $FORM2MAIL["smtppass"]);
    $smtp->mail_from($FORM2MAIL["smtpuser"]);
    $smtp->send($FORM2MAIL["to"], $FORM2MAIL["subject"], $TEXT);

    $_BODY .= "##Your form was submited!##";

    return $_BODY;

    /**
     * if ($redirect) {
     *          echo "<script type=\"text/javascript\"> top.location.replace('index.php?id=$redirect'); </script>";
     * }
     */
} 

/**
 * __CreateForm()
 * 
 * @param array $data 
 * @param string $hide 
 * @return string $_BODY
 */
function __CreateForm($data = null, $hide = null) {
    global
    $FORM2MAIL,
    $doFORM,
    $_TRANSFORMTARGET;
    
    $TAGEND = ($_TRANSFORMTARGET == "xml")?" /":"";
    $_BODY .= "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\">\n";
    $_BODY .= "<input type=\"hidden\" name=\"_submitted\" value=\"1\"{$TAGEND}>";
    $_BODY .= $doFORM->start($FORM2MAIL["odd"], $data, $hide);
    $_BODY .= "<div class=\"formrow\">\n";
    $_BODY .= "<div class=\"col1button\">\n";
    $_BODY .= "<input type=\"submit\" value=\" ##Submit## \"{$TAGEND}>\n";
    $_BODY .= "</div><div class=\"col2button\">\n";
    $_BODY .= "<input type=\"reset\" value=\" ##Reset## \"{$TAGEND}>\n";
    $_BODY .= "</div></div>\n";
    $_BODY .= "</form>\n";

    return $_BODY;
}

if ($_POST) {
    $VALIDATED = $UFV->validate($_POST, $FORM2MAIL["odd"]);
} 

if ($UFV->hasErrors() || (!$_POST)) {
    $_BODY .= $UFV->getErrors(1);
    $_BODY .= __CreateForm($VALIDATED);
} else {
    $_BODY .= __Form2Email($VALIDATED);
}

if ($_FREETXT) $this->_TXT = $_FREETXT;
if ($_CAT) $this->_CAT = $_CAT;
if ($_BODY) $this->_BODY = $_BODY;

?>