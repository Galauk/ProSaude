$(function () {
    $('#tabs-1').css("padding", "0");
    $('.tabs').hide();
    $("#historico").click(function () {
        $("#historico-dialog").dialog({
            modal: true
        });
    });

    if($('#pep_sexo').val() != "F"){
        mensagem("Atenção.","Paciente do sexo masculino!", 300, 150, function () {
            location.replace(baseUrl+"/prontuario/index");
        });
    }


    checaPrimeiraConsulta();

    if ($("#pc_codigo").val() !== '') {
        $('#antropometria').find('input, textarea, select').attr('disabled', 'disabled');
        $('#sinais_vitais').find('input, textarea, select').attr('disabled', 'disabled');
        $('#glicemia').find('input, textarea, select').attr('disabled', 'disabled');
        $('#botoes_pre_consulta').hide();
        $('#botao_editar_pc').hide();
        $('#salva_pre_consulta').val("0");


    } else {
        $('#reavaliar').hide();
        $('#botao_editar_pc').hide();
        $('#cancela_pre_consulta').hide();
    }

    $("#dum").change(function () {
        calculaDPP();
    });

    if($("#proc_qtd_total").val() == 0){
        $('#botao_imprimir').hide();
    }

    tipoAtendimento();
    if ($("#usu_cartao_sus").val() == "") {
        alert("Para consultas de Pré-Natal/Puerpério é necessário informar o CNS do paciente.");
        atualizaCnsParticipante($('#usu_codigo').val(), null, null, 1);
    }

});

function atualizaIMC() {
    var peso = $("#peso").val();
    var altura = $("#altura").val();

    altura *= altura;

    if (peso && altura) {
        return $("#imc").val(Math.round(peso / altura * 100) / 100);
    }
}

function checaPrimeiraConsulta() {
    var tipo_consulta = $('#tipo_consulta:checked').val();
    var ultima_consulta = $('#ultima_consulta').val();
    var primeira_consulta = false;

    if (ultima_consulta == '' || ultima_consulta == 2) {
        if (tipo_consulta == 1) {
            primeira_consulta = true;
        } else {
            primeira_consulta = false
        }
    }
    if (primeira_consulta) {
        $('tr.primeiraconsulta').show();
        $('#atualize_historico').show();
        $('#label_dum').append("<span class='obrigatorio'>*</span>");
    } else {
        $('tr.primeiraconsulta').hide();
        $('#atualize_historico').hide();
        $('#label_dum').html("<abbr title='Data da última menstruação'>DUM</abbr>");
    }

}

function inserirGrupoProcedimento(gruex_codigo) {
  var date = new Date();
  var dia = (date.getDate() > 0 && date.getDate() < 10 ? '0' + date.getDate() : date.getDate());
  var dataSolicitacao = dia + '/' + date.getMonth() + '/' + date.getFullYear();

  $.ajax({
    url: baseUrl + "/laboratorio/grupo-de-exames/grupo-prenatal/gruex_codigo/" + gruex_codigo + "/inserir/1",
    type: "GET",
    success: function (txt) {
      for (var i = 0; i < txt.length; i++) {
        adicionaProcedimentoGrupos(txt[i]['proc_nome'], txt[i]['proc_codigo'], txt[i]['proc_codigo_sus'], dataSolicitacao);
      }
      return true;
    }
  });
}

function buscaProcedimentosSus() {
    $("#proc_nome").buscar({
        url: baseUrl + '/procedimento/buscar/',
        minLength: 3,
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            return true;
        }
    });
}

