<?php
	require_once '../global.php';
	setError(1);
	
	list($ano,$mes) = explode("-",$_GET['mes_ref']);
 	 $data1 = $_GET['data_inicial'];
 	 $data2 = $_GET['data_final'];
	
	// seleciona os bpas que não estão validados, ou são inválidos.
	$sql = "SELECT *
	          FROM bpa
	         WHERE uni_codigo={$_GET['uni_codigo']}
	           AND bpa_data BETWEEN '$data1' AND '$data2'
	           AND bpa_ativo='t'
	           AND bpa_status_inconsistencia <> 'f'";
	
	//echo $sql;exit;
	$query = pg_query($sql);
	$invalidos = pg_num_rows($query);
	
	$r = pg_fetch_all($query);
	//fdebug($r);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
	<link href="/WebSocialComum/library/css/ui.jqgrid.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/i18n/grid.locale-pt-br.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery.jqGrid.min.js"></script>
	<?php $form = new classForm(); ?>
	<?php $table = new tableClass(); ?>
	<?php $common = new commonClass(); ?>
<style>
	.ui-pg-input,
	.ui-pg-selbox{
		background-color:#eee;
	}
	.a{cursor:pointer;}
</style>
<script>
	$(function(){
		$("#tabs").tabs();
		
		$("#grid").jqGrid({ 

			url:'ajaxGridBpa.php?uni_codigo=<?=$_GET['uni_codigo'];?>&data_inicial=<?=$_GET[data_inicial]; ?>&data_final=<?=$_GET[data_final]; ?>', 
			datatype: "json", 
			colNames:['Cod. Proc','Procedimento', 'Paciente', 'CID','Inconsistências','Tipo'], 
			colModel:[ {name:'proc_codigo_sus',index:'proc_codigo_sus', width:80}, 
			   		   {name:'proc_nome',index:'proc_nome', width:250}, 
			   		   {name:'usu_nome',index:'usu_nome', width:200}, 
			   		   {name:'cd10_codigo_cid',index:'cd10_codigo_cid', width:30, align:"center"}, 
			   		   {name:'bpa_status_inconsistencia',index:'bpa_status_inconsistencia',hidden:true}, 
			   		   {name:'bpa_tipo', index:'bpa_tipo', width:100, align:"center", sortable:false}],
			rowNum:20, 
			rowList:[10,20,30,40,50,60,70,80,90,100], 
			height: 400,
			pager: '#gridPager', 
			sortname: 'bpa_status_inconsistencia', 
			sortorder: "ASC", 
			subGrid : true, 
			subGridUrl: 'bpa_inconsistencias.ajax.php',
			subGridModel : [ 
			                {
			                name  : ['Inconsist&ecirc;ncias encontradas'],
			                index:  ['bpai_descricao'],
			                width : [600],
			                align : ['left']
			                }
			             ],
			caption: "Procedimentos a exportar",
	        afterInsertRow: function(rowid, aData){
		        if(aData.bpa_status_inconsistencia == 't')
	            	$("#grid").jqGrid('setRowData',rowid,aData,{color:'red'});
	        },
	        gridComplete: function(){/*
	            var ids = $("#grid").jqGrid('getDataIDs');
	            for(var i=0;i < ids.length;i++){
	                var id = ids[i];
	                editar = "<span class=\"ui-icon ui-icon-pencil a\" style=\"float:left\" title=\"Editar\" onclick=\"window.location.href='editar_bpa.php?bpa_codigo="+id+"';\"></span>";
	                $("#grid").jqGrid('setRowData',ids[i],{
	                    opcoes:editar
	                });
	            }*/
	        }
		}); 
		$("#grid").jqGrid('navGrid','#gridPager',{add:false,edit:false,del:false,search:false});
	  });
</script>
</head>
<body>
	<?php
		echo $common->menuTab(array("Inconsist&ecirc;ncias do BPA"));
			echo $common->bodyTab();
	?>
	
	<table id="grid"></table> 
	<div id="gridPager"></div>
	
	<?php 
	
	if($invalidos){
		echo utf8_encode("<br />Atenção: há itens do BPA com erros!");
					echo $common->commonButton("Verificar Inconsist&ecirc;ncias","./bpa_inconsistencias.php?uni_codigo={$_GET['uni_codigo']}&data_inicial={$_GET['data_inicial']}&data_final={$_GET['data_final']}&mes_ref={$_GET['mes_ref']}","gerar.png",null);
	}
	echo $common->commonButton("Gerar BPA","./exportaBPA.php?uni_codigo={$_GET['uni_codigo']}&data_inicial={$_GET['data_inicial']}&data_final={$_GET['data_final']}&mes_ref={$_GET['mes_ref']}","gerar.png",null);
	echo $common->closeTab(); 
	
	?>
</body>
</html>
