$(document).ready(function(){
	$("tr:odd").addClass("odd");
        var height = $( "#area_entrada" ).height();
        $(".item_entrada").each(function(){
           height = parseInt(height) + 30;
        });
        $( "#area_entrada" ).height(height);
});
$(function(){
    $("#set_codigo_sol").addClass("ui-state-default");
    $("#set_codigo_sol").width("370px");
    $("#set_codigo_req").addClass("ui-state-default");
    $("#set_codigo_req").width("370px");
    
    $(".menu_small").click(function(){
        var link = $(this).data("acao");
        if($(this).data("tipo")  != $("#mov_tipo").val()){
            confirme("Confirme:", "Ao sair da tela os dados digitados e não salvos serão perdidos. Deseja continuar?", 300, 150, function(){
                $(".menu_small").css( "background-image", "-moz-linear-gradient(bottom, #D8E1E6 0%, #FAFAFA 100%)" ); 
                window.location = baseUrl + "/materiais/"+link;
            });
        }
    });
});


function excluirItem(pro_codigo,remi_codigo){
    
    $.ajax({
       url: baseUrl+"/materiais/envio-de-materiais/cancelar-item/",
       type: "POST",
       data:{pro_codigo:pro_codigo,
             remi_codigo:remi_codigo},
       success: function(txt){
           $("#situacao_remi_"+txt).html("Não enviado");
       }
    });
}

function verificaSeDigitouQtde(){
    var val = 1;
    $(".qtde_lote").each(function(){
        if($(this).val() == ""){
            val = 0;
            return false;
        }else{
            val = 1;
        }
    });
    if(val == 0){
        return false;
    }else{
        return true;
    }
}

function verificaSeDigitouLote(){
    var val = 1;
    $(".lotes").each(function(){
        if($(this).val() == ""){
            val = 0;
            return false;
        }else{
            val = 1;
        }
    });
    if(val == 0){
        return false;
    }else{
        return true;
    }
}

function atribuiLote(pro_lote_val_qtde,count){
    /*OS SPLITS SERVEM POIS A QUANTIDADE VEM CONCATENADA COM O LOTE LOTE|QTDE*/
    $(".qtde_"+count).val("");
    var array_pro_lote_val = pro_lote_val_qtde.split("-");
    var pro_lote_val = array_pro_lote_val[0];
    var pro_qtde_val = array_pro_lote_val[1];
    
    var opt_removido = $("."+$("#"+count).val()).val();
    $("."+opt_removido).remove();
    if($(".lotes").length > 1){// se tiver mais de um combo entra
        $(".lotes").each(function(){
            var valor_combo_array = $(this).val().split("-");
            var valor_combo = valor_combo_array[0];
            if(valor_combo != pro_lote_val){ //nao percorre o select que estou mexendo
                if(opt_removido){

                    var opt_removido_array = opt_removido.split("-");
                    var opt_removido_lote = opt_removido_array[0];
                    var opt_removido_qtde = opt_removido_array[1];
                    $(this).append("<option value=\""+opt_removido_lote+"-"+opt_removido_qtde+"\">Lote: "+opt_removido_lote+" Qtde: "+opt_removido_qtde+"</option>");
                }
                $(this).find('option').each(function() { //percorre todos os options de cada select combo
                    var valor_opt_array = $(this).val().split("-");
                    var valor_opt = valor_opt_array[0];
                    if(valor_opt == pro_lote_val){
                        $(this).remove();
                    }
                    
                });
            }
            
        });
        
    }
    if($("."+pro_lote_val).length < 1)
        $("#lotes_hist").append("<input type=\"hidden\" class=\"lotes_digitados "+pro_lote_val+"-"+pro_qtde_val+"\" id=\""+count+"\" value='"+pro_lote_val+"-"+pro_qtde_val+"'>");
}


