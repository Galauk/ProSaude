$(function(){
    $("#form").validate({
		rules: {
                    area_codigo: {
                        required: true
                    },
                    mic_descricao: {
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
                    area_codigo: {
                            required: "Campo Obrigatório"
                    },
                     mic_descricao: {
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

