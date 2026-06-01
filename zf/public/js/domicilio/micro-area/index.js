function excluir(id){
    $("#sys").append("<div id=\"excluir-dialog\" title=\"Confirmação\">Deseja realmente excluir este item?</div>");
    $("#excluir-dialog").dialog({
            modal: true,
            width: 300,
            height: 140,
            buttons:{
                    Sim: function(){                        
                        window.location.href = baseUrl+'/domicilio/area/excluir/id/'+id
                        //window.location.href = url;
                    },
                    "Não": function(){
                            $("#excluir-dialog").dialog("destroy").remove();
                    }
            }
    });
	
}