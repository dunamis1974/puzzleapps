<?php

/**
 * FormBuilder
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 */
class FormBuilder
{

    public $WORKFLOW = null;

    public $NOEDIT = null;

    public $DTD = null;

    public $DTDN = null;

    public $HIDE = null;

    public $array = null;

    public $WIDTHIGNORE = null;

    public $stylecount = 0;

    public $_FORM;

    public $ADVANCEDADD = false;
    
    public $MULTILANG = true;

    /**
     * FormBuilder::start()
     * 
     * @param $dtd
     * @param boolean $val
     * @param array $HIDE
     * @return 
     * @access public 
     */
    function start($odd, $val = false, $HIDE = array())
    {
        global $DTD, $_TRANSFORMTARGET;
        /*
        echo "<pre>";
        print_r($DTD->dtdlist);
        echo "</pre>";
        */
        $FORM = new FormBuilder();
        
        if (isset($this->WORKFLOW)) {
            $FORM->WORKFLOW = $this->WORKFLOW;
            unset($this->WORKFLOW);
        }
        
        if (isset($this->array)) {
            $FORM->array = $this->array;
            unset($this->array);
        }
        
        $FORM->DTD = $DTD->get_odd($odd);
        $FORM->DTDN = $odd;
        $FORM->HIDE = $HIDE;
        $FORM->NOEDIT = $this->NOEDIT;
        $FORM->MULTILANG = $DTD->odd_multilang($odd);
        $FORM->ADVANCEDADD = $this->ADVANCEDADD;
        $FORM->TAGEND = ($_TRANSFORMTARGET == "xml")?" /":"";
        $FORM->load($val);
        
        $FORM->body();
        
        return $FORM->_FORM;
    }

    /**
     * FormBuilder::body()
     * 
     * @return 
     * @access private 
     */
    function body()
    {
        global $EDITLANGUAGE, $DEFAULTLANGUAGE;

        if (!$this->MULTILANG) {
            $EDITLANGUAGE = $DEFAULTLANGUAGE;
        }
        
        $end = count($this->DTD);
        
        if ($this->array) {
            $this->arr_ = $this->array . "[";
            $this->_arr = "]";
        }
        
        $this->_security();
        for($i = 0; $i < $end; $i++) {
            if ($this->NOEDIT) {
                $field = "_lang";
            } elseif ((!$this->DTD[$i]["attributes"]["lang"]) && ($EDITLANGUAGE != $DEFAULTLANGUAGE) && ($this->DTD[$i]["data"][1]["data"] != "title")) {
                $field = "_lang";
            } else {
                $field = "_" . $this->DTD[$i]["data"][1]["data"];
            }
            
            if (method_exists($this, $field))
                $this->$field($i);
        }
        
        if ($this->ADVANCEDADD == true) {
            $this->_add_top_bottom();
        }
    }

    /**
     * Add security value to the form
     *
     * FormBuilder::_security()
     *
     * @param  $i
     * @return
     * @access private
     */
    function _security()
    {
        global $_EXTRA_SECURITY;
        
        if (!$_EXTRA_SECURITY)
            return;

        $this->_FORM .= "<div class=\"formhidden\">\n";
        $this->_FORM .= "<input type=\"hidden\" name=\"__secval__\" value=\"" . $this->form_sec_val() . "\"{$this->TAGEND}>\n";
        $this->_FORM .= "</div>\n";
    }

    /**
     * Create security value
     *
     * FormBuilder::form_sec_val()
     *
     * @param  $i
     * @return
     * @access public
     */
    function form_sec_val()
    {
        global $_GENERAL_SEED;
        $CRYPT = new DOCRYPT($_GENERAL_SEED);
        $text = time() . "|" . $_SERVER["SERVER_NAME"];
        $test_value = $CRYPT->enc($text);
        $_SESSION["FORM_SEC"] = $test_value;
        
        return $test_value;
    }

