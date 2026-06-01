$(function(){
    $(".salvar").click(function(){
        //var cookieValue = $.cookie("test");
        
        
    });
    
    $("#form").validate({
		rules: {
                    area_desc: {
                            required: true
                    },
                    usr_nome:{
                        required: true
                    },
                    usr_codigo:{
                        required: true
                    }
			
		},
		messages: {
                    area_desc: {
                            required: "Campo Obrigatório"
                    },
                    usr_codigo: {
                            required: "Selecione uma opção"
                    },
                    usr_nome: {
                            required: "Campo Obrigatório"
                    }
                        
		}
	});
        
    $("#usr_nome").buscar({
        url: baseUrl+"/usuarios/buscar",
                        template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        }
    });
});



