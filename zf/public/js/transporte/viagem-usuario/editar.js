$(function(){    
    $("#tabs-1").height(800);
    $(document).ready(function(){
        if($("#usu_nome_2").val() != null && $("#usu_nome_2").val() != ""){
            $("#usu_nome_2").show();
        } else {
            $("#usu_nome_2").hide();
        }

        if($("#usu_nome_3").val() != null && $("#usu_nome_3").val() != ""){
            $("#usu_nome_3").show();
        } else {
            $("#usu_nome_3").hide();
        }

        if($("#usu_nome_4").val() != null && $("#usu_nome_4").val() != ""){
            $("#usu_nome_4").show();
        } else {
            $("#usu_nome_4").hide();
        }
    });
    
    $("#form").validate({
		rules: {
                    usu_nome: {
                            required: true
                    },
                    busca1:{
                        required: true
                    },
                    busca2:{
                        required: true
                    },
                    // viausu_km:{
                    //     required: true
                    // }
			
		},
		messages: {
                    usu_nome: {
                            required: "Campo Obrigatório"
                    },
                    busca1: {
                            required: "Campo Obrigatório"
                    }, 
                    busca2: {
                            required: "Campo Obrigatório"
                    }, 
                    // viausu_km: {
                    //         required: "Campo Obrigatório"
                    // }
                        
		}
    });
    $("#usu_nome").buscar({
            url: baseUrl+'/paciente/buscar/',
            callback: function(event, ui){
                    return true;
            }
    });
    
    $("#busca1").buscar({
            url: baseUrl+'/cidade/buscar/',
            template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
            callback: function(event, ui){
                    return true;
            }
    });
    
    $("#busca2").buscar({
            url: baseUrl+'/cidade/buscar/',
            suffix: '_2',
            template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
            callback: function(event, ui){
                    return true;
            }
    });    
    // if($("#viausu_codigo").val()){
    //     $.ajax({
    //     url: baseUrl+"/transporte/usuario-acompanhante/get-acompanhante",
    //         type: "POST",
    //         data: {
    //                 viausu_codigo: $("#viausu_codigo").val()
    //         },
    //         success: function(txt){
    //           total = $(".buscaAcom").size();
    //             for( var i in txt){
    //                 $("#usu_nome_"+total+"").val(txt[i].usu_nome_);
    //                 $("#usu_codigo_"+total+"").val(txt[i].usu_codigo_);
    //                 $("#usu_nome_"+total+"").addClass("buscaAcom");
    //                 $("#usu_nome_"+total+"").show();
    //                // alert(total);
    //                 total = $(".buscaAcom").size()+1;
                  
                   
    //                 //alert(txt[i].usu_codigo_);
                    
    //             }             
    //         }
    // });
    // }
    
    $("#addPac").click(function(){
          total = $(".buscaAcom").size()+1;
          total_anterior = $(".buscaAcom").size();
        //  alert("#usu_nome_"+total+"");
         if($("#usu_codigo_"+total_anterior+"").val() != ""){
             if(total >= $("#disponivel").val()){
                 mensagem("Erro","Erro ao inserir acompanhante!.<br/>"+"Já excedeu a quantidade de pacientes por veículo!",300,150);
                 return false;
             }
            $("#usu_nome_"+total+"").addClass("buscaAcom");
            $("#usu_nome_"+total+"").show();
         }
        
    });
    
    
    for(i=1; i<=4; i++){
        $("#usu_nome_"+i).buscar({
            suffix: '_'+i,
            url: baseUrl+'/paciente/buscar/',
            callback: function(event, ui){
                    return true;
            }
        });
    }
    



});