function adicionaProcedimentoGrupos(proc_nome, proc_codigo, proc_codigo_sus, dataSolicitacao) {

    var cont = parseInt($("#proc_qtd_total").val()) + 1;
    if (proc_codigo_sus == null) {
        proc_codigo_sus = ""
    }
    var ate_codigo = $("#ate_codigo").val();
    var usu_codigo = $("#usu_codigo").val();
    var valoresForm = {usu_codigo: usu_codigo, proc_codigo: proc_codigo, proc_solicitado: "S", ate_codigo: ate_codigo};
    if (validaProcedimento(proc_codigo) == 0) {
        var req_codigo = 0;
        $.ajax({
            url: baseUrl + "/prontuario/exame/salvar/pre_natal/1",
            type: "POST",
            data: valoresForm,
            async: false,
            success: function (txt) {
                if (txt.success == true) {
                    req_codigo = txt.req_codigo;
                } else {
                    mensagem(txt.titulo + "," + txt.mensagem, 300, 150, function () {
                        return false;
                    });
                }

            }
        });
        $("#proc_qtd_total").val(cont);
        $("#procedimentos").append(
            '<tr id="proc_qtd' + cont + '">' +
            "<td class='ui-state-default'><input type='checkbox' class='itensSelecionados' value='" + req_codigo + "'>" +
            "</td>" +
            "   <td class='ui-state-default' style='text-align: center;'>" + proc_codigo_sus +
            "       <input type='hidden' name='proc[" + cont + "][proc_codigo]' value=\"" + proc_codigo + "\" />" +
            "   </td>" +
            "<td class='ui-state-default'>" + proc_nome + "</td>" +
            "<td class='ui-state-default escape' style='width: 40%'><input  class='ui-state-default' type='text'  style='width: 99%' value='' placeholder='Digite uma observação' onblur='salvaObservacao("+cont+","+req_codigo+");' name='exame_obs_"+cont+"' id='exame_obs_"+cont+"'/></td>" +
            "<td class='ui-state-default'>" + dataSolicitacao + "</td>" +
            "<td class='ui-state-default c'>" +
            "<a>" +
            '<img src="' + baseUrl + '/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="excluirProcedimento(' + cont + ',' + req_codigo + ')"/>' +
            "</a>" +
            "</td>" +
            "</tr>");
        $("#proc_nome").val("");
        $('#req_observacao').val("");
        $("#proc_codigo").val("");
        $("#proc_codigo_sus").val("");
        $('#botao_imprimir').show();
    }

}

function adicionaProcedimento() {
    var cont = parseInt($("#proc_qtd_total").val()) + 1;
    var ate_codigo = $("#ate_codigo").val();
    var usu_codigo = $("#usu_codigo").val();
    var proc_nome = $("#proc_nome").val().trim();
    var proc_codigo = $("#proc_codigo").val();
    var proc_codigo_sus = $("#proc_codigo_sus").val();
    var sem_codigo = "";
    var dataSolicitacao = $('#data-solicitacao').val();
    var observacao = $('#req_observacao').val();
    if (observacao) {
        observacao = observacao.replace("</p>", "");
        observacao = observacao.replace("<p>", "");
    }

    var valoresForm = {
        usu_codigo: usu_codigo,
        proc_codigo: proc_codigo,
        req_observacao: observacao,
        proc_solicitado: "S",
        ate_codigo: ate_codigo,
        dataSolicitacao: dataSolicitacao
    };

    if (proc_nome == "" || proc_codigo == "") {
        return;
    }

    if (validaProcedimento(proc_codigo) == 0) {
        var req_codigo = 0;
        $.ajax({
            url: baseUrl + "/prontuario/exame/salvar/pre_natal/1",
            type: "POST",
            data: valoresForm,
            async: false,
            success: function (txt) {
                if (txt.success == true) {
                    req_codigo = txt.req_codigo;
                } else {
                    mensagem(txt.titulo + "," + txt.mensagem, 300, 150, function () {
                        return false;
                    });
                }

            }
        });

        $("#proc_qtd_total").val(cont);
        $("#procedimentos").append(
            '<tr id="proc_qtd' + cont + '">' +
            "<td class='ui-state-default'><input type='checkbox' class='itensSelecionados' value='" + req_codigo + "'>" +
            "</td>" +
            "   <td class='ui-state-default' style='text-align: center;'>" + sem_codigo + proc_codigo_sus +
            "       <input type='hidden' name='proc[" + cont + "][proc_codigo]' value=\"" + proc_codigo + "\" />" +
            "   </td>" +
            "<td class='ui-state-default'>" + proc_nome + "</td>" +
            "<td class='ui-state-default escape' style='width: 40%'><input  class='ui-state-default' type='text'  style='width: 99%' value='" + observacao + "'placeholder='Digite uma observação' onblur='salvaObservacao("+cont+","+req_codigo+");' name='exame_obs_"+cont+"' id='exame_obs_"+cont+"'/></td>" +
            "<td class='ui-state-default'>" + dataSolicitacao + "</td>" +
            "<td class='ui-state-default c'>" +
            "<a>" +
            '<img src="' + baseUrl + '/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="excluirProcedimento(' + cont + ',' + req_codigo + ')"/>' +
            "</a>" +
            "</td>" +
            "</tr>");
        $("#proc_nome").val("");
        $('#req_observacao').val("");
        $("#proc_codigo").val("");
        $("#proc_codigo_sus").val("");
        $('#botao_imprimir').show();
    } else {
        mensagem("Erro", "Procedimento já cadastrado", 250, 150);
        $("#proc_nome").val("");
        $("#proc_codigo").val("");
        $("#proc_codigo_sus").val("");
        $('#req_observacao').val("");

    }


}

