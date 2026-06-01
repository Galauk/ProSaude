
/**
 * Começa a piscar $ minutos antes do horário da próxima dispensação
 * vide zf/configuracao
 */
var _alertaEm;
var _timerRefresh;

$(function(){
		
	atualizarDiferencas();
	setInterval('atualizarDiferencas()',1000);
	setInterval('alertas()',1000);
	setTimeout('verificaERecarrega()',360000); // 5 min

	$(".leito").click(function(){
		
	
    
	//session.initialize();
	var id = $(this).data("id");
	writeCookie('idGrade', id, 3); 
        atribuiId(id);
	//alert(id);
	//session.putValue("teste",id);
//	$("body").append("<div id=\"valida-dialog\" title=\"Validação de usuário\" />");
//	$("#valida-dialog").html("<applet  code=\"br.com.elotech.applet.ui.BiometricMainForm.class\" archive=\"/WebSocialSaude/biometria/EloBiometriaApplet-DigitalPersona.jar\" mayscript=\"mayscript\"><param name=\"tipooperacao\" value=\"verificar\"></applet>")
//
//	.dialog({
//		modal: true,
//		width: 270,
//		height: 250,
//		close: function(){
//			$(this).remove();
//		},
//		beforeClose: function() { 
//		// alert(resultado);
//		}
//	});

//	
	
});
//	$("body").append("<div id=\"leito-dialog\" title=\"Medicamentos a serem ministrados\" />");
//	
//	$("#leito-dialog")
//	.html( imgCarregando() )
//	.load(baseUrl + '/leito/medicamentos/listar-produtos/lgra/'+ id, onLoadMedicamentos)
//	.dialog({
//		modal: true,
//		width: 542,
//		height: 400,
//		close: function(){
//			$(this).remove();
//		},
//		buttons: [
//		{
//			id: "btn_acao",
//			text: "carregando",
//			click: function(){
//				$("form#form-grade").submit();
//				$(this).dialog('close');
//			}
//		},
//		{
//			id: "btn_historico",
//			text: "Histórico",
//			click: function(){
//				mostrarHistorico(id);
//				//$(this).dialog('close');
//			}
//		},
//		{
//			id: "btn_devolver",
//			text: "Devolver",
//			click: function(){
//				confirme("Confirme","Deseja realmente devolver este(s) medicamentos para o estoque?", 330,150, function(){
//					devolver(id);
//				});
//				$(this).dialog('close');
//			}
//		},
//		{
//			id: "btn_cancelar",
//			text: "Cancelar",
//			click: function(){
//				$(this).dialog('close');
//			}
//		}
//		]
//	});
//	//return false;
//	$("#btn_acao,#btn_devolver")
//	.attr("disabled","disabled")
//	.addClass("ui-state-disabled");
//	// especial button!
//	$("#btn_historico").prependTo(".ui-dialog-buttonpane");
//	//$(this).remove();
	});
	
function writeCookie(name,value,days) { var date, expires; if (days) { date = new Date(); date.setTime(date.getTime()+(days*24*60*60*1000)); expires = "; expires=" + date.toGMTString(); }else{ expires = ""; } document.cookie = name + "=" + value + expires + "; path=/"; } 
function readCookie(name) { var i, c, ca, nameEQ = name + "="; ca = document.cookie.split(';'); for(i=0;i < ca.length;i++) { c = ca[i]; while (c.charAt(0)==' ') { c = c.substring(1,c.length); } if (c.indexOf(nameEQ) == 0) { return c.substring(nameEQ.length,c.length); } } return ''; } 
function atribuiId (id2) {
    idGrade = readCookie('idGrade'); 
//	alert(baseUrl + '/leito/medicamentos/listar-produtos/lgra/'+ id);
	//alert(session.getValue(id));
//	$.ajax({
//		  url: "/WebSocialSaude/biometria/getUsrSistem.php?id="+id2,
//		  success: function(resultado){
//			 
//			  if(resultado == '0') {
//				  alert("Usuario nao encontrado!");
//				 
//				  // $(this).dialog('close');
//			  } else {
//				   
//
//
//			  }
//		  }
//	});

    jQuery('#valida-dialog').dialog('close');
        
        var url = baseUrl + '/leito/medicamentos/listar-produtos/lgra/'+idGrade;
        //alert(dados[5]);
        $("body").append("<div id=\"leito-dialog\" title=\"Medicamentos a serem ministrados\" />");

        $("#leito-dialog")
        .html( imgCarregando() )
        .load(url, onLoadMedicamentos)
        //.load(baseUrl + '/leito/medicamentos/listar-produtos/lgra/'+idGrade+'/nome/'+usu_nome, onLoadMedicamentos)
        .dialog({
                modal: true,
                width: 542,
                height: 400,
                close: function(){
                        $(this).remove();
                },
                buttons: [
                {
                        id: "btn_acao",
                        text: "carregando",
                        click: function(){
                                $("form#form-grade").submit();
                                $(this).dialog('close');
                        }
                },
                {
                        id: "btn_historico",
                        text: "Histórico",
                        click: function(){
                                mostrarHistorico(idGrade);
                                //$(this).dialog('close');
                        }
                },
                {
                        id: "btn_devolver",
                        text: "Devolver",
                        click: function(){
                                confirme("Confirme","Deseja realmente devolver este(s) medicamentos para o estoque?", 330,150, function(){
                                        devolver(idGrade);
                                });
                                $(this).dialog('close');
                        }
                },
                {
                        id: "btn_cancelar",
                        text: "Cancelar",
                        click: function(){
                                $(this).dialog('close');
                        }
                }
                ]
        });
        //return false;
        $("#btn_acao,#btn_devolver")
        .attr("disabled","disabled")
        .addClass("ui-state-disabled");
        // especial button!
        $("#btn_historico").prependTo(".ui-dialog-buttonpane");
  writeCookie('idGrade', '', 3); 
}
function mostrarHistorico(lgra_codigo){
	carregandoAba(1);
	$("body").append("<div id=\"historico-dialog\" title=\"Histórico de Dispensação\" />");
	$("#historico-dialog")
	.load(baseUrl+'/leito/medicamentos/historico/lgra/'+lgra_codigo, function(){
		carregandoAba(0);
		bindHistorico();
	})
	.dialog({
		modal: true,
		width: 440,
		height: 330,
		close: function(){
			$(this).remove();
		},
		buttons: {
			Ok: function(){
				$(this).dialog('close');
			}
		}
	});
}

