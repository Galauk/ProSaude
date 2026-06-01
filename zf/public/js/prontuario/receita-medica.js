$(function () {
    
    
    
    $('#dialog').validate({
        rules: {
            irec_quantidade2: {
                required: true
            }
        },
        messages: {
            irec_quantidade2: {
                required: "pls enter user name"
            }
        }
    });

    $(".buscar").each(function () {
        var rel = $(this).attr("rel");
        var tt = baseUrl + "/produto/" + rel;
        $(this).buscar({
            url: baseUrl + "/produto/" + rel,
            template: function (ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function (event, ui) {
                if (ui.item.id > 0) {
                    $("#pro_codigo-" + rel).val(ui.item.data['pro_codigo']);
                    $("#umed-"+rel).html(ui.item.data['umed_nome']);
                    $("#saldo-"+rel).html('Qtd em Estoque:');
                    $("#quantidade-" + rel).select();
                    return true;
                }

                return false;
            }
        });
    });
});

function excluirItemViaInternacao(id) {
    var pegandoId = id.split('-');
    var novoId = pegandoId[1];
    var io_codigo = $("#io_codigo").val();
    var ate_codigo = $("#ate_codigo").val();
    
    if ($("#ehViaInternacao").val() == 'S') {
        mensagemSemOk("deletando-item-receita-medica", "Aguarde", "Excluindo medicamento ...", 350, 100);
        $.ajax({
            type:"POST",
            url: baseUrl + "/prontuario/receita-medica/excluir",
            data:{
                viaInternacao : true,
                id : novoId,
                ate_codigo : $("#ate_codigo").val(),
                io_codigo : $("#io_codigo").val(),
            },
            success: function(txt){
                fecharMensagemSemOk("deletando-item-receita-medica");
                window.opener.document.location = baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo;
                location.reload();
            }
        });
    }
}

function pegaIndiceAba() {
    $('#abas div').click(function () {
        var idAba = $(this).attr('id');
        var tamanhoIdAba = idAba.length;
        var indiceAba = idAba.substring(tamanhoIdAba - 1, tamanhoIdAba);

        return indiceAba;
    });
}

/*function verificaCamposObrigatorios(idAba) {

    if (idAba != 'externo') {

        $("#form_rec_" + idAba.toString()).validate({
            rules: {
                rec_validade: {
                    required: false,
                    newDataBR: true,
                    dataFutura: true
                },
                pro_codigo: {
                    required: true
                },
                irec_quantidade: {
                    required: true,
                    range: [1, 1000] // #verificar
                }
            },
            messages: {
                pro_codigo: "Informe o produto!",
                irec_quantidade: {
                    required: "Informe a quantidade!",
                    range: "Verifique a quantidade!"
                }
            }
        });
    }
    
    if ($("#io_codigo").val() > 0) {
        window.opener.document.location = baseUrl + "/leito/atendimento/index/cod/"+$("#io_codigo").val()+"/ate_codigo/"+$("#ate_codigo").val();
    }
}*/

function imprimir(duasVias, tipoMedicamento, tipoImpressao, io_codigo) {
    var rec_codigo = $("#rec_codigo").val();
    var usu_codigo = $("#usu_codigo").val();
    var _arraySelecinados = new Array();

    if ($("input." + tipoMedicamento + ":checkbox[name=imprimir]:checked").length > 0) {
        $(".produto").each(function () {
            if ($(this).attr("checked") == "checked") {
                _arraySelecinados.push($(this).val());
            }
        });
    } else {
        _arraySelecinados = null;
    }
    
    if (io_codigo > 0 ) {
        popup(baseUrl + '/prontuario/receita-medica/' + tipoImpressao + '/caminhoTipo/' + tipoMedicamento + '/selecionados/' + _arraySelecinados + '/rec_codigo/' + rec_codigo + '/seg/' + duasVias + '/io_codigo/'+ io_codigo + '/usu_codigo/' + usu_codigo , 'medicamentos', 1400, 600);
    } else {
        popup(baseUrl + '/prontuario/receita-medica/' + tipoImpressao + '/caminhoTipo/' + tipoMedicamento + '/selecionados/' + _arraySelecinados + '/rec_codigo/' + rec_codigo + '/seg/' + duasVias, 'medicamentos', 1400, 600);
    }
}

function addReceitaMedica(idUsuario, pro_codigo, idAba) {

    var rec_tipo = idAba;
    var rec_codigo = $("#rec_codigo").val();
    var ate_codigo = $("#ate_codigo").val();
    var irec_quantidade = $("input[name='irec_quantidade']").val();
    var desc_produto = $("input[name='desc_produto']").val();
    var io_codigo = $("#io_codigo").val();

    mensagemSemOk("aplicando-produto-receita-medica", "Aguarde", "Aplicando produto ...", 350, 100);
    $.ajax({
        type: 'POST',
        url: baseUrl + '/prontuario/receita-medica/salvar',
        data: {
            pro_codigo: pro_codigo,
            rec_tipo: rec_tipo,
            rec_codigo: rec_codigo,
            usu_codigo: idUsuario,
            desc_produto: desc_produto,
            irec_quantidade: irec_quantidade,
            ate_codigo: ate_codigo
        },
        success: function (data) {
            fecharMensagemSemOk("aplicando-produto-receita-medica");
            location.reload();
            window.opener.document.location = baseUrl + "/leito/atendimento/index/cod/"+io_codigo+"/ate_codigo/"+ate_codigo;
        }
    });
}

function inserirQuantidade(codigos) {

    var separaCodigos = codigos.split("/");
    var pro_codigo = separaCodigos[0];
    var idAba = separaCodigos[1];
    var idUsuario = separaCodigos[2];

    $("input[name='pro_codigo']").val(pro_codigo);

    $("#inserir_complemento").show();
    $("#inserir_complemento").dialog({
        title: 'Quantidade',
        modal: true,
        width: 250,
        height: 150,
        close: function () {
            $(this).dialog('close');
        },
        buttons: {
            Enviar: function () {
                addReceitaMedica(idUsuario, pro_codigo, idAba);
                $(this).dialog('close');
            }
        }
    });
}

function addValorQtde(valor) {
    if (valor == '') {
        alert("O campo quantidade é obrigatório e deve ser preechido!");
        $("#inserir_complemento").focus();
        $("#irec_quantidade2").focus();
    }
    $("input[name='irec_quantidade']").val(valor);
}

