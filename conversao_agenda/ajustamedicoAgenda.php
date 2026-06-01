<?php
include "../global.php";
include "../../WebSocialComum/library/php/funcoes.db.php";
set_time_limit(0);

$sqlAgenda = "SELECT *
					 FROM agenda
					 ORDER BY age_codigo";
$queryAgenda = pg_query($sqlAgenda);
//echo pg_num_rows($queryAgenda);
$par = 0;
while($regAgenda = pg_fetch_array($queryAgenda)){
	$sqlCadastroDoExame = "SELECT * FROM cadastrodoexame WHERE agex_codigo = $regAgenda[age_codigo]  ORDER by cad_exame";
//AND cad_exame not in (2163,2159,2277,2382)	
	$queryCadastroDoExame = pg_query($sqlCadastroDoExame);
	echo $sqlCadastroDoExame."</br>";
	while($registroPegaCadExame = pg_fetch_array($queryCadastroDoExame)){
		$usuarios = "select * from usuarios where usr_codigo = $registroPegaCadExame[med_codigo]";
		$queryUsuarios = pg_query($usuarios);
		$numUsuarios = pg_num_rows($queryUsuarios);
		if($numUsuarios == 0 && $registroPegaCadExame[cad_medico_externo] == "E"){
			$updateAgenda1 = "UPDATE agenda 
			    					 SET med_codigo = ".($registroPegaCadExame[cad_medico_externo] == "E" ? ($registroPegaCadExame[med_codigo] == "" ? "NULL" : $registroPegaCadExame[med_codigo]) : "null").",
			    					 	 usr_codigo_medico = ".($registroPegaCadExame[cad_medico_externo] != "E" ? ($registroPegaCadExame[med_codigo] == "" ? "NULL" : $registroPegaCadExame[med_codigo])  : "NULL")."
			    				   WHERE age_codigo = $regAgenda[age_codigo]";
			$queryUpdateAgenda1 = pg_query($updateAgenda1) or die($updateAgenda1.pg_last_error());
		}
	}
}
	