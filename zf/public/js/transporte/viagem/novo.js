$(function(){
    
     $("#form").validate({
		rules: {
                    via_data: {
                            required: true
                    },
                    vei_codigo:{
                        required: true
                    },
                    usr_nome:{
                        required: true
                    }
			
		},
		messages: {
                    via_data: {
                            required: "Campo Obrigatório"
                    },
                    vei_codigo: {
                            required: "Campo Obrigatório"
                    },
                    usr_nome: {
                            required: "Campo Obrigatório"
                    }
                        
		}
	});
    
    $("#usr_nome").buscar({
        url: baseUrl+"/default/usuarios/buscar",
			template : function(ul, item) {
		return jQuery("<li></li>").data("item.autocomplete", item).append(
			"<a>" + item.label + "</a>").appendTo(ul);
	}
    });



});
function excluir(id){
    $("#sys").append("<div id=\"excluir-dialog\" title=\"Confirmação\">Deseja realmente excluir este item?</div>");
    $("#excluir-dialog").dialog({
            modal: true,
            width: 300,
            height: 140,
            buttons:{
                    Sim: function(){                        
                        $(this).dialog('close');
                          $.ajax({
                            url: baseUrl+"/transporte/viagem/get-viagem-usuario",
                            type: "POST",
                            data: {
                                    id: id
                            },
                            success: function(txt){                            
                                if(txt == 0){
                                    window.location.href = baseUrl+'/transporte/viagem/excluir/id/'+id
                                }else{
                                 
                                    $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">Não foi possível excluir <br> Há Pacientes agendados para essa viagem</div>")
                                    $("#mensagem-dialog").dialog({
                                            modal: true,                                               
                                            close: function(){
                                                    $(this).remove();
                                            },
                                            buttons: {
                                                    "Ok": function(){
                                                            $(this).dialog('close');
                                                    }
                                            }
                                    });
                                    
                                }
                           }
                        });
                        //window.location.href = url;
                    },
                    "Não": function(){
                            $("#excluir-dialog").dialog("destroy").remove();
                    }
            }
    })
	
}

function limparLocalEmbarque() {
    $("#via_local").val("");
}

function limparEmbarqueUnidade() {
    $(".unidades").attr('checked', false);
}