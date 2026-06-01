$(document).ready(function () {
    $("tr:odd").addClass("odd");
});

$(function () {
    $("#rec_codigo").change(function () {
        $("#usu_codigo").val("");
        $("#buscar1").val("");
        $("#usr_codigo").val("");
        $("#buscar5").val("");
        $("#interno").val("");
        $(".linha_produto").remove();

        $.ajax({
            url: baseUrl + "/farmacia/farmacia/get-receita",
            data: {
                rec_codigo: $("#rec_codigo").val()
            },
            success: function (txt) {
                if (txt.length != 0) {
                    $("#usu_codigo").val(txt.usu_codigo);
                    $("#buscar1").val(txt.usu_nome);
                    $("#buscar1").attr('readonly', true);
                    $("#usr_codigo").val(txt.usr_codigo);
                    $("#buscar5").val(txt.usr_nome);
                    $("#buscar5").attr('readonly', true);
                    $("#interno").val(1);
                    $("#pro_codigo").attr('readonly', true);
                    $("#pro_nome").attr('readonly', true);
                    $("#ite_quantidade").attr('readonly', true);
                    $("#ite_duracao").attr('readonly', true);
                    var faltou = "";
                    var table = "";
                    for (var i in txt.itens) {
                        faltou += addLinhaCodBarrasAutomatico(txt.itens[i].pro_codigo, txt.itens[i].irec_quantidade, txt.itens[i].pro_nome, txt.itens[i].irec_codigo, "", txt.itens.length);
                    }

                    if (faltou != "") {
                        table = "<table>" +
                            "<tr>" +
                            "<th>Produto</th>" +
                            "<th>Qtde. Solicitada</th>" +
                            "<th>Qtde. Pendente</th>" +
                            "</tr>";

                        table += faltou;
                        table += "</table>";
                        mensagem("Alerta", "Não há estoque suficiente para os seguintes produtos:<br/><br/>" + table, 700, 300);
                    }

                } else {
                    mensagem("Erro", "Receita Inválida", 300, 150, function () {
                        setTimeout(function () {
                            $("#rec_codigo").val("");
                            $("#rec_codigo").focus();
                        }, 500);
                    });

                }
            }
        });
    });

    $("#buscar5").buscar({
        url: baseUrl + '/default/usuarios/buscar/externo/1',
        categoria: 'categoria',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            $("#buscar1").focus();
            return true;
        }
    });

    $("#pro_codigo").change(function () {
        if ($("#usu_codigo").val() == "") {
            mensagem("Erro", "Selecione o Paciente", 300, 150);
            $("#pro_codigo").val("");
            $("#pro_nome").val("");
            return false;
        }

        if ($("#pro_codigo").val() == "") {
            $("#pro_codigo").val("");
            $("#pro_nome").val("");
            $("#div_lote").html("<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default\" style=\"width:350px;\" disabled=\"disabled\">" +
                "<option value=\"\">Lote - Validade - Saldo</option>" +
                "</select>");
            $("#ite_quantidade").val("");
            $("#ite_duracao").val("");
            return false;
        }

        $.ajax({
            url: baseUrl + '/default/produto/verifica-se-dispensou-no-dia',
            data: {
                pro_codigo: $(this).val(),
                usu_codigo: $("#usu_codigo").val()
            },
            success: function (txt) {

                if (txt == 1) {
                    $("#pro_codigo").val("");
                    $("#pro_nome").val("");
                    mensagem("Erro", "Este Paciente já pegou esse produto hoje!", 300, 150);
                    return false;
                } else {
                    mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);

                    $.ajax({
                        url: baseUrl + '/default/produto/get-produto-com-estoque',
                        data: {
                            pro_codigo: $("#pro_codigo").val(),
                            set_codigo: $("#set_codigo").val()
                        },
                        success: function (txt) {

                            if (txt) {
                                $("#pro_nome").val(txt.pro_nome);
                                $("#psico_codigo").val(txt.psico_codigo);
                                if ($("#lote_automatico").val() != 1) {
                                    getLotes(txt.pro_codigo);
                                }
                                $("#carregando-ate").dialog("destroy").remove();
                                $("#ite_quantidade").focus();
                                $("#ite_quantidade").removeAttr("readonly");
                                if ($("#psico_codigo").val() != "" && txt.psico_exige_codigo != "false") {
                                    getCodigoReceita();
                                }

                            } else {
                                $("#carregando-ate").dialog("destroy").remove();
                                $("#pro_codigo").focus();
                                $("#pro_codigo").val("");
                                $("#pro_nome").val("");
                                $("#ite_lote").html("<option value=\"\">Lote - Validade - Saldo</option>");
                                $("#ite_lote").prop('disabled', 'disabled');
                                $("#ite_lote").addClass("ui-state-disabled");
                            }

                        }
                    });
                    //mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);

                    $.ajax({
                        url: baseUrl + '/default/produto/get-produto-com-estoque',
                        data: {
                            pro_codigo: $(this).val(),
                            set_codigo: $("#set_codigo").val()
                        },
                        success: function (txt) {

                            if (txt) {
                                $("#pro_nome").val(txt.pro_nome);
                                if ($("#lote_automatico").val() != 1) {
                                    getLotes(txt.pro_codigo);
                                }
                                $("#carregando-ate").dialog("destroy").remove();
                                $("#ite_quantidade").focus();
                                $("#ite_quantidade").removeAttr("readonly");

                            } else {
                                $("#carregando-ate").dialog("destroy").remove();
                                $("#pro_codigo").focus();
                                $("#pro_codigo").val("");
                                $("#pro_nome").val("");
                                $("#ite_lote").html("<option value=\"\">Lote - Validade - Saldo</option>");
                                $("#ite_lote").prop('disabled', 'disabled');
                                $("#ite_lote").addClass("ui-state-disabled");
                            }

                        }
                    });
                }
            }
        });

    });

    $("#pro_nome").buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl + "/produto/buscar-produtos-com-estoque/setor/" + $("#set_codigo").val() + "/setor_movimento/" + 1, //Passando true como parametro de setor da nota Ps: Nome da variavel errado
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function (ul, item) {
            if ($("#usu_codigo").val() == "") {
                mensagem("Erro", "Selecione o Paciente", 300, 150);
                $("#pro_codigo").val("");
                $("#pro_nome").val("");
                return false;
            }

            $('#psico_codigo').val(item.item.data.psico_codigo);
            $('#psico_exige_codigo').val(item.item.data.psico_exige_codigo);
            
            var recebeCodigoCnes = $("#cnes_tp_unid_id").val();
            
            if (recebeCodigoCnes == 't') {
                $.ajax({
                    url: baseUrl + '/default/produto/verifica-se-dispensou-no-dia',
                    data: {
                        pro_codigo: $("#pro_codigo").val(),
                        usu_codigo: $("#usu_codigo").val()
                    },
                    success: function (txt) {
                        if (txt == 1) {
                            $("#pro_codigo").val("");
                            $("#pro_nome").val("");
                            mensagem("Erro", "Este Paciente já pegou esse produto hoje!", 300, 150);
                            return false;
                        } else {

                            if ($("#lote_automatico").val() != 1) {
                                getLotes();
                            }
                            $("#ite_quantidade").focus();
                            $("#ite_quantidade").removeAttr("readonly");
                        }
                    }
                });

            } else {

                if ($("#psico_codigo").val() != "" && $("#psico_exige_codigo").val() != "false") {
                    getCodigoReceita();
                }

                $.ajax({
                    url: baseUrl + '/default/produto/verifica-se-dispensou-no-dia',
                    data: {
                        pro_codigo: $("#pro_codigo").val(),
                        usu_codigo: $("#usu_codigo").val()
                    },
                    success: function (txt) {
                        if (txt == 1) {
                            $("#pro_codigo").val("");
                            $("#pro_nome").val("");
                            mensagem("Erro", "Este Paciente já pegou esse produto hoje!", 300, 150);
                            return false;
                        } else {

                            if ($("#lote_automatico").val() != 1) {
                                getLotes();
                            }
                            $("#ite_quantidade").focus();
                            $("#ite_quantidade").removeAttr("readonly");
                        }
                    }
                });
            }
        }
    });

    $(".nova_linha").click(function () {
        addLinhaProd();
    });


    $("#new").click(function () {
        if ($("#lote_automatico").val() != 1) {
            addLinhaProd();
        } else {
            addLinhaLoteAutomatico();
        }
    });

    $("#salvar_dispensacao").click(function () {
        salvarDispensa();
    });

    $("#ite_lote").change(function () {
        $("#ite_quantidade").focus();
    });

    $(".historico").click(function () {

        var usu_codigo = $("#usu_codigo").val();
        if (usu_codigo == "" || usu_codigo == null) {
            setTimeout(function () {
                mensagem("Erro", "Selecione um paciente", 300, 150)
            }, 500);
            return false;
        }

        $("body").append("<div id=\"historico-dialog\" title=\"Histórico\"></div>");
        $.ajax({
            url: baseUrl + '/farmacia/farmacia/get-historico-paciente',
            data: {
                usu_codigo: usu_codigo
            },
            success: function (txt) {
                var table = "<table class=\"lista\">" +
                    "<tr>" +
                    "<th>Produto</th>" +
                    "<th>Data</th>" +
                    "<th>Lote</th>" +
                    "<th>Qtde.</th>" +
                    ($("#validade_medicamentos").val() == 1 ? "<th>Próxima Dispensação</th>" : "") +
                    "<th>Usuário</th>" +
                    "<th>Setor</th>" +
                    "</tr>";
                for (var i in txt) {
                    table += "<tr>" +
                        "<td>" + txt[i].pro_nome + "</td>" +
                        "<td>" + dataToBr(txt[i].mov_data) + "</td>" +
                        "<td>" + txt[i].ite_lote + "</td>" +
                        "<td>" + Math.round(txt[i].ite_quantidade) + "</td>" +
                        ($("#validade_medicamentos").val() == 1 ? "<td>" + txt[i].duracao + "</td>" : "") +
                        "<td>" + txt[i].usr_nome + "</td>" +
                        "<td>" + txt[i].set_nome + "</td>" +
                        "</tr>";
                }
                table += "</table>";
                $("#historico-dialog").html(table);
            }
        });
        $("#historico-dialog").dialog({
            modal: true,
            width: 960,
            height: 600,
            close: function () {
                $("#pro_codigo").focus();

                $(this).dialog('close');
            },
            buttons: {
                Ok: function () {
                    $("#pro_codigo").focus();
                    $(this).dialog('close');
                }
            }
        });
    });

    $(".paciente").click(function () {
        var usu_codigo = $("#usu_codigo").val();
        var cadastro_aise = $("#cadastro_aise").val();
        var link = "";


        // if(cadastro_aise == 1){
        link = baseUrl + "/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
        //}else{
        // link = "../../../../WebSocialSaude/paciente.php?acao=form&poupup=1&usu_codigo="+usu_codigo;
        //}
        window.open(link, "_blank", "scrollbars=1,height=800,width=900", 'width=850,height=700');
    });

});

