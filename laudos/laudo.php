<?php
	session_start();	
	echo "<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/ajax_motor.js'></script>";
?>
<script>
function validador (){
	proc_codigo = document.getElementById("proc_codigo").value;
	//alert(proc_codigo);
	if(proc_codigo == "4629"){
		inputs = document.getElementsByTagName("input");
		var ite_codigo = "";
		for(var i in inputs){
			if(/ite_codigo/.test(inputs[i].name)){
				var ite_codigo = ite_codigo + inputs[i].value ;
				//alert(ite_codigo);			
			}
		}
	}
	//document.valores.submit;
}
</script>
<? 
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";

	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();
	echo $common->menuTab(array("Digita&ccedil;&atilde;o de Laudo"));
	echo $common->bodyTab("1");
	if($acao == "form_add"){
		
		$sqlProcedimento = "SELECT proc_nome
							  FROM procedimento
							 WHERE proc_codigo = $proc_codigo";
		$queryProcedimento = pg_query($sqlProcedimento);
		$regProcedimento = pg_fetch_array($queryProcedimento);
		echo $common->divisoria($regProcedimento['proc_nome']);
		echo $form->openForm("laudo.php","POST","valores");
			$verificaAcao = "SELECT *
					     FROM resultadoexame
					    WHERE itx_codigo = $itx_codigo";
			$queryVerificaAcao = pg_query($verificaAcao);
			$regVerificaAcao = pg_fetch_array($queryVerificaAcao);
			$numLinhasVerificaAcao = pg_num_rows($queryVerificaAcao);
			if($numLinhasVerificaAcao == 0){
				echo $form->hiddenForm("acao", "insert");
			}else{
				echo $form->hiddenForm("acao", "update");
			}
			echo $form->hiddenForm("cad_exame", "$cad_exame","cad_exame");
			echo $form->hiddenForm("usu_codigo", "$usu_codigo");
			echo $form->hiddenForm("proc_codigo", "$proc_codigo","proc_codigo");
			echo $form->hiddenForm("proc_nome", $regProcedimento['proc_nome'],"proc_nome");
			echo $form->hiddenForm("itx_codigo", "$itx_codigo");
			echo $form->hiddenForm("id_login", "$id_login");
			echo $table->openTable("lista");
			$sqlSubexame = "SELECT *
							  FROM tipodeexame AS t
							  LEFT JOIN subexame s
							    ON t.txa_codigo = s.txa_codigo
							 WHERE proc_codigo = $proc_codigo";
			$querySubexame = pg_query($sqlSubexame);
			while($regSubexame = pg_fetch_array($querySubexame)){
				
				if($regSubexame[sex_codigo] != ""){
					echo $table->criaLinha(array($regSubexame['sex_subexame']),null,array(4),"S");
				}
				
				$sqlItensDeAnalise = "SELECT i.ite_codigo,
											 i.ite_itemdoexame,
											 v.vlr_valordereferencia,
											 i.ite_itemdoexame
										FROM itensanalise i
										LEFT JOIN valoresdereferencia v
	  									  ON i.ite_codigo = v.ite_codigo
	  								   WHERE i.sex_codigo = $regSubexame[sex_codigo] ORDER BY i.ite_codigo";
				$queryItensDeAnalide = pg_query($sqlItensDeAnalise);				
				while($regItensDeAnalise = pg_fetch_row($queryItensDeAnalide)){
					echo $form->hiddenForm("ite_codigo[]", $regItensDeAnalise[0],"ite_codigo[]");
					$arrayItensLeucograma = array($regItensDeAnalise[0]=>"$regItensDeAnalise[3]");
					echo $form->hiddenForm("ite_nome[]", $arrayItensLeucograma);
					//echo "<pre>".print_r($arrayItensLeucograma,true)."</pre>";
					$sqlAll = "SELECT *
					             FROM resultadoexame
					            WHERE itx_codigo = $itx_codigo
					              AND ite_codigo = $regItensDeAnalise[0]";
					//echo $sqlAll."<br/>";
					$queryAll = pg_query($sqlAll);
					$regAll = pg_fetch_array($queryAll);
					array_push($regItensDeAnalise,$form->inputText("vlr_valor[]", $regAll[vlr_valor],null,"20"));
					echo $table->criaLinha($regItensDeAnalise);
				}
			}
			echo $table->closeTable();
			echo $table->openTable("table",null,null,0);
				$sqlObservacao = "SELECT oe.obs_exa_codigo,
										 oe.obs_exa_observacoes
									FROM procedimento_observacoes as pc
									JOIN observacoes_exames as oe
									  ON pc.obs_exa_codigo = oe.obs_exa_codigo
								   ORDER BY obs_exa_observacoes";
				
				$verificaOpcoes = "SELECT * 
									 FROM itensdoexame_observacoes
									WHERE itx_codigo = $itx_codigo";
				$queryVerificaOpcoes = pg_query($verificaOpcoes);
				$regVerificaOpcoes = pg_fetch_all($queryVerificaOpcoes);
				$marcados = array();
				foreach($regVerificaOpcoes as $teste){
					array_push($marcados, $teste[obs_exa_codigo]);
				}
			echo $table->criaLinha(array($form->inputSelect("obs_exa_codigo[]", null,"Observa&ccedil;&atilde;o",$sqlObservacao,null,null,$marcados,null,null,"style='width:200px'","S","S")),array(100));
			
			for($i = 0; $i < 4; $i++){
				echo $table->criaLinha(array("&nbsp;"));
			}

			echo $table->closeTable();
		
		echo $table->openTable("table");
			echo $table->criaLinha(array($common->commonButton("voltar", "../exa_digitacaoresultado.php?cad_exame=$cad_exame&usu_codigo=$usu_codigo", "voltar.png"),
										 $common->commonButton("salvar",null,"salvar.gif","onClick=\"validador();\"")),array(100));
		echo $table->closeTable();
		echo $form->closeForm();
	}
	
	if($acao == "insert"){
		$cad_exame = $_POST["cad_exame"];
		$usu_codigo = $_POST["usu_codigo"];
		$proc_codigo = $_POST["proc_codigo"];
		$itx_codigo = $_POST[itx_codigo];
		$id_login = $_POST[id_login];
		
		# VALIDACAO DA SOMA DO LEUCOGRAMA
		$validaContaProcedimento = "SELECT * 
										  FROM procedimento as p
										  JOIN procedimento_tipo as pt
										    ON p.proc_tipo_codigo = pt.proc_tipo_codigo 
										 WHERE proc_codigo = $proc_codigo
										   AND proc_tipo_nome = 'HEMOGRAMA'";
		$queryValidaContaProcedimento = pg_query($validaContaProcedimento);
		$numLinhasValidaContaProcedimento = pg_num_rows($queryValidaContaProcedimento);
		// caso ele for hemograma a variacel exameHemograma recebe um valor para fazer a soma dos itens após o insert
		if($numLinhasValidaContaProcedimento > 0){
			include_once $_SESSION[root].$_SESSION[comum]."class/calculosHemogramaClass.php";
			$hemograma = new Hemograma();
			$exameHemograma = "hemograma";
			$v = 0;
			foreach($ite_codigo as $ite_valida){
				$sqlCalculosLeucograma = "SELECT i.ite_codigo,
											     it.ite_tipo_codigo,
												 it.ite_tipo_nome,
												 i.ite_itemdoexame 
											FROM itensanalise AS i
											JOIN itensanalise_tipo as it
										   	  ON i.ite_tipo_codigo = it.ite_tipo_codigo 
									  	   WHERE ite_codigo = $ite_valida
									  	     AND i.ite_tipo_codigo in (7,8,9,10)";
				$querySqlCalculoLeucograma = pg_query($sqlCalculosLeucograma);
				$numLinhasCalculoLeucograma = pg_num_rows($querySqlCalculoLeucograma);
				$regCalculoLeucograma = pg_fetch_array($querySqlCalculoLeucograma);
				if($numLinhasCalculoLeucograma != 0){
					if($regCalculoLeucograma[2] == "EOSINOFILOS"){
						$eosinofilos = $vlr_valor[$v];
					}
					if($regCalculoLeucograma[2] == "LINFOCITOS"){
						$linfocitos = $vlr_valor[$v];
					}
					if($regCalculoLeucograma[2] == "MONOCITOS"){
						$monocitos = $vlr_valor[$v];
					}
					if($regCalculoLeucograma[2] == "SEGMENTADOS"){
						$segmentados = $vlr_valor[$v];
					}
				}
				$v++;
			}
			$somaLeucograma = $hemograma->somaLeucograma($eosinofilos, $linfocitos, $monocitos, $segmentados);
			if($somaLeucograma == 100){
				echo $common->modalMsg("ERRO", "Os valores digitados s&atilde;o superiores ao permitido","$PHP_SELF?acao=form_add&cad_exame=$cad_exame&proc_codigo=$proc_codigo&itx_codigo=$itx_codigo&usu_codigo=$usu_codigo");
			}
		}
		# FIM DA VALIDACAO DA SOMA DO LEUCOGRAMA
		
		$i=0;
		foreach ($ite_codigo as $val){
			
			
			$stmt = "INSERT INTO resultadoexame ( 
								 itx_codigo, 
								 ite_codigo, 
								 id_login, 
								 res_dataresultado, 
								 res_horaresultado, 
							 	 vlr_valor, 
								 cad_exame, 
								 proc_codigo
					  ) VALUES ( 
								 $itx_codigo, 
								 $val, 
								 $id_login, 
								 ".CURRENT_DATE.", 
								 ".CURRENT_TIME.", 
								 ".($vlr_valor[$i] == "" ? "null" : "'$vlr_valor[$i]'").", 
								 ".intval($cad_exame).", 
								 ".intval($proc_codigo)." )";
			/*if($queryStmt = pg_query($stmt)){
				$erro .= "";
			}else{
				$erro .= "ERRO";
			}*/
			$i++;
		}
		
		exit();
		
		if($erro != ""){
			echo $common->modalMsg("ERRO", "Erro ao inserir","$PHP_SELF?acao=form_add&cad_exame=$cad_exame&proc_codigo=$proc_codigo&itx_codigo=$itx_codigo&usu_codigo=$usu_codigo",$stmt);
		}
		
		/********************************************************/
		// ELE VALIDA DEPOIS DO INSERT PARA ATUALIZAR OS VALORES JA INSERIDOS ANTERIORMENTE COMO VAZIO
			$cont = 0;
			/*$validaContaProcedimento = "SELECT * 
										  FROM procedimento as p
										  JOIN procedimento_tipo as pt
										    ON p.proc_tipo_codigo = pt.proc_tipo_codigo 
										 WHERE proc_codigo = $proc_codigo
										   AND proc_tipo_nome = 'HEMOGRAMA'";
			$queryValidaContaProcedimento = pg_query($validaContaProcedimento);
			$numLinhasValidaContaProcedimento = pg_num_rows($queryValidaContaProcedimento);*/
			if($exameHemograma == "hemograma"){
				include_once $_SESSION[root].$_SESSION[comum]."class/calculosHemogramaClass.php";
				$hemograma = new Hemograma();
				
				foreach($ite_codigo as $ite){
					$validaContaItensAnalise = "SELECT i.ite_codigo,
													   it.ite_tipo_codigo,
													   it.ite_tipo_nome,
													   i.ite_itemdoexame
											      FROM itensanalise as i
											      JOIN itensanalise_tipo as it
													ON it.ite_tipo_codigo = i.ite_tipo_codigo
										    	 WHERE i.ite_codigo = $ite
										    	   AND it.ite_tipo_codigo in (1,2,3)";
					//echo $validaContaItensAnalise."<br/>";
					$queryValidaContaItensAnalise = pg_query($validaContaItensAnalise);
					$numLinhasValidaContaItensAnalise = pg_num_rows($queryValidaContaItensAnalise);
					$regLinhasValidaContaItensAnalise = pg_fetch_array($queryValidaContaItensAnalise);
					
					if($numLinhasValidaContaItensAnalise > 0){
						if($regLinhasValidaContaItensAnalise[2] == "ERITROCITOS"){
							$eritrocitos = $vlr_valor[$cont];

						}
						if($regLinhasValidaContaItensAnalise[2] == "HEMOGLOBINA"){
							$hemoglobina = $vlr_valor[$cont];
						}
						if($regLinhasValidaContaItensAnalise[2] == "HEMATOCRITO"){
							$hematocrito = $vlr_valor[$cont];
						}
					}
					$cont++;
				}
				$sqlValoresCalculados = "SELECT i.ite_codigo,
											    it.ite_tipo_codigo,
											    it.ite_tipo_nome,
											    i.ite_itemdoexame
									       FROM itensanalise as i
									       JOIN itensanalise_tipo as it
											 ON it.ite_tipo_codigo = i.ite_tipo_codigo
										  WHERE it.ite_tipo_codigo in (4,5,6)";
				$queryValoresCalculados = pg_query($sqlValoresCalculados);
				while($valoresCalculados = pg_fetch_array($queryValoresCalculados)){
					if($valoresCalculados[1] == 4){
						$vlr = $hemograma->calculoVcm($hematocrito,$eritrocitos);
						//echo $hemograma->calculoVcm($hematocrito,$eritrocitos);
						//exit();
					}
					if($valoresCalculados[1] == 5){
						$vlr = $hemograma->calculoHcm($hemoglobina, $eritrocitos);
					}
					if($valoresCalculados[1] == 6){
						$vlr = $hemograma->calculoChcm($hemoglobina, $hematocrito);
					}
					$updateCalculados = "UPDATE resultadoexame
										    SET vlr_valor = '$vlr'
										  WHERE ite_codigo = $valoresCalculados[1]";
					$queryUpdateCalculados = pg_query($updateCalculados);
				}
				
				//echo "erito".$eritocitos."<br/>"."hemog".$hemoglobina."<br />"."hemat".$hematocrito."<br/>";
			} 

		foreach($obs_exa_codigo as $ind){
			$sqlRelacionamento = "INSERT INTO itensdoexame_observacoes(
												itx_codigo,
												obs_exa_codigo)
										 VALUES ($itx_codigo,
										 		 ".($ind == "" ? "null" : "$ind").")";
			$queryRelacionamento = pg_query($sqlRelacionamento);
		}
		echo $common->modalMsg("OK","Salvo com sucesso!","../exa_digitacaoresultado.php?cad_exame=$cad_exame&usu_codigo=$usu_codigo&id_login=$id_login");
		
	}
	
	if($acao == "update"){

		$sqlResultados = "SELECT * FROM resultadoexame where itx_codigo = $itx_codigo order by ite_codigo";
		//echo $sqlResultados;
		//exit();
		$queryResultados = pg_query($sqlResultados);
		$cont = 0;
		
		/*akeeee*/
		
		
		/*ateh aki*/
		
		$i=0;
		while($regResultados = pg_fetch_array($queryResultados)){
			$stmt = "UPDATE resultadoexame 
						SET 
							id_login = ".intval($id_login).", 
							res_dataresultado = ".CURRENT_DATE.", 
							res_horaresultado = ".CURRENT_TIME.", 
							vlr_valor = ".($vlr_valor[$i] == "" ? "null" : "'$vlr_valor[$i]'")." , 
							cad_exame = ".intval($cad_exame).", 
							proc_codigo = ".intval($proc_codigo)."
					  WHERE res_codigo = $regResultados[res_codigo] ";
			echo $stmt."<br/>";
			if($queryStmt = pg_query($stmt)){
				$erro .= "";
			}else{
				$erro .= "ERRO";
			}
			$i++;
		}
		//exit();
		if($erro != ""){
			echo $common->modalMsg("ERRO", "Erro ao editar","$PHP_SELF?acao=form_add&cad_exame=$cad_exame&proc_codigo=$proc_codigo&itx_codigo=$itx_codigo&usu_codigo=$usu_codigo",$stmt);
		}
		foreach($obs_exa_codigo as $ind){
			$condicaoSelect .= $ind.",";
			
			$stmtItens = "UPDATE itensdoexame_observacoes
							  SET obs_exa_codigo = $ind,
							      itx_codigo = $itx_codigo
						    WHERE obs_exa_codigo = $ind";
			$queryItens = pg_query($stmtItens);
			if(pg_affected_rows($queryItens) == 0){
				echo pg_affected_rows($queryItens);
				$sqlRelacionamento = "INSERT INTO itensdoexame_observacoes(
											      itx_codigo,
											      obs_exa_codigo)
									      VALUES ($itx_codigo,
									 		      ".($ind == "" ? "null" : "$ind").")";
				$queryRelacionamento = pg_query($sqlRelacionamento);
			}
		}
		
		$subs = substr($condicaoSelect, 0, -1);
		$selectDelete = "SELECT * FROM itensdoexame_observacoes WHERE obs_exa_codigo not in ($subs)"; 
		$queryDelete = pg_query($selectDelete);
		while($regDelete = pg_fetch_array($queryDelete)){
			$deleteObservacao = "DELETE FROM itensdoexame_observacoes WHERE itx_obs_codigo = $regDelete[itx_obs_codigo]";
			$queryDelete = pg_query($deleteObservacao);
		}
		
		///aoaokaokoakkoakoaokaokaokaoka
		
		$validaContaProcedimento = "SELECT * 
									  FROM procedimento as p
									  JOIN procedimento_tipo as pt
									    ON p.proc_tipo_codigo = pt.proc_tipo_codigo 
									 WHERE proc_codigo = $proc_codigo
									   AND proc_tipo_nome = 'HEMOGRAMA'";
		$queryValidaContaProcedimento = pg_query($validaContaProcedimento);
		$numLinhasValidaContaProcedimento = pg_num_rows($queryValidaContaProcedimento);
	
		if($numLinhasValidaContaProcedimento > 0){
			include_once $_SESSION[root].$_SESSION[comum]."class/calculosHemogramaClass.php";
			$hemograma = new Hemograma();
			
			foreach($ite_codigo as $ite){
				$validaContaItensAnalise = "SELECT i.ite_codigo,
												   it.ite_tipo_codigo,
												   it.ite_tipo_nome,
												   i.ite_itemdoexame
										      FROM itensanalise as i
										      JOIN itensanalise_tipo as it
												ON it.ite_tipo_codigo = i.ite_tipo_codigo
									    	 WHERE i.ite_codigo = $ite
									    	   AND it.ite_tipo_codigo in (1,2,3)";
				//echo $validaContaItensAnalise."<br/>";
				$queryValidaContaItensAnalise = pg_query($validaContaItensAnalise);
				$numLinhasValidaContaItensAnalise = pg_num_rows($queryValidaContaItensAnalise);
				$regLinhasValidaContaItensAnalise = pg_fetch_array($queryValidaContaItensAnalise);
				
				if($numLinhasValidaContaItensAnalise > 0){
					
					if($regLinhasValidaContaItensAnalise[2] == "ERITROCITOS"){
						$eritrocitos = $vlr_valor[$cont];
					}
					if($regLinhasValidaContaItensAnalise[2] == "HEMOGLOBINA"){
						$hemoglobina = $vlr_valor[$cont];
					}
					if($regLinhasValidaContaItensAnalise[2] == "HEMATOCRITO"){
						$hematocrito = $vlr_valor[$cont];
					}
				}
				$cont++;
			}
			$sqlValoresCalculados = "SELECT i.ite_codigo,
										    it.ite_tipo_codigo,
										    it.ite_tipo_nome,
										    i.ite_itemdoexame
								       FROM itensanalise as i
								       JOIN itensanalise_tipo as it
										 ON it.ite_tipo_codigo = i.ite_tipo_codigo
									  WHERE it.ite_tipo_codigo in (4,5,6)";
			$queryValoresCalculados = pg_query($sqlValoresCalculados);
			while($valoresCalculados = pg_fetch_array($queryValoresCalculados)){
				if($valoresCalculados[1] == 4){
					$vlr = $hemograma->calculoVcm($hematocrito,$eritrocitos);
				}
				if($valoresCalculados[1] == 5){
					$vlr = $hemograma->calculoHcm($hemoglobina, $eritrocitos);
				}
				if($valoresCalculados[1] == 6){
					$vlr = $hemograma->calculoChcm($hemoglobina, $hematocrito);
				}
				$updateCalculados = "UPDATE resultadoexame
									    SET vlr_valor = '$vlr'
									  WHERE ite_codigo = $valoresCalculados[1]";
				//echo $updateCalculados."<br/>";
				
				$queryUpdateCalculados = pg_query($updateCalculados);
			}
			
			//echo "erito".$eritocitos."<br/>"."hemog".$hemoglobina."<br />"."hemat".$hematocrito."<br/>";
		}
			
	}