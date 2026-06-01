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
    
    $("#set_codigo_req").change(function(){
            $("#set_codigo_sol option[value="+$(this).val()+"]").remove();
            if($("#set_codigo_req_hist").val() != ""){
                $("#set_codigo_sol").append("<option value=\""+$("#set_codigo_req_hist").val()+"\">"+$("#set_nome_req_hist").val()+"</option>");
            }
            $("#set_codigo_req_hist").val($(this).val());
            $("#set_nome_req_hist").val($("#set_codigo_req option[value="+$(this).val()+"]").text());
    });
    
     
     
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


function validaCadastro(){
    
    if($("#set_codigo_sol").val() == 0){
        mensagem("Erro","Selecione o setor que será solicitado",300,150);
        return false;
    }
    
    if($("#set_codigo_req").val() == 0){
        mensagem("Erro","Selecione o setor requisitante",300,150);
        return false;
    }
    
    salvarCadastro();
}

function salvarCadastro(){
    var valoresForm = $('#form').serialize();

    if($("#rem_codigo").val() != ""){
        valoresForm += "&mov_codigo="+$("#mov_codigo").val();
    }else{
        mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);
    }
    $.ajax({
        url: baseUrl+"/materiais/requisicao-materiais/salvar/",
        type: "POST",
        data: valoresForm,
        success: function(txt){
                $("#carregando-ate").dialog("destroy").remove();
                if(txt.id == "" || txt.id == null || txt.id == "undefined"){
                    mensagem("Erro!",txt,300,150);
                } else {
                    mensagem("Confirmação de Cadastro",txt.msg,300,150,function(){load_itens(txt.id)});

                }
        }
    });
}

function load_itens(rem_codigo){
    $(".salvar-icon").hide();
    $("#rem_codigo").val(rem_codigo);
    setTimeout(function() { $('#pro_nome').focus() }, 500);
    $("#itens_requisicao").show();
    $(".form-produtos").show();
    $("#set_codigo_req").prop('disabled', 'disabled');
    $("#set_codigo_sol").prop('disabled', 'disabled');
}


function buscaProdutos(){  
    $("#pro_nome").buscar({
        url: baseUrl+"/produto/buscar-produtos/setor/"+$("#set_codigo_req").val()+"/setor_movimento/1/setor_logado/1", //Passando true como parametro de setor da nota Ps: Nome da variavel errado
        template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(ul,item){
                if(verificaVinculoComSetorDestino()){
                    trocaBotoes();
                }
                $("#remi_quantidade").focus();
        }
    });
}

function verificaVinculoComSetorDestino(){
    var set_codigo = $("#set_codigo_sol").val();
    var pro_codigo = $("#pro_codigo").val();
    var valida = "";
    $.ajax({
        url: baseUrl+"/produto/verifica-produto-setor/",
        type: "POST",
        data: {pro_codigo:pro_codigo,
               set_codigo:set_codigo},
        async:false,
        success: function(txt){
            if(txt == 0){
                mensagem("Erro","O produto selecionado não possui vinculo com o setor de destino, favor entrar em contato com o responsável do setor",300,150);
                $("#pro_codigo").val("");
                $("#pro_nome").val("");
                $("#div_lotes").hide();
                valida = 1;
            }
        }
    });
    if(valida == 1){
        return false;
    }else{
        return true;
    }
    
}

function trocaBotoes(){
    if($("#produto_vinculo_setor").val() == 0){
        $(".erro").show();
        $("#botao-add").hide();
        $("#botao-vinculo").show();
    }else{
        $(".erro").hide();
        $("#botao-add").show();
        $("#botao-vinculo").hide();
    }
}

function vincularProdutoSetor(){
    confirme("Confirme:", "Deseja realmente vincular o produto ao centro estocador de entrada?", 300, 150, function(){
        $.ajax({
            url: baseUrl+"/produto/vincular-produto-setor/",
            data: {set_codigo: $("#set_codigo_req").val(),
                   pro_codigo: $("#pro_codigo").val()},
            success: function(txt){
                
                if(txt == 1){
                    $(".erro").hide();
                    $("#botao-add").show();
                    $("#botao-vinculo").hide();
                }else{
                    mensagem("Erro!","Não foi possivel vincular o setor ao produto <br/><br/><br/>"+txt, 350, 250);
                }
                
            }
        });
    });
    
}


