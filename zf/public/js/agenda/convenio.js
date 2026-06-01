/* -----------------------------------------------------------------
 * MÉTODOS CONVÊNIOS AGENDAMENTO ESTABELECIMENTO DE SAÚDE
 * ----------------------------------------------------------------*/

// Validação Formulário de Cadastro Estabelecimento, Agendamento
$(function () {
    $("#form-vincageest").validate({
        rules: {
            codigo_convenio: {
                required: true,
            }
        },
        messages: {
            codigo_convenio: {
                required: "Campo Obrigatório",
            }
        }
    });

    $('#conv_tipo_valor').hide();
    $('#conv_valor_total').hide();
    $('#valor_mensal').show();
    $('#valor_contratual').hide();

    $("#a_data_inicial, #a_data_final").datepicker();
    $("#a_data_inicial, #a_data_final").mask("99/99/9999");

    $("#data_inicial, #data_final").datepicker();
    $("#data_inicial, #data_final").mask("99/99/9999");
    $('[name="tipo_convenio"]').change(function () {
        if ($('#tipo_convenio_s').is(':checked')) {
            $('#conv_tipo_valor').hide();
            $('#conv_valor_total').hide();
        }
        if ($('#tipo_convenio_c').is(':checked')) {
            $('#conv_tipo_valor').show();
            $('#conv_valor_total').show();
        }
    });

    $('[name="conv_tipo_valor"]').change(function () {
        if ($('#conv_tipo_valor_m').is(':checked')) {
            $('#valor_mensal').show();
            $('#valor_contratual').hide();
        }
        if ($('#conv_tipo_valor_c').is(':checked')) {
            $('#valor_mensal').hide();
            $('#valor_contratual').show();
        }
    });

    montaCampos();
});

function montaCampos() {
    if ($('#tipo_convenio_s').is(':checked')) {
        $('#conv_tipo_valor').hide();
        $('#conv_valor_total').hide();
    } else {
        $('#conv_tipo_valor').show();
        $('#conv_valor_total').show();
    }
    if ($('#conv_tipo_valor_m').is(':checked')) {
        $('#valor_mensal').show();
        $('#valor_contratual').hide();
    } else {
        $('#valor_mensal').hide();
        $('#valor_contratual').show();
    }

}

// Busca Genérica Unidade(Estabelecimento)
$(function () {
    $("#nome_convenio_est").buscar({
        url: baseUrl + '/agenda/convenio/buscar-estabelecimentos-de-saude/',
        categoria: 'categoria',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            return true;
        }
    });
})

// Exclui e valida a exclusão do Estabelecimento(Convênio)
function excluirVinculoAgendamentoEstabelecimentoDeSaude(conv_codigo, uni_codigo) {
    $.ajax({
        url: baseUrl + "/agenda/convenio/get-num-agendamento-estabelecimento-de-saude",
        type: "POST",
        data: {
            conv_codigo: conv_codigo,
            uni_codigo: uni_codigo
        },
        success: function (txt) {
            if (txt > 0) {
                mensagem("Erro ao Excluir!", "Estabelecimento de Saúde possui Agendamentos cadastrados!", 380, 140);
            } else {
                confirme("Confirme", "Deseja realmente excluir este item?", 300, 120, function () {
                    $.ajax({
                        url: baseUrl + "/agenda/convenio/excluir-vinculo-agendamento-estabelecimento-de-saude/",
                        type: "POST",
                        data: {
                            conv_codigo: conv_codigo,
                            uni_codigo: uni_codigo
                        },
                        success: function (txt) {
                            window.location.href = baseUrl + "/agenda/convenio/agendamento-estabelecimentos-de-saude";
                        }
                    });
                });
            }
        }
    });
}

/* -----------------------------------------------------------------
 * MÉTODOS CONVÊNIOS
 * ----------------------------------------------------------------*/

// Busca Genérica Laboratórios e Hospital(Médico)
$(function () {
    $("#nome_convenio_geral").buscar({
        url: baseUrl + "/agenda/convenio/buscar-convenios/",
        categoria: "categoria",
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            return true;
        }
    });
});

$(function () {
    $("#form-conv").validate({
        rules: {
            codigo_convenio: "required"
        },
        messages: {
            codigo_convenio: "Campo Obrigatório"
        }
    });
});