function validaProcedimento(term) {
    var cont = new Number($("#proc_qtd_total").val()) + 1;
    var table = $('#procedimentos');
    var retorno = "";
    if (cont > 1) {
        table.find('tr').each(function (indice) {
            $(this).find('td input[type="hidden"]').each(function (indice) {
                if (term == $(this).val()) {
                    retorno = 1;
                }
            });
        });
    }
    return retorno;
}

function imprimirSelecionados(url) {
    var print = [];
    $(".itensSelecionados ").each(function () {
        if (this.checked)
            print.push(this.value);
    });
    popup(url + "/selecionados/" + print, 800, 600);

}

function calculaDPP() {
    data = brToSql($('#dum').val());
    if ($('#tipo_consulta:checked').val() == 2) {
        return false;
    }
    $.ajax({
        url: baseUrl + "/prontuario/pre-natal/calcula-dpp/dum/" + data,
        type: "POST",
        data: data,
        success: function (txt) {
            if (txt.success) {
                $('#dpp').val(dataToBr(txt.data));
                return true;
            }
        }
    });
}

function habilitaPreConsulta() {
    $('#antropometria').find('input, textarea, select').removeAttr('disabled', 'disabled');
    $('#sinais_vitais').find('input, textarea, select').removeAttr('disabled', 'disabled');
    $('#glicemia').find('input, textarea, select').removeAttr('disabled', 'disabled');
    $('#reavaliar').hide();
    $('#botoes_pre_consulta').show();
    $('#botao_editar_pc').hide();
    $('#salva_pre_consulta').val("1");


}

function desabilitaPreConsulta() {
    $('#antropometria').find('input, textarea, select').attr('disabled', 'disabled');
    $('#sinais_vitais').find('input, textarea, select').attr('disabled', 'disabled');
    $('#glicemia').find('input, textarea, select').attr('disabled', 'disabled');
    $('#botoes_pre_consulta').hide();
    $('#reavaliar').show();
    $('#salva_pre_consulta').val("0");
}

function salvaPreConsulta() {
    var peso = $('#peso').val();
    var altura = $('#altura').val();
    var valoresPreConsulta = $('#form-pn').serializeArray();
    if (!(peso > 1)) {
        alert('É necessário informar o peso.');
        document.getElementById('peso').focus();
        return false;
    }
    if (!(altura > 0)) {
        alert('É necessário informar a altura.');
        document.getElementById('altura').focus();
        return false;
    }
    $.ajax({
        url: baseUrl + "/prontuario/pre-consulta/salvar-do-prenatal",
        type: "POST",
        data: valoresPreConsulta,
        success: function (txt) {
            if (txt.success) {
                $('#pc_codigo').val(txt.pc_codigo);
                $('#botoes_pre_consulta').hide();
                $('#botao_editar_pc').show();
                $('#reavaliar').hide();
                $('#salva_pre_consulta').val("0");
            }
            return true;
        }
    });
}

