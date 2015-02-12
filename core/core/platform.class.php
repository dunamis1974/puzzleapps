<?php
/**
 * PLATFORM
 *
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public
 */

class PLATFORM extends CORE {
	public $_CLASS = __CLASS__;
	
	/**
	 * PLATFORM class constructor
	 * If you give him argument they will be passed to load function
	 * and new PLATFORM object will be replased with the loaded one
	 *
	 * PLATFORM::__construct()
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
	 * Load current paltform
	 *
	 * PLATFORM::LoadPlatform()
	 *
	 * @return object
	 * @access public
	 */
	function LoadPlatform() {
		global $_DOINSTALL, $_GENERAL_EMAIL, $PLATFORMNAME, $PLATFORMID, $DTD, $DB;
		
		//$platform_ = $CORE->getByTypeAndParam("platform", "name", );
		$O = new PLATFORM ( );
		$O->_objectname = "platform";
		$O->_object = $DTD->get_object_id ( $O->_objectname );
		//$O->_load_dtd();
		

		$sql = "
          SELECT
              g.id,
              g._group,
              g._o,
              g._g,
              g._w,
              p.name,
              p.descrption
          FROM " . TQT . "global" . TQT . " g, " . TQT . "platform" . TQT . " p
          WHERE
            (p.name = '$PLATFORMNAME') and
            (g.id = p.gid) and
            (g._platform = g.id)
            ";
		if ($PLATFORMNAME)
			$data = $DB->getRow ( $sql );
		
		if (is_object ( $data )) {
			$data = SQL_fixNames ( $data );
			$O->id = $data->id;
			$O->name = $data->name;
			$O->descrption = $data->descrption;
			$O->_group = $data->_group;
			$O->_o = $data->_o;
			$O->_g = $data->_g;
			$O->_w = $data->_w;
		}
		
		if (! $O->id && ! $_DOINSTALL) {
			die ( "<br /><h3>Can't find Puzzle Apps CMS installation!</h3><br /><i>Please contact server administrator at: <a href=\"mailto:" . $_GENERAL_EMAIL . "\">" . $_GENERAL_EMAIL . "</a>.</i>" );
		} else {
			$PLATFORMID = $O->id;
		}
		
		if ($O->id)
			$O->_loaded = true;
		
		return $O;
	}
	
	/**
	 * PLATFORM::groupsForm()
	 *
	 * @param $platform
	 * @return string $_BODY
	 */
	function groupsForm(&$O) {
		global $SYSTEMIMAGES, $CURRENTUSER;
		
		/**
		 * Change group
		 */
		if ($_GET ["select"] && ($CURRENTUSER->isAllowed ( "chown" ))) {
			$_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"450\"><tr><td>\n";
			$data = PERMISSIONS::GetGroup ( $_GET ["select"] );
			if (count ( $data ) > 0) {
				PERMISSIONS::changeGroup ( $O->id, $_GET ["select"] );
				$_BODY .= BuildErrorMsg ( "##Object assigned to group## '" . $data [0]->groupname . "'" );
			} else {
				$_BODY .= BuildErrorMsg ( "##You can't assign object to this group!##" );
			}
			$_BODY .= "</td></tr></table>\n";
			$O->_group = $_GET ["select"];
		}
		
		/**
		 * Display groups
		 */
		$data = PERMISSIONS::GetGroups ();
		$end = count ( $data );
		$_BODY .= "<table bgcolor=\"#FFFFFF\" width=\"450\">\n";
		$_BODY .= "<tr class=\"title__\"><td class=\"\">ID</td><td class=\"\">##Title##</td><td class=\"\">##Description##</td><td class=\"\">&nbsp;</td></tr>\n";
		if ($end > 0)
			for($i = 0; $i < $end; $i ++) {
				$_BODY .= "
                <tr onMouseOver=\"this.style.backgroundColor='#FFDEAD'\" onMouseOut=\"this.style.backgroundColor=''\">
                    <td>[ " . $data [$i]->id . " ]</td>
                    <td>" . $data [$i]->groupname . "</td>
                    <td>" . nl2br ( $data [$i]->note ) . "</td>
                    <td align=\"center\">
                        " . (($data [$i]->id != $O->_group) ? "<a href=\"" . BuildLinkGet () . "&select=" . $data [$i]->id . "\"><img src=\"" . $SYSTEMIMAGES . "16x16/group_select.png\" border=0 width=\"16\" height=\"16\" title=\"##select default group##\" /></a>" : "<img src=\"" . $SYSTEMIMAGES . "16x16/group_selected.png\" border=0 width=\"16\" height=\"16\" title=\"##default group##\" />") . "
                    </td>
                </tr>";
			}
		$_BODY .= "\n</table>";
		
		return $_BODY;
	}
	
	/**
	 * PLATFORM::CreatePlatform()
	 *
	 * @param array $data
	 * @return boolean
	 */
	function CreatePlatform($data) {
		$ODD = "platform";
		$O = new PLATFORM ( $ODD, $data );
		$O->insert ();
		$O->updatePlatform ();
		return true;
	}
	
	/**
	 * PLATFORM::updatePlatform()
	 */
	function updatePlatform() {
		global $DB;
		if ($this->id) {
			$sql = "UPDATE " . TQT . "global" . TQT . " SET " . TQT . "_platform" . TQT . " = '" . $this->id . "' WHERE " . TQT . "id" . TQT . " = '" . $this->id . "';";
			$DB->query ( $sql );
			
			$sql = "INSERT INTO " . TQT . "platform" . TQT . " (" . TQT . "gid" . TQT . ", " . TQT . "name" . TQT . ", " . TQT . "descrption" . TQT . ") VALUES ('" . $this->id . "', '" . escape_sql ( $this->_arrayData ["name"] ) . "', '" . escape_sql ( $this->_arrayData ["descrption"] ) . "');";
			$DB->query ( $sql );
		}
	}
	
	/**
	 * PLATFORM::updateDescription()
	 */
	function updateDescription($data) {
		global $DB;
		if ($this->id) {
			$sql = "UPDATE " . TQT . "platform" . TQT . " SET " . TQT . "descrption" . TQT . " = '" . escape_sql ( $data ) . "' WHERE " . TQT . "gid" . TQT . " = '" . $this->id . "';";
			$DB->query ( $sql );
		}
	}
}

?>