$(function(){
        $(".finalizar").click(function(){
                var age_codigo = $("#age_codigo").val();
                
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
                                        url:baseUrl + "/prontuario/ficha/finalizar",
                                        data: {age_codigo : age_codigo},
                                        type:"GET",
                                        success:function(){
                                            $("#finalizar-dialog")
                                                .html(imgCarregando())
                                            $(this).dialog('close');
                                            location.href = baseUrl+"/guia-diagnostico";
                                        }
                                    });
				},
                                Cancelar: function(){
					$(this).dialog('close');
				}
			}
		});
        });
        
	$("#proc_nome").buscar({
		url: baseUrl+'/procedimento/buscar/esp/'+$("#esp_codigo").val()+'/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
                    url = baseUrl + "/prontuario/cid/procedimento/id/"+$("#proc_codigo").val();
                    $("#cid")
                    .attr("disabled","disabled")
                    .html("<option value=\"0\">Carregando...</option>")
                    .load(url, function(r){
                            if(r == "")
                                    $(this).html("<option value=\"0\">Nenhum CID relacionado</option>");
                            else
                                    $("#cid").removeAttr("disabled").focus();
                    });
                    return true;
		}
	});
        
        $("#pro_nome").each(function(){
		var rel = $(this).attr("rel");
		$(this).buscar({			
			url: baseUrl+"/produto/" + rel,
			template : function(ul, item) {
				return jQuery("<li></li>").data("item.autocomplete", item).append(
					"<a>" + item.label + "</a>").appendTo(ul);
			},
			callback: function(event, ui){
				//alert(url);
				if( ui.item.id > 0){
					$("#pro_codigo-"+rel).val(ui.item.data['pro_codigo']);
					$("#quantidade-"+rel).select();
					return true;
				}
				
				return false;
			}
		});
	});
	// pega o click no +
	$(".add").click(function(){
		
		var tipo = $(this).data("tipo");
		
		switch (tipo) {
			case "alerta":
				$("#form-alerta").slideToggle();
				break;
			case "pre-consulta":
				$("#form-pc").slideToggle();
				break;
			case "atendimento":
				$("#form-ate").slideToggle();
				break;
			case "procedimentos":
				mostrarFormProcedimento();
				break;
                        case "medicamentos":
				mostrarFormReceita();
				break;
			default:
				alert(tipo+" é desconhecido");
				break;
		}		
	});
        /* Alerta */
	formBind("#form-alerta", "Salvando alerta", alertaAddFromJson, true);
	/* Pré-Colsunta */
	formBind("#form-pc", "Salvando pré-consulta", pcAddFromJson, true);
	
	/* Atendimento */
	formBind("#form-ate", "Salvando atendimento", ateAddFromJson, false);
	
	/* Procedimentos */
	formBind("#form-proc", "Salvando procedimento", procAddFromJson, true);
        
        /*Receita*/
        formBind("#form-rec", "Salvando Receita", recAddFromJson, false);
	// o reset do procedimento precisa limpar o CID.
	$("#form-proc").bind("reset",function(){
		$("#form-proc #cid")
		.html("<option value=\"0\">-- Selecione um procedimento --</option>")
		.attr("disabled","disabled");
	})
	
	/* Cid do atendimento */
	$("#cd10_descricao").buscar({
		url: baseUrl + '/prontuario/cid/buscar/',
		template : function(ul, item) {
			return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(event, ui){
			return true;
		}
	});
	
	// preenche o select do CID (do procedimento), baseado no procedimento informado
	$("#proc_codigo").change(function(){
		url = baseUrl + "/prontuario/cid/procedimento/id/"+$(this).val();
		$("#cid")
		.attr("disabled","disabled")
		.html("<option value=\"0\">Carregando...</option>")
		.load(url, function(r){
			if(r == "")
				$(this).html("<option value=\"0\">Nenhum CID relacionado</option>");
			else
				$("#cid").removeAttr("disabled").focus();
		});
	});
        
        $("#data_consulta").change(function(){
            var data_retroativa = $(this).val();
            $(".data_retroativa").val(data_retroativa);
        });
	
});

function formBind(seletor,msg,callback, reset){
	$(seletor).hide()
	.bind("reset",function(){
		$(this).slideToggle()
	}).bind('submit', function(e) {
		e.preventDefault(); 
		mensagemSemOk("form-add", "Salvando...", msg, 330, 100);
		$(this).ajaxSubmit({
			success: function(json){
				fecharMensagemSemOk("form-add");
				if(json.error){
					mensagem("Erro!",json.mensagem,330,150);
					return;
				} 
				if(reset)
					$(seletor).trigger("reset");
				else
					$(seletor).slideToggle();
				
				callback(json);
			}
		});
	});
}


