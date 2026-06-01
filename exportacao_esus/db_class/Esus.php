<?php
#namespace esus\banco_cidadao;
#include_once $_SESSION['root'].$_SESSION['modulo']."global.php";

include_once getcwd()."/conexao_db.php";


class BancoEsus {

	public function validaRespEnvio(){
		$sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
		$query = pg_query($sql) or die(pg_last_error());
		return pg_num_rows($query);
	}

	public function validaCnsBanco($cns){
		if ((substr($cns,0,1)!="7") && (substr($cns,0,1)!="8") && (substr($cns,0,1)!="9")){
			return validaCNS(vlr_cns);
		}else{
			return ValidaCNS_PROV(vlr_cns);
		}
	}

	public function validaCNS($cns) {
		if ((strlen(trim($cns))) != 15) {
			return false;
        }
        
		$pis = substr($cns,0,11);
		$soma = (((substr($pis, 0,1)) * 15) +
		         ((substr($pis, 1,1)) * 14) +
			     ((substr($pis, 2,1)) * 13) +
			     ((substr($pis, 3,1)) * 12) +
			     ((substr($pis, 4,1)) * 11) +
			     ((substr($pis, 5,1)) * 10) +
			     ((substr($pis, 6,1)) * 9) +
			     ((substr($pis, 7,1)) * 8) +
			     ((substr($pis, 8,1)) * 7) +
			     ((substr($pis, 9,1)) * 6) +
			     ((substr($pis, 10,1)) * 5));
		$resto = fmod($soma, 11);
        $dv = 11  - $resto;
        
		if ($dv == 11) {
			$dv = 0;
        }
        
		if ($dv == 10) {
			$soma = ((((substr($pis, 0,1)) * 15) +
		              ((substr($pis, 1,1)) * 14) +
			          ((substr($pis, 2,1)) * 13) +
			          ((substr($pis, 3,1)) * 12) +
			          ((substr($pis, 4,1)) * 11) +
			          ((substr($pis, 5,1)) * 10) +
			          ((substr($pis, 6,1)) * 9) +
			          ((substr($pis, 7,1)) * 8) +
			          ((substr($pis, 8,1)) * 7) +
			          ((substr($pis, 9,1)) * 6) +
			          ((substr($pis, 10,1)) * 5)) + 2);
			$resto = fmod($soma, 11);
			$dv = 11  - $resto;
			$resultado = $pis."001".$dv;
		} else {
			$resultado = $pis."000".$dv;
        }
        
		if ($cns != $resultado){
            return false;
        } else {
        	return true;
		}
	}

	function validaCNS_PROVISORIO($cns) {
		if ((strlen(trim($cns))) != 15) {
            return false;
        }
        
		$soma = (((substr($cns,0,1)) * 15) +
		((substr($cns,1,1)) * 14) +
		((substr($cns,2,1)) * 13) +
		((substr($cns,3,1)) * 12) +
		((substr($cns,4,1)) * 11) +
		((substr($cns,5,1)) * 10) +
		((substr($cns,6,1)) * 9) +
		((substr($cns,7,1)) * 8) +
		((substr($cns,8,1)) * 7) +
		((substr($cns,9,1)) * 6) +
		((substr($cns,10,1)) * 5) +
		((substr($cns,11,1)) * 4) +
		((substr($cns,12,1)) * 3) +
		((substr($cns,13,1)) * 2) +
        ((substr($cns,14,1)) * 1));
        
        $resto = fmod($soma,11);
        
		if ($resto != 0) {
			return false;
		} else {
			return true;
		}
	}
}