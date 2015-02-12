<?php
/**
 * XML
 * 
 * @package Puzzle Apps
 * @author UNKNOWN (fixed by Boyan Dzambazov)
 * @copyright Copyright (c) 2004
 * @version $Id: xml.driver.php,v 1.2 2005/10/29 11:46:09 bobby Exp $
 * @access public 
 */
 
class XML {
    var $x2a_array;
    var $x2a_depth = 1;
    var $indentstring = "    ";
    var $x2a_element_count;

    var $a2x_xml_string;
    var $a2x_depth;

    function open_element_handler($parser, $element_name, $attributes) {
        $this->tag_count++;
        $this->x2a_array[$this->x2a_depth] = &$this->x2a_array[$this->x2a_depth -1]['data'][];
        $this->x2a_array[$this->x2a_depth]['element_name'] = $element_name;
        $attributes and $this->x2a_array[$this->x2a_depth]['attributes'] = $attributes;
        $this->x2a_depth++;
    } 

    function close_element_handler($parser, $element_name) {
        $this->x2a_depth--;
        unset($this->x2a_array[$this->x2a_depth]);
    } 

    function character_data_handler($parser, $data) {
        $data = trim($data);
        /**
         * strip leading/trailing whitespace
         * does the array node allready contain text (multiline cdata)?
         */
        if ($data != '') {
            if ($this->x2a_array[$this->x2a_depth -1]['data']) {
                /**
                 * append the linebreak + text after the last line
                 */
                $this->x2a_array[$this->x2a_depth -1]['data'] .= "\n" . $data;
            } else {
                /**
                 * first line of cdata
                 */
                $this->x2a_array[$this->x2a_depth -1]['data'] = $data;
            } 
        } 
    } 

    function default_handler($parser, $data) {
        if (trim($data)) {
            preg_match_all('/ (\w+=".+")/U', $data, $matches);
            foreach($matches[1] as $match) {
                list($attribute_name, $attribute_value) = (explode('=', $match));
                $attribute_value = str_replace('"', '', $attribute_value);
                $this->x2a_array[0]['attributes'][$attribute_name] = $attribute_value;
            } 
        } 
    } 

    /*
     * function toArray(string $file) function is an XML abstraction
     * layer that can operate with:
     * 1. SimpleXML - fastest
     * 2. XMLReader
     * 3. xml_parser - slowest and some bugs with non latin letters
     */
    function toArray($file) {
        if (function_exists(simplexml_load_file)) {
            $xml = simplexml_load_file($file);
            $array[0]["data"][0]["element_name"] = "odd";
            $array[0]["data"][0]["data"] = $this->SimpleXML2array($xml);
        } else if (class_exists(XMLReader)) {
            $xml = new XMLReader();
            $xml->open($file);
            $array[0]["data"] = $this->XMLReader2array($xml);
            $xml->close();
        } else {
            unset($this->x2a_array);
            $data = file_get_contents($file);
            $array = $this->xml2array($data);
        }
        return $array;
    }
    
    function SimpleXML2array($xml) {
        $arr = array();
        $x = 0;
        foreach($xml as $a => $b) {
            $arr[$x] =  array();
            $arr[$x]['element_name'] = $a;
            
            $att = $b->attributes();       
            foreach($att as $c=>$d) {
                $arr[$x]['attributes'][$c] = (string)$d;
            }
            
            $children = $this->SimpleXML2array($b);
            if (!$children) {
                $arr[$x]['data'] = trim((string)$b);
            } else {
                $arr[$x]['data'] = $children;
            }
            $x++;
        }
        
        return $arr;
    }
    
    function XMLReader2array($xml)
    {
        $tree = null;
        while($xml->read())
            switch ($xml->nodeType) {
                case XMLReader::END_ELEMENT: return $tree;
                case XMLReader::ELEMENT:
                    $node = array('element_name' => $xml->name, 'data' => $xml->isEmptyElement ? '' : $this->XMLReader2array($xml));
                    if($xml->hasAttributes)
                        while($xml->moveToNextAttribute())
                            $node['attributes'][$xml->name] = $xml->value;
                    $tree[] = $node;
                break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $tree .= $xml->value;
            }
        return $tree;
    }

    function xml2array($xml) {
        unset($this->x2a_array);
        
        $this->parser = xml_parser_create();

        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'open_element_handler', 'close_element_handler');
        xml_set_character_data_handler($this->parser, 'character_data_handler');
        xml_set_default_handler($this->parser, 'default_handler');
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);

        if (!xml_parse($this->parser, $xml)) {
            die(sprintf('XML error: %s at line %d', xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser)));
        } 

        xml_parser_free($this->parser);

        return $this->x2a_array;
    } 

    function make_attribute_string($attributes) {
        if (is_array($attributes)) {
            foreach($attributes as $attribute_name => $attribute_value) {
                $attribute_string .= ' ' . $attribute_name . '="' . $attribute_value . '"';
            } 
        } 
        return $attribute_string;
    } 

    var $compact_xml;

    function array2xml($array, $compact_xml = false) {
        $this->compact_xml = $compact_xml;
        $this->a2x_xml_string = '<?xml version="1.0" encoding="utf-8"?>';
        $this->compact_xml or $this->a2x_xml_string .= "\n";
        $this->xml_output($array[0]['data']);
        return $this->a2x_xml_string;
    }

    function xml_output($sub_array) {
        foreach((array)$sub_array as $element) {
            $no_data_flag = $element['data'] == '' ? true : false; #Leeres Element? z.B.: <x id="2" />
            $multiline_flag = (!is_array($element['data']) && preg_match('/\n/', $element['data'])) ? true : false;
            $text_node_flag = !is_array($element['data']);

            $this->a2x_depth++;
            $this->compact_xml or $this->a2x_xml_string .= str_repeat($this->indentstring, $this->a2x_depth -1);

            $this->a2x_xml_string .= '<' . $element['element_name'] . $this->make_attribute_string($element['attributes']) . ($no_data_flag ? ' /' : '') . '>';
            if (!$text_node_flag && !$this->compact_xml) {
                $this->a2x_xml_string .= "\n";
            } 
            if ($no_data_flag && !$this->compact_xml) {
                $this->a2x_xml_string .= "\n";
            } 

            /**
             * text node output
             */
            if ($text_node_flag) {
                if ($multiline_flag && !$this->compact_xml) {
                    /**
                     * tab the text output
                     */
                    $this->a2x_xml_string .= "\n<![CDATA[";
                    $this->a2x_xml_string .= preg_replace('/^/m', str_repeat($this->indentstring, $this->a2x_depth), $element['data']);
                    $this->a2x_xml_string .= "]]>\n";
                } else {
                    $this->a2x_xml_string .= "<![CDATA[" . $element['data'] . "]]>";
                } 
            } 

            if (is_array($element['data'])) {
                $this->xml_output($element['data']);
            } 

            if ($multiline_flag && !$this->compact_xml) {
                $this->a2x_xml_string .= str_repeat($this->indentstring, $this->a2x_depth -1);
            } 

            if (!$no_data_flag) {
                if (!$text_node_flag && !$this->compact_xml) {
                    $this->a2x_xml_string .= str_repeat($this->indentstring, $this->a2x_depth -1);
                } 
                $this->a2x_xml_string .= '</' . $element['element_name'] . '>';
                $this->compact_xml or $this->a2x_xml_string .= "\n";
            } 
            $this->a2x_depth--;
        } 
    } 
} 

?>