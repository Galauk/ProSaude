$(function() {
    $("#form").validate({
        rules: {
            bai_codigo:{ required:true },
            rua_codigo:{required:true},
            dom_numero:{required:true},
            // usu_codigo: {required:true},
        },
        messages: { 
            rua_codigo:{required:"Campo Obrigatório"},
            bai_codigo:{required:"Campo Obrigatório"},
            dom_numero:{required:"Campo Obrigatório"},
            // usu_codigo:{required:"Campo Obrigatório"}
        },
        submitHandler: function() { verificaSeExiste(); }
    });
    
    $(".paciente").click(function(){
        var codigoResponsavelFamiliar = $("#codigoResponsavelFamiliar").val();
        var cadastro_aise = $("#cadastro_aise").val();
        var link = "";
        if(cadastro_aise == 1){
            link = baseUrl+"/paciente/form-paciente/pessoa/"+codigoResponsavelFamiliar+"/poupup/1";
        }else{
            link = baseUrl+"/default/paciente/form-paciente/pessoa/"+codigoResponsavelFamiliar+"/poupup/1";
            //link = "../../../../WebSocialSaude/paciente.php?acao=form&poupup=1&usu_codigo="+usu_codigo;
        }
        window.open(link, "name", "scrollbars=1,height=800,width=900",'width=850,height=700');
    });
    
});

	
function retornaPac(usu_codigo,usu_nome){
    $("#usu_codigo").val(usu_codigo);
    $("#usu_nome").val(usu_nome);
}

function addRua(){
    window.open(baseUrl + "/rua/novo/popup/1","_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
}

function clearRua(){
    setTimeout(function() { 
        if($("#rua_nome").length == 0){
            //mensagem("Atenção","Rua não localizada",300,150);
            $("#editar_rua").hide();
            $("#rua_codigo").val("");
            //$("#rua_cep").val("");
            $("#rua_nome").val("");
            $("#rua_nome").focus();
            $("#bai_codigo").val("");
            $("#bai_nome").val("");
        }
    }, 500);
    
}

function buscarRua(){
    $("#rua_codigo").val("");
    //$("#rua_cep").val("");
    $("#rua_nome").buscar({
        url: baseUrl+'/rua/buscar',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + ""
                    + "<br/><strong>Bairro:</strong>"+ item.data.bai_nome
                    + " <strong>Distrito:</strong> " + item.data.dis_nome
                    + "</a>&nbsp;").appendTo(ul);
        },
        callback: function(event,ui){
            $("#rua_codigo").val(ui.item.id);
            $("#rua_cep").val(ui.item.data.rua_cep);
            $("#rua_cep").attr("disabled");
            $("#bai_nome").val(ui.item.data.bai_nome);
            $("#bai_codigo").val(ui.item.data.bai_codigo);
            $("#bai_nome").attr("disabled");
             $("#localidade").val( ui.item.data['cid_nome'] + " - Distrito: "+ui.item.data['dis_nome']);
        }
    });
}





function informaSn(e){
    var checado = false;
    if($(e).attr("checked")=="checked"){
       checado=true;
    }else{
       checado = false;
    }
    
    if(checado){
        $("#dom_numero").val("S/N");
        $("#dom_numero").attr("disabled");
    }else{
        $("#dom_numero").removeAttr("disabled");
        $("#dom_numero").val("");
    }
   
}


