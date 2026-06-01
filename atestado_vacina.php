<?php 
	include "global.php";
	
	// buscar próximo aprazamento, se houver
	$usu_codigo = $_GET['usu_codigo'];
	$sql = "SELECT MIN(vac_data) AS dataMax FROM vacina_usuario WHERE usu_codigo=$usu_codigo AND vac_acao='Z'";
	$query = pg_query($sql);
	
	if(pg_num_rows($query)){
		$row = pg_fetch_array($query);
		$dataMax = $row['datamax'];	
	}
?>
<html>
	<head>
		<title>Selecione a data</title>
		<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
		<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
		<style type="text/css">
			.ui-datepicker {font-size:12px;}
		</style>
		<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
		<script>
		$(function(){
			$.datepicker.regional['pt-BR'] = {
				closeText: 'Fechar',
				prevText: '&#x3c;Anterior',
				nextText: 'Pr&oacute;ximo&#x3e;',
				currentText: 'Hoje',
				monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
				dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
				dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
				weekHeader: 'Sm',
				dateFormat: 'dd/mm/yy',
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ''
			};
			$.datepicker.setDefaults($.datepicker.regional['pt-BR']);

			
			var options = {};
			<?php 
			
			if($dataMax){
				list($Y,$m,$d) = explode("-",$dataMax);
				echo "options = { maxDate: new Date($Y, ".($m-1).", $d, 0, 0, 0, 0) };";				
			}
			
			?>			
			options = $.extend(options, {
				changeMonth: true,
				changeYear: true
			});
			
			$("#data").datepicker(options);

			$("form").submit(function(e){
				e.preventDefault();
					
				var data = $("#data").datepicker( "getDate" );
				var d = (data.getDate()<=9?"0"+data.getDate():data.getDate());
				var m = (data.getMonth()<=10?"0"+(data.getMonth()+1):(data.getMonth()+1));
				var Y = data.getFullYear();
				var strData = d+"/"+m+"/"+Y;
				
				window.location.href = 'atestado_vacina_print.php?usu_codigo=<?=$usu_codigo?>&data='+strData;
				return false;			
			});
		});
		</script>
	</head>
	<body>
		<form method="post">
			<input type='hidden' name='usu_codigo' id='usu_codigo' value='<?=$usu_codigo;?>'>
			<span>Informe a validade do atestado:</span>
			<div id="data"></div>
			<input type="submit" value="Emitir atestado" />
		</form>	
	</body>
</html>