    /**
     * Other language field
     *
     * FormBuilder::_lang()
     *
     * @param  $i
     * @return
     * @access private
     */
    function _lang($i)
    {
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if (ereg("cmsimage", $field["validate"]))
            $this->values[$this->DTD[$i]["element_name"]] = substr($this->values[$this->DTD[$i]["element_name"]], 0, strpos($this->values[$this->DTD[$i]["element_name"]], "."));
        
        $this->_FORM .= "<div class=\"formrow\">\n";
        $this->_FORM .= "<span class=\"col1form\"><div class=\"" . $this->mandatory . "\">##" . $field["formfield"] . "##</div></span>\n";
        $this->_FORM .= "<div class=\"lang__\">" . $this->values[$this->DTD[$i]["element_name"]] . "</div>\n";
        $this->_FORM .= "<input type=\"hidden\" class=\"input\" name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" value=\"" . $this->values[$this->DTD[$i]["element_name"]] . "\"{$this->TAGEND}>\n";
        if ($field["description"])
            $this->_FORM .= "<div class=\"_description\">##" . $field["description"] . "##</div>";
        $this->_FORM .= "</div>\n";
    }

    /**
     * FormBuilder::_hidden()
     *
     * @param  $i
     * @return
     * @access private 
     */
    function _hidden($i)
    {
        $field = $this->parseField($this->DTD[$i]["data"]);
        $this->_FORM .= "<div class=\"formhidden\">\n";
        $this->_FORM .= "<input type=\"" . $this->DTD[$i]["data"][1]["data"] . "\" class=\"input\" name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $field["validate"] . $this->_arr . "\" value=\"" . $this->values[$this->DTD[$i]["element_name"]] . "\"{$this->TAGEND}>\n";
        $this->_FORM .= "</div>\n";
    }

