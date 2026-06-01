let _cacheDataSelecionada = [];
let _examesDoAtendimento = [];

$(function(){
	$("#proc_nome").keyup(function() {
		let filter = $(this).val();

		$("#procedimentos option").each(function() {
            let match = $(this).text().search(new RegExp(filter, "i"));
			//alert(match);
			if (match < 0 && $(this).text() != "--select--")  {                   
				$(this).attr("hidden", true)
			} else {
                $(this).removeAttr("hidden")
            }
		})
	})
        
	// Salvar por ajax
	$("#salvar-agenda").ajaxForm(afterSubmit)
	
	$(".salvar").click(function(){
		if($(this).hasClass("ui-state-disabled")){
			return
        }
		mensagemSemOk("salvando-age", "Aguarde", "Salvando agendamento...", 280, 80)
	})
	
	// buscar atendimento
	$("#buscar-atendimento").bind("click", buscarAtendimento)
	
	$("#usu_nome").buscar({
		url: baseUrl+'/paciente/buscar',
		callback: carregarHistoricoDoPaciente
	})
	
	$("#procedimentos").bind('dblclick', selecionarProcedimento).bind('keydown', selecionarProcedimento)
	
	$("#procedimentos-selecionados").bind('dblclick', deselecionarProcedimento).bind('keydown', deselecionarProcedimento)
	
	$("#atualizar-grid").click(carregarCalendario)
	
	$("#usr_nome").buscar({
		url: baseUrl+'/default/usuarios/buscar/externo/1',
		categoria: 'categoria',
		//suffix: '_solicitante',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul)
		},
		callback: function(){
			return true
		}
	})
	
	$("#med_nome").buscar({
		url: baseUrl+'/agenda/convenio/buscar/',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul)
		},
		callback: carregarItensDoLocal
	})
        
	$("#unidade_nome").buscar({
        url: baseUrl + "/unidade/buscar",
        minLength: 3,
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul)
        }
    })
})


function carregarHistoricoDoPaciente(){
	fecharMensagemSemOk("excluindo-agei")
	habilitarOuNaoBotaoSalvar()
	
	$("#em-historico").html( "<img class=\"loading\" src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" />" )
	
	let url = baseUrl+"/agenda/agenda/historico/usu/"+$("#usu_codigo").val()
	$("#historico").load(url, onHistoricoLoad)
}

function onHistoricoLoad(){	
	// verifica se atendimento já foi usando
	let ate_codigo = $("#ate_codigo").val()
	if(ate_codigo){
		let usado = $("tr[data-ate_codigo='"+ate_codigo+"'] td").addClass("ui-state-highlight").size()
		
		if(usado){
			mensagem("Atenção:","Este código de atendimento já foi usado.<br /><br />Os itens deste agendamento estão destacados no histórico abaixo.", 400, 180)
		}
	}
	
	megaBind("#historico")
	
	// bind do excluir
	$("#historico .excluir").bind("click", function(){
		let agei_codigo = $(this).data("agei")
		
		confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function(){
			excluirAgei(agei_codigo)
		})
	})
	
	$("#em-historico").html("") // tira img loading
	$("#historico").show("normal")
}

function excluirAgei(agei_codigo){
	mensagemSemOk("excluindo-agei", "Excluindo...", "Excluindo exame...", 280, 80)
	
	$.ajax({
		url: baseUrl+"/agenda/agenda/excluir/",
		type: "POST",
		data: {
			agei_codigo: agei_codigo
		},
		success: carregarHistoricoDoPaciente
	})
}

// modal
function buscarAtendimento(){
	let html = "<form id=\"buscar-atedimento-form\">";
	html += "<label>Código do atendimento:</label> ";
	html += "<input id=\"ate_codigo_temp\" /> ";
	html += "</form>";
	
	$("body").append("<div id=\"buscar-atedimento-dialog\" title=\"Buscar Atendimento\" />");
	$("#buscar-atedimento-dialog")
	.html(html)
	.dialog({
		modal: true,
		width: 520,
		height: 120,
		close: function(){
			$(this).remove()
		},
		buttons: {
			Ok: function(){
				carregarPeloAtendimento()
				$(this).dialog('close')
			}
		}
	})
	
	$("#buscar-atedimento-form").submit(function(e){
		e.preventDefault()
		carregarPeloAtendimento()
		$("#buscar-atedimento-dialog").dialog('close')
    })
    
	megaBind("#buscar-atedimento-dialog")
}

