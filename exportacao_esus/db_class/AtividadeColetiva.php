<?php
#namespace esus\banco_cidadao;
#include_once $_SESSION['root'].$_SESSION['modulo']."global.php";

include_once getcwd()."/conexao_db.php";

class AtividadeColetiva {

	public function getDadosAtividadeColetiva(){
		$sql = "SELECT * FROM esus_atividade_coletiva WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		return pg_fetch_all($query);
	}

	public function numDadosAtividadeColetiva() {
		$sql = "SELECT * FROM esus_atividade_coletiva WHERE uuid_ficha IS NULL OR uuid_ficha = ''" ;
		$query = pg_query($sql) or die(pg_last_error());
		return pg_num_rows($query);
	}

	public function getDadosProfissional($codAtiv){
		$sql = "SELECT
                    rlacp.cbo,
                    usr.cnes_cod_cns
				FROM
                    rl_cds_ficha_ativ_col_prof AS rlacp
				INNER JOIN
                    usuarios AS usr ON rlacp.usr_codigo=usr.usr_codigo
				WHERE
		            co_cds_ficha_ativ_col = $codAtiv";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getCodigosTemas($codAtiv){
		$sql = "SELECT co_cds_ativ_col_tema FROM rl_cds_ficha_ativ_col_tema WHERE co_cds_ficha_ativ_col  = $codAtiv";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getCodigosPublicoAlvo($codAtiv){
		$sql = "SELECT co_cds_ativ_col_publico_alvo FROM rl_cds_ficha_ativ_col_pub_alvo WHERE co_cds_ficha_ativ_col  = $codAtiv";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getCodigosPratica($codAtiv){
		$sql = "SELECT co_cds_ativ_col_pratica FROM rl_cds_ficha_ativ_col_pratica WHERE co_cds_ficha_ativ_col  = $codAtiv";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getDadosParticipantes($codAtiv){
		$sql = "SELECT
					tbacp.dt_nascimento,
					tbacp.st_avaliacao_alterada,
					tbacp.nu_peso,
					tbacp.nu_altura,
					tbacp.st_cessou_habito_fumar,
					tbacp.st_abandonou_grupo,
					usu.usu_cartao_sus
				FROM
					tb_cds_ativ_col_participante AS tbacp
				INNER JOIN
					usuario AS usu ON tbacp.usu_codigo=usu.usu_codigo
				WHERE
					co_cds_ficha_ativ_col  = $codAtiv AND
					usu_cartao_sus <> ''";
		$query = pg_query($sql);
		return pg_fetch_all($query);
	}

	public function getDadosOriginadoraRemetente(){
		$sql = "SELECT * FROM esus_remente_originadora WHERE ero_status = 't'";
		$query = pg_query($sql);
		return pg_fetch_array($query);
	}

	public function atualizaStatus($uuid,$codigo){
		$sql = "UPDATE esus_atividade_coletiva SET uuid_ficha = '".$uuid."' WHERE co_cds_ficha_ativ_col = '".$codigo."'";
		$query = pg_query($sql);
	}
}