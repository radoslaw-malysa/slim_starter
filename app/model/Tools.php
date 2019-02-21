<?php

namespace Model;

class Tools  {
    static public function trim($str, $len)
    {
        if(strlen($str) > $len) {
           $str = preg_replace("/^(.{1,$len})(\s.*|$)/s", '\\1...', $str);
        }
        return($str);
    }
    
    public static function makedirs($dirpath, $mode=0777) {
        return is_dir($dirpath) || mkdir($dirpath, $mode, true);
    }
    
    static public function strToUrl($str) {
        $str = str_replace(
        array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż','?',',','!','#'), 
        array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z','A','C','E','L','N','O','S','Z','Z','','','',''), 
        trim($str));
       
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
       
        return $clean;
    }
}