function carregarPeloAtendimento(){
	let ate_codigo = $("#ate_codigo_temp").val()
	if(!ate_codigo){
		return
    }
		
	mensagemSemOk("buscando-atendimento", "Buscando...", "Buscando dados do atendimeto", 280, 80)
	$.ajax({
		url: baseUrl+'/prontuario/atendimento/json/ate/'+ate_codigo,
		dataType: 'json',
		success: function(json){
			fecharMensagemSemOk("buscando-atendimento")
			tratarRetornoDoAtendimento(json)
		}
	});
}

function tratarRetornoDoAtendimento(json){
	if(!json.success){
		mensagem("Erro!",json.mensagem, 250, 130)
		return
	}	
	
	for ( var i in json) {
		$("#" + i).val(json[i])
	}
			
	// sempre que achar, interno será 1, pois só há atendimento realizado por usr e nunca med
	$("#interno").val(1)
			
	// limpar cache
	_examesDoAtendimento = []
			
	// salvar os exames em cache*
	for(var i in json.exames){
		_examesDoAtendimento.push(json.exames[i].proc_codigo)
	}
			
	// foco no local
	$("#med_nome").select()
	carregarHistoricoDoPaciente()
			
}

function carregarItensDoLocal(){
	mensagemSemOk("carregando-conis", "Aguarde", "Carregando lista de exames...", 280, 80)
	$("#procedimentos-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum exame selecionado</option>')
	var conv_codigo = $("#conv_codigo").val()
	$.ajax({
		url: baseUrl+'/agenda/convenio-itens/procedimentos-ajax',
		type: "POST",
		data: {
			conv_codigo: conv_codigo
		},
		success: function(json){
			listarProcedimento(json)
		}
	});
	
	$('#escondida').show()
	buscaProc(conv_codigo)
}

function buscaProc(conv_codigo){
        /*$(function(){
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
        });*/
}


function afterSubmit(json){	
	fecharMensagemSemOk("salvando-age")
	
	if(!json.success){
		if(json.code == 1 || json.code == 2){
			popupLogin()
        } else {
			mensagem(json.titulo,json.mensagem, 300, 150)
        }
        
        return
	} else {
		$("body").append("<div id=\"mensagem-dialog\" title=\"Sucesso\">Exame(s) agendado com sucesso!<br /><br />Deseja imprimir a guia de agendamento?</div>")
		$("#mensagem-dialog").dialog({
			modal: true,
			width: 290,
			height: 180,
			close: function(){
				window.location.href = baseUrl + "/agenda/agenda/novo"
				$(this).remove()
			},
			buttons: {
				Sim: function(){
					// imprimir
					popup(baseUrl+"/agenda/agenda/imprimir/age/"+json.age_codigo,"imprimir-agenda",600,500)
					$(this).dialog('close')
				},
				"Não": function(){
					// não imprimir
					$(this).dialog('close')
				}
			}
		})
	}
}

function carregarCalendario(){
	// desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled")
	
	var selecionados = getProcedimentosSelecionados()
	
	if(selecionados.length == 1 && selecionados[0] == 0){
		$("#calendario").html("<em>Selecione algum exame</em>")
		return
	}
	
	$("#calendario").html(imgCarregando())
	
	var url = baseUrl + "/agenda/agenda/selecionar-data-novo/procs/"+selecionados+"/de/"+brToSql($("#data").val())
	$("#calendario").load(url, bindCalendario)
}

/**
 * Adicionar eventos no grid
 */
function bindCalendario(){
	$("#grade tr th").slice(1,2).html("<div style='width:80px;'>Valor</div>")
	$("#grade tr th").slice(2,3).html("Data")
	
	$("[data-dow='0'],[data-dow='6']").each(function(){// cada domingo e sábado
		var index = $(this).data("index")
		var dow   = $(this).data("dow")
		
		$("[data-index='"+index+"']:not(.com-vaga)").addClass("dow"+dow)
	})
	
	$("#grade tr td[data-dia]").hover(function(){
		var data = $(this).data("dia")
		$("td[data-dia="+data+"]").addClass("destaque")
		
	}, function(){
		var data = $(this).data("dia")
		$("td[data-dia="+data+"]").removeClass("destaque")
	})
	.click(marcarDia)
	.disableSelection()
	
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
	
	carregaValoresAntigos();

}

function carregaValoresAntigos(){
	$.each(_cacheDataSelecionada,function(coni_codigo,data){
		if(coni_codigo){
			$("#coni_"+coni_codigo).val(data);
			var dataSql = brToSql(data);
			$("[data-dia='"+dataSql+"'][data-coni='"+coni_codigo+"']").html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
		}
	});
	
	habilitarOuNaoBotaoSalvar()
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
	
	habilitarOuNaoBotaoAtualizarData();
	carregarCalendario();
}

function deselecionarProcedimento(e){
	
	// só pode ser a tecla 39 (seta para esquerda)
	if(e.keyCode && e.keyCode != 37 || e.charCode)
		return;
	
	// remover
	$("#procedimentos-selecionados option:selected").appendTo("#procedimentos");
	
	// se não houver mais opções, add "Nenhum"
	if($("#procedimentos-selecionados option").size() == 0){
		$("#procedimentos-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum exame selecionado</option>');
	}
	
	habilitarOuNaoBotaoAtualizarData();
	carregarCalendario();
}

function listarProcedimento(json){	
	var select = $("#procedimentos").empty();
	var loop = 0;

	if(_examesDoAtendimento.length){ // existe uma lista de exames pré-selecionada, pelo código do atendimento
		for (var proc in json) {		
			if( _examesDoAtendimento.indexOf(json[proc].proc_codigo) > -1){
				select.append("<option value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+"</option>");
			} else {
				select.append("<option disabled=\"disabled\" value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+"</option>");
			}		
			loop++;		
		}
	} else {		
		for (var proc in json) {
            if (json[proc].proc_apelido){
			    select.append("<option value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+" ("+json[proc].proc_apelido+")</option>");			
			    loop++
            } else {
			    select.append("<option value=\""+json[proc].coni_codigo+"\" title=\""+json[proc].proc_nome+"\">"+json[proc].proc_nome+"</option>");			
			    loop++;
            }
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

function marcarDia(){
	if($(this).hasClass("sem-vaga"))
		return;
	
	if($(this).hasClass("com-vaga")){
		var coni = $(this).data("coni");
		var data = $(this).data("dia");
		
		$("[data-coni="+coni+"]").html("&nbsp;"); // limpa todos da mesma linha
		$("#coni_"+coni).val(dataToBr(data)); // salva a data em um input hidden (está junto do nome do exame)
		_cacheDataSelecionada[coni] = dataToBr(data);
		
		$(this).html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");		
	}	

	habilitarOuNaoBotaoSalvar();
}

function habilitarOuNaoBotaoSalvar(){
	var tudo_ok = true;
	
	// verificar se há paciente seleciondo:
	if($("#usu_codigo").val() == ""){
		//mensagem("Atenção","Selecione o paciente!", 250, 120);
		//$("#usu_nome").select();
		tudo_ok = false;
	}
	
	// verificar se há médico solicitante seleciondo:
	if($("#usr_codigo").val() == ""){
		//mensagem("Atenção","Informe o médico solicitante!", 250, 120);
		//$("#usr_codigo").select();
		tudo_ok = false;
	}
	
	// verificar se há procedimentos selecionados:
	var sel = getProcedimentosSelecionados();
	if(sel.length == 1 && sel[0] == 0){
		//mensagem("Atenção","Selecione algum exame!", 250, 120);
		//$("#med_nome").select();
		tudo_ok = false;
	}
	
	// verifica se há data selecionada para todos os exames escolhidos
	$(".hidden-coni").each(function(){
		if($(this).val() == ""){
			//var exame = $(this).parents("th").html().replace(/(<([^>]+)>)/ig,"");
			//alert(exame);
			tudo_ok = false;
		}
	});
	
	if(tudo_ok)
		$(".salvar").removeClass("ui-state-disabled");
	else 
		$(".salvar").addClass("ui-state-disabled");
}

function habilitarOuNaoBotaoAtualizarData(){
	var selecionados = getProcedimentosSelecionados();
	if(selecionados.length){
		$("#atualizar-grid").removeClass("ui-state-disabled");
	} else {
		$("#atualizar-grid").addClass("ui-state-disabled");
	}
}