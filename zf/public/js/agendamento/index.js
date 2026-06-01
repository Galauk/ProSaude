var _cacheDataSelecionada = [];
var _examesDoAtendimento = [];
$(function(){
        var coni_codigo = $("#coni_codigo").val();        
        var esp_codigo = $("#esp_codigo_sessao").val();
        
        if(esp_codigo != ""){
            //alert(esp_codigo);
            mostraEspecialidade();
        }
        
	$("#salvar-agenda").ajaxForm(afterSubmit);
	
	$("#med_nome").buscar({
		url: baseUrl+'/agenda/convenio/buscar/',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: mostraMedico
	});
        
    $("#data").change(function(){
        var usr_codigo = $("#usr_codigo").val();
        if(usr_codigo){
                $("#atualizar-grid").removeClass("ui-state-disabled");
        } else {
                $("#atualizar-grid").addClass("ui-state-disabled");
        }
    });
    
    $("#atualizar-grid").click(carregarCalendario);
    
    $("#esp").change(function(){
        var coni_codigo = $(this).val();
        $("#coni_codigo").val(coni_codigo);
        $.ajax({
            url: baseUrl+"/agendamento/agendamento/buscar-especialidade-por-coni/",
            type: "POST",
            data: {
                coni_codigo: coni_codigo
            },
            success: function(json){
                for (var i in json){                       
                    $("#esp_codigo_config").val(json[i]);
                }
            }
        });
        
        carregarCalendario();
    });
    
    $(".detalhes").click(function(){
        if($("#sem-tamanho").val() <= 4){
            $(".divRolagem").css("height","auto");
        }else{
            $(".divRolagem").css("height","250px");
        }
        $("#historico").show("normal"); 
    });
    
    $(".detalhes").click(function(){
        if($("#historico").hasClass("hide")){
            $("#historico").show("slow");
            $("#historico").removeClass("hide");
        }else{
            $("#historico").hide("slow");
            $("#historico").addClass("hide");
        }
    });
});

function carregarHistoricoDoPaciente(){
    $("#botao_historico").show("slow");
    $("#historico").addClass("hide");
	fecharMensagemSemOk("excluindo-agei");
	habilitarOuNaoBotaoSalvar();
	$("#em-historico").html( "<img class=\"loading\" src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" />" );
	var url = baseUrl+"/agendamento/agendamento/historico/usu/"+$("#usu_codigo").val();
	$("#historico").load(url, onHistoricoLoad);
}

function onHistoricoLoad(){	
	// verifica se atendimento já foi usando
	var ate_codigo = $("#ate_codigo").val();
	if(ate_codigo){
		var usado = $("tr[data-ate_codigo='"+ate_codigo+"'] td").addClass("ui-state-highlight").size();
		
		if(usado){
			mensagem("Atenção:","Este código de atendimento já foi usado.<br /><br />Os itens deste agendamento estão destacados no histórico abaixo.", 400, 180);
		}
	}
	megaBind("#historico");

	$("#em-historico").html(""); // tira img loading 
}

function excluirAgei(agei_codigo){
	mensagemSemOk("excluindo-agei", "Excluindo...", "Excluindo exame...", 280, 80);
	
	$.ajax({
		url: baseUrl+"/agenda/agenda/excluir/",
		type: "POST",
		data: {
			agei_codigo: agei_codigo
		},
		success: carregarHistoricoDoPaciente
	});
}

function mostraMedico(){
    $('#escondida').show();
    var conv_codigo = $("#conv_codigo").val();
    $("#usr_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-profissionais-saude/conv_codigo/'+conv_codigo,
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(txt){
            mostraEspecialidade();
            getTipoMedico();
            return true;
        }
    });
}

function getTipoMedico() {
    var usr_codigo = $("#usr_codigo").val();
    $.ajax({
        url: baseUrl+"/default/usuarios/get-tipo-usuario/",
        type: "POST",
        data: {
            usr_codigo: usr_codigo
        },
        success: function(txt){
            $("#usr_tipo_medico").val(txt);
        }
    });
}