function verificaSeExiste(){
    var codigoResponsavelFamiliar = $("#codigoResponsavelFamiliar").val();
    // console.log(codigoResponsavelFamiliar);
    
    var dom_numero = 0;
    if($("#dom_numero").val() != "S/N"){
        dom_numero = $("#dom_numero").val();
    }
    
    var controle_responsavel = 0;
    var controle_logradouro_numero_complemento = 0;
    
    if( $("#codigoResponsavelFamiliar").val() != $("#usu_codigo_controle_editar" ).val()){
        controle_responsavel = 1;
    }
    if( $("#rua_codigo").val() != $("#rua_codigo_controle_editar" ).val()){
        controle_logradouro_numero_complemento = controle_logradouro_numero_complemento + 1;
    }
    if( $("#dom_numero").val() != $("#dom_numero_controle_editar" ).val()){
        controle_logradouro_numero_complemento = controle_logradouro_numero_complemento + 1;
    }
    if( $("#dom_complemento").val() != $("#dom_complemento_controle_editar" ).val()){
        controle_logradouro_numero_complemento = controle_logradouro_numero_complemento + 1;
    }
    
    //verifica tanto responsavel como logradouro e numero

    if(controle_responsavel != 0 && controle_logradouro_numero_complemento != 0){
        $.ajax({
            url: baseUrl + '/domicilio/domicilio/verifica-vinculo',
            data:{
                dom_numero: dom_numero,
                dom_complemento: $("#dom_complemento").val(),
                rua_codigo: $("#rua_codigo").val(),
                codigoResponsavelFamiliar: codigoResponsavelFamiliar
              },
            success: function(txt){
                if(txt < 1){
                    salvarCadastro();
                }else{
                    mensagem("Erro","Já existe um domicilio nesta rua, com este número e complemento. <br/>Ou o responsavel informado já é responsavel por outro domicilio.",250,200);
                }
            }
        });
    }

    //verifica somente o responsavel
    if(controle_responsavel != 0 && controle_logradouro_numero_complemento == 0){
        $.ajax({
            url: baseUrl + '/domicilio/domicilio/verifica-vinculo',
            data:{
                    codigoResponsavelFamiliar: codigoResponsavelFamiliar
                },
            success: function(txt){
                if(txt < 1){
                    salvarCadastro();
                }else{
                    mensagem("Erro","O responsavel informado já é responsavel por outro domicilio.",250,200);
                }
            }
        });
    }
    //verifica somente o logradouro e numero e complemento
    if(controle_responsavel == 0 && controle_logradouro_numero_complemento != 0){
        $.ajax({
            url: baseUrl + '/domicilio/domicilio/verifica-vinculo',
            data:{
                    dom_numero: dom_numero,
                    dom_complemento: $("#dom_complemento").val(),
                    rua_codigo: $("#rua_codigo").val()
                },
            success: function(txt){
                if(txt < 1){
                    salvarCadastro();
                }else{
                    mensagem("Erro","Já existe um domicilio nesta rua, com este número e complemento.",250,200);
                }
            }
        });
    }
    if(controle_responsavel == 0 && controle_logradouro_numero_complemento == 0){
        salvarCadastro();
    }
    
}

function salvarCadastro(){
    mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);
    var valoresForm = $('#form').serialize();
    $.ajax({
        url: baseUrl+'/domicilio/domicilio/salvar',
        data: valoresForm,
        type: "POST",
        success:function(txt){
            if(!txt.dom_codigo){
                fecharMensagemSemOk("carregando-ate");
                mensagem("Erro","Erro ao salvar domicilio!.<br/>",400,250,null);
                return false;
            }
            fecharMensagemSemOk("carregando-ate");
            window.location = baseUrl + "../../domicilio.php";
        }
    });
}

function buscarResponsavelModal(){
   $("#usu_nome").buscar({
            url: baseUrl+'/paciente/buscar',
           template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a><strong>" + item.label + "</strong>"
                        + "<br><strong>Data Nasc.:</strong> "
                        + item.data.usu_datanasc
                        + " <strong>Mãe:</strong> " + item.data.usu_mae
                        + "</a>&nbsp;").appendTo(ul);
            },
           suffix:'-2',
           callback: function(event,ui){
                $("#usu_codigo").val(ui.item.id);
           }
            
    });
}


function retornaRua(id,nome,cep,bai_codigo,bai_nome,cidade,distrito){
    //alert("haaaa mulek");
    $("#rua_cep").val(cep);
    $("#rua_codigo").val(id);
    $("#rua_nome").val(nome);
    $("#bai_codigo").val(bai_codigo);
    $("#bai_nome").val(bai_nome);
    $("#localidade").val( cidade + " - Distrito: "+distrito);
    $("#rua_cep").prop('readonly', true);
    //$("#rua_cep").prop('readonly', true);
}

