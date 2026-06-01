$(function () {

    exibeInfoPaciente();

    //($('#usu_codigo').val());
   // checaSexoGestante();

    $("#tipoBusca").change(function () {
        if ($(this).val() == '2') {
            $('#busca').hide();
            $('#busca2').show();
        } else {
            $('#busca2').hide();
            $('#busca').show();
        }
    });

    carregaEspecialidade();

    $("tr:odd").addClass("odd");

    $("#form-busca").validate({
        rules: {
            tipo_busca: {required: true},
        },
        messages: {
            tipo_busca: {required: "(*)Obrigatório"}
        }
    });

    $("#usu_codigo").live('input', function () {
        if(usu_codigo === undefined){
        }else{
            console.log("usu_codigo = "+usu_codigo);
            $.ajax({
                url: baseUrl + '/default/usuarios/getSexo/usu_codigo/' + usu_codigo,
                success: function (result) {
                    $("#usu_sexo").val(result);
                }
            });
        }
    });




    $("#form_complementar").validate({
        rules: {

            usu_codigo: {required: true},
            usr_codigo: {required: true},
            esp_codigo: {required: true},
            uni_codigo: {required: true},
              efc_data: {required: true}

        },
        messages: {

            usu_codigo: {required: "Selecione um Paciente."},
            usr_codigo: {required: "Selecione um Profissional."},
            esp_codigo: {requires: "Selecione uma Especialidade."},
            uni_codigo: {required: "Campo obrigatório."},
              efc_data: {required: "Data é obrigatória"}

        }
    });




    $(".paciente").click(function () {
        var usu_codigo = $("#usu_codigo").val();
        if (usu_codigo > 1) {
            var link = baseUrl + "/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
        } else {
            link = baseUrl + "/default/paciente/form-paciente/poupup/1";
        }
        window.open(link, "name", "scrollbars=1,height=800,width=900", 'width=850,height=700');
    });


    if ($("#usr_nome").val() != "") {
        carregaIne();
    };

});

function buscaPaciente() {
    var tipo_busca = $("#tipo_busca").val();
    $("#usu_nome").buscar({
        url: baseUrl + '/paciente/buscar/tipo_busca/' + tipo_busca,
        callback: function () {
            return true;
        }
    });
}

function carregaEspecialidade() {
    if ($("#usr_codigo").val() && $("#uni_codigo").val()) {
        $("#especialidade").show();
        $.ajax({
            url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional",
            type: "POST",
            data: {
                usrCodigo: $("#usr_codigo").val(),
                uni_codigo: $("#uni_codigo").val()
            },
            success: function (txt) {
                $("#esp_codigo").html("");
                $.each(txt, function (key, value) {
                    if (value['esp_codigo'] == $("#esp_codigo_editar").val()) {
                        $("#esp_codigo").append("<option selected = '" + "selected" + "' title=\"" + value['esp_nome'] + "\" value=\"" + value['esp_codigo'] + "\">" + value['esp_nome'] + "</option>");
                    } else {
                        $("#esp_codigo").append("<option title=\"" + value['esp_nome'] + "\" value=\"" + value['esp_codigo'] + "\">" + value['esp_nome'] + "</option>");
                    }
                });
            }
        });
    }
}

function buscaUnidade() {
    $("#uni_desc").buscar({
        url: baseUrl + "/unidade/buscar",
        minLength: 3,
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });
}

function buscaProfissional() {

    var uni_codigo = $("#uni_codigo").val();
    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar/unidade/' + uni_codigo + '/',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            carregaEspecialidade();
            carregaIne();
        }
    });

}

function validaTipoConsulta() {
    $("#conf_tc").val("1");
}

function retornaPac(usu_codigo, usu_nome) {
    $("#usu_codigo").val(usu_codigo);
    $("#usu_nome").val(usu_nome);
}

function carregaIne() {
    setTimeout(function () {
        $("#cod_equipe option").remove();
        $("#cod_equipe").show();
        $.ajax({
            url: baseUrl + "/default/usuarios/carrega-equipes",
            type: "POST",
            data: {
                uni_codigo: $("#uni_codigo").val(),
                usr_codigo: $("#usr_codigo").val()
            },
            success: function (txt) {
                if (txt.length > 0) {
                    $("#equipe").show();

                    var codIne = $("#cod_equipe_ine").val();
                    $.each(txt, function (key, value) {
                        var selectedIne = '';
                        if (codIne == value['co_seq_equipe']) {
                            selectedIne = "selected='selected'";
                        }
                        $("#cod_equipe").append("<option " + selectedIne + " value=\"" + value['co_seq_equipe'] + "\">" + value['nu_ine'] + " - " + value['no_equipe'] + "\</option>");
                    })
                } else {

                    $("#equipe").hide();

                }
            }
        });
    }, 150);
}

function getIdade() {
    var parts = $('#datanascimento').val().split('/');
    var dataNascimento = new Date(parts[2], parts[1] - 1, parts[0]);

    var parts = $('#dataatual').val().split('/');
    var dataAtual = new Date(parts[2], parts[1] - 1, parts[0]);

    var idade = dataAtual.getFullYear() - dataNascimento.getFullYear();
    var m = dataAtual.getMonth() - dataNascimento.getMonth();
    if (m < 0 || (m = 0 && dataAtual.getDate() < dataNascimento.getDate())) {
        idade--;
    }
    return idade;
}

function exibeEstratificacaoCheckbox(idCheckbox, idDiv, idCampoRisco) {
    if ($("#" + idCheckbox).is(':checked')) {
        $("#" + idDiv).show();
    } else {
        $("#" + idDiv).hide();
        $("#" + idCampoRisco).val("");
    }
}

function exibeEstratificacaoIdade() {
    if (getIdade() >= 60) {
        $("#estrat_idoso").show();
        $("#estrat_crianca").hide();
        $("#risco_crianca").val("");
    } else if (getIdade() < 1 && $('#usr_tipo_medico').val() !== 'P') {
        $("#estrat_idoso").hide();
        $("#risco_idoso").val("");
        $("#estrat_crianca").show();
    } else {
        $("#estrat_idoso").hide();
        $("#risco_idoso").val("");
        $("#estrat_crianca").hide();
        $("#risco_crianca").val("");
    }
}

function exibeInfoPaciente() {
    if ($('#usu_codigo').val() == '') {
        $('#dados_paciente').hide();
    } else {
        $('#dados_paciente').show();
    }
}

function buscaResponsavel() {


    var tipo_busca = $("#tipo_busca").val();

    $("#usu_responsavel_nome").buscarResponsavel({
        minLength: 3,
        url: baseUrl + '/paciente/buscar/tipo_busca/' + tipo_busca,
            callback: function () {
            return true;
            }
        });


}

function validaExames(e) {
    var olhinho = $('#efc_data_olhinho').val();
    var fundo = $('#efc_data_fundo').val();
    var orelhinha = $('#efc_data_orelhinha').val();
    var transfontanela = $('#efc_data_transfontanela').val();
    var tomografia = $('#efc_data_tomografia').val();
    var ressonancia = $('#efc_data_ressonancia').val();



    if (olhinho == "" && fundo == "" && orelhinha == "" && transfontanela == "" && tomografia == "" && ressonancia == "") {
        alert('Obrigatório preencher um resultado e uma data de exame!');
        e.preventDefault();
        return false;
    }

}