    /**
     * FormBuilder::_submit()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _submit($i)
    {
        $this->_FORM .= "<div class=\"formrow\" id=\"f_submit\">\n";
        $this->_FORM .= "<div class=\"col1button\"><input type=\"submit\" value=\"##" . $this->DTD[$i]["data"][0]["attributes"]["submit"] . "##\" class=\"button\"{$this->TAGEND}></div>\n";
        $this->_FORM .= "<div class=\"col2button\">\n";
        if ($this->DTD[$i]["data"][0]["attributes"]["close"] == 1) {
            $this->_FORM .= "<input type=\"button\" onclick=\"javascript:self.close();\" value=\"##" . $this->DTD[$i]["data"][0]["attributes"]["cancel"] . "##\" class=\"button\"{$this->TAGEND}>\n";
        } else {
            $this->_FORM .= "<input type=\"button\" onclick=\"javascript:history.go(-1);\" value=\"##" . $this->DTD[$i]["data"][0]["attributes"]["cancel"] . "##\" class=\"button\"{$this->TAGEND}>\n";
        }
        $this->_FORM .= "</div></div>\n";
    }

    /**
     * FormBuilder::_title()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _title($i)
    {
        $field = $this->parseField($this->DTD[$i]["data"]);
        $this->_FORM .= "<div class=\"formtitle\" id=\"f_title{$i}\">\n";
        $this->_FORM .= "##" . $field["formfield"] . "##\n";
        $this->_FORM .= "</div>\n";
    }

    /**
     * FormBuilder::_textarea()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _textarea($i)
    {
        global $SYSTEMIMAGES;
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
            //
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        
        $this->_FORM .= "<div class=\"col2form\"><textarea class=\"textarea\" " . $this->_attributes($i) . " name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" id=\"" . $this->DTD[$i]["element_name"] . "\">" . $this->values[$this->DTD[$i]["element_name"]] . "</textarea>\n";
        
        if ($this->values_primary) {
            $this->_FORM .= "<img id=\"img_" . $i . "\" src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" onclick=\"ShowHide('" . $i . "');\">\n";
            $this->_FORM .= "<div id=\"data_" . $i . "\" style=\"display:none; width:500\" class=\"formprimarylang\">" . $this->values_primary[$this->DTD[$i]["element_name"]] . "</div>";
        }
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_htmlarea()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _htmlarea($i)
    {
        global $_TINYMCEINIT, $SYSTEMIMAGES;
        
        if (! $this->htmlarea) {
            //$this->htmlarea = Modules::LoadModule("xinha");
            $this->htmlarea = Modules::LoadModule("tinymce");
            $this->_FORM .= $_TINYMCEINIT;
        }
        
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        
        $this->_FORM .= "<div class=\"col2form\"><textarea class=\"mceEditor\" " . $this->_attributes($i) . " name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" id=\"" . $this->DTD[$i]["element_name"] . "\">" . $this->values[$this->DTD[$i]["element_name"]] . "</textarea>\n";
        $this->_FORM .= "<a href=\"javascript:toggleEditor('" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "');\">[##Add/Remove editor##]</a>";
        
        if ($this->values_primary) {
            $this->_FORM .= "<img align=\"right\" id=\"img_" . $i . "\" src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" onclick=\"ShowHide('" . $i . "');\">\n";
            $this->_FORM .= "<div id=\"data_" . $i . "\" style=\"display:none; width:500\" class=\"formprimarylang\">" . $this->values_primary[$this->DTD[$i]["element_name"]] . "</div>";
        }
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_file()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _file($i)
    {
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\"><input type=\"checkbox\" name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" value=\"" . (($this->values[$this->DTD[$i]["element_name"]])?$this->values[$this->DTD[$i]["element_name"]]:1) . "\"" . (($this->values[$this->DTD[$i]["element_name"]])?" checked":"") . ">&nbsp;<input type=\"file\" " . $this->_attributes($i) . " class=\"input\" name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" value=\"\"{$this->TAGEND}>\n";
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_text()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _text($i)
    {
        global $SYSTEMIMAGES;
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\"><input type=\"text\" " . $this->_attributes($i) . " class=\"input\" name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" value=\"" . $this->values[$this->DTD[$i]["element_name"]] . "\"{$this->TAGEND}>\n";
        
        if ($this->values_primary) {
            $this->_FORM .= "<img  id=\"img_" . $i . "\" src=\"" . $SYSTEMIMAGES . "16x16/plus.png\" onclick=\"ShowHide('" . $i . "');\">\n";
            $this->_FORM .= "<div id=\"data_" . $i . "\" style=\"display:none; width:500\" class=\"formprimarylang\">" . $this->values_primary[$this->DTD[$i]["element_name"]] . "</div>";
        }
        
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_password()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _password($i)
    {
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        // type password
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\"><input type=\"password\" " . $this->_attributes($i) . " class=\"input\" name=\"{$this->arr_}" . $this->DTD[$i]["element_name"] . "{$this->arr_}\" value=\"\"{$this->TAGEND}></div>\n";
        $this->_FORM .= "</div>\n";
        // retype password
        if (! $this->array) {
            $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_re_{$this->DTD[$i]["element_name"]}\">\n";
            $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
            $this->_FORM .= "<div class=\"col2form\"><input type=\"password\" " . $this->_attributes($i) . " class=\"input\" name=\"re_" . $this->DTD[$i]["element_name"] . "\" value=\"\"{$this->TAGEND}></div>\n";
            $this->_FORM .= "</div>\n";
        }
        $this->_FORM .= "<input type=\"hidden\" class=\"input\" name=\"_old_" . $this->DTD[$i]["element_name"] . "\" value=\"" . $this->values[$this->DTD[$i]["element_name"]] . "\"{$this->TAGEND}>\n";
    }

    /**
     * FormBuilder::_time()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _time($i)
    {
        global $DATE;
        
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        
        if (method_exists($DATE, "drop_time")) {
            $DATE->drop_time($this->DTD[$i]["value"], $key[$i], $this->req);
        } else {
            $this->_FORM .= "Missing function 'drop_time'!";
        }
        
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_lock_form()
     * 
     * @return 
     * @access private 
     */
    
    function _lock_form()
    {
        $lock_var = md5(time());
        $this->_FORM .= "<input type=\"hidden\" class=\"input\" name=\"" . $this->arr_ . "lock_form" . $this->_arr . "\" value=\"" . $lock_var . "\"{$this->TAGEND}>\n";
    }

