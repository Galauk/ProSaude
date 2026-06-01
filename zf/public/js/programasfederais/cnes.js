$(function (){
    
    $("#cnes").validate({
        rules: {
            arquivo: { required: true }
        },
        messages: { 
            arquivo: { required: "Nenhum arquivo selecionado." },
        }
    });
    
    $("#enviar").click(function(){
        if ($("#arquivo").val() != "") {
            $('.spinner').show()
            //mensagemSemOk("carregando-ate", "Aguarde", "Importando dados do CNES ...", 300, 100);
        }
    })  
})