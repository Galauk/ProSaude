$(function (){
    $("#consulta-horus").validate({
        rules: {
            num_protocolo: { required: true },
        },
        messages: {
            num_protocolo: { required: "Campo Obrigatório" }
        }
    });
    
    $("#consulta-protocolo-horus").validate({
        rules: {
            num_protocolo: { required: true },
        },
        messages: {
            num_protocolo: { required: "Campo Obrigatório" }
        }
    });
    
});

function excluiProtocolo(numProtocolo){
    confirme("Confirme:", "Deseja realmente excluir os dados?", 300, 150, function(){
        window.location.href = baseUrl+"/programasfederais/horus/envia-deleta-protocolo/numProtocolo/"+numProtocolo;
    });
}

function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;
    if((tecla>47 && tecla<58)){
        getEnderecos();
        return true;
    } else{
        if (tecla==8 || tecla==0) {
            getEnderecos();
            return true;
        } else{
            return false;
        }
    }
}

function baixarXmls(numProtocolo){
    mensagemSemOk("xml-horus","XMLS Horus","Baixando XMLS Horus, protocolo: "+$("#numProtocoloHidden").val()+"",300,100);
    $.ajax({
       url:baseUrl+"/programasfederais/horus/gera-xml-por-protocolo",
       type: "POST",
       data:{
           num_protocolo: $("#numProtocoloHidden").val()
       },
       success:function(txt) {
            fecharMensagemSemOk("xml-horus");
            mensagem("XMLS HORUS",txt,350,150);
       }
    });
}

function editaDados(horDadCodigo){
    //$("#datanascimento").live('focus', function () { $(this).datepicker({ changeMonth: true, changeYear: true }) });
        
    setTimeout(function() { 
        $("body").append("<div id=\"form-esus-dialog\" title=\"Edita Inconsistência E-SUS\" />");
        $("#form-esus-dialog")
        .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Edição de Inconsistência...\" />")
        .dialog({
            open: function( event, ui ) {
                $("#form-esus").validate();
            },  
            modal: true,
            width: 724,
            height: 450,
            close: function(){
                $(this).remove();
            },
            buttons: {
                Cancelar: function(){
                    //$("#form-esus-dialog").dialog("destroy").remove();
                },
                Salvar: function(){
                    /*var valoresForm = $('#form-esus').serialize();
                    mensagemSemOk("salvando-edita-inc", "Aguarde", "Salvando inconsistências ...", 280, 80);
                    $.ajax({
                        url:baseUrl+"/default/paciente/salvar-form-paciente-esus",
                        type: "POST",
                        data: valoresForm,
                        success:function(txt){
                            $.ajax({
                                url:baseUrl+"/programasfederais/esus/altera-status-importacao",
                                type: "POST",
                                data: {eir_codigo:eir_codigo},
                                success:function(txt){
                                    mensagem("Confirmação de Cadastro","Inconsistência salva com sucesso!", 350, 120);
                                    fecharMensagemSemOk("salvando-edita-inc");
                                    $("#form-esus-dialog").dialog("destroy").remove();
                                    $("#"+eir_codigo).remove();
                                }
                            });
                        }
                    });*/
                }
            }
        })
        .load(baseUrl+"/programasfederais/horus/edita-dados-horus/hor_dad_codigo/"+horDadCodigo);
    }, 1);
}

