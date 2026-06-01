$(function(){
     
    $("#bai_nome").change(function(){
        if($("#bai_nome").val().length <= 2){
            $("#bai_codigo").val("");
            $("#editar_rua").hide();
            $("#localidade").val("");
        }
    })
    
    $("#form").submit(function(){
        if($("#cid_codigo").val() == ""){
            mensagem("Atenção","Informe a cidade",300,150);
            $("#cid_codigo").focus();
            return false;
        }
        if($("#rua_cep").val() == ""){
            mensagem("Atenção","Informe um cep",300,150);
            $("#rua_cep").focus();
            return false;
        }
        if($("#rua_nome").val() == ""){
            mensagem("Atenção","Informe o nome da rua",300,150);
            $("#rua_nome").focus();
            return false;
        }
        
        if($("#bai_codigo").val() == ""){
            mensagem("Atenção","Informe um bairro",300,150);
            $("#bai_nome").focus();
            return false;
        }
        
        if($("#co_tipo_logradouro").val() == ""){
            mensagem("Atenção","Informe tipo de logradouro!",300,150);
            $("#ds_tipo_logradouro").focus();
            return false;
        }
        
        var popup = $("#popup").val();
        
        mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);
        var valoresForm = $('#form').serialize();
        $.ajax({
            url: baseUrl+'/rua/salvar',
            data: valoresForm,
            type: "POST",
            success:function(txt){
                if(!txt.id){
                    mensagem("Erro","Erro ao salvar rua!.<br/>"+txt.msg,300,150);
                    window.close();
                    return false;
                }
                //alert(txt.id+","+txt.nome+","+txt.rua_cep);
                //fecharMensagemSemOk("carregando-ate");
                if(popup == 1){
                    window.opener.retornaRua(txt.id,txt.nome,txt.rua_cep,txt.bai_codigo,txt.bai_nome,txt.cid,txt.dist);
                    window.close();
                }else{
                    window.location = baseUrl + "/default/rua/index";
                }
            }
        });
        return false;
    });
    
});


function retornaBairro(bai_codigo,bai_nome,localidade){
    //alert("haaaa mulek");
    $("#bai_codigo").val(bai_codigo);
    $("#bai_nome").val(bai_nome);
    $("#localidade").val(localidade);
    
    //$("#rua_cep").prop('readonly', true);
}

function buscaCidade(){
    $("#cidade").buscar({
        url: baseUrl+'/cidade/buscar/',
        template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + " - " + item.data.uf_sigla + "</a>").appendTo(ul);
            },
        callback: function(event, ui){
            return false;
        }
    });    
}

function buscaTipoLogradouro(){
    $("#ds_tipo_logradouro").buscar({
        url: baseUrl+'/rua/busca-tipo-logradouro',
        template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
        callback: function(event, ui){
            return false;
        }
    });    
}

function buscaBairro(){
    $("#bai_nome").buscar({
        url: baseUrl+'/bairro/buscar',
        template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a><strong>" + item.label + "</strong>"
                            + "<br><strong>Cidade.:</strong> "
                            + item.data.cid_nome
                            + " <strong>Distrito:</strong> " + item.data.dis_nome
                            + "</a>&nbsp;").appendTo(ul);
            },
        callback: function(event, ui){
            $("#localidade").val( ui.item.data['cid_nome'] + " - Distrito: "+ui.item.data['dis_nome']);
            $("#editar_rua").show();
        }
    });    
}


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
                            url: baseUrl+"/default/rua/excluir",
                            type: "POST",
                            data: {
                                    id: id
                            },
                            success: function(txt){
                                if(txt == 0){
                                    window.location.href = baseUrl+'/default/rua/index'
                                }else{
                                 
                                    $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">Esta rua não pode ser excluída, está vinculada a domicílios!</div>")
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

function addBairro(){
    window.open(baseUrl + "/domicilio/bairro/novo/popup/1","_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
}


function editarBairro(){
    var bai_codigo = $("#bai_codigo").val();
    if(rua_codigo){
        window.open(baseUrl + "/domicilio/bairro/novo/popup/1/id/"+bai_codigo,"_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
    }else{
        mensagem("Atenção","Rua não selecionada para edição",300,150);
    }
}