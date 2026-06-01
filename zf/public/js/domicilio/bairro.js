$(function() {
    $("#form").submit(function(){
        var popup = $("#popup").val();
        
        if($("#bai_nome").val() == "")
            return false;
        
        if($("#dis_codigo").val() == "" && $("#cid_codigo").val() == "" ){
            return false;
        }
        
        if($("input[name=possui_distrito]:checked").val() == "S" && $("#dis_codigo").val() == "" ){
            return false;
        }
        
        mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);
        var valoresForm = $('#form').serialize();
        var localidade = "";
        $.ajax({
            url: baseUrl+'/domicilio/bairro/salvar',
            data: valoresForm,
            type: "POST",
            success:function(txt){
                if(!txt.bai_codigo){
                    mensagem("Erro","Erro ao salvar bairro!.<br/>"+txt.msg,300,150);
                    //window.close();
                    return false;
                }
                //alert(txt.id+","+txt.nome+","+txt.rua_cep);
                //fecharMensagemSemOk("carregando-ate");
                if(popup == 1){
                    if(txt.dis_nome != null ){
                        localidade = txt.cid_distrito + " - " + txt.dis_nome
                    }else{
                        localidade = txt.cid_nome;
                    }
                    window.opener.retornaBairro(txt.bai_codigo,txt.bai_nome,localidade);
                    window.close();
                }else{ 
                   window.location = baseUrl + "/domicilio/bairro/index";
                }
            }
        });
        return false;
    });
    
   $(".possui_distrito").change(function(){
       if($(this).val() == "N"){
           $("#div_distrito").hide();
           $("#div_cidade").show();
           $("#cid_nome").prop('readonly', false);
       }else{
            $("#cid_nome").prop('readonly', true);
           $("#div_distrito").show();
           $("#div_cidade").hide();
       }   
    });
    
    $("#cid_nome").buscar({
            url: baseUrl+'/cidade/buscar/',
            minLength: 3,
            template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
            callback: function(event, ui){
                    return true;
            }
    });
    
    $("#cid_nome").change(function(){
        if($("#cid_nome").val().length == 0){
            $("#cid_codigo").val("");
        }
    });
    
    $.validator.addMethod("validaDistrito", function(validaDistrito, element){
        if($("input[name=possui_distrito]:checked").val() == "S"){
            if($("#dis_codigo").val() == ""){

                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    },"Campo Obrigatorio");
    
    
    
    
    $("#form").validate({
           rules: {
               bai_nome:{
                    required: true,
                    minlength:3
                },
               dis_codigo:{
                    validaDistrito : true
               },
               cid_codigo:{required : true}
           },
           messages: {
               bai_nome:{
                    required: "Preencha o campo Nome",
                    minlength : "Coloque no minimo 3 caracteres"
               }
           }
    });
    
    $("#dis_codigo").change(function(){
       $.ajax({
          url: baseUrl + '/cidade/get-cidade-por-distrito',
          data:{dis_codigo : $(this).val()},
          success:function(txt){
              $("#cid_codigo").val(txt["cid_codigo"]);
              $("#cid_nome").val(txt["cid_nome"]);
              $("#cid_nome").prop('readonly', true);
          }
       }); 
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
                        $.ajax({
                           url: baseUrl + '/domicilio/bairro/verifica-vinculo',
                           data:{bai_codigo : id},
                           success:function(txt){
                               if(txt >= 1){
                                   mensagem("Erro","Este bairro possui vinculo com outros logradouros",300,150,function(){$("#excluir-dialog").dialog("destroy").remove();});
                                   
                               }else{
                                   window.location.href = baseUrl+'/domicilio/bairro/excluir/id/'+id
                               }
                           }
                        }); 
                        //window.location.href = baseUrl+'/domicilio/area/excluir/id/'+id
                        //window.location.href = url;
                    },
                    "Não": function(){
                            $("#excluir-dialog").dialog("destroy").remove();
                    }
            }
    })
	
}