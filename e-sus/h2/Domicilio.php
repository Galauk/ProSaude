<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="progresso.css">
<div class='warning'>IMPORTANDO DADOS E-SUS</div>
<div class="barra-area">
    <div class="barra">
		<div id='div_progress' class='progresso'>
		</div>
	</div>
</div>
<?php

include "Bd.php"; 
set_time_limit(100000000000);
define(CONN, $connection);
define(CONN_PG, $connectionPg);

// Passa o que deseja importar por parametro e importa
$variable = $_GET["importa"];
switch ($variable) {
	case "pais":
		importaPais();
	break;
	case "estado":
		importaEstados();
	break;
	case "tplocalidade":
		importaTipoLocalidades();
	break;
	case "cidades":
		importaCidades();
	break;
	case "bairros":
		importaBairros();
	break;
	case "baiesus":
		importaBairrosEsus();
	break;
	case "atubairro":
		atualizaBairros();
	break;
	case "tplogradouro":
		importaTipoLogradouro();
	break;
	case "tppatente":
		importaTituloPatente();
	break;
	case "tpparidade":
		importaTipoParidade();
	break;
	case "logradouro":
		importaLogradouros();
	break;
	case "endereco":
		importaEnderecos();
	break;
	case "domicilio":
		importaDomicilios();
	break;
	default:
	break;
}

