<?php
/**
 * FILES
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 */

class FILES extends CORE {
    public $_CLASS = __CLASS__;

    /**
     * This is class constructor that can load file by given id
     * 
     * FILES::__construct()
     * 
     * @return object 
     * @access constructor
     */
    function __construct () {
        if (func_num_args() != 0) {
            $O = $this->load(func_get_arg(0));
            $this->_assgin_object($O);
        }
    }

    /**
     * FILES::upload()
     * 
     * @return object 
     * @access public 
     */
    function upload () {
        global $FILEROOT, $CURRENTPLATFORM;
        
        $parent = new CORE(($_GET["parent"])?$_GET["parent"]:$CURRENTPLATFORM->id);
        $parentdata = $parent->translate_object_data();
        
        if ($_FILES['file']['size'] <= 0 && !$_POST["name"])
                return false;
        if ($_FILES['file']['size'] > 0) {
            $temp = fopen($_FILES['file']['tmp_name'], "rb");
            $file_data = fread($temp, $_FILES['file']['size']);
            fclose ($temp);
            // if we want this file to be compressed
            if ($_POST["compress"]) {
                LoadModule ( "zip");
                $ZIP = new zipfile();
                $ZIP->add_file($file_data, $_FILES['file']['name']);
                $zip_data = $ZIP->file();
                $file_data = $zip_data;
            }
            $O = new FILES("file");
            $O->insert();
            // die();
            $parent->addChild($O->id);
            // Create Unique name
            $newname = $O->id . strrchr ( $_FILES['file']['name'], "." );
            $newfile = $FILEROOT . $newname;
            // This code writes uploaded file to the new location
            $fp = fopen($newfile, "w");
            if ($fp) {
                fwrite ($fp, $file_data);
                fclose ($fp);
                // and now populate data and update
                $_POST["realname"] = $_FILES['file']['name'];
                $_POST["file"] = $newname;
                $_POST["type"] = $_FILES["file"]["type"];
                $_POST["size"] = $_FILES["file"]["size"];
                $_POST["tid"] = $parentdata["tid"] . "/" . $_POST["realname"];
                if (!$_POST["name"])
                        $_POST["name"] = $_FILES['file']['name'];

                $Obj = new CORE($O->id, $_POST);
                $Obj->update();

                unlink($_FILES['file']['tmp_name']);
            } else {
                $O->delete();
                return false;
            }
        } else {
            
            $_POST["type"] = "folder";
            $_POST["tid"] = $parentdata["tid"] . "/" . $_POST["name"];

            $Obj = new CORE("file", $_POST);
            $Obj->insert();
            $parent->addChild($Obj->id);

        }

        return $Obj;
    }

    /**
     * Delete file object
     * FILES::remove()
     * 
     * @return NULL 
     * @access public 
     */
    function remove() {
        global $FILEROOT;

        $DATA = $this->translate_object_data();

        if (file_exists($FILEROOT . $DATA["file"])) {
            unlink($FILEROOT . $DATA["file"]);
        }
        $this->delete();
    }

    /**
     * FILES::edit()
     * 
     * @return NULL 
     * @access public 
     */
    function edit() {
    }

    /**
     * Take tha list of all files
     * 
     * FILES::listFiles()
     * 
     * @return array 
     */
    function listFiles() {
        $result = $this->getAllOfType("file");
        
        return $result;
    }

    /**
     * FILES::createFolder()
     * 
     * @return NULL 
     */
    function createFolder() {
    }

    /**
     * FILES::deleteFolder()
     * 
     * @return NULL 
     */
    function deleteFolder() {
    }

    /**
     * Take file name of file object with $id
     * 
     * FILES::imageName()
     * 
     * @param integer $id 
     * @return text 
     * @access public 
     */
    function imageName($id = null) {
        
        if (! $id)
            return null;
            
        // load object
        $O = new FILES($id);
        $DATA = $O->translate_object_data ();

        if (!$DATA["file"])
            return null;

        return $DATA["file"];
    }

    /**
     * Add file to normal object
     * 
     * FILES::uploadInObject()
     * 
     * @param text $file 
     * @return text 
     * @access public 
     */
    function uploadInObject($file) {
        global $FILEROOT;
        
        if ($_FILES[$file]['size'] <= 0)
            return false;
            
        $temp = fopen($_FILES[$file]['tmp_name'], "rb");
        $file_data = fread($temp, $_FILES[$file]['size']);
        fclose ($temp);
        
        // Create Unique name
        //$newname = md5($_FILES[$file]['name'] . "-" . time()) . strrchr($_FILES[$file]['name'], ".");
        $newname = FILES::_fix_file_name($_FILES[$file]['name']);
        $newfile = $FILEROOT . $newname;
        
        // This code writes uploaded file to the new location
        $fp = fopen($newfile, "w");
        if ($fp) {
            fwrite ($fp, $file_data);
            fclose ($fp);
            unlink ($_FILES[$file]['tmp_name']);
        } else {
            return false;
        }
        
        $img_data = getimagesize($newfile);
        $size = round(filesize($newfile) / 1024, 2) . "KB";
        $filedata = array(
            "name" => $newname,
            "type" => $img_data["mime"],
            "size" => $img_data[1] . "/" . $img_data[0],
            "dimensions" => $size,
        );
        
        return $filedata;
    }
    
    /**
     * This function intends to fix
     * file name and make it more seo friendly
     * 
     * FILES::_fix_file_name()
     * 
     * @param  $name 
     * @return string $final
     * @access static 
     */
    static function _fix_file_name($name) {
        global $FILEROOT;
        
        $name = str_replace(" ", "-", $name);
        $name = str_replace("(", "", $name);
        $name = str_replace(")", "", $name);
        $name = str_replace("--", "-", $name);
        
        $ext = strrchr($name, ".");
        $fname = str_replace($ext, "", $name);
        $final = $name;
        $newfile = $FILEROOT . $name;
        
        while (1) {
            if (!file_exists($newfile))
                break;
            $i++;
            $newfile = $FILEROOT . $fname . "-{$i}" . $ext;
        }
        
        if ($i > 0)
            $final = $fname . "-{$i}" . $ext;
        
        return $final;
    }

    /**
     * This function deletes all files
     * uploaded with current object
     * can be used on object delete
     * 
     * FILES::_delete_files()
     * 
     * @param  $id 
     * @return boolean true
     * @access private 
     */
    private function _delete_files($id) {
        global $SYS_LANGUAGES, $LANGUAGES, $FILEROOT;

        $O = CORE::load ( $id );
        foreach ( $O->_dtd as $_ELM ) {

            if ($_ELM["data"][1]["data"] == "file") {
                foreach ( $LANGUAGES as $LNG ) {
                    $DATA = $O->_xml_to_array ( $SYS_LANGUAGES[$LNG]["id"] );
                    foreach ( $DATA[0]["data"][0]["data"] as $DT ) {
                        if (($DT["element_name"] == $_ELM["element_name"]) && file_exists ( $FILEROOT . $DT["data"] )) {
                            unlink ( $FILEROOT . $DT["data"] );
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * This function removes files from old object
     * can be used on object update
     * 
     * FILES::_update_file()
     * 
     * @param  $id 
     * @param  $data 
     * @return boolean true
     * @access private 
     */
    function _update_file($id, $data) {
        global $XML, $EDITLANGUAGE, $FILEROOT;
        // take data from new object
        $NEWXML = "<" . $data->_objectname . ">\n";
        $NEWXML .= $data->data;
        $NEWXML .= "</" . $data->_objectname . ">";
        $NEW_ = $XML->xml2array($NEWXML);

        foreach ( $NEW_[0]["data"][0]["data"] as $DT ) {
            $NEW[$DT["element_name"]] = $DT["data"];
        }

        // take data from old object
        $O = CORE::load($id);

        $EDIT = "data_" . $EDITLANGUAGE;

        if (!$O->$EDIT)
                return false;

        $OLD_ = $XML->xml2array($O->$EDIT);

        foreach ( $OLD_[0]["data"][0]["data"] as $DT ) {
            $OLD[$DT["element_name"]] = $DT["data"];
        }

        foreach ( $O->_dtd as $_ELM ) {
            if ($_ELM["data"][1]["data"] == "file") {
                if (($OLD[$_ELM["element_name"]] != $NEW[$_ELM["element_name"]]) && ! is_dir($FILEROOT . $OLD[$_ELM["element_name"]])) {
                    unlink($FILEROOT . $OLD[$_ELM["element_name"]]);
                }
            }
        }
        
        return true;
    }
}

?>