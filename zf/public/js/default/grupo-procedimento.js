$(function () {
    
    $("#grupo_proc").validate({
        rules: {
            gp_descricao: {required: true},
            proc_codigo: {required: true}
        },
        messages: {
            gp_descricao: {required: "Campo obrigatório."},
            proc_codigo: {required: "Selecione um Procedimento."}
        }
    });

    $("#gp_codigo").ready(function(){

        console.log($("#gp_codigo").val());
        if($("#gp_codigo").val() != null && $("#gp_codigo").val() != ""){

            //Inserir os procedimentos salvos puxados para edição
            $.ajax({
                url: baseUrl + "/grupo-procedimento/procedimentos-grupo-ajax",
                type: "POST",
                data: {
                    gp_codigo: $("#gp_codigo").val()
                },
                success: function (txt) {
                    
                    $.each(txt, function (key, value) {
                        console.log(value['co_gp_codigo']);
                        $("#dadosProcAtendSimp").append("\
                            <div class='procAtendSimp' id='procAtendSimp" + value['proc_codigo'] +"-"+ $(".procAtendSimp").length + "'>\n\
                                <span class='titProcAtendSimp'>\n\
                                    " + value['proc_codigo'] + " - " + value['proc_nome'].substr(0, 32) + " ...\n\
                                </span>\n\
                                <div class='excProcAtendSimp'>\n\
                                    <img src='" + baseUrl + "/public/images/icons/excluir.png' onclick=\"excluiProcedimento('" + value['proc_codigo'] +"-"+ $(".procAtendSimp").length + "')\" title='Excluir Horários' alt='Clique aqui para excluir os horários do dia' style='cursor: pointer' />\n\
                                    <input type='hidden' name='procedimento[]' value='" + value['proc_codigo'] + "' />\n\
                                    <input type='hidden' name='co_gp_codigo[" + value['proc_codigo'] + "]' value='" + value['co_gp_codigo'] + "' />\n\
                                </div>\n\
                            </div>");
                    })
                }
            });
        }
        limparCampos();
    });
});


function buscaProcedimentos(){
    $("#proc_nome").buscar({
        url: baseUrl + "/procedimento/buscar/",
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            adicionaProcedimentos();
            limparCampos();
        }
    });
}

// Pega o procedimento e o código selecionado e coloca o valor em um campo 
// oculto para realizar a inserção
function adicionaProcedimentos() {
    //alert($("#procAtendSimp"+$("#proc_codigo").val()).length);
        $("#dadosProcAtendSimp").append("\
            <div class='procAtendSimp' id='procAtendSimp" + $("#proc_codigo").val()+"-"+ $(".procAtendSimp").length + "'>\n\
                <span class='titProcAtendSimp'>\n\
                    " + $("#proc_codigo_sus").val() + " - " + $("#proc_nome").val().substr(0, 32) + " ...\n\
                </span>\n\
                <div class='excProcAtendSimp'>\n\
                    <img src='" + baseUrl + "/public/images/icons/excluir.png' onclick=\"excluiProcedimento('" + $("#proc_codigo").val() +"-"+ $(".procAtendSimp").length + "')\" title='Excluir Horários' alt='Clique aqui para excluir os horários do dia' style='cursor: pointer' />\n\
                    <input type='hidden' name='procedimento[]' value='" + $("#proc_codigo").val() + "' />\n\
                </div>\n\
            </div>");

 }

 function limparCampos() {
    $("#proc_nome").val("");
    $("#proc_codigo").val("0");
}

function excluiProcedimento(procCodigo) {
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#procAtendSimp" + procCodigo).remove();
    });
}