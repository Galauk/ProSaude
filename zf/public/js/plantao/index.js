var _cacheDataSelecionada = [];
var _examesDoAtendimento = [];
$(function(){

	$("#salvar-agenda").ajaxForm(afterSubmit);

	$("#unidade").buscar({
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

});

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
            carregarCalendario();
        }
    });
}

function salvarEscala(){
    if($(".salvar").hasClass("ui-state-disabled") === false)
        mensagemSemOk("salvando-age", "Salvando...", "Salvando Escala ...", 280, 80);
}

function afterSubmit(json){
	fecharMensagemSemOk("salvando-age");

	if(!json.success){
                mensagem(json.titulo,json.mensagem, 300, 150);
		return;
	} else {
		$("body").append("<div id=\"mensagem-dialog\" title=\"Sucesso\">Plantão agendado com sucesso!<br />");
		$("#mensagem-dialog").dialog({
			modal: true,
			width: 280,
			height: 80,
			close: function(){
				window.location.href = baseUrl + "/plantao/index/";
				$(this).remove();
			}
		});
	}
}

function carregarCalendario(){
	// desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled");
	$("#horario").hide("fast");
	var selecionado = $("#usr_codigo").val();
	if(selecionado.length == 1 && selecionado[0] === 0){
            $("#calendario").html("<em>Selecione algum Profissional</em>");
            return;
	}

	$("#calendario").html(imgCarregando());
	var url = baseUrl + "/plantao/plantao/selecionar-data/prof/"+selecionado+"/de/"+brToSql($("#data").val());
    $("#calendario").load(url, bindCalendario);
}

function carregarHorario(){
	// desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled");

	var selecionado = $("#usr_codigo").val();
    var conv_codigo = $("#conv_codigo").val();
    var data_selecionada = $(".hidden-coni").val();

	if(selecionado.length == 1 && selecionado[0] === 0){
		//$("#horario").html("<em>Selecione algum Profissional</em>");
		return;
	}

	$("#horario").html(imgCarregando());

	var url = baseUrl + "/plantao/plantao/selecionar-horario/prof/"+selecionado+"/ds/"+brToSql(data_selecionada)+"/conv_codigo/"+conv_codigo;
	$("#horario").load(url, bindHorario);
}

/**
 * Adicionar eventos no grid
 */

function bindHorario(){

	$("#grade tr td[data-hora]").hover(function(){
		var data = $(this).data("hora");
		$("td[data-hora="+data+"]").addClass("destaque");

	}, function(){
		var data = $(this).data("hora");
		$("td[data-hora="+data+"]").removeClass("destaque");

	})
	.click(marcarHora)
	.disableSelection();

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
	});

	$("#grade tr td[data-dia]").hover(function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").addClass("destaque");

	}, function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").removeClass("destaque");

	})
	.click(marcarDia)
	.disableSelection();

	$(".com-vaga,.sem-vaga").each(function(){
		var obj = $(this);
		var index = obj.data("index");
		var dow = $("[data-dow][data-index='"+index+"']").data("dow");
		var semana = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"];
		var proc_nome = obj.parents("tr").find("th").html();

		var vagas = obj.data("vagas");
		vagas = (vagas==-1)?"&infin; ilimitadas":vagas;

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

	$.each(_cacheDataSelecionada,function(usr_codigo,data){
		if(usr_codigo){
			$("#coni_"+usr_codigo).val(data);
			var dataSql = brToSql(data);
			$("[data-dia='"+dataSql+"'][data-coni='"+usr_codigo+"']").html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
		}
	});

	habilitarOuNaoBotaoSalvar();
}

function marcarDia(){
        $("#horario").show("fast");
	if($(this).hasClass("sem-vaga"))
		return;

	if($(this).hasClass("com-vaga")){
		var coni = $(this).data("coni");
		var data = $(this).data("dia");

		$("[data-coni="+coni+"]").html("&nbsp;"); // limpa todos da mesma linha
		$("#coni_"+coni).val(dataToBr(data)); // salva a data em um input hidden (está junto do nome do exame)
                $("#escpla_data").val(data);
		_cacheDataSelecionada[coni] = dataToBr(data);
		$(this).html("<img src=\""+baseUrl+"/public/images/icons/accept.png\" width=\"12px\" />");
        var usr_codigo = $("#usr_codigo").val();
        carregarHorario();
	}

	habilitarOuNaoBotaoSalvar();
}

function habilitarOuNaoBotaoSalvar(){
	var tudo_ok = true;

	// verificar se há médico solicitante seleciondo:
	if($("#usr_codigo").val() === ""){
		tudo_ok = false;
	}

	// verifica se há data selecionada para todos os exames escolhidos
	$(".hidden-coni").each(function(){
		if($(this).val() === "" || $(this).val() == "1"){
			tudo_ok = false;
		}
	});

    if($(".marcada").html() === null){
        tudo_ok = false;
    }

	if(tudo_ok){
        $(".salvar").removeClass("ui-state-disabled");
    }else{
        $(".salvar").addClass("ui-state-disabled");
    }
}

function marcarHora(){
	if($(this).hasClass("sem-vaga"))
		return;

	if($(this).hasClass("com-vaga")){
		var coni = $(this).data("coni");
		var data = $(this).data("hora");

		$("[data-coni="+coni+"]").html("&nbsp;"); // limpa todos da mesma linha
		$("#coni_"+coni).val(dataToBr(data)); // salva a data em um input hidden (está junto do nome do exame)
		_cacheDataSelecionada[coni] = dataToBr(data);

        $(".hora_per").each(function(i){
            $(this).html($(this).data("hora").replace(/_/g, ':'));
            $(this).addClass("com-vaga");
            $(this).removeClass("marcada");
        });

        data = data.replace(/_/g, ':');
		$(this).html(data);
        $(this).addClass("marcada");
        $(this).removeClass("com-vaga");
        $("#age_horario").val(data);
        var horarios = data.split(":");
        var hora_inicio = horarios[0]+":"+horarios[1];
        var hora_fim = horarios[2]+":"+horarios[3];
        $("#hora_inicio").val(hora_inicio);
        $("#hora_fim").val(hora_fim);
	}
	habilitarOuNaoBotaoSalvar();
}
