<?php
/**
 * PERMISSIONS
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 */

class PERMISSIONS {
	
	/**
	 * Loading permissons into PERSON object
	 * 
	 * PERMISSIONS::_load_permissions()
	 * 
	 * @param unknown $user 
	 * @return NULL 
	 * @access public 
	 */
	function load_permissions($user) {
		global $DB;
		
		if (! $user)
			return false;
		
		/**
		 * Load default permissions
		 */
		
		$sql = "SELECT g.* FROM " . TQT . "groups" . TQT . " g, " . TQT . "global" . TQT . " glb WHERE glb.id = '$user' and g.id = glb._group;";
		$_permissions = $DB->getRow ( $sql );
		
		if ($_permissions) {
			$_permissions = SQL_fixNames ( $_permissions );
			$permissions = unserialize ( $_permissions->permissions );
			$_groups [] = $_permissions->id;
			$_groupnames [] = $_permissions->groupname;
		}
		
		$sql = "SELECT g.* FROM " . TQT . "groups" . TQT . " g, " . TQT . "users_in_groups" . TQT . " u WHERE u." . TQT . "userid" . TQT . " = '" . $user . "' and g." . TQT . "id" . TQT . " = u." . TQT . "_group" . TQT . "";
		$data = $DB->getAll ( $sql );
		$end = count ( $data );
		for($i = 0; $i < $end; $i ++) {
			$data [$i] = SQL_fixNames ( $data [$i] );
			$_groups [] = $data [$i]->id;
			$_groupnames [] = $data [$i]->groupname;
			if (trim ( $data [$i]->permissions ) != "") {
				$permissions_ = unserialize ( $data [$i]->permissions );
				$permissions = array_merge ( ( array ) $permissions, ( array ) $permissions_ );
			}
		}
		$permissions ["_groups"] = $_groups;
		$permissions ["_groupnames"] = $_groupnames;
		return $permissions;
	}
	
	/**
	 * 
	 * PERMISSIONS::changeGroup()
	 * 
	 * @param int $id
	 * @param int $grp
	 * @return NULL 
	 * @access public 
	 */
	function changeGroup($id, $grp) {
		global $DB;
		if ($id && $grp) {
			$sql = "UPDATE " . TQT . "global" . TQT . " SET " . TQT . "_group" . TQT . " = '" . $grp . "' WHERE " . TQT . "id" . TQT . "='" . $id . "'";
			$DB->query ( $sql );
		}
	}
	
	/**
	 * 
	 * PERMISSIONS::changeOwner()
	 * 
	 * @param int $id
	 * @param int $usr
	 * @return NULL 
	 * @access public 
	 */
	function changeOwner($id, $usr) {
		global $DB;
		if ($id && $usr) {
			$sql = "UPDATE " . TQT . "global" . TQT . " SET " . TQT . "_owner" . TQT . " = '" . $usr . "' WHERE " . TQT . "id" . TQT . "='" . $id . "'";
			$DB->query ( $sql );
		}
	}
	
	/**
	 * 
	 * PERMISSIONS::changePermissions()
	 * 
	 * @param int $obj
	 * @param int $grp
	 * @return boolean 
	 * @access public 
	 */
	function changePermissions($id, $prm) {
		global $DB;
		if ($id) {
			$sql = "UPDATE " . TQT . "global" . TQT . " SET " . TQT . "_o" . TQT . " = '" . $prm ["_o"] . "', " . TQT . "_g" . TQT . " = '" . $prm ["_g"] . "', " . TQT . "_w" . TQT . " = '" . $prm ["_w"] . "' WHERE " . TQT . "id" . TQT . "='" . $id . "'";
			$DB->query ( $sql );
		}
	}
	
	/**
	 * PERMISSIONS::GetGroups()
	 *
	 * @return array $data
	 */
	function GetGroups() {
		global $DB, $CURRENTPLATFORM;
		
		$sql = "SELECT * FROM " . TQT . "groups" . TQT . " WHERE " . TQT . "platform" . TQT . " = '" . $CURRENTPLATFORM->id . "' ORDER BY " . TQT . "groupname" . TQT . "";
		$data = $DB->getAll ( $sql );
		
		return $data;
	}
	
	/**
	 * PERMISSIONS::GetGroup()
	 *
	 * @param $id
	 * @return array $data
	 */
	function GetGroup($id = null) {
		global $DB, $CURRENTPLATFORM;
		
		if (! $id)
			$id = $this->_group;
		
		$sql = "SELECT * FROM " . TQT . "groups" . TQT . " WHERE " . TQT . "platform" . TQT . " = '" . $CURRENTPLATFORM->id . "' and " . TQT . "id" . TQT . " = '$id'";
		$data = $DB->getAll ( $sql );
		
		return $data;
	}
	