/* Alertas */
function alertaAdd(obj){
	/*obj
	.parents("h2")
	.after("<span><input id=\"addAlerta\" onblur=\"alertaBlur()\" onkeypress=\"alertaKeyPress(event)\" style=\"width: 300px\" /><br /></span>");
	$("input#addAlerta").select();	*/
}

function alertaKeyPress(e){
	if(e.keyCode == 13)
		return alertaBlur();
}

function alertaBlur(){
	var input = $("input#addAlerta");
	var alerta = input.val();
	
	if($.trim(alerta) == ""){
		input.parents("span").remove();
		return;
	}
	
	span = input.parents("span").html(imgCarregando()+"<br />");
	
	$.ajax({
		url: baseUrl+'/prontuario/alerta/salvar',
		type: 'post',
		dataType: 'json',
		data: {
			usu_codigo: getUsuCodigo(),
			ale_desc: alerta,
			json: true
		},
		success: function(json){
			var img = '<img src="'+baseUrl+'/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="alertaExcluir('+json.ale_codigo+')" /> ';
			span
			.html(img + json.ale_desc +"<br />" )
			.attr("id","ale_"+json.ale_codigo);
			
			escondeEm("alerta");
			destacar("#ale_"+json.ale_codigo);
		}
	});
}

function alertaExcluir(id){
	confirme("Confirme","Deseja realmente excluir este alerta?",300,120, function(){
		$("#ale_"+id+" br").before('<img src="'+baseUrl+'/public/images/loading.gif" class="loading" style="width:12px" />');
		
		$.ajax({
			url: baseUrl+'/prontuario/alerta/excluir',
			type: 'get',
			data:{
				id: id,
				json: true
			},
			success: function(){
                            $(".line_"+id).remove();
                            if($("table#table-alerta tr.linha_registro_ale").length < 1){
                                $("table#table-alerta tr.titulo").remove();
                                $("table#table-alerta").html("<em class=\"em-alerta\">Nenhum Alerta encontrado!");
                            }
			}
		});
	});
}


/*Alerta*/

function alertaAddFromJson(json){
        var tr = "";
        if($("table#table-alerta tr.titulo").length >= 1){
            $(".titulo").remove();
        }
        tr += "<tr class=\"titulo\"><th colspan=\"2\">Alerta</th></tr>";
	tr += "<tr class=\"line_"+json.ale_codigo+" linha_registro_ale\"><td width=\"30\"><img src=\""+baseUrl+"/public/images/icons/excluir.png \" alt=\"Excluir\" title=\"Excluir\" onclick=\"alertaExcluir("+json.ale_codigo+")\" />  </td>";
        tr += "<td nowrap>"+json.ale_desc+"</td></tr>";
	$("#table-alerta").prepend(tr);
        $(".em-alerta").remove();
}
//<img src="<?= $this->baseUrl("/public/images/icons/excluir.png"); ?>" alt="Excluir" title="Excluir" onclick="alertaExcluir(<?php echo $item->ale_codigo; ?>)" />
/* Pré-Consultas */

function pcExcluir(id){
	confirme("Confirme","Deseja realmente excluir esta pré-consulta?",300,120, function(){
		$("#ale_"+id+" br").before('<img src="'+baseUrl+'/public/images/loading.gif" class="loading" style="width:12px" />');
		
		$.ajax({
			url: baseUrl+'/prontuario/pre-consulta/excluir',
			type: 'get',
			data:{
				id: id,
				json: true
			},
			success: function(){
                            $(".line_pc_"+id).remove();
                            if($("table#table-pc tr.linha_registro_pc").length < 1){
                                $("table#table-pc tr.titulo_pc").remove();
                                $("table#table-pc").html("<em class=\"em-pc\">Nenhuma Pré-Consulta encontrada!");
                            }

			}
		});
	});
}