function getCodigoReceita() {
    //$("body").append("");
    $("#receita-controlada").dialog({
        modal: true,
        width: 300,
        height: 200,
        buttons: {
            Ok: function () {
                $("#ite_quantidade").focus();
                if ($("#cod_receita").val() != "") {
                    $("#ite_cod_receita").val($("#cod_receita").val());
                    $(this).dialog('destroy');
                } else {
                    $("#cod_receita").focus();
                }
            }
        },
        open: function () {
            $('.ui-dialog-titlebar-close').remove();
        }
    });
}

function buscaPaciente() {
    var tipo_busca = $("#tipo_busca").val();
    $("#buscar1").buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl + '/paciente/buscar/tipo_busca/' + tipo_busca,
        callback: function () {
            getUltimosDispensados();
            getProgramaDispensacao();
            $("#pro_codigo").focus();
        }
    });
}

function getUltimosDispensados() {
    var usu_codigo = $("#usu_codigo").val();
    $("body").append("<div id=\"ultimos-dialog\" style=\"display:none;\" title=\"O paciente pegou medicamento nos ultimos dias\"></div>");
    var quantidade = 0;
    $.ajax({
        url: baseUrl + '/farmacia/farmacia/get-ultimos-dispensados',
        data: {
            usu_codigo: usu_codigo
        },
        async: false,
        success: function (txt) {
            if (txt != "" || txt != null) {
                var table = "<table class=\"lista\">" +
                    "<tr>" +
                    "<th>Produto</th>" +
                    "<th>Data</th>" +
                    "<th>Lote</th>" +
                    "<th>Qtde.</th>" +
                    ($("#validade_medicamentos").val() == 1 ? "<th>Próxima Dispensação</th>" : "") +
                    "<th>Usuário</th>" +
                    "<th>Setor</th>" +
                    "</tr>";
                quantidade = txt.length;
                for (var i in txt) {

                    table += "<tr>" +
                        "<td>" + txt[i].pro_nome + "</td>" +
                        "<td>" + dataToBr(txt[i].mov_data) + "</td>" +
                        "<td>" + txt[i].ite_lote + "</td>" +
                        "<td>" + Math.round(txt[i].ite_quantidade) + "</td>" +
                        ($("#validade_medicamentos").val() == 1 ? "<td>" + txt[i].duracao + "</td>" : "") +
                        "<td>" + txt[i].usr_nome + "</td>" +
                        "<td>" + txt[i].set_nome + "</td>" +
                        "</tr>";
                }
                table += "</table>";
                $("#ultimos-dialog").html(table);
            }
        }
    });
    if (quantidade > 0) {
        $("#ultimos-dialog").show();
        $("#ultimos-dialog").dialog({
            modal: true,
            width: 960,
            height: 400,
            close: function () {
                foco();
                $(this).dialog('close');
            },
            buttons: {
                Ok: function () {
                    foco();
                    $(this).dialog('close');
                }
            },
            open: function () {
                $(this).parent().find('button:nth-child(1)').focus();
            }
        });

    }
}

