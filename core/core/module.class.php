<?php
/**
 * Modules
 * 
 * Module Abstraction Layer
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 *
 */
class Modules {
	/**
	 * Modules::__construct()
	 * 
	 * @param  $codename 
	 * @return NULL 
	 */
	function __construct($codename = null) {
		if ($codename)
			$this->MODULEDIR = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "../modules" . DIRECTORY_SEPARATOR . $codename . DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Modules::LoadModule()
	 * 
	 * @param  $codename 
	 * @param unknown $params 
	 * @return 
	 */
	function LoadModule($codename = null, $params = null) {
		$module = new Modules ( );
		if ($codename != null) {
			$module->_LoadModule ( $codename, $params );
			return $module;
		}
		return false;
	}
	
	/**
	 * Modules::_LoadModule()
	 * 
	 * @param  $codename 
	 * @param unknown $params 
	 * @return 
	 * @access private 
	 */
	function _LoadModule($codename, $params = null) {
		$this->MODULEDIR = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "../modules" . DIRECTORY_SEPARATOR . $codename . DIRECTORY_SEPARATOR;
		$__INCFILE = $this->MODULEDIR . $codename . ".module.php";
		if (file_exists ($__INCFILE))
			include ($__INCFILE);
		else
			return false;
	}
	
	/**
	 * Modules::LoadDriver()
	 * 
	 * @param  $codename 
	 * @param unknown $params 
	 * @return 
	 * @access public 
	 */
	function LoadDriver($codename = null, $params = null) {
		if (file_exists ( $this->MODULEDIR . "drivers" . DIRECTORY_SEPARATOR . $codename . ".driver.php" ))
			include_once ($this->MODULEDIR . "drivers" . DIRECTORY_SEPARATOR . $codename . ".driver.php"); else
			return false;
	}
	
	/**
	 * Modules::LoadLib()
	 * 
	 * @param text $codename 
	 * @param array $params 
	 * @return boolean 
	 * @access public 
	 */
	function LoadLib($codename = null, $params = null) {
		if (file_exists ( $this->MODULEDIR . "libs" . DIRECTORY_SEPARATOR . $codename . ".lib.php" ))
			include_once ($this->MODULEDIR . "libs" . DIRECTORY_SEPARATOR . $codename . ".lib.php"); else
			return false;
	}
	
	/**
	 * Modules::LoadConfig()
	 * 
	 * @param text $codename 
	 * @param array $params 
	 * @return boolean 
	 * @access public 
	 */
	function LoadConfig($codename = null, $params = null) {
		if (! $this->MODULEDIR) {
			$this->MODULEDIR = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "../modules" . DIRECTORY_SEPARATOR . $codename . DIRECTORY_SEPARATOR;
		}
		
		if (file_exists ( $this->MODULEDIR . "config" . DIRECTORY_SEPARATOR . $codename . ".conf.php" )) {
			include_once ($this->MODULEDIR . "config" . DIRECTORY_SEPARATOR . $codename . ".conf.php");
		} else {
			return false;
		}
	}
}

?>