function mostraTipoConsulta(){
    var usrTipoMedico = $("#usr_tipo_medico").val();
    var tipoAtendimento = $("#tat_codigo option:selected").val();
    if (usrTipoMedico=="D" && tipoAtendimento != "4") {
        // Se for tipo de atendimento urgência, remove consulta de retorno
        if (tipoAtendimento=="6") { $("#tp_consulta2").remove(); }
        $("#tipo_consulta").show();
    } else {
        $("#tipo_consulta").hide();
    }
}

function mostraEspecialidade(){
    var conv_codigo = $("#conv_codigo").val();
    var usr_codigo = $("#usr_codigo").val();
    var esp_codigo_sessao = $("#esp_codigo_sessao").val();
    var esp_codigo_selecionado = $("#esp_codigo_selecionado").val();
   // $("#tat_codigo option[value='']").attr('selected','selected');
    //$("#tat_codigo select").val("");
    if(esp_codigo_sessao != null || esp_codigo_sessao != "") {
        var selected = "selected=selected";
    } else {
        var selected = "";
    }

    $.ajax({
        url: baseUrl+'/agenda/convenio-itens/carrega-especialidade-por-convenio',
        type: "POST",
        data: {
                conv_codigo: conv_codigo,
                usr_codigo:usr_codigo
        },
        success: function(json){
            //$("#usr_tipo_medico").val(json[0].usr_tipo_medico);
            $("#esp").html("");
            $.each( json, function( key, value ) {
                var especialidade = value['esp_codigo'];
                $("#esp_codigo_config").val(especialidade)
                $("#esp").append("<option title=\""+value['esp_nome']+"\"  value=\""+value['coni_codigo']+"\" "+selected+">"+value['esp_nome']+"</option>");
                $("#med_esp").show('slow');
            });
            //$("#esp_codigo_config").val('asdasdsadsadsad');
            var coni_codigo = $('#esp').find('option').filter(':selected').val();
            $("#coni_codigo").val(coni_codigo);
            //alert(teste);
            if( $("#coni_codigo").val() == "" || $("#coni_codigo").val() == "" || !$("#coni_codigo").val()){
                $("#med_esp").show('slow');
                $("#med_esp").html("<em>O profissional selecionado não possui especialidade</em>");
                return false;
            }else{
                carregarCalendario();
                mostraTipoConsulta();
            }
        }
    });
}

function salvarAgendamento(){
    if($(".salvar").hasClass("ui-state-disabled") == false){
        mensagemSemOk("salvando-age", "Salvando...", "Salvando Agendamento ...", 280, 80);
    }
}

