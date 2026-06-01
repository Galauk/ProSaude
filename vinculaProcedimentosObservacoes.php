<?php
	session_start();	
	echo "<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/ajax_motor.js'></script>";
?>
<script>
	function validaVinculacao(){
		var obs_exa_codigo = document.getElementById("obs_exa_codigo").value;
		var proc_codigo = document.getElementById("proc_codigo").value;
		if(obs_exa_codigo == ""){
			alert("Preencha o campo observações !");
			return false;
		}else if(proc_codigo == ""){
			alert("Preencha o campo procedimentos !");
			return false;
		}else{
			document.vinculacao.submit();
		}
		
	}

	function chamaVinculacoes(proc_ori_codigo,proc_nome,acao,proc_codigo){
		if(acao == ""){
			url = "<?=$_SESSION[linkroot].$_SESSION[modulo]?>procedimentosComObservacoes.php?proc_ori_codigo="+proc_ori_codigo+"&proc_nome="+proc_nome+"&proc_codigo="+proc_codigo;
		}else if(acao == "acao"){
			url = "<?=$_SESSION[linkroot].$_SESSION[modulo]?>procedimentosComObservacoes.php?proc_ori_codigo="+proc_ori_codigo+"&proc_nome="+proc_nome+"&acao="+acao+"&proc_codigo="+proc_codigo;
		}
		ajax_tudo(url,retornaVinculacao);
	}

	function retornaVinculacao(txt){
		resp = txt.split('|');
		resposta = resp[0];
		qtde = resp[1];
		div = document.getElementById('hide');
		div.style.display = "block";
		div.innerHTML = resposta;
		if (qtde == 0){
			setTimeout("location='vinculaProcedimentosObservacoes.php'", 0);
		}
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
	echo $common->menuTab(array("Vincular observa&ccedil;&otilde;es"));
	echo $common->bodyTab("1");
	if($acao == ""){
		
		// verifica se há filtro (buscar)
		if( !empty($_POST['palavra'])){
			$palavra = strtoupper( $_POST['palavra'] );
			$filtro = "WHERE p.proc_nome ilike '%$palavra%' OR oe.obs_exa_observacoes ilike '%$palavra%'";
		} else {
			$filtro = "";// Motivo: Register_Globals on
		}
		
		
		echo $form->openForm("$PHP_SELF","POST","busca");
			echo $form->hiddenForm("buscar","buscar");
				echo $table->openTable();
					echo $table->criaLinha(array($common->commonButton("adicionar","$PHP_SELF?acao=form&busca_usuario=$busca_usuario","adicionar.png"),$common->commonButton("buscar",null,"buscar.png","onclick='document.busca.submit()'"),$form->inputText("palavra","$palavra",null,null,null,null,null,null,"S")),array(130));
				echo $table->closeTable();
			echo $form->closeForm();
		echo $table->openTable(lista);
		
			$sqlLinhas = " SELECT distinct p.proc_codigo,
						          p.proc_nome
						     FROM procedimento as p
						     JOIN procedimento_observacoes as po
						       ON po.proc_codigo=p.proc_codigo
						     JOIN observacoes_exames as oe
						       ON oe.obs_exa_codigo=po.obs_exa_codigo
						    $filtro";
			
			$queryLinhas = pg_query($sqlLinhas);
				echo $table->criaLinha(array("C&oacute;digo","Procedimento"),null,null,"S");
			while($linhas = pg_fetch_array($queryLinhas)){
				echo $table->criaLinha(array($linhas[proc_codigo],$linhas[proc_nome]),null,null,"N","onClick=\"chamaVinculacoes('$linhas[proc_ori_codigo]','$linhas[proc_nome]','','$linhas[proc_codigo]')\"");
			}
		echo $table->closeTable();
		echo "<div id=hide style=\"clear:both; display:none;\"></div>";
	}
	if($acao == "form"){
		echo $form->openForm("$PHP_SELF","POST","vinculacao");
			echo $form->hiddenForm("acao","add");
			$sqlProcedimentos =  "SELECT * FROM procedimento ORDER BY proc_nome";
			#$sqlProcedimentos =  "SELECT * FROM procedimento ORDER BY proc_NOME limit 2895";
			#$sqlProcedimentos =  "SELECT * FROM procedimento where proc_codigo <> 5963 ORDER BY proc_NOME ";
			#$sqlProcedimentos =  "SELECT * FROM procedimento where proc_codigo = 5963 ORDER BY proc_NOME ";
			echo $form->inputSelect("proc_codigo",null,"Procedimento",$sqlProcedimentos,null,null,null,"style=\"width:500px\"",null,"style=\"width:500px\"");
			$sqlOrientacoes = "SELECT *
			                     FROM observacoes_exames";
			echo $form->inputSelect("obs_exa_codigo", null,"Observa&ccedil;&otilde;es",$sqlOrientacoes);
			echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";		
						echo $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"validaVinculacao();\"");
					echo"</div>";
					echo"<div style='float:right'>";
						echo $common->commonButton("voltar","$PHP_SELF?busca_usuario=$busca_usuario","voltar.png");
					echo"</div>";
				echo"</div>";
		echo $form->closeForm();
	}
	if($acao == "add"){
		$proc_codigo = $_POST[proc_codigo];
		$obs_exa_codigo = $_POST[obs_exa_codigo];
		$sqlValida = "SELECT *
						FROM procedimento_observacoes
					   WHERE proc_codigo = $proc_codigo
					     AND obs_exa_codigo = $obs_exa_codigo";
		
		$query = pg_query($sqlValida);
		$numRows = pg_num_rows($query);
		if($numRows > 0){
			echo $common->modalMsg("OK","Ja contem um registro com essas informações: \n\$proc_codigo: $proc_codigo,\n\$obs_exa_codigo: $obs_exa_codigo",$PHP_SELF);
		}
		else {
			$stmt = "INSERT 
					   INTO procedimento_observacoes(proc_codigo,
					   								 obs_exa_codigo)
					   						  VALUES($proc_codigo,
					   						  		 $obs_exa_codigo)";
			
			if($query = pg_query($stmt)){
				echo $common->modalMsg("OK","Inserido com sucesso!",$PHP_SELF);
			}else{
				echo $common->modalMsg("ERRO","Falha ao inserir!",$PHP_SELF,$stmt.pg_last_error());
			}
		}
	}
	echo $common->closeTab();
	
	