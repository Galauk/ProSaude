$(function () {
    $.validator.addMethod("usr_tipo_medico", function (usr_tipo_medico, element) {

        if (usr_tipo_medico == "M" || usr_tipo_medico == "E" || usr_tipo_medico == "A" || usr_tipo_medico == "D" || usr_tipo_medico == "P") {
            if ($(".esp_codigo_u").length == 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }

    }, "Informe uma especialidade");

    $.validator.addMethod("validaCpf", function (validaCpf, element) {
        //var strCPF = "06069060962"; alert(TestaCPF(strCPF))
        var cpf = $("#usr_cpf").val()
        cpf = cpf.replace(".", "").replace(".", "").replace("-", "");
        //alert(TestaCPF(cpf));
        if (TestaCPF(cpf) || cpf == "") {
            return true;
        } else {
            return false;
        }
    }, "Cpf Inválido");

    if ($("#con_codigo").val() != '') {
        $("#num_conselho").show();
    }

    $("#form").validate({
        rules: {
            usr_senha: {
                minlength: 3
            },
            usr_senha_confirm: {
                equalTo: "#usr_senha"
            },
            usr_nome: {
                required: true,
                minlength: 4
            },
            cnes_cod_cns: {
                required: true,
                minlength: 11
            },
            usr_login: {
                minlength: 4
            },
            usr_tipo_medico: {usr_tipo_medico: true},
            usr_cpf: {validaCpf: true, required: true}
        },
        messages: {
            usr_senha: {
                required: "Preencha o campo senha",
                minlength: "Coloque no minimo 3 caracteres"
            },
            usr_senha_confirm: {
                equalTo: "Não é igual o campo senha"
            },
            usr_cpf: {
                required: "Preencha o CPF",
            },
            usr_nome: {
                required: "Preencha o Nome",
                minlength: "Coloque no minimo 3 caracteres"
            },
            cnes_cod_cns: {
                required: "Preencha o CNS",
                minlength: "Coloque no minimo 15 caracteres"
            },
            usr_login: {
                minlength: "Coloque no minimo 4 caracteres"
            },
            usr_tipo_medico: "Informe uma especialidade"
        }
    });

    $('.password_strength').pstrength();


    $("#uni_desc").buscar({
        url: baseUrl + "/unidade/buscar",
        minLength: 3,
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: adicionaUnidade
    });



});

function chamaBuscaEsp() {
    $("#esp_nome").buscar({
        url: baseUrl + '/especialidade/buscar/',
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: adicionaLinhaEspecialidade
    });
}

function mostraNumeroConselho() {
    if ($("#con_codigo").val() != "") {
        $("#num_conselho").show();
    } else {
        $("#num_conselho").hide();
    }
}

function chamaBuscaSet() {
    var array_uni = new Array();
    $(".uni_codigo").each(function () {
        array_uni.push($(this).val());
    });
    $("#set_nome").buscar({
        url: baseUrl + '/setor/buscar-unidade-setor/array_uni/' + array_uni,
        //data:{array_uni:array_uni},
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: adicionaSetor
    });
}

function bloqueia() {

    if ($("#todas_unidades").is(":checked")) {
        $(".l_uni").remove();
        $(".tb_unidades").hide();
        $("#uni_desc").attr('readonly', true);
    } else {
        $("#uni_desc").attr('readonly', false);
    }

}

function adicionaUnidade() {
    if ($(".tr_" + $("#uni_codigo").val()).length == 0) {
        $(".tb_unidades").show();
        $(".tb_unidades").append("<tr class=\"tr_" + $("#uni_codigo").val() + " l_uni\">" +
                "<td >" +
                $("#uni_desc").val() + "<input type=\"hidden\" name=\"uni_codigo[]\" class=\"uni_codigo\" value=\"" + $("#uni_codigo").val() + "\">" +
                "</td>" +
                "<td >" +
                "<img style=\"cursor:pointer;\" src='" + baseUrl + "/public/images/icons/excluir2.png' id='espec_vinc_" + $("#uni_codigo").val() + "' onClick=\"deleteRow(" + $("#uni_codigo").val() + ")\">" +
                "</td>" +
                "<td >" +
                "<img style=\"cursor:pointer;\" src='" + baseUrl + "/public/images/icons/dentista.png'  title='Especialidade' alt='especialidade' onClick=\"adicionaEspecialidade(" + $("#uni_codigo").val() + ")\">" +
                "</td>" +
                "</tr>");

    }
    $("#uni_desc").val("");
    $("#uni_codigo").val("");
}

function adicionaEspecialidade(uni_codigo) {
    $("#conf-cadastro").dialog({
        modal: true,
        width: 300,
        height: 200,
        close: function () {
            $(".tb_especialidades").html("");
            $(this).dialog('close');
        },
        buttons: {
            "Realizar Vinculo": function () {
                realizarVinculoEspecialidadeUnidade(uni_codigo);
            },
            "Fechar": function () {
                $(".tb_especialidades").html("");
                $(this).dialog('close');
            }
        }
    });
}

function adicionaLinhaEspecialidade() {
    console.log('att');
    if ($(".tr_esp_" + $("#esp_codigo").val()).length == 0) {
        $(".tb_especialidades").show();
        $(".tb_especialidades").append("<tr class=\"tr_esp_" + $("#esp_codigo").val() + " l_esp\"><td >" + $("#esp_nome").val() + "<input type=\"hidden\" name=\"esp_codigo[]\" class=\"esp_codigo\" value=\"" + $("#esp_codigo").val() + "\">" +
                "<input type=\"hidden\" id=\"esp_nome_" + $("#esp_codigo").val() + "\" name=\"esp_nome[]\" class=\"esp_nome\" value=\"" + $("#esp_nome").val() + "\"></td><td ><img style=\"cursor:pointer;\" src='" + baseUrl + "/public/images/icons/excluir2.png' onClick=\"deleteRowEsp(" + $("#esp_codigo").val() + ")\"></td></tr>");
    }
    $("#esp_nome").val("");
    $("#esp_codigo").val("");
}

function realizarVinculoEspecialidadeUnidade(uni_codigo) {
    console.log('att');
    if ($(".l_esp_" + uni_codigo).length == 0)
        $("#espec_vinc_" + uni_codigo).hide();
    else
        $("#espec_vinc_" + uni_codigo).show();

    $(".esp_codigo").each(function () {
        if ($(".tr_esp_" + uni_codigo + "_" + $(this).val()).length == 0) {
            $(".tr_" + uni_codigo).after("<tr class=\"tr_esp_" + uni_codigo + "_" + $(this).val() + "  l_esp_" + uni_codigo + "\">" +
                    "<td colspan=2 style=\"background-color: #49afcd;\">" +
                    "&nbsp;&nbsp;&nbsp;&nbsp;" + $("#esp_nome_" + $(this).val()).val() + "<input type=\"hidden\" name=\"esp_codigo_u[" + uni_codigo + "]\" class=\"esp_codigo_u\" value=\"" + $(this).val() + "\">" +
                    "<input type=\"hidden\" name=\"uni_codigo_" + $(this).val() + "\" class=\"esp_codigo_u\" value=\"" + uni_codigo + "\">" +
                    "</td>" +
                    "<td style=\"background-color: #49afcd;\">" +
                    "<img style=\"cursor:pointer;\" src='" + baseUrl + "/public/images/icons/excluir2.png' onClick=\"deleteRowEsp(" + $(this).val() + "," + uni_codigo + ")\">" +
                    "</td>" +
                    "</tr>");
        }
    });
}

function adicionaSetor() {
    if ($(".tr_esp_" + $("#esp_codigo").val()).length == 0) {
        $(".tb_setores").show();
        $(".tb_setores").append("<tr class=\"tr_set_" + $("#set_codigo").val() + " l_esp\"><td >" + $("#set_nome").val() + "<input type=\"hidden\" name=\"set_codigo[]\" value=\"" + $("#set_codigo").val() + "\"></td><td ><img style=\"cursor:pointer;\" src='" + baseUrl + "/public/images/icons/excluir2.png' onClick=\"deleteRowSet(" + $("#set_codigo").val() + ")\"></td></tr>");
    }
    $("#set_nome").val("");
    $("#set_codigo").val("");
}

function deleteRow(uni_codigo) {
    $(".tr_" + uni_codigo).remove();
    if ($("table tr").length == 0) {
        $(".tb_unidades").hide();
    }
}

function deleteRowEsp(esp_codigo, uni_codigo) {
    $(".tr_esp_" + esp_codigo).remove();
    if (uni_codigo) {
        if ($(".l_esp_" + uni_codigo).length == 0)
            $("#espec_vinc_" + uni_codigo).hide();
        else
            $("#espec_vinc_" + uni_codigo).show();

        $(".tr_esp_" + uni_codigo + "_" + esp_codigo).remove();
    }

    if ($("table tr").length == 0) {
        $(".tb_especialidades").hide();
    }
}


function deleteRowSet(set_codigo) {
    $(".tr_set_" + set_codigo).remove();
    if ($("table tr").length == 0) {
        $(".tb_setores").hide();
    }
}

function verificaExistente() {
    if ($("#usr_login").val().length >= 3) {
        $.ajax({
            url: baseUrl + "/default/usuarios/login/",
            type: "POST",
            data: {
                term: $("#usr_login").val()
            },
            success: function (verificaExistente) {
                if (!verificaExistente) {
                    $("#login_existente").html("<font color=\"red\"><b>Login já existe</b></font>");
                    $("#login_invalido").val(verificaExistente);
                } else {
                    $("#login_existente").html("<font color=\"green\"><b>Login Válido</b></font>");
                }
            }
        });
    } else {
        $("#login_existente").html("<font color=\"blue\"><b>Coloque no minimo 3 Caracteres</b></font>");
    }
}

function deleteUnu(unu_codigo, uni_codigo) {
    $.ajax({
        url: baseUrl + "/usuarios/usuarios/excluir-unidade",
        type: "POST",
        data: {
            id: unu_codigo
        },
        success: function (txt) {
            deleteRow(uni_codigo);
        }
    });
}

function deleteMes(mes_codigo, esp_codigo, uni_codigo) {
    $.ajax({
        url: baseUrl + "/usuarios/usuarios/excluir-especialidade",
        type: "POST",
        data: {
            id: mes_codigo
        },
        success: function (txt) {
            deleteRowEsp(esp_codigo, uni_codigo);
        }
    });
}

function deleteUset(uset_codigo, set_codigo) {
    $.ajax({
        url: baseUrl + "/usuarios/usuarios/excluir-setores",
        type: "POST",
        data: {
            id: uset_codigo
        },
        success: function (txt) {
            deleteRowSet(set_codigo);
        }
    });
}



function validador_cns(vlr_cns) {
    if ((vlr_cns.substring(0, 1) != "7") && (vlr_cns.substring(0, 1) != "8") && (vlr_cns.substring(0, 1) != "9")) {
        validaCNS(vlr_cns);
    } else {
        ValidaCNS_PROV(vlr_cns);
    }
}


function validaCNS(vlrCNS) {
    // Formulário que contem o campo CNS
    var soma = new Number;
    var resto = new Number;
    var dv = new Number;
    var pis = new String;
    var resultado = new String;
    var tamCNS = vlrCNS.length;
    if ((tamCNS) != 15) {
        mensagem("Atenção", "Numero de CNS invalido", 300, 150);
        return false;
    }
    pis = vlrCNS.substring(0, 11);
    soma = (((Number(pis.substring(0, 1))) * 15) +
            ((Number(pis.substring(1, 2))) * 14) +
            ((Number(pis.substring(2, 3))) * 13) +
            ((Number(pis.substring(3, 4))) * 12) +
            ((Number(pis.substring(4, 5))) * 11) +
            ((Number(pis.substring(5, 6))) * 10) +
            ((Number(pis.substring(6, 7))) * 9) +
            ((Number(pis.substring(7, 8))) * 8) +
            ((Number(pis.substring(8, 9))) * 7) +
            ((Number(pis.substring(9, 10))) * 6) +
            ((Number(pis.substring(10, 11))) * 5));
    resto = soma % 11;
    dv = 11 - resto;
    if (dv == 11) {
        dv = 0;
    }
    if (dv == 10) {
        soma = (((Number(pis.substring(0, 1))) * 15) +
                ((Number(pis.substring(1, 2))) * 14) +
                ((Number(pis.substring(2, 3))) * 13) +
                ((Number(pis.substring(3, 4))) * 12) +
                ((Number(pis.substring(4, 5))) * 11) +
                ((Number(pis.substring(5, 6))) * 10) +
                ((Number(pis.substring(6, 7))) * 9) +
                ((Number(pis.substring(7, 8))) * 8) +
                ((Number(pis.substring(8, 9))) * 7) +
                ((Number(pis.substring(9, 10))) * 6) +
                ((Number(pis.substring(10, 11))) * 5) + 2);
        resto = soma % 11;
        dv = 11 - resto;
        resultado = pis + "001" + String(dv);
    } else {
        resultado = pis + "000" + String(dv);
    }
    if (vlrCNS != resultado) {
        mensagem("Atenção", "Numero de CNS invalido", 300, 150);
        $("#cnes_cod_cns").val("");
        return false;
    } else {
        return true;
    }
}

function ValidaCNS_PROV(Obj)
{
    var pis;
    var resto;
    var dv;
    var soma;
    var resultado;
    var result;
    result = 0;

    pis = Obj.substring(0, 15);

    if (pis == "")
    {
        return false
    }

    if ((Obj.substring(0, 1) != "7") && (Obj.substring(0, 1) != "8") && (Obj.substring(0, 1) != "9"))
    {
        mensagem("Atenção", "Numero de CNS invalido", 300, 150);
        $("#cnes_cod_cns").val("");
        return false
    }

    soma = ((parseInt(pis.substring(0, 1), 10)) * 15)
            + ((parseInt(pis.substring(1, 2), 10)) * 14)
            + ((parseInt(pis.substring(2, 3), 10)) * 13)
            + ((parseInt(pis.substring(3, 4), 10)) * 12)
            + ((parseInt(pis.substring(4, 5), 10)) * 11)
            + ((parseInt(pis.substring(5, 6), 10)) * 10)
            + ((parseInt(pis.substring(6, 7), 10)) * 9)
            + ((parseInt(pis.substring(7, 8), 10)) * 8)
            + ((parseInt(pis.substring(8, 9), 10)) * 7)
            + ((parseInt(pis.substring(9, 10), 10)) * 6)
            + ((parseInt(pis.substring(10, 11), 10)) * 5)
            + ((parseInt(pis.substring(11, 12), 10)) * 4)
            + ((parseInt(pis.substring(12, 13), 10)) * 3)
            + ((parseInt(pis.substring(13, 14), 10)) * 2)
            + ((parseInt(pis.substring(14, 15), 10)) * 1);

    resto = soma % 11;

    if (resto == 0)
    {
        return true;
    }
    else
    {
        mensagem("Atenção", "Numero de CNS invalido", 300, 150);
        $("#cnes_cod_cns").val("");
        return false;
    }
}

