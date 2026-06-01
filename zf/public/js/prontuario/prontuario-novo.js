// NAO UTILIZA ESSE ARQUIVO, SÓ UTILIZARÁ SE MUDAR O PRONTUÁRIO ELETRONICO PARA ESTE NOVO MODELO
$(function(){
	$("#ver-mais-pre-consultas").click(verMaisPreConsultas);
	
	$("#procedimento").keypress(function(){
		alert('HAHA');
	});
	
	
	$(".exame").click(function(){

		$("#procedimento").buscar({
		url: baseUrl+"/procedimento/buscar/",
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});
	});
	$(".pre-consulta").click(function(){
		var pc_codigo = $(this).data("pc");
		$("body").append("<div id=\"pre-consulta-dialog\" title=\"Pré-Consulta\" />");
		$("#pre-consulta-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/prontuario/pre-consulta/ver/id/"+pc_codigo+"/sem-layout/1",function(){
			megaBind("#pre-consulta-dialog");
		})
		.dialog({
			modal: true,
			width: 610,
			height: 550,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Ok: function(){
					$(this).dialog('close');
				}
			}
		});
	});
	
	$(".exame").click(function(){		
		$("body").append("<div id=\"exame-dialog\" title=\"Exame\" />");
		$("#exame-dialog")		
		.html(imgCarregando())
		.load(baseUrl + "/prontuario/exame/index/obs/S",function(){
			megaBind("#exame-dialog");
		})
		.dialog({
			modal: true,
			width: 610,
			height: 550,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Ok: function(){
					$(this).dialog('close');
				}
			}
		});
		
	});
	
	$(".receita").click(function(){		
		$("body").append("<div id=\"receita-dialog\" title=\"Receita Médica\" />");
		$("#receita-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/prontuario/receita-medica/index/obs/S",function(){
			megaBind("#receita-dialog");
		})
		.dialog({
			modal: true,
			width: 610,
			height: 550,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Ok: function(){
					$(this).dialog('close');
				}
			}
		});
	});
	$(".procedimento").click(function(){		
		$("body").append("<div id=\"procedimento-dialog\" title=\"Procedimento\" />");
		$("#procedimento-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/prontuario/procedimento/index/obs/S",function(){
			megaBind("#procedimento-dialog");
		})
		.dialog({
			modal: true,
			width: 610,
			height: 550,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Ok: function(){
					$(this).dialog('close');
				}
			}
		});
	});
	$(".encaminhamento").click(function(){		
		$("body").append("<div id=\"encaminhamento-dialog\" title=\"Encaminhamento\" />");
		$("#encaminhamento-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/prontuario/encaminhamento/index/obs/S",function(){
			megaBind("#encaminhamento-dialog");
		})
		.dialog({
			modal: true,
			width: 610,
			height: 550,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Ok: function(){
					$(this).dialog('close');
				}
			}
		});
	});
	
});

function verMaisPreConsultas(){
	$("#ver-mais-pre-consultas").html("Menos")
	.unbind("click")
	.click(verMenosPreConsltas);
	
	$("#historico-pre-consulta").show("normal");
}

function verMenosPreConsltas(){
	$("#ver-mais-pre-consultas").html("Ver mais")
	.unbind("click")
	.click(verMaisPreConsultas);
	
	$("#historico-pre-consulta").hide("fast");	
}