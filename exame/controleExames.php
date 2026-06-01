<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
?>
<script src="<?= $_SESSION[linkroot].$_SESSION[comum];?>library/js/ajax_motor.js"></script>
<script language="JavaScript">
	function marcaDesmarca(){
		var j = -1;
		for (i = 0; i < document.requisicoes.elements.length; i++){
			if(document.requisicoes.elements[i].type == "checkbox"){
				if (j == -1){
					j = i; //seleciona o primeiro checkbox para saber qual operação realizar
					marcar = !document.requisicoes.elements[j].checked; //marcar recebe o contrário do primeiro checkbox
				}
				document.requisicoes.elements[i].checked = marcar;// marca todos os checkbox com o contrário do primeiro
			}
		}
		img = document.getElementById('img');
		if (!document.requisicoes.elements[j].checked){
			img.src = "<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/uncheckAll.png";
		}else{
			img.src = "<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/checkAll.png";
		}
	}
	function listaExames(codigo, nome){
		url = "listaExames.php?acao=listar&codigo="+codigo+"&nome="+nome;
		ajax_tudo(url, retornaExames);
	}
	function retornaExames(txt){
		div = document.getElementById("listagem");
		div.innerHTML = txt;
	}
	function recepcionar(codigo, linha){
		url = "listaExames.php?acao=recepcao&codigo="+codigo+"&linha="+linha;
		ajax_tudo(url, refresh);
	}
	function cancelar(codigo, linha){
		url = "listaExames.php?acao=cancelar&codigo="+codigo+"&linha="+linha;
		ajax_tudo(url, refresh);
	}
	function refresh(txt){
		resposta = txt.split("|");
		txts = resposta[0];
		linha = resposta[1];
		if (txts != "NADA"){
			td = document.getElementById(linha+"3");
			if (txts == "R"){
				td.innerHTML = "Recepcionado";
			}else if(txts == "C" ){
				td.innerHTML = "Cancelado";
			}else{
				td.innerHTML = "Agendado";
			} 
		}else{
			alert("Só é possível "+codigo+" no dia do agendamento.");
		}
	}
	function reload(id_login){
		med_codigo = document.getElementById("prestador_servico");
		document.location.href = "controleExames.php?id_login="+id_login+"&med_codigo="+med_codigo.value;
	}
	function agenda_libera(id_login,palavra_chave){
		checks = document.getElementsByName('exames[]');
		var reqs = "";
		for(i = 0; i < checks.length; i++){
			if (checks[i].checked == true){
				reqs += checks[i].value + ", ";
			}
		}
		requisicoes = reqs.substr(0, reqs.length-2);
		window.open("agendarLiberar.php?requisicoes="+requisicoes+"&id_login="+id_login+"&palavra_chave="+palavra_chave,
					null,
					"height=500,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	}
	function limpaAgendados(palavra_chave){
		setTimeout("location='<?= "controleExames.php?palavra_chave=$palavra_chave&id_login=$id_login#tabs-2";?>'", 0);
	}
</script>
<?php
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	
	$id_login = ($_GET[id_login] == "" ? $_POST[id_login] : $_GET[id_login]);
	$med_codigo = ($_GET[med_codigo] == "" ? $_POST[prestador_servico] : $_GET[med_codigo]);
	$palavra_chave = $_GET[palavra_chave];
	
	echo $common->incJquery();
	$arrayMenu = array("Exames Agendados", "Exames Requisitados");//, "Agendar Exames"); -- COMENTADO POIS VAI USAR A TELA DE AGENDAMENTO ANTIGA POR ENQUANTO
	echo $common->menuTab($arrayMenu);
	echo $common->bodyTab('1');
		echo $form->openForm($PHP_SELF."?id_login=$id_login", "POST", "filtrar");
			echo $table->openTable();
				$conteudo = array($form->inputText("palavra_chave", $palavra_chave, "Buscar", 45, null, null, null, null, "S"), "&nbsp;");
				$tamanhos = array(450, 100);
				echo $table->criaLinha($conteudo, $tamanhos, null);
				$sql = "SELECT med_codigo,
							   med_nome
						  FROM medico
						 WHERE prestador_servico = 'S'";
				$query = pg_query($sql);
				$arrayPrestador[0] = "MEUS PRESTADORES";
				while ($linha = pg_fetch_row($query)){
					$arrayPrestador[$linha[0]] = $linha[1];
				}
				$conteudo2 = array($form->inputSelect("prestador_servico", $arrayPrestador, "Filtrar Prestador de Servi&ccedil;o", null, null, null, $med_codigo, "style=width:235px;"), $common->commonButton("BUSCAR", null, "buscar.png", "onClick=\"document.filtrar.submit();\""));
				echo $table->criaLinha($conteudo2, null, null);
			echo $table->closeTable();
		echo $form->closeForm();
		if (($med_codigo == "") || ($med_codigo == 0)){
			$selMedicos = "SELECT med_codigo
							 FROM usuarios_medico
							WHERE usr_codigo = $id_login";
			$run = pg_query($selMedicos);
			while ($arrayMedicos = pg_fetch_array($run)){
				$medCodigos .= $arrayMedicos[med_codigo].", ";
			}
			$medCodigos = substr($medCodigos, 0, -2);
		}else{
			$medCodigos = $med_codigo;
		}
		//echo "<pre>".print_r($_POST,true)."</pre>";
		$palavra_chave = $_POST[palavra_chave]; 
		if($palavra_chave != ""){
			$busca = " AND (u.usu_prontuario = '$palavra_chave'
						   ".(is_numeric($palavra_chave) ? "OR agx.agex_codigo = $palavra_chave" : "")."
						OR u.usu_nome ilike '%$palavra_chave%'
						OR m.med_nome ilike '%$palavra_chave%'
						OR to_char(agx.agexl_data, 'DD/MM/YYYY') = '$palavra_chave'
						OR to_char(agx.agexl_data, 'YYYY-MM-DD') = '$palavra_chave')";
		}else{
			$busca = "";
		}
		//echo $busca."x";
		$select = "SELECT agx.agex_codigo,
						  u.usu_prontuario,
						  u.usu_nome,
						  CASE WHEN agx.agexl_status = 'A' THEN 'Agendado'
						  	   WHEN agx.agexl_status = 'C' THEN 'Cancelado' 
						 	   WHEN agx.agexl_status = 'R' THEN 'Recepcionado' END as status,
						  m.med_nome,
						  to_char(agx.agexl_data, 'DD/MM/YYYY') as data
					 FROM agendamento_exame as age, 
						  usuario as u, 
						  agendamento_exame_lista as agx,
						  medico m
					WHERE agx.agex_codigo = age.agex_codigo  
					  AND u.usu_codigo = age.usu_codigo  
					  AND agx.usu_codigo = age.usu_codigo
					  AND m.med_codigo = agx.med_codigo 
					  AND agx.agexl_data >= CURRENT_DATE
					  AND agx.med_codigo IN ($medCodigos)".
						  $busca
				  ."GROUP BY age.cod_controle,
						  m.med_nome,
						  u.usu_prontuario,
						  u.usu_nome,
						  agx.agexl_data,
						  agx.agexl_status,
						  agx.agex_codigo 
					ORDER BY agx.agexl_data, u.usu_nome";
		
		//echo $select;
		$executa = pg_query($select);
		echo $table->openTable("lista");
			$arrayTitulosTabela = array("C&oacute;digo", "Prontu&aacute;rio", "Nome", "Situa&ccedil;&atilde;o", "Local", "Data", "A&ccedil;&atilde;o");
			$arrayTamanhosTabela = array(50, 80, 400, 80, 200, 50, 550);
			$arrayColspanTabela = array(1, 1, 1, 1, 1, 1, 3);
			echo $table->criaLinha($arrayTitulosTabela, $arrayTamanhosTabela, $arrayColspanTabela, "S");
			$linha = 0;
			while ($resultado = pg_fetch_row($executa)){
				array_push($resultado, $common->commonButton("Detalhes", null, "calendar_list.png", "onClick=\"listaExames('$resultado[0]', '$resultado[2]');\""), $common->commonButton("Recepcionar", null, "recepcionar_calendar.png", "onClick='recepcionar($resultado[0], $linha)';"), $common->commonButton("Cancelar", null, "removeEvent.png", "onClick='cancelar($resultado[0], $linha)'"));
				//$arrayIdColuna = array("codigo".$resultado[0], "prontuario".$resultado[0], "nome".$resultado[0], "status".$resultado[0], "laboratorio".$resultado[0], "data".$resultado[0], "detalhes".$resultado[0], "recepcionar".$resultado[0], "cancelar".$resultado[0]);
				echo $table->criaLinha($resultado, null, null, "N", null, $linha);
				$linha++;
			}
		echo $table->closeTable();
		echo "<div id='listagem'></div>";
	echo $common->closeTab();
	echo $common->bodyTab('2');
		echo $form->openForm($PHP_SELF."#tabs-2", "GET", "busca");
			echo $table->openTable();
				echo $form->hiddenForm("id_login", $id_login);
				$conteudo = array($form->inputText("palavra_chave", $palavra_chave, "Buscar", 45, null, null, null, null, "S"), $common->commonButton("BUSCAR", null, "buscar.png", "onClick=\"document.busca.submit();\""));
				$tamanhos = array(450, 100);
				echo $table->criaLinha($conteudo, $tamanhos, null);
			echo $table->closeTable();
		echo $form->closeForm();
		if($palavra_chave != ""){
			$busca = "AND (".(is_numeric($palavra_chave) ? "re.req_codigo = $palavra_chave OR" : "")."
						   u.usu_nome ilike '%$palavra_chave%'
						OR p.proc_nome ilike '%$palavra_chave%'
						OR to_char(re.dt_requisicao, 'DD/MM/YYYY') = '$palavra_chave'
						OR to_char(re.dt_requisicao, 'YYYY-MM-DD') = '$palavra_chave')";
			$limit = "";
		}else{
			$busca = "";
			$limit = "LIMIT 15";
		}
		$sql = " SELECT req_codigo,
						usu_nome,
						p.proc_nome,
						to_char(dt_requisicao, 'DD/MM/YYYY') as data			
				   FROM requisicao_exames re
				   JOIN usuario u
				     ON u.usu_codigo = re.usu_codigo
				   JOIN procedimento p
				     ON p.proc_codigo = re.proc_codigo
				  WHERE NOT EXISTS (SELECT req_codigo 
								      FROM agendamento_exame_lista ael
								     WHERE ael.req_codigo = re.req_codigo)
					AND NOT EXISTS (SELECT req_codigo 
								      FROM liberacao_exame_lista lel
								     WHERE lel.req_codigo = re.req_codigo)
				  		$busca
				  ORDER BY dt_requisicao DESC 
				  		$limit";
		$exec = pg_query($sql);
		echo $form->openForm("#", "POST", "requisicoes");
			echo $table->openTable("lista");
				$arrayTitulosTabela = array("<img id='img' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/uncheckAll.png' onClick=\"marcaDesmarca();\">", "N&uacute;mero", "Paciente", "Exame Solicitado", "Data");
				$arrayTamanhosTabela = array(10, 50, 300, 300, 100);
				$arrayColspanTabela = array(1, 1, 1, 1, 1);
				echo $table->criaLinha($arrayTitulosTabela, $arrayTamanhosTabela, $arrayColspanTabela, "S");
				while ($resultado = pg_fetch_row($exec)){
					array_unshift($resultado, "<input type=checkbox name='exames[]' value='$resultado[0]'>");
					echo $table->criaLinha($resultado, null, null);
				}
			echo $table->closeTable();
		echo $form->closeForm();
		echo $table->openTable("table");
			$resultado2 = array("&nbsp;");
			$arrayTamanhosTabela2 = array(1000,100);
			array_push($resultado2, $common->commonButton("Agendar/Liberar", null, "calendar.png", "onClick=\"agenda_libera($id_login,'$palavra_chave');\""));
			//array_push($resultado2, $common->commonButton("Cancelar", "#", "removeEvent.png"));
			echo $table->criaLinha($resultado2, $arrayTamanhosTabela2, null);
		echo $table->closeTable();
	echo $common->closeTab();
	/*TERCEIRA ABA SERÁ IMPLEMENTADA NA PRÓXIMA VERSÃO*/
	/*echo $common->bodyTab('3');
		//include $_SESSION[root].$_SESSION[modulo]."exame/exa_agendamento.php";
	echo $common->closeTab();*/
?>