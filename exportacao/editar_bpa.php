<?php
	require_once '../global.php';
	require_once SOCIAL . '/__array.php';
	require_once COMUM . '/library/php/funcoes.db.php';
	setError(1);
	
	if(isset($_POST['bpa_codigo'])){
		
		$sql = "UPDATE bpa
				   SET bpa_ativo='f'
				 WHERE bpa_codigo={$_POST['bpa_codigo']};";
		pg_query($sql);
		
		$bpa_codigo = nextVal('bpa_bpa_codigo_seq');
		
		$sql = "INSERT INTO bpa(
				            bpa_codigo, uni_codigo, usr_codigo, usu_codigo, bpa_data, proc_codigo, 
				            ci_codigo, bpa_autorizacao, bpa_tipo, bpa_cd10_codigo, bpa_origem, 
				            bpa_origem_codigo,usr_codigo_alt)
				    VALUES ('%s', '%s', '%s', '%s', '%s', '%s', 
				            '%s', %s, '%s', %s, '%s', 
				            '%s', '%s');";
		
		$num_autorizacao = empty($_POST['bpa_autorizacao'])?"NULL":$_POST['bpa_autorizacao'];
		$cid = empty($_POST['bpa_cd10_cid'])?"NULL":$_POST['bpa_cd10_cid'];
		
		$sql = sprintf($sql, 
		               $bpa_codigo, $_POST['uni_codigo'],$_POST['usr_codigo'],$_POST['usu_codigo'],$_POST['bpa_data'],$_POST['proc_codigo'],
		               $_POST['ci_codigo'],$num_autorizacao,$_POST['bpa_tipo'],$cid, "bpa",
		               $_POST['bpa_codigo'], $_SESSION['id_login']); 
		$query = pg_query($sql) or die($sql);
		
		// valida o BPA
		include 'bpa_inconsistencias_unico.php';
		
		if(!$erros){
			list(,$mes,$ano) = explode("/",$_POST['bpa_data']);
			header("location: bpa_inconsistencias.php?uni_codigo={$_POST['uni_codigo']}&mes_ref={$ano}-{$mes}");
		} else {
			header("location: editar_bpa.php?bpa_codigo=$bpa_codigo");
		}
		exit;
		
	}
	
	$bpa_codigo = $_GET['bpa_codigo'];
	
	$sql = "SELECT bpa.*,
				   TO_CHAR(bpa_data,'DD/MM/YYYY') AS bpa_data,
	               usu_nome,
	               usr_nome,
	               proc_nome
	          FROM bpa
	          JOIN usuario AS usu
	            ON usu.usu_codigo=bpa.usu_codigo
	          JOIN usuarios AS usr
	            ON usr.usr_codigo=bpa.usr_codigo
	          JOIN procedimento AS proc
	            ON proc.proc_codigo=bpa.proc_codigo
	         WHERE bpa_codigo=$bpa_codigo;";
	
	$query = pg_query($sql);
	$r = pg_fetch_array($query);
	
	$sql = "   SELECT i.bpai_codigo,
 	 				   bpai_descricao
				  FROM bpa_inconsistencias AS i
				  JOIN rl_bpa_inconsistencia AS rl
				    ON rl.bpai_codigo=i.bpai_codigo
				 WHERE rl.bpa_codigo=$bpa_codigo";
	$query = pg_query($sql);
	
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
	<link href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
	<link href="/WebSocialComum/library/css/ui.jqgrid.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
	<?php $form = new classForm(); ?>
	<?php $table = new tableClass(); ?>
	<?php $common = new commonClass(); ?>
	<script>
		$(function(){
			$("#tabs").tabs();

			$("#usu_nome").buscar();

			$("#usr_nome").buscar({
				tipo: 'usuarios',
				template : function(ul, item) {
					return $("<li></li>").data("item.autocomplete", item).append(
							"<a>" + item.label + "</a>").appendTo(ul);
				}
			});

			$("#proc_nome").buscar({
				tipo: 'procedimento',
				template : function(ul, item) {
					return $("<li></li>").data("item.autocomplete", item).append(
							"<a>" + item.label + "</a>").appendTo(ul);
				},
				callback: function(event, ui){
					$("#bpa_cd10_codigo")
					.attr("disabled","disabled")
					.html("<option value=\"0\">Carregando...</option>")
					.load("../selectCidPorProcedimento.ajax.php?procedimento=" + ui.item.id, function(){
						$("#bpa_cd10_codigo").removeAttr("disabled");
					});
				}
			});
			
		});
	</script>