	/**
	 * PERMISSIONS::GetUserGroups()
	 *
	 * @param $user
	 * @return string $groups
	 */
	function GetUserGroups($user) {
		global $DB;
		
		$sql = "SELECT g.* FROM " . TQT . "groups" . TQT . " g, " . TQT . "users_in_groups" . TQT . " u WHERE u." . TQT . "userid" . TQT . " = '" . $user . "' and g." . TQT . "id" . TQT . " = u." . TQT . "_group" . TQT . "";
		$data = $DB->getAll ( $sql );
		$end = count ( $data );
		for($i = 0; $i < $end; $i ++) {
			$data [$i] = SQL_fixNames ( $data [$i] );
			$groups .= $data [$i]->groupname;
			if ($i < ($end - 1))
				$groups .= ", ";
		}
		return $groups;
	}
	
	/**
	 * PERMISSIONS::GetUserGroups()
	 *
	 * @param $user
	 * @return array $data
	 * @access private
	 */
	function _GetUserGroups() {
		global $DB;
		
		$sql = "SELECT g.* FROM " . TQT . "groups" . TQT . " g, " . TQT . "users_in_groups" . TQT . " u WHERE u." . TQT . "userid" . TQT . " = '" . $this->id . "' and g." . TQT . "id" . TQT . " = u." . TQT . "_group" . TQT . "";
		$data = $DB->getAll ( $sql );
		$end = count ( $data );
		for($i = 0; $i < $end; $i ++) {
			$data [$i] = SQL_fixNames ( $data [$i] );
		}
		return $data;
	}
	
	function writeAccess() {
		global $CURRENTUSER;
		if ($CURRENTUSER->isSuperUser ())
			return true;
		if ($this->_owner == $CURRENTUSER->id)
			return true;
		if ((is_array ( $CURRENTUSER->_permissions ["_groups"] )) && in_array ( $this->_group, $CURRENTUSER->_permissions ["_groups"] ) && ($this->_g == 6))
			return true;
		if ($this->_w == 6)
			return true;
		return false;
	}
	
	/**
	 * PERMISSIONS::groupsForm()
	 *
	 * @param object $O
	 * @return string $_BODY
	 */
	function groupsForm(&$O) {
		global $CURRENTUSER;
		
		/**
		 * Change group
		 */
		if ($_GET ["select"]) {
			$data = PERMISSIONS::GetGroup ( $_GET ["select"] );
			if (count ( $data ) > 0) {
				PERMISSIONS::changeGroup ( $O->id, $_GET ["select"] );
				$_BODY .= BuildErrorMsg ( "##Object assigned to group## '" . $data [0]->groupname . "'" );
			} else {
				$_BODY .= BuildErrorMsg ( "##You can't assign object to this group!##" );
			}
		
		}
		
		/**
		 * Display groups
		 */
		$_BODY .= "<div class=\"formrow\">";
                $_BODY .= "<span class=\"col1form\">##Group##</span>\n";
                $_BODY .= "<span class=\"col2form\">";
		if ($CURRENTUSER->isAllowed ( "chown" )) {
			if ($CURRENTUSER->isSuperUser ())
				$data = $CURRENTUSER->GetGroups (); else
				$data = $CURRENTUSER->_GetUserGroups ();
			$end = count ( $data );
			$_BODY .= "<select name=\"_group\">\n";
			if ($end > 0)
				for($i = 0; $i < $end; $i ++) {
					$_BODY .= "<option value=\"" . $data [$i]->id . "\"" . (($data [$i]->id == $O->_group) ? " selected=\"true\"" : "") . ">" . $data [$i]->groupname . "</option>\n";
				}
			$_BODY .= "\n</select>";
		} else {
			//$grp = $O->GetGroup();
			$grp = $CURRENTUSER->_GetUserGroups ();
			$_BODY .= ": <i>" . $grp [0]->groupname . "</i>\n<input type=\"hidden\" name=\"_group\" value=\"" . $grp [0]->id . "\"><br />";
		}
		$_BODY .= "</span></div>";
		return $_BODY;
	}
	
