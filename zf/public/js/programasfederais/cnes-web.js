$(function (){
    
    
});

function inativa(id){
    
    confirme("Confirme:", "Deseja realmente inativar este usuário? <br/>Atenção este registro só servirá para consultar históricos:", 300, 150, function(){
        $.ajax({
            url:baseUrl+"/default/usuarios/inativa",
            type: "POST",
            data: {
                usr_codigo : id
            },
            success:function(txt){
                if(txt == 1){
                    $(".linha_"+id).remove();
                    verificaSeTodosEstãoPreenchidos();
                }else{
                    mensagem("Aviso",txt,400,200);
                }
            }
        });
    });
    
    
    
}


function testaCpf(cpf,id){
    if(!TestaCPF(cpf)){
        mensagem("Alerta","CPF inválido",200,200);
        $("#"+id).val("");
        return false;
    }
    
    $.ajax({
        url:baseUrl+"/default/usuarios/verifica-se-existe-cpf",
        type: "POST",
        data: {
            cpf : cpf
        },
        success:function(txt){
            if(txt > 0){
               mensagem("Alerta","Este CPF já existe!",200,150);
               $("#"+id).val("");
               return false;
            }
        }
    });
    var count = 0;
    $(".cpf").each(function(){
       if(cpf == $(this).val()){
           count++;
       } 
    });
    if(count > 1){
        mensagem("Alerta","Este CPF já foi digitado!",200,150);
        $("#"+id).val("");
    }
    verificaSeTodosEstãoPreenchidos();
    
}

function verificaSeTodosEstãoPreenchidos(tipo){
    var count = 0;
    var campo = "";
    if(tipo == "U"){
        campo = "cnes";
    }else{
        campo = "cpf";
    }
    $("."+campo).each(function(){
        if($(this).val() == ""){
            count++;
        } 
    });

    if(count == 0){
        $(".salvar").removeClass("ui-state-disabled");
    }
}


function inativa_unidade(id){
    $.ajax({
        url:baseUrl+"/default/unidade/inativa",
        type: "POST",
        data: {
            uni_codigo : id
        },
        success:function(txt){
            if(txt == 1){
                $(".uni_"+id).remove();
                verificaSeTodosEstãoPreenchidos("U");
            }
        }
    });
}

function validaCnes(cnes,id){
    $.ajax({
        url:baseUrl+"/default/unidade/verifica-se-existe-cnes",
        type: "POST",
        data: {
            cnes : cnes
        },
        success:function(txt){
            if(txt > 0){
               mensagem("Alerta","Este CNES já existe!",200,150);
               $("#"+id).val("");
               return false;
            }
        }
    });
    var count = 0;
    $(".cnes").each(function(){
       if(cnes == $(this).val()){
           count++;
       } 
    });
    if(count > 1){
        mensagem("Alerta","Este CNES já foi digitado!",200,150);
        $("#"+id).val("");
    }
    verificaSeTodosEstãoPreenchidos("U");
}