function addLinha(pro_codigo,remi_codigo){
    if($(".item_entrada").length >= 1){
        var height = $( "#area_entrada" ).height() + 30;
        $( "#area_entrada" ).height(height);
    }
    
    if(!verificaSeDigitouQtde()){
        mensagem("Erro","Há quantidades a serem preenchidas",300,150);
        return false;
    }
    if(!verificaSeDigitouLote()){
        mensagem("Erro","Há lotes a serem preenchidos",300,150);
        return false;
    }
    
    var count = $("#count").val();
    count++;
    $("#count").val(count);
    
    var set_codigo = $("#set_codigo_sol").val();
    var select = "<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default lotes combo_"+count+"\"  style=\"width:305px;\" onChange=\"atribuiLote(this.value,"+count+")\">"+
                        "<option value=\"\">---SELECIONE---</option>";
    
    $.ajax({
        url: baseUrl+"/produto/get-lotes/",
        type: "POST",
        data: {pro_codigo:pro_codigo,
               set_codigo:set_codigo,
               enviados:1},
        async:false,
        success: function(txt){
            var validade = "";
            for(var i in txt){
                var quantidade = txt[i].sal_qtde;
                if($("."+pro_codigo+txt[i].sal_lote).data("qtde")){
                    quantidade = (txt[i].sal_qtde - parseInt($("."+pro_codigo+txt[i].sal_lote).data("qtde")));
                }
                /*Essa parte verifica se o lote ja esta selecionado para nao repetir nos combos*/
                var valida_lote = 1;
                $(".lotes_digitados").each(function(){
                    if($(this).val() == txt[i].sal_lote+"-"+txt[i].sal_qtde){
                        valida_lote = 0;
                    }
                });
                
                if(valida_lote == 1){
//                    if(txt[i].sal_qtde < txt[i].saldo_original){
//                        mensagem("Alerta","O lote:<b>"+txt[i].sal_lote+"</b> possui envios pendentes!",300,150);
//                    }
                    validade  = txt[i].sal_validade.replace("-", "/").replace("-", "/");
                    select += "<option value=\""+txt[i].sal_lote+"-"+txt[i].sal_qtde+"-"+validade+"\">Lote: "+txt[i].sal_lote+" / Qtde: "+quantidade+"</option>";
                }
            }

            select += "</select>";
            
        }
    });
    
    var linha = "<tr style=\"background-color:#EEEEE0;\" class=\"sublinhas_"+count+" linhas_lotes\" data-remi=\""+remi_codigo+"\" data-count=\""+count+"\">"+
                    "<td>"+
                        select+
                    "</td>"+
                    "<td align=\"center\">"+
                        "<input type=\"text\" name=\"remil_quantidade[]\" id=\"remil_quantidade[]\" onkeypress=\"return SomenteNumero(event)\" onchange=\"verificaQuantidadeValdia(this.value,"+count+",'"+remi_codigo+"')\" value=\"\" class=\"ui-state-default qtde_lote qtde_"+count+"\" style=\"border:1px solid;\" size=\"10\">"+
                    "</td>"+
                    "<td colspan=\"3\"></td>"+
                   "<td align=\"center\">"+
                        "<img src=\""+baseUrl+"/public/images/icons/remove.png\"  style=\"cursor:pointer;\" onclick=\"removerLinha("+count+")\" />&nbsp"+
                    "</td>"+
                "</tr>";
            
   
    
    
    $("."+pro_codigo).after(linha);
    if($(".combo_"+count+" option").length == 1){
        mensagem("Erro","Não existem mais lotes disponíveis",300,150);
        $(".sublinhas_"+count).remove();
    }

}

function verificaQuantidadeValdia(valor_digitado,count,remi_codigo){
    if($(".combo_"+count).val() == ""){
        mensagem("Erro","Escolha um lote",300,150);
        $(".qtde_"+count).val("");
        return false;
    }
    
    var qtde_lote_array = $(".combo_"+count).val().split("-");
    var qtde_lote = qtde_lote_array[1];
    if( parseInt(qtde_lote) <  parseInt(valor_digitado)){
        mensagem("Erro","O valor digitado é maior do que o valor em estoque",300,150);
        $(".qtde_"+count).val("");
        return false;
    }
    
    //alert(parseInt($("#qtde_sol").val())+ ">" +parseInt(valor_digitado));
    var valor_digitado_total = 0;
    $(".qtde_"+count).each(function(){
         valor_digitado_total = ((parseFloat(valor_digitado_total))+(parseFloat($(this).val())));
    });
    
//    if(parseInt($("#qtde_sol_"+remi_codigo).val()) < parseInt(valor_digitado_total)){
//        mensagem("Erro","O valor digitado é maior do que a quantidade solicitada",300,150);
//        $(".qtde_"+count).val("");
//        return false;
//    }
    
}

function removerLinha(cod_linha){
    var value_array;
    if($(".qtde_"+cod_linha).val() != ""){
        confirme("Confirme:", "Deseja remover esta linha e seus dados?", 300, 150, function(){
               $(".sublinhas_"+cod_linha).remove();
               var value = $("#"+cod_linha).val();
               $(".lotes").each(function(){
                   value_array = value.split("-");
                   $(this).append("<option value=\""+value+"\">Lote: "+value_array[0]+" / Qtde: "+value_array[1]+"</option>");
               });
               $("#"+cod_linha).remove();
        });
    }else{
        $(".sublinhas_"+cod_linha).remove();
    }
}

function finalizaMovimentacao(){
    //sublinhas_
    
    var itens = new Array;
    confirme("Confirme:", "Ao concluir essa operação não será possível mexer nos itens desta requisição! Deseja realmente finalizar?", 300, 150, function(){
        $(".linhas_lotes").each(function(){
           itens.push(new Item($(this).data("remi"),$(".qtde_"+$(this).data("count")).val(),$(".combo_"+$(this).data("count")).val())); 
        });
        mensagemSemOk("carregando-ate1", "Aguarde", "Carregando...", 280, 80);
        var rem_codigo = $("#rem_codigo").val();
        $.ajax({
            url: baseUrl+"/materiais/envio-de-materiais/salvar-itens/",
            type: "POST",
            data: {itens:itens,
                   rem_codigo:rem_codigo},
            success: function(txt){
                $("#carregando-ate1").dialog("destroy").remove();
                if(txt.id == "" || txt.id == null || txt.id == "undefined"){
                    mensagem("Erro",txt.msg,300,150);
                    $("#div_finalizar").show();
                }else{
                    mensagem("Sucesso",txt.msg,300,150,function(){sucesso_salvar();});
                }
            }
        });
        
    });
}

function Item(remi_codigo, remil_quantidade, remil_lote){
    //alert(mov_codigo);
    this.remi_codigo = remi_codigo;
    this.remil_quantidade = remil_quantidade;
    this.remil_lote = remil_lote;

}


function sucesso_salvar(){
    window.location = baseUrl + "/materiais/envio-de-materiais";
}