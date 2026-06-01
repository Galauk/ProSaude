$(function(){
    $("#dados-envio").hide();
    $("#mostradadosenvio").click(function (){
            $("#dados-envio").show();
    });
    $("#ocultadadosenvio").click(function(){
        $("#dados-envio").hide();
        $("#hor_dad_respenvio").val("");
        $("#numprotocoloenvio").val("");
    });
    $("#ocultadadosenviotodos").click(function(){
        $("#dados-envio").hide();
        $("#hor_dad_respenvio").val("");
        $("#numprotocoloenvio").val("");
    });
});

