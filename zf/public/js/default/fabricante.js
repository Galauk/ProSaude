$(function(){
    
});

function excluir(id){
    $("html").append("<div id=\"excluir-dialog\" title=\"Confirmação\">Deseja realmente excluir este item?</div>");
    $("#excluir-dialog").dialog({
            modal: true,
            width: 300,
            height: 140,
            buttons:{
                    Sim: function(){
                        $(this).dialog('close');
                          $.ajax({
                            url: baseUrl+"/default/fabricante/excluir",
                            type: "POST",
                            data: {
                                    id: id
                            },
                            success: function(txt){
                                if(txt == 0){
                                    window.location.href = baseUrl+'/default/fabricante/index'
                                }else{
                                 
                                    $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">Erro ao excluir este fabricante!</div>")
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