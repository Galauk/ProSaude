$(document).ready(function () {

        $('#menorSeis').hide();
        $('#maiorSeis').hide();
        $('#maiorDois').hide();

    $("#cons-alimentar").validate({
        rules: {
            uni_codigo: {required: true},
            usr_codigo: {required: true},
            usu_codigo: {required: true},
            co_local_atend: {required: true},
            data_atendimento: {required: true}
        },
        messages: {
            uni_codigo: {required: "(*)Obrigatório"},
            usr_codigo: {required: "(*)Obrigatório"},
            usu_codigo: {required: "(*)Obrigatório"},
            co_local_atend: {required: "(*)Obrigatório"},
            data_atendimento: {required: "(*)Obrigatório"}
        }
    });

        $("#botaoPaciente").click(function () {
            var usu_codigo = $("#usu_codigo").val();
            if (usu_codigo != "") {
                var link = baseUrl + "/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
            } else {
                link = baseUrl + "/default/paciente/form-paciente/poupup/1";
            }
            window.open(link, "name", "scrollbars=1,height=800,width=900", 'width=850,height=700');
        });

        $("#form-busca").validate({
            rules: {
                tipo_busca: {required: true},
                busca: {required: true},
            },
            messages: {
                tipo_busca: {required: "(*)Obrigatório"},
                busca: {required: "(*)Obrigatório"}
            }
        });

        $("#form-upload").uploaded(
            habilitaPerguntas()
        );

    }
);

function trataValor() {
    var val22_check = $('input[name=maiorSeis22]:checked').val();
    var val24_check = $('input[name=maiorSeis24]:checked').val();
    if (val22_check == 1) {
        $('input[name=maiorSeis23]').attr("disabled", false);
    } else {
        $('input[name=maiorSeis23]').attr("disabled", true);
        $('input[name=maiorSeis23]').attr("checked", false);
    }
    ;
    if (val24_check == 1) {
        $('input[name=maiorSeis25]').attr("disabled", false);
        $('input[name=maiorSeis26]').attr("disabled", false);
    } else {
        $('input[name=maiorSeis25]').attr("disabled", true);
        $('input[name=maiorSeis25]').attr("checked", false);
        $('input[name=maiorSeis26]').attr("disabled", true);
        $('input[name=maiorSeis26]').attr("checked", false);
    }
    ;
};

function calcularIdadePorMeses() {

    var parts = $('#data_nasc').val().split('/');
    var dtAniversario = new Date(parts[2], parts[1] - 1, parts[0]);

    var parts = $('#data_atendimento').val().split('/');
    var dtInformada = new Date(parts[2], parts[1] - 1, parts[0]);

    if (dtInformada)
        dtInformada = new Date(dtInformada);
    else
        dtInformada = new Date();

    var age = [], dtAniversario = new Date(dtAniversario),
        y = [dtInformada.getFullYear(), dtAniversario.getFullYear()],
        ydiff = y[0] - y[1],
        m = [dtInformada.getMonth(), dtAniversario.getMonth()],
        mdiff = m[0] - m[1],
        d = [dtInformada.getDate(), dtAniversario.getDate()],
        ddiff = d[0] - d[1];

    if (mdiff < 0 || (mdiff === 0 && ddiff < 0)) --ydiff;
    if (mdiff < 0) mdiff += 12;
    if (ddiff < 0) {
        dtAniversario.setMonth(m[1] + 1, 0);
        ddiff = dtAniversario.getDate() - d[1] + d[0];
        --mdiff;
    }
    age.push((ydiff * 12) + mdiff);

    return age.join('');
}

function habilitaPerguntas() {
    if ($('#data_nasc').val() != "" && $('#data_atendimento').val() != "") {
        var meses = calcularIdadePorMeses();
        if (meses <= 6) {
            $(".inputMaiorSeis").attr("checked", false);
            $(".inputMaiorDois").attr("checked", false);

            $(".inputMaiorSeis").attr("disabled", true);
            $(".inputMaiorDois").attr("disabled", true);
            $(".inputMenorSeis").attr("disabled", false);

            $('#menorSeis').show();
            $('#maiorSeis').hide();
            $('#maiorDois').hide();
        }

        if (meses > 6 && meses < 24) {
            $(".inputMenorSeis").attr("checked", false);
            $(".inputMaiorDois").attr("checked", false);

            $(".inputMenorSeis").attr("disabled", true);
            $(".inputMaiorDois").attr("disabled", true);
            $(".inputMaiorSeis").attr("disabled", false);

            $('#menorSeis').hide();
            $('#maiorSeis').show();
            $('#maiorDois').hide();
            trataValor();
        }

        if (meses >= 24) {
            $(".inputMaiorSeis").attr("checked", false);
            $(".inputMenorSeis").attr("checked", false);

            $(".inputMaiorSeis").attr("disabled", true);
            $(".inputMenorSeis").attr("disabled", true);
            $(".inputMaiorDois").attr("disabled", false);

            $('#menorSeis').hide();
            $('#maiorSeis').hide();
            $('#maiorDois').show();
        }
    }
}


