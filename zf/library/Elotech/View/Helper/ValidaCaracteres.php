<?php

class Elotech_View_Helper_ValidaCaracteres extends Zend_View_Helper_Abstract {
    
    function validaCaracteres($string){
        $string = preg_replace("/[áàâãä]/", "a", $string);
        $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
        $string = preg_replace("/[éèê]/", "e", $string);
        $string = preg_replace("/[ÉÈÊ]/", "E", $string);
        $string = preg_replace("/[íì]/", "i", $string);
        $string = preg_replace("/[ÍÌ]/", "I", $string);
        $string = preg_replace("/[óòôõö]/", "o", $string);
        $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
        $string = preg_replace("/[úùü]/", "u", $string);
        $string = str_replace("ç", "c", $string);
        $string = str_replace("Ç", "C", $string);
        $string = str_replace("/", "", $string);
        $string = str_replace('"\"', "", $string);
        return $string;
    }
    
}
