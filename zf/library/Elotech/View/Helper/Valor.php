<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Elotech_View_Helper_Valor extends Zend_View_Helper_Abstract {

function valor($valor){
        if(!empty($valor)){
            return number_format($valor,2,",",".");
        }else{
            return number_format(0,2,",",".");
        }
    }

}