function marcarFalta(conv_codigo, med_codigo) {
    confirme("Confirme", "Deseja liberar as cotas dos exames não realizados no período abaixo informado?\n\
            <br/><br/><div style='width:190px;display:inline'>Data inicial: </div><input id='a_data_inicial' style='width:85px;' type='text' onfocus='$(\"#a_data_inicial, #a_data_final\").datepicker();\n\
    $(\"#a_data_inicial, #a_data_final\").mask(\"99/99/9999\");' onclick='$(\"#a_data_inicial, #a_data_final\").datepicker();\n\
    $(\"#a_data_inicial, #a_data_final\").mask(\"99/99/9999\");'/>\n\
            <br/><div style='width:190px;display:inline'>Data Final: </div>&nbsp;<input id='a_data_final' style='width:85px;' type='text' onfocus='$(\"#a_data_inicial, #a_data_final\").datepicker();\n\
    $(\"#a_data_inicial, #a_data_final\").mask(\"99/99/9999\");' onclick='$(\"#a_data_inicial, #a_data_final\").datepicker();\n\
    $(\"#a_data_inicial, #a_data_final\").mask(\"99/99/9999\");'/>",
            300, 220, function () {
                if ($("#a_data_inicial").val().length > 0) {
                    $.ajax({
                        url: baseUrl + "/agenda/convenio/liberar-cota/",
                        type: "POST",
                        data: {
                            med_codigo: med_codigo,
                            conv_codigo: conv_codigo,
                            data_inicio: $("#a_data_inicial").val(),
                            data_fim: $("#a_data_final").val()
                        },
                        success: function (txt) {
                            window.location.href = baseUrl + "/agenda/convenio/";
                        }
                    });
                }
            });
}

function excluir(conv_codigo, med_codigo) {
    $.ajax({
        url: baseUrl + "/agenda/convenio/get-num-conv-agendados",
        type: "POST",
        data: {
            med_codigo: med_codigo,
            conv_codigo: conv_codigo
        },
        success: function (txt) {
            if (txt > 0) {
                mensagem("Erro ao Excluir!", "Convênio possui agendamentos cadastrados!", 380, 140);
            } else {
                confirme("Confirme", "Deseja realmente excluir este item?", 300, 120, function () {
                    $.ajax({
                        url: baseUrl + "/agenda/convenio/excluir/",
                        type: "POST",
                        data: {
                            med_codigo: med_codigo,
                            conv_codigo: conv_codigo
                        },
                        success: function (txt) {
                            window.location.href = baseUrl + "/agenda/convenio/";
                        }
                    });
                });
            }
        }
    });
}

/* -----------------------------------------------------------------
 * OUTROS MÉTODOS DE CONVÊNIO QUE NÃO SEI SE ESTÁ SENDO USADO
 * ----------------------------------------------------------------*/

$(function () {
    $("#nome_convenio").buscar({
        url: baseUrl + '/agenda/convenio/buscar/todos/1',
        categoria: 'categoria',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            return true;
        }
    });
})

$(function () {
    $("#busca").select();
    $("#tabs").tabs();
});

function sabadoDomingo(to, id, tipo) {
    $("#" + tipo + id)
            .attr("src", baseUrl + "/public/images/icons/loading.gif")
            .attr("title", "Carregando...")
            .removeAttr("onclick");
    $.ajax({
        url: baseUrl + '/agenda/convenio/atende',
        type: "POST",
        data: {
            conv_codigo: id,
            tipo: tipo,
            to: to
        },
        success: function (r) {
            if (r == 'T') {
                to = 'F';
                img = "accept";
                title = "Ativo";
            } else if (r == 'F') {
                to = "T";
                img = "excluir2";
                title = "Desativado";
            } else {
                mensagem('Erro', r, 250, 150);
                return;
            }
            $("#" + tipo + id)
                    .attr("src", baseUrl + "/public/images/icons/" + img + ".png")
                    .attr("title", title)
                    .attr("onclick", "sabadoDomingo('" + to + "'," + id + ",'" + tipo + "')");
        }
    });
}

// domingo
function enc(to, id) {
    $("#enc" + id)
            .attr("src", "<?=LINKCOMUM?>/imgs/loading.gif")
            .attr("alt", "Carregando...")
            .removeAttr("onclick");
    $.ajax({
        url: "especialidade.ajax.php",
        type: "POST",
        data: {
            esp_codigo: id,
            tipo: 'enc',
            to: to
        },
        success: function (r) {
            if (r == 1) {
                to = 0;
                img = "selecionar";
                alt = "Sim";
            } else {
                to = 1;
                img = "excluir";
                alt = "Não";
            }
            $("#enc" + id)
                    .attr("src", "<?=LINKCOMUM?>/imgsBotoes/" + img + ".png")
                    .attr("alt", alt)
                    .attr("onclick", "enc(" + to + "," + id + ")");
        }
    });
}
