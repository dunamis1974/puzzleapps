<?php

/**
 * Validator
 *
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public
 */
class Validator
{

    public $validated;

    public $post_vars;

    public $data;

    public $err;

    public $lng;

    /**
     * Main Function
     * Validator::validate()
     *
     * @param unknown $post_vars
     * @return
     */
    function validate($post_vars = null, $object)
    {
        global $DTD, $_EXTRA_SECURITY;

        $this->DTDN = $object;
        $this->DTD = $DTD->get_odd($this->DTDN);

        if (!$post_vars) {
            $this->post_vars = $_POST;
        } else {
            $this->post_vars = $post_vars;
        }
        
        if ($_EXTRA_SECURITY) {
            $sec_val = $this->sec_val($this->post_vars["__secval__"]);
    
            if ($sec_val) {
                $this->err[] = "__secval__:{$sec_val}";
            }
    
            unset($this->post_vars["__secval__"]);
        }
        $key = array_keys($this->post_vars);
        $val = array_values($this->post_vars);
        $num = count($this->post_vars);
        for($i = 0; $i < $num; $i++) {

            unset($this->data);

            if (($key[$i] != "redir") && (! is_array($post_vars[$key[$i]]))) {
                $val[$i] = ereg_replace('\\\"', "\"", $val[$i]);
                $val[$i] = ereg_replace("\\\'", "'", $val[$i]);
            }

            $this->data = $this->getValidation($key[$i]);
            $num_d = count($this->data);

            if ($num_d > 0) {

                if (($this->data[0] == "_old_password") && ($val[$i] != '') && (! $this->validated["password"])) {
                    $this->validated["password"] = $val[$i];
                }

                for($n = 0; $n < $num_d; $n++) {
                    // have to think on easy validation expanding
                    if ($this->data[$n] == "cmsimage") {
                        $val[$i] = FILES::imageName($val[$i]);
                    }

                    if ($this->data[$n] == "file") {
                        $_FILENAME = FILES::uploadInObject($key[$i]);
                        if ($_FILENAME)
                            $val[$i] = $_FILENAME["name"];
                    }
                    if ($this->data[$n] == "noharm") {
                        $val[$i] = $this->noharm($val[$i]);
                    }
                    if ($this->data[$n] == "time") {
                        $val[$i] = $this->time_change($this->data[0], $this->post_vars[$this->data[2]], $this->post_vars[$this->data[3]]);
                    }
                    /*
                    if ($this->data[$n] == "date") {
                        $val[$i] = $this->date_change($this->data[0], $this->data[2], $this->post_vars[$this->data[3]], $this->post_vars[$this->data[4]], $this->post_vars[$this->data[5]], $this->post_vars[$this->data[6]], $this->post_vars[$this->data[7]]);
                    }
                    */
                    if ($this->data[$n] == "serialize") {
                        $val[$i] = $this->serial_val($val[$i]);
                    }
                    if ($this->data[$n] == "implode") {
                        $val[$i] = $this->implode_val($val[$i]);
                    }
                    if ($this->data[$n] == "password") {
                        if ($val[$i] == $val[$i+1]) {
                            $val[$i] = PERSON::hashPassword($val[$i]);
                            $_passkey = $this->data[0];
                            unset($val[$i+1]);
                            $this->validated[$key[$i]] = $val[$i];
                            $i++;
                        } else {
                            $this->err[] = $key[$i] . ":8:" . $this->lng;
                            $this->validated[$key[$i]] = "";
                            $this->validated[$key[$i+1]] = "";
                            $i++;
                        }
                    }
                    $this->data[$n] = $this->case_($this->data[$n], $val[$i]);
                    if (($this->data[$n] != 0) && ($this->data[$n] != '')) {
                        $this->err[] = $key[$i] . ":" . $this->data[$n] . ":" . $this->lng;
                    }
                }
            }

            $this->validated[$key[$i]] = $val[$i];
        }

        return $this->validated;
    }