	/**
	 * PERMISSIONS::permissionsForm()
	 *
	 * @param $user
	 * @return array $data
	 */
	function permissionsForm(&$O, $form = true) {
		global $CURRENTUSER, $SYSTEMIMAGES;
		
		if (! $O && is_object ( $this ))
			$O = $this;
		if (! is_object ( $O ))
			return null;
		
		/**
		 * Edit permissions
		 */
		if ($_POST ["_permissions"] && $O->id) {
			$_P ["_o"] = $_POST ["_or"] + $_POST ["_ow"];
			$_P ["_g"] = $_POST ["_gr"] + $_POST ["_gw"];
			$_P ["_w"] = $_POST ["_wr"] + $_POST ["_ww"];
			$O->_o = $_P ["_o"];
			$O->_g = $_P ["_g"];
			$O->_w = $_P ["_w"];
			PERMISSIONS::changePermissions ( $O->id, $_P );
		}
		
		/**
		 * Display permissions form
		 */
		if ($O->_o > 0)
			$_or = " checked=\"true\"";
		if ($O->_g > 0)
			$_gr = " checked=\"true\"";
		if ($O->_w > 0)
			$_wr = " checked=\"true\"";
		if ($O->_o == 6)
			$_ow = " checked=\"true\"";
		if ($O->_g == 6)
			$_gw = " checked=\"true\"";
		if ($O->_w == 6)
			$_ww = " checked=\"true\"";
		
		$_dis = null;
		
		$_BODY .= "<br /><br /><div class=\"formrow\">";
		//$_BODY .= "<div class=\"formtitle\">##Permissions##</div>";
		if ($form)
			$_BODY .= "<form action=\"" . $_SERVER ["REQUEST_URI"] . "\" method=\"POST\" enctype=\"multipart/form-data\">";
		$_BODY .= "<input type=\"hidden\" name=\"_permissions\" value=\"true\" />\n";
		$_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"350\">
            <tr class=\"title__\">
                <td align=\"center\"></td>
                <td align=\"center\">##Owner##</td>
                <td align=\"center\">##Group##</td>
                <td align=\"center\">##Other##</td>
            </tr>";
		if (! $CURRENTUSER->isAllowed ( "chmod" )) {
			// {" . $O->_o . $O->_g . $O->_w . "}
			$_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td align=\"center\" class=\"title__\">##Read##</td>
                <td align=\"center\">" . (($O->_o > 0) ? "<input type=\"hidden\" name=\"_or\" value=\"4\" /> <img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />" : "<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
                <td align=\"center\">" . (($O->_g > 0) ? "<input type=\"hidden\" name=\"_gr\" value=\"4\" />  <img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />" : "<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
                <td align=\"center\">" . (($O->_w > 0) ? "<input type=\"hidden\" name=\"_wr\" value=\"4\" />  <img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />" : "<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
            </tr>
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td align=\"center\" class=\"title__\">##Write##</td>
                <td align=\"center\">" . (($O->_o == 6) ? "<input type=\"hidden\" name=\"_ow\" value=\"2\" />  <img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />" : "<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
                <td align=\"center\">" . (($O->_g == 6) ? "<input type=\"hidden\" name=\"_gw\" value=\"2\" />  <img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />" : "<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
                <td align=\"center\">" . (($O->_w == 6) ? "<input type=\"hidden\" name=\"_ww\" value=\"2\" />  <img src=\"" . $SYSTEMIMAGES . "16x16/ok.png\" />" : "<img src=\"" . $SYSTEMIMAGES . "16x16/delete.png\" />") . "</td>
            </tr>";
		} else {
			$_BODY .= "
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td align=\"center\" class=\"title__\">##Read##</td>
                <td align=\"center\"><input type=\"checkbox\" name=\"_or\" value=\"4\"$_or$_dis /></td>
                <td align=\"center\"><input type=\"checkbox\" name=\"_gr\" value=\"4\"$_gr$_dis /></td>
                <td align=\"center\"><input type=\"checkbox\" name=\"_wr\" value=\"4\"$_wr$_dis /></td>
            </tr>
            <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                <td align=\"center\" class=\"title__\">##Write##</td>
                <td align=\"center\"><input type=\"checkbox\" name=\"_ow\" value=\"2\"$_ow$_dis /></td>
                <td align=\"center\"><input type=\"checkbox\" name=\"_gw\" value=\"2\"$_gw$_dis /></td>
                <td align=\"center\"><input type=\"checkbox\" name=\"_ww\" value=\"2\"$_ww$_dis /></td>
            </tr>";
		}
		
		if ($form)
			$_BODY .= "<tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\"><td align=\"center\" colspan=\"4\"><input type=\"submit\" value=\" ##Update## \"$_dis /></td></tr>\n";
		$_BODY .= "\n</table>";
		if ($form)
			$_BODY .= "\n</form>";
		$_BODY .= "\n</div>";
		
		return $_BODY;
	}
}

?>