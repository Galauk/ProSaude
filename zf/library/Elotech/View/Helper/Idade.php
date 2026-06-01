<?php

class Elotech_View_Helper_Idade extends Zend_View_Helper_Abstract {

	/**
	 * Converte uma data (aaaa-mm-dd) em um valor relativo a idade
	 * @example idade('1989-06-04'); // 22 anos
	 * @example idade('2012-01-01'); // 1 mês e 10 dias
	 * @param string $dataNasc Data de nascimento no formato Y-m-d
	 * @return string 
	 */
    function idade($dataNasc=FALSE,$full=FALSE) {
  		if(!$dataNasc)
        return "";
      $now = new DateTime;
      $ago = new DateTime($dataNasc);
      $diff = $now->diff($ago);

      $diff->w = floor($diff->d / 7);
      $diff->d -= $diff->w * 7;

      $string = array(
        'y' => 'ano',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'dia'
      );
      foreach ($string as $k => &$v) {
        if(array_key_exists($k, $string)){
          if ($diff->$k) {
            $plural = $k == 'm' ? $diff->$k > 1 ? 'meses' : 'mês' : $v.'s';
            $v = $diff->$k . ' ' . $plural;
          } else {
            unset($string[$k]);
          }
        }
      }

      if (!$full) $string = array_slice($string, 0, 1);
      return $string ? implode(' e ', $string) : 'agora';
    }

} 