function bindHistorico(){
    $("#historico .mais").click(function(){
            $(this).parents("tr").next("tr").show();
    });
}

function onLoadMedicamentos(){
	//return false
	habilitarBotoes();
	if($("#status").val() != 1){ // pare por aqui: a grade não está mais ativa
		mensagem("Atenção!","Esta grade não está mais ativa!<br /><br />Clique em OK para recarregar a tela.", 330,170,reload)
	}
	
	var tipo = $("#tipo").val();
	//alert(tipo);
	var url = baseUrl+"/"+ (tipo==1?"leito/medicamentos/dispensar-da-reserva":"produto/reservar") ;
	
	var lgra = $("#lgra_codigo").val();
	window.console && console.log("codigo "+lgra);
	window.console && console.log("vezes: "+$("#lgra_"+lgra).data("vezes"));
	$("#num").val( $("#lgra_"+lgra).data("vezes") );
	
	$('#form-grade')
	.attr("action",url)
	.bind('submit', function(e) {
		e.preventDefault(); // <-- important
		carregandoAba(1);
		$(this).ajaxSubmit(afterSubmit);
	});
}

function afterSubmit(json){
	carregandoAba(1);
	if(json.error){
		mensagem("Erro!",json.mensagem, 300,150, function(){
			reload(json);
		});
	} else 
            
		reload(json);
}

function reload(json){
    window.location.replace(baseUrl+"/leito/medicamentos/index/"); 
}

function verificaERecarrega(){
	var modaisAbertos = $(".ui-dialog").size();
	if(modaisAbertos > 0){
		if(typeof(_timerRefresh) == "undefined")
			_timerRefresh = setInterval('verificaERecarrega()',1000);
		
	} else {
		if(typeof(_timerRefresh) != "undefined")
			clearInterval(_timerRefresh);
		
		reload();
	}
	
	window.console && console.log("verificando modais: "+modaisAbertos);
}

function habilitarBotoes(){
	var tipo = $("#tipo").val();
	$("#btn_acao").removeAttr("disabled").removeClass("ui-state-disabled").find("span").html(tipo == 1?"Dispensar":"Reservar");
	if(tipo == 1){ // botão devolver
		$("#btn_devolver").removeAttr("disabled").removeClass("ui-state-disabled");		
	} else {
		$("#btn_devolver").remove();
	}
}

function devolver(id){
	carregandoAba(1);
	$.ajax({
		url: baseUrl+'/produto/retirar-reserva/',
		type: 'post',
		data:{
			tipo: "lgra_codigo",
			codigo: id
		},
		success: function(){
			carregandoAba(0);
			mensagem("Sucesso","Medicamentos devolvidos com sucesso",330,150);
		}
	});
}

function atualizarDiferencas(){
        //alert("localization");
	_alertaEm = $("#alertaEm").val();
	
	$(".leito").each(function(){
		
		var horario = $(this).data("proximo");
		
		if(horario == ""){
			$(this)
			.find("span:last")
			.html( "Nenhuma dispensação realizada ainda" )
		} else {
			var atraso = compararDataHora(horario)==2;
			var dif = diferencaEntreHoras(horario);
			var lgra = $(this).attr("id");
			
			$(this)
			.find("span:last")
			.html( segundosToHora ( dif ) )
			.end()
			.find("span:first")
			.html( (atraso?"Atraso":"Faltam")+":" );
			
			if(atraso){
				$(this).addClass("ui-state-error").addClass("high");
				$(this).effect("highlight", {}, 1000);
			
			} else if(dif < _alertaEm * 60){
				$(this).addClass("high").effect("highlight", {}, 1000);				
			}
			
		}
		
	});
}

function alertas(){
	$(".high").effect("highlight", {}, 1000);
}