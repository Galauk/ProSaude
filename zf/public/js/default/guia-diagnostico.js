$(function(){
    $("#modal").show();
    $("#modal").dialog({
            modal: true,
            width: 500,
            height: 200,
            buttons:{
                    Ok: function(){                        
                        /*Verifica se o agendamento é válido*/
                        var age_codigo = $("#age_codigo").val();
                        $.ajax({
                            url: baseUrl+"/agendamento/agendamento/get-agendamento/",
                            type: "POST",
                            data:{age:age_codigo},
                            success: function (txt){
                                if(txt){
                                    //window.open(baseUrl+'/prontuario/ficha/index/modulo/prontuario/age/'+age_codigo,'_blank');
                                    location.href = baseUrl+"/prontuario/ficha/index/modulo/prontuario/age/"+age_codigo;
                                    
                                }
                            }
                         });
                        
                    }
            }
    })
});