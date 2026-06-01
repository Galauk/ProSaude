<?php
	require_once '../global.php';
	setError(1);
	
	$sqlUnidade = "SELECT * 
					 FROM unidade
					 ORDER BY uni_desc";
	$query = pg_query($sqlUnidade);
	$i=0;
	$colNames = array("Procedimento","Total");
	
	$colModel = array("{name: 'proc_s', index: 'proc_s', width: 200, align: 'left', sorttype: 'int', frozen:true}");
	$colModel []= "{name: 'total_proc', index: 'total_proc', width: 45, align: 'center', sorttype: 'int', frozen:true, formatter: input2}";
	
	while ($row = pg_fetch_array($query)){
		$colNames []= $row['uni_desc'];
		$colModel []= "{name: 'quantidade_{$row['uni_codigo']}',index: 'quantidade_{$row['uni_codigo']}', width: 155, align: 'center', sorttype: 'int', formatter: input}"; //, editable: true, editoptions:{size:'2',maxlength:'4',style:'text-align:center'}
	}
	
	
	
	$colNames = "'".implode("','",$colNames)."'";
	$colModel = implode(",",$colModel);
	
	
	include("retornoProcedimentos.php");
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
		<link href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
		<link href="/WebSocialComum/library/css/ui.jqgrid.4.3.1.css" rel="stylesheet" type="text/css" />
		
<style>
	.ui-pg-input,
	.ui-pg-selbox{
		background-color:#eee;
	}
	body, table{
	  font-size: 0.9em;
	}
</style>
		<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
		<script src="http://api.jquery.com/scripts/events.js"></script>
		<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
		<script type="text/javascript" src="/WebSocialComum/library/js/i18n/grid.locale-pt-br.4.3.1.js"></script>
		<script type="text/javascript" src="/WebSocialComum/library/js/jquery.jqGrid.min.4.3.1.js"></script>
		<script type="text/javascript">

			$(function(){
				var _tmp;

	        	function isInt(x) {
	        	  var y=parseInt(x);
	        	  if (isNaN(y)) return false;
	        	  return x==y && x.toString()==y.toString();
	        	}

	        	var input = function(cellvalue,options,rowdata){
	                if( isInt(cellvalue) ){
						return "<input value=\""+cellvalue+"\" style=\"width:35px; text-align:center;\" class=\"linha\" data-proc=\""+options.rowId+"\" />";
	                }
	            }

	        	var input2 = function(cellvalue,options,rowdata){
	                if( isInt(cellvalue) ){
	                	return "<input value=\""+cellvalue+"\" style=\"width:30px; text-align:center;\" class=\"e\" id=\"total_"+options.rowId+"\" />";
						return "<span id=\"total_"+options.rowId+"\">"+cellvalue+"</span>";
	                }
	            }
	            var dados = <?=json_encode($responce->rows);?>;
				
				$("#grid").jqGrid({ 

					//url:"retornoProcedimentos.php",
					datatype: "local", 
					colNames:[<?=$colNames;?>], 
					colModel:[<?=$colModel;?>], 
	                shrinkToFit: false,
					width: 800,
					height: 300,
	                rownumbers: true,
					sortname: 'bpa_codigo', 
					sortorder: "desc", 
	                caption: 'Quantidade de procedimentos por unidade',
	                loadComplete: function () {
	                    clickToSelect();
	                    updateTotal();
	                }
				}); 

				$("#grid").jqGrid('setFrozenColumns');

		        function clickToSelect(){
		            $("input").click(function(){
						$(this).select();
		            });
		        }

		        function updateTotal(){
		            window.console && console.log("listen change");
					$(".linha").change(function(){
									
						var id = $(this).data("proc");
						var total = 0;

						$("[data-proc="+id+"]").each(function(){
							total += parseInt($(this).val());
						});
						window.console && console.log(id+"="+total);

						

						$("#grid")
						   .jqGrid('destroyFrozenColumns')
						   .jqGrid('setColProp','total_proc', {frozen:false});
						   
						$("#total_"+id).val(total);
						
						$("#grid").jqGrid('setColProp','total_proc', {frozen:true})
						.jqGrid('setFrozenColumns')
						.trigger('reloadGrid', [{current:true}]);
						
					});
		        }
//		        for(var i=0;i<=dados.length;i++) 
//			        $("#grid").jqGrid('addRowData',i+1,dados[i]);

			});
		
		</script>
	</head>
	<body>
	
		<table id="grid"></table>
		<div id="log"></div>
	</body>
</html>