<?php

class Elotech_View_Helper_ValidaArray extends Zend_View_Helper_Abstract {

    function validaArray($busca, $arrays) {
        foreach ($arrays as $array) {
            if ($i = array_search($busca, $array) != false) {
                return $i;
            }
        }
        return false;
    }

}