function addItens(){
    if($(".item_entrada").length >= 1){
        var height = $( "#area_entrada" ).height() + 30;
        $( "#area_entrada" ).height(height);
    }
    
    var quantidade = $("#remi_quantidade").val();
    
    if($("#ite_quantidade").val() == "" || $("#ite_quantidade").val() == 0){
        mensagem("Erro","Informe uma quantidade",300,150);
        return false;
    }
    var table = "<tr class=\""+$("#pro_codigo").val()+" item_entrada\" data-nome=\""+$("#pro_nome").val()+"\" data-qtde=\""+$("#remi_quantidade").val()+"\"  data-pro=\""+$("#pro_codigo").val()+"\" data-remi=\""+$("#remi_codigo").val()+"\" data-edit=\"1\" data-status=\"S\">"+
                    "<td>"+$("#pro_nome").val()+"</td>"+
                    "<td align=\"center\"> "+quantidade+" </td>"+
                    "<td align=\"center\"> "+$("#usr_nome").val()+" </td>"+
                    "<td align=\"center\"> Req.Pendente </td>"+
                    "<td align=\"center\">"+
                        "<img src=\""+baseUrl+"/public/images/icons/editar.png\"  style=\"cursor:pointer;\" onclick=\"editar('"+$("#pro_codigo").val()+"')\" />&nbsp"+
                        "<img src=\""+baseUrl+"/public/images/icons/excluir2.png\" style=\"cursor:pointer;\" onclick=\"excluir('"+$("#pro_codigo").val()+"')\" />"+
                    "</td>"+
                "</tr>";
            
    
    
    
    $(".itens-entrada-dir").show();
    
    if($("."+$("#pro_codigo").val()).length > 0){
        mensagem("Erro","Este produto já está na lista!",300,150);
        return false;
    }
    
    $("#table-itens").append(table);
    montaZebrado();
    $("#div_finalizar").show();
    $("#pro_codigo").val("");
    $("#pro_nome").val("");
    $("#remi_quantidade").val("");
    $("#remi_codigo").val("");
    setTimeout(function() { $('#pro_nome').focus() }, 500);
}

function montaZebrado(){
    
    
    var cont = 1;
    $("tr.item_entrada").each(function(){
        $(this).removeClass("odd");
        if (cont % 2 != 0) {
            $("tr:odd").addClass("odd");
        }
        cont++;
    });
}

function excluir(pro_codigo_lote){ // metodo que apenas usa qnd inserir
    $("."+pro_codigo_lote).remove();
    montaZebrado();
    if($(".item_entrada").length <= 0 && $("#finalizar_all").val() != 1){
        $("#div_finalizar").hide();
        $(".itens-entrada-dir").hide();
    }
}

function editar(pro_codigo){
    //var produto_lote = pro_codigo_lote.split("|");
    
    if($("#remi_codigo").val() != ""){
        mensagem("Erro","Só pode ser editado um item por vez",300,150);
        return false;
    }
    
    if($("."+pro_codigo).data("status") != "S"){
        mensagem("Erro","Já existem movimentações pra esse produto",300,150);
        return false;
    }
    
    /*if(verificaSeJaRealizouMovimentacao(produto_lote[1],ite_codigo)){*/
       // pro_codigo_lote = produto_lote[0]+produto_lote[1];
        $("#pro_codigo").val($("."+pro_codigo).data("pro"));
        $("#pro_nome").val($("."+pro_codigo).data("nome"));
        $("#remi_quantidade").val($("."+pro_codigo).data("qtde"));
        $("#remi_codigo").val($("."+pro_codigo).data("remi"));


        $("."+pro_codigo).remove();
        if($(".item_entrada").length <= 0){
            $("#div_finalizar").hide();
            $(".itens-entrada-dir").hide();
        }
/*    }else{
        mensagem("Erro","Já existem movimentações pra esse produto",300,150);
    }*/
    
    montaZebrado();
    
}

