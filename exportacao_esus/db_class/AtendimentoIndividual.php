<?php
#namespace esus\banco_cidadao;
//include_once $_SESSION['root'].$_SESSION['modulo']."global.php";
include_once getcwd()."/conexao_db.php";

class BancoAtendimentoIndividual {

	public function getDados(){
		$sql = "SELECT nu_ine,ate_tipo,eai_dtnascimento,esus.co_local_atend,eai_tipo_atendimento,esus.ate_codigo,eai_num_cartao_sus,eai_numprontuario,eai_sexo,turno,ate_peso,ate_altura,natal.aleitamento_materno,
                dum,idade_gestacional,usu_ate_dom_mod,vacinacao_em_dia,ate_nasf_aval,ate_nasf_proc,ate_nasf_presc,gravidez_planejada,gestas_previas,partos,ate_rac_saude,ate_perimetro_cefalico
                FROM esus_atendimento_individual as esus
                INNER JOIN atendimento as ate on ate.ate_codigo = esus.ate_codigo
                LEFT JOIN atendimento_prenatal as natal on ate.ate_codigo = natal.ate_codigo
                INNER JOIN unidade as uni on uni.uni_codigo = ate.uni_codigo
                LEFT JOIN tb_equipe as te on te.co_seq_equipe = ate.co_equipe
                WHERE cnes_tp_unid_id in ('01','02') and uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		return pg_fetch_all($query);
	}

	public function getQtdRegistros(){
		$sql = "SELECT nu_ine,ate_tipo,eai_dtnascimento,esus.co_local_atend,eai_tipo_atendimento,esus.ate_codigo,eai_num_cartao_sus,eai_numprontuario,eai_sexo,turno,ate_peso,ate_altura,natal.aleitamento_materno,
                dum,idade_gestacional,usu_ate_dom_mod,vacinacao_em_dia,ate_nasf_aval,ate_nasf_proc,ate_nasf_presc,gravidez_planejada,gestas_previas,partos,ate_rac_saude,ate_perimetro_cefalico
                FROM esus_atendimento_individual as esus
                INNER JOIN atendimento as ate on ate.ate_codigo = esus.ate_codigo
                LEFT JOIN atendimento_prenatal as natal on ate.ate_codigo = natal.ate_codigo
                INNER JOIN unidade as uni on uni.uni_codigo = ate.uni_codigo
                LEFT JOIN tb_equipe as te on te.co_seq_equipe = ate.co_equipe
                WHERE cnes_tp_unid_id in ('01','02') and uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		$numRegistro = pg_num_rows($query);
		return $numRegistro;
	}

	public function getCiaps($ateCodigo){
		$sql = "SELECT
					tbc.co_ciap
				FROM
					rl_cds_atend_individual_ciap as rlai
				INNER JOIN
					tb_ciap AS tbc ON rlai.co_ciap=tbc.co_seq_ciap
				WHERE
					rlai.ate_codigo = $ateCodigo";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getCondutas($ateCodigo){
		$sql = "SELECT
					tbtc.co_cds_tipo_conduta
				FROM
					rl_cds_atend_individual_condut as rlac
				INNER JOIN
					tb_cds_tipo_conduta AS tbtc ON rlac.tp_cds_conduta=tbtc.co_cds_tipo_conduta
				WHERE
					rlac.ate_codigo = $ateCodigo";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getExames($ateCodigo){
		$sql = "SELECT
					proc.proc_codigo_sus
				FROM
					requisicao_exames AS re
				INNER JOIN
					procedimento AS proc ON re.proc_codigo=proc.proc_codigo
				WHERE
					re.ate_codigo = $ateCodigo";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function atualizaStatus($uuid,$codigo){
		$sql = "UPDATE esus_atendimento_individual SET uuid_ficha = '".$uuid."' WHERE ate_codigo = '".$codigo."'";
		$query = pg_query($sql);
	}

}
?>
