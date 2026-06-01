$(function(){
    $("#form_bpa").validate({
        rules: {
            data_inicial: {
                required: true
            },
            data_final: {
                required: true
            },
            competencia: {
                required: true
            }
        },
        messages: {
            data_inicial: {
                required: "Preencha a Data Inicial!"
            },
            data_final: {
                required: "Preencha a Data Final!"
            },
            competencia: {
                required: "Preencha a Competência!"
            }
        }
    });
     $("#med_nome").buscar({
            url: baseUrl+'/agenda/convenio/buscar/todos/1',
            categoria: 'categoria',
            template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(){
                    return true;
            }
    });
    
    $("#competencia").mask("99/9999");  
  
    $("#local_nome").buscar({
            url: baseUrl+'/relatorio/bpa/buscar/tipo/'+$("#tipo").val(),
            categoria: 'categoria',
            template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(){
                    return true;
            }
    });
});

function limpaLocal() {
    $('#limpar').click( function(e) {
        e.preventDefault();
        e.stopPropagation();
        var resultado = "";
        $('#codigo_convenio').val(resultado);
        $('#prestador_servico').val(resultado);
        $('#med_nome').val(resultado);
    });
}