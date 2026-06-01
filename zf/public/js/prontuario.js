$(function () {
    
    $("#finalizar").click(function (e) {
        e.preventDefault()
        var url = $(this).attr("href")
        var ate = $(this).data("ate")
        var imprimir = $(this).data("imprimir")
        var ate_encaminhamento = $("#ate_encaminhamento").val()
        var pc = $("#pc").val()
        var age_atendido = $("#age_atendido").val()
        var age_codigo = $("#age_codigo").val()

        $("body").append("<div id=\"finalizar-dialog\" title=\"Finalizar\">Deseja realmente finalizar este atendimento?</div>")

        $("#finalizar-dialog")
            .dialog({
                modal: true,
                width: 405,
                height: 225,
                close: function () {
                    $(this).remove()
                },
                buttons: [
                    {
                        id: "button-finalizar",
                        text: "Finalizar",
                        click: function () {
                            $.ajax({
                                url: baseUrl + "/prontuario/agenda-do-dia/finalizar",
                                data: {
                                    age: age_codigo
                                },
                                type: "GET",
                                success: function () {
                                    if (typeof ate != "undefined") {
                                        $.cookie("ate_reclamacao", "")
                                        $.cookie("ate_exame_fisico", "")
                                        $.cookie("ate_diagnostico", "")
                                        $.cookie("ate_tratamento", "")
                                        $.cookie("ate_curativo", "")
                                    }

                                    if (imprimir) {
                                        $("body").append("<div id=\"imprimi-dialog\" title=\"Impressão de Prontuário\"></div>")
                                        $("#imprimi-dialog")
                                            .html("Deseja realmente finalizar e imprimir este atendimento?")
                                            .dialog({
                                                modal: true,
                                                width: 400,
                                                height: 160,
                                                buttons: {
                                                    Sim: function () {
                                                        popup(baseUrl + "/prontuario/ficha/atendimento/ate/" + ate, "imprimir-atendimento", 835, 500)
                                                        location.href = baseUrl + "/prontuario/agenda-do-dia"
                                                    },
                                                    Não: function () {
                                                        location.href = baseUrl + "/prontuario/agenda-do-dia"
                                                    }
                                                }
                                            })
                                    } else {
                                        window.location.href = baseUrl + "/prontuario/agenda-do-dia"
                                    }
                                }
                            })
                            $(this).dialog('close')
                        }
                    },
                    {
                        id: "button-retorno",
                        text: "Retorno",
                        click: function () {
                            $.ajax({
                                url: baseUrl + "/prontuario/agenda-do-dia/retorno",
                                data: {
                                    age: age_codigo,
                                    retorno: "S",
                                    ate_codigo: ate
                                },
                                type: "GET",
                                success: function () {
                                    location.href = baseUrl + "/prontuario/agenda-do-dia"
                                }
                            });
                            $(this).dialog("close")
                        }
                    },
                    {
                        id: "button-cancelar",
                        text: "Cancelar",
                        click: function () {
                            $(this).dialog("close")
                        }
                    }
                ]
            });
        if (pc == "S" && ate_encaminhamento == "") {
            $("#button-retorno").hide();
        }
    })
})

function trim(str) {
    if (str != null) {
        return str.replace(/^\s+|\s+$/g, "")
    }
}


function chamar(age_codigo) {
    $.ajax({
        url: baseUrl + "/guiche/chamar/",
        data: {
            age_codigo: age_codigo
        },
        type: "GET",
        success: function (txt) {
            var dados = txt
            console.log(dados)
               
}
    })
}