<?php

/**
 * Helper para models
 */
class Application_Model_Funcoes {

    /**
     * Informa o IP do cliente
     * @return string IP do cliente
    */

    public function getIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Converte uma data de d/m/Y para Y-m-d e vice-versa
     */
    public function invertData($data, $separadorEntrada = "-", $separadorSaida = "/") {
        list($a, $b, $c) = explode($separadorEntrada, $data);
        return $c . $separadorSaida . $b . $separadorSaida . $a;
    }

    public function converteData($data){
        list($a, $b, $c) = explode('/', $data);
        return $c . '-' . $b . '-' . $a;
    }

    /**
     * Transforma um Zend_Db_Table_Rowset_Abstract em uma string, concatenando os procedimentos com virgula
     * @param Zend_Db_Table_Rowset_Abstract $rowset
     * @return string 
     */
    public function rowsetToStr($rowset, $colName) {
        $out = array();
        foreach ($rowset as $row)
            $out [] = trim($row->$colName);

        return implode(", ", $out);
    }

    /**
     * Cria uma array com as datas entre $data_inicial e $data_final (inclusive)
     * @param string $data_inicial
     * @param string $data_final 
     */
    public function datasToArray($data_inicial, $data_final) {
        list($y1, $m1, $d1) = explode("-", $data_inicial);
        list($y2, $m2, $d2) = explode("-", $data_final);

        $mk_inicio = mktime(0, 0, 0, $m1, $d1, $y1);
        $mk_fim = mktime(0, 0, 0, $m2, $d2, $y2);

        $out = array();
        while ($mk_inicio <= $mk_fim) {
            $out [] = date("Y-m-d", $mk_inicio);
            $mk_inicio = mktime(0, 0, 0, $m1, ++$d1, $y1);
        }
        return $out;
    }

    /**
     * Retorna o primeiro e o ultimo dia do mês informado
     * @example "04/2012": 2012-04-01,2012-04-30
     * @param string $mesAno m/Y
     * @return array
     */
    public function getPrimeiroEUltimoDia($mesAno) {
        if (!$mesAno)
            return array_pad(array(), 4, FALSE);

        list($mes, $ano) = explode("/", $mesAno);
        $mk = mktime(0, 0, 0, $mes, 1, $ano);

        return array(
            "$ano-$mes-01",
            "$ano-$mes-" . date("t", $mk),
            $mes,
            $ano
        );
    }