function getProgramaDispensacao() {
    var usu_codigo = $("#usu_codigo").val();

    $.ajax({
        url: baseUrl + '/farmacia/farmacia/get-programa-dispensa',
        type: "GET",
        data: {
            "usu_codigo": usu_codigo
        },
        success: retorno => {
            if (retorno && retorno.length > 0) {
                $("select.PP").empty()

                window.programa_produto = retorno

                $("select.PP").append('<option readonly disabled selected>Escolha o grupo</option>')

                retorno.forEach(item => {
                    $("select.PP").append(`<option value="${item.prg_codigo}">${item.prg_nome}</option>`)
                })
                $(".PP").show()
            } else {
                $(".PP").hide()
            }
        }
    })
}

function selectAll() {
    addAllLines();
    $("#selectAll").hide()
}

function getMedicamentosGrupo(el) {
    $("#itens_div table tbody tr.linha_produto").empty()

    $.ajax({
        url: baseUrl + '/farmacia/farmacia/get-medicamentos-grupo',
        type: "GET",
        data: {
            "prg_codigo": el.value,
            'usu_codigo': $("#usu_codigo").val()
        },
        success: retorno => {
            if (retorno && retorno.length > 0) {

                $("#tbPrograma table tbody").empty()

                var cont = 0

                var arr = []

                retorno.forEach(item => {
                    cont++

                    var duracao = null

                    switch (item.ctp_periodo) {
                        case 'SEMANAL':
                            duracao = 7
                            break

                        case 'MENSAL':
                            duracao = 30
                            break

                        case 'DIARIO' || 'DIARIO':
                            duracao = 1
                            break

                        case 'ANUAL':
                            duracao = 365
                            break

                        case 'TRIMESTRAL':
                            duracao = 90
                            break

                        case 'BIMESTRAL':
                            duracao = 60
                            break

                        case 'SEMESTRAL':
                            duracao = 180
                            break
                    }

                    arr.push(item)


                    $("#tbPrograma table tbody").append(`
                        <tr class="line_${cont} lote_${item.sal_lote} ${item.pro_codigo}${item.sal_lote}" data-pro="${item.pro_codigo}" data-lote="${item.sal_lote}" data-validade="${item.sal_validade}" data-saldo="${item.sal_qtde}" data-qtde="${parseInt(item.ctp_quantidade)}" data-duracao="${duracao}" data-codrec="">
                            <td>
                                ${item.sal_qtde > 0 ? `<input type="checkbox" onchange="javascript: addLine(${cont})"  />` : '<span>Sem estoque</span>'}
                            </td>
                            <td>${item.pro_codigo}</td>
                            <td>${item.pro_nome}</td>
                            <td><b>Lote: ${item.sal_lote.toUpperCase()} - Val.: ${dataToBr(item.sal_validade)} - Saldo: ${item.sal_qtde}</b></td>
                            <td>${parseInt(item.ctp_quantidade)}</td>
                            <td>${duracao}</td>
                        </tr>
                    `)
                })

                $("#ite_quantidade").find("select").remove()

                window.itens = arr

                //<tr class="linha_produto line_1 lote_1708743 31708743" data-pro="3" data-lote="'1708743" data-validade="2019-07-30" data-saldo="7" data-qtde="1" data-duracao="30" data-codrec=""><td>3</td><td>ACEBROFILINA XPE ADULTO 50MG/5ML 120ML</td><td><b>Lote</b>:1708743 <b>Validade</b>: 30/07/2019 <b>Saldo</b>: 7</td><td>1</td><td>30</td><td><a href="#" class="ui-button add new ui-corner-bl ui-corner-tr" style="margin-left: 5px;" onclick="excluir('31708743')"> <div><img src="/WebSocialSaude/zf/public/images/icons/excluir2.png"></div>Excluir</a></td></tr>

                $("programa").text($(el).find('option[value=' + el.value + ']')[0].text)
                $("#tbPrograma").show()
            } else {
                $("#tbPrograma").hide()
            }
        }
    })
}

function foco() {
    $("#pro_codigo").focus();
}