function finalizaMovimentacao(){
    mensagemSemOk("carregando-ate1", "Aguarde", "Carregando...", 280, 80);
    var itens = new Array();
    $(".item_entrada").each(function(){
        itens.push(new Item($(this).data("pro"),$(this).data("qtde"),$(this).data("usr") ,$("#rem_codigo").val(),$(this).data("remi"),$(this).data("status")));
    });
    
    var itens_deletar_banco = new Array();
    $(".remi_codigo_del").each(function(){
        itens_deletar_banco.push($(this).val());
    });    
   // alert(itens_deletar_banco);return false;
    $("#carregando-ate1").dialog("destroy").remove();
    confirme("Confirme:", "Deseja realmente gerar movimentação?", 300, 150, function(){
        $("#div_finalizar").hide();
        $.ajax({
            url: baseUrl+"/materiais/requisicao-materiais/salvar-itens/",
            type: "POST",
            data: {itens:itens,
                   itens_deletar_banco:itens_deletar_banco},
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

function sucesso_salvar(){
    window.location = baseUrl + "/materiais/movimentacao";
}

function Item(pro_codigo, remi_quantidade, usr_codigo,rem_codigo,remi_codigo,remi_status){
    //alert(mov_codigo);
    this.pro_codigo = pro_codigo;
    this.remi_quantidade = remi_quantidade;
    this.rem_codigo = rem_codigo;
    this.remi_status = remi_status;
    if(remi_codigo){
        this.remi_codigo = remi_codigo;
    }

}

function excluirItem(pro_codigo,remi_codigo){//metodo apenas utilizado quando editar
    if($("."+pro_codigo).data("status") == "S"){
        excluir(pro_codigo);
        $(".itens-entrada-dir").append("<input type=\"hidden\" name=\"remi_codigo_del[]\" class=\"remi_codigo_del\" value=\""+remi_codigo+"\">");
    }else{
        mensagem("Erro","Já existem movimentações pra esse produto",300,150);
    }
    
}

function verificaSeJaRealizouMovimentacao(pro_codigo,remi_codigo){
    var val = "";
    $.ajax({
            url: baseUrl+"/materiais/requisicao-materiais/verifica-se-enviou/",
            type: "POST",
            data: {pro_codigo:pro_codigo,
                   remi_codigo:remi_codigo},
            async:false,
            success: function(txt){
                if(txt >= 1){
                    val = 0;
                }else{
                    val = 1;
                }
            }
        });
        
    if(val == 1){
        return true;
    }else{
        return false;
    }
}

function addLinha(pro_codigo){
    var count = $("#count").val();
    count++;
    $("#count").val(count);
    
    var set_codigo = $("#set_codigo_sol").val();
    var select = "<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default lotes\"  style=\"width:305px;\" onChange=\"atribuiLote(this.value,"+count+")\">"+
                        "<option value=\"\">---SELECIONE---</option>";
    
    $.ajax({
        url: baseUrl+"/produto/get-lotes/",
        type: "POST",
        data: {pro_codigo:pro_codigo,
               set_codigo:set_codigo},
        async:false,
        success: function(txt){
            for(var i in txt){
                var quantidade = txt[i].sal_qtde;
                if($("."+pro_codigo+txt[i].sal_lote).data("qtde")){
                    quantidade = (txt[i].sal_qtde - parseInt($("."+pro_codigo+txt[i].sal_lote).data("qtde")));
                }

                select += "<option value=\""+txt[i].sal_lote+"|"+quantidade+"|"+txt[i].sal_validade+"\">Lote: "+txt[i].sal_lote+" / Qtde: "+quantidade+"</option>";
            }
            select += "</select>";
        }
    });
    
    var linha = "<tr style=\"background-color:#EEEEE0;\" class=\"sublinhas_"+count+"\">"+
                    "<td>"+
                        select+
                    "</td>"+
                    "<td align=\"center\">"+
                        "<input type=\"text\" name=\"remil_quantidade[]\" id=\"remil_quantidade[]\" value=\"\" class=\"ui-state-default\" style=\"border:1px solid;\" size=\"10\">"+
                    "</td>"+
                    "<td colspan=\"4\"></td>"+
                "</tr>";
    $("."+pro_codigo).after(linha);
}

function listaLotesPorRequisicao(codRequisicaoItens){
    $.ajax({
        url: baseUrl+"/materiais/requisicao-materiais/lista-lotes-por-requisicao",
        type: "POST",
        data: {codRequisicaoItens: codRequisicaoItens},
        success: function(txt){
            var linha = "";
            var pro_codigo = txt[0].pro_codigo;
            $(".sublinhas_"+pro_codigo).remove();
            for (var i in txt) {
                linha += "<tr style=\"background-color:#EEEEE0;\" class=\"sublinhas_"+pro_codigo+"\">"+
                    "<td>"+
                        "<strong>LOTE: </strong>"+txt[i].remil_lote+
                    "</td>"+
                    "<td align=\"center\">"+
                        txt[i].remil_quantidade+
                    "</td>"+
                    "<td colspan=\"4\"></td>"+
                "</tr>";
            }
            $("."+pro_codigo).after(linha);
        }
    });
}

function atualizaStatusItemRequisicao(codRequisicaoItens){
    $("#remi-"+codRequisicaoItens)
    .attr("src",baseUrl+"/public/images/loading.gif")
    .attr("title","Carregando");
    $.ajax({
        url: baseUrl+"/materiais/requisicao-materiais/atualiza-status-item-requisicao",
        type: "POST",
        data : { codRequisicaoItens: codRequisicaoItens},
        success: function(txt){
            if (txt=="C") {
                $("#remi-"+codRequisicaoItens)
                .attr("src",baseUrl+"/public/images/icons/accept.png")
                .attr("title","Requisição realizada com sucesso!")
                $("#situacao-"+codRequisicaoItens).html("Confirmado");
                //$("#remi-"+codRequisicaoItens).prop( "onclick", null );
            } else {
                $("#remi-"+codRequisicaoItens)
                .attr("src",baseUrl+"/public/images/icons/transferir.png")
                .attr("title","Realizar transferência!")
                $("#situacao-"+codRequisicaoItens).html("Aguardando Confirmação");
            }
        }
    });
}