var _cacheDataSelecionada = [];
var _examesDoAtendimento = [];

$(function(){

	// Salvar por ajax
	//$("#salvar-agenda").ajaxForm(afterSubmit);

	$("#salvar-agenda").ajaxForm(afterSubmit);
	$(".salvar").click(function(){
		if($(this).hasClass("ui-state-disabled"))
			return;
		mensagemSemOk("salvando-age", "Aguarde", "Salvando agendamento...", 280, 80);
	});

	// buscar atendimento
	// $("#buscar-atendimento").bind("click",buscarAtendimento);

	$("#usu_nome").buscar({
		url: baseUrl+'/paciente/buscar',
		callback: verificarPreechimentoPaciente
	});



	$("#procedimentos")
	.bind('dblclick', selecionarProcedimento)
	.bind('keydown', selecionarProcedimento);

	$("#procedimentos-selecionados")
	.bind('dblclick', deselecionarProcedimento)
	.bind('keydown', deselecionarProcedimento);

	$("#atualizar-grid").click(carregarCalendario);

	$("#usr_nome").buscar({
		url: baseUrl+'/default/usuarios/buscar/externo/1',
		categoria: 'categoria',
		//suffix: '_solicitante',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});

	$("#med_nome").buscar({
		url: baseUrl+'/agenda/convenio/buscar/',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: carregarItensDoLocal
	});



});

function verificarPreechimentoPaciente() {
		fecharMensagemSemOk("excluindo-agei");
		habilitarOuNaoBotaoSalvar();
}

// function carregarHistoricoDoPaciente(){
// 	fecharMensagemSemOk("excluindo-agei");
// 	habilitarOuNaoBotaoSalvar();
//
// 	$("#em-historico").html( "<img class=\"loading\" src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" />" );
//
// 	var url = baseUrl+"/agenda/agenda/historico/usu/"+$("#usu_codigo").val();
// 	$("#historico").load(url, onHistoricoLoad);
//
// }

// function onHistoricoLoad(){
// 	// verifica se atendimento já foi usando
// 	var ate_codigo = $("#ate_codigo").val();
// 	if(ate_codigo){
// 		var usado = $("tr[data-ate_codigo='"+ate_codigo+"'] td")
// 		.addClass("ui-state-highlight")
// 		.size();
//
// 		if(usado){
// 			mensagem("Atenção:","Este código de atendimento já foi usado.<br /><br />Os itens deste agendamento estão destacados no histórico abaixo.", 400, 180);
// 		}
// 	}
//
// 	megaBind("#historico");
//
// 	// bind do excluir
// 	$("#historico .excluir").bind("click", function(){
// 		var agei_codigo = $(this).data("agei");
//
// 		confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function(){
// 			excluirAgei(agei_codigo);
// 		});
// 	})
//
// 	$("#em-historico").html(""); // tira img loading
// 	$("#historico").show("normal");
// }

// function excluirAgei(agei_codigo){
// 	mensagemSemOk("excluindo-agei", "Excluindo...", "Excluindo exame...", 280, 80);
//
// 	$.ajax({
// 		url: baseUrl+"/agenda/agenda/excluir/",
// 		type: "POST",
// 		data: {
// 			agei_codigo: agei_codigo
// 		},
// 		success: carregarHistoricoDoPaciente
// 	});
// }

// modal
// function buscarAtendimento(){
// 	var html = "<form id=\"buscar-atedimento-form\">";
// 	html += "<label>Código do atendimento:</label> ";
// 	html += "<input id=\"ate_codigo_temp\" /> ";
// 	html += "</form>";
//
// 	$("body").append("<div id=\"buscar-atedimento-dialog\" title=\"Buscar Atendimento\" />");
// 	$("#buscar-atedimento-dialog")
// 	.html(html)
// 	.dialog({
// 		modal: true,
// 		width: 520,
// 		height: 120,
// 		close: function(){
// 			$(this).remove();
// 		},
// 		buttons: {
// 			Ok: function(){
// 				carregarPeloAtendimento();
// 				$(this).dialog('close');
// 			}
// 		}
// 	});
//
// 	$("#buscar-atedimento-form").submit(function(e){
// 		e.preventDefault();
// 		carregarPeloAtendimento();
// 		$("#buscar-atedimento-dialog").dialog('close');
// 	});
// 	megaBind("#buscar-atedimento-dialog");
// }

// function carregarPeloAtendimento(){
// 	var ate_codigo = $("#ate_codigo_temp").val();
//
// 	if(!ate_codigo)
// 		return;
//
// 	mensagemSemOk("buscando-atendimento", "Buscando...", "Buscando dados do atendimeto", 280, 80);
// 	$.ajax({
// 		url: baseUrl+'/prontuario/atendimento/json/ate/'+ate_codigo,
// 		dataType: 'json',
// 		success: function(json){
// 			fecharMensagemSemOk("buscando-atendimento");
// 			tratarRetornoDoAtendimento(json);
// 		}
// 	});
// }

// function tratarRetornoDoAtendimento(json){
// 	if(!json.success){
// 		mensagem("Erro!",json.mensagem, 250, 130);
// 		return;
// 	}
//
// 	for ( var i in json) {
// 		$("#" + i).val(json[i]);
// 	}
//
// 	// sempre que achar, interno será 1, pois só há atendimento realizado por usr e nunca med
// 	$("#interno").val(1);
//
//
// 	// limpar cache
// 	_examesDoAtendimento = [];
//
// 	// salvar os exames em cache*
// 	for(var i in json.exames){
// 		_examesDoAtendimento.push(json.exames[i].proc_codigo);
//
//
// 	}
//         //insere os exames do paciente ao digitar o codigo de barras do atendimento
// 	//listarProcedimento(json.exames);
// 	// foco no local
// 	$("#med_nome").select();
// 	carregarHistoricoDoPaciente();
//
// }

function carregarItensDoLocal(){
	mensagemSemOk("carregando-conis", "Aguarde", "Carregando lista de exames...", 280, 80);
	$("#procedimentos-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum exame selecionado</option>');
	var conv_codigo = $("#conv_codigo").val();
	$.ajax({
		url: baseUrl+'/agenda/convenio-itens/procedimentos-ajax',
		type: "POST",
		data: {
			conv_codigo: conv_codigo
		},
		success: function(json){
			listarProcedimento(json);
		}
	});
        $('#escondida').show();
        buscaProc(conv_codigo);
}

function buscaProc(conv_codigo){
        $(function(){
            $("#proc_nome").buscar({

                url: baseUrl+'/agenda/convenio-itens/buscar/conv_codigo/'+ conv_codigo,
                suffix: '_2',
		search: function(){
			$("#procedimentos").empty();
		},
		template : function(ul, item) {
                                ul.hide();
                                $("<option />").val(item.id).html(item.label).appendTo("#procedimentos");
                                return false;
		},
		callback: function(event, ui){
			$("#procedimentos").focus();
		}

            });
        });
}


function afterSubmit(json){

	fecharMensagemSemOk("salvando-age");
	if(!json.success){
		if(json.code == 1 || json.code == 2)
			popupLogin();

		else
			mensagem(json.titulo,json.mensagem, 300, 150);

		return;
	} else {
		setarColeta(json.age_codigo);
		$("body").append("<div id=\"mensagem-dialog\" title=\"Sucesso\">Exame(s) agendado com sucesso!<br /><br />Deseja imprimir a guia de agendamento?</div>");
		$("#mensagem-dialog").dialog({
			modal: true,
			width: 290,
			height: 180,
			close: function(){
				//window.location.href = baseUrl + "/agenda/agenda-emergencia/";
				var url = baseUrl+"/agenda/agenda-emergencia/redirecionar/usu/"+$("#usu_codigo").val()+"/age/"+json.age_codigo;
				window.location.href = url;
				//redirecionar(json.age_codigo, $("#usu_codigo").val());
				$(this).remove();
			},
			buttons: {
				Sim: function(){
					// imprimir
					popup(baseUrl+"/agenda/agenda-emergencia/imprimir/age/"+json.age_codigo,"imprimir-agenda",600,500);
					$(this).dialog('close');
				},
				"Não": function(){
					// não imprimir
					$(this).dialog('close');
				}
			}
		});
	}
}

function redirecionar(age_codigo, usu_codigo){
	$.ajax({
        type: 'POST',
        url: baseUrl + "/agenda/agenda-emergencia/redirecionar/",
        data: {
            usu_codigo: usu_codigo,
						age_codigo: age_codigo
        },
        success: function(data){
            //window.location.href = url;
						console.log(data);
        }
    });
}

function setarColeta(age_codigo){
	console.log("DADOS FINAIS = " + _cacheDataSelecionada);
	//var cache = $.JSON.encode(_cacheDataSelecionada);
	var datasql = brToSql($("#data").val());
	$.ajax({
        type: 'POST',
        url: baseUrl + "/agenda/agenda-emergencia/coleta/",
        data: {
            array_cache: JSON.stringify(_cacheDataSelecionada),
						age_codigo: age_codigo,
						data: $("#data").val(),
						datasql: datasql
        },
        success: function(){
            console.log("success coleta");
        }
    });
}

function carregarCalendario(){
	// desabilitar btn salvar
	//$(".salvar").addClass("ui-state-disabled");
	
	var selecionados = getProcedimentosSelecionados();
	//console.log(getProcedimentosSelecionados());
	if(selecionados.length == 1 && selecionados[0] == 0){
		$("#calendario").html("<em>Selecione algum exame</em>");
		habilitarOuNaoBotaoSalvar();
		fecharMensagemSemOk("salvando-proc");
		return;
	}
	//console.log("passo2");
	$("#calendario").html(imgCarregando());


	
	var url = baseUrl + "/agenda/agenda/selecionar-data/procs/"+selecionados+"/de/"+brToSql($("#data").val());
	$("#calendario").load(url, bindCalendario);
}

/**
 * Adicionar eventos no grid
 */
function bindCalendario(){
	$("#grade tr th").slice(1,2).html("Selec.");

	$("[data-dow='0'],[data-dow='6']").each(function(){// cada domingo e sábado
		var index = $(this).data("index");
		var dow   = $(this).data("dow");
		$("[data-index='"+index+"']:not(.com-vaga)").addClass("dow"+dow);
	})

	// $("#grade tr td[data-dia]").ready(function(){
	// 	// var data = $(this).data("dia");
	// 	var data = $("[data-index='1']").data("dia");
	// 	console.log(data);
	// 	$("td[data-dia="+data+"]").addClass("destaque");
	// 	$("td[data-dia="+data+"]").addClass("com-vaga");
	//
	// }, function(){
	// 	var data = $(this).data("dia");
	// 	$("td[data-dia="+data+"]").removeClass("destaque");
	//
	// })
	// .ready(marcarDia)
	// .disableSelection();

	// console.log(brToSql($("#data").val()));
	  // $("td[data-dia=" + brToSql($("#data").val()) + "]").addClass("destaque");
		// $("td[data-dia=" + brToSql($("#data").val()) + "]").removeClass("destaque");
	//  console.log($("td[data-dia=" + brToSql($("#data").val()) + "]").val());
	//  marcarDia();

	$(".com-vaga,.sem-vaga").each(function(){
		var obj = $(this);
		var index = obj.data("index");
		var dow = $("[data-dow][data-index='"+index+"']").data("dow");
		var semana = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"];
		var proc_nome = obj.parents("tr").find("th").html();

		var vagas = obj.data("vagas");
		vagas = (vagas==-1)?"&infin; ilimitadas":vagas;

		var html = "<div><strong>Exame: </strong>"+proc_nome+"<br />";
			html += "<strong>Data: </strong>"+dataToBr(obj.data("dia"))+" ("+semana[dow].toLowerCase()+")<br />";
			html += "<strong>Vagas: </strong>"+vagas+"</div>";

		obj.easyTooltip({
			content: html
		});
	});
	ttmarcarDia();
	carregaValoresAntigos();

}

function carregaValoresAntigos(){
	$.each(_cacheDataSelecionada,function(coni_codigo,data){
		if(coni_codigo){
			//console.log(coni_codigo + " : "  + data);
			$("#coni_"+coni_codigo).val(data);
			var dataSql = brToSql(data);
			$("[data-dia='"+dataSql+"'][data-coni='"+coni_codigo+"']").html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
		}
	});

	habilitarOuNaoBotaoSalvar();
}

function selecionarProcedimento(e){
	// só pode ser a tecla 39 (seta para direita)
	if(e.keyCode && e.keyCode != 39 || e.charCode)
		return;

	if(!$("#procedimentos option:selected").size())
		return;

	// se o primeiro for 0, limpar select
	if($("#procedimentos-selecionados option:first").val() == "0"){
		$("#procedimentos-selecionados").empty();
	}

	// add
	
	$("#procedimentos-selecionados").append(
		$("#procedimentos option:selected")
	);

	mensagemSemOk("salvando-proc", "Aguarde", "Salvando procedimentos...", 280, 80);
	//habilitarOuNaoBotaoSalvar();
	carregarCalendario();

	//console.log(_cacheDataSelecionada);
}

function deselecionarProcedimento(e){
	var _cacheDataAux= [];
	// só pode ser a tecla 39 (seta para esquerda)
	if(e.keyCode && e.keyCode != 37 || e.charCode)
		return;

	// remover
	console.log("Deselecionado = "+$("#procedimentos-selecionados option:selected").val());
	//Copiar para um array auxiliar todos os elementos dos array com exceção do elemento ue deseja remover
	$.each(_cacheDataSelecionada, function(key, value) {
		if(key != $("#procedimentos-selecionados option:selected").val() && (value != null && value != "")){
			_cacheDataAux[key] = value;
		}
	});
	//Limpa array original
	_cacheDataSelecionada.splice(0,_cacheDataSelecionada.length);
	//_cacheDataSelecionada.splice($("#procedimentos-selecionados option:selected").val());
	//Copia os elementos do array auxiliar para o original
	$.each(_cacheDataAux, function(key, value) {
		if((value != null && value != "")){
			_cacheDataSelecionada[key] = value;
		}
	});
	$("#procedimentos-selecionados option:selected").appendTo("#procedimentos");
	//console.log("passou");


	// se não houver mais opções, add "Nenhum"
	if($("#procedimentos-selecionados option").size() == 0){
		$("#procedimentos-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum exame selecionado</option>');
	}
	
	//console.log(_cacheDataSelecionada);
	//habilitarOuNaoBotaoSalvar();
	mensagemSemOk("salvando-proc", "Aguarde", "Salvando procedimentos...", 280, 80);
	carregarCalendario();
}

function listarProcedimento(json){
	var select = $("#procedimentos").empty();
	var loop = 0;

	if(_examesDoAtendimento.length){ // existe uma lista de exames pré-selecionada, pelo código do atendimento
		for (var proc in json) {
                    //console.log(json[proc]);
			if( _examesDoAtendimento.indexOf(json[proc].proc_codigo) > -1){
				select.append("<option value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+"</option>");
			} else {
				select.append("<option disabled=\"disabled\" value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+"</option>");
			}
			loop++;
		}
	} else {
		for (var proc in json) {
			select.append("<option value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+"</option>");
			loop++;
		}
	}

	if(loop == 0){
		select.append("<option value=\"0\" disabled=\"disabled\">Nenhum procedimento disponível</option>");
	}

	fecharMensagemSemOk("carregando-conis");

}

/**
 * @return Array
 */
function getProcedimentosSelecionados(){
	var selecionados = [];

	$("#procedimentos-selecionados option").each(function(){
		selecionados.push($(this).val());
	});

	return selecionados;
}

function ttmarcarDia(){
	//console.log("here");
	// if($(this).hasClass("sem-vaga"))
	// 	return;

	
		//var coni = $("[data-index='1']").data("coni");
		var coni = $("#procedimentos-selecionados").val();		
		//var coni = 2074;
	//	var data = $("[data-index='1']").data("dia");
		// console.log($("#procedimentos-selecionados").val());
		// console.log(data);
		//$("[data-coni="+coni+"]").html("&nbsp;"); // limpa todos da mesma linha
		//$("#coni_"+coni).val($("#data").val()); // salva a data em um input hidden (está junto do nome do exame)
		if(coni != null){
			_cacheDataSelecionada[coni] = $("#data").val();
		} else {
			
		}
		console.log(coni);
		console.log(_cacheDataSelecionada);
		console.log(getProcedimentosSelecionados());
//		$(this).html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
		fecharMensagemSemOk("salvando-proc");
		//console.log(_cacheDataSelecionada);
		//habilitarOuNaoBotaoSalvar();
}

function habilitarOuNaoBotaoSalvar(){
	var tudo_ok = true;

	// verificar se há paciente seleciondo:
	if($("#usu_codigo").val() == ""){
		console.log("here1");
		//mensagem("Atenção","Selecione o paciente!", 250, 120);
		//$("#usu_nome").select();
		tudo_ok = false;
	}

	// verificar se há médico solicitante seleciondo:
	if($("#usr_codigo").val() == ""){
		console.log("here2");
		//mensagem("Atenção","Informe o médico solicitante!", 250, 120);
		//$("#usr_codigo").select();
		tudo_ok = false;
	}

	// verificar se há procedimentos selecionados:
	var sel = getProcedimentosSelecionados();
	if(sel.length == 1 && sel[0] == 0){
		console.log("here3");
		//mensagem("Atenção","Selecione algum exame!", 250, 120);
		//$("#med_nome").select();
		tudo_ok = false;
	}

	// verifica se há data selecionada para todos os exames escolhidos
	$(".hidden-coni").each(function(){
		if($(this).val() == ""){
			console.log($(".hidden-coni").val());
			console.log("here4");
			//var exame = $(this).parents("th").html().replace(/(<([^>]+)>)/ig,"");
			//alert(exame);
			tudo_ok = false;
		}
	});
	console.log(tudo_ok);
	if(tudo_ok)
		$(".salvar").removeClass("ui-state-disabled");
	else
		$(".salvar").addClass("ui-state-disabled");
}

// function habilitarOuNaoBotaoAtualizarData(){
// 	var selecionados = getProcedimentosSelecionados();
// 	if(selecionados.length){
// 		$("#atualizar-grid").removeClass("ui-state-disabled");
// 	} else {
// 		$("#atualizar-grid").addClass("ui-state-disabled");
// 	}
// }
