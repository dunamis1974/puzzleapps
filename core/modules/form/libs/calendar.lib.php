<?php

/**
 * This is Calendar date function
 *
 * $type = 2 - add hour. = 3 - hide dd/mm and and JS for make them 0, = 4 - same as 3 but no present
 * Make date values
 */

function calendar ($date = -1, $type = 1, $range = "-5|+5", $name = "date", $req = "")
{
    global $calendar;
    
    $type = $type;
    
    if (($date != - 1) && ($date != "")) {
        if ((eregi(":", $date) || eregi("-", $date)) && (strpos($date, "-") > 0)) {
            $this_date = $date;
        } else {
            if ($date == "today") {
                $this_date = date("Y-m-d");
            } else {
                $this_date = date("Y-m-d", $date);
            }
        }
        //list ($year, $month, $day) = split("[:-]", $this_date);
        $this_unix = Validator::make_unix($this_date);
    }
    
    //$this_year = date("Y");
    /**
     * Define Range
     */
    if ($range == "short") {
        $min = - 1;
        $max = 1;
    } else if ($range == "long") {
        $min = - 100;
        $max = 0;
    } else {
        list($min, $max) = explode("|", $range);
    }
    
    if ($this_unix && ((date("Y") + $min) <= (date("Y", $this_unix) <= (date("Y") + $max)))) {
        $date_txt = Validator::make_readible($this_date);
    } else {
        $date_txt = "##Select date## > ";
        $this_unix = time();
        $this_date = date("Y-m-d", $this_unix);
    }
    
    if ($req == "yes")
        $required = ":required";
    
    if (! $calendar) {
        $_BODY .= "
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"./admin/calendar/calendar-blue.css\" title=\"win2k-cold-1\" />
        <script type=\"text/javascript\" src=\"./admin/calendar/calendar.js\"></script>
        <script type=\"text/javascript\" src=\"./admin/calendar/calendar-en.js\"></script>
        <script type=\"text/javascript\" src=\"./admin/calendar/calendar-setup.js\"></script>
        ";
        $calendar = true;
    }
    
    $_BODY .= "
    <nobr>
    <span style=\"background-color: #FFFFC0; cursor: default;\" id=\"" . $name . "_show\">$date_txt</span>
    <input type=\"hidden\" name=\"" . $name . ":date:calendar$required\" id=\"" . $name . "_date\" value=\"$this_date\" />
    <img src=\"./images/date.gif\" id=\"" . $name . "_triger\" style=\"cursor: pointer;\" title=\"Date selector\" align=\"middle\" />
    </nobr>
    <script type=\"text/javascript\">
    Calendar.setup({
        inputField:\"" . $name . "_date\",
        ifFormat:\"%Y-%m-%d\",
        displayArea:\"" . $name . "_show\",
        daFormat:\"%b %d, %Y\",
        align:\"BR\",
        button:\"" . $name . "_triger\",
        singleClick:true,
        step:1,
        firstDay:1,
        date:" . (($this_date)?$this_date:date("Y-m-d")) . ",
        showOthers:true,
        range:[" . (date("Y") + $min) . "," . (date("Y") + $max) . "]
    });
    </script>
    ";
    return $_BODY;
}

?>