function pcAddFromJson(json){
        if($("table#table-pc tr.titulo_pc").length >= 1){
            $(".titulo_pc").remove();
        }
        
	var tr = "<tr class=\"titulo_pc\"><th></th><th>Data</th><th>Unidade De Saúde</th><th>Profissional</th><th>Temp.</th><th>Peso</th><th>Alt.</th><th>P/A</th><th>IMC</th></tr>";
        tr += "<tr class=\"line_pc_"+json.pc_codigo+" linha_registro_pc\"><td width=\"30\"><img src=\""+baseUrl+"/public/images/icons/excluir.png \" alt=\"Excluir\" title=\"Excluir\" onclick=\"pcExcluir("+json.pc_codigo+")\" />  </td>";
        if(json.age_data == null){
            json.age_data = "---";
        }
	tr += "<td>"+json.pc_data+"</td>";
        

	tr += "<td nowrap>"+json.uni_desc+"</td>";
	tr += "<td>"+json.usr_nome+"</td>";
        if(json.pc_temperatura == null){
            json.pc_temperatura = "---";
        }
	tr += "<td>"+json.pc_temperatura+"</td>";
        if(json.pc_peso == null){
            json.pc_peso = "---";
        }
	tr += "<td>"+json.pc_peso+"</td>";
        if(json.pc_altura == null){
            json.pc_altura = "---";
        }
	tr += "<td>"+json.pc_altura+"</td>";
        if(json.pc_pressao_sistolica == null && json.pc_pressao_diastolica == null){
            json.pc_pressao_diastolica = "---";
            json.pc_pressao_sistolica = "---";
        }
	tr += "<td>"+json.pc_pressao_sistolica+"/"+json.pc_pressao_diastolica+"</td>";
	
        if(json.pc_peso == null || json.pc_altura == null){
            tr += "<td>---</td></tr>";
        }else{
            tr += "<td>"+imc(json.pc_peso, json.pc_altura)+"</td></tr>";
        }
	
	$(".em-pc").remove();
	$("#table-pc").prepend(tr);
}

/* Atendimentos */

function ateExcluir(id){
	confirme("Confirme","Deseja realmente excluir?",300,120, function(){
		$("#ate_"+id+" br").before('<img src="'+baseUrl+'/public/images/loading.gif" class="loading" style="width:12px" />');
		
		$.ajax({
			url: baseUrl+'/prontuario/atendimento/excluir',
			type: 'get',
			data:{
				id: id,
				json: true
			},
			success: function(){
                            $(".line_ate_"+id).remove();
                            $(".ate_codigo").val("");
                            if($("table#table-ate tr.linha_registro").length < 1){
                                $("table#table-ate tr.titulo_ate").remove();
                                $("table#table-ate").html("<em class=\"em-ate\">Nenhuma Consulta encontrada!");
                            }

			}
		});
	});
}
function ateAddFromJson(json){
        if($("table#table-ate tr.titulo_ate").length >= 1){
            $(".titulo_ate").remove();
        }
        
	var tr = "<tr class=\"titulo_ate\"><th></th><th>Data</th><th>Unidade De Saúde</th><th>Profissional</th><th>Especialidade</th></tr>";
        tr += "<tr class=\"line_ate_"+json.ate_codigo+" linha_registro\"><td width=\"30\"><img src=\""+baseUrl+"/public/images/icons/excluir.png \" alt=\"Excluir\" title=\"Excluir\" onclick=\"ateExcluir("+json.ate_codigo+")\" />  </td>";
	tr += "<td>"+dataToBr(json.ate_data)+"</td>";
	tr += "<td>"+json.uni_desc+"</td>";
	tr += "<td>"+json.usr_nome+"</td>";
	tr += "<td>"+json.esp_nome+"</td></tr><tr class=\"line_ate_"+json.ate_codigo+"\">";
	tr += "<td colspan=\"8\"><strong>CID</strong> "+(json.cd10_codigo_cid ? json.cd10_codigo_cid + " - " + json.cd10_descricao:"<em>Não informado</em>")+"</td>";
	tr += "</tr><tr class=\"line_ate_"+json.ate_codigo+"\"><td colspan=\"8\" class=\"escape\">";
	if(json.ate_reclamacao)
		tr += json.ate_reclamacao;
		
	if(json.ate_exame_fisico)
		tr += json.ate_exame_fisico;
		
	if(json.ate_diagnostico)
		tr += json.ate_diagnostico;
		
	if(json.ate_tratamento)
		tr += json.ate_tratamento;
		
	if(json.ate_curativo)
		tr += json.ate_curativo;
		
		
	// insert ou update?
        tr += "</tr>";
	
	
	// forms que precisam do ate_codigo: proc
	$("input[name=ate_codigo]").val(json.ate_codigo);
        $(".ate_codigo").val(json.ate_codigo);
        $(".em-ate").remove();
	$("#table-ate").prepend(tr);
}

