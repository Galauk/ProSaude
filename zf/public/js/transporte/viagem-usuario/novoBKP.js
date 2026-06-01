$(function(){
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
                    }
			
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
                    }
                        
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
    
    $("#proc_nome").buscar({
            url: baseUrl+'/procedimento/buscar/',
            suffix: '_2',
            template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
            callback: function(event, ui){
                    return true;
            }
    });
    
    $(".selec").click(function(){
    // alert($("option:selected").html());
     adicionar($("option:selected"));   
    
    });
    
    $("#proc_snome").dblclick(function(){    
    
        proc_nome =  $("#proc_snome option:selected").html();
        proc_codigo =  $("#proc_snome option:selected").val();

        $("#procedimentos").append("<option title=\""+proc_nome+"\" value=\""+proc_codigo	+"\">"+proc_nome+"</option>");    
        $("#proc_snome option:selected").remove();
      

    });

    $("#viausu_km").change(function(){
        tipo = $("input[name='viausu_tipo']:checked").val();
        km = $("#viausu_km").val();
        
        if(tipo == 'A'){
            proc_codigo_sus = "0803010125";
        }else{
            proc_codigo_sus = "0803010125";
        }
        $.ajax({
        url: baseUrl+"/transporte/viagem-usuario/get-codigo-procedimento-tfd",
            type: "POST",
            data: {
                    proc_codigo_sus: proc_codigo_sus
            },
            success: function(txt){
                $("#proc_snome option").each(function( index ) {
                    if(txt['proc_codigo'] == $(this).val()){
                       proc_nome = $(this).html();
                       proc_codigo = $(this).val()
                       $("#procedimentos").append("<option title=\""+proc_nome+"\" value=\""+proc_codigo	+"\">"+proc_nome+"</option>");                  
                       $(this).remove();                        
                    }         
                });
                alert(calculaQtdeProcedimentoskm(km));
               // for(i=0; 1<calculaQtdeProcedimentoskm(km); i++){}
             //   $("#proc_snome").append("<option title=\""+data.proc_nome+"\" value=\""+data.proc_codigo	+"\">"+data.proc_nome+"</option>");
                
            }
        });
        
      //  $("#proc_snome option").each(function( index ) {
          //  alert($(this).val());
        // });
        
        
    });
    
    
    //atualiza o combo de procedimentos diferenciando se é por paciente ou acompanhante;
    $(".viausu_tipo").click(function(){
        tipo = $("input[name='viausu_tipo']:checked").val();

        $.ajax({
                url: baseUrl+"/transporte/viagem-usuario/buscar-procedimentos-por-tipo-de-pessoa",
                type: "POST",
                data: {
                        tipo: tipo
                },
                success: function(txt){
                    $("#proc_snome option").remove();
                    $("#procedimentos option").remove();
                    $.each( txt, function( key, value ) {                    
                        $("#procedimentos").append("<option title=\""+value['proc_nome']+"\" value=\""+value['proc_codigo']	+"\">"+value['proc_nome']+"</option>");
                       // alert( key + ": " + value['proc_codigo']+value['proc_nome'] );
                    });
               }
        });
      
    });
        
    function calculaQtdeProcedimentoskm(km){
        total = km / 50;
        return parseInt(total)
    }
        // adiciona os itens no select
    function adicionar(obj){	
            if(!obj.size())
                    return;
            var data = {};           
            
            data.proc_codigo = obj.val();
            data.proc_nome = obj.html();
            $("#proc_snome").append("<option title=\""+data.proc_nome+"\" value=\""+data.proc_codigo	+"\">"+data.proc_nome+"</option>");
           obj.remove();
            return true;
    }

});