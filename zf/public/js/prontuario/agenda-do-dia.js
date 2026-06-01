$(function(){
    $("#ate_data").mask("99/99/9999");
    $("#buscar_paciente").buscar({
            url: baseUrl+'/paciente/buscar/',
            template : function(ul, item) {
                    return jQuery("<li></li>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(event, ui){
                    return true;
            }
    });
        $("#buscar_medico").buscar({
		url: baseUrl+'/default/usuarios/buscar/',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});
});
function iniciarAge(cod){	
	window.location = baseUrl + "/prontuario/index/iniciar/cod/"+cod;
}

function verAte(cod){
	window.location = baseUrl + "/prontuario/atendimento/ver/age/"+cod;
}
function verificaSeEstaEmAtendimento(cod,ate_codigo){
        mensagemSemOk("carregando-ate", "Aguarde", "Carregando prontuário...", 280, 80);
	$.ajax({
                url: baseUrl+'/prontuario/atendimento/verificaseestaematendimento',
                type: "POST",
                data: {
                    age_codigo: cod,
                    ate_codigo: ate_codigo
                },
                success: function(txt){
                    $.cookie("ate_reclamacao","");
                    $.cookie("ate_exame_fisico", "");
                    $.cookie("ate_diagnostico", "");
                    $.cookie("ate_tratamento", "");
                    $.cookie("ate_curativo", "");

					if(txt == 2){
                        window.location = baseUrl + "/prontuario/agenda-do-dia/index/pacem/s";	
                    }else{
                        window.location = baseUrl + "/prontuario/index/iniciar/cod/"+cod;	
                    }

                },
                error: function(pq){alert(pq)}
        });
}