    /**
     * FormBuilder::_date()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _date($i)
    {
        $_loaded = $this->_loadlib("calendar");
        if (! $_loaded)
            return;
        $field = $this->parseField($this->DTD[$i]["data"]);
        $this->Required($i);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $value = ($this->values[$this->DTD[$i]["element_name"]])?$this->values[$this->DTD[$i]["element_name"]]:$field["value"];
        $this->_FORM .= calendar($value, $field["params"]["type"], $field["field"]["period"], $this->DTD[$i]["element_name"], $this->req);
        $this->_FORM .= "{$description}</div></div>\n";
    }

	/**
     * FormBuilder::_library()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _library ($i)
    {
        $field = $this->parseField($this->DTD[$i]["data"]);
        $lib = str_replace(":", "", $field["validate"]);
        
        $this->Required($i);
        
        $_loaded = $this->_loadlib($lib);
        if (!$_loaded) {
            return;
        }
        
        $func = "build_{$lib}";
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $value = ($this->values[$this->DTD[$i]["element_name"]])?$this->values[$this->DTD[$i]["element_name"]]:$field["value"];
        $this->_FORM .= $func($this->DTD[$i]["element_name"], $field["libdata"], $value);
        $this->_FORM .= "{$description}</div></div>\n";
    }
    
    /**
     * FormBuilder::_dropdown()
     * 
     * @param  $i
     * @return
     * @access private
     */
    function _dropdown($i)
    {
        global $_FORM_IMPLODE_STRING;
        
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        if (strstr($this->_attributes($i), "multiple")) {
            $multiple = true;
            $mpdata = explode($_FORM_IMPLODE_STRING, $this->values[$this->DTD[$i]["element_name"]]);
        }
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $this->_FORM .= "<select " . $this->_attributes($i) . " name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . (($multiple)?":m[]":"") . "\" class=\"input\">\n";
        $this->_FORM .= "<option></option>\n";
        $drops = explode("|", $field["drop"]);
        //$text = $this->DTD[$i]["text"];
        $dnum = count($drops);
        
        for($d = 0; $d < $dnum; $d++) {
            $indrops = explode(":", $drops[$d]);
            if ($indrops[1] == null)
                $indrops[1] = $indrops[0];
            $this->_FORM .= "<option value='$indrops[1]'";
            if ($multiple && in_array($indrops[1], $mpdata)) {
                $this->_FORM .= " selected='selected'";
            } else if (("$indrops[1]" == $this->values[$this->DTD[$i]["element_name"]])) {
                $this->_FORM .= " selected='selected'";
            }
            $this->_FORM .= ">##" . $indrops[0] . "##</option>\n";
        }
        
        $this->_FORM .= "</select>";
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_add_top_bottom()
     * 
     * @return NULL 
     * @access private 
     */
    function _add_top_bottom()
    {
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_top_bottom\">\n";
        $this->_FORM .= "<div class=\"col1form non_mandatory__\">##Object position##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $this->_FORM .= "<select name=\"_add_on_top\" class=\"input\">\n";
        $this->_FORM .= "<option value='1'" . (($GLOBALS["ADDOBJECTONTOP"])?" selected='selected'":"") . ">##Add on top##</option>\n";
        $this->_FORM .= "<option value='-1'" . ((! $GLOBALS["ADDOBJECTONTOP"])?" selected='selected'":"") . ">##Add to the bottom##</option>\n";
        $this->_FORM .= "<option value='sa'" . (($GLOBALS["SORTOBJECTS"])?" selected='selected'":"") . ">##Sort Alphabetically##</option>\n";
        $this->_FORM .= "</select>";
        $this->_FORM .= "</div></div>\n";
    }

    /**
     * FormBuilder::_module()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _module($i)
    {
        global $MODULES;
        
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_module\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $this->_FORM .= "<select " . $this->_attributes($i) . " name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" class=\"input\">\n";
        $this->_FORM .= "<option value=\"NULL\"></option>\n";
        
        $key = array_keys((array)$MODULES);
        $num = count($key);
        for($d = 0; $d < $num; $d++) {
            $this->_FORM .= "<option value='" . $key[$d] . "'";
            if ($key[$d] == $this->values[$this->DTD[$i]["element_name"]]) {
                $this->_FORM .= " selected='selected'";
            }
            $this->_FORM .= ">##" . $MODULES[$key[$d]]["title"] . "##</option>\n";
        }
        
        $this->_FORM .= "</select>";
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_objects()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _objects($i)
    {
        global $_OBJECTCONTROLS;
        
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">##" . $field["formfield"] . "##</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $this->_FORM .= "<select " . $this->_attributes($i) . " multiple size=\"10\"  style=\"width: 200px;\" name=\"" . $this->DTD[$i]["element_name"] . "[]\" class=\"input\">\n";
        $key = array_keys($_OBJECTCONTROLS);
        $num = count($key);
        $this->values[$this->DTD[$i]["element_name"]] = (array)$this->values[$this->DTD[$i]["element_name"]];
        for($d = 0; $d < $num; $d++) {
            $this->_FORM .= "<option value='" . $key[$d] . "'";
            if (in_array($key[$d], $this->values[$this->DTD[$i]["element_name"]])) {
                $this->_FORM .= " selected='selected'";
            }
            $this->_FORM .= ">##" . $key[$d] . "##</option>\n";
        }
        
        $this->_FORM .= "</select>";
        $this->_FORM .= "{$description}</div></div>\n";
    }

    /**
     * FormBuilder::_checkbox()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _checkbox($i)
    {
        $this->Required($i);
        
        if ($this->values[$this->DTD[$i]["element_name"]])
            $checked = " checked"; else
            $checked = "";
        
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">{$description}</div>\n";
        $this->_FORM .= "<div class=\"col2form\">";
        $this->_FORM .= "<nobr><input type=\"checkbox\" " . $this->_attributes($i) . " name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $this->_arr . "\" value=\"true\"{$checked}{$this->TAGEND}>\n";
        $this->_FORM .= "&nbsp;<span class=\"" . $this->mandatory . "\">##" . $field["formfield"] . "##</span></nobr>\n";
        $this->_FORM .= "</div></div>\n";
    }

    /**
     * FormBuilder::_radio()
     * 
     * @param  $i 
     * @return 
     * @access private 
     */
    function _radio($i)
    {
        $this->Required($i);
        $field = $this->parseField($this->DTD[$i]["data"]);
        
        if ($field["description"])
            $description .= "<img src=\"./admin/images/16x16/messagebox_info.png\" alt=\"i\" id=\"hint-{$i}\" class=\"hint\" title=\"##Hint##:##" . $field["description"] . "##\" />&nbsp;";
        
        $this->_FORM .= "<div class=\"formrow " . $this->rowStyle() . "\" id=\"f_{$this->DTD[$i]["element_name"]}\">\n";
        
        if ($field["quiz"]) {
            $titles = split(":", $field["formfield"]);
            if ($titles[2]) {
                $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">{$description}##" . $titles[0] . "##</div>\n";
            } else {
                $titles[2] = $titles[1];
                $titles[1] = $titles[0];
            }
            $this->_FORM .= "<div class=\"col2form\">";
            $this->_FORM .= "<table cellpadding=\"2\" cellspacing=\"0\">";
            if ($field["top"]) {
                $this->_FORM .= "<tr><td align=\"right\">&nbsp;</td>";
                for($q = 1; $q <= $field["quiz"]; $q++)
                    $this->_FORM .= "<td width=\"10\" align=\"center\">" . $q . "</td>";
                $this->_FORM .= "<td align=\"left\">&nbsp;</td></tr>";
            }
            $this->_FORM .= "<tr>";
            $this->_FORM .= "<td align=\"right\">##" . $titles[1] . "##</td>";
            for($q = 0; $q < $field["quiz"]; $q++) {
                $checked = ((($q + 1 == $this->values[$this->DTD[$i]["element_name"]]))?"checked":"");
                $this->_FORM .= "<td width=\"10\" align=\"center\"><input type=\"radio\" " . $this->_attributes($i) . " class=\"input\" name=\"" . $this->DTD[$i]["element_name"] . $field["validate"] . $this->req . "\" value=\"" . ($q + 1) . "\"{$checked}{$this->TAGEND}></td>";
            }
            $this->_FORM .= "<td align=\"left\">##" . $titles[2] . "##</td></tr>";
            $this->_FORM .= "</table>\n";
            $this->_FORM .= "</div>";
        } else {
            $this->_FORM .= "<div class=\"col1form {$this->mandatory}\">{$description}</div>\n";
            $this->_FORM .= "<div class=\"col2form\">";
            $this->_FORM .= "<input type=\"radio\" " . $this->_attributes($i) . " name=\"" . $this->arr_ . "" . $this->DTD[$i]["element_name"] . $field["validate"] . $this->req . $this->_arr . "\" value=\"true\"{$checked}{$this->TAGEND}>\n";
            $this->_FORM .= "&nbsp;<span class=\"" . $this->mandatory . "\">##" . $field["formfield"] . "##</span>\n";
            $this->_FORM .= "</div>";
        }
        $this->_FORM .= "</div>\n";
    }

    /**
     * Function that will load values into form
     * 
     * FormBuilder::load()
     * 
     * @param  $val 
     * @return 
     * @access private 
     */
    function load($val)
    {
        /**
         * This is the easiast way
         * $this->values = $val;
         * But can't handle languages
         */
        for($i = 0; $i < count($this->DTD); $i++) {
            $this->values[$this->DTD[$i]["element_name"]] = $val[$this->DTD[$i]["element_name"]];
            if ($val["_primary"] && $this->MULTILANG) {
                $this->values_primary[$this->DTD[$i]["element_name"]] = $val["_primary"][$this->DTD[$i]["element_name"]];
                if (! $this->DTD[$i]["attributes"]["lang"]) {
                    $this->values[$this->DTD[$i]["element_name"]] = $val["_primary"][$this->DTD[$i]["element_name"]];
                }
            }
        }
        return true;
    }

    /**
     * FormBuilder::rowStyle()
     * 
     * @return 
     * @access private 
     */
    function rowStyle()
    {
        $this->stylecount++;
        if (($this->stylecount == 2)) {
            $this->style = "even__";
            $this->stylecount = 0;
        } else {
            $this->style = "odd__";
        }
        
        return $this->style;
    }

    /**
     * FormBuilder::Required()
     * 
     * @param  $i 
     * @return null
     * @access private 
     */
    function Required($i)
    {
        if ((bool)$this->DTD[$i][attributes]["reqired"] == "true") {
            $this->mandatory = "mandatory__";
            // $this->req = ":required";
        } else {
            $this->mandatory = "non_mandatory__";
            // $this->req = "";
        }
    }

    /**
     * FormBuilder::_attributes()
     * 
     * @param  $id 
     * @return $attr
     * @access private 
     */
    function _attributes($id)
    {
        if (! is_array($this->DTD[$id]["data"][1]["attributes"]))
            return null;
        
        $key = array_keys($this->DTD[$id]["data"][1]["attributes"]);
        
        for($i = 0; $i < count($key); $i++) {
            $attr .= " " . $key[$i] . "=\"" . $this->DTD[$id]["data"][1]["attributes"][$key[$i]] . "\"";
        }
        return $attr;
    }

    /**
     * FormBuilder::parseField()
     * 
     * @param  $data 
     * @return 
     * @access private 
     */
    function parseField($data)
    {
        $end = count($data);
        for($i = 0; $i < $end; $i++) {
            if (($data[$i]["element_name"] == "validate") && ($data[$i]["data"] != ''))
                $data[$i]["data"] = ":" . $data[$i]["data"];
            $var[$data[$i]["element_name"]] = $data[$i]["data"];
            if ($data[$i]["attributes"]) {
                $var[$data[$i]["element_name"]] = array_merge((array)$var[$data[$i]["element_name"]], $data[$i]["attributes"]);
            }
        }
        
        return $var;
    }

    /**
     * FormBuilder::cancel_uri()
     * 
     * @param unknown $get_vars
     * @param integer $less
     * @return 
     * @access public 
     */
    function cancel_uri($get_vars = null, $less = 1)
    {
        $get_vars = $get_vars;
        $query = explode("&", $_SERVER["QUERY_STRING"]);
        $num = count($query);
        $num -= $less;
        $new_uri = "?";
        for($i = 0; $i < $num; $i++) {
            if ($i != 0) {
                $new_uri .= "&";
            }
            $new_uri .= $query[$i];
        }
        return $new_uri;
    }

    /**
     * FormBuilder::hide_get()
     * 
     * @param boolean $get 
     * @return 
     * @access public 
     */
    function hide_get($get = false)
    {
        if (! $get)
            $get = $_SERVER["REQUEST_URI"];
        $data_ = explode("?", $get);
        $val_ = explode("&", $data_[1]);
        for($i = 0; $i < count($val_); $i++) {
            list($key, $val) = explode("=", $val_[$i]);
            $this->_FORM .= "<input type=\"hidden\" name=\"$key\" value=\"$val\"{$this->TAGEND}>\n";
        }
    }

    /**
     * FormBuilder::_loadlib()
     *
     * @param string $lib 
     * @return 
     * @access private
     */
    function _loadlib($lib)
    {
        global $COREROOT;
        
        $FILE = $COREROOT . "modules/form/libs/" . $lib . ".lib.php";
        if (file_exists($FILE))
            include_once ($FILE); else
            return false;
        return true;
    }
}

?>