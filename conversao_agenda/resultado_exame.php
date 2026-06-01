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
	$sqlCadastroDoExame = "SELECT * FROM cadastrodoexame WHERE agex_codigo = $regAgenda[age_codigo] ORDER by cad_exame";
	$queryCadastroDoExame = pg_query($sqlCadastroDoExame);
	//echo $sqlCadastroDoExame."</br>";
	while($regCadastroDoExame = pg_fetch_array($queryCadastroDoExame)){
		$sqlItensDoExame = "SELECT * 
							  FROM itensdoexame 
							 WHERE cad_exame = $regCadastroDoExame[cad_exame] 
							   AND itx_status = 'C'
							 ORDER BY itx_codigo";
		$queryItensDoExame = pg_query($sqlItensDoExame);
		while($regItensDoExame = pg_fetch_array($queryItensDoExame)){
			$sqlAgendamentoLista = "SELECT * 
									  FROM agendamento_exame_lista 
									 WHERE proc_codigo = $regItensDoExame[proc_codigo] 
									   AND agex_codigo = $regAgenda[age_codigo]
									   AND agexl_codigo <> 22";
			//echo $sqlAgendamentoLista."</br>";
			$queryAgendamentoLista = pg_query($sqlAgendamentoLista) or die($sqlAgendamentoLista.pg_last_error());
			while($regAgendamentoLista = pg_fetch_array($queryAgendamentoLista)){
				echo $regItensDoExame[itx_data]."a";
				if($par = $regAgendamentoLista[agexl_codigo]){
					$insertColeta = "INSERT INTO coleta(agei_codigo,
														col_data_entrega,
														col_data_coleta)
												 VALUES ($regAgendamentoLista[agexl_codigo],
												 		 '$regCadastroDoExame[cad_previsaoentrega]',
												 		 ".($regItensDoExame[itx_data] == "" ? "'$regAgendamentoLista[agexl_data]'" : "'$regItensDoExame[itx_data]'").")";
					$queryColeta = pg_query($insertColeta) or die($insertColeta.pg_last_error());
					$par = $regAgendamentoLista[agexl_codigo];
				}
				$sqlResultadoDoExame = "SELECT * 
										  FROM resultadoexame
										 WHERE itx_codigo = $regItensDoExame[itx_codigo]
										   AND proc_codigo = $regItensDoExame[proc_codigo]";
				$queryResultadoDoExame = pg_query($sqlResultadoDoExame) or die($sqlResultadoDoExame.pg_last_error());
				while($regResultadoDoExame = pg_fetch_array($queryResultadoDoExame)){
					$updateExame = "UPDATE resultadoexame 
									   SET agei_codigo = $regAgendamentoLista[agexl_codigo] 
									 WHERE res_codigo = $regResultadoDoExame[res_codigo]";
					$queryUpdateExame = pg_query($updateExame) or die($updateExame.pg_last_error());
					echo $updateExame."<br/>";
				}
			}
		}
	}	
}
echo "Fuck The System";
?>