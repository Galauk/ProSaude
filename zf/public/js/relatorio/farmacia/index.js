$(function(){
    $("#form").validate({
		rules: {
            med_nome: {
                required: true
            }
		},
		messages: {
            med_nome: {
                required: "Campo Obrigatório"
            }               
		}
    });
     $("#set_nome").buscar({
        url: baseUrl+'/default/setor/buscar/set_logado/1',
        categoria: 'categoria',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
            return true;
        }
    });
});