function alterData(el, index) {
    var arr_lote = window.item_lotes

    var itens = el.value.split("|")
    var time = new Date().getTime(itens[2])

    //itens[2] = new Date(time).toISOString().slice(0, 10).replace('T', ' ');

    $("#table_itens tbody tr.line_" + (index - 1)).attr('data-lote', itens[0]).attr('data-validade', itens[2]).attr('data-saldo', itens[1])
}

function addLine(c) {
    var cont = 0

    var arr_lote = window.itens

    var duracao = null

    var option = ""

    $.ajax({
        url: baseUrl + '/farmacia/farmacia/get-lotes-medicamentos',
        type: "GET",
        data: {
            "prod_cod": arr_lote[c - 1].pro_codigo,
            "set_codigo": arr_lote[c - 1].set_codigo
        },
        success: response => {
            if (response.length > 1) {

                window.item_lotes = response

                option += `<select id="ite_lote" name="ite_lote" class="ui-state-default" onchange="alterData(this, ${c})" style="width:350px;">`

                response.forEach(el => {
                    option += `<option value="${el.sal_lote}|${el.sal_qtde}|${el.sal_validade}"><b>Lote</b>:${el.sal_lote} - <b>Validade</b>:${dataToBr(el.sal_validade)} - <b>Saldo</b>:${el.sal_qtde}</option>`
                })

                option += `</select>`
            } else {
                option = "<b>Lote</b>:" + arr_lote[c - 1].sal_lote.toUpperCase() + " <b>Val.</b>: " + (arr_lote[c - 1].sal_validade != "" ? dataToBr(arr_lote[c - 1].sal_validade) : "SEM_VALIDADE") + " <b>Saldo</b>: " + arr_lote[c - 1].sal_qtde
            }

            switch (arr_lote[c - 1].ctp_periodo) {
                case 'SEMANAL':
                    duracao = 7
                    break

                case 'MENSAL':
                    duracao = 30
                    break

                case 'DIARIO' || 'DIARIO':
                    duracao = 1
                    break

                case 'ANUAL':
                    duracao = 365
                    break

                case 'TRIMESTRAL':
                    duracao = 90
                    break

                case 'BIMESTRAL':
                    duracao = 60
                    break

                case 'SEMESTRAL':
                    duracao = 180
                    break
            }

            var linha = "<tr class=\"linha_produto line_" + cont + " lote_" + replaceSpecialChars(arr_lote[c - 1].sal_lote) + " " + arr_lote[c - 1].pro_codigo + replaceSpecialChars(arr_lote[c - 1].sal_lote) + "\" data-pro=\"" + arr_lote[c - 1].pro_codigo + "\" data-lote=\"'" + arr_lote[c - 1].sal_lote + "\" data-validade=\"" + dataToBr(arr_lote[c - 1].sal_validade) + "\" data-saldo=\"" + arr_lote[c - 1].sal_qtde + "\" data-qtde=\"" + arr_lote[c - 1].ctp_quantidade + "\" data-duracao=\"" + duracao + "\" data-codrec=\"" + $("#ite_cod_receita").val() + "\">" +
                "<td>" + arr_lote[c - 1].pro_codigo + "</td>" +
                "<td>" + arr_lote[c - 1].pro_nome + "</td>" +
                "<td>" + option + "</td>" +
                "<td><input type='text' style='width: 60px !important;' onkeyup='changeQtde(this)' value='" + parseInt(arr_lote[c - 1].ctp_quantidade) + "'></td>" +

                ($("#validade_medicamentos").val() == 1 ? "<td>" + duracao + "</td>" : "") +
                "<td><a href=\"#\" class=\"ui-button add new ui-corner-bl ui-corner-tr\" style=\"margin-left: 5px;\" onclick=\"excluir('" + arr_lote[c - 1].pro_codigo + replaceSpecialChars(arr_lote[c - 1].sal_lote) + "'); showLine(" + (c) + ")\"> <div><img src=\"/WebSocialSaude/zf/public/images/icons/excluir2.png\"></div>Excluir</a></td>" +
                "</tr>";

            $("#table_itens").append(linha);

            $(".line_" + c).hide()

        }
    })

    if ($("table#tablePrograma tbody tr:visible").length == 0) {
        $("#tbPrograma").hide()
    }
}

function changeQtde(el) {
    var v = $(el).parent().parent().get(0)

    $(v).attr('data-qtde', el.value)
}

function addAllLines() {
    var qtd = $("table#tablePrograma tbody tr:visible").length

    for (var i = 0; i < qtd; i++) {
        addLine(i + 1)
    }

}