function visualizarProcGrupos(gruex_codigo) {
    $("<div id='grupo_exame'></div>").dialog({
        autoOpen: true,
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: ['top', 'center'],
        width: 400,
        height: 400,
        title: "Exames vinculados ao grupo:",
        close: function () {
            $(this).remove();
        },
        buttons: {
            "Adicionar": function () {
                inserirGrupoProcedimento(gruex_codigo);
                $(this).dialog('close');
            },
            "Voltar": function () {


                $(this).dialog('close');
            }
        }
    }).load(baseUrl + '/laboratorio/grupo-de-exames/grupo-prenatal/gruex_codigo/' + gruex_codigo);
}

function excluirProcedimento(id, req_codigo) {
    $("#proc_qtd" + id).remove();
    if (req_codigo) {
        $.ajax({
            url: baseUrl + "/prontuario/exame/excluir/id/" + req_codigo + "/pre_natal/1",
            type: "GET",
            success: function (txt) {
                if (txt.success == true) {
                    return true;
                } else {
                    mensagem(txt.titulo + "," + txt.mensagem, 300, 150, function () {
                        return false;
                    });
                }

            }
        });
    }
}

function salvaObservacao(id, req_codigo) {

    var observacao = $("#exame_obs_" + id).val().trim();
    if (observacao.length > 3) {
        var valores = {req_codigo: req_codigo, req_observacao: observacao};
        $.ajax({
            url: baseUrl + "/prontuario/exame/salva-observacao",
            type: "POST",
            data: valores,
            success: function () {
                return true;
            }
        });
    }
}

function salvar() {
     var valoresForm = $('#form-pn').serialize();
    if (validaPreNatal() === true) {
        if($('#salva_pre_consulta').val() == 1){
            salvaPreConsulta();
        }
        $.ajax({
            url: baseUrl + "/prontuario/pre-natal/salvar",
            type: "POST",
            data: valoresForm,
            success: function (txt) {
                if (txt.success) {
                    mensagem("Atenção",txt.mensagem,240,150);
                    location.reload();

                }
            }
        });
    }
}

function validaPreNatal() {

    var tipo_consulta = $('#tipo_consulta:checked').val();
    var ultima_consulta = $('#ultima_consulta').val();
    var dum = $('#dum').val();
    var peso = $('#peso').val();
    var altura = $('#altura').val();
    var gravidez_planejada = $('#gravidez_planejada:checked').val();
    var gestas_previas = $('#gestas_previas').val();
    var partos = $('#partos').val();
    var vacinacao_em_dia = $('#vacinacao_em_dia:checked').val();
    var tipo_gravidez = $('#tipo_gravidez').find(':selected').val();

    if (gestas_previas == "") {
        alert('É necessário informar as gestas prévias');
        document.getElementById('gestas_previas').focus();
        return false;
    }
    if (partos == "") {
        alert('É necessário informar o numero de partos já realizados');
        document.getElementById('dum').focus();
        return false;
    }
    if (tipo_consulta == 1 && (ultima_consulta == '' || ultima_consulta == 2)) {
        if (dum == "") {
            alert('É necessário informar a DUM no primeiro atendimento a gestante');
            document.getElementById('dum').focus();
            return false;
        }
        if (tipo_gravidez == "") {
            alert('É necessário informar o tipo de gravidez no primeiro atendimento a gestante');
            document.getElementById('tipo_gravidez').focus();
            return false;
        }
        if (gravidez_planejada == null) {
            alert('A informação sobre gravidez planejada é obrigatória.');
            document.getElementById('gravidez_planejada').focus();
            return false;
        }
    }

    if (!(peso > 1)) {
        alert('É necessário informar o peso.');
        document.getElementById('peso').focus();
        return false;
    }
    if (!(altura > 0)) {
        alert('É necessário informar a altura.');
        document.getElementById('altura').focus();
        return false;
    }
    if (vacinacao_em_dia == null) {
        alert('É necessário informar a situação vacinal.');
        document.getElementById('vacinacao_em_dia').focus();
        return false;
    }

    return true;
}

function tipoAtendimento() {
    if ($('#tipo_consulta:checked').val() == 2) {
        $('#dados_pre_natal').hide();
        $('#dados_dpp').hide();
        $('#dpp').val("");
        $('#idade_gestacional').val("");
        $('#data_parto').show();


    } else {
        $('#dados_pre_natal').show();
        $('#dados_dpp').show();
        $('#data_parto').hide();
    }
}
