<?php
    //echo "<pre>".print_r($_REQUEST,1);
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	ini_set("display_errors", 1);

	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/calculosHemogramaClass.php";

	echo "\n<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/jquery-1.5.2.min.js'></script>\n";
	echo "<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/ajax_motor.js'></script>";
	echo "<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/tiny_mce/tiny_mce.js'></script>";



?>
<script type="text/javascript">
	tinyMCE.init({
		language : 'pt',
		mode : "textareas",
		theme : "advanced",
		skin : "o2k7",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor",
		theme_advanced_buttons2 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true
	});
</script>
<script>
$(function(){

	// adiciona o texto do select no textarea
	$("#obs_exa_codigo").bind("click",function(){
		tinyMCE.execCommand('mceInsertContent',false,$(this).val());
	});
});


function validador (){
	proc_codigo = document.getElementById("proc_codigo").value;
	//alert(proc_codigo);
	if(proc_codigo == "4629"){ // hemograma
		inputs = document.getElementsByTagName("input");
		var ite_codigo = "";
		for(var i in inputs){
			if(/ite_codigo/.test(inputs[i].name)){
				var ite_codigo = inputs[i].value;
			}
		}
		var eosinofilos = document.getElementById("vlr_valor|8").value.replace(/[^0-9]+/gi,"");

		var linfocitos = document.getElementById("vlr_valor|10").value.replace(/[^0-9]+/gi,"");
		var monocitos = document.getElementById("vlr_valor|11").value.replace(/[^0-9]+/gi,"");
		var neutrofilos = document.getElementById("vlr_valor|16").value.replace(/[^0-9]+/gi,"");

		window.console && console.log("eosinofilos: "+eosinofilos);
		window.console && console.log("linfocitos: "+linfocitos);
		window.console && console.log("monocitos: "+monocitos);
		window.console && console.log("neutrofilos: "+neutrofilos);

		//alert(eosinofilos + linfocitos + monocitos + segmentados );
		var soma=((parseFloat(eosinofilos))+(parseFloat(linfocitos))+(parseFloat(monocitos))+(parseFloat(neutrofilos)));
		//alert(soma);
		if(soma != 100){
			if(!confirm("Os valores digitados no subexame Leucograma sao diferentes da quantidade permitida! Deseja continuar ?")){
				return false;
			}
		}
	}
	document.valores.submit();

}
</script>
<?php
	$hemograma = new Hemograma();

	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();
	echo $common->menuTab(array("Digita&ccedil;&atilde;o de Laudo"));
	echo $common->bodyTab("1");

	//Conferindo se laudo j� foi liberado, se j� foi n�o pode ser editado
	$sqlConfere = "SELECT * FROM agenda_itens WHERE agei_codigo = '".$_GET["agei_codigo"]."' AND usr_codigo_bioquimico IS NOT NULL";
	$queryConfere = pg_query($sqlConfere);
	$numConfere = pg_num_rows($queryConfere);
	if ($numConfere > 0) {
		echo $common->modalMsg("ERRO","Laudo n�o pode ser editado! Laudo j� foi liberado para o paciente!","../exa_digitacaoresultado2.php?age_codigo=$age_codigo&usu_codigo=$usu_codigo&id_login=$id_login");
	}
	$agenda_codigo = $_GET[age_codigo];
	if($acao == "form_add"){

		$sqlProcedimento = "SELECT proc_nome
							  FROM procedimento
							 WHERE proc_codigo = $proc_codigo";
		$queryProcedimento = pg_query($sqlProcedimento);
		$regProcedimento = pg_fetch_array($queryProcedimento);
		echo $common->divisoria($regProcedimento['proc_nome']);
		echo $form->openForm("laudo2.php","POST","valores");
			if($agei_codigo == "" || $agei_codigo == "null"){// se n�o vier o AGEI_CODIGO "agendamento laboratorio" ele busca pelo preenchimento do medico "age_codigo"
				$where = "age_codigo = $agenda_codigo";
			}else{
				$where = "agei_codigo = $agei_codigo";
			}
			$verificaAcao = "SELECT *
					     FROM resultadoexame
					    WHERE $where";
			$queryVerificaAcao = pg_query($verificaAcao);
			$regVerificaAcao = pg_fetch_array($queryVerificaAcao);
			$numLinhasVerificaAcao = pg_num_rows($queryVerificaAcao);
			if($numLinhasVerificaAcao == 0){
				echo $form->hiddenForm("acao", "insert");
			}else{
				echo $form->hiddenForm("acao", "update");
			}
			$observacoesTxt = $regVerificaAcao['res_observacao'];
			echo $form->hiddenForm("usu_codigo", "$usu_codigo");
			echo $form->hiddenForm("proc_codigo", "$proc_codigo","proc_codigo");
			echo $form->hiddenForm("proc_nome", $regProcedimento['proc_nome'],"proc_nome");
			echo $form->hiddenForm("itx_codigo", "$itx_codigo");
			echo $form->hiddenForm("agei_codigo", "$agei_codigo");
			echo $form->hiddenForm("age_codigo", "$age_codigo");
			echo $form->hiddenForm("id_login", "$id_login");
			echo $table->openTable("lista");
			$sqlSubexame = "SELECT *
							  FROM tipodeexame AS t
							  LEFT JOIN subexame s
							    ON t.txa_codigo = s.txa_codigo
							 WHERE proc_codigo = $proc_codigo";
			$querySubexame = pg_query($sqlSubexame);
			$numLinhasSubexame = pg_num_rows($querySubexame);
			while($regSubexame = pg_fetch_array($querySubexame)){

				if($regSubexame[sex_codigo] != ""){
					echo $table->criaLinha(array($regSubexame['sex_subexame']),null,array(5),"S");
					$where = "WHERE i.sex_codigo = $regSubexame[sex_codigo]";
				}else{
					$sqlTipoExame = "SELECT * FROM tipodeexame where proc_codigo = $proc_codigo";
					$queryTipoExame = pg_query($sqlTipoExame);
					$regTipoExame = pg_fetch_array($queryTipoExame);
					$where = "WHERE i.txa_codigo = $regTipoExame[txa_codigo]";
				}

				$mesesUsuario = "SELECT usu_sexo,
										((DATE_PART('YEAR', AGE(NOW(), usu_datanasc))*12)+DATE_PART('MONTH', AGE(NOW(), usu_datanasc))) AS meses from usuario
 								  WHERE usu_codigo = $usu_codigo";
				$queryMesesUsuario = pg_query($mesesUsuario);


				$regMesesUsuario = pg_fetch_array($queryMesesUsuario);
				$idadeMeses = $regMesesUsuario[meses];

				/*$sqlItensDeAnalise = "SELECT distinct i.ite_codigo,
											 i.ite_itemdoexame,
											 v.vlr_valordereferencia || ' ' || i.ite_tipo_medida
										FROM itensanalise i
										LEFT JOIN valoresdereferencia v
	  									  ON i.ite_codigo = v.ite_codigo
	  									  $where
	  									 AND (vlr_faixa_etaria_inicio <= ".intval($idadeMeses)."
	  									 	 OR vlr_faixa_etaria_inicio IS NULL)
	  									 AND (vlr_faixa_etaria_fim > ".intval($idadeMeses)."
	  									  	  OR vlr_faixa_etaria_fim IS NULL)
	  									 AND (vlr_sexo = '$regMesesUsuario[usu_sexo]' OR vlr_sexo is null)
	  								    ORDER BY i.ite_codigo";*/


	  			$sqlItensDeAnalise = "SELECT distinct i.ite_codigo,
											 i.ite_itemdoexame,
											 v.vlr_valordereferencia || ' ' || i.ite_tipo_medida
										FROM itensanalise i
										LEFT JOIN valoresdereferencia v
	  									  ON i.ite_codigo = v.ite_codigo
	  									  $where
	  									 AND $idadeMeses BETWEEN COALESCE(v.vlr_faixa_etaria_inicio,0) AND COALESCE(v.vlr_faixa_etaria_fim,9999999)
	  									 AND (vlr_sexo = '$regMesesUsuario[usu_sexo]' OR vlr_sexo is null)
	  								    ORDER BY i.ite_codigo";
				//echo $sqlItensDeAnalise."<br/>";
				$queryItensDeAnalide = pg_query($sqlItensDeAnalise);
				$numRowsUsuario = pg_num_rows($queryItensDeAnalide);
				if($numRowsUsuario == "" || $numRowsUsuario == 0){
					echo $common->modalMsg("ERRO","Verifique se o paciente possui as informa&ccedil;&otilde;es Sexo e Data de Nascimento","../exa_digitacaoresultado2.php?age_codigo=$age_codigo&usu_codigo=$usu_codigo&id_login=$id_login");
				}
				while($regItensDeAnalise = pg_fetch_row($queryItensDeAnalide)){
					$regItensDeAnalise[2] = nl2br($regItensDeAnalise[2]);
					echo $form->hiddenForm("ite_codigo[$regItensDeAnalise[0]]", $regItensDeAnalise[0]);
					$arrayItensLeucograma = array($regItensDeAnalise[0]=>"$regItensDeAnalise[3]");
					if($agei_codigo == "" || $agei_codigo == "null"){// se n�o vier o AGEI_CODIGO "agendamento laboratorio" ele busca pelo preenchimento do medico "age_codigo"
						$where = "age_codigo = $agenda_codigo";
					}else{
						$where = "agei_codigo = $agei_codigo";
					}
					$sqlAll = "SELECT *
					             FROM resultadoexame
					            WHERE $where
					              AND ite_codigo = $regItensDeAnalise[0]";
					$queryAll = pg_query($sqlAll);
					$regAll = pg_fetch_array($queryAll);
					#$observacoesTxt = $regAll['res_observacao'];
					//$mostra = substr(,3,0);
                                        //desabilitado o input vcm hcm chcm
					$rest = substr($regAll[vlr_valor], 0, 30);
                                        if(in_array($regItensDeAnalise[0],array(4,5,6))){
                                        $readonly=true;
                                        }else{
                                        $readonly=false;
                                        }
					if( $rest == NULL &&
							strpos(trim($regItensDeAnalise[2]),"0") == false &&
							strpos(trim($regItensDeAnalise[2]),"1") == false &&
							strpos(trim($regItensDeAnalise[2]),"2") == false &&
							strpos(trim($regItensDeAnalise[2]),"3") == false &&
							strpos(trim($regItensDeAnalise[2]),"4") == false &&
							strpos(trim($regItensDeAnalise[2]),"5") == false &&
							strpos(trim($regItensDeAnalise[2]),"6") == false &&
							strpos(trim($regItensDeAnalise[2]),"7") == false &&
							strpos(trim($regItensDeAnalise[2]),"8") == false &&
							strpos(trim($regItensDeAnalise[2]),"9") == false &&
							$regItensDeAnalise[0] != 626) {
								$rest = $regItensDeAnalise[2];
							}
					if(strpos(trim($rest),"/mm")){
						$rest = "0%";
					}
					array_push($regItensDeAnalise,$form->inputText("vlr_valor[$regItensDeAnalise[0]]", trim($rest),null,"20",70,null,null,$readonly,null,null,"vlr_valor|$regItensDeAnalise[0]"));
					echo $table->criaLinha($regItensDeAnalise);
				}
			}
			echo $table->closeTable();
			echo $table->openTable("table",null,null,0);
			$sqlObservacao = "SELECT oe.obs_exa_observacoes,
									 oe.obs_exa_observacoes
								FROM procedimento_observacoes as pc
								JOIN observacoes_exames as oe
								  ON pc.obs_exa_codigo = oe.obs_exa_codigo
							   WHERE pc.proc_codigo = $proc_codigo
							   ORDER BY obs_exa_observacoes";


			$opcoes = array(
				"nome" 			=> "obs_exa_codigo",
				"caption" 		=> "Observa&ccedil;&atilde;o",
				"sql"			=> $sqlObservacao,
				"fSizeInterno" 	=> "style='width:200px'",
				"multiple"  	=> 'S',
				"disabledFirst"	=> 'S'
			);

			echo "<tr>";
				echo "<td valign=\"top\">".$form->inputSelect($opcoes)."</td>";
				echo "<td><textarea name=\"observacoes_txt\" id=\"observacoes_txt\" cols=\"70\" rows=\"10\">$observacoesTxt</textarea></td>";
			echo "</tr>";

				#echo $table->criaLinha(array($form->inputSelect("obs_exa_codigo[]", null,"Observa&ccedil;&atilde;o",$sqlObservacao,null,null,$marcados,null,null,"style='width:200px'","S","S"),"<textarea cols=\"70\" rows=\"10\"></textarea>"),array(200,250));
			/*
			for($i = 0; $i < 4; $i++){
				echo $table->criaLinha(array("&nbsp;"));
			}*/

			echo $table->closeTable();

		echo $table->openTable("table");
			echo $table->criaLinha(array($common->commonButton("voltar", "../exa_digitacaoresultado2.php?age_codigo=$age_codigo&usu_codigo=$usu_codigo", "voltar.png"),
										 $common->commonButton("salvar",null,"salvar.gif","onClick=\"validador();\"")),array(100));
		echo $table->closeTable();
		echo $form->closeForm();
	}

	if($acao == "insert"){
			$i=1;
		//$stmt = "n�o entrou";
		//echo "<pre>".print_r($ite_codigo);
		$ite_codigo = $_REQUEST["ite_codigo"];
		foreach ($ite_codigo as $ind=>$val){
			$validaContaProcedimento = "SELECT *
										  FROM procedimento as p
										  JOIN procedimento_tipo as pt
										    ON p.proc_tipo_codigo = pt.proc_tipo_codigo
										 WHERE proc_codigo = $proc_codigo
											   AND proc_tipo_nome = 'HEMOGRAMA'";
			$queryValidaContaProcedimento = pg_query($validaContaProcedimento);
			$numLinhasValidaContaProcedimento = pg_num_rows($queryValidaContaProcedimento);
			// caso ele for hemograma a variacel exameHemograma recebe um valor para fazer a soma dos itens ap�s o insert
			if($numLinhasValidaContaProcedimento > 0){
				$sqlValoresCalculados = "SELECT i.ite_codigo,
												    it.ite_tipo_codigo,
												    it.ite_tipo_nome,
												    i.ite_itemdoexame
										       FROM itensanalise as i
										       JOIN itensanalise_tipo as it
												 ON it.ite_tipo_codigo = i.ite_tipo_codigo
											  WHERE it.ite_tipo_codigo in (4,5,6)
											    AND ite_codigo = $val";
				$queryValoresCalculados = pg_query($sqlValoresCalculados);
				$resSqlValoresCalculados = pg_fetch_array($queryValoresCalculados);

				$sqlItensAnalise = "SELECT
											retira_acentos(UPPER(TRIM(ite_itemdoexame))) AS itemdoexame
										FROM
											itensanalise
										WHERE ite_codigo = $val";
				$queryItensAnalise = pg_query($sqlItensAnalise);
				$rowItensAnalise = pg_fetch_array($queryItensAnalise);

				if($val == 1 ){
					$eritrocitos = $vlr_valor[$val];
				}
				if($val == 2){
					$hemoglobina = $vlr_valor[$val];
				}
				if($val == 3){
					$hematocrito = $vlr_valor[$val];
				}
				//////////////////////////////////////////////////////////////////////////////////////////
				if($val == 4){
					$vlr_valor[$val] = $hemograma->calculoVcm($hematocrito,$eritrocitos);
				}
				if($val == 5){
					$vlr_valor[$val] = $hemograma->calculoHcm($hemoglobina,$eritrocitos);
				}
				if($val == 6){
					$vlr_valor[$val] = $hemograma->calculoChcm($hemoglobina,$hematocrito);
				}
				if($val == 7){
					$leucocitos = $vlr_valor[$val];
					$vlr_valor_m3 = $vlr_valor[$val];
				}
				///////////////////////////////////////////////////////////////////////////////////////////////////
				if($val == 8){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 9){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 10){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 11){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 12){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 13){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 14){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 15){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					//$vlr_valor_m3 = $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				if($val == 16){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					//$vlr_valor[$val] .= "%"."                              ".$hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]);
					$vlr_valor[$val] .= "%";
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				// BLASTOS
				if($rowItensAnalise["itemdoexame"] == "BLASTOS"){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					$vlr_valor[$val] .= "%";
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
				// PROMIELOCITOS
				if($rowItensAnalise["itemdoexame"] == "PROMIELOCITOS"){
					if(empty($vlr_valor[$val])){
						$vlr_valor[$val] = "0";
					}
					$vlr_valor[$val] .= "%";
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$val]));
				}
			}
			//COLOCAR A POSICAO DO ARRAY...
			$observacoesTxt = $_POST['observacoes_txt'];
			$stmt = "INSERT INTO resultadoexame (
								 ite_codigo,
								 id_login,
								 res_dataresultado,
								 res_horaresultado,
								 res_observacao,
							 	 vlr_valor,
								 vlr_valor_m3,
								 proc_codigo,
								 res_liberado,
								 agei_codigo,
								 age_codigo
					  ) VALUES (
								 $val,
								 $id_login,
								 ".CURRENT_DATE.",
								 ".CURRENT_TIME.",
								 '$observacoesTxt',
								 ".($vlr_valor[$val] == "" ? "null" : "'$vlr_valor[$val]'").",
								 ".($vlr_valor_m3 == "" ? "null" : "'$vlr_valor_m3'").",
								 ".intval($proc_codigo).",
								 'S',
								 ".($agei_codigo == "" ? "null" : "$agei_codigo").",
								 ".($agenda_codigo == "" ? "null" : "$agenda_codigo").")";
			if($queryStmt = pg_query($stmt)){
				if($agenda_codigo){
					echo "
					<script>
						window.close();
					</script>";
				}
				$erro .= "";
			}else{
				$erro .= "ERRO";
			}
			$i++;
		}
		if($erro != ""){
			echo $common->modalMsg("ERRO", "Erro ao inserir","$PHP_SELF?acao=form_add&agei_codigo=$agei_codigo&proc_codigo=$proc_codigo&usu_codigo=$usu_codigo",$stmt);
			exit;
		}


		/*
		foreach($obs_exa_codigo as $ind){
			$sqlRelacionamento = "INSERT INTO itensdoexame_observacoes(
												itx_codigo,
												obs_exa_codigo)
										 VALUES ($itx_codigo,
										 		 ".($ind == "" ? "null" : "$ind").")";
			$queryRelacionamento = pg_query($sqlRelacionamento);
		}*/
			echo $common->modalMsg("OK","Salvo com sucesso!","../exa_digitacaoresultado2.php?age_codigo=$age_codigo");
	}

	if($acao == "update"){
		if($agei_codigo == "" || $agei_codigo == null){
			$where = "age_codigo = $agenda_codigo";
		}else{
			$where = "agei_codigo = $agei_codigo";
		}
		$sqlResultados = "SELECT * FROM resultadoexame where $where order by ite_codigo";
		$queryResultados = pg_query($sqlResultados);
		$i=1;
		//echo "<pre>".print_r($vlr_valor,true)."</pre>";
		while($regResultados = pg_fetch_array($queryResultados)){
				$sqlItensAnalise = "SELECT
											retira_acentos(UPPER(TRIM(ite_itemdoexame))) AS itemdoexame
										FROM
											itensanalise
										WHERE ite_codigo = '".$regResultados["ite_codigo"]."'";
				$queryItensAnalise = pg_query($sqlItensAnalise);
				$rowItensAnalise = pg_fetch_array($queryItensAnalise);

				$vlr_valor_m3 = null;
				$posicao = $regResultados[ite_codigo];
				if($regResultados[ite_codigo] == 1 ){
					$eritrocitos = $vlr_valor[$posicao];
				}
				if($regResultados[ite_codigo] == 2){
					$hemoglobina = $vlr_valor[$posicao];
				}
				if($regResultados[ite_codigo] == 3){
					$hematocrito = $vlr_valor[$posicao];
				}
				if($regResultados[ite_codigo] == 4){
					$vlr_valor[$posicao] = $hemograma->calculoVcm($hematocrito,$eritrocitos);
				}
				if($regResultados[ite_codigo] == 5){
					$vlr_valor[$posicao] = $hemograma->calculoHcm($hemoglobina,$eritrocitos);
				}
				if($regResultados[ite_codigo] == 6){
					$vlr_valor[$posicao] = $hemograma->calculoChcm($hemoglobina,$hematocrito);
				}
				if($regResultados[ite_codigo] == 7){
					$leucocitos = $vlr_valor[$posicao];
					$vlr_valor_m3 = $vlr_valor[$posicao];
				}

				if($regResultados[ite_codigo] == 8){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 9){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 10){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 11){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 12){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 13){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 14){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 15){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				if($regResultados[ite_codigo] == 16){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				// BLASTOS
				if($rowItensAnalise["itemdoexame"] == "BLASTOS"){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}
				// PROMIELOCITOS
				if($rowItensAnalise["itemdoexame"] == "PROMIELOCITOS"){
					if(empty($vlr_valor[$posicao])){
						$vlr_valor[$posicao] = "0 %";
					}
					$vlr_valor_m3 = ($hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]) == "null" ? "0" : $hemograma->porcentagemLeucograma($leucocitos,$vlr_valor[$posicao]));
				}

			$observacoesTxt = $_POST['observacoes_txt'];
			$stmt = "UPDATE resultadoexame
						SET
							id_login = ".intval($id_login).",
							res_dataresultado = ".CURRENT_DATE.",
							res_horaresultado = ".CURRENT_TIME.",
							vlr_valor = ".($vlr_valor[$posicao] == "" ? "null" : "'$vlr_valor[$posicao]'").",
							vlr_valor_m3 = ".($vlr_valor_m3 == "" ? "null" : "'$vlr_valor_m3'").",
							agei_codigo = ".($agei_codigo == "" ? "null" : "$agei_codigo").",
							proc_codigo = ".intval($proc_codigo).",
							res_observacao = '$observacoesTxt'
					  WHERE res_codigo = $regResultados[res_codigo]";
			//die($stmt);
			if($queryStmt = pg_query($stmt)){
				if($agenda_codigo){
					echo "
					<script>
						window.close();
					</script>";
				}
				$erro .= "";
			}else{
				$erro .= "ERRO";
			}
			$i++;
		}
	if($erro != ""){
			echo $common->modalMsg("ERRO", "Erro ao editar","$PHP_SELF?acao=form_add&agei_codigo=$agei_codigo&proc_codigo=$proc_codigo&usu_codigo=$usu_codigo",$stmt);
		}
		foreach($obs_exa_codigo as $ind){
			$condicaoSelect .= $ind.",";

			$stmtItens = "UPDATE itensdoexame_observacoes
							  SET obs_exa_codigo = $ind,
							      agei_codigo = $agei_codigo
						    WHERE obs_exa_codigo = $ind";
			$queryItens = pg_query($stmtItens);
			if(pg_affected_rows($queryItens) == 0){
				$t = "INSERT INTO itensdoexame_observacoes(
											      agei_codigo,
											      obs_exa_codigo)
									      VALUES ($agei_codigo,
									 		      ".($ind == "" ? "null" : "$ind").")";
				$queryRelacionamento = pg_query($t) or die(pg_last_error());
			}
		}

		$subs = substr($condicaoSelect, 0, -1);
		$where = ($subs == "" ? "" : "WHERE obs_exa_codigo not in ($subs)");

		$selectDelete = "SELECT * FROM itensdoexame_observacoes $where";
		$queryDelete = pg_query($selectDelete);
		while($regDelete = pg_fetch_array($queryDelete)){
			$deleteObservacao = "DELETE FROM itensdoexame_observacoes $where";
			$queryDelete = pg_query($deleteObservacao);
		}
		echo $common->modalMsg("OK","Editado com sucesso!","../exa_digitacaoresultado2.php?age_codigo=$age_codigo&usu_codigo=$usu_codigo&id_login=$id_login");
	}