function addLinhaProd() {

    var cont = $(".linha_produto").length + 1;

    var arr_lote = $("#ite_lote").val().split("|");
    // alert($("#ite_lote").val());
    if (parseInt(arr_lote[1]) < parseInt($("#ite_quantidade").val())) {
        setTimeout(function () {
            mensagem("Erro", "A quantidade digitada é maior que a quantidade do respectivo lote!", 300, 150)
        }, 500);
        $("#ite_quantidade").focus();
        return false;
    }

    if ($("#pro_codigo").val() != "" && $("#ite_quantidade").val() != "") {
        var linha = "<tr class=\"linha_produto line_" + cont + " lote_" + replaceSpecialChars(arr_lote[0]) + " " + $("#pro_codigo").val() + replaceSpecialChars(arr_lote[0]) + "\" data-pro=\"" + $("#pro_codigo").val() + "\" data-lote=\"'" + arr_lote[0] + "\" data-validade=\"" + arr_lote[2] + "\" data-saldo=\"" + arr_lote[1] + "\" data-qtde=\"" + $("#ite_quantidade").val() + "\" data-duracao=\"" + $("#ite_duracao").val() + "\" data-codrec=\"" + $("#ite_cod_receita").val() + "\">" +
            "<td id='pro_codigo'>" + $("#pro_codigo").val() + "</td>" +
            "<td>" + $("#pro_nome").val() + "</td>" +
            "<td><b>Lote</b>:" + arr_lote[0] + " <b>Validade</b>: " + (arr_lote[2] != "" ? dataToBr(arr_lote[2]) : "SEM_VALIDADE") + " <b>Saldo</b>: " + arr_lote[1] + "</td>" +
            "<td>" + $("#ite_quantidade").val() + "</td>" +

            ($("#validade_medicamentos").val() == 1 ? "<td>" + $("#ite_duracao").val() + "</td>" : "") +
            "<td>"+
                "<a href=\"#\" class=\"ui-button add new ui-corner-bl ui-corner-tr\" style=\"margin-left: 5px;\" onclick=\"excluir('" + $("#pro_codigo").val() + replaceSpecialChars(arr_lote[0]) + "')\"> <div><img src=\"/WebSocialSaude/zf/public/images/icons/excluir2.png\"></div>Excluir</a>" +
                "<a href=\"#\" class=\"ui-button add new ui-corner-bl ui-corner-tr\" style=\"margin-left: 5px;\" onclick=\"editarItensProdutos('" + $("#pro_codigo").val() +"', '" + $("#pro_nome").val() +"', '" + arr_lote[0] +"', '" + arr_lote[2] +"', '" + arr_lote[1] +"', '" + $("#ite_quantidade").val() +"', '" + $("#ite_duracao").val() +"', '" + $("#pro_codigo").val() + replaceSpecialChars(arr_lote[0]) + "')\"> <div><img src=\"/WebSocialSaude/zf/public/images/icons/editar.png\"></div>Editar</a></td>" +
            "</tr>";

        $("#table_itens").append(linha);
        $(".campos").val("");
        $("#div_lote").html("<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default\" style=\"width:350px;\" disabled=\"disabled\">" +
            "<option value=\"\">Lote - Validade - Saldo</option>" +
            "</select>");

        $("#pro_codigo").focus();
    } else {
        setTimeout(function () {
            mensagem("Erro", "Tem informações a serem preenchidas!", 300, 150)
        }, 500);
    }
}

function editarItensProdutos(pro_codigo, pro_nome, lote, validade, saldo, quantidade, duracao, pro_codigo_lote) {
    
    $("#table_itens ." + replaceSpecialChars(pro_codigo_lote)).remove();


    var recebeProCodigo = pro_codigo;
    var recebeProNome = pro_nome;
    var recebeLote = lote;
    var recebeDataToBr = dataToBr;
    var recebeSaldo = saldo;
    var recebeQuantidade = quantidade;
    var recebeDuracao = duracao;

    $('#pro_codigo').val(recebeProCodigo);
    // $('#pro_nome').val(recebeProNome);
    // // $('#').val(recebeLote);
    // // $('#').val(recebeDataToBr);
    // // $('#').val(recebeSaldo);
    $('#ite_quantidade').val(recebeQuantidade);
    $('#ite_duracao').val(recebeDuracao);


    $.ajax({
        url: baseUrl + '/default/produto/get-produto-com-estoque',
        data: {
            pro_codigo: recebeProCodigo,
            set_codigo: $("#set_codigo").val()
        },
        success: function (txt) {
            // console.log(txt);
            // return false
            if (txt) {
                $("#pro_nome").val(txt.pro_nome);
                if ($("#lote_automatico").val() != 1) {
                    console.log("aqui")
                    getLotesEditar(txt.pro_codigo);
                }
                $("#carregando-ate").dialog("destroy").remove();
                $("#ite_quantidade").focus();
                $("#ite_quantidade").removeAttr("readonly");

            } else {
                $("#carregando-ate").dialog("destroy").remove();
                $("#pro_codigo").focus();
                $("#pro_codigo").val("");
                $("#pro_nome").val("");
                $("#ite_lote").html("<option value=\"\">Lote - Validade - Saldo</option>");
                $("#ite_lote").prop('disabled', 'disabled');
                $("#ite_lote").addClass("ui-state-disabled");
            }

        }
    });
    
}

function getLotesEditar(pro_codigo) {

    if (pro_codigo == "" || pro_codigo == null) {
        pro_codigo = $("#pro_codigo").val();
    }

    $.ajax({
        url: baseUrl + "/produto/get-lotes/",
        type: "POST",
        data: {
            pro_codigo: pro_codigo,
            set_codigo: $("#set_codigo").val(),
            enviados: 1
        },
        success: function (txt) {
            // console.log()
            if (txt.length > 0) {
                var select = "<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default\" style=\"width:350px;\">";
                for (var i in txt) {
                    var quantidade = txt[i].sal_qtde;
                    if ($("." + pro_codigo + replaceSpecialChars(txt[i].sal_lote)).data("qtde")) {
                        quantidade = (txt[i].sal_qtde - parseInt($("." + pro_codigo + replaceSpecialChars(txt[i].sal_lote)).data("qtde")));
                    }
                    var checked = "";

                    //                    if(txt[i].sal_qtde < txt[i].saldo_original){
                    //                        mensagem("Alerta","O lote:<b>"+txt[i].sal_lote+"</b> possui envios pendentes!",300,150);
                    //                    }

                    //alert($(".lote_"+replaceSpecialChars(txt[i].sal_lote)).length);
                    if ($("." + txt[i].pro_codigo + replaceSpecialChars(txt[i].sal_lote)).length < 1) {
                        select += "<option value=\"" + txt[i].sal_lote + "|" + quantidade + "|" + txt[i].sal_validade + "\"" + checked + ">Lote: " + txt[i].sal_lote + " - Val.: " + dataToBr(txt[i].sal_validade) + " - Saldo: " + quantidade + "</option>";
                    }
                }

                select += "</select>";
                $("#div_lote").html(select);

                getFracionamentoEditar(document.querySelector("#div_lote").children[0], pro_codigo)

            } else {
                mensagem("Erro", "O produto selecionado não possui saldo!", 300, 150, function () {
                    foco();
                });
                $("#pro_codigo").val("");
                $("#pro_nome").val("");
                $("#ite_quantidade").val("");
                $("#pro_codigo").focus();
            }
        }
    });
}


