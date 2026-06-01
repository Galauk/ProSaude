<?php
class Elotech_View_Helper_Dpp extends Zend_View_Helper_Abstract {

   function dpp($data) {
       $date = new DateTime($data);
        $date->add(new DateInterval('P7D'));
        $date->sub(new DateInterval('P3M'));
        $date->add(new DateInterval('P12M'));

        return $date->format('d/m/Y');
  }

}