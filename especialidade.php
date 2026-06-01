<?php
	
	require_once 'global.php';
	
	// na tela inicial, mostra as especialidade que tem algum médico relacionado
	// na opção de busca, lista todas que 'casarem' com a busca
	$busca = $_GET['busca'];
	if($busca){
		$sql = "SELECT * 
		          FROM especialidade
		         WHERE retira_acentos(esp_nome) ilike retira_acentos('%$busca%')
		         ORDER BY esp_nome;";
		
	} else {
		$sql = "SELECT DISTINCT(e.esp_codigo),
		  			   e.* FROM especialidade AS e
				  JOIN medico_especialidade AS m
				    ON m.esp_codigo=e.esp_codigo
				 ORDER BY esp_nome";
	}
	
	$query = pg_query($sql);
	
	$form = new classForm();
	
?><html>
<head>
	<?php $commum = new commonClass(); ?>
	<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
	<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
	<link rel="stylesheet" href="/WebSocialSaude/estiloPE.css" rel="stylesheet">
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript">
		$(function(){
			$("#busca").select();
			$("#tabs").tabs();
		});

		// pre-consulta
		function pc(to,id){
			$("#pc"+id)
				.attr("src","<?=LINKCOMUM?>/imgs/loading.gif")
				.attr("alt","Carregando...")
				.removeAttr("onclick");
			$.ajax({
				url: "especialidade.ajax.php",
				type:"POST",
				data: {
					esp_codigo: id,
					tipo: 'pc',
					to: to
				},
				success:function(r){
					if(r == 1){
						to = 0;
						img = "selecionar";
						alt = "Sim";
					} else {
						to = 1;
						img = "excluir";
						alt = "Não";
					}
					$("#pc"+id)
						.attr("src","<?=LINKCOMUM?>/imgsBotoes/"+img+".png")
						.attr("alt",alt)
						.attr("onclick","pc("+to+","+id+")");	
				}
			});
		}

		// encaminhamento
		function enc(to,id){
			$("#enc"+id)
				.attr("src","<?=LINKCOMUM?>/imgs/loading.gif")
				.attr("alt","Carregando...")
				.removeAttr("onclick");
			$.ajax({
				url: "especialidade.ajax.php",
				type:"POST",
				data: {
					esp_codigo: id,
					tipo: 'enc',
					to: to
				},
				success:function(r){
					if(r == 1){
						to = 0;
						img = "selecionar";
						alt = "Sim";
					} else {
						to = 1;
						img = "excluir";
						alt = "Não";
					}
					$("#enc"+id)
						.attr("src","<?=LINKCOMUM?>/imgsBotoes/"+img+".png")
						.attr("alt",alt)
						.attr("onclick","enc("+to+","+id+")");	
				}
			});
		}
		// agendamento
		function age(to,id){
			$("#age"+id)
				.attr("src","<?=LINKCOMUM?>/imgs/loading.gif")
				.attr("alt","Carregando...")
				.removeAttr("onclick");
			$.ajax({
				url: "especialidade.ajax.php",
				type:"POST",
				data: {
					esp_codigo: id,
					tipo: 'age',
					to: to
				},
				success:function(r){
					if(r == 1){
						to = 0;
						img = "selecionar";
						alt = "Sim";
					} else {
						to = 1;
						img = "excluir";
						alt = "Não";
					}
					$("#age"+id)
						.attr("src","<?=LINKCOMUM?>/imgsBotoes/"+img+".png")
						.attr("alt",alt)
						.attr("onclick","age("+to+","+id+")");	
				}
			});
		}
	</script>
</head>
<body style="margin:5px;">
<img src="<?=LINKCOMUM;?>/imgs/loading.gif" alt="" title="" style="display:none"/>
<?php echo $commum->menuTab(array("Especialidade"));?>
<?php echo $commum->bodyTab('1');?>
<form method="GET" action="" name="form">
<?php echo $form->inputText("busca", $busca, "Buscar").$commum->commonButton("Buscar", NULL, "buscar.png", "onclick=\"document.form.submit();\"")?>
</form>
<?php if(pg_num_rows($query)): ?>
	<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
		<tr class="ui-widget-header">
			<th width="10%">Código</th>
			<th>Especialidade</th>
			<th width="10%">Pré-Consulta</th>
			<th width="10%">Encaminhamento</th>
			<th width="10%">Mais de um agendamento</th>
		</tr>
		<?php while($r = pg_fetch_assoc($query)):?>
		<tr>
			<td class="ui-widget ui-widget-content"><?=$r['esp_codigo'];?></td>
			<td class="ui-widget ui-widget-content"><?=preg_replace("/$busca/i", "<u>$0</u>", $r['esp_nome']);?></td>
			<td class="ui-widget ui-widget-content c pc"><?
			
			if($r['esp_pre_consulta'] == 't'){
				echo "<img src=\"".LINKCOMUM."/imgsBotoes/selecionar.png\" id=\"pc".$r['esp_codigo']."\" onclick=\"pc(0,".$r['esp_codigo'].");\" alt=\"Sim\" title=\"Clique para alterar\" style=\"width:16px;cursor:pointer\">";
			} else {
				echo "<img src=\"".LINKCOMUM."/imgsBotoes/excluir.png\" id=\"pc".$r['esp_codigo']."\" onclick=\"pc(1,".$r['esp_codigo'].");\" alt=\"Não\" title=\"Clique para alterar\" style=\"width:16px;cursor:pointer\">";
			}
			
			?></td>
			<td class="ui-widget ui-widget-content c enc"><?
			
			if($r['esp_encaminhamento'] == 't'){
				echo "<img src=\"".LINKCOMUM."/imgsBotoes/selecionar.png\" id=\"enc".$r['esp_codigo']."\" onclick=\"enc(0,".$r['esp_codigo'].");\" alt=\"Sim\" title=\"Clique para alterar\" style=\"width:16px;cursor:pointer\">";
			} else {
				echo "<img src=\"".LINKCOMUM."/imgsBotoes/excluir.png\" id=\"enc".$r['esp_codigo']."\" onclick=\"enc(1,".$r['esp_codigo'].");\" alt=\"Não\" title=\"Clique para alterar\" style=\"width:16px;cursor:pointer\">";
			}
			
			?></td>
			<td class="ui-widget ui-widget-content c enc"><?
			
			if($r['esp_mais_agendamento'] == 't'){
				echo "<img src=\"".LINKCOMUM."/imgsBotoes/selecionar.png\" id=\"age".$r['esp_codigo']."\" onclick=\"age(0,".$r['esp_codigo'].");\" alt=\"Sim\" title=\"Clique para alterar\" style=\"width:16px;cursor:pointer\">";
			} else {
				echo "<img src=\"".LINKCOMUM."/imgsBotoes/excluir.png\" id=\"age".$r['esp_codigo']."\" onclick=\"age(1,".$r['esp_codigo'].");\" alt=\"Não\" title=\"Clique para alterar\" style=\"width:16px;cursor:pointer\">";
			}
			
			?></td>
		</tr>
		<?php endwhile; ?>
	</table>
<?php else: ?>
<em>Nenhuma especilidade encontrada.</em>
<?php endif; ?>
<?php echo $commum->closeTab();?>
</body>
</html>