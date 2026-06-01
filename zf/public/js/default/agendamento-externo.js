$(function(){
	
	// $("#salvar-agenda").ajaxForm(afterSubmit);
	// se já houver usu_codigo quando abrir a página (editar ou erro) já carrega o historico
	if( $("#usu_codigo").val() != ""){
		carregarItens();
	}
	
	
	if($("#imprimir").size() && $("#imprimir").val() != "") {
        popup(baseUrl+"/agendamento-externo/imprimir/id/"+$("#imprimir").val(),"agendamento-externo",600,450);
        $("#imprimir").val("");
    }
	
	

	// Buscar pacientes
	$("#buscar1").buscar({

		url: baseUrl+'/paciente/buscar/',
		callback: function(){
			carregarItens();
			return false;
		}
	});

	// Buscar especialidades
	$("#buscar2").buscar({
		url: baseUrl+'/especialidade/buscar/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});

	// Buscar médico externo (destino)
	$("#buscar3").buscar({
		url: baseUrl+'/medico-externo/buscar/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});

	// Buscar procedimento
	$("#buscar4").buscar({
		url: baseUrl+'/procedimento/buscar/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});

	// Solicitante: Buscar médicos internos (usr) ou externos (med)
	$("#buscar5").buscar({
		url: baseUrl+'/default/usuarios/buscar/externo/1',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});

	// Buscar prestradores de serviço
	$("#buscar6").buscar({
		url: baseUrl+'/medico-externo/buscar/prestador/L/prestador/H/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});

});

function carregarItens(){
	$("#itens")
	.html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando histórico\" title=\"Carregando\" />")
	.load(baseUrl+"/agendamento-externo/itens/usu_codigo/"+ $("#usu_codigo").val(), function(){
		bind();
	});
}

function bind(){
	$("#itens .excluir")
	.unbind("click")
	.bind("click", function(e){
		e.preventDefault();
		e.stopPropagation();
		
		var link = $(this);
		confirme("Confirme","Deseja realmente excluir este item?",300,120,function(){
			$.ajax({
				url: link.attr("href"),
				success:function(){
					link.parents("tr").slideUp("normal",function(){
						$(this).remove();
					});
				}
			});
		});
	});
	
}

function salvarAgendamento(){
    if($(".salvar").hasClass("ui-state-disabled") === false){
        mensagemSemOk("salvando-age", "Salvando...", "Salvando Agendamento ...", 280, 80);
    }
}

// function afterSubmit(json){
	
// 	if(!json.success){
// 		console.log("if")
//         mensagem(json.titulo,json.mensagem, 300, 150);
// 		return;
// 	} else {
// 		console.log("else")
// 		return false
// 		$("body").append("<div id=\"mensagem-dialog\" title=\"Sucesso\">Paciente agendado com sucesso!<br /><br />Deseja imprimir a guia de agendamento?</div>");
		
// 		$("#mensagem-dialog").dialog({
// 			modal: true,
// 			width: 290,
// 			height: 180,
// 			close: function(){
// 				window.location.href = baseUrl + "/agendamento/index/";
// 				$(this).remove();
// 			},
// 			buttons: {
// 				Sim: function(){
// 					// imprimir
//                     var p_horario = "";
//                     if($("#imprimir_primeiro_horario").val() == 1){
//                         p_horario = $("#primeiro_horario").val();
//                     }
// 					popup(baseUrl+"/agendamento/agendamento/imprimir/age/"+json.age_codigo+"/p_horario/"+p_horario,"imprimir-agenda",600,500);
// 					$(this).dialog('close');
// 				},
// 				"Não": function(){
// 					// não imprimir
// 					$(this).dialog('close');
// 				}
// 			}
// 		});
// 	}
// }