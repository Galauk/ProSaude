$(function (){
    $("tr:odd").addClass("odd");
    //usr_cpf:{validaCpf:true}
});

function editaIncosistencias(eir_codigo,usu_codigo){
    $.ajax({
        url:baseUrl+"/default/paciente/edita-paciente-esus",
        type: "POST",
        data: {
            eir_codigo : eir_codigo,
            usu_codigo : usu_codigo
        },
        success:function(txt){
            switch(txt) {
                case 'aviso': incosistenciaInvalida(); break;
                case 'form': formPaciente(eir_codigo,usu_codigo); break;
                case 'lista': listaPacientesDuplicados(eir_codigo,usu_codigo); break;
            }
        }
    });
}

function incosistenciaInvalida(){
    $("body").append("<div id=\"invalida-dialog\" title=\"Inconsistência E-SUS\" />");
    $("#invalida-dialog")
    .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Edição de Inconsistência...\" />")
    .dialog({
        modal: true,
        width: 650,
        height: 150,
        close: function(){
            $(this).remove();
        }
    })
    .load(baseUrl+"/default/paciente/esus-incosistencia-invalida");
}

function formPaciente(eir_codigo,usu_codigo){
    $("#datanascimento").live('focus', function () { $(this).datepicker({ changeMonth: true, changeYear: true }) });
    
        
    $("#lista-esus-dialog").dialog("destroy").remove();
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
                $("#form-esus-dialog").dialog("destroy").remove();
            },
            Salvar: function(){
                if (validaCpf()==true) {
                    var valoresForm = $('#form-esus').serialize();
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
                    });
                } else {
                    mensagem("Valida CPF","CPF Inválido",100,130);
                    $("#cnpj_cpf").focus();
                }
            }
        }
    })
    .load(baseUrl+"/default/paciente/esus-form-paciente/eir_codigo/"+eir_codigo+"/usu_codigo/"+usu_codigo);
    }, 1);
    
}

function listaPacientesDuplicados(eir_codigo,usu_codigo){
    $("body").append("<div id=\"lista-esus-dialog\" title=\"Edita Inconsistência E-SUS\" />");
    $("#lista-esus-dialog")
    .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Edição de Inconsistência...\" />")
    .dialog({
        modal: true,
        width: 700,
        height: 450,
        close: function(){
            $(this).remove();
        }
    })
    .load(baseUrl+"/default/paciente/esus-lista-pacientes-duplicados/eir_codigo/"+eir_codigo+"/usu_codigo/"+usu_codigo);
}

function ativaMascaraTel(){
    $("#pep_telefone").mask("99 999999999");
    $("#pep_celular").mask("99 999999999");
    $("#pep_contato").mask("99 999999999");
}

function ativaMascaraData(){
    $("#datanascimento").mask("99/99/9999");
}

function ativaCpf(){
    $("#cnpj_cpf").mask("999.999.999-99");
}

function validaCpf(){
    var cpf = $("#cnpj_cpf").val() 
    cpf = cpf.replace(".","").replace(".","").replace("-","");
    if (TestaCPF(cpf)){
        return true;
    } else {
        $("#erro-cpf-esus").show();
    }
}

function teste(){
    $("#form-esus").validate({
        ignore: [],
        rules: {
           pep_telefone:{
               required: true,
               minlength: 4
           }
        },
        messages: {
           pep_telefone:{
                required: "Preencha o campo telefone",
                minlength : "Coloque no minimo 3 caracteres"
           }
        }
    });
}

//Função relativa a tela de estorno de exportação

function ativaEstorno(eeh_codigo){
    var link = baseUrl+"/programasfederais/esus/estorno-esus";
    mensagemSemOk("carregando-ate", "Aguarde", "Gerando arquivo de exportação do E-SUS ...", 350, 100);
    $.ajax({
        url: link,
        data:{eeh_codigo:eeh_codigo},
        success: function(txt){
            fecharMensagemSemOk("carregando-ate");
            
        }
    });
}
//fim da função