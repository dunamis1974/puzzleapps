<?php
/**
 * Web start this file loads core and request data.class execution
 */

$COREROOT = dirname(__FILE__) . DIRECTORY_SEPARATOR;

include_once ($COREROOT . "loader.php");

if (! $__ADMIN && empty($_GET["admin"])) {
    // Get body start time
    $time_body_start = getmicrotime();
    
    // Fixing GET[id] test for numeric
    if (! is_numeric($_GET["id"]))
        unset($_GET["id"]);
    
    echo $DATA->DataColector();
    
    // Get end time
    $time_end = getmicrotime();
    
    if ($_GET["debug"] == "time") {
        $body_time = round($time_end - $time_body_start, 4);
        
        $total_time = round($time_end - $time_start, 4);
        
        $load_time = round($time_body_start - $time_start, 4);
        // sql time
        $data_time = round($time_xml_end - $time_xml_start, 4);
        // XSLT Preparation time
        $xslt_time = round($time_prep_end - $time_xml_end, 4);
        // Transformation time
        $transform_time = round($time_end - $time_prep_end, 4);
        echo "<br /><br /><div style=\"background-color:#E5E5E5; padding:5; font-size: 10px; font-family:Verdana, Arial, Helvetica, sans-serif;\">";
        echo "<center>Site created in: $total_time s.<br />";
        echo "Scripts loaded in: $load_time s.<br />";
        echo "Site body created in: $body_time s.<br />";
        echo "------------------------------------------------<br />";
        echo "Data colected in: $data_time s.<br />";
        echo "XSLT prepared in: $xslt_time s.<br />";
        echo "Data transformed in: $transform_time s.</center>";
        echo "</div><br />";
    }
    if ($_GET["sqldebug"] == "query") {
        echo "<div style=\"background-color:#E5E5E5; padding:5; font-size: 10px; font-family:Verdana, Arial, Helvetica, sans-serif;\">";
        echo "<br />This is the list of all sql queries executed to build current page!<br />";
        echo "____________________________________________________________________<br />";
        echo $DB->sqldebug;
        echo "____________________________________________________________________<br />";
        echo "Total number of queries: " . ($DB->sqlNo - 1) . "<br />";
        echo "Total execution time: " . $DB->total_time . " s.<br />";
        echo "Total wasted time (between queries): " . $DB->waste . " s.";
        echo "</div><br />";
    }
    if ($_DEBUG) {
        echo "<div style=\"background-color:#E5E5E5; padding:5; font-size: 10px; font-family:Verdana, Arial, Helvetica, sans-serif;\">";
        echo "<br />Requested debug:<br />";
        echo "____________________________________________________________________<br />";
        echo $_DEBUG . "<br />";
        echo "____________________________________________________________________<br />";
        echo "</div><br />";
    
    }
} else {
    Modules::LoadModule("admin");
}
?>