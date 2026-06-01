$(function(){
    $("#form").validate({
		rules: {
                    vei_capacidade: {
                            required: true
                    },
                    vei_descricao:{
                        required: true
                    }
			
		},
		messages: {
                    vei_capacidade: {
                            required: "Campo Obrigatório"
                    },
                    vei_descricao: {
                            required: "Campo Obrigatório"
                    }
                        
		}
	});
	$("#for_nome").buscar({
		url: baseUrl+'/default/fornecedor/buscar',
		
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});	
	// validações
	$("form:first").validate({
		rules: {
			for_codigo: {
				min: 1
			}
		},
		messages: {
			for_codigo: {
				min: "Infome um procedimento"
			}
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
                            url: baseUrl+"/transporte/veiculo/get-viagem-por-veiculo",
                            type: "POST",
                            data: {
                                    id: id
                            },
                            success: function(txt){                                
                                if(txt == 0){
                                    window.location.href = baseUrl+'/transporte/veiculo/excluir/id/'+id
                                }else{
                                 
                                    $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">Não foi possível excluir <br> Há viagens agendadas para esse veículo</div>")
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