function buscaCidadao() {
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
    var ativCol = $("#ativCol").val();
    $("#" + idNome).buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl + '/paciente/buscar',
        callback: function (event, ui) {
            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var nomePai = ui.item.data.usu_pai;
            var dtNasc = ui.item.data.usu_datanasc;
            if ((cns != "" && cns != null && cns != "undefined") && (validaCnsDigitado(cns) == "true") && (validaEspacoNome(nome) == "true") && (validaEspacoNomeMae(nomeMae) == "true")) {
                if (idNome != "" && idNome != "null" && idNome != "undefined") {
                    $("#" + idNome).val(nome);
                }
                if (idCodigo != "" && idCodigo != "null" && idCodigo != "undefined") {
                    $("#" + idCodigo).val(usuCodigo);
                }
                if (idData != "" && idData != "null" && idData != "undefined") {
                    $("#" + idData).val(dtNasc);
                    $("#data_nasc").val(dtNasc);
                }
                if (idButton != "" && idButton != "null" && idButton != "undefined") {
                    $("#" + idButton).show();
                }
                // A - Agendamento
                if (tipo == 'A') {
                    carregarHistoricoDoPaciente();
                }
                // O - Ficha Odontológica
                if (tipo == 'O') {
                    carregarDadosEspeciaisPaciente(usuCodigo);
                }
            } else {
                atualizaCnsParticipante(usuCodigo, idNome, idData, ativCol);
            }
            habilitaPerguntas();
        }
    });
}

function buscaProfissional() {
    var tipo_atend = $("#tipo_atendimento").val();
    var uni_codigo = $("#uni_codigo").val();
    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar/tipo/' + tipo_atend + '/unidade/' + uni_codigo + '/',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            carregaEspecialidade();
        }
    });
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

function carregaEspecialidade(usrCodigo) {
    $("#profs_part_esp option").remove();
    $("#td_profs_part_esp").show();
    $("#td_profs_part_conf").show();
    $.ajax({
        url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional",
        type: "POST",
        data: {
            usrCodigo: usrCodigo
            //uni_codigo: $("#uni_codigo").val()
        },
        success: function (txt) {
            $.each(txt, function (key, value) {
                $("#profs_part_esp").append("<option title=\"" + value['esp_nome'] + "\" value=\"" + value['cod_cbo'] + "\">" + value['esp_nome'] + "</option>");
            })
        }
    });
}

function validaData(e) {
    if ($(e).val().length > 0) {
        if (VerificaData(e)) {
            return true;
        } else {
            $("#dt_atividade").val("");
            setTimeout(function () {
                $('#dt_atividade').focus()
            }, 500);
            $("#dt_atividade").focus();
        }
    }
}

function validaDataValidate() {
    var data = $("#dt_atividade").val();
    var dataFormatada = data.split('/');
    var dataFormatada = new Date(dataFormatada[2], dataFormatada[1] - 1, dataFormatada[0]);
    var dataHoje = new Date();

    if (dataFormatada <= dataHoje) {
        $("#data_valida").val(true);
    } else {
        $("#data_valida").val('');
    }
}

function validaDataAtendimento() {
    var dataAtual = $('#data_atual').val();
    var dataAtualFormatada = dataAtual.split('/');
    var dataAtualFormatada = new Date(dataAtualFormatada[2], dataAtualFormatada[1] - 1, dataAtualFormatada[0]);

    var mesAtual = dataAtualFormatada.getMonth();
    var diaAtual = dataAtualFormatada.getDate();
    var anoAtual = dataAtualFormatada.getFullYear();

    var dataMinima = new Date(anoAtual, mesAtual - 12, diaAtual);

    var dataInformada = $("#dt_atividade").val();
    var dataInformadaFormatada = dataInformada.split('/');
    var dataInformadaFormatada = new Date(dataInformadaFormatada[2], dataInformadaFormatada[1] - 1, dataInformadaFormatada[0]);

    if (dataInformadaFormatada < dataMinima) {
        alert('A data do atendimento deve constar dentro do período de 1 ano!');
        $("#dt_atividade").val('');
        $("#dt_atividade").focus();
    }
}

function verificaTamanho(tamanho) {

    var valorCampo = $("#ds_local").val();

    if (valorCampo.length >= tamanho) {
        alert("O campo suporta no máximo " + tamanho + " caracteres!");
        var retorno = valorCampo.substr(0, tamanho - 1);
        $("#ds_local").val(retorno);
        $("#ds_local").focus();
        return false;
    }
}

function verificaTecla(e) {
    if (e.keyCode) {
        var tecla = e.keyCode;
    } else if (e.which) {
        var tecla = e.which;
    }

    if (tecla != 8) { // 8 = backspace
        verificaTamanho(250);
    }

}


function salvar() {
    var verifica = 0;
    if ($("#tab-menorSeis").is(':visible')) {
        $("#tab-menorSeis :input").each(function (e) {
            if (!$("input[type='radio'][name=" + this.name + "]").is(':checked')) {
                verifica++;
            }
        }, verifica);
    }

    if ($("#tab-maiorSeis").is(':visible')) {
        $("#tab-maiorSeis :input").each(function (e) {
            if ((!$("input[type='radio'][name=" + this.name + "]").is(':checked')) &&
                (!$("input[type='radio'][name=" + this.name + "]").is(':disabled'))) {
                verifica++;
            }
        }, verifica);
    }

    if ($("#tab-maiorDois").is(':visible')) {
        $("#tab-maiorDois :input").each(function (e) {
            if (!$("input[type='radio'][name=" + this.name + "]").is(':checked')) {
                verifica++;
            }
            if (!$('#checkboxMaiorDois:checked').val())
            {
                verifica++;
            }
        }, verifica);
    }

    if (verifica == 0) {
        $("#cons-alimentar").submit();
    } else {
        alert('É necessário responder todas as perguntas!');
    }
}
