$(function(){
    $("#form-num-paciente-atendido-por-medicamentos").validate({
        rules:{
            data_inicial: {required:true},
            data_final: {required:true}
        },
        messages: {
            data_inicial: "Campo Obrigatório",
            data_final: "Campo Obrigatório"
        }
    });
    $("#busca_produtos").buscar({
            url: baseUrl+'/produto/medicamento-posto/',
            template : function(ul, item) {
                    return jQuery("<li></li>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(event, ui){
                    return true;
            }
    });
});