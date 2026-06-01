
<?php
	include_once "global.php";
	//$db = pg_connect("host=189.75.189.51 dbname=historico user=postgres port=5432 password=gvw60!@.5A");
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->menuTab(array("Historico"));
		echo $common->bodyTab();
?>

<link rel="stylesheet" href="lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="../WebSocialComum/library/js/jquery.shortcuts.min.js"></script>
<script type="text/javascript" src="../WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript" src="../WebSocialComum/library/js/ajax_motor.js"></script>
<script>

$(function(){
	$("#tabs").tabs();
	
	$("#buscar").buscar({
		tipo:'cd10',
		callback: function(event,ui){
			$("#parte2").show("");
			
		}
	});
});

	function chamaVinculacoes(gd_descricao,gd_codigo,acao,codigo){
		if(acao == ""){
			url = "<?=$_SESSION[linkroot].$_SESSION[modulo]?>gruposComCid.php?gd_descricao="+gd_descricao+"&gd_codigo="+gd_codigo;
		}else{
			url = "<?=$_SESSION[linkroot].$_SESSION[modulo]?>gruposComCid.php?gd_descricao="+gd_descricao+"&gd_codigo="+gd_codigo+"&cd10_codigo="+codigo+"&acao="+acao;
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
			//setTimeout("location='vincular_grupos_doencas.php'", 0);
		}
	}
</script>
	
			<input type="hidden" id="pro_codigo" />
			<input type="hidden" id="pro_nome" />
			<form method="POST" action="vincular_grupos_doencas.php" name="grupos_cid">
				<input type="hidden" name="cd10_codigo" id="cd10_codigo" />
				<input type="hidden" name="acao" value="gerar">
 				
				<div id="bloco">
					<div id="pac-dados">
						<?=$form->inputText('buscar', $valor,'Buscar','60');?>
						<?//$form->inputText('usu_nome', $valor,'Nome do Paciente','60',NULL,null,NULL,"S");?>
						<?=$form->inputText("cd10_descricao", $valor,"CID", 60, NULL, "onChange=\"return buscaDispensados(this.value)\"");?>
						<?php $sqlGrupos = "SELECT * FROM grupo_doencas";?>
						<?=$form->inputSelect("gd_codigo",null,"Grupo de Doen&ccedil;as",$sqlGrupos); ?>
						<?php
							echo "<div style='clear:both; width:400px; border:solid 0px;'>";
								echo"<div style='float:right; width:205px;'>";		
									echo $common->commonButton("voltar", "vincular_grupos_doencas.php", "voltar.png");
								echo"</div>";
								echo"<div style='float:right'>";
								echo $common->commonButton("Salvar",null,"salvar.gif","onClick=\"document.grupos_cid.submit()\"");
								echo"</div>";
							echo"</div>";
						?>
					</div>	
				</div>
				<div class="clear"></div>
			</form>
<?php
		
		echo $common->divisoria("VINCULOS");
		$sqlLista = "SELECT * FROM grupo_doencas LIMIT 15";
		$queryLista = pg_query($sqlLista);
		echo $table->openTable("lista");
			echo $table->criaLinha(array("Grupos"),null,null,"S");
			while($regLista = pg_fetch_array($queryLista)){
				echo $table->criaLinha(array($regLista["gd_descricao"]),null,null,"N","onClick=\"chamaVinculacoes('$regLista[gd_descricao]','$regLista[gd_codigo]')\"");

			}
		echo $table->closeTable();
		echo "<div id='hide'>";
		echo "</div>";
		$acao = $_POST["acao"];
		if($acao == "gerar"){
			$sqlValidacao = pg_query("SELECT * FROM grupos_cid WHERE cd10_codigo = $cd10_codigo");
			$numValidacao = pg_num_rows($sqlValidacao);
			if($numValidacao > 0){
				echo $common->modalMsg("ERRO","O cid informado já existe nesse grupo","vincular_grupos_doencas.php");
			}else{
				$sql = "INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ($gd_codigo,$cd10_codigo)";
				if(pg_query($sql)){
					echo $common->modalMsg("OK","Salvo com Sucesso","vincular_grupos_doencas.php");
				}else{
					echo $common->modalMsg("ERRO","Erro ao salvar","vincular_grupos_doencas.php",$sql);
				}
			}
		}
		echo $common->closeTab();
		//echo "<pre>".print_r($_POST,1);
?>
	
