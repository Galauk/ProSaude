<script language="JavaScript" type="text/javascript" src="sisprenatal.js"></script>
<link href="/WebSocialSaude/zf/public/css/redmond/jquery-ui-1.8.16.custom.css" media="screen" rel="stylesheet" type="text/css" />
<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
	echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/jquery-1.5.2.min.js'></script>\n";
	echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[modulo]/zf/public/js/jquery.price_format.1.6.min.js'></script>\n";
	echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[modulo]/zf/public/js/jquery-ui-1.8.16.custom.min.js'></script>\n";
	
	
?>
<script>
$(function(){
	$( '#tabs' ).tabs();
	$(".float").each(function(){
		var rel = $(this).attr("rel");
		alert(rel);
		if(rel){
			limit = parseInt(rel.split(",").shift());
			centsLimit = parseInt(rel.split(",").pop());
		}
		$(this).priceFormat({
			prefix: '',
			centsSeparator: '.',
			thousandsSeparator: '',
			limit: limit+centsLimit,
			centsLimit: centsLimit
		});
	});
});
</script>

<?php 
	$common = new commonClass();
	$table = new tableClass();
	$form = new classForm();
	//echo "<pre>".print_r($_POST, true)."</pre>";
	if($acao == ""){
		// os includes estao dentro de acao "" por que senao ele ia escrever no ajax do deletaExames
		//echo $common->incJquery();
		//echo "<pre>".print_r($_POST,TRUE)."</pre>";
	
		if($sispn_codigo == ""){
			$query = pg_query("SELECT NEXTVAL('sis_pre_natal_sispn_codigo_seq');") or die(pg_last_error()); 
			$row = pg_fetch_array($query);			
			$sispn_codigo = $row[0];
			
			$stmt = "INSERT INTO sis_pre_natal ( 
								 sispn_codigo,
								 sispn_data_cadastro, 
								 sispn_data_ultima_menstruacao, 
								 sispn_peso_anterior, 
								 sispn_data_provavel_parto, 
								 sispn_numero_gestacao, 
								 sispn_competencia, 
								 sispn_tipo_interrupcao, 
								 sispn_classificacao_risco, 
								 sispn_tipo_parto,
								 usu_codigo,
								 sispn_tipo_consulta) 
						VALUES ( $sispn_codigo,
								 '$sispn_data_cadastro', 
								 '$sispn_data_ultima_menstruacao', 
								 '$ate_peso', 
								 '$sispn_data_provavel_parto', 
								 '$sispn_numero_gestacao', 
								 ".($sispn_competencia == "" ? "null" : "'$sispn_competencia'").", 
								 ".($sispn_tipo_interrupcao == "" ? "'00'" : "'$sispn_tipo_interrupcao'").", 
								 ".($sispn_classificacao_risco == "" ? "'00'" : "'$sispn_classificacao_risco'").", 
								 ".($sispn_tipo_parto == "" ? "null" : "'$sispn_tipo_parto'").",
								 $usu_codigo,
								 $sispn_tipo_consulta)";
								 
								 //die($stmt);
			$queryStmt = pg_query($stmt) or die(pg_last_error()."<pre>".$stmt);
			$sqlPegaCodigo = "SELECT *
								FROM sis_pre_natal
							   WHERE sispn_numero_gestacao = '$sispn_numero_gestacao'";
			$queryPegaCodigo = pg_query($sqlPegaCodigo);
			$linhaPegaCodigo = pg_fetch_array($queryPegaCodigo);
			$sispn_codigo = $linhaPegaCodigo["sispn_codigo"];
		}else{
			$stmt = "UPDATE sis_pre_natal 
						SET	sispn_data_cadastro = '$sispn_data_cadastro', 
							sispn_data_ultima_menstruacao = '$sispn_data_ultima_menstruacao', 
							sispn_peso_anterior = ".($ate_peso == "" ? "null" : "'$ate_peso'").", 
							sispn_data_provavel_parto = '$sispn_data_provavel_parto', 
							sispn_tipo_interrupcao = ".($sispn_classificacao_risco == "" ? "'00'" : "'$sispn_tipo_parto'").", 
							sispn_classificacao_risco = ".($sispn_classificacao_risco == "" ? "'00'" : "'$sispn_tipo_parto'").", 
							sispn_tipo_parto = ".($sispn_tipo_parto == "" ? "null" : "'$sispn_tipo_parto'")."
					  WHERE sispn_codigo = $sispn_codigo ";
			$queryStmt = pg_query($stmt);
		}
		
		if($ate_codigo == ""){
			$select = "SELECT nextval ('seq_ate_codigo') as ate_codigo";
			$exec_select = pg_query($select);
			$linha = pg_fetch_array($exec_select);
			$ate_codigo = $linha['ate_codigo'];
			$ate_datf = date("Y/m/d");
			$ate_hora = date("h:i");			
			
			$q = "INSERT INTO atendimento (ate_codigo,
				    						 ate_hora,
											 med_codigo,
											 usu_codigo,
											 age_codigo,
											 ate_data,
											 ate_tipo_consulta_prenatal,
											 ate_observacao_prenatal,
											 ate_peso,
											 sispn_codigo,
											 ate_classificacao_risco_prenatal,
											 ate_pre_natal)
								    VALUES ('$ate_codigo',
								    		'$ate_hora',
										    '$med_codigo',
										    '$usu_codigo',
										    '$age_codigo',
										    NOW(),
										    $sispn_tipo_consulta,
										    ".($observacao == "" ? "null" : "'$observacao'").",
										    '$ate_peso',
										    $sispn_codigo,
										    $sispn_classificacao_risco,
										    't')";
			
			$queryQ = pg_query($q);
				/*AQUI VERIFICA OS EXAMES QUE NÃO FORAM SOLICITADOS PARA COLOCALOS NA TABELA DO PRENATAL*/
			$sqlNaoRequisitados = "SELECT proc_codigo 
									 FROM procedimentos_sisprenatal
									WHERE proc_codigo NOT IN (";
			foreach($exames as $proc_codigo){
				$aux .= $proc_codigo.", ";
			}
			$sqlNaoRequisitados .= substr($aux, 0, -2);
			$sqlNaoRequisitados .= ")";
			$queryNaoRequisitados = pg_query($sqlNaoRequisitados);
			$registros = pg_fetch_all($queryNaoRequisitados);
			foreach($registros as $valor ){	
				// A TABELA EXAMES DO SISPRENATAL INFORMA OS EXAMES QUE ESTÃO EM DIA PARA UMA GESTANTE, 
				//OU EXAMES QUE FORAM SOLICITADOS DENTRO DO PRAZO DE VALIDADE
				$insertExamesEmDia = "INSERT INTO exames_sisprenatal(proc_codigo,
																	 sispn_codigo)
															 VALUES ($valor[proc_codigo],
															 		 $sispn_codigo);";
				$queryExamesEmDia = pg_query($insertExamesEmDia);
			}
			foreach($exames as $proc_codigo){
			
				$insertExames = "INSERT INTO requisicao_exames (usu_codigo,
																ate_codigo,
																proc_codigo,
																req_finalizada,
																dt_requisicao) 
														VALUES ('$usu_codigo',
															    '$ate_codigo',
															    '$proc_codigo',
															    'N',
															    NOW())";
				$queryExames = pg_query($insertExames);
			}
			if($exames != ""){
				echo "<script>
					  	window.open(\"../print_requisicao.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo\",null,\"height=600,width=560,status=yes,toolbar=no,menubar=no,location=no\")
					  </script>";
			}
		}else{
			//echo "com ate_codigo";exit;
			$q = "UPDATE atendimento
					 SET ate_tipo_consulta_prenatal = '$sispn_tipo_consulta',
						 ate_observacao_prenatal = ".($observacao == "" ? "null" : "'$observacao'").",
						 ate_peso = $ate_peso,
						 ate_classificacao_prenatal = '$sispn_classificacao_risco',
				         sispn_codigo = $sispn_codigo,
				         ate_pre_natal = 't'
				   WHERE ate_codigo=$ate_codigo";
			$query = pg_query($q) or die(pg_last_error()."<pre>".$q);
			
			/*Aqui começa a parte de deletar exames para inserir novamente.*/
			
			$sqlNaoRequisitados = "SELECT proc_codigo 
									 FROM procedimentos_sisprenatal
									WHERE proc_codigo NOT IN (";
			foreach($exames as $proc_codigo){
				$aux .= $proc_codigo.", ";
			}
			$sqlNaoRequisitados .= substr($aux, 0, -2);
			$sqlNaoRequisitados .= ")";
			$queryNaoRequisitados = pg_query($sqlNaoRequisitados);
			$registros = pg_fetch_all($queryNaoRequisitados);
			foreach($registros as $valor ){	
				// A TABELA EXAMES DO SISPRENATAL INFORMA OS EXAMES QUE ESTÃO EM DIA PARA UMA GESTANTE, 
				//OU EXAMES QUE FORAM SOLICITADOS DENTRO DO PRAZO DE VALIDADE
				$insertExamesEmDia = "INSERT INTO exames_sisprenatal(proc_codigo,
																	 sispn_codigo)
															 VALUES ($valor[proc_codigo],
															 		 $sispn_codigo);";
				$queryExamesEmDia = pg_query($insertExamesEmDia);
			}
			foreach($exames as $proc_codigo){
			
				$insertExames = "INSERT INTO requisicao_exames (usu_codigo,
																ate_codigo,
																proc_codigo,
																req_finalizada,
																dt_requisicao) 
														VALUES ('$usu_codigo',
															    '$ate_codigo',
															    '$proc_codigo',
															    'N',
															    NOW())";
				$queryExames = pg_query($insertExames);
			}
			if($exames != ""){
				echo "<script>
					  	window.open(\"../print_requisicao.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo\",null,\"height=600,width=560,status=yes,toolbar=no,menubar=no,location=no\")
					  </script>";
			}
			/*e aqui termina a parte de exames editados no dia da consulta*/
		}
		if($sispn_tipo_consulta == 5){
			echo $common->openModal("Salvar",700,"Salvar",null,"document.avaliacao.submit()",null,null,200);
				$sqlPre = "SELECT * 
						      FROM pre_consulta 
						     WHERE age_codigo = $age_codigo";
				$queryPre = pg_query($sqlPre);
				$linhaPre = pg_fetch_array($queryPre);
				//sqlPrenatal.php?acao=salvarAvaliacao&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data
				echo $form->openForm("sqlPrenatal.php","POST","avaliacao");
					//echo $form->hiddenForm("acao","salvarAvaliacao");
					echo $form->hiddenForm("acao","salvarAvaliacao");
					echo $form->hiddenForm("id_login",$id_login);
					echo $form->hiddenForm("ate_codigo",$ate_codigo);
					echo $form->hiddenForm("age_codigo",$age_codigo);
					echo $form->hiddenForm("usu_codigo",$usu_codigo);
					echo $form->hiddenForm("uni_codigo",$uni_codigo);
					echo $form->hiddenForm("age_data",$age_data);
					echo $form->hiddenForm("med_codigo",$med_codigo);
					$arrayCaracteristicaMamas = array("1"=>"Normal",
													  "2"=>"Fissuras",
													  "3"=>"Misto",
													  "4"=>"Mama cheia",
													  "5"=>"Mastite",
													  "6"=>"Artificial");
					echo $form->inputText("ate_peso",$ate_peso,"Peso");
					echo $form->inputText("pc_temperatura",$linhaPre[pc_temperatura],"Temperatura");
					echo $form->inputText("pc_pressao_sistolica",$linhaPre[pc_pressao_sistolica],"PA sist&oacute;lica");
					echo $form->inputText("pc_pressao_diastolica",$linhaPre[pc_pressao_diastolica],"PA diastolica");
					echo $form->inputText("ava_caracteristicas_loquios",$ava_caracteristicas_loquios,"Caracteristicas dos l&oacute;quios",80);
					//echo $form->textArea("ava_caracteristicas_loquios","$ava_caracteristicas_loquios","Caracteristicas dos l&oacute;quios");
					echo $form->inputCheckboxRadio("ava_hemorragia",$ava_hemorragia,"Hemorragia",null,$arraySimNao,"radio");
					echo $form->inputCheckboxRadio("ava_infeccao",$ava_infeccao,"Infec&ccedil;&atilde;o",null,$arraySimNao,"radio");
					echo $form->inputSelect("ava_amamentacao",$arraySimNao,"Amamenta&ccedil;&atilde;o",null,"onChange=\"puerperal()\"");
					echo "<div id='amamentacao' style='display:none;'>";
						echo $form->inputText("ava_motivo_nao_amamentacao",$ava_motivo_nao_amamentacao,"Motivo",80);
					echo "</div>";
					echo $form->inputSelect("ava_catacteristicas_das_mamas",$arrayCaracteristicaMamas,"Caracteristicas da mama");
					echo $form->inputText("sispn_data_provavel_parto",$sispn_data_provavel_parto,"Data Parto");
					if($sispn_tipo_parto == "00"){
						$tipoParto = "-N&atilde;o Informado";
					}else if($sispn_tipo_parto == "20"){
						$tipoParto = "Parto Domiciliar";
					}else if($sispn_tipo_parto == "30"){
						$tipoParto = "Parto Hospitalar";
					}else if($sispn_tipo_parto == "99"){
						$tipoParto = "Ignorado";
					}
					echo $form->inputText("sispn_tipo_parto",$sispn_tipo_parto,"Tipo Parto");
					echo $form->inputSelect("ava_sexo",$arraySexo,"Sexo do rec&eacute;m nascido");
					echo $form->inputText("ava_peso",$ava_peso,"Peso do rec&eacute;m nascido",null,null,"rel=\"3,3\"",null,null,null,null,null,"float inputForm");
					echo $form->inputText("ava_tamanho",$ava_tamanho,"Tamanho do rec&eacute;m nascido");
					echo $form->inputText("ava_apgar",$ava_apgar,"APGAR");
					echo $form->inputText("ava_parkim",$ava_parkim,"PARKIM");
				echo $form->closeForm();
			echo $common->closeModal();
		}else{
			echo $common->modalMsg("OK","Salvo com sucesso!","../prontuarioEletronico/prontuario.php?pagina=17&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
		}
	}
	
	if($acao=='deletaExames'){
		$usu_codigo = $_GET["usu_codigo"];
		$med_codigo = $_GET["med_codigo"];
		$age_codigo = $_GET["age_codigo"];
		$uni_codigo = $_GET["uni_codigo"];
		$sispn_codigo = $_GET["sispn_codigo"];
		$ate_codigo = $_GET["ate_codigo"];
		$selecionadas = $_GET["selec"];
		
		//echo "<pre>".print_r($_GET,TRUE)."</pre>";
		$sqlx = pg_query("DELETE
							FROM requisicao_exames
						   WHERE proc_codigo in ($selec)
							 AND ate_codigo = $ate_codigo
							 AND dt_requisicao = CURRENT_DATE");
		 
		$deletaPendentes = "DELETE 
							  FROM exames_sisprenatal
							 WHERE sispn_codigo = $sispn_codigo
							   AND proc_codigo in ($select)";
		$queryDeletaPendentes = pg_query($deletaPendentes);
		
		
		//echo $sqlx;
		//$querySql = pg_query($sqlx);
		echo "../prontuarioEletronico/prontuario.php?pagina=17&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data";
	}
	if($acao == "salvarAvaliacao"){
		echo $common->incJquery();
		$stmt = "INSERT INTO avaliacao_puerperal ( 
							 ava_peso, 
							 ava_temperatura, 
							 ava_pressao_sistolica, 
							 ava_pressao_diastolica, 
							 ava_caracteristicas_loquios, 
							 ava_hemorragia, 
							 ava_infeccao, 
							 ava_amamentacao, 
							 ava_motivo_nao_amamentacao, 
							 ava_catacteristicas_das_mamas, 
							 ava_sexo, 
							 ava_tamanho, 
							 ava_apgar, 
							 ava_parkim, 
							 ate_codigo
			     ) VALUES ( 
							".($ava_peso == "" ? "null" : "'$ava_peso'").",
							".($pc_temperatura == "" ? "null" : "'$pc_temperatura'").",
							".($pc_pressao_sistolica == "" ? "null" : "'$pc_pressao_sistolica'").",
							".($pc_pressao_diastolica == "" ? "null" : "'$pc_pressao_diastolica'").",
							".($ava_caracteristicas_loquios == "" ? "null" : "'$ava_caracteristicas_loquios'").",
							".($ava_hemorragia == "" ? "null" : "'$ava_hemorragia'").",
							".($ava_infeccao == "" ? "null" : "'$ava_infeccao'").",
							".($ava_amamentacao == "" ? "null" : "'$ava_amamentacao'").",
							".($ava_motivo_nao_amamentacao == "" ? "null" : "'$ava_motivo_nao_amamentacao'").",
							".($ava_catacteristicas_das_mamas == "" ? "null" : "'$ava_catacteristicas_das_mamas'").",
							".($ava_sexo == "" ? "null" : "'$ava_sexo'").",
							".($ava_tamanho == "" ? "null" : "'$ava_tamanho'").",
							".($ava_apgar == "" ? "null" : "'$ava_apgar'").",
							".($ava_parkim == "" ? "null" : "'$ava_parkim'").",
							".($ate_codigo == "" ? "null" : "'$ate_codigo'")." )";	
		if($queryStmt = pg_query($stmt)){
			echo $common->modalMsg("OK","Salvo com sucesso!","../prontuarioEletronico/prontuario.php?pagina=17&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
		}else{
			echo $common->modalMsg("ERRO","N&atilde;o foi poss&iacute;vel salvar","../prontuarioEletronico/prontuario.php?pagina=17&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data",$stmt);
		}
		
		
	}
?>