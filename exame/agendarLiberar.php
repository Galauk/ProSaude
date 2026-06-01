<?php 
	session_start();
	$procedimentos = $_GET[procedimentos];
	$med_codigo = $_GET[med_codigo];
	$uni_codigo = $_GET[uni_codigo];
?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
	<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
	<script type="text/javascript">
		function verificaAcao(med_codigo){
			url = "buscaCampo.php?medcod="+med_codigo;
			ajax_tudo(url, preencheAcao);
		}
		function preencheAcao(txt){
			div = document.getElementById('hide');
			acao = document.getElementById('acao');
			if (txt == "S"){
				requisicoes = document.getElementById("requisicoes").value;
				med_codigo = document.getElementById("med_codigo").value;
				uni_codigo = document.getElementById("uni_codigo").value;
				url = "buscaDatasDisponiveis.php?requisicoes="+requisicoes+"&med_codigo="+med_codigo+"&uni_codigo="+uni_codigo;
				ajax_tudo(url, selecionaData);
			}else{
				div.style.display = "none";
				acao.value = "liberar";
			}
		}
		function selecionaData(txt){
			div = document.getElementById('hide');
			div.style.display = "block";
			div.innerHTML = txt;
		}
	</script>
</head>
<body>
<?php
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();
	$requisicoes = $_GET[requisicoes];
	$acao = $_GET[acao];
	$usr_codigo_cad = $_GET[id_login];
	$palavra_chave = $_GET[palavra_chave];
	$liberacoes = array();
	$agendamentos = array();
	$usuarios = array();

	$sqlLogon = "SELECT uni_codigo
				   FROM logon
				  WHERE id_login = $usr_codigo_cad";
	$query = pg_query($sqlLogon);
	$dado = pg_fetch_array($query);
	$uni_codigo = $dado[uni_codigo];
	
	if ($acao == ""){
		echo $common->menuTab(array("Agendar/Liberar Exames"));
		echo $common->bodyTab('1');
			echo $form->openForm($PHP_SELF, "GET", "agelib");
				echo $form->hiddenForm("requisicoes", $requisicoes);
				echo $form->hiddenForm("acao", "agendar");
				echo $form->hiddenForm("id_login", $usr_codigo_cad);
				echo $form->hiddenForm("palavra_chave", $palavra_chave);
				echo $form->hiddenForm("uni_codigo", $uni_codigo);
				$sel = "SELECT med_codigo,
							   med_nome
						  FROM medico
						  where prestador_servico='S'
						 ORDER BY med_nome";
			echo $table->openTable("table");
				echo $table->criaLinha(array($form->inputSelect("med_codigo", null, "Local", $sel, "onChange=verificaAcao(this.value);", null, null, "style=\"width:200px;\"", "SELECIONE", "style=\"width:180px;\"")));
				
				echo $table->criaLinha(array("<div id=hide style=\"clear:both; display:none;\"></div>"));
				echo $table->closeTable();
				echo $table->openTable("table");
					echo $table->criaLinha(array($common->commonButton("Agendar/Liberar", null, "calendar.png", "onClick=\"document.agelib.submit();\"")));
				echo $table->closeTable();
						
			echo $form->closeForm();
		echo $common->closeTab();	
	}else if ($acao == "agendar"){
		echo "<pre>".print_r($_GET,true)."</pre>";
		$agexl_data = $_GET[agexl_data];
		$agexl_hora = "07:00";
		$med_codigo = $_GET[med_codigo];
		$usr_codigo_cad = $_GET[id_login];
		
		$agexl_status = $agex_status = "A";
		$agexl_dt_cadastro = $agex_data_cad = "CURRENT_DATE";
		
		$sqlExterno = "SELECT distinct re.ate_codigo,
							  re.usu_codigo,
							  a.med_codigo as med_codigo_responsavel,
							  ag.esp_codigo as esp_codigo_responsavel
						 FROM requisicao_exames re
					 	 JOIN atendimento a
						   ON a.ate_codigo = re.ate_codigo
					  	 JOIN agendamento ag
						   ON ag.age_codigo = a.age_codigo
						WHERE req_codigo IN ($requisicoes)";
		$querySqlExterno = pg_query($sqlExterno);
		pg_query("BEGIN TRANSACTION");
		try {
			while($linha = pg_fetch_array($querySqlExterno)){
				$sql = "SELECT re.req_codigo,
							   re.ate_codigo,
							   re.proc_codigo,
							   re.usu_codigo,
							   re.req_finalizada,
							   re.dt_requisicao,
							   a.med_codigo as med_codigo_responsavel,
							   ag.esp_codigo as esp_codigo_responsavel
						  FROM requisicao_exames re
						  JOIN atendimento a
							ON a.ate_codigo = re.ate_codigo
						  JOIN agendamento ag
							ON ag.age_codigo = a.age_codigo
						 WHERE re.ate_codigo = ($linha[ate_codigo])
						   AND re.req_codigo IN ($requisicoes)";
				$exec = pg_query($sql);
				$sqlNextVal = "SELECT nextval('agendamento_exame_agex_codigo_seq') AS prox";
				$execSeq = pg_query($sqlNextVal);
				$valida = $execSeq;
				$sequencia = pg_fetch_array($execSeq);
				$agex_codigo = $sequencia[prox];
				$sqlInsertAE = "INSERT INTO agendamento_exame(agex_codigo, 
															  usu_codigo, 
															  agex_data_cad, 
															  agex_status, 
															  med_codigo_responsavel,
															  esp_codigo_responsavel)
													  VALUES ($agex_codigo, 
															  $linha[usu_codigo], 
															  $agex_data_cad, 
															  '$agex_status', 
															  $linha[med_codigo_responsavel],
															  $linha[esp_codigo_responsavel]);";
				$queryInsertAE = pg_query($sqlInsertAE);
				array_push($agendamentos, $agex_codigo);
				array_push($usuarios, $linha[usu_codigo]);
				$valida = $valida && $queryInsertAE;
				while($dados = pg_fetch_array($exec)){
					$sqlInsertAEL = "INSERT INTO agendamento_exame_lista(agex_codigo, 
																		 usu_codigo, 
																		 med_codigo, 
																		 proc_codigo, 
																		 agexl_data, 
																		 agexl_hora, 
																		 agexl_status, 
																		 usr_codigo_cad, 
																		 agexl_dt_cadastro, 
																		 uni_codigo, 
																		 req_codigo)
																 VALUES ($agex_codigo, 
																		 $dados[usu_codigo], 
																		 $med_codigo, 
																		 $dados[proc_codigo], 
																		 '$agexl_data', 
																		 '$agexl_hora', 
																		 '$agexl_status', 
																		 $usr_codigo_cad, 
																		 $agexl_dt_cadastro, 
																		 $uni_codigo, 
																		 $dados[req_codigo]);";
					$queryInsertAEL = pg_query($sqlInsertAEL);
					$valida = $valida && $queryInsertAEL;
				}

				if (!$valida){
					throw new Exception("Houve um erro durante o agendamento e as requisi&ccedil;&otilde;es n&atilde;o foram agendadas, tente novamente.");
				}
			}
		}catch (Exception $e){
			pg_query("ROLLBACK TRANSACTION");
		}
		pg_query("COMMIT TRANSACTION");
		if($valida){
			echo "<script>";
				for($i = 0; $i < count($agendamentos); $i++){
					echo "window.open(\"../agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=$agendamentos[$i]&hora=$hora&usu_codigo=$usuarios[$i]&lab=$med_codigo&id_login=$id_login\",\"nv$i\",\"width=750,height=400\");";
				}
			echo "</script>";
			echo $common->modalMsg("OK", "As requisi&ccedil;&otilde;es foram agendadas com sucesso", "$PHP_SELF?acao=fechar&palavra_chave=$palavra_chave");
		}else{
			echo $common->modalMsg("ERRO", $e->getMessage(), "$PHP_SELF?acao=fechar&palavra_chave=$palavra_chave");
		}
	}else if($acao == "liberar"){
		$agexl_data = $_GET[agexl_data];
		$agexl_hora = $_GET[agexl_hora];
		$med_codigo = $_GET[med_codigo];
		$usr_codigo_cad = $_GET[id_login];
		
		$agexl_status = $agex_status = "A";
		$agexl_dt_cadastro = $agex_data_cad = "CURRENT_DATE";
		
		$sqlExterno = "SELECT distinct re.ate_codigo,
							  re.usu_codigo,
							  a.med_codigo as med_codigo_responsavel,
							  ag.esp_codigo as esp_codigo_responsavel
						 FROM requisicao_exames re
					 	 JOIN atendimento a
						   ON a.ate_codigo = re.ate_codigo
					  	 JOIN agendamento ag
						   ON ag.age_codigo = a.age_codigo
						WHERE req_codigo IN ($requisicoes)";
		$querySqlExterno = pg_query($sqlExterno);
		pg_query("BEGIN TRANSACTION");
		try {
			while($linha = pg_fetch_array($querySqlExterno)){
				$sql = "SELECT re.req_codigo,
							   re.ate_codigo,
							   re.proc_codigo,
							   re.usu_codigo,
							   re.req_finalizada,
							   re.dt_requisicao,
							   a.med_codigo as med_codigo_responsavel,
							   ag.esp_codigo as esp_codigo_responsavel
						  FROM requisicao_exames re
						  JOIN atendimento a
							ON a.ate_codigo = re.ate_codigo
						  JOIN agendamento ag
							ON ag.age_codigo = a.age_codigo
						 WHERE re.ate_codigo = ($linha[ate_codigo])
						   AND re.req_codigo IN ($requisicoes)";
				$exec = pg_query($sql);
				$sqlNextVal = "SELECT nextval('liberacao_exame_libex_codigo_seq') AS prox";
				$execSeq = pg_query($sqlNextVal);
				$valida = $execSeq;
				$sequencia = pg_fetch_array($execSeq);
				$agex_codigo = $sequencia[prox];
				$sqlInsertAE = "INSERT INTO liberacao_exame(libex_codigo, 
															usu_codigo, 
															libex_data_cad, 
															libex_status, 
															med_codigo_responsavel,
															esp_codigo_responsavel)
													VALUES ($agex_codigo, 
															$linha[usu_codigo], 
															$agex_data_cad, 
															'$agex_status', 
															$linha[med_codigo_responsavel],
															$linha[esp_codigo_responsavel]);";
				$queryInsertAE = pg_query($sqlInsertAE);
				array_push($liberacoes, $agex_codigo);
				array_push($usuarios, $linha[usu_codigo]);
				$valida = $valida && $queryInsertAE;
				while($dados = pg_fetch_array($exec)){
					$sqlInsertAEL = "INSERT INTO liberacao_exame_lista(libex_codigo, 
																	   usu_codigo, 
																	   med_codigo, 
																	   proc_codigo, 
																	   libexl_status, 
																	   usr_codigo_cad, 
																	   libexl_dt_cadastro, 
																	   uni_codigo, 
																	   req_codigo)
															   VALUES ($agex_codigo, 
																	   $dados[usu_codigo], 
																	   $med_codigo, 
																	   $dados[proc_codigo], 
																	   '$agexl_status', 
																	   $usr_codigo_cad, 
																	   $agexl_dt_cadastro, 
																	   $uni_codigo, 
																	   $dados[req_codigo]);";
					$queryInsertAEL = pg_query($sqlInsertAEL);
					$valida = $valida && $queryInsertAEL;
				}
				if (!$valida){
					throw new Exception("Houve um erro durante a libera&ccedil;&atilde;o e as requisi&ccedil;&otilde;es n&atilde;o foram liberadas, tente novamente.");
				}
			}
		}catch (Exception $e){
			pg_query("ROLLBACK TRANSACTION");
		}
		pg_query("COMMIT TRANSACTION");
		if($valida){
			echo "<script>";
				for($i = 0; $i < count($liberacoes); $i++){
					echo "window.open(\"../liberacao_print_exames.php?acao=form_imprime&imprimir=a&libex_codigo=$liberacoes[$i]&usu_codigo=$usuarios[$i]&lab=$med_codigo&id_login=$id_login\",\"nv$i\",\"width=750,height=400\");";
				}
			echo "</script>";
			echo $common->modalMsg("OK", "As requisi&ccedil;&otilde;es foram liberadas com sucesso", "$PHP_SELF?acao=fechar&palavra_chave=$palavra_chave");
		}else{
			echo $common->modalMsg("ERRO", $e->getMessage(), "$PHP_SELF?acao=fechar&palavra_chave=$palavra_chave");
		}
	}else if($acao == "fechar"){
		echo "<script>
			window.opener.limpaAgendados('$palavra_chave');
			window.close();
		</script>";
	}
?>
</body>
</html>