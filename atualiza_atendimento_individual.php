<?php 
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$sql = pg_query("
SELECT DISTINCT
				ate.ate_codigo,
				age.tat_codigo AS eai_tipo_atendimento,
				tbl.co_local_atend,
				usr.cnes_cod_cns AS eai_profissional_cns,
				esp.cod_cbo AS eai_cbo_codigo_2002,
				uni.uni_cnes AS eai_cnes,
				ate.ate_data AS eai_dtatendimento,
				uni.uni_codigo_ibge AS eai_codigo_ibge_mun,
				usu.usu_cartao_sus AS eai_num_cartao_sus,
				usu.usu_datanasc AS eai_dtnascimento,
				usu.usu_prontuario AS eai_numprontuario, 
				usu.usu_sexo
			FROM 
				atendimento AS ate
			INNER JOIN 
				agendamento AS age ON ate.age_codigo=age.age_codigo
			INNER JOIN 
				especialidade AS esp ON age.esp_codigo=esp.esp_codigo
			INNER JOIN 
				usuarios AS usr ON ate.med_codigo=usr.usr_codigo
			INNER JOIN
				usuario AS usu ON ate.usu_codigo=usu.usu_codigo
			INNER JOIN
				unidade AS uni ON ate.uni_codigo=uni.uni_codigo
			INNER JOIN 
				tb_local_atend AS tbl ON ate.co_local_atend=tbl.co_local_atend
			INNER JOIN
				rl_cds_atend_individual_ciap AS rlai ON ate.ate_codigo=rlai.ate_codigo
			INNER JOIN 
				rl_cds_atend_individual_condut AS rlaic ON ate.ate_codigo=rlaic.ate_codigo
			WHERE 
				ate_data >= '14/11/2018' and ate_data <= '16/01/2019'
");

while($rr = pg_fetch_array($sql)) {
			if ($rr['usu_sexo']='M') { 
				$eai_sexo = '0 L';
			} else {
				$eai_sexo = '1 L';
			}

echo "INSERT INTO esus_atendimento_individual(
					ate_codigo,
					co_local_atend,
					eai_profissional_cns,
					eai_cbo_codigo_2002,
					eai_cnes,
					eai_dtatendimento,
					eai_codigo_ibge_mun,
					eai_dtnascimento,
					eai_num_cartao_sus,
					eai_numprontuario,
					eai_tipo_atendimento,
					eai_sexo,
					eai_tipo_dado_serializado
				) VALUES (
					'$rr[ate_codigo]',
					'$rr[co_local_atend]',
					'$rr[eai_profissional_cns]',
					'$rr[eai_cbo_codigo_2002]',
					'$rr[eai_cnes]',
					'$rr[eai_dtatendimento]',
					'$rr[eai_codigo_ibge_mun]',
					'$rr[eai_dtnascimento]',
					'$rr[eai_num_cartao_sus]',
					'$rr[eai_numprontuario]',
					'$rr[eai_tipo_atendimento]',
					'$eai_sexo',
					'4 L');<br>";
}

?>