    public function diaSemana($data) {
        $ano = substr($data, 0, 4);
        $mes = substr($data, 5, 2);
        $dia = substr($data, 8, 7);

        if (checkdate($mes, $dia, $ano)) {
            $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));
            switch ($diasemana) {
                case"0": $diasemana = "7";
                    break;  //domingo
                case"1": $diasemana = "1";//segunda
                    break;     
                case"2": $diasemana = "2";//terça
                    break;  
                case"3": $diasemana = "3";//quarta
                    break;  
                case"4": $diasemana = "4";//quinta
                    break;  
                case"5": $diasemana = "5";
                    break;  //sexta
                case"6": $diasemana = "6";
                    break;  //sabado
            }
            return $diasemana;
        }
    }

    public function calculaQuantidadePorIntervalo($horarios = FALSE, $coni_codigo = FALSE, $dia = FALSE, $condiAgeCod = FALSE) {
        if (empty($horarios))
            return false;

        $minutosEntreConsultas = array();

        //echo "<pre>".print_r($horarios,1);die();
        foreach ($horarios as $horario) {
            //echo "Hora Final:".$horario->hora_final." - Hora Inicial:".$horario->hora_inicial;
            // Configura o padrão de horário brasileiro
            date_default_timezone_set('UTC');
            // Pega o hora final menos a hora inicial e retorna o valor em segundos
            $hours_diff = strtotime($horario->hora_final) - strtotime("$horario->hora_inicial");
            $hora_minutos = $hours_diff / 60;


            array_push($minutosEntreConsultas, $hora_minutos);
        }
        //echo "<pre>".print_r($minutosEntreConsultas);die();die();
        $quantidades = $this->calculaQuantidade($minutosEntreConsultas, $coni_codigo, $dia, $condiAgeCod);
        //echo "<pre>".print_r($quantidades,1);die();die();
        return $quantidades;
        //return $minutosEntreConsultas;
    }

    public function calculaQuantidade($minutosEntreConsultas = FALSE, $coni_codigo, $dia = FALSE, $condiAgeCod = FALSE) {
        $tbGrad = new Application_Model_GradeDia();
        $intervalo = $tbGrad->getIntervaloDia($coni_codigo, $dia, $condiAgeCod);
        $quantidades = array();
        //die($intervalo);
        foreach ($minutosEntreConsultas as $minutosEntreConsulta) {
            $quantidade = ($minutosEntreConsulta / $intervalo);
            array_push($quantidades, $quantidade);
        }
        //echo "<pre>".print_r($quantidades,1);die();
        return $quantidades;
    }

    public function distribuicao($quantidades = FALSE, $horarios = FALSE, $coni_codigo = FALSE, $data_selecionada, $condiAgeCod = FALSE) {

        $i = 0;
        $tbConi = new Application_Model_ConvenioItens();
        $tbAgen = new Application_Model_Agendamento();
        $tbGrad = new Application_Model_GradeDia();
        $intervalo = $tbGrad->getIntervaloDia($coni_codigo, $data_selecionada, $condiAgeCod);
        $tbGrah = new Application_Model_GradeHorario();

        $tempos = array();
        foreach ($horarios as $horario) {
            $horario->hora_inicial;
            $horario->hora_final;
            // echo $quantidades[$i]."<br/>";
            for ($j = 0; $j < (int) round($quantidades[$i]); $j++) {
                $hora = explode(":", $horario->hora_inicial);
                $horario->hora_inicial = $hora[0] . ":" . $hora[1]; // funcao para a primeira hora nao ficar como 00:00:00
                $horario_bloqueado = $tbGrah->getHorarioCancelado($horario->hora_inicial, $coni_codigo, $data_selecionada);
                $quantidade_agendada = $tbAgen->getAgendamentosPorHorario($horario->hora_inicial, $coni_codigo, $data_selecionada);
                if ($horario_bloqueado) {
                    $tempos[$horario->hora_inicial] = $horario_bloqueado->quantidade . "|BLOQUEADO:  " . ($horario_bloqueado->grah_motivo == "" ? "Sem Motivo" : "$horario_bloqueado->grah_motivo");
                } else {
                    $tempos[$horario->hora_inicial] = $quantidade_agendada->quantidade . "|" . $quantidade_agendada->age_paciente;
                }
                $novo_horario = mktime($hora[0], $hora[1] + $intervalo);
                $horario->hora_inicial = date("H:i", $novo_horario);
                $hora_minutos_inicial += $intervalo;
            }
            $i++;
        }
        return $tempos;
    }

    public function montaArrayDeHorarios($quantidades = FALSE, $horarios = FALSE, $coni_codigo = FALSE, $data_selecionada, $condiAgeCod = FALSE) {
        $i = 0;
        $tbConi = new Application_Model_ConvenioItens();
        $tbAgen = new Application_Model_Agendamento();
        $tbGrad = new Application_Model_GradeDia();
        $intervalo = $tbGrad->getIntervaloDia($coni_codigo, $data_selecionada, $condiAgeCod);
        $tbGrah = new Application_Model_GradeHorario();

        $tempos = array();
        foreach ($horarios as $horario) {
            // Pega a hora inicial e a hora final
            $horario->hora_inicial;
            $horario->hora_final;
            // echo $quantidades[$i]."<br/>";
            for ($j = 0; $j < (int) round($quantidades[$i]); $j++) {
                $hora = explode(":", $horario->hora_inicial);
                // funcao para a primeira hora nao ficar como 00:00:00
                $horario->hora_inicial = $hora[0] . ":" . $hora[1];
                array_push($tempos, $horario->hora_inicial);
                $novo_horario = mktime($hora[0], $hora[1] + $intervalo);
                $horario->hora_inicial = date("H:i", $novo_horario);
                $hora_minutos_inicial += $intervalo;
            }
            $i++;
        }
        //echo "<pre>";
        //print_r($tempos);
        //echo "</pre>";
        return $tempos;
    }

    public function ValidaData($dat) {
        $data = explode("/", "$dat"); // fatia a string $dat em pedados, usando / como referência
        $d = $data[0];
        $m = $data[1];
        $y = $data[2];

        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        return checkdate($m, $d, $y);
    }
    
    public function validaCnsGeral($cns) {
        if ((substr($cns, 0,1)!="7") && (substr($cns, 0,1)!="8") && (substr($cns, 0,1)!="9")) {
            return $this->validaCNS($cns);
        } else {
            return $this->validaCNS_PROVISORIO($cns);
        }
    }

    public function validaCNS($cns) {
        if ((strlen(trim($cns))) != 15) {
            return false;
        }
        $pis = substr($cns, 0, 11);
        $soma = (((substr($pis, 0, 1)) * 15) +
                ((substr($pis, 1, 1)) * 14) +
                ((substr($pis, 2, 1)) * 13) +
                ((substr($pis, 3, 1)) * 12) +
                ((substr($pis, 4, 1)) * 11) +
                ((substr($pis, 5, 1)) * 10) +
                ((substr($pis, 6, 1)) * 9) +
                ((substr($pis, 7, 1)) * 8) +
                ((substr($pis, 8, 1)) * 7) +
                ((substr($pis, 9, 1)) * 6) +
                ((substr($pis, 10, 1)) * 5));
        $resto = fmod($soma, 11);
        $dv = 11 - $resto;
        if ($dv == 11) {
            $dv = 0;
        }
        if ($dv == 10) {
            $soma = ((((substr($pis, 0, 1)) * 15) +
                    ((substr($pis, 1, 1)) * 14) +
                    ((substr($pis, 2, 1)) * 13) +
                    ((substr($pis, 3, 1)) * 12) +
                    ((substr($pis, 4, 1)) * 11) +
                    ((substr($pis, 5, 1)) * 10) +
                    ((substr($pis, 6, 1)) * 9) +
                    ((substr($pis, 7, 1)) * 8) +
                    ((substr($pis, 8, 1)) * 7) +
                    ((substr($pis, 9, 1)) * 6) +
                    ((substr($pis, 10, 1)) * 5)) + 2);
            $resto = fmod($soma, 11);
            $dv = 11 - $resto;
            $resultado = $pis . "001" . $dv;
        } else {
            $resultado = $pis . "000" . $dv;
        }
        if ($cns != $resultado) {
            return false;
        } else {
            return true;
        }
    }

    public function validaCNS_PROVISORIO($cns) {
        if ((strlen(trim($cns))) != 15) {
            return false;
        }
        $soma = (((substr($cns, 0, 1)) * 15) +
                ((substr($cns, 1, 1)) * 14) +
                ((substr($cns, 2, 1)) * 13) +
                ((substr($cns, 3, 1)) * 12) +
                ((substr($cns, 4, 1)) * 11) +
                ((substr($cns, 5, 1)) * 10) +
                ((substr($cns, 6, 1)) * 9) +
                ((substr($cns, 7, 1)) * 8) +
                ((substr($cns, 8, 1)) * 7) +
                ((substr($cns, 9, 1)) * 6) +
                ((substr($cns, 10, 1)) * 5) +
                ((substr($cns, 11, 1)) * 4) +
                ((substr($cns, 12, 1)) * 3) +
                ((substr($cns, 13, 1)) * 2) +
                ((substr($cns, 14, 1)) * 1));
        $resto = fmod($soma, 11);
        if ($resto != 0) {
            return false;
        } else {
            return true;
        }
    }

    public function validaIne($ine){
        // Se quantidade de caracters for igual a 10 e diferente de vazio é valido
        if (strlen($ine) == 10 && $ine!="") {
            return true;
        } else {
            return false;            
        }
    }

}
