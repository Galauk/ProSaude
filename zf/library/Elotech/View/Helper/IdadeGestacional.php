<?php

class Elotech_View_Helper_IdadeGestacional extends Zend_View_Helper_Abstract
{

    /**
     * Converte uma data (aaaa-mm-dd) em um valor relativo a idade gestacional
     * @return string
     */

    function idadeGestacional($dum, $data_comparacao = FALSE)
    {
        if(!$dum){
            return "";
        }

        if($data_comparacao){
            $time = new DateTime($data_comparacao);
            $current_time = time($data_comparacao);
        } else {
            $time = new DateTime();
            $current_time = time();
        }
        $data = explode('-', $dum);

        $age_years = date('Y', $current_time) - $data[0];
        $age_months = date('m', $current_time) - $data[1];
        $age_days = date('d', $current_time) - $data[2];
        $age = new DateTime($dum);

        if ($age_years == 0) {

            $dias = $time->format("z");
            $age_years = ($dias - $age->format("z")) / 7;
            $semana = intval($age_years);

        } else {
            $dias = 365 - $age->format("z");
            $dias = ($dias + $time->format("z"))/7;
            $semana = intval($dias);
        }

        return $semana;
    }
}