<?php
/**
 * Translate
 * 
 * @package Puzzle Apps
 * @author Boyan Dzambazov (DuNaMiS)
 * @access public 
 */

global $_TRANSLATION;

class Translate {
	/**
	 * Go()
	 * 
	 * @param  $source 
	 * @return string $source
	 * @access public 
	 */
	function Go($source) {
		$source = str_replace ( "\r", "", $source );
		$source = trim ( $source ) . "\n";
		$source = preg_replace_callback ( "/({{.*?}}|##.*?##)/ms", "__lang_callback", $source );
		return trim($source);
	}
}

/**
 * __lang_callback()
 * 
 * @param  $things 
 * @return string $thing
 * @access private 
 */
function __lang_callback($things) {
	global $_TRANSLATION;
	
	$thing = $things [1];
	
	$lang = $_TRANSLATION;
	$matches = null;
	
	if (preg_match ( "/^{{(.*)}}$/s", $thing, $matches )) {
		if ($lang [$matches [1]] != '')
			return $lang [$matches [1]];
		return false; //$matches[1];
	}
	
	if (preg_match ( "/^##(.*)##$/s", $thing, $matches )) {
		if ($lang [$matches [1]] != '')
			return $lang [$matches [1]];
		return $matches [1];
	}
	
	return $thing;
}

?>