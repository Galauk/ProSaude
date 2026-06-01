<script>
	function deletaGrupo(){
		gruex_codigo = document.getElementById('gruex_codigo').value;
		if (gruex_codigo == 0){
			alert("Selecione o grupo a ser excluído!");
		}else{
			location.href = "grupoExames.php?acao=delGrupo&gruex_codigo="+gruex_codigo;
		}
	}
</script>
<?
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	
	echo $common->incJquery();
	
	$proc_codigo = $_GET['proc_codigo'];
	$gruex_codigo = $_GET['gruex_codigo'];
	$acao = $_GET['acao'];
	
	if ($acao == "addGrupo"){
		echo $common->openModal("Inserir um novo grupo de exames", 800, "OK", null, "document.addGrupo.submit();");
			echo $form->openForm("$PHP_SELF", "GET", "addGrupo");
				echo $form->hiddenForm("acao", "inserirGrupo");
				echo $form->inputText("gruex_descricao", null, "Nome");
			echo $form->closeForm();
		echo $common->closeModal();
		echo "<script>
			elmt = document.getElementById('gruex_descricao');
			elmt.focus();
		</script>";
	}else if ($acao == "inserirGrupo"){
		$gruex_descricao = $_GET['gruex_descricao'];
		$insert = "INSERT 
					 INTO grupoexame 
					 	  (gruex_descricao) 
				   VALUES ('$gruex_descricao')";
		
		$exec = pg_query($insert);
		if (!$exec){
			echo $common->modalMsg("ERRO", "Houve um problema ao salvar o grupo $gruex_descricao, tente novamente.", "grupoExames.php", $insert);
			exit;
		}
		echo "<script>location.href = \"grupoExames.php\";</script>";
	}else if ($acao == "adicionar"){
		$insert = "INSERT INTO grupoexame_procedimento(proc_codigo, 
													   gruex_codigo)
    										   VALUES ($proc_codigo, 
    												   $gruex_codigo)";
		$exec = pg_query($insert);
		if (!$exec){
			echo $common->modalMsg("ERRO", "Houve um problema ao vincular o exame ao grupo, tente novamente.", "grupoExames.php", $insert);
			exit;
		}
		echo "<script>location.href = \"grupoExames.php\";</script>";
	}else if($acao == "desvincular"){
		$proc_codigo = $_GET['proc_codigo'];
		$gruex_codigo = $_GET['gruex_codigo'];
		$delete = "DELETE
					 FROM grupoexame_procedimento
					WHERE proc_codigo = $proc_codigo
					  AND gruex_codigo = $gruex_codigo";
		$exec = pg_query($delete);
		if (!$exec){
			echo $common->modalMsg("ERRO", "N&atilde;o foi poss&iacute;vel desvincular, tente novamente.", "grupoExames.php", $delete);
			exit;
		}
		echo "<script>location.href = \"grupoExames.php\";</script>";
	}else if ($acao == "delGrupo"){
		$gruex_codigo = $_GET[gruex_codigo];
		$delete = "DELETE
					 FROM grupoexame
					WHERE gruex_codigo = $gruex_codigo";
		$exec = pg_query($delete);
		if (!$exec){
			echo $common->modalMsg("ERRO", "N&atilde;o foi poss&iacute;vel excluir pois existem procedimentos vinculados a este grupo, desvincule-os e tente novamente.", "grupoExames.php", $delete);
			exit;
		}
		echo "<script>location.href = \"grupoExames.php\";</script>";
	}
	echo $common->menuTab(array("Grupos de Exames"));
	echo $common->bodyTab();
		echo $form->openForm("$PHP_SELF", "GET", "addgru");
			echo $form->hiddenForm("acao", "adicionar");
			$select1 = "SELECT gruex_codigo,
							   gruex_descricao
						  FROM grupoexame";
			echo $form->inputSelect("gruex_codigo", null, "Grupo", $select1, null, null, null, "style=width:250px;","SELECIONE", "style=width:250px;", "N", "S")."<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/adicionar.png style=\"cursor:pointer;margin-left:115px;padding-top:2px;\" onClick=\"location.href = 'grupoExames.php?acao=addGrupo'\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/delete2.png style=\"cursor:pointer;padding-left:5px;padding-top:2px;\" onClick=\"deletaGrupo();\">";
			$select = "SELECT proc_codigo,
							  proc_nome
						 FROM procedimento
						ORDER BY proc_nome ASC
						";
			echo $form->inputSelect("proc_codigo", null, "Exame", $select, null, null, null, "style=width:400px;","SELECIONE", "style=width:380px;", "N", "S");
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
				echo"<div style='float:right; width:205px;'>";		
					echo $common->commonButton("Adicionar", null, "adicionar.png", "onclick=\"document.addgru.submit();\"");
				echo"</div>";
				echo"<div style='float:right'>";
					echo $common->commonButton("voltar",null,"voltar.png", "onclick=\"alert('link de voltar');\"");
				echo"</div>";
			echo"</div>";
		echo $form->closeForm();
		echo $common->divisoria("Listagem dos grupos");
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Grupo", "Exame", "A&ccedil;&atilde;o"), null, null, "S");
			$sql = "SELECT g.gruex_descricao gdesc,
						   g.gruex_codigo,
						   p.proc_nome pnome,
						   p.proc_codigo
					  FROM grupoexame g
					  JOIN grupoexame_procedimento gp
					    ON g.gruex_codigo = gp.gruex_codigo
					  JOIN procedimento p
					    ON p.proc_codigo = gp.proc_codigo
					 ORDER BY gdesc, pnome";
			$result = pg_query($sql);
			while ($dados = pg_fetch_array($result)){
				$linha = array();
				array_push($linha, $dados[gdesc]); 
				array_push($linha, $dados[pnome]); 
				array_push($linha, $common->commonButton("Desvincular", "$PHP_SELF?acao=desvincular&proc_codigo=$dados[proc_codigo]&gruex_codigo=$dados[gruex_codigo]", "desvincular.png"));
				echo $table->criaLinha($linha);
			}
		echo $table->closeTable();
	echo $common->closeTab();
?>