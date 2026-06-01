<?php
include "../global.php";
include "../../WebSocialComum/library/php/funcoes.db.php";
set_time_limit(0);
$sqlAgendamentoExame = "SELECT agex_codigo,
                               usu_codigo,
                               agex_status,
                               agex_data_cad
                          FROM agendamento_exame";
$queryAgendamentoExame = pg_query($sqlAgendamentoExame);
$i = 0;
$med_codigo = 0;
$agendaVer = 0;
$it = 0;
$cont = 0;
$j = 0;
while($registro = pg_fetch_array($queryAgendamentoExame)){
    $i++;
    $insertAgenda = "INSERT 
                       INTO agenda(age_codigo,
                                   usu_codigo,
                                   usr_data_insert)
                             VALUES ($registro[agex_codigo],
                                     $registro[usu_codigo],
                                     '$registro[agex_data_cad]')";
    $queryInsert = pg_query($insertAgenda) or die($insertAgenda.pg_last_error());
    $sqlAgendamentoExameLista = "SELECT agexl_codigo,
                                        usu_codigo,
                                        med_codigo,
                                        proc_codigo,
                                        agexl_data,
                                        agexl_hora,
                                        agexl_status,
                                        usr_codigo_cad,
                                        uni_codigo
                                   FROM agendamento_exame_lista
                                  WHERE agex_codigo = $registro[agex_codigo]";
    $queryAgendamentoExameLista = pg_query($sqlAgendamentoExameLista) or die($sqlAgendamentoExameLista.pg_last_error());
    
    while($registroLista = pg_fetch_array($queryAgendamentoExameLista)){
        if($med_codigo != $registroLista[med_codigo]){
        	$med_codigo = $registroLista[med_codigo];
	        $conv_codigo = nextVal("convenio_conv_codigo_seq");
	        $insertConvenio = "INSERT 
	                             INTO convenio(conv_codigo,
	                                           med_codigo,
	                                           uni_codigo,
	                                           conv_sabado,
	                                           conv_domingo,
	                                           conv_status)
	                                    VALUES($conv_codigo,
	                                           $registroLista[med_codigo],
	                                           $registroLista[uni_codigo],
	                                           'f',
	                                           't',
	                                           't')";
	        $queryInsertConvenio = pg_query($insertConvenio) or die($insertConvenio.pg_last_error());
        }
        if($cont == 0){
	        $sqlExames = "select distinct proc_codigo from tipodeexame";
	        $queryExames = pg_query($sqlExames) or die($sqlExames.pg_last_error());
	        while($regExames = pg_fetch_array($queryExames)){
		        $coni_codigo = nextVal("convenio_itens_coni_codigo_seq");
		        $insertItensConverio = "INSERT 
		                                  INTO convenio_itens(coni_codigo,
		                                                      proc_codigo,
		                                                      coni_tipo_origem,
		                                                      coni_tipo_prestador,
		                                                      usr_codigo,
		                                                      conv_codigo
		                                                      )
		                                               VALUES ($coni_codigo,
		                                                       $regExames[proc_codigo],
		                                                       'U',
		                                                       'Q',
		                                                        1,
		                                                        $conv_codigo)";
		        $queryItensConvenio = pg_query($insertItensConverio) or die($insertItensConverio.pg_last_error());
	        }
	        $cont++;
        }
        //if($j != $coni_codigo){
        $pegaConvenio = "select * from convenio_itens where proc_codigo = $registroLista[proc_codigo]";
        $queryConvenio = pg_query($pegaConvenio);
        $regConvenio = pg_fetch_array($queryConvenio);
        if($regConvenio[proc_codigo] != ""){
	        $insertAgendaLista = "INSERT 
	                                INTO agenda_itens(agei_codigo,
	                                                  age_codigo,
	                                                  agei_data,
	                                                  agei_hora,
	                                                  coni_codigo,
	                                                  agei_status)
	                                           VALUES ($registroLista[agexl_codigo],
	                                                   $registro[agex_codigo],
	                                                   '$registroLista[agexl_data]',
	                                                   ".($registroLista[agexl_hora] == "" ? "null" : "'$registroLista[agexl_hora]'").",
	                                                   $regConvenio[coni_codigo],
	                                                   '$registroLista[agexl_status]')";
	        $queryAgendaLista = pg_query($insertAgendaLista) or die($insertAgendaLista.pg_last_error());
        }
        //$j = $coni_codigo;
        //}
	    if($agendaVer != $registro[agex_codigo]){
		    $updateAgenda1 = "UPDATE agenda 
		    					 SET med_codigo = ".($registroPegaCadExame[cad_medico_externo] == "E" ? ($registroPegaCadExame[med_codigo] == "" ? "NULL" : $registroPegaCadExame[med_codigo]) : "null").",
		    					 	 usr_codigo_medico = ".($registroPegaCadExame[cad_medico_externo] != "E" ? ($registroPegaCadExame[med_codigo] == "" ? "NULL" : $registroPegaCadExame[med_codigo])  : "NULL")."
		    				   WHERE age_codigo = $registro[agex_codigo]";
		    $queryUpdateAgenda1 = pg_query($updateAgenda1) or die($updateAgenda1.pg_last_error());
		  
	    }
        $updateAgenda = "UPDATE agenda 
                            SET usr_codigo = $registroLista[usr_codigo_cad],
                                age_status = '$registroLista[agexl_status]'
                          WHERE age_codigo = $registro[agex_codigo]";
        $queryUpdateAgenda = pg_query($updateAgenda) or die($updateAgenda.pg_last_error());
    }
}
echo "aa";
echo $i;
?>
