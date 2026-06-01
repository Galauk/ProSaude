$(function(){
    $("form a.salvar-proc").bind("click",function(e){
        
        e.preventDefault();
        e.stopPropagation();
                
        confirme("Aviso","Os procedimentos cadastrados manualmente não será contado no faturamento do BPA! Deseja salvar?",350,150,enviar);
    });
    
    $("#proc_nome").buscar({
        url: baseUrl + '/procedimento/buscar',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });
    
    $("#form").validate({
        rules: {
            proc_codigo: {
                    required: true
            },
            proc_apelido:{
                required: true
            }
        },
        messages: {
            proc_codigo: {
                required: "Campo Obrigatório"
            },
            proc_apelido: {
                required: "Campo Obrigatório"
            }
        }
    });
})

function enviar(){
    $("form").trigger("submit");
}