function afterSubmit(json){
	fecharMensagemSemOk("salvando-age");
	
	if(!json.success){
        mensagem(json.titulo,json.mensagem, 300, 150);
		return;
	} else {
		$("body").append("<div id=\"mensagem-dialog\" title=\"Sucesso\">Paciente agendado com sucesso!<br /><br />Deseja imprimir a guia de agendamento?</div>");
		$("#mensagem-dialog").dialog({
			modal: true,
			width: 290,
			height: 180,
			close: function(){
				window.location.href = baseUrl + "/agendamento/index/";
				$(this).remove();
			},
			buttons: {
				Sim: function(){
					// imprimir
                    var p_horario = "";
                    if($("#imprimir_primeiro_horario").val() == 1){
                        p_horario = $("#primeiro_horario").val();
                    }
					popup(baseUrl+"/agendamento/agendamento/imprimir/age/"+json.age_codigo+"/p_horario/"+p_horario,"imprimir-agenda",600,500);
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

function carregarCalendario(){
	// desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled");
	$("#horario").hide("fast");
	var selecionado = $("#usr_codigo").val();
        //var conv_codigo = $("#conv_codigo").val();
    var coni_codigo = $("#coni_codigo").val();
    
    if(selecionado.length == 1 && selecionado[0] == 0){
        $("#calendario").html("<em>Selecione algum Profissional</em>");
        return;
	}
	
	$("#calendario").html(imgCarregando());
	var url = baseUrl + "/agendamento/agendamento/selecionar-data/prof/"+selecionado+"/de/"+brToSql($("#data").val())+"/coni_codigo/"+coni_codigo;
    $("#calendario").load(url, bindCalendario);
}

function carregarHorario(){
	// desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled");
	
	var selecionado = $("#usr_codigo").val();
    var coni_codigo = $("#coni_codigo").val();
    var conv_codigo = $("#conv_codigo").val();
    var data_selecionada = $(".hidden-coni").val();

    // alert('teste:'.coni_codigo);

	if(selecionado.length == 1 && selecionado[0] == 0){
		//$("#horario").html("<em>Selecione algum Profissional</em>");
		return;
	}
	
	$("#horario").html(imgCarregando());
		
	var url = baseUrl + "/agendamento/agendamento/selecionar-horario/prof/"+selecionado+"/ds/"+brToSql(data_selecionada)+"/coni_codigo/"+coni_codigo+"/conv_codigo/"+conv_codigo;
	$("#horario").load(url, bindHorario);	
}

function carregarHorarioToBind(usu_codigo,coni_codigo,conv_codigo,data){
	// desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled");
	
	var selecionado = usu_codigo;
    var coni_codigo = coni_codigo;
    var conv_codigo = conv_codigo;
    var data_selecionada = data;

	if(selecionado.length == 1 && selecionado[0] == 0){
		//$("#horario").html("<em>Selecione algum Profissional</em>");
		return;
	}
	
	$("#horario").html(imgCarregando());
		
	var url = baseUrl + "/agendamento/agendamento/selecionar-horario/prof/"+selecionado+"/ds/"+brToSql(data_selecionada)+"/coni_codigo/"+coni_codigo+"/conv_codigo/"+conv_codigo;
	$("#horario").load(url, bindHorario);	
    $("#horario").show("fast");
}

function bindHorario(){
	$("#grade tr td[data-hora]").hover(function(){
		var data = $(this).data("hora");
		$("td[data-hora="+data+"]").addClass("destaque");
	}, function(){
		var data = $(this).data("hora");
		$("td[data-hora="+data+"]").removeClass("destaque");
	}).click(marcarHora).disableSelection();
	
	$(".sem-vaga").each(function(){
		var obj = $(this);
		var index = obj.data("index");
        var paciente = $(this).data("paciente");
        if($(this).hasClass("sem-vaga")){
            var html = "<div><strong>Pacientes: </strong>"+paciente+"<br />";
            obj.easyTooltip({
                    content: html
            });
        }       
	});
	carregaValoresAntigos();
}

function bindCalendario(){
	$("#grade tr th").slice(1,2).html("Selec.");
	
	$("[data-dow='0'],[data-dow='6']").each(function(){// cada domingo e sábado
		var index = $(this).data("index");
		var dow   = $(this).data("dow");
		
		$("[data-index='"+index+"']:not(.com-vaga)").addClass("dow"+dow);
	})
	
	$("#grade tr td[data-dia]").hover(function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").addClass("destaque");
		
	}, function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").removeClass("destaque");
		
	}).click(marcarDia).disableSelection();
	
	$(".com-vaga,.sem-vaga").each(function(){
		var obj = $(this);
		var index = obj.data("index");
		var dow = $("[data-dow][data-index='"+index+"']").data("dow");
		var semana = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"];
		var proc_nome = obj.parents("tr").find("th").html();
		
		var vagas = obj.data("vagas");
		vagas = (vagas==-1)?"&infin; ilimitadas":vagas;
                
        if(obj.data("dia")=="2017-11-14") {
            var usu_codigo = $("#usr_codigo").val();
            var coni_codigo = $("#coni_codigo").val();
            var conv_codigo = $("#conv_codigo").val();
            
            $(this).html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");  
            carregarHorarioToBind(usu_codigo,coni_codigo,conv_codigo)
        }		

        var html = "<div><strong>Profissional: </strong>"+proc_nome+"<br />";
            
        html += "<strong>Data: </strong>"+dataToBr(obj.data("dia"))+" ("+semana[dow].toLowerCase()+")<br />";
        html += "<strong>Vagas: </strong>"+vagas+"</div>";
                
        obj.easyTooltip({
            content: html
        });
	});        
	carregaValoresAntigos();
}

function carregaValoresAntigos(){
    for (var i in _cacheDataSelecionada){
		if(coni_codigo){
			$("#coni_"+coni_codigo).val(data);
			var dataSql = brToSql(data);
			$("[data-dia='"+dataSql+"'][data-coni='"+coni_codigo+"']").html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
		}
   }
	habilitarOuNaoBotaoSalvar()
}

function marcarDia(){
    $("#horario").show("fast");
	if($(this).hasClass("sem-vaga")){
	    return;
    }
	
	if($(this).hasClass("com-vaga")){
		var coni = $(this).data("coni");
		var data = $(this).data("dia");
		
		$("[data-coni="+coni+"]").html("&nbsp;"); // limpa todos da mesma linha
		$("#coni_"+coni).val(dataToBr(data)); // salva a data em um input hidden (está junto do nome do exame)
        $("#age_data").val(data);
		_cacheDataSelecionada[coni] = dataToBr(data);
		$(this).html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
        var coni_codigo = $("#coni_codigo_selecionado").val();
        carregarHorario();		
	}
	//habilitarOuNaoBotaoSalvar();
}

function habilitarOuNaoBotaoSalvar(){
	var tudo_ok = true;
	
	// verificar se há paciente seleciondo:
	if($("#usu_codigo").val() == ""){
		//mensagem("Atenção","Selecione o paciente!", 250, 120);
		//$("#usu_nome").select();
		tudo_ok = false;
	}
        
    if($("#tat_codigo").val() == ""){
        tudo_ok = false;
    }
	
	// verificar se há médico solicitante seleciondo:
	if($("#usr_codigo").val() == ""){
		//mensagem("Atenção","Informe o médico solicitante!", 250, 120);
		//$("#usr_codigo").select();
		tudo_ok = false;
	}
        
    if($("#conv_codigo").val() == ""){
		//mensagem("Atenção","Informe o médico solicitante!", 250, 120);
		//$("#usr_codigo").select();
		tudo_ok = false;
	}
        
	// verificar se há procedimentos selecionados:
	
	// verifica se há data selecionada para todos os exames escolhidos
	$(".hidden-coni").each(function(){
		if($(this).val() == "" || $(this).val() == "1"){
			//var exame = $(this).parents("th").html().replace(/(<([^>]+)>)/ig,"");
			tudo_ok = false;
		}
	});
        
    if($(".marcada").html() == null){
        tudo_ok = false;
    }
    
    if($("#tat_codigo").val() == "") {
        tudo_ok =  false;
    }
       
    if($("#usr_tipo_medico").val() == "D"){
        if($("#tp_cod").val() == "" && $("#tat_codigo").val() == 2) {
            tudo_ok =  false;
        }
    }
	
	if(tudo_ok){
        $(".salvar").removeClass("ui-state-disabled");
    }else{ 
        $(".salvar").addClass("ui-state-disabled");
    }
}

function getProcedimentosSelecionados(){
	var selecionados = [];
	
	$("#procedimentos-selecionados option").each(function(){
		selecionados.push($(this).val());
	});
	
	return selecionados;
}

function marcarHora(){
	if($(this).hasClass("sem-vaga")){
		return;
    }
            
    if($(this).hasClass("encaixe")){
        $("#horario_de_encaixe").val("S");
    }else{
        $("#horario_de_encaixe").val("N");
    }
	
	if($(this).hasClass("com-vaga")){
		var coni = $(this).data("coni");
		var data = $(this).data("hora");
		
		$("[data-coni="+coni+"]").html("&nbsp;"); // limpa todos da mesma linha
		$("#coni_"+coni).val(dataToBr(data)); // salva a data em um input hidden (está junto do nome do exame)
		_cacheDataSelecionada[coni] = dataToBr(data);
                
        $(".hora_per").each(function(i){
            $(this).html($(this).data("hora").replace("_", ":"));
            $(this).addClass("com-vaga");
            if($(this).hasClass("remover_encaixe")){
                $(this).addClass("encaixe");
            }
            $(this).removeClass("marcada");
        });
        
        data = data.replace("_", ":");
		$(this).html(data);
        $(this).addClass("marcada");
        $(this).removeClass("com-vaga");
        $(this).removeClass("encaixe");
        $("#age_horario").val(data);
	}
	habilitarOuNaoBotaoSalvar();
}

