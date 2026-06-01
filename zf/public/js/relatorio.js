$(function () {

    $("#form").validate({
        rules: {
            data_inicial: {
                required: true
            },
            data_final: {
                required: true
            },
            uni_codigo: {
                required: true
            }
        },
        messages: {
            data_inicial: {
                required: "Preencha a data inicial",
            },
            data_final: {
                required: "Preencha a data final",
            },
            uni_codigo: {
                required: "Selecione uma unidade",
            }
        }
    });


    $("#buscar5").buscar({
        url: baseUrl + '/default/usuarios/buscar/externo/1',
        categoria: 'categoria',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            return true;
        }
    });

    $("#set_nome").buscar({
        url: baseUrl + "/setor/buscar/set_logado/1",
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });

    $("#uni_desc").buscar({
        url: baseUrl + "/unidade/buscar",
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });

    $("#usu_nome").buscar({
        url: baseUrl + "/paciente/buscar-usuario-relatorio"
    });

    $("#pro_nome").buscar({
        url: baseUrl + "/produto/medicamento-controlados",
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });

    $("#usr_nome").buscar({
        url: baseUrl + "/default/usuarios/buscar/externo/1",
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });

    $("#proc_nome").buscar({
        url: baseUrl + "/default/procedimento/buscar",
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });

     $("#nu_ine").buscar({
        url: baseUrl + '/domicilio/psf/buscar-ine',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });



    $("#cid").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function (event, ui) {
            return true;
        }
    });

    $("#usr_nome").change(function () {
        if ($("#usr_nome").length == 0)
            $("#usr_codigo").val("");
    });


    $("#usu_nome").change(function () {
        if ($("#usu_nome").length == 0)
            $("#usu_codigo").val("");
    });




});


function buscaProdutos() {
    var set_codigo = $("#set_codigo").val();
    $(".pro_nome").buscar({
        url: baseUrl + "/produto/buscar-produtos/setor/" + set_codigo,
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });
}

function validarFiltrosRelatorioProducaoDiaria() {

    $("#relatorioProducaoDiaria").validate({
        rules: {
            data_inicial: {
                required: true
            },
            data_final: {required: true}
        },
        messages: {
            data_inicial: {
                required: "Campo Obrigatório!"
            },
            data_final: {
                required: "Campo Obrigatório!"
            }
        }
    });

}

