<?php
	require_once '../global.php';
	setError(1);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
	<link href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
	<link href="/WebSocialComum/library/css/ui.jqgrid.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/i18n/grid.locale-pt-br.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery.jqGrid.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
	<?php $form = new classForm(); ?>
	<?php $table = new tableClass(); ?>
	<?php $common = new commonClass(); ?>
<style>
	.ui-pg-input,
	.ui-pg-selbox{
		background-color:#eee;
	}
</style>
<script>
	jQuery(function($){
		$("#tabs").tabs();
		
		$("#list12").jqGrid({ 

			url:'ajaxGridBpa.php?uni_codigo=<?=$_POST['uni_codigo'];?>&data_inicial=<?=$_POST[data_inicial]; ?>&data_final=<?=$_POST[data_final]; ?>', 
			datatype: "json", 
			colNames:['Cod. Proc','Procedimento', 'Paciente', 'CID','Inconsistências','Tipo'], 
			colModel:[ {name:'proc_codigo_sus',index:'proc_codigo_sus', width:65}, 
			   		   {name:'proc_nome',index:'proc_nome', width:250}, 
			   		   {name:'usu_nome',index:'usu_nome', width:200}, 
			   		   {name:'cd10_codigo_cid',index:'cd10_codigo_cid', width:30, align:"center"}, 
			   		   {name:'bpa_status_inconsistencia',index:'bpa_status_inconsistencia', width:80, align:"right",hidden:true}, 
			   		   {name:'bpa_tipo',index:'bpa_tipo', width:150, sortable:false}],
			rowNum:10, 
			rowList:[10,20,30], 
			pager: '#pager12', 
			sortname: 'bpa_codigo', 
			sortorder: "desc", 
			caption: "Procedimentos a exportar"
		}); 
		$("#list12").jqGrid('navGrid','#pager12',{add:false,edit:false,del:false,search:false});
	  })
</script>
</head>
<body>
	<?php
		echo $common->menuTab(array("BPA"));
			echo $common->bodyTab();
				echo $form->openForm("$PHP_SELF","POST","form");
					$sqlMedicoI = "SELECT DISTINCT uni.uni_codigo AS cod,
										  uni_desc AS nome
									 FROM bpa
									 JOIN unidade AS uni
									   ON uni.uni_codigo=bpa.uni_codigo
								   	ORDER BY uni_desc";
					
					$sqlMedicoE = "SELECT DISTINCT m.med_codigo AS cod,
									      med_nome AS nome
									 FROM medico m
									WHERE prestador_servico in ('L','H')
								 	ORDER BY med_nome";
					
					$query1 = pg_query($sqlMedicoI);
					$query2 = pg_query($sqlMedicoE);
					$selectMedico  = "<select id=\"uni_codigo\" name=\"uni_codigo\" class=\"inputForm\">";
					$selectMedico .= "<option value=\"0\">-- SELECIONE --</option>";
					$selectMedico .= "<optgroup label=\"Unidades\">";
					while($r = pg_fetch_assoc($query1)){
						$selectMedico .= "<option value=\"".$r['cod']."|1"."\">".$r['nome']."</option>";	
					}	
					$selectMedico .= "</optgroup>";
					$selectMedico .= "<optgroup label=\"Prestadores\">";
					while($r = pg_fetch_assoc($query2)){
						$selectMedico .= "<option value=\"".$r['cod']."|0"."\">".$r['nome']."</option>";	
					}	
					$selectMedico .= "</optgroup>";
					$selectMedico .= "</select>";
					echo $form->inputLabel("Unidade").$selectMedico;
					
					echo $form->inputText("data_inicial",null,"Data Inicial",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
					echo $form->inputText("data_final",null,"Data Final",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
					echo $form->inputText("mes_ref",null,"Mês de referência",null,7,"onKeypress=\"return Ajusta_Mes(this,event)\"");
					echo "<div style='clear:both; width:400px; border:solid 0px;'>";
						echo"<div style='float:right; width:205px;'>";
							echo "<input type=image src=/WebSocialComum/imgs/selecionar_on.jpg alt=Submit/>  ";
						echo "</div>";
					echo "</div>";
				echo $form->closeForm();
				
				if(isset($_POST['uni_codigo']) && isset($_POST['data_inicial']) && $_POST['uni_codigo'] && $_POST['data_inicial'] && $_POST['data_final'] && $_POST['mes_ref']){
					echo "<br /><br />";
					echo "<table id=list12></table> 
						  <div id=pager12></div>";
					echo "<br /><br />";
					echo $common->commonButton("Verificar Inconsist&ecirc;ncias","./bpa_inconsistencias.php?uni_codigo={$_POST['uni_codigo']}&data_inicial={$_POST['data_inicial']}&data_final={$_POST['data_final']}&mes_ref={$_POST['mes_ref']}","gerar.png",null);					
				}
		echo $common->closeTab();
	?>

</body>
</html>
