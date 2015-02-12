<?

global $COREROOT, $RUNNINGNDIR, $__ADMIN, $_DEBUG;

$MODULEDIR = $this->MODULEDIR;

function filter_htmltidy ($html) {
    global $_TRANSFORMTARGET, $CURRENTENCODING, $CURRENTUSER, $_DEBUG;

    if ($CURRENTUSER->isAllowed("edit") || ($CURRENTUSER->isAllowed("delete")) || ($CURRENTUSER->isAllowed("move")) || $_GET["skiptidy"])
        return $html;

    if (class_exists("tidy")) {
        $config = array(
           //"clean" => true,
           "drop-proprietary-attributes" => true,
           "alt-text" => "image",
           "indent"=> false,
           "wrap" => 200,
        );
        if ($_TRANSFORMTARGET == "xml")
            $config["output-xhtml"] = true;
        else
            $config["output-html"]  = true;

        $tidy = new tidy;
        $tidy->parseString($html, $config, "utf8");
        $tidy->cleanRepair();
        
        //echo "-------------------" . $tidy->errorBuffer . "-------------------";
        if ($_GET["debug"] == "tidy")
            $_DEBUG = nl2br($tidy->errorBuffer);
            
        $html = tidy_get_output($tidy);
    }
    
    return $html;
}

?>