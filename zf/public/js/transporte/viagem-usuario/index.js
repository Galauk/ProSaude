$(function(){

});
function verificaVagas(id){
       $.ajax({
            url: baseUrl+"/transporte/veiculo/verifica-cota",
            type: "POST",
            data: {
                    id: id
            },
            success: function(txt){                 
                //if(txt > 0){
                    window.location.href = baseUrl+'/transporte/viagem-usuario/novo/cod/'+id+'/disponivel/'+txt;
                /*}else{
                    $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">Veículo com capacidade esgotada</div>")
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

                }*/
           }
        });
}

function capacidadeAtual(id) {
    console.log("aqui");
    console.log(id);
   $.ajax({
    url: baseUrl+"/transporte/veiculo/verifica-cota",
    type: "POST",
    data: {
            id: id
    },
        success: function(){

        }
    });
}