function getFracionamentoEditar(elm = null, pro_codigo) {
    var qtd = elm.value.split('|')[1]
    

    $.ajax({
        url: baseUrl + "/produto/get-fracionamento",
        type: "GET",
        data: {
            pro_codigo: pro_codigo
        },
        success: res => {
            var pro_frmmin = res.pro_frmmin
            delete res

            var inp = $("input#ite_quantidade").length > 0 ? $("input#ite_quantidade") : '<input type="text" name="ite_quantidade" id="ite_quantidade" value="" class="campos center-block" style="width: 100px;" placeholder="Qtde" title="Qtde" onkeypress="return SomenteNumero(event)">'

            if (pro_frmmin !== null) {
                
                var qnt = parseInt(qtd / pro_frmmin)
                var opt = ""

                if (qnt > 0) {
                    
                    for (var i = 0; i < qnt; i++) {
                        opt += `<option value="${pro_frmmin * (i+1)}">${pro_frmmin * (i+1)}</option>`
                    }

                    var select = `<select name="ite_quantidade" id="ite_quantidade" class="center-block" style="width: 100px;" placeholder="Qtde" title="Qtde">
                            ${opt}
                            </select>`
                    $("input#ite_quantidade").parent().append(select).find('input[type="text"]').remove()
                }

            } else {
                
                $("select#ite_quantidade").parent().append(inp).find('select[name="ite_quantidade"]').remove()
            }
        }
    })
}



function addLinhaLoteAutomatico() {
    var cont = $(".linha_produto").length + 1;

    if ($("#pro_codigo").val() != "" && $("#ite_quantidade").val() != "") {
        var qtde = $("#ite_quantidade").val();
        $.ajax({
            url: baseUrl + "/produto/get-lote-automatico/",
            data: {
                pro_codigo: $("#pro_codigo").val(),
                quantidade: qtde,
                set_codigo: $("#set_codigo").val()
            },
            success: function (txt) {
                var arr_txt = "";
                var validador = 0;
                var produtos_faltou = "";
                var produtos = "";

                for (var i in txt) {
                    if (validador != 1) {
                        var pro_codigo = $("#pro_codigo").val();
                        var duracao = $("#ite_duracao").val();
                        var nome = $("#pro_nome").val();
                        validador = 1;
                    }
                    if (i != "faltam") {

                        arr_txt = txt[i].split("|");
                        var linha = "<tr class=\"linha_produto line_" + cont + " lote_" + replaceSpecialChars(i) + " " + pro_codigo + replaceSpecialChars(i) + "\" data-pro=\"" + pro_codigo + "\" data-lote=\"'" + i + "\" data-validade=\"" + arr_txt[1] + "\" data-saldo=\"" + arr_txt[0] + "\" data-qtde=\"" + arr_txt[0] + "\" data-duracao=\"" + duracao + "\" data-codrec=\"" + $("#ite_cod_receita").val() + "\">" +
                            "<td>" + $("#pro_codigo").val() + "</td>" +
                            "<td>" + $("#pro_nome").val() + "</td>" +
                            "<td><b>Lote</b>:" + i + " <b>Validade</b>:" + dataToBr(arr_txt[1]) + "  <b>Quantidade</b>: " + arr_txt[0] + "</td>" +
                            "<td>" + $("#ite_quantidade").val() + "</td>" +
                            ($("#validade_medicamentos").val() == 1 ? "<td>" + $("#ite_duracao").val() + "</td>" : "") +
                            "<td>" + ($("#rec_codigo").val() == "" ? "<a href=\"#\" class=\"ui-button add new ui-corner-bl ui-corner-tr\" style=\"margin-left: 5px;\" onclick=\"excluir('" + pro_codigo + replaceSpecialChars(i) + "')\"><div><img src=\"/WebSocialSaude/zf/public/images/icons/excluir2.png\"></div>Excluir</a>" : "") + "</td>" +
                            "</tr>";

                        $("#table_itens").append(linha);
                        $(".campos").val("");
                    } else {
                        var table = "<table>" +
                            "<tr>" +
                            "<th>Produto</th>" +
                            "<th>Qtde. Solicitada</th>" +
                            "<th>Qtde. Pendente</th>" +
                            "</tr>" +
                            "<tr>" +
                            "<td>" + nome + "</td>" +
                            "<td>" + $("#ite_quantidade").val() + "</td>" +
                            "<td>" + txt[i] + "</td>" +
                            "</tr>" +
                            "</table>";
                        mensagem("Alerta", "Não há estoque suficiente para os seguintes produtos:<br/><br/>" + table, 700, 300);
                    }
                }

                $("#pro_codigo").focus();
            }
        });
    } else {
        setTimeout(function () {
            mensagem("Erro", "Tem informações a serem preenchidas!", 300, 150)
        }, 500);
    }

}

function showLine(id) {
    $("#tablePrograma tbody tr.line_" + id + " td input[type='checkbox']").attr('checked', false)
    $("#tablePrograma tbody tr.line_" + id).show()
    $("#selectAll").show()
}

function excluir(pro_codigo_lote) {
    $("#table_itens ." + replaceSpecialChars(pro_codigo_lote)).remove();
    // $("#tbPrograma").show()
}

function excluirAll() {
    $("#table_itens .linha_produto").remove();
    $("#tbPrograma").hide()
}