/*Receita*/
function recAddFromJson(json){
        if($("table#table-rec tr.titulo_rec").length >= 1){
            $(".titulo_rec").remove();
        }
	var tr = "<tr class=\"titulo_rec\"> <th width=\"20\">&nbsp;</th><th width=\"140\">Data</th><th>Produto</th><th width=\"60\">Quant.</th><th>Recomendação</th> </tr>";
        tr += "<tr class=\"line_rec_"+json.irec_codigo+" linha_registro_rec\"><td width=\"30\"><img src=\""+baseUrl+"/public/images/icons/excluir.png \" alt=\"Excluir\" title=\"Excluir\" onclick=\"irecExcluir("+json.irec_codigo+")\" />  </td>";
	tr += "<td>"+dataToBr(json.rec_data)+"</td>";
        tr += "<td>"+json.pro_nome+"</td>";
	tr += "<td>"+json.irec_quantidade+"</td>";
	tr += "<td>"+json.irec_recomendacao+"</td></tr><tr>";
	tr += "</tr><tr><td colspan=\"8\" class=\"escape\">";
	
        $(".em-rec").remove();
	$("#table-rec").prepend(tr);
}

function irecExcluir(id){
	confirme("Confirme","Deseja realmente excluir?",300,120, function(){
		$("#pat_"+id+" td:first").html('<img src="'+baseUrl+'/public/images/loading.gif" class="loading" />');
		
		$.ajax({
			url: baseUrl+'/prontuario/receita-medica/excluir',
			type: 'get',
			data:{
				id: id,
				json: true
			},
			success: function(){
                            $(".line_rec_"+id).remove();
                            if($("table#table-rec tr.linha_registro_rec").length < 1){
                                $("table#table-rec tr.titulo_rec").remove();
                                $("table#table-rec").html("<em class=\"em-rec\">Nenhum Item encontrado!");
                            }
                            
			}
		});
	});
	
}


/* Procedimentos */
function mostrarFormProcedimento(){
	var ate = $("#ate_codigo").val();
	/*if(ate == ""){
		mensagem("Erro!","Antes de realizar um procedimento é necessario fazer a consulta!", 330, 150);
		return;
	}*/
	
	$("#form-proc").slideToggle();
}

function mostrarFormReceita(){
	var ate = $("#ate_codigo").val();
        $(".rec").show();
	$("#form-rec").slideToggle();
}

function procAddFromJson(json){
        if($("table#table-proc tr.titulo_proc").length >= 1){
            $(".titulo_proc").remove();
        }
        var tr = "<tr class=\"titulo_proc\"><th>&nbsp;</th><th>Procedimento</th><th>CID</th><th>Profissional</th><th>Especialidade</th></tr>";
	tr += "<tr id=\"pat_"+json.pat_codigo+"\" class=\"line_proc_"+json.pat_codigo+" linha_registro_proc\">";
	tr += "<td><img class=\"a\" onclick=\"procExcluir("+json.pat_codigo+")\" src=\""+baseUrl+"/public/images/icons/excluir.png\" alt=\"Excluir\" title=\"Excluir\" /></td>";
	//tr += "<td>"+dataToBr(json.ate_data)+"</td>";
	tr += "<td>"+json.proc_nome+"</td>";
	tr += "<td>"+(json.cd10_descricao?json.cd10_descricao:"--")+"</td>";
	tr += "<td>"+json.usr_nome+"</td>"
	tr += "<td>"+json.esp_nome+"</td></tr>";
	$(".em-proc").remove();
	$("#table-proc").prepend(tr);
	destacar("#pat_"+json.pat_codigo);
}

function procExcluir(id){
	confirme("Confirme","Deseja realmente excluir este procedimento?",300,120, function(){
		$("#pat_"+id+" td:first").html('<img src="'+baseUrl+'/public/images/loading.gif" class="loading" />');
		
		$.ajax({
			url: baseUrl+'/prontuario/procedimento/excluir',
			type: 'get',
			data:{
				id: id,
				json: true
			},
			success: function(){
                            $(".line_proc_"+id).remove();
                            if($("table#table-proc tr.linha_registro_proc").length < 1){
                                $("table#table-proc tr.titulo_proc").remove();
                                $("table#table-proc").html("<em class=\"em-proc\">Nenhum Procedimento encontrado!");
                            }
			}
		});
	});
	
}

/* helpers */
function verificarEm(classe, filho, texto){
	if(!$("div."+classe+" "+filho).size()){
		$("div."+classe+" .space").before("<em>"+texto+"</em>").find("em").hide().slideDown("normal");
	}
}

function escondeEm(classe){
	$("div."+classe+" em").hide();
}

function getUsuCodigo(){
	return $("#usu_codigo").val();
}

function destacar(seletor){
	$(seletor).effect("highlight", {}, 1000);
}