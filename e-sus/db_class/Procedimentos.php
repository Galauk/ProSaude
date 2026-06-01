<?php
    //namespace esus\banco_cidadao;
    include_once $_SESSION['root'].$_SESSION['modulo']."global.php";
//include_once "../../global.php";
    class BancoProcedimentos {

		public function getDadosProcedimentos(){
			$sql = "SELECT DISTINCT
						efp.age_codigo,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0301100039'
						) AS numTotalAfericaoPa,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0214010015'
						) AS numTotalGlicemiaCapilar,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0101040024'
						) AS numTotalMedicaoAlturaPeso,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0201020041'
						) AS numTotalExame,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0401010023'
						) AS numTotalCurativo,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = 'ABPG034'
						) AS numTotalAfericaoTemperatura,
						efp.co_local_atend,
						efp.efp_profissional_cns,
						efp.efp_cbo_codigo_2002,
						efp.efp_cnes,
						efp.efp_ine,
						efp_dtatendimento,
						efp.efp_codigo_ibge_mun,
						efp.efp_num_cartao_sus,
						efp.efp_dtnascimento,
						efp.efp_sexo,
						efp.efp_tipo_dado_serializado,
						efp.efp_dtcadastro,
						efp.efp_codigo
					FROM
						esus_ficha_procedimento AS efp
					WHERE
						uuid_ficha IS NULL OR uuid_ficha = ''
					GROUP BY
						age_codigo,co_local_atend,
						efp_profissional_cns,
						efp_cbo_codigo_2002,
						efp_cnes,
						efp_ine,
						efp_dtatendimento,
						efp_codigo_ibge_mun,
						efp_num_cartao_sus,
						efp_dtnascimento,
						efp_sexo,
						efp_tipo_dado_serializado,
						efp_dtcadastro,
						efp.efp_codigo";
            $query = pg_query($sql) or die(pg_last_error());
            return pg_fetch_all($query);
        }

		public function getCountDadosProcedimentos(){
			$sql = "SELECT DISTINCT
						efp.age_codigo,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0301100039'
						) AS numTotalAfericaoPa,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0214010015'
						) AS numTotalGlicemiaCapilar,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0101040024'
						) AS numTotalMedicaoAlturaPeso,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0201020041'
						) AS numTotalExame,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = '0401010023'
						) AS numTotalCurativo,
						(SELECT COUNT(*) FROM procedimento_atendimento AS pat
							INNER JOIN
								atendimento AS ate ON pat.ate_codigo=ate.ate_codigo
							INNER JOIN
								agendamento AS age ON ate.age_codigo=age.age_codigo
							INNER JOIN
								procedimento AS proc ON pat.proc_codigo=proc.proc_codigo
							WHERE
								age.age_codigo = efp.age_codigo AND
								proc.proc_codigo_sus = 'ABPG034'
						) AS numTotalAfericaoTemperatura,
						efp.co_local_atend,
						efp.efp_profissional_cns,
						efp.efp_cbo_codigo_2002,
						efp.efp_cnes,
						efp.efp_ine,
						efp.efp_dtatendimento,
						efp.efp_codigo_ibge_mun,
						efp.efp_num_cartao_sus,
						efp.efp_dtnascimento,
						efp.efp_sexo,
						efp.efp_tipo_dado_serializado,
						efp.efp_dtcadastro,
						efp.efp_codigo
					FROM
						esus_ficha_procedimento AS efp
					WHERE
						uuid_ficha IS NULL OR uuid_ficha = ''
					GROUP BY
						age_codigo,co_local_atend,
						efp_profissional_cns,
						efp_cbo_codigo_2002,
						efp_cnes,
						efp_ine,
						efp_dtatendimento,
						efp_codigo_ibge_mun,
						efp_num_cartao_sus,
						efp_dtnascimento,
						efp_sexo,
						efp_tipo_dado_serializado,
						efp_dtcadastro,
						efp_codigo";
            $query = pg_query($sql) or die(pg_last_error());
            return pg_num_rows($query);
        }

		public function getProcedimentosSigtap($ageCodigo){
			$sql = "SELECT
						proc.proc_codigo_sus
					FROM
						esus_ficha_procedimento AS esf
					INNER JOIN
						procedimento AS proc ON esf.proc_codigo=proc.proc_codigo
					WHERE
						esf.age_codigo = '".$ageCodigo."'";
			$query = pg_query($sql);
			return pg_fetch_all($query);
		}

		public function atualizaStatus($uuid,$codigo){
			$sql = "UPDATE esus_ficha_procedimento SET uuid_ficha = '".$uuid."' WHERE age_codigo = '".$codigo."'";
			$query = pg_query($sql);
		}

    }


?>