function importaPais(){
	$sqlH2Pais = "SELECT * FROM TB_PAIS ";
	$queryH2Pais = pg_query(CONN,$sqlH2Pais) or die(pg_last_error());
	$qtdRegitsro = pg_num_rows($queryH2Pais);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$error = 0;
    pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowH2Pais = pg_fetch_array($queryH2Pais)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		$sqlPais = "SELECT 
						pais_codigo FROM pais 
					WHERE 
						 TRIM(UPPER(retira_acentos(pais_nome))) = '".trataDados($rowH2Pais["no_pais_portugues"])."' OR
						 TRIM(UPPER(retira_acentos(pais_nome))) = '".trataDados($rowH2Pais["no_pais_portugues_filtro"])."'";
		$queryPais = pg_query(CONN_PG,$sqlPais) or die (pg_last_error());
		$rowPais = pg_fetch_array($queryPais);
		$numPais = pg_num_rows($queryPais);
		if ($numPais > 0) {
			$sqlAtuPais = "UPDATE PAIS 
							SET sg_pais_2 = '".trataCaracteres($rowH2Pais["sg_pais_2"])."',
								sg_pais_3 = '".trataCaracteres($rowH2Pais["sg_pais_3"])."',
								no_pais_portugues = '".trataCaracteres($rowH2Pais["no_pais_portugues"])."',
								no_pais_ingles = '".trataCaracteres($rowH2Pais["no_pais_ingles"])."',
								no_pais_frances = '".trataCaracteres($rowH2Pais["no_pais_frances"])."',
								no_pais_portugues_filtro = '".trataCaracteres($rowH2Pais["no_pais_portugues_filtro"])."'
							WHERE
								pais_codigo = '".$rowPais["pais_codigo"]."'";
			$queryAtuPais = pg_query(CONN_PG,$sqlAtuPais) or die (pg_last_error());
			if(!$queryAtuPais){
				$error += 1; 
			}
		} else {
			$sqlInsPais = "INSERT INTO PAIS 
									(pais_nome, sg_pais_2, sg_pais_3, no_pais_portugues, no_pais_ingles, no_pais_frances, no_pais_portugues_filtro) 
								  VALUES
									 ('".trataCaracteres($rowH2Pais["no_pais_portugues"])."','".trataCaracteres($rowH2Pais["sg_pais_2"])."','".trataCaracteres($rowH2Pais["sg_pais_3"])."','".trataCaracteres($rowH2Pais["no_pais_portugues"])."','".trataCaracteres($rowH2Pais["no_pais_ingles"])."','".trataCaracteres($rowH2Pais["no_pais_frances"])."','".trataCaracteres($rowH2Pais["no_pais_portugues_filtro"])."')";
			$queryInsPais = pg_query(CONN_PG,$sqlInsPais) or die (pg_last_error());
			if(!$queryInsPais){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}


function importaEstados(){
	$sqlH2Est = "SELECT DISTINCT sg_uf, nu_cep, nu_dne, no_uf, no_identificador FROM TB_UF";
	$queryH2Est = pg_query(CONN,$sqlH2Est) or die(pg_last_error());
	$qtdRegitsro = pg_num_rows($queryH2Est);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$error = 0;
    pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowH2Est = pg_fetch_array($queryH2Est)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		$sqlEst = "SELECT 
						uf_codigo FROM estado 
					WHERE 
						 TRIM(UPPER(retira_acentos(uf_sigla))) = '".trataDados($rowH2Est["sg_uf"])."' OR
						 TRIM(UPPER(retira_acentos(uf_nome))) = '".trataDados($rowH2Est["no_uf"])."'";
		$queryEst = pg_query(CONN_PG,$sqlEst) or die (pg_last_error());
		$rowEst = pg_fetch_array($queryEst);
		$numEst = pg_num_rows($queryEst);
		if ($numEst > 0) {
			$sqlAtuEst = "UPDATE estado 
							SET uf_sigla = '".trataCaracteres($rowH2Est["sg_uf"])."',
								uf_nome = '".trataCaracteres($rowH2Est["no_uf"])."',
								pais_codigo = '010',
								uf_nu_cep = '".trataCaracteres($rowH2Est["nu_cep"])."',
								uf_nu_dne = '".trataCaracteres($rowH2Est["nu_dne"])."',
								uf_no_identificador = '".trataCaracteres($rowH2Est["no_identificador"])."'
							WHERE
								uf_codigo = '".$rowEst["uf_codigo"]."'";
			$queryAtuEst = pg_query(CONN_PG,$sqlAtuEst) or die (pg_last_error());
			if(!$queryAtuEst){
				$error += 1; 
			}
		} else {
			$sqlInsEst = "INSERT INTO estado 
									(uf_sigla, uf_nome, pais_codigo, uf_nu_cep, uf_nu_dne, uf_no_identificador) 
								  VALUES
									 ('".trataCaracteres($rowH2Est["sg_uf"])."','".trataCaracteres($rowH2Est["no_uf"])."','010','".trataCaracteres($rowH2Est["nu_cep"])."','".trataCaracteres($rowH2Est["nu_dne"])."','".trataCaracteres($rowH2Est["no_identificador"])."')";
			$queryInsEst = pg_query(CONN_PG,$sqlInsEst) or die (pg_last_error());
			if(!$queryInsEst){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaTipoLocalidades(){
	$sqlH2 = "SELECT * FROM TB_TIPO_LOCALIDADE";
	$queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
	$qtdRegitsro = pg_num_rows($queryH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$error = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($reg = pg_fetch_array($queryH2)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		$sql = "SELECT co_tipo_localidade FROM tb_tipo_localidade WHERE no_tipo_localidade = '".$reg["no_tipo_localidade"]."'";
		$query = pg_query(CONN_PG,$sql);
		$num = pg_num_rows($query);
		if ($num > 0) {
			$update = "UPDATE tb_tipo_localidade
						SET no_tipo_localidade = '".$reg["no_tipo_localidade"]."', 
							sg_tipo_localidade = '".$reg["sg_tipo_localidade"]."'
						WHERE
							co_tipo_localidade = '".$reg["co_tipo_localidade"]."'";
			$queryUpdate = pg_query(CONN_PG,$update) or die($insert);
			if(!$queryUpdate){
				$error += 1; 
			}
		} else {
			$insert = "INSERT INTO tb_tipo_localidade 
							(co_tipo_localidade, no_tipo_localidade, sg_tipo_localidade)
						 VALUES 
							($reg[co_tipo_localidade], '$reg[no_tipo_localidade]', '$reg[sg_tipo_localidade]')";
			$queryInsert = pg_query(CONN_PG,$insert) or die($insert);
			if(!$queryInsert){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaCidades(){

	$sqlH2Cid = "SELECT DISTINCT
					tbu.no_uf, tbu.sg_uf, tbl.nu_cep, tbl.nu_dne, tbl.no_localidade, tbl.nu_cep8, tbl.no_localidade_abreviatura, tbl.tp_localidade, 
					tbl.co_situacao_localidade, tbl.co_ibge, tbl.no_localidade_filtro 
				FROM 
					TB_LOCALIDADE AS tbl 
				INNER JOIN 
					TB_UF AS tbu ON tbl.co_uf=tbu.co_uf";

	$queryH2Cid = pg_query(CONN,$sqlH2Cid) or die(pg_last_error());

	$qtdRegitsro = pg_num_rows($queryH2Cid);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$error = 0;
	$cont = 0;
    pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowH2Cid = pg_fetch_array($queryH2Cid)){
	
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		$sqlEst = "SELECT 
						uf_codigo FROM estado 
					WHERE 
						 TRIM(UPPER(retira_acentos(uf_nome))) = '".trataDados($rowH2Cid["no_uf"])."' OR
						 TRIM(UPPER(retira_acentos(uf_sigla))) = '".trataDados($rowH2Cid["sg_uf"])."'";
		$queryEst = pg_query(CONN_PG,$sqlEst) or die (pg_last_error());
		$rowEst = pg_fetch_array($queryEst);
		
		$sqlCid = "SELECT 
						cid_codigo FROM cidade 
					WHERE 
						 TRIM(UPPER(retira_acentos(cid_nome))) = '".trataDados($rowH2Cid["no_localidade"])."'";
		$queryCid = pg_query(CONN_PG,$sqlCid) or die (pg_last_error());
		$rowCid = pg_fetch_array($queryCid);
		$numCid = pg_num_rows($queryCid);
		if ($numCid > 0) {
			$cont++;
			$sqlAtuCid = "UPDATE cidade 
							SET cid_nome = '".trataCaracteres($rowH2Cid["no_localidade"])."',
								uf_codigo = '".trataCaracteres($rowEst["uf_codigo"])."',
								cid_codigo_ibge = '".trataCaracteres($rowH2Cid["co_ibge"])."',
								uf_sigla = '".trataCaracteres($rowH2Cid["sg_uf"])."',
								cid_nu_cep = '".trataCaracteres($rowH2Cid["nu_cep"])."',
								cid_nu_dne = '".trataCaracteres($rowH2Cid["nu_dne"])."',
								cid_nome_abreviatura = '".trataCaracteres($rowH2Cid["no_localidade_abreviatura"])."',
								cid_nome_filtro = '".trataCaracteres($rowH2Cid["no_localidade_filtro"])."',
								tp_localidade = '".trataCaracteres($rowH2Cid["tp_localidade"])."'
							WHERE
								cid_codigo = '".$rowCid["cid_codigo"]."'";
			//die($sqlAtuCid);
			$queryAtuCid = pg_query(CONN_PG,$sqlAtuCid) or die (pg_last_error());
			if(!$queryAtuCid){
				$error += 1; 
			}
		} else {
			$cont++;
			$sqlInsCid = "INSERT INTO cidade 
									(cid_nome, uf_codigo, cid_codigo_ibge, uf_sigla, cid_nu_cep, cid_nu_dne, cid_nome_abreviatura, cid_nome_filtro, tp_localidade) 
								  VALUES
									 ('".trataCaracteres($rowH2Cid["no_localidade"])."','".trataCaracteres($rowEst["uf_codigo"])."','".trataCaracteres($rowH2Cid["co_ibge"])."','".trataCaracteres($rowH2Cid["sg_uf"])."','".trataCaracteres($rowH2Cid["nu_cep"])."','".trataCaracteres($rowH2Cid["nu_dne"])."','".trataCaracteres($rowH2Cid["no_localidade_abreviatura"])."','".trataCaracteres($rowH2Cid["no_localidade_filtro"])."','".trataCaracteres($rowH2Cid["tp_localidade"])."')";
			$queryInsCid = pg_query(CONN_PG,$sqlInsCid) or die (pg_last_error());
			if(!$queryInsCid){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaBairros(){
	// Remove acentos, espa�os e deixa tudo maiusculo
	$sqlAtuBairro = "UPDATE rua SET rua_bairro = TRIM(UPPER(retira_acentos(rua_bairro)))";
	$queryAtuBairro = pg_query(CONN_PG,$sqlAtuBairro);
	
	$sqlBairroPos = "SELECT DISTINCT cid_codigo, rua_bairro FROM rua WHERE cid_codigo IS NOT NULL";
	$queryBairroPos = pg_query(CONN_PG,$sqlBairroPos);
	$qtdRegitsro = pg_num_rows($queryBairroPos);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowBairroPos = pg_fetch_array($queryBairroPos)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		$sqlConfBairroPos = "SELECT 
								bai_codigo 
							FROM 
								bairro 
							WHERE 
								cid_codigo = '".$rowBairroPos["cid_codigo"]."' AND
								TRIM(UPPER(retira_acentos(bai_nome))) = '".$rowBairroPos["rua_bairro"]."'";
		$queryConfBairroPos = pg_query(CONN_PG,$sqlConfBairroPos);
		$rowBairrroConfPos = pg_fetch_array($queryConfBairroPos);
		$numConfBairroPos = pg_num_rows($queryConfBairroPos);
		if ($numConfBairroPos > 0) {
			$sqlUpdBairroPos = "UPDATE bairro
									SET cid_codigo = '".trataCaracteres($rowBairroPos["cid_codigo"])."',
										bai_nome = '".trataCaracteres($rowBairroPos["rua_bairro"])."'
									WHERE
										bai_codigo = '".$rowBairrroConfPos["bai_codigo"]."'";
			$queryUpdBairroPos = pg_query(CONN_PG,$sqlUpdBairroPos);
			if(!$queryUpdBairroPos){
				$error += 1; 
			}
		} else {
			$sqlInsBairroPos = "INSERT INTO bairro 
										(cid_codigo,bai_nome)
									VALUES
										('".trataCaracteres($rowBairroPos["cid_codigo"])."','".trataCaracteres($rowBairroPos["rua_bairro"])."')";
			$queryInsBairroPos = pg_query(CONN_PG,$sqlInsBairroPos) or die($sqlInsBairroPos);
			if(!$queryInsBairroPos){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaBairrosEsus(){
	$sqlBairroH2 = "SELECT DISTINCT
					  TRIM(UPPER(tbb.nu_cep)) AS nu_cep,
					  TRIM(UPPER(tbb.nu_dne)) AS nu_dne,
					  TRIM(UPPER(tbb.no_bairro)) AS no_bairro,
					  TRIM(UPPER(tbb.no_bairro_abreviatura)) AS no_bairro_abreviatura, 
					  TRIM(LOWER(tbb.no_bairro_filtro)) AS no_bairro_filtro,
					  TRIM(UPPER(tbl.no_localidade)) AS no_localidade,
					  TRIM(LOWER(tbl.no_localidade_filtro)) AS no_localidade_filtro,
					  TRIM(UPPER(tbl.co_ibge)) AS co_ibge
					FROM 
					  TB_BAIRRO AS tbb
					INNER JOIN 
					  TB_LOCALIDADE AS tbl ON tbb.co_localidade=tbl.co_localidade
					INNER JOIN 
						TB_UF AS tbu ON tbl.co_uf=tbu.co_uf ";
	$queryBairroH2 = pg_query(CONN,$sqlBairroH2);
	$qtdRegitsro = pg_num_rows($queryBairroH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$contErro = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowBairroH2 = pg_fetch_array($queryBairroH2)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
	
		$sqlCid = "SELECT 
						cid_codigo FROM cidade 
					WHERE 
						 TRIM(LOWER(retira_acentos(cid_nome))) = '".trataCaracteres($rowBairroH2["no_localidade_filtro"])."'";
		$queryCid = pg_query(CONN_PG,$sqlCid) or die ($sqlCid);
		$rowCid = pg_fetch_array($queryCid);
		$numCid = pg_num_rows($queryCid);
		if ($numCid > 0) {
		
			$sqlBairro = "SELECT 
							bai_codigo 
						  FROM 
							bairro 
						  WHERE 
							cid_codigo = '".$rowCid["cid_codigo"]."' AND
							TRIM(LOWER(retira_acentos(bai_nome))) = '".trataCaracteres($rowBairroH2["no_bairro_filtro"])."'";
			$queryBairro = pg_query(CONN_PG,$sqlBairro) or die($sqlBairro);
			$rowBairrro = pg_fetch_array($queryBairro);
			$numBairro = pg_num_rows($queryBairro);
		
			if ($numBairro > 0) {
				$sqlUpdBairro = "UPDATE bairro
										SET cid_codigo = '".trataCaracteres($rowCid["cid_codigo"])."',
											bai_co_cep = '".trataCaracteres($rowBairroH2["nu_cep"])."',
											bai_co_dne = '".trataCaracteres($rowBairroH2["nu_dne"])."',
											bai_nome = '".trataCaracteres($rowBairroH2["no_bairro"])."',
											bai_abreviatura = '".trataCaracteres($rowBairroH2["no_bairro_abreviatura"])."',
											bai_filtro = '".trataCaracteres($rowBairroH2["no_bairro_filtro"])."' 
										WHERE
											bai_codigo = '".$rowBairrro["bai_codigo"]."'";
				$queryUpdBairro = pg_query(CONN_PG,$sqlUpdBairro) or die ($sqlUpdBairro); 
				if(!$queryUpdBairro){
					$error += 1; 
				}
			} else {
				 $sqlInsBairro = "INSERT INTO bairro 
											(cid_codigo, bai_co_cep, bai_co_dne, bai_nome, bai_abreviatura, bai_filtro)
										VALUES
											('".trataCaracteres($rowCid["cid_codigo"])."','".trataCaracteres($rowBairroH2["nu_cep"])."','".trataCaracteres($rowBairroH2["nu_dne"])."','".trataCaracteres($rowBairroH2["no_bairro"])."','".trataCaracteres($rowBairroH2["no_bairro_abreviatura"])."','".trataCaracteres($rowBairroH2["no_bairro_filtro"])."')";
				$queryInsBairro = pg_query(CONN_PG,$sqlInsBairro) or die($sqlInsBairro);
				if(!$queryInsBairro){
					$error += 1; 
				}
			}
		} 
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
} 

function atualizaBairros(){
	$sqlBairroPos = "SELECT rua_codigo, cid_codigo, rua_bairro FROM rua WHERE cid_codigo IS NOT NULL";
	$queryBairroPos = pg_query(CONN_PG,$sqlBairroPos);
	$qtdRegitsro = pg_num_rows($queryBairroPos);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$cont = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowBairroPos = pg_fetch_array($queryBairroPos)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		$sqlConfBairroPos = "SELECT 
								bai_codigo 
							FROM 
								bairro 
							WHERE 
								cid_codigo = '".$rowBairroPos["cid_codigo"]."' AND
								TRIM(UPPER(retira_acentos(bai_nome))) = '".$rowBairroPos["rua_bairro"]."'";
		$queryConfBairroPos = pg_query(CONN_PG,$sqlConfBairroPos);
		$rowBairrroConfPos = pg_fetch_array($queryConfBairroPos);
		$numConfBairroPos = pg_num_rows($queryConfBairroPos);
		if ($numConfBairroPos > 0) {
			$updRua = "UPDATE rua SET bai_codigo = '".$rowBairrroConfPos["bai_codigo"]."' WHERE rua_codigo = '".$rowBairroPos["rua_codigo"]."'";
			$queryUpdRua = pg_query(CONN_PG,$updRua);	
		} else {
			echo  $rowBairroPos["rua_bairro"]."-".$rowBairroPos["cid_codigo"]."<br />";
		}
		if(!$queryUpdRua){
			$error += 1; 
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaTipoLogradouro(){
	$sqlTlH2 = "SELECT 
					  TRIM(UPPER(tbtl.nu_cep)) AS nu_cep,
					  TRIM(UPPER(tbtl.nu_dne)) AS nu_dne,
					  TRIM(UPPER(tbtl.no_tipo_logradouro)) AS no_tipo_logradouro,
					  TRIM(UPPER(tbtl.no_tipo_logradouro_abreviatura)) AS no_tipo_logradouro_abreviatura,
					  TRIM(LOWER(tbtl.no_tipo_logradouro_filtro)) AS no_tipo_logradouro_filtro
					FROM 
					  TB_TIPO_LOGRADOURO AS tbtl ";
	$queryTlH2 = pg_query(CONN,$sqlTlH2);
	$qtdRegitsro = pg_num_rows($queryTlH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$contigual = 0;
	$cont = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowTlH2 = pg_fetch_array($queryTlH2)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
	
		$sql = "SELECT co_tipo_logradouro FROM tb_ms_tipo_logradouro WHERE co_tipo_logradouro = '".$rowTlH2["nu_dne"]."'";
		$query = pg_query(CONN_PG,$sql) or die ($sql);
		$num = pg_num_rows($query);
		$row = pg_fetch_array($query);
		if ($num > 0) {
			$update = "UPDATE tb_ms_tipo_logradouro
						SET ds_tipo_logradouro = '".trataCaracteres($rowTlH2["no_tipo_logradouro"])."', 
							ds_tipo_logradouro_abrev = '".trataCaracteres($rowTlH2["no_tipo_logradouro_abreviatura"])."',
							ds_nu_cep = '".trataCaracteres($rowTlH2["nu_cep"])."',
							ds_nu_dne = '".trataCaracteres($rowTlH2["nu_dne"])."',
							ds_tipo_logradouro_filtro = '".trataCaracteres($rowTlH2["no_tipo_logradouro_filtro"])."'
						WHERE
							co_tipo_logradouro = '".$row["co_tipo_logradouro"]."'";
			$queryUpdate = pg_query(CONN_PG,$update) or die($update);
			if(!$queryUpdate){
				$error += 1; 
			}
		} else {
			$insert = "INSERT INTO tb_ms_tipo_logradouro 
							(co_tipo_logradouro, ds_tipo_logradouro, ds_tipo_logradouro_abrev, ds_nu_cep, ds_nu_dne, ds_tipo_logradouro_filtro)
						 VALUES 
							('".trataCaracteres($rowTlH2["nu_dne"])."','".trataCaracteres($rowTlH2["no_tipo_logradouro"])."', '".trataCaracteres($rowTlH2["no_tipo_logradouro_abreviatura"])."', '".trataCaracteres($rowTlH2["nu_cep"])."', '".trataCaracteres($rowTlH2["nu_dne"])."', '".trataCaracteres($rowTlH2["no_tipo_logradouro_filtro"])."')";
			$queryInsert = pg_query(CONN_PG,$insert) or die($insert);
			if(!$queryInsert){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaTituloPatente(){
	$sqlTlH2 = "SELECT * FROM TB_TITULO_PATENTE";
	$queryTlH2 = pg_query(CONN,$sqlTlH2);
	$qtdRegitsro = pg_num_rows($queryTlH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$contigual = 0;
	$cont = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowTlH2 = pg_fetch_array($queryTlH2)){
	
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
	
		$sql = "SELECT co_titulo_patente FROM tb_titulo_patente WHERE nu_dne = '".$rowTlH2["nu_dne"]."'";
		$query = pg_query(CONN_PG,$sql) or die ($sql);
		$num = pg_num_rows($query);
		$row = pg_fetch_array($query);
		if ($num > 0) {
			$update = "UPDATE tb_titulo_patente
						SET nu_cep = '".trataCaracteres($rowTlH2["nu_cep"])."', 
							nu_dne = '".trataCaracteres($rowTlH2["nu_dne"])."',
							no_titulo_patente = '".trataCaracteres($rowTlH2["no_titulo_patente"])."',
							no_titulo_patente_abreviatura = '".trataCaracteres($rowTlH2["no_titulo_patente_abreviatura"])."'
						WHERE
							co_titulo_patente = '".$row["co_titulo_patente"]."'";
			$queryUpdate = pg_query(CONN_PG,$update) or die($update);
			if(!$queryUpdate){
				$error += 1; 
			}
		} else {
			$insert = "INSERT INTO tb_titulo_patente 
							(nu_cep, nu_dne, no_titulo_patente, no_titulo_patente_abreviatura)
						 VALUES 
							('".trataCaracteres($rowTlH2["nu_cep"])."','".trataCaracteres($rowTlH2["nu_dne"])."', '".trataCaracteres($rowTlH2["no_titulo_patente"])."', '".trataCaracteres($rowTlH2["no_titulo_patente_abreviatura"])."')";
			$queryInsert = pg_query(CONN_PG,$insert) or die($insert);
			if(!$queryInsert){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaTipoParidade(){
	$sqlTlH2 = "SELECT * FROM TB_TIPO_PARIDADE ";
	$queryTlH2 = pg_query(CONN,$sqlTlH2);
	$qtdRegitsro = pg_num_rows($queryTlH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	$contigual = 0;
	$cont = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowTlH2 = pg_fetch_array($queryTlH2)){
	
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
	
		$sql = "SELECT co_tipo_paridade FROM tb_tipo_paridade WHERE TRIM(UPPER(retira_acentos(no_tipo_paridade))) = '".$rowTlH2["no_tipo_paridade"]."'";
		$query = pg_query(CONN_PG,$sql) or die ($sql);
		$num = pg_num_rows($query);
		$row = pg_fetch_array($query);
		if ($num > 0) {
			$update = "UPDATE tb_tipo_paridade
						SET no_tipo_paridade = '".trataCaracteres($rowTlH2["no_tipo_paridade"])."', 
							sg_tipo_paridade = '".trataCaracteres($rowTlH2["sg_tipo_paridade"])."'
						WHERE
							co_tipo_paridade = '".$row["co_tipo_paridade"]."'";
			$queryUpdate = pg_query(CONN_PG,$update) or die($update);
			if(!$queryUpdate){
				$error += 1; 
			}
		} else {
			$insert = "INSERT INTO tb_tipo_paridade 
							(no_tipo_paridade, sg_tipo_paridade)
						 VALUES 
							('".trataCaracteres($rowTlH2["no_tipo_paridade"])."','".trataCaracteres($rowTlH2["sg_tipo_paridade"])."')";
			$queryInsert = pg_query(CONN_PG,$insert) or die($insert);
			if(!$queryInsert){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaLogradouros(){	
	$sqlRuaH2 = "SELECT 
				  TRIM(UPPER(tbb.no_bairro)) AS no_bairro,
				  TRIM(LOWER(tbb.no_bairro_filtro)) AS no_bairro_filtro,
				  TRIM(LOWER(tblc.no_localidade_filtro)) AS no_localidade_filtro,
				  TRIM(UPPER(tbtl.nu_dne)) AS nu_dne_log,
				  TRIM(UPPER(tbtl.nu_dne)) AS nu_dne_patente,
				  tbl.tp_paridade,
				  TRIM(UPPER(tbl.no_logradouro)) AS no_logradouro,
				  TRIM(UPPER(tbl.nu_cep8)) AS nu_cep8,
				  TRIM(UPPER(tbl.nu_cep)) AS nu_cep,
				  TRIM(UPPER(tbl.nu_dne)) AS nu_dne,
				  TRIM(UPPER(tbl.no_preposicao)) AS no_preposicao,
				  TRIM(UPPER(tbl.no_logradouro)) AS no_logradouro,
				  TRIM(UPPER(tbl.no_logradouro_abreviatura)) AS no_logradouro_abreviatura,
				  TRIM(UPPER(tbl.nu_inicial_trecho)) AS nu_inicial_trecho,
				  TRIM(UPPER(tbl.nu_final_trecho)) AS nu_final_trecho,
				  TRIM(UPPER(tbl.nu_seccionamento_dne)) AS nu_seccionamento_dne,
				  TRIM(LOWER(tbl.no_logradouro_filtro)) AS no_logradouro_filtro,
				  TRIM(UPPER(tbl.no_logradouro_exibicao)) AS no_logradouro_exibicao
				FROM TB_LOGRADOURO AS tbl
				INNER JOIN TB_BAIRRO AS tbb ON tbl.co_bairro_inicial=tbb.co_bairro
				INNER JOIN TB_LOCALIDADE AS tblc ON tbb.co_localidade=tblc.co_localidade
				LEFT JOIN TB_TIPO_LOGRADOURO AS tbtl ON tbl.tp_logradouro=tbtl.co_tipo_logradouro
				LEFT JOIN TB_TITULO_PATENTE AS tbtp ON tbl.co_titulo_patente=tbtp.co_titulo_patente ";
	$queryRuaH2 = pg_query(CONN,$sqlRuaH2);
	$qtdRegitsro = pg_num_rows($queryRuaH2);
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	while($rowRuaH2 = pg_fetch_array($queryRuaH2)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		// Buscando cidade
		$sqlCid = "SELECT 
						cid_codigo FROM cidade 
					WHERE 
						 TRIM(LOWER(retira_acentos(cid_nome))) = '".trataCaracteres($rowRuaH2["no_localidade_filtro"])."'";
		$queryCid = pg_query(CONN_PG,$sqlCid) or die ($sqlCid);
		$rowCid = pg_fetch_array($queryCid);
		// Buscando bairro
		$sqlBairro = "SELECT 
						bai_codigo 
					  FROM 
						bairro 
					  WHERE 
						cid_codigo = '".$rowCid["cid_codigo"]."' AND
						TRIM(LOWER(retira_acentos(bai_nome))) = '".trataCaracteres($rowRuaH2["no_bairro_filtro"])."'";
		$queryBairro = pg_query(CONN_PG,$sqlBairro) or die($sqlBairro);
		$rowBairrro = pg_fetch_array($queryBairro);
		// Buscando Tipo de Logradouro
		$sqlTlog= "SELECT co_tipo_logradouro FROM tb_ms_tipo_logradouro WHERE co_tipo_logradouro = '".$rowRuaH2["nu_dne_log"]."'";
		$queryTlog = pg_query(CONN_PG,$sqlTlog) or die ($sqlTlog);
		$rowTlog = pg_fetch_array($queryTlog);
		// Buscando tipo de patente
		$sqlTPat = "SELECT co_titulo_patente FROM tb_titulo_patente WHERE nu_dne = '".$rowRuaH2["nu_dne_patente"]."'";
		$queryTPat = pg_query(CONN_PG,$sqlTPat) or die ($sqlTPat);
		$rowTPat = pg_fetch_array($queryTPat);
		// Verifica se rua j� existe
		$sqlRua = "SELECT 
						rua_codigo 
					  FROM 
						rua 
					  WHERE 
						cid_codigo = '".$rowCid["cid_codigo"]."' AND
						bai_codigo = '".$rowBairrro["bai_codigo"]."' AND
						TRIM(LOWER(retira_acentos(rua_nome))) = '".trataCaracteres($rowRuaH2["no_logradouro_filtro"])."' AND
						TRIM(UPPER(retira_acentos(rua_cep))) = '".trataCaracteres($rowRuaH2["nu_cep8"])."'
						";
		$queryRua = pg_query(CONN_PG,$sqlRua);
		$rowRua = pg_fetch_array($queryRua);
		$numRua = pg_num_rows($queryRua);
		if ($numRua > 0) {
			$sqlUpdRua = "UPDATE rua
									SET rua_nome = '".$rowRuaH2["no_logradouro"]."',
										cid_codigo = '".$rowCid["cid_codigo"]."',
										co_tipo_logradouro = '".$rowTlog["co_tipo_logradouro"]."', 
										rua_cep = '".$rowRuaH2["nu_cep8"]."',
										rua_bairro = '".$rowRuaH2["no_bairro"]."',
										bai_codigo = '".$rowBairrro["bai_codigo"]."',
										rua_nu_cep = '".$rowRuaH2["nu_cep"]."',
										rua_nu_dne = '".$rowRuaH2["nu_dne"]."',
										rua_no_preposicao = '".$rowRuaH2["no_preposicao"]."',
										rua_nome_abreviatura = '".$rowRuaH2["no_logradouro_abreviatura"]."',
										rua_nu_inicial_trecho = '".$rowRuaH2["nu_inicial_trecho"]."',
										rua_nu_final_trecho = '".$rowRuaH2["nu_final_trecho"]."',
										rua_nu_seccionamento_dne = '".$rowRuaH2["nu_seccionamento_dne"]."',
										rua_no_logradouro_filtro = '".$rowRuaH2["no_logradouro_filtro"]."',
										rua_no_logradouro_exibicao = '".$rowRuaH2["no_logradouro_exibicao"]."',
										co_titulo_patente = '".$rowTPat["co_titulo_patente"]."',
										tp_paridade = '".$rowRuaH2["tp_paridade"]."',
										rua_sg_tipo_registro = '".$rowRuaH2["sg_tipo_registro"]."',
									WHERE
										rua_codigo = '".$rowRua["rua_codigo"]."'";
			$queryUpdRua = pg_query(CONN_PG,$sqlUpdRua);
			if(!$queryUpdRua){
				$error += 1; 
			}
		} else {
                        $sqlInsRua = "INSERT INTO rua 
                                                (rua_nome, cid_codigo, co_tipo_logradouro, rua_cep, rua_bairro, bai_codigo, rua_nu_cep, rua_nu_dne, rua_no_preposicao, rua_nome_abreviatura, rua_nu_inicial_trecho, rua_nu_final_trecho, rua_nu_seccionamento_dne, rua_no_logradouro_filtro, rua_no_logradouro_exibicao, co_titulo_patente, tp_paridade, rua_sg_tipo_registro)
                                        VALUES
                                                ('".str_replace("'","",$rowRuaH2["no_logradouro"])."','".$rowCid["cid_codigo"]."','".$rowTlog["co_tipo_logradouro"]."','".$rowRuaH2["nu_cep8"]."','".$rowRuaH2["no_bairro"]."','".$rowBairrro["bai_codigo"]."','".$rowRuaH2["nu_cep"]."','".$rowRuaH2["nu_dne"]."','".$rowRuaH2["no_preposicao"]."','".str_replace("'","",$rowRuaH2["no_logradouro_abreviatura"])."','".$rowRuaH2["nu_inicial_trecho"]."','".$rowRuaH2["nu_final_trecho"]."','".$rowRuaH2["nu_seccionamento_dne"]."','".str_replace("'","",$rowRuaH2["no_logradouro_filtro"])."','".str_replace("'","",$rowRuaH2["no_logradouro_exibicao"])."',".($rowRuaH2["co_titulo_patente"] != "" ? $rowRuaH2["co_titulo_patente"] : "null").",".($rowRuaH2["tp_paridade"] != "" ? $rowRuaH2["tp_paridade"] : "null").",'".$rowRuaH2["sg_tipo_registro"]."')";
			$queryInsRua = pg_query(CONN_PG,$sqlInsRua) or die ($sqlInsRua);
			if(!$queryInsRua){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaEnderecos(){
	$sqlRuaH2 = "SELECT DISTINCT
					  TRIM(UPPER(tbe.ds_logradouro)) AS no_logradouro,
					  TRIM(LOWER(tbe.ds_logradouro)) AS no_logradouro_filtro,
					  tbe.ds_cep,
					  TRIM(UPPER(tbb.no_bairro)) AS no_bairro,
					  TRIM(LOWER(tbb.no_bairro_filtro)) AS no_bairro_filtro,
					  TRIM(LOWER(tbl.no_localidade_filtro)) AS no_localidade_filtro,
					  tbl.co_ibge
				 FROM 
					TB_ENDERECO AS tbe
				INNER JOIN 
				   TB_BAIRRO AS tbb ON tbe.co_bairro=tbb.co_bairro
				INNER JOIN 
					  TB_LOCALIDADE AS tbl ON tbb.co_localidade=tbl.co_localidade ";
	$queryRuaH2 = pg_query(CONN,$sqlRuaH2);
	$qtdRegitsro = pg_num_rows($queryRuaH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowRuaH2 = pg_fetch_array($queryRuaH2)){
		
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
	
		// Buscando cidade
		$sqlCid = "SELECT 
						cid_codigo FROM cidade 
					WHERE 
						 TRIM(LOWER(retira_acentos(cid_nome))) = '".trataCaracteres($rowRuaH2["no_localidade_filtro"])."'";
		$queryCid = pg_query(CONN_PG,$sqlCid) or die ($sqlCid);
		$rowCid = pg_fetch_array($queryCid);
		// Buscando bairro
		$sqlBairro = "SELECT 
						bai_codigo 
					  FROM 
						bairro 
					  WHERE 
						cid_codigo = '".$rowCid["cid_codigo"]."' AND
						TRIM(LOWER(retira_acentos(bai_nome))) = '".trataCaracteres($rowRuaH2["no_bairro_filtro"])."'";
		$queryBairro = pg_query(CONN_PG,$sqlBairro) or die($sqlBairro);
		$rowBairrro = pg_fetch_array($queryBairro);
		// Verifica se rua j� existe
		$sqlRua = "SELECT 
						rua_codigo 
					  FROM 
						rua 
					  WHERE 
						cid_codigo = '".$rowCid["cid_codigo"]."' AND
						bai_codigo = '".$rowBairrro["bai_codigo"]."' AND
						TRIM(LOWER(retira_acentos(rua_nome))) = '".trataCaracteres($rowRuaH2["no_logradouro_filtro"])."' AND
						TRIM(UPPER(retira_acentos(rua_cep))) = '".trataCaracteres($rowRuaH2["ds_cep"])."'
						";
		$queryRua = pg_query(CONN_PG,$sqlRua);
		$rowRua = pg_fetch_array($queryRua);
		$numRua = pg_num_rows($queryRua);
		if ($numRua > 0) {
			
			$sqlUpdRua = "UPDATE rua
									SET rua_nome = '".$rowRuaH2["no_logradouro"]."',
										rua_no_logradouro_filtro = '".$rowRuaH2["no_logradouro_filtro"]."',
										rua_bairro = '".$rowRuaH2["no_bairro"]."',
										cid_codigo = '".$rowCid["cid_codigo"]."',
										rua_cep = '".$rowRuaH2["ds_cep"]."',
										bai_codigo = '".$rowBairrro["bai_codigo"]."'
									WHERE
										rua_codigo = '".$rowRua["rua_codigo"]."'";
			$queryUpdRua = pg_query(CONN_PG,$sqlUpdRua);
			
			if(!$queryUpdRua){
				$error += 1; 
			}
		} else {
			$sqlInsBairro = "INSERT INTO rua 
										(rua_nome, rua_no_logradouro_filtro, rua_bairro, cid_codigo, rua_cep, bai_codigo)
									VALUES
										('".$rowRuaH2["no_logradouro"]."', '".$rowRuaH2["no_logradouro_filtro"]."', '".$rowRuaH2["no_bairro"]."', '".$rowCid["cid_codigo"]."','".$rowRuaH2["ds_cep"]."','".$rowBairrro["bai_codigo"]."')";
			$queryInsBairro = pg_query(CONN_PG,$sqlInsBairro);
			if(!$queryInsBairro){
				$error += 1; 
			}
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}

function importaDomicilios(){
	$sqlRuaH2 = "SELECT DISTINCT
					  TRIM(UPPER(tbe.ds_logradouro)) AS no_logradouro,
					  TRIM(LOWER(tbe.ds_logradouro)) AS no_logradouro_filtro,
					  tbe.ds_cep,
					  TRIM(UPPER(tbb.no_bairro)) AS no_bairro,
					  TRIM(LOWER(tbb.no_bairro_filtro)) AS no_bairro_filtro,
					  TRIM(LOWER(tbl.no_localidade_filtro)) AS no_localidade_filtro,
					  tbl.co_ibge,
					  rlpe.co_ator,
					  tbe.nu_numero,
					  tbe.ds_complemento,
					  tbpf.no_pessoa_fisica,
                      tbpf.no_pessoa_fisica_filtro,
					  tbpf.dt_nascimento,
					  TRIM(LOWER(tbpf.no_mae_filtro)) AS no_mae_filtro
				 FROM 
					TB_ENDERECO AS tbe
				 INNER JOIN 
				   RL_PESSOA_ENDERECO  AS rlpe ON tbe.co_seq_endereco=rlpe.co_endereco
				INNER JOIN 
				   TB_PESSOA_FISICA AS tbpf ON rlpe.co_ator=tbpf.co_ator
				 INNER JOIN 
				   TB_BAIRRO AS tbb ON tbe.co_bairro=tbb.co_bairro
				 INNER JOIN 
					  TB_LOCALIDADE AS tbl ON tbb.co_localidade=tbl.co_localidade ";
	$queryRuaH2 = pg_query(CONN,$sqlRuaH2);
	$qtdRegitsro = pg_num_rows($queryRuaH2);
	$porcentagem = 356/$qtdRegitsro;
	$progresso = 0;
	pg_query(CONN_PG,"BEGIN TRANSACTION;");
	while($rowRuaH2 = pg_fetch_array($queryRuaH2)){
	
		// Barra de Progresso
		$progresso = $progresso + $porcentagem;
		//sleep(1);
		?>
		<script language='javascript'>
			document.getElementById('div_progress').style.width = '<?php echo $progresso; ?>px';
		</script>   
		<?php
		echo "<span style='display:none'>". str_repeat('x', $progresso)."</span>";
		flush();
		// Fim Barra de Progresso
		
		// Buscando cidade
		$sqlCid = "SELECT 
						cid_codigo FROM cidade 
					WHERE 
						 TRIM(LOWER(retira_acentos(cid_nome))) = '".trataCaracteres($rowRuaH2["no_localidade_filtro"])."'";
		$queryCid = pg_query(CONN_PG,$sqlCid) or die ($sqlCid);
		$rowCid = pg_fetch_array($queryCid);
		$numCid = pg_num_rows($queryCid);
		if ($numCid > 0) { $contCid++; }
		// Buscando bairro
		$sqlBairro = "SELECT 
						bai_codigo 
					  FROM 
						bairro 
					  WHERE 
						cid_codigo = '".$rowCid["cid_codigo"]."' AND
						TRIM(LOWER(retira_acentos(bai_nome))) = '".trataCaracteres($rowRuaH2["no_bairro_filtro"])."'";
		$queryBairro = pg_query(CONN_PG,$sqlBairro) or die($sqlBairro);
		$rowBairrro = pg_fetch_array($queryBairro);
		$numBairro = pg_num_rows($queryBairro);
		if ($numBairro > 0) { $contBairro++; }
		// Verifica se rua j� existe
		$sqlRua = "SELECT 
						rua_codigo 
					  FROM 
						rua 
					  WHERE 
						cid_codigo = '".$rowCid["cid_codigo"]."' AND
						bai_codigo = '".$rowBairrro["bai_codigo"]."' AND
						TRIM(LOWER(retira_acentos(rua_nome))) = '".trataCaracteres($rowRuaH2["no_logradouro_filtro"])."' AND
						TRIM(UPPER(retira_acentos(rua_cep))) = '".trataCaracteres($rowRuaH2["ds_cep"])."'";
		$queryRua = pg_query(CONN_PG,$sqlRua);
		$rowRua = pg_fetch_array($queryRua);
		$numRua = pg_num_rows($queryRua);
		if ($numRua > 0) { $contRua++; }
		// Verificando se p�ciente j� n�o esta cadastrado
		$sqlPosConfUsu = "SELECT usu_codigo FROM usuario WHERE TRIM(LOWER(retira_acentos(usu_nome))) = '".trataCaracteres($rowRuaH2["no_pessoa_fisica_filtro"])."' AND usu_datanasc = '".trataCaracteres($rowRuaH2["dt_nascimento"])."' AND TRIM(LOWER(retira_acentos(usu_mae))) = '".$rowRuaH2["no_mae_filtro"]."'";
		$queryPosConfUsu = pg_query(CONN_PG,$sqlPosConfUsu) or die($sqlPosConfUsu);
		$regPosUsu = pg_fetch_array($queryPosConfUsu);
		$numPosConfUsu = pg_num_rows($queryPosConfUsu);
		if ($numPosConfUsu > 0) { $contUsu++; }
		// Verifica se domicilio j� existe
		$sqlDom = "SELECT 
						dom_codigo 
					  FROM 
						domicilio 
					  WHERE 
						rua_codigo = '".trataCaracteres($rowRua["rua_codigo"])."' AND
						dom_numero = ".trataCaracteres($rowRuaH2["nu_numero"])."";
		$queryDom = pg_query(CONN_PG,$sqlDom);
		$rowDom = pg_fetch_array($queryDom);
		$numDom = pg_num_rows($queryDom);
		$numDom = pg_num_rows($queryDom);
		if ($numDom > 0) { 
			$contDom++; 
		} else { 
			$sqlInsDom = "INSERT INTO domicilio 
										(dom_data_cadastro, rua_codigo, dom_numero, dom_segmento, co_tipo_domicilio)
									VALUES
										('NOW()', '".trataCaracteres($rowRua["rua_codigo"])."', ".trataCaracteres($rowRuaH2["nu_numero"]).", '1','1')";
			$queryInsDom = pg_query(CONN_PG,$sqlInsDom);
			// Busca id do �ltimo domicilio
			$sqlUlt = "SELECT dom_codigo FROM domicilio ORDER BY dom_codigo DESC LIMIT 1";
			$queryUlt = pg_query(CONN_PG,$sqlUlt);
			$rowUlt = pg_fetch_array($queryUlt);
			// Seta domicilio para o usu�rio
			$sqlUpdUsu = "UPDATE usuario SET dom_codigo = '".trataCaracteres($rowUlt["dom_codigo"])."' WHERE usu_codigo = '".$regPosUsu["usu_codigo"]."'";
			$queryUsu = pg_query(CONN_PG,$sqlUpdUsu) or die($sqlUpdUsu);
		}
	}
	if($error > 0){
		pg_query(CONN_PG,"ROLLBACK");
		echo "<div class='error'>OCORREU UM ERRO AO IMPORTAR OS DADOS!</div>";
	}else{
		pg_query(CONN_PG,"COMMIT");
		echo "<div class='success'>DADOS IMPORTADO COM SUCESSO!</div>";	
	}
}








?>
