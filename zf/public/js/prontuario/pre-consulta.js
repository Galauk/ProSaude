$(function(){
       /*var gambi = $("#gambi").val();
        if(gambi != "s"){
            $(":input").attr('disabled', 'disabled');
        }else{
            $(":input").removeAttr('disabled');
        }*/
    
	$("#historico").click(function(){
		$("#historico-dialog").dialog({
			modal: true	
		});
	});
    
	$("form").validate({
		rules: {
			temperatura: {
				range: [30,45]
			},
			peso: {
				range: [0,200]
			},
			altura: {
				range: [0,2.5]
			},
			pressao_sistolica: {
				range: [0,300]
			},
			pressao_distolica: {
				range: [0,300]
			}
			
		},
		messages: {
			temperatura: {
				range: "Verifique a temperatura."
			},
			peso: {
				range: "Verifique o peso."
			},
			altura: {
				range: "Verifique a altura."
			}
		}
	});
    
});

function atualizaIMC() {
    var peso = $("#peso").val();
    var altura = $("#altura").val();

    altura *= altura;

    if (peso && altura) {
	     var imc = Math.round(peso / altura * 100) / 100;
		if(imc<'17') {
		  $("#r_imc").html("( <b>Muito abaixo do peso</b> )");
		}
		if((imc>='17' & imc<='18.49')) {
		  $("#r_imc").html("( <b>Abaixo do peso</b> )");
		}
		if((imc>='18.5' & imc<='24.99')) {
		  $("#r_imc").html("( <b>Peso normal</b> )");
		}
		if((imc>='25' & imc<='29.99')) {
		  $("#r_imc").html("( <b>Acima do peso</b> )");
		}
		if((imc>='30' & imc<='34.99')) {
		  $("#r_imc").html("( <b>Obesidade I</b> )");
		}
		if((imc>='35' & imc<='39.99')) {
		  $("#r_imc").html("( <b>Obesidade II (severa)</b> )");
		}
		if(imc>='40') {
		  $("#r_imc").html("( <b>Obesidade III (mórbida)</b> )");
		}
		
        return $("#imc").val(Math.round(peso / altura * 100) / 100);
    }

}