function getLotes(pro_codigo) {
    // console.log("teste");
    if (pro_codigo == "" || pro_codigo == null) {
        pro_codigo = $("#pro_codigo").val();
    }

    $.ajax({
        url: baseUrl + "/produto/get-lotes/",
        type: "POST",
        data: {
            pro_codigo: pro_codigo,
            set_codigo: $("#set_codigo").val(),
            enviados: 1
        },
        success: function (txt) {

            if (txt.length > 0) {
                var select = "<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default\" style=\"width:350px;\">";
                for (var i in txt) {
                    var quantidade = txt[i].sal_qtde;
                    if ($("." + pro_codigo + replaceSpecialChars(txt[i].sal_lote)).data("qtde")) {
                        quantidade = (txt[i].sal_qtde - parseInt($("." + pro_codigo + replaceSpecialChars(txt[i].sal_lote)).data("qtde")));
                    }
                    var checked = "";

                    //                    if(txt[i].sal_qtde < txt[i].saldo_original){
                    //                        mensagem("Alerta","O lote:<b>"+txt[i].sal_lote+"</b> possui envios pendentes!",300,150);
                    //                    }

                    //alert($(".lote_"+replaceSpecialChars(txt[i].sal_lote)).length);
                    if ($("." + txt[i].pro_codigo + replaceSpecialChars(txt[i].sal_lote)).length < 1) {
                        select += "<option value=\"" + txt[i].sal_lote + "|" + quantidade + "|" + txt[i].sal_validade + "\"" + checked + ">Lote: " + txt[i].sal_lote + " - Val.: " + dataToBr(txt[i].sal_validade) + " - Saldo: " + quantidade + "</option>";
                    }
                }

                select += "</select>";
                $("#div_lote").html(select);

                getFracionamento(document.querySelector("#div_lote").children[0])

            } else {
                mensagem("Erro", "O produto selecionado não possui saldo!", 300, 150, function () {
                    foco();
                });
                $("#pro_codigo").val("");
                $("#pro_nome").val("");
                $("#ite_quantidade").val("");
                $("#pro_codigo").focus();
            }
        }
    });
}


function getFracionamento(elm = null) {
    var qtd = elm.value.split('|')[1]


    $.ajax({
        url: baseUrl + "/produto/get-fracionamento",
        type: "GET",
        data: {
            pro_codigo: $("#pro_codigo").val()
        },
        success: res => {
            var pro_frmmin = res.pro_frmmin
            delete res

            var inp = $("input#ite_quantidade").length > 0 ? $("input#ite_quantidade") : '<input type="text" name="ite_quantidade" id="ite_quantidade" value="" class="campos center-block" style="width: 100px;" placeholder="Qtde" title="Qtde" onkeypress="return SomenteNumero(event)">'

            if (pro_frmmin !== null) {
                var qnt = parseInt(qtd / pro_frmmin)
                var opt = ""

                if (qnt > 0) {
                    for (var i = 0; i < qnt; i++) {
                        opt += `<option value="${pro_frmmin * (i+1)}">${pro_frmmin * (i+1)}</option>`
                    }
                    var select = `<select name="ite_quantidade" id="ite_quantidade" class="center-block" style="width: 100px;" placeholder="Qtde" title="Qtde">
                                ${opt}
                            </select>`
                    $("input#ite_quantidade").parent().append(select).find('input[type="text"]').remove()
                }
            } else {
                $("select#ite_quantidade").parent().append(inp).find('select[name="ite_quantidade"]').remove()
            }
        }
    })
}

function resetIteQtd() {
    $("#ite_quantidade").parent().empty().append(`<input type="text" name="ite_quantidade" id="ite_quantidade" value="" class="campos center-block" style="width: 100px;" placeholder="Qtde" title="Qtde" onkeypress='return SomenteNumero(event)'>`)
}


function Item(pro_codigo, ite_lote, ite_validade, ite_quantidade, ite_duracao, ite_cod_receita) {
    this.pro_codigo = pro_codigo;
    this.ite_lote = ite_lote;
    this.ite_quantidade = ite_quantidade;
    this.ite_validade = ite_validade;
    this.ite_duracao = ite_duracao;
    this.ite_cod_receita = ite_cod_receita;

    return this
}

function salvarDispensa() {
    event.preventDefault();
    //setTimeout(function() { mensagem("Erro","Não há produtos selecionados",300,150) }, 500);
    var usu_codigo = $("#usu_codigo").val();
    var usr_codigo = $("#usr_codigo").val();
    var interno = $("#interno").val();
    var set_codigo = $("#set_codigo").val();
    var rec_codigo = $("#rec_codigo").val();
    var mov_data = $("#mov_data").val();

    if ($("#pro_codigo").val() != "") {
        setTimeout(function () {
            mensagem("Erro", "Há um item a ser salvo", 300, 150)
        }, 500);
        return false;
    }

    if (usu_codigo == "" || usu_codigo == null) {
        setTimeout(function () {
            mensagem("Erro", "Informe um paciente", 300, 150)
        }, 500);
        return false;
    }

    if (usr_codigo == "" || usr_codigo == null) {
        setTimeout(function () {
            mensagem("Erro", "Informe um profissional", 300, 150)
        }, 500);
        return false;
    }

    confirme("Confirme:", "Deseja realmente gerar movimentação?", 300, 150, function () {
        mensagemSemOk("carregando-ate1", "Aguarde", "Carregando...", 280, 80);

        var itens = new Array();
        var irec_codigo = "";
        if ($(".linha_produto").length == 0) {
            $("#carregando-ate1").dialog("destroy").remove();
            setTimeout(function () {
                mensagem("Erro", "Não há produtos selecionados", 300, 150)
            }, 500);
            return false;
        } else {
            var duracao = "";
            $(".linha_produto").each(function () {
                irec_codigo = $(this).data("irec");
                if (irec_codigo == "" || irec_codigo == null || irec_codigo == "undefined") {
                    duracao = $(this).data("duracao");
                } else {
                    duracao = $("#irec_duracao_" + irec_codigo).val();
                }
                itens.push(new Item($(this).data("pro"), $(this).attr("data-lote").replace("'", ""), $(this).attr("data-validade"), $(this).attr("data-qtde"), duracao, $(this).data("codrec")));
            });
        }

        $.ajax({
            url: baseUrl + "/farmacia/farmacia/salvar",
            data: {
                usu_codigo: usu_codigo,
                set_codigo: set_codigo,
                usr_codigo: usr_codigo,
                interno: interno,
                itens: itens,
                rec_codigo: rec_codigo,
                mov_data: mov_data
            },
            success: function (txt) {
                //alert(txt.id);
                if (txt.id == "" || txt.id == null || txt.id == "undefined") {
                    mensagem("Erro", txt, 300, 150);
                } else {
                    sucesso_salvar(txt.id);
                    //mensagem("Sucesso",txt.msg,300,150,function(){sucesso_salvar(txt.id);});
                }
            }
        });

        $("#carregando-ate1").dialog("destroy").remove();

    });
}

