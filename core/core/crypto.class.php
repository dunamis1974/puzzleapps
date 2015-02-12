<?php
/**
 * DOCRYPT
 * 
 * @package Puzzle Apps
 * @author info@free-dev.com, Boyan Dzambazov (DuNaMiS)
 * @access public 
 */

class DOCRYPT {
    /**
     * DOCRYPT::DOCRYPT()
     * 
     * @param  $pass 
     * @param integer $level 
     * @return NULL 
     */
    function __construct($pass, $level = 30) {
        
        for($i = 0; $i < $level; $i ++)
            $mymd [$i] = md5 ( substr ( $pass, ($i % strlen ( $pass )), 1 ) );
            
        for($a = 0; $a < 32; $a ++)
            for($i = 0; $i < $level; $i ++)
                $key .= substr ( $mymd [$i], $a, 1 );
                
        $this->key = $key;
    }

    /**
     * DOCRYPT::enc()
     * 
     * @param  $text
     * @param integer $method
     * @return text $ntext
     * @access public
     */
    function enc($text, $method = 1) { // 1=encrypt, 0=decrypt
        $key = $this->key;
        
        if ($method == 0) {
            $key = str_replace ( "3", "j", str_replace ( "2", "i", str_replace ( "1", "h", str_replace ( "0", "g", $key ) ) ) );
            $key = str_replace ( "7", "n", str_replace ( "6", "m", str_replace ( "5", "l", str_replace ( "4", "k", $key ) ) ) );
            $key = str_replace ( "b", "4", str_replace ( "a", "5", str_replace ( "9", "6", str_replace ( "8", "7", $key ) ) ) );
            $key = str_replace ( "f", "0", str_replace ( "e", "1", str_replace ( "d", "2", str_replace ( "c", "3", $key ) ) ) );
            $key = str_replace ( "j", "c", str_replace ( "i", "d", str_replace ( "h", "e", str_replace ( "g", "f", $key ) ) ) );
            $key = str_replace ( "n", "8", str_replace ( "m", "9", str_replace ( "l", "a", str_replace ( "k", "b", $key ) ) ) );
            $text = base64_decode ( $text );
        }
        
        for($i = 0; $i < strlen ( $key ); $i = $i + 2)
            $d [$i / 2] = hexdec ( substr ( $key, $i, 2 ) );
            
        for($i = 0, $ntext = ""; substr ( $text, $i, 1 ) != ""; $i ++)
            $ntext .= chr ( (ord ( substr ( $text, $i, 1 ) ) + $d [($i % strlen ( $key ))]) % 255 );
            
        if ($method == 1)
            $ntext = base64_encode ( $ntext );
            
        return ($ntext);
    }
}

?>