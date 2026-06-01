$(function(){
	
	// duplicar os inputs
	$("input[name^=d]").each(function(){
		var nome = $(this).attr("name");
		var value = $(this).val();
		
		// coloca um hidden com o valor que estava antes de ser editado
		$(this)
		.bind('change',function(){
			$(this).addClass("alterando");
			calcularTotais();
		})
		.after("<input name=\"o"+nome.substring(1)+"\" value=\""+value+"\" type=\"hidden\" />")
		verificarSeEstaDiferenteDoValorCalculado($(this));		
	});
	
	calcularTotais();
	
});

function somar(arr){
	var total = 0;
	for(var i in arr){
		total += parseInt($("#"+arr[i]).val() );
	}
	
	return total;
}

function calcularTotais(){
	$("#VISITAS_TOTAL").val(
		somar(['VISITAS_MEDICO','VISITAS_ENFERMEIRO','VISITAS_SUPERIOR','VISITAS_MEDIO','VISITAS_ACS'])
		);
		
	$("#CONSULTA_SUBTOTAL").val(
		somar(['CONSULTA_MENOR_DE_1_ANO','CONSULTA_DE_1_A_4','CONSULTA_DE_5_A_9','CONSULTA_DE_10_A_14','CONSULTA_DE_15_A_19','CONSULTA_DE_20_A_39','CONSULTA_DE_40_A_49','CONSULTA_DE_50_A_59','CONSULTA_60_OU_MAIS'])
		);
		
	$("#CONSULTA_TOTAL").val(
		parseInt( $("#CONSULTA_FORA_DA_AREA").val() ) + parseInt($("#CONSULTA_SUBTOTAL").val())
	);
	
}

function verificarSeEstaDiferenteDoValorCalculado(input){
	var nome = input.attr("name").substring(2);
	nome = nome.substr(0, nome.length-1).replace(".","_");
	var value = input.val();
	var calc = $("#c_"+nome);

	if(value != calc.val()){
		var usr_nome = calc.data("usr");
		var date = calc.data("data");
		var html = "<div class=\"tooltip\"><strong>Alterado por:</strong> "+usr_nome+"<br />";
		html+= "<strong>em:</strong> "+datatimeToBr(date)+"<br />";
		html+= "<strong>Valor calculado:</strong> "+calc.val()+"</div>";
		
		input
		.addClass("ui-state-highlight")
		.easyTooltip({
			content: html
		});

	} else {
		input.removeClass("ui-state-highlight");
	}
		
}