function sucesso_salvar(mov_codigo) {
    if ($("#via_medicamentos").val() == 1) {
        $("body").append("<div id=\"confirma-disp\" title=\"Confirmação:\"><br />Dados cadastrado com sucesso! <br />Deseja imprimir o comprovante de dispensação?</div>");
        $("#confirma-disp").dialog({
            modal: true,
            width: 290,
            height: 180,
            close: function () {
                window.location = baseUrl + "/farmacia/farmacia";
                $(this).remove();
            },
            buttons: {
                Sim: function () {
                    console.log("sadsadsadas");
                    window.open(baseUrl + "/farmacia/farmacia/imprimir-via/mov_codigo/" + mov_codigo, '', 'width=750,height=700');
                    $(this).dialog('close');
                },
                Não: function () {
                    $(this).dialog('close');
                    window.location = baseUrl + "/farmacia/farmacia";
                }
            }
        });
    } else {
        window.location = baseUrl + "/farmacia/farmacia";
    }
}

function retornaPac(usu_codigo, usu_nome) {

    $("#usu_codigo").val(usu_codigo);
    $("#buscar1").val(usu_nome);
    $("#pro_codigo").focus();
}

function addLinhaCodBarrasAutomatico(pro_codigo, qtde, pro_nome, irec_codigo, ite_duracao) {
    mensagemSemOk("carregando-ate2", "Aguarde", "Carregando...", 280, 80);
    var cont = $(".linha_produto").length + 1;
    if (pro_codigo != "" && qtde != "") {
        var produtos = "";
        $.ajax({
            url: baseUrl + "/produto/get-lote-automatico/",
            data: {
                pro_codigo: pro_codigo,
                quantidade: qtde,
                set_codigo: $("#set_codigo").val()
            },
            async: false,
            success: function (txt) {
                $("#carregando-ate2").dialog("destroy").remove();
                var arr_txt = "";
                var validador = 0;
                var codigo = "";
                var nome = "";
                var quantidade = "";
                var campo_duracao = "";
                var duracao = "";

                for (var i in txt) {
                    if (validador != 1) {

                        codigo = pro_codigo;
                        nome = pro_nome;
                        quantidade = qtde;
                        validador = 1;
                        duracao = ite_duracao
                        campo_duracao = "<input name=\"irec_duracao\" id=\"irec_duracao_" + irec_codigo + "\"  value=\"" + duracao + "\" class=\"campos ui-state-default\" style=\"width: 60px;\" onkeypress=\"return SomenteNumero(event)\" type=\"text\">";
                    } else {
                        codigo = "";
                        nome = "";
                        quantidade = "";
                        duracao = "";
                        campo_duracao = "";
                    }

                    if (i != "faltam") { //entra só a primeira vez no for

                        arr_txt = txt[i].split("|");
                        var linha = "<tr class=\"" + irec_codigo + " linha_produto line_" + cont + " lote_" + replaceSpecialChars(i) + " " + pro_codigo + replaceSpecialChars(i) + "\" data-pro=\"" + pro_codigo + "\" data-lote=\"'" + i + "\" data-validade=\"" + arr_txt[1] + "\" data-saldo=\"" + arr_txt[0] + "\" data-qtde=\"" + arr_txt[0] + "\" data-irec=\"" + irec_codigo + "\" data-codrec=\"" + $("#rec_codigo").val() + "\">" +
                            "<td>" + codigo + "</td>" +
                            "<td>" + nome + "</td>" +
                            "<td><b>Lote</b>:" + i + " <b>Validade</b>:" + dataToBr(arr_txt[1]) + "  <b>Quantidade</b>: " + arr_txt[0] + "</td>" +
                            "<td>" + (quantidade != "" ? "<input name=\"irec_quantidade\" id=\"irec_quantidade_" + irec_codigo + "\"  value=\"" + quantidade + "\" class=\"campos ui-state-default\" style=\"width: 60px;\" onkeypress=\"return SomenteNumero(event)\" onChange=\"alteraLinha('" + pro_codigo + "','" + pro_nome + "','" + irec_codigo + "')\" type=\"text\">" : "") + "</td>" +
                            "<td>" + ($("#validade_medicamentos").val() == 1 ? campo_duracao : "") + "<input type=\"hidden\" value=\"" + $("#rec_codigo").val() + "\" id=\"ite_cod_receita\"></td>" +
                            "<td>" + ($("#rec_codigo").val() == "" ? "<a href=\"#\" class=\"ui-button add new ui-corner-bl ui-corner-tr\" style=\"margin-left: 5px;\" onclick=\"excluir('" + pro_codigo + replaceSpecialChars(i) + "')\"><div><img src=\"/WebSocialSaude/zf/public/images/icons/excluir2.png\"></div>Excluir</a>" : "") + "</td>" +
                            "</tr>";

                        $("#table_itens").append(linha);
                    } else {
                        if (validador != 1) {

                            validador = 1;
                        }
                        //setTimeout(function() { mensagem("Erro","Não há estoque suficiente para a quantidade solicitada. Faltou:"+txt[i],300,150) }, 500);
                        //alert("Não há estoque suficiente para a quantidade solicitada."+nome+" Faltou:"+txt[i])
                        produtos += "<tr>" +
                            "<td>" + nome + "</td>" +
                            "<td>" + qtde + "</td>" +
                            "<td>" + txt[i] + "</td>" +
                            "</tr>";
                    }

                }

                $("#pro_codigo").focus();
            }
        });
        return produtos;
    } else {
        setTimeout(function () {
            mensagem("Erro", "Tem informações a serem preenchidas!", 300, 150)
        }, 500);
    }
}

function alteraLinha(pro_codigo, pro_nome, irec_codigo) {
    var quantidade = $("#irec_quantidade_" + irec_codigo).val();
    var duracao = $("#irec_duracao_" + irec_codigo).val();
    $("." + irec_codigo).remove();
    addLinhaCodBarrasAutomatico(pro_codigo, quantidade, pro_nome, irec_codigo, duracao)
}