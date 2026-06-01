$(function(){
        $(".finalizar").click(function(){
                var ate_codigo = $("#ate_codigo").val();
                var age_codigo = $("#age_codigo").val();
                var io_codigo = $("#io_codigo").val();
                
                
		$("body").append("<div id=\"finalizar-dialog\" title=\"Finalizar\">"+
                                    "Deseja realmente finalizar este atendimento?"+
                                  "</div>");
		$("#finalizar-dialog")
		.dialog({
			modal: true,
			width: 405,
			height: 225,
			close: function(){
				$(this).remove();
			},
			buttons: {
				Finalizar: function(){
                                    $.ajax({
                                        url:baseUrl + "/leito/atendimento/finalizar",
                                        data: {obs : "S",
                                               age_codigo : age_codigo,
                                               ate_codigo : ate_codigo,
                                               io_codigo : io_codigo},
                                        type:"GET",
                                        success:function(){
                                            $.cookie("ate_reclamacao","");
                                            $.cookie("ate_exame_fisico", "");
                                            $.cookie("ate_diagnostico", "");
                                            $.cookie("ate_tratamento", "");
                                            $.cookie("ate_curativo", "");
                                            $("#finalizar-dialog")
                                                .html(imgCarregando())
                                            $(this).dialog('close');
                                            location.href = baseUrl+"/leito/internacao";
                                        }
                                    });
				},
                                Retorno: function(){
					$.ajax({
                                            url:baseUrl + "/leito/atendimento/retorno",
                                            data: {
                                                   ate_codigo : ate_codigo,
                                                   retorno : "S",
                                                   io_codigo : io_codigo,
                                                  },
                                            type:"GET",
                                            success:function(txt){
                                                $("#finalizar-dialog")
                                                    .html(imgCarregando())
                                                $(this).dialog('close');
                                                location.href = baseUrl+"/leito/internacao";
                                            }
                                        });
				},
                                Cancelar: function(){
					$(this).dialog('close');
				}
			}
		});
        });
	$(".alta2").click(function(){		
		ate_codigo = $("#ate_codigo").val();
		io_codigo = $("#io_codigo").val();		
		url = baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo+"/#tabs3-2";		
		document.alta.submit();
                setInterval(function() {
                    window.opener.document.location= baseUrl + "/leito/internacao/";
                    window.close();
                }, 1000);
		
	});
	$("#ver-mais-pre-consultas").click(verMaisPreConsultas);

	
	
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
		var ate_codigo = $(this).data("pc");
		$("body").append("<div id=\"pre-consulta-dialog\" title=\"Sinais Vitais\" />");
		$("#pre-consulta-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/leito/sinais-vitais/ver/ate_codigo/"+ate_codigo+"/sem-layout/1/readonly/1",function(){
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
	
	$(".sinais").click(function(){

		if(!$(this).data("pc")){
			var ate_codigo = $("#ate_codigo").val();			
		}else{
			var ate_codigo = $(this).data("pc");
		}
		
		
		var io_codigo = $("#io_codigo").val();
		
		$("body").append("<div id=\"pre-consulta-dialog\" title=\"Sinais Vitais\" />");
		$("#pre-consulta-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/leito/sinais-vitais/ver/ate_codigo/"+ate_codigo+"/cod/"+io_codigo+"/sem-layout/1",function(){
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
		.load(baseUrl + "/prontuario/exame/index/obs/S/ate_codigo/"+$("#ate_codigo").val()+"/io_codigo/"+$("#io_codigo").val(),function(){
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
		.load(baseUrl + "/prontuario/receita-medica/index/obs/S/io_codigo/"+$("#io_codigo").val(),function(){
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
		.load(baseUrl + "/prontuario/procedimento/index/obs/S/ate_codigo/"+$("#ate_codigo").val()+"/io_codigo/"+$("#io_codigo").val(),function(){
			megaBind("#procedimento-dialog");
		})
		.dialog({
			modal: true,
			width: 800,
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
	$("#mais").click(function(){
		$.ajax({
			url: baseUrl + "/prontuario/procedimento/itens-internacao",
			data:{
				obs : "S",
				ate_codigo : $("#ate_codigo").val(),
				io_codigo : $("#io_codigo").val(),
				limit: "2"
				
			},
			type:"GET",
			success: function(txt){
				alert(txt);
			}
		
		
		});
			
		
	});
	$(".encaminhamento").click(function(){		
		$("body").append("<div id=\"encaminhamento-dialog\" title=\"Encaminhamento\" />");
		$("#encaminhamento-dialog")
		.html(imgCarregando())
		.load(baseUrl + "/prontuario/encaminhamento/index/obs/S/io_codigo/"+$("#io_codigo").val()+"/ate_codigo/"+$("#ate_codigo").val(),function(){
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