</head>
<body>
<?php 
	
	echo $common->menuTab(array("Detalhe do BPA")); 
	echo $common->bodyTab(); 
	
	echo "<ul class=\"ui-state-error\">";
	while($linha = pg_fetch_array($query)){
		echo "<li>".$linha['bpai_descricao']."</li>";
	}
	echo "</ul><br />";
	
	echo $form->openForm("","POST","form");
	echo $form->hiddenForm("bpa_codigo", $bpa_codigo);
	
	// unidade
	$unidadesSql = "SELECT uni_codigo,
					       uni_desc
					  FROM unidade
					 ORDER BY uni_desc;";
	
	echo $form->inputSelect("uni_codigo",NULL,"Unidade",$unidadesSql,NULL,NULL,$r['uni_codigo']);
	
	// usuario
	echo $form->hiddenForm("usu_codigo", $r['usu_codigo']);
	echo $form->inputText("usu_nome", $r['usu_nome'],"Paciente", 70);
	
	// usuarios
	echo $form->hiddenForm("usr_codigo", $r['usr_codigo']);
	echo $form->inputText("usr_nome", $r['usr_nome'],"Médico", 70);
	
	// data
	echo $form->inputText("bpa_data", $r['bpa_data'],"Data",10);
	
	// procedimento
	echo $form->hiddenForm("proc_codigo", $r['proc_codigo']);
	echo $form->inputText("proc_nome", trim($r['proc_nome']),"Procedimento", 70);
	
	// cid10
	$cidSql = "  SELECT c.cd10_codigo,c.cd10_codigo_cid || ' - ' || c.cd10_descricao
				   FROM cid10 AS c
				   JOIN rl_procedimento_cid AS rl
				     ON rl.co_cid=c.cd10_codigo_cid
				   JOIN procedimento AS p
				     ON p.proc_codigo_sus=rl.co_procedimento
				    AND p.proc_codigo={$r['proc_codigo']}
				  ORDER BY c.cd10_descricao";
	
	$tipoOptions = array(
		"nome" => "bpa_cd10_codigo",
		"caption" => "CID",
		"sel" => $r['bpa_cd10_codigo'],
		"sql" => $cidSql,
		"disabledFirst" => "S"
	);
	echo $form->inputSelect($tipoOptions);
	
	// carater de atendimento (ci)
	$ciSql = "SELECT ci_codigo,
					       ci_descricao
					  FROM ci
					 WHERE ci_ativo='S'
					 ORDER BY ci_descricao;";
	
	echo $form->inputSelect("ci_codigo",NULL,"Carater de Atend.",$ciSql,NULL,NULL,$r['ci_codigo']);
	
	// número de autorização
	echo $form->inputText("bpa_autorizacao", $r['bpa_autorizacao'],"Núm. Autorização", 70);
	
	// tipo
	$tipoOptions = array(
		"nome" => "bpa_tipo",
		"valor" => $arrayTipoBPA,
		"caption" => "Tipo",
		"sel" => $r['bpa_tipo'],
		"disabledFirst" => "S"
	);
	echo $form->inputSelect($tipoOptions);
	echo "<br />";
	echo $common->commonButton("Salvar","","salvar.gif","onclick=\"document.form.submit();");
	
	echo $form->closeForm();	
	
	echo $common->closeTab(); 
	
?>
</body>
</html>