    /**
     * This function is reading the ODD data and creates
     * a validation array for the given field
     *
     * @param $name
     * @return array $validate
     */
    private function getValidation ($name)
    {
        $validate = array();

        foreach ($this->DTD AS $field) {
            $this->fields[$field["element_name"]] = $field["data"][0]["data"];

            if ($field["element_name"] == $name) {
                foreach ($field["data"] AS $data) {
                    if ($data["element_name"] == "validate") {
                        $validate = explode(":", $data["data"]);
                        break;
                    }
                }
                if ($field["attributes"]["reqired"]) {
                    $validate[] = "required";
                }
            }
        }

        return $validate;
    }

    /**
     * Returns boolean true if there were errors
     * during the validation, and false if all was ok.
     *
     * Validator::hasErrors()
     *
     * @return boolean
     */
    function hasErrors()
    {
        if (isset($this->err)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the array of arrays with errorcodes
     * and field names that had errors.
     *
     * Validator::getErrors()
     *
     * @param integer $what
     * @return
     */
    function getErrors($what = 0)
    {
        if ($what == 0) {
            return $this->err;
        } else {
            $num = count($this->err);
            if ($num) {
                $ERR_ .= "<div class=\"block_error_\">
                <table>
                <tr>
                <td width=\"50\" align=\"center\"><span class=\"error_title__\">!</span></td>
                <td><nobr>
                ";
                for($i = 0; $i < $num; $i++) {
                    $error = $this->err[$i];
                    $this->data = explode(":", $error);
                    $this->lng = $this->data[2];
                    $ERR_ .= "<span class=\"error__\"><li>";
                    $ERR_ .= $this->err_code($this->data[1], $this->data[0]);
                    $ERR_ .= "</span>";
                }
                $ERR_ .= "</nobr></td></tr></table></div><p />";
            }
            return $ERR_;
        }
    }

    // Returns the array of validated
    // and renamed to the proper names values.
    function getValidated()
    {
    }

    // Returns initial not validated array.
    function getNotValidated()
    {
    }

    /**
     * This function takes Referer
     * and redirect you there.
     *
     * Validator::redirectToReferer()
     *
     * @return
     */
    function redirectToReferer()
    {
        $reff = $GLOBALS["HTTP_REFERER"];
        $num = count($this->validated);
        if ($num) {
            $key_v = array_keys($this->validated);
            $val_v = array_values($this->validated);
            for($i = 0; $i < $num; $i++) {
                $post .= "&" . $key_v[$i] . "=" . $val_v[$i];
            }
        }
        header("Location: $reff$post");
    }

    /**
     * This function takes redir POST value
     * and redirect you there.
     *
     * Validator::redirectToNew()
     *
     * @param string $new_loc
     * @return
     */
    function redirectToNew($new_loc = "")
    {
        if (isset($this->validated[redir])) {
            $new_loc = $this->validated[redir];
        }
        if ($new_loc) {
            $location = "http://" . $_SERVER["HTTP_HOST"] . "$new_loc";
            header("Location: $location");
        }
    }

    /**
     * This function is setting the verified POST_VARS
     * and make them global
     * Validator::setGlobals()
     *
     * @return
     */
    function setGlobals()
    {
        $num = count($this->validated);
        if ($num) {
            $key_v = array_keys($this->validated);
            $val_v = array_values($this->validated);
            for($i = 0; $i < $num; $i++) {
                global $$key_v[$i];
                $$key_v[$i] = $val_v[$i];
            }
        }
    }

    /**
     * You can use this function for to take the referer GET_VARS
     * and use them now.
     * Validator::refGetVars()
     *
     * @return
     */
    function refGetVars()
    {
        $reff = $GLOBALS["HTTP_REFERER"];
        $reff = parse_url($reff);
        $query = $reff[query];
        $reff_data = explode("&", $query);
        $num = count($reff_data);
        if ($num) {
            for($i = 0; $i < $num; $i++) {
                $data_r = explode("=", $reff_data[$i]);
                global $$data_r[0];
                $$data_r[0] = $data_r[1];
            }
        }
    }

    /**
     * Switch Function
     *
     * Validator::case_()
     *
     * @param  $case
     * @param  $val
     * @return
     */
    function case_($case, $val)
    {
        if (eregi("max", $case) || eregi("min", $case)) {
            $case_old = $case;
            $case = substr($case, 0, 3);
        }

        switch ($case) {
            // case "serialize": $data_ = $this->serial_val($val); break;
            case "email":
                $data_ = $this->mail_val($val);
                break;
            case "www":
                $data_ = $this->www_val($val);
                break;
            case "number":
                $data_ = $this->number($val);
                break;
            case "text":
                $data_ = $this->text_val($val);
                break;
            case "required":
                $data_ = $this->notNull_val($val);
                break;
            case "min":
                $data_ = $this->min_val($val, $case_old);
                break;
            case "max":
                $data_ = $this->max_val($val, $case_old);
                break;
            case "date":
                $data_ = $this->date_val($this->data[0], $this->data[2], $this->post_vars[$this->data[3]], $this->post_vars[$this->data[4]], $this->post_vars[$this->data[5]], $this->post_vars[$this->data[6]], $this->post_vars[$this->data[7]]);
                break;
            case "time":
                $data_ = $this->time_val($this->data[0], $this->post_vars[$this->data[2]], $this->post_vars[$this->data[3]]);
                break;
            default:
                if ($case != '') {
                    $data_ = $this->lib_val($case, $val);
                } else {
                    $data_ = 0;
                }
                break;
        }
        return $data_;
    }

    /**
     * Validator::err_code()
     *
     * @param  $code
     * @return
     */
    function err_code($code, $field)
    {
        switch ($code) {
            case 1:
                return $field . " ##is not a valide e-mail address##";
                break;
            case 2:
                return "##The string## '##" . $this->fields[$field] . "##' ##must contain characters from a to z and numbers from 0 to 9##";
                break;
            case 3:
                return "##Field## '##" . $this->fields[$field] . "##' ##can not be empty##";
                break;
            case 4:
                return "##Field## '##" . $this->fields[$field] . "##' ## must contain at least## $this->lng ##characters##";
                break;
            case 5:
                return "##Field## '##" . $this->fields[$field] . "##' ## must contain not more than## $this->lng ##characters##";
                break;
            case 6:
                return "##Field## '##" . $this->fields[$field] . "##' ## must be a web site www.*.*##";
                break;
            case 7:
                return "##Field## '##" . $this->fields[$field] . "##' ## must contain numbers from 0 to 9##";
                break;
            case 8:
                return "##Data entered in## '" . $this->fields[$field] . "' ##is not corect##";
                break;
            case 9:
                return "##Please enter corect zip value in## '##" . $this->fields[$field] . "##'";
                break;
            case 10:
                return "##Please enter corect time value## '##" . $this->fields[$field] . "##'";
                break;
            case 11:
                return "##Your data was not submited! Please try again!##";
                break;
            case 12:
                return "##Please enter corect value in## '##" . $this->fields[$field] . "##'";
                break;
            default:
                $this->data_ = $code;
                break;
        }
        return true;
    }

    /**
     * Validator::sec_val()
     *
     * @param  $value
     * @return
     */
    function sec_val($value)
    {
        global $_GENERAL_SEED, $_IGNORE_SEC;

        if ($_IGNORE_SEC)
            return 0;

        if (trim($value) == '')
            return 11;

        $CRYPT = new DOCRYPT($_GENERAL_SEED);
        $text = $CRYPT->enc($_SESSION["FORM_SEC"], 0);
        unset($_SESSION["FORM_SEC"]);

        $text2 = $CRYPT->enc($value, 0);

        list($time, $server) = explode("|", $text);
        list($time2, $server2) = explode("|", $text2);

        if (($server == $server2) && ($time == $time2) && ($server == $_SERVER["SERVER_NAME"]) && ($time > (time() - 7200))) {
            $key = 0;
        } else {
            $key = 11;
        }
        return $key;
    }
    
    /**
     * @param $lib
     * @param $val
     * @return unknown_type
     */
    function lib_val ($lib, $value) {
        
        $_loaded = $this->_loadlib($lib);
        
        if (!$_loaded) {
            return 0;
        }
        
        $func = "validate_{$lib}";
        $key = $func($value);
        
        return $key;
    }

    /**
     * Validator::noharm()
     *
     * @param  $value
     * @return
     */
    function noharm($value)
    {
        $value = stripslashes($value);
        $value = htmlentities($value);
        return $value;
    }

    /**
     * Validator::mail_val()
     *
     * @param  $value
     * @return
     */
    function mail_val($value)
    {
        if (trim($value) != "") {
            if (eregi("^[A-Z0-9._%-]+@[A-Z0-9-]+(\.[A-Z0-9-]+)*(\.[A-Z]{2,4})$", $value)) {
                $key = 0;
            } else {
                $key = 1;
            }
        }
        return $key;
    }

    /**
     * Validator::www_val()
     *
     * @param  $value
     * @return
     */
    function www_val($value)
    {
        // name:email:notNull...
        if (trim($value) != "") {
            $UrlPtn = "^[_a-z0-9-]+(\.[_a-z0-9-]+)+[\.a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
            if (ereg($UrlPtn, $value)) {
                // '/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\//i'
                // $UrlPtn  = "(http:|mailto:|https:|ftp:|gopher:|news:)" ."([^ \\/\"\']*\\/)*[^ \\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";
                $key = 0;
            } else {
                $key = 6;
            }
        }
        return $key;
    }

    /**
     * Validator::text_val()
     *
     * @param  $value
     * @return
     */
    function text_val($value)
    {
        // name:text:notNull:...
        //ereg('[A-Za-z0-9-]', $value)
        if (trim($value) != "") {
            if (true) {
                $key = 0;
            } else {
                $key = 2;
            }
        }
        return $key;
    }

    /**
     * Validator::number()
     *
     * @param  $value
     * @return
     */
    function number($value)
    {
        // name:text:notNull:...
        $valid_chars = "0123456789";
        for($i = 0; $i < strlen($value); $i++) {
            if (strchr($valid_chars, substr($value, $i, 1)) == true) {
                $key = 0;
            } else {
                $key = 7;
                break;
            }
        }
        return $key;
    }

    /**
     * Validator::notNull_val()
     *
     * @param  $value
     * @return
     */
    function notNull_val($value)
    {
        // name:notNull:...
        if (($value != '') && ($value != - 1)) { // -1 is used for required dates.
            $key = 0;
        } else {
            $key = 3;
        }
        return $key;
    }

    /**
     * name:min6:...
     *
     * Validator::min_val()
     *
     * @param  $value
     * @param  $case
     * @return
     */
    function min_val($value, $case)
    {
        if (trim($value) != "") {
            $lng = substr($case, 3);
            if (strlen($value) >= $lng) {
                $key = 0;
            } else {
                $key = 4;
            }
            $this->lng = $lng;
        }
        return $key;
    }

    /**
     * name:max60:...
     *
     * Validator::max_val()
     *
     * @param  $value
     * @param  $case
     * @return
     */
    function max_val($value, $case)
    {
        if (trim($value) != "") {
            $lng = substr($case, 3);
            if (strlen($value) <= $lng) {
                $key = 0;
            } else {
                $key = 5;
            }
            $this->lng = $lng;
        }
        return $key;
    }

    /**
     * Validator::date_val()
     *
     * @param  $field
     * @param  $type
     * @param  $yyyy
     * @param  $mm
     * @param  $dd
     * @return
     */
    function date_val($field, $type, $yyyy, $mm, $dd)
    {
        if ((trim($yyyy) != "") && (trim($mm) != "") && (trim($dd) != "")) {
            if (checkdate($mm, $dd, $yyyy)) {
                $key = 0;
            } else {
                $key = 8;
            }
        } else {
            $key = 0;
        }
        return $key;
    }

    /**
     * Validator::time_val()
     *
     * @param  $field
     * @param  $hh
     * @param  $mm
     * @return
     */
    function time_val($field, $hh, $mm)
    {
        $field = $field;
        if ((trim($mm) != "") && (trim($hh) != "")) {
            if (($hh <= 24) && ($mm < 60)) {
                $key = 0;
            } else {
                $key = 10;
            }
        } else {
            $key = 0;
        }
        return $key;
    }

    /**
     * Modification functions
     * Used to modify some variables before validation
     *
     * Validator::date_change()
     *
     * @param  $field
     * @param  $type
     * @param  $yyyy
     * @param  $mm
     * @param  $dd
     * @return
     */
    function date_change($field, $type, $yyyy, $mm, $dd)
    {
        $field = $field;
        $type = $type;
        if ((trim($yyyy) != "") && (trim($mm) != "") && (trim($dd) != "")) {
            $this_date = "$yyyy:$mm:$dd";
            if (checkdate($mm, $dd, $yyyy)) {
                if ($this->data[2] == "unix") {
                    list($yyyy, $mm, $dd) = split(':', $this_date);
                    $this_date = mktime(0, 0, 0, $mm, $dd, $yyyy);
                }
            }
        } else {
            $this_date = - 1;
        }
        unset($this->validated[$this->data[3]]);
        unset($this->validated[$this->data[4]]);
        unset($this->validated[$this->data[5]]);
        unset($this->validated[$this->data[6]]);
        unset($this->validated[$this->data[7]]);
        // unset($this->post_vars[$this->data[3]]); unset($this->post_vars[$this->data[4]]); unset($this->post_vars[$this->data[5]]); unset($this->post_vars[$this->data[6]]); unset($this->post_vars[$this->data[7]]);
        return $this_date;
    }

    /**
     * Validator::time_change()
     *
     * @param  $field
     * @param  $hh
     * @param  $mm
     * @return
     */
    function time_change($field, $hh, $mm)
    {
        $field = $field;
        if ((trim($hh) != "") && (trim($mm) != "")) {
            $this_time = "$hh:$mm";
        } else {
            $this_time = "00:00";
        }
        unset($this->validated[$this->data[2]], $this->validated[$this->data[3]]);
        // unset($this->post_vars[$this->data[3]]); unset($this->post_vars[$this->data[4]]); unset($this->post_vars[$this->data[5]]); unset($this->post_vars[$this->data[6]]); unset($this->post_vars[$this->data[7]]);
        return $this_time;
    }

    /**
     * Validator::serial_val()
     *
     * @param  $val
     * @return
     */
    function serial_val($val)
    {
        $data = explode(":", $val);
        for($i = 0; $i < count($data); $i++) {
            $val_[$data[$i]] = $this->validated[$data[$i]];
            unset($this->validated[$data[$i]]);
        }
        $value = serialize($val_);

        return $value;
    }

    /**
     * Validator::serial_val()
     *
     * @param  $val
     * @return
     */
    function implode_val($val)
    {
        global $_FORM_IMPLODE_STRING;

        $value = implode($_FORM_IMPLODE_STRING, $val);

        return $value;
    }

    /**
     * Validator::make_unix()
     *
     */
    function make_unix($date, $h = 0, $m = 0)
    {
        list($yyyy, $mm, $dd) = split('[:-]', $date);

        if (! $dd || ! $mm || ! $yyyy)
            return $date;

        $date_ = mktime($h, $m, 0, $mm, $dd, $yyyy);
        return $date_;
    }

    /**
     * Validator::make_readible()
     *
     */
    function make_readible($date)
    {
        if (($date != - 1) && (trim($date) != '')) {
            if (eregi("-", $date)) {
                list($yyyy, $mm, $dd) = split('[:-]', $date);
                if (! $dd) {
                    $date = date("Y-m-d", $date);
                }
                list($yyyy, $mm, $dd) = split('[:-]', $date);
                $date_ = date("M j, Y", mktime(0, 0, 0, $mm, $dd, $yyyy));
            } else {
                $date_ = date("M j, Y", $date);
            }
        }
        return $date_;
    }
    
    /**
     * Validator::_loadlib()
     *
     * @param string $lib 
     * @return 
     * @access private
     */
    function _loadlib ($lib)
    {
        global $COREROOT;
        
        $FILE = $COREROOT . "modules/form/libs/" . $lib . ".lib.php";
        
        if (file_exists($FILE)) {
            include_once($FILE);
        } else {
            return false;
        }
        
        return true;
    }
}

?>