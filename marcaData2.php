<html>
	<head>
		<title>Selecione a data</title>
		<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
		<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
		<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
		<script>
		$(function(){
			$.datepicker.regional['pt-BR'] = {
				closeText: 'Fechar',
				prevText: '&#x3c;Anterior',
				nextText: 'Pr&oacute;ximo&#x3e;',
				currentText: 'Hoje',
				monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
				'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
				'Jul','Ago','Set','Out','Nov','Dez'],
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
			if(resposta == A){ // aplicar: num pode ser data futura
				options = { minDate: new Date() };
			} else if(resposta == "P"){
				options = { maxDate: new Date() };
			}
			
			$("#data").datepicker(options);

			$("form").submit(function(){				
				var id = $("#id").val();
				var para = $("#para").val();
				var resposta = $("#resposta").val();
				var pro_codigo = $("#pro_codigo").val();
				var usu_codigo_prontuario = $("#usu_codigo_prontuario").val();
				var data = $("#data").val();
				var unidade = $("#unidade").val();				

				window.opener.executaAcao(id, data, unidade,resposta,para,pro_codigo,usu_codigo_prontuario);
				window.close();	

				return false;			
			});
		});
		</script>
	</head>
	<body>
		<form method="post">
			<input type='hidden' name='id' id='id' value='<?=$id;?>'>
			<input type='hidden' name='para' id='para' value='<?=$para;?>'>
			<input type='hidden' name='usu_codigo_prontuario' id='usu_codigo_prontuario' value='<?=$usu_codigo_prontuario;?>'>
			<input type='hidden' name='pro_codigo' id='pro_codigo' value='<?=$pro_codigo;?>'>
			<input type='hidden' name='resposta' id='resposta' value='<?=$resposta;?>'>
			<input name="data" id="data" />
			<input type="submit" value="Enviar" />
		</form>	
	</body>
</html>