<?php
/**
 * PERSON
 *
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public
 */

class PERSON extends CORE {
	public  $_CLASS = __CLASS__;
	
	/**
	 * PERSON class constructor
	 * If you give him argument they will be passed to load function
	 * and new CORE object will be replased with the loaded one
	 *
	 * PERSON::__construct()
	 *
	 * @return object
	 */
	function __construct() {
        $numargs = func_num_args ();

        if ($numargs == 1) {
            $arg = func_get_arg ( 0 );
            $O = $this->load ( $arg );
            $this->_assgin_object ( $O );
        } else if ($numargs == 2) {
            $arg = func_get_arg ( 0 );
            $arg1 = func_get_arg ( 1 );
            $O = $this->load ( $arg, $arg1 );
            $this->_assgin_object ( $O );
        }
	}
	
	/**
	 * PERSON::PERSONLOAD()
	 *
	 * @return object
	 * @access public
	 */
	function PERSONLOAD() {
		global $PLATFORMID, $RUNNINGNDIR;
		
		if ($_GET ["do"] == "logout") {
            PERSON::logout();
            PERSON::setCache(null);
            unset($_SESSION["LOGIN_ERROR"], $_SESSION["LOGIN_ERROR_MSG"]);
            return null;
		} else {
            if (!empty($_SESSION["CURRENTUSER_SESSION"])) {
                $O = unserialize($_SESSION ["CURRENTUSER_SESSION"]);
                $O->_permissions = $O->load_permissions($O->id);
                return $O;
            }

            if (($_POST["do"] == "login") && ($_POST["email"] != "") && ($_POST["password"] != "")) {
                $O_ = new PERSON();

                if ($GLOBALS["_CAPTCHA"]) {
                    include($RUNNINGNDIR . "/admin/securimage/securimage.php");
                    $img = new Securimage();
                    $valid = $img->check($_POST['seccode']);
                    if($valid != true) {
                        $_SESSION["LOGIN_ERROR"] = true;
                        $_SESSION["LOGIN_ERROR_MSG"] = "Sorry, the code you entered was invalid!";
                        return $O_;
                    }
                }

                $O = $O_->PERSONLOGIN($_POST["email"], $_POST["password"]);
                if (is_object($O))
                    $O->_permissions = $O->load_permissions ( $O->id );
                $GLOBALS ["REDIRECT"] = true;
                if ($O->_loggedin) {
                    $O->setCache ( true );
                    return $O;
                }
                $_SESSION ["LOGIN_ERROR"] = true;
            }
		}

		$O = new PERSON();
		$O->_objectname = "person";
		$O->_permissions = $O->load_permissions($PLATFORMID);
		$O->_loggedin = false;

		return $O;
	}
	
	/**
	 * PERSON::PERSONLOGIN()
	 *
	 * @param  $email
	 * @param  $password
	 * @return boolean
	 * @access public
	 */
	function PERSONLOGIN($uname, $password) {
        global $DTD, $DB, $PLATFORMID;

        $this->_objectname = "person";
        $this->_object = $DTD->get_object_id ($this->_objectname);
        $newPassword = PERSON::hashPassword ($password);
        if ($GLOBALS["_CAPTCHA"]) {
            
        }
        $sql = "SELECT g.*, u.* FROM " . TQT . "global" . TQT . " g, " . TQT . "users" . TQT . " u
                  WHERE
                    (u." . TQT . "username" . TQT . " = '$uname') and
                    (u." . TQT . "password" . TQT . " = '" . $newPassword . "') and
                    (g." . TQT . "id" . TQT . " = u." . TQT . "gid" . TQT . ") and
                    ((g." . TQT . "_platform" . TQT . " = '" . $PLATFORMID . "') or
                    (u." . TQT . "_group" . TQT . " = '-1'))";
	
        $data_ = $DB->getRow($sql);
        $data = SQL_fixNames($data_);
        if ($data->id) {
            $p = new PERSON ( );
            $p->_objectname = "person";
            
            $p->id = $data->id;
            $p->username = $data->username;
            $p->password = $data->password;
            $p->group = $data->_group;
            $p->_loggedin = true;
            if (strpos($_SERVER["REQUEST_URI"], 'login'))
                $GLOBALS ["REDIRECT"] = true;
            $p->register_userobject();
            $p->login_mail();
            return $p;
        }
        return false;
	}
	
	/**
	 * PERSON::logout()
	 *
	 * @return NULL
	 * @access public
	 */
	function logout() {
        unset ($_SESSION["CURRENTUSER_SESSION"]);
        
        $_SESSION["LOGGEDOUT"] = true;
        $GLOBALS["REDIRECT"] = true;
	}
	
	/**
	 * Here we handle passwords
	 *
	 * PERSON::hashPassword()
	 *
	 * @param  $text
	 * @return string
	 * @access public
	 */
	function hashPassword($text) {
        global $_GENERAL_SEED;
        
        $CRYPT = new DOCRYPT ( $_GENERAL_SEED );
        
        return $CRYPT->enc ( $text );
	}
	
	/**
	 * This function tests permissions
	 *
	 * PERSON::isAllowed()
	 *
	 * @param text $permission
	 * @return boolean
	 * @access public
	 */
	function isAllowed($permission) {
        if ($this->isSuperUser ())
                return true;
        if (is_array ( $this->_permissions ) && in_array ( $permission, $this->_permissions )) {
                return true;
        }
        
        return false;
	}
	
	/**
	 * PERSON::testAccess()
	 *
	 * @return boolean
	 * @access public
	 */
	function testAccess() {
        global $SET_ACCESS;

        $this->access = true;

        /**
         * If there is no access value set return true
         * Also if user is SU he gets full access
         */
        if (! $SET_ACCESS)
            return true;
        if ($this->isSuperUser ())
            return true;

        foreach ( $SET_ACCESS as $ACCESS )
            if ((is_array ( $this->_permissions )) && in_array ( $ACCESS, $this->_permissions ))
                return true;

        $this->access = false;
        return false;
	}
	
	/**
	 * Test for super user of the current user
	 *
	 * PERSON::isSuperUser()
	 *
	 * @return boolean
	 * @access public
	 */
	function isSuperUser() {
        if ($this->group == - 1) {
            return true;
        } else {
            return false;
        }
	}
	
	/**
	 * PERSON::addToGroup()
	 *
	 * @param int $grp
	 * @return boolean
	 * @access public
	 */
	function addToGroup() {
        if ($this->group == - 1) {
            return true;
        } else {
            return false;
        }
	}
	
	/**
	 * PERSON::setDefaultGroup()
	 *
	 * @param int $grp
	 * @return boolean
	 * @access public
	 */
	function setDefaultGroup() {
        if ($this->group == - 1) {
            return true; 
        } else {
            return false;
        }
	}
	
	/**
	 * PERSON::_update_person()
	 *
	 * @param object $val
	 * @return NULL
	 * @access private
	 */
	function _update_person($val = null) {
        global $DB;

        if (! $val)
            $val = &$this;

        $sql = "SELECT * FROM users WHERE gid='" . $val->id . "'";
        $data = $DB->getRow ( $sql );

        $fromXML = $val->translate_object_data ();
        if ($data->gid) {
            // Do update
            $sql = "UPDATE users SET " . TQT . "username" . TQT . " = '" . escape_sql ( $fromXML ["username"] ) . "', " . TQT . "password" . TQT . " = '" . escape_sql ( $fromXML ["password"] ) . "', " . TQT . "_group" . TQT . " = '" . (($data->_group) ? $data->_group : 0) . "' WHERE " . TQT . "gid" . TQT . " = '" . $val->id . "'";
        } else {
            // Do insert
            //???????
            //$fromXML["_username"] . "', '" . $fromXML["_password"]
            $sql = "INSERT INTO users (" . TQT . "gid" . TQT . ", " . TQT . "username" . TQT . ", " . TQT . "password" . TQT . ") VALUES ('" . $val->id . "', '" . escape_sql ( $fromXML ["username"] ) . "', '" . escape_sql ( $fromXML ["password"] ) . "')";
        }
        $DB->query ( $sql );

        return;
	}
	
	/**
	 * PERSON::MakeItSU()
	 * Make it Super User (root)
	 *
	 * @param object $val
	 * @return NULL
	 * @access public
	 */
	function MakeItSU($val = null) {
        global $DB;
        
        if (! $val)
            $val = &$this;
        $sql = "UPDATE " . TQT . "users" . TQT . " SET " . TQT . "_group" . TQT . " = '-1' WHERE " . TQT . "gid" . TQT . " = '" . $val->id . "'";
        $DB->query ( $sql );
        
        return;
	}
	
	/**
	 * PERSON::setCache()
	 *
	 * @param $SET_TO
	 * @return NULL
	 */
	function setCache($SET_TO) {
        if ($SET_TO) {
            $_SESSION ["cache"] = "on";
        } else {
            unset ( $_SESSION ["cache"] );
        }

        return;
	}
	
	/**
	 * PERSON::session_register_userobject()
	 *
	 * @param  $object
	 * @return NULL
	 * @access private
	 */
	function register_userobject() {
        global $CURRENTUSER_SESSION;
        
        unset ($_SESSION["CURRENTUSER_SESSION"]);
        
        if (is_array( $this->_shoppingcart ) && sizeof ( $this->_shoppingcart )) {
            for($i = 0; $i < sizeof ( $this->_shoppingcart ); $i ++) {
                $this->_shoppingcart [$i]->_dtd = "";
            }
        }
        
        $CURRENTUSER_SESSION = serialize ( $this );
        $_SESSION ["CURRENTUSER_SESSION"] = $CURRENTUSER_SESSION;
	}
	
	/**
	 * PERSON::login_mail()
	 *
	 * @param  $login
	 * @return NULL
	 * @access private
	 */
	function login_mail() {
        global $_GENERAL_EMAIL;

        if (! $_GENERAL_EMAIL)
            return;

        $login = $this->username;
        if ($_SERVER ["HTTP_HOST"] != "localhost") {
            $subject = "Puzzle login";
            $messege = "Puzzle Instalation: http://" . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"] . "\n\n";
            $messege .= "USER NAME: $login \n";
            $messege .= "DATE, TIME: " . date ( "r" ) . "\n";
            $messege .= "IP ADDRESS: " . $_SERVER ["REMOTE_ADDR"] . "\n\n";

            @mail ( $_GENERAL_EMAIL, $subject, $messege, "From: " . $_GENERAL_EMAIL . "\r\n" );
        }
	}
}

?>