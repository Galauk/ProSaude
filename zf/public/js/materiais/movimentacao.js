$(document).ready(function(){
	$("tr:odd").addClass("odd")
        
    var height = $( "#area_entrada" ).height()

    $(".item_entrada").each(function(){
        height = parseInt(height) + 30
    })
    
    $( "#area_entrada" ).height(height);
    
    $("#set_nome").ready(function(){
        $("#nome_setor").text($("#set_nome").val())
    })
})

$(function(){
    
    $("#setor option").each(function() {
        $("#setor option").attr("selected","selected")
    })
    
    $("#zera-mov-envia").click(function(){
        mensagemSemOk("carregando-ate", "Aguarde", "Removendo movimentações ...", 300, 100)
    })
    
    $('#ite_lote').keypress(function(event) {
        var tecla = (window.event) ? event.keyCode : event.which
        if (tecla != 64 && tecla != 92 && tecla != 34 && tecla != 35 && tecla != 36 && tecla != 37 && tecla != 38 && tecla != 40 && tecla != 41 && tecla != 42) {
            return true
        } else {
            return false
        }
    })
    
    if($("#mov_tipo").val() == "E"){
        configuraCampos("mov_entrada");
        $("#entrada").css( "background-image", "-moz-linear-gradient(bottom, #ffffff 0%, #ffffff 100%)" );
    }else if($("#mov_tipo").val() == "S"){
        configuraCampos("mov_saida");
        $("#saida").css( "background-image", "-moz-linear-gradient(bottom, #ffffff 0%, #ffffff 100%)" ); 
    }else if($("#mov_tipo").val() == "T"){
        configuraCampos("set_codigo_destino");
        $("#transferencia").css( "background-image", "-moz-linear-gradient(bottom, #ffffff 0%, #ffffff 100%)" ); 
    }
    
    $(".botao_superior").click(function(){
        if($(this).data("tipo") == "E"){
           window.location = baseUrl + "/materiais/entrada/index";
        }
        if($(this).data("tipo") == "S"){
          window.location = baseUrl + "/materiais/saida/index";
        }
        if($(this).data("tipo") == "T"){
           window.location = baseUrl + "/materiais/transferencia/index";
        }
        if($(this).data("tipo") == "C"){
           window.location = baseUrl + "/materiais/controle-movimentos/index";
        }
        if($(this).data("tipo") == "R"){
            window.location = baseUrl + "/materiais/requisicao-materiais/index";
        }
        if($(this).data("tipo") == "EV"){
            window.location = baseUrl + "/materiais/envio-de-materiais/index";
        }
        $("#botoes").hide("slow");
        $(".menu_small").show("slow");
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

function buscaFabricante(){
    $("#fab_descricao").buscar({
            url: baseUrl+"/default/fabricante/buscar",
            template : function(ul, item) {
                    return jQuery("<li></li>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            }
        });
}

function configuraCampos(mov_tipo){
    if(mov_tipo){
        $("#"+mov_tipo).addClass("ui-state-default");
        $("#"+mov_tipo).width("370px");
    }
    
    $("#for_codigo").addClass("ui-state-default");
    $("#for_codigo").width("370px");
    $("#set_codigo").addClass("ui-state-default");
    $("#set_codigo").width("370px");
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
            data: {set_codigo: $("#set_codigo").val(),
                   pro_codigo: $("#pro_codigo").val()},
            success: function(txt){
                
                if(txt == 1){
                    setTimeout(function() { $('#ite_lote').focus() }, 500);
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

window.isVacina = false
function buscaProduto(){
    var setor_logado = "";
    if($("#mov_tipo").val() == "S"){
        setor_logado = 1;
    }
    
    $("#pro_nome").buscar({
        url: baseUrl+"/produto/buscar-produtos/setor/"+$("#set_codigo").val()+"/setor_logado/"+setor_logado , //Passando true como parametro de setor da nota Ps: Nome da variavel errado
        template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(ul, item){
            $("#pro_frmmin").val(item.item.data.pro_frmmin)

            
            if(item.item.data.grupo === "VACINA") {
                window.isVacina = true
            } else {
                window.isVacina = false
            }

            if($("#pro_validade").val() == "S"){
                $("#lote_validade_entrada").show()
            } else {
                $("#lote_validade_entrada").hide()
            }
            
            if($("#pro_fracionado").val() == "S"){
                $("#doses").show()
            } else {
                $("#doses").hide()
            }
            
            trocaBotoes()
            
            $("#ite_lote").val("")
            $("#ite_validade").val("")
        }
    })
}

function buscaProdutoSaida(){
    var setor_movimento = "";
    if($("#mov_tipo").val() == "S"){
        setor_movimento = 1;
    }
    
    $("#pro_nome").buscar({
        url: baseUrl+"/produto/buscar-produtos-com-estoque/setor/"+$("#set_codigo").val()+"/setor_movimento/"+setor_movimento+"/tipo/"+$("#mov_tipo").val() , //Passando true como parametro de setor da nota Ps: Nome da variavel errado
        template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(ul,item){
            if($("#mov_tipo").val() == "T"){//validação aplicada pra verificar se o produto selecionado tem vinculo com o setor de destino
                verificaVinculoComSetorDestino();
                
            }else{
                carregaComboLote();
            }
        }
    });
}

function buscaProdutoTransf(){
    
    $("#pro_nome").buscar({
        url: baseUrl+"/produto/buscar-produtos-com-estoque/setor/"+$("#set_codigo").val() , //Passando true como parametro de setor da nota Ps: Nome da variavel errado
        template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(ul,item){
            if($("#mov_tipo").val() == "T"){//validação aplicada pra verificar se o produto selecionado tem vinculo com o setor de destino
                verificaVinculoComSetorDestino();
                
            }else{
                carregaComboLote();
            }
        }
    });
}

function verificaVinculoComSetorDestino(){
    var set_codigo = $("#set_codigo_destino").val();
    var pro_codigo = $("#pro_codigo").val();
    $.ajax({
        url: baseUrl+"/produto/verifica-produto-setor/",
        type: "POST",
        data: {pro_codigo:pro_codigo,
               set_codigo:set_codigo},
        success: function(txt){
            if(txt == 0){
                mensagem("Erro","O produto selecionado não possui vinculo com o setor de destino, favor entrar em contato com o responsável do setor",300,150);
                $("#pro_codigo").val("");
                $("#pro_nome").val("");
                $("#div_lotes").hide();
            }else{
                carregaComboLote();
            }
        }
            
    });
    
}

function carregaComboLote(lote_edit,qtde_edit_soma,ite_codigo){
    var pro_codigo = $("#pro_codigo").val();
    var set_codigo = $("#set_codigo").val();

    var recebeCodigoItensMovimento = $("#recebeCodigoItensMovimento").val();
    var setCodigoDestino = parseInt($("#set_codigo_destino").val());
    
    retornaEstoqueCentroDestino(recebeCodigoItensMovimento, setCodigoDestino, pro_codigo);

    var tipo = $("#mov_tipo").val();
    $.ajax({
        url: baseUrl+"/produto/get-lotes/",
        type: "POST",
        data: {pro_codigo:pro_codigo,
               set_codigo:set_codigo,
               enviados:1,
               tipo:tipo},
        success: function(txt){
            var select = "<label class=\"ui_state_default\">Lote /Val /Quantidade: </label>&nbsp;"+
                         "<select id=\"ite_lote\" name=\"ite_lote\" class=\"ui-state-default\" style=\"width:305px;\">"
            for(var i in txt){
                var fab_descricao = txt[i].fab_descricao;
                var quantidade = txt[i].sal_qtde;

                if($("."+pro_codigo+replaceSpecialChars(txt[i].sal_lote)).data("qtde")){
                    quantidade = (txt[i].sal_qtde - parseInt($("."+pro_codigo+replaceSpecialChars(txt[i].sal_lote)).data("qtde")));
                }
                var checked = "";
                if($("#edit").val() == 1){
                    lote_edit = lote_edit.replace("'","");
                }
                if(lote_edit == txt[i].sal_lote){
                    checked = "selected";
                    if(ite_codigo){
                        quantidade = parseInt(quantidade) + parseInt(qtde_edit_soma);
                    }else{
                        quantidade = parseInt(quantidade);
                    }
                }
                select += "<option value=\""+txt[i].sal_lote+"|"+quantidade+"|"+txt[i].sal_validade+"\""+checked+">Lote: "+txt[i].sal_lote+"/ Val:"+dataToBr(txt[i].sal_validade)+" / Qtde: "+quantidade+"/ Fabricante: "+txt[i].fab_descricao+"</option>";
            }
            select += "</select>";
            $("#div_lotes").html(select);
            $("#div_lotes").show();

            if($("#pro_fracionado").val() == "S"){
                $("#doses").show();
            }else{
                $("#doses").hide();
            }
        }
    });

}

function retornaEstoqueCentroDestino(recebeCodigoItensMovimento, setCodigoDestino, pro_codigo){
    recebeCodigoItensMovimento = recebeCodigoItensMovimento;
    setCodigoDestino = setCodigoDestino;

    $.ajax({
        url: baseUrl+"/produto/retorna-estoque-centro-destino/",
        type: 'POST',
        data: {
            recebeCodigoItensMovimento : recebeCodigoItensMovimento,
            setCodigoDestino : setCodigoDestino,
            pro_codigo : pro_codigo
        },
        success:function(txt) {
            var recebeTotal = JSON.parse(txt)
            var semValor = 'Sem Estoque';
            if (recebeTotal[0].sum == 0) {
                $("#qtdeSetorDestino").val(semValor);              
            } else{
                $("#qtdeSetorDestino").val(recebeTotal[0].sum);
            }

        }
    })
}

function validaCadastro(){

    if($("#set_codigo").val() == 0){
        mensagem("Confirmação de Cadastro","Selecione um setor",300,150);
        return false;
    }
    
    if($("#mov_entrada").val() == 0 || $("#mov_saida").val() == 0){
        mensagem("Confirmação de Cadastro","Selecione um tipo de movimento",300,150);
        return false;
    }
    
    salvarCadastro();
}

function salvarCadastro(){
    
    var valoresForm = $('#form').serialize();

    var recebeCodigoItensMovimento = null;

    if($("#mov_codigo").val() != ""){
        valoresForm += "&mov_codigo="+$("#mov_codigo").val();
    }else{
        mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);
    }
    $.ajax({
        url: baseUrl+"/materiais/movimentacao/salvar/",
        type: "POST",
        data: valoresForm,
        success: function(txt){
            console.log(txt)
            if($("#finalizar_all").val() != 1){ // soh passa aqui se não for o editar
                $("#carregando-ate").dialog("destroy").remove();
                if(txt.id == "" || txt.id == null || txt.id == "undefined"){
                    //$("#conf-cadastro").dialog("destroy").remove();
                    mensagem("Erro!",txt,300,150);
                } else {
                    setTimeout(() => {
                        recebeCodigoItensMovimento = parseInt(txt.id);
                        $("#recebeCodigoItensMovimento").val(recebeCodigoItensMovimento);
                    }, 250);
                    
                    mensagem("Confirmação de Cadastro",txt.msg,300,150,function(){load_itens(txt.id)});
                }
            }
        }
    });
}

function load_itens(mov_codigo){
    $(".salvar-icon").hide();
    $("#mov_codigo").val(mov_codigo);
    $("#itens_movimento").show();
    $(".form-entrada-esq").show();
    setTimeout(function() { $('#pro_nome').focus() }, 500);
    $("#set_codigo").prop('disabled', 'disabled');
    $("#for_codigo").prop('disabled', 'disabled');
    $("#mov_saida").prop('disabled', 'disabled');
    $("#mov_entrada").prop('disabled', 'disabled');
    $("#mov_observacao").attr("disabled", "disabled");
    
    if($("#mov_tipo").val() == "T"){
        $("#set_codigo_destino").attr("disabled", "disabled");
    }
    
}

function addItens(){
    if($(".item_entrada").length >= 1){
        let height = $( "#area_entrada" ).height() + 30;
        $( "#area_entrada" ).height(height);
    }

    let quantidade = $("#ite_quantidade").val();

    if($("#ite_doses").val() != ""){
        quantidade += "/"+$("#ite_doses").val();
    }
    
    if($("#pro_validade").val()=="S"){
        if ($("#ite_lote").val()=="") {
            mensagem("Erro","Informe o lote",300,150);
            return false;
        }
        if ($("#ite_validade").val()=="") {
            mensagem("Erro","Informe a validade",300,150);
            return false;
        }
    }

    if($("#ite_quantidade").val() == "" || $("#ite_quantidade").val() == 0){
        mensagem("Erro","Informe uma quantidade",300,150);
        return false;
    } 
    
    if(window.isVacina == true && $("#fab_codigo").val() == ""){
        mensagem("Erro", "Informe o fabricante", 300, 150)
        return false
    }
    
    var lote = $("#ite_lote").val();/*CASO FOR SAIDA ELE VEM O LOTE DO COMBO E NÃO DIGITADO POR ISSO PASSA A QUANTIDADE JUNTO*/

    var validade =  $("#ite_validade").val();
    var fab_codigo = null; //não é setado fabricante
    var fab_descricao = null; //não é setado fabricante
    if($("#mov_tipo").val() == "S" || $("#mov_tipo").val() == "T"){/*REGRA ABAIXO SÓ SE APLICA A SA�?DAS*/
        var lote_quantidade = lote.split('|');
        var quantidade_lote = parseInt(lote_quantidade[1]);
        var quantidade_digitada = parseInt($("#ite_quantidade").val());
        lote = lote_quantidade[0]; //pra nao ir concatenado com a quantidade
        validade = lote_quantidade[2];
        if(quantidade_lote < quantidade_digitada){
            mensagem("Erro","A quantidade digitada é superior a quantidade do respectivo lote!",300,150);
            return false;
        }
        
    }else if($("#mov_tipo").val() == "E"){
        fab_codigo = $("#fab_codigo").val();
        fab_descricao = $("#fab_descricao").val();
        pro_frmmin = $("#pro_frmmin").val();
        var data_validade_array = $("#ite_validade").val().split("/");
        var data_validade = data_validade_array[2]+data_validade_array[1]+data_validade_array[0];
        if(data_validade < $("#data_atual").val()){
            mensagem("Erro","Data de validade menor que a data atual",300,150);
            return false;
        }
    }
    
    var table = "<tr class=\""+replaceSpecialChars($("#pro_codigo").val()+lote)+" item_entrada\" data-nome=\""+$("#pro_nome").val()+"\" data-doses=\""+$("#ite_doses").val()+"\" data-valor=\""+$("#ite_vlrunid").val()+"\" data-vlrtotal=\""+($("#ite_vlrunid").val() * parseInt(quantidade))+"\" data-ite=\""+$("#ite_codigo").val()+"\" data-val=\""+validade+"\" data-lote=\"'"+lote+"\" data-qtde=\""+$("#ite_quantidade").val()+"\"  data-pro=\""+$("#pro_codigo").val()+"\" data-frm=\""+$("#pro_frmmin").val()+"\" data-fab=\""+fab_codigo+"\" data-nfab=\""+fab_descricao+"\">"+
                    "<td>"+$("#pro_nome").val()+"</td>"+
                    "<td align=\"center\"> "+quantidade+" </td>"+
                    "<td> "+lote+" </td>"+
                    ($("#mov_tipo").val() == "E" ? "<td align=\"center\"> "+$("#ite_validade").val()+"</td>": "") +
                    ($("#mov_tipo").val() == "E" ? "<td align=\"center\"> "+$("#ite_vlrunid").val()+" </td>" : "")+
                    ($("#mov_tipo").val() == "E" ? "<td align=\"center\"> "+$("#pro_frmmin").val()+" </td>" : "")+
                    ($("#mov_tipo").val() == "E" ? "<td align=\"center\"> "+($("#ite_vlrunid").val() * parseInt(quantidade)).toFixed(2)+" </td>" : "")+
                    "<td align=\"center\">"+
                        "<img src=\""+baseUrl+"/public/images/icons/editar.png\"  style=\"cursor:pointer;\" onclick=\"editar('"+replaceSpecialChars($("#pro_codigo").val()+"|"+lote)+"')\" />&nbsp"+
                        "<img src=\""+baseUrl+"/public/images/icons/excluir2.png\" style=\"cursor:pointer;\" onclick=\"excluir('"+$("#pro_codigo").val()+lote+"')\" />"+
                    "</td>"+
                "</tr>";
    $(".itens-entrada-dir").show();
    
    if($("."+$("#pro_codigo").val()+lote).length > 0){
        mensagem("Erro","Já existe um produto com mesmo lote incluso!",300,150);
        return false;
    }
    if($("#mov_tipo").val() == "E"){
        var total = 0;
        $(".valor_total").before(table);
        $(".item_entrada").each(function(){
            total += parseFloat($(this).data("vlrtotal"));
        });
        $("#vlr_total").html(" Valor Total: R$ "+total.toFixed(2))
    }else{
        $("#table-itens").append(table);
    }
    montaZebrado();
    $("#div_finalizar").show();
    $("#pro_codigo").val("");
    $("#pro_nome").val("");
    $("#ite_quantidade").val("");
    $("#ite_lote").val("");
    $("#ite_doses").val("");
    $("#ite_codigo").val("");
    $("#edit").val("");
    setTimeout(function() { $('#pro_nome').focus() }, 500);
    if($("#mov_tipo").val() == "S" || $("#mov_tipo").val() == "T"){
        $("#div_lotes").html("");
        $("#div_lotes").hide();
    }else if($("#mov_tipo").val() == "E"){
        $("#ite_validade").val("");
        $("#pro_frmmin").val("");
        $("#ite_vlrunid").val("");
        $("#fab_codigo").val("");
        $("#fab_descricao").val("");
    }

}

function excluirItem(pro_lote,ite_codigo){//metodo apenas utilizado quando editar
    var produto_lote = pro_lote.split("|");
    if(verificaSeJaRealizouMovimentacao(produto_lote[1],ite_codigo)){
        excluir(produto_lote[0]+produto_lote[1]);
        $(".itens-entrada-dir").append("<input type=\"hidden\" name=\"ite_codigo_del[]\" class=\"ite_codigo_del\" value=\""+ite_codigo+"\">");
    }else{
        mensagem("Erro","Já existem movimentações pra esse produto",300,150);
    }
    
}

function excluir(pro_codigo_lote){ // metodo que apenas usa qnd inserir
    $("."+replaceSpecialChars(pro_codigo_lote)).remove();
    montaZebrado();
    if($(".item_entrada").length <= 0 && $("#finalizar_all").val() != 1){
        $("#div_finalizar").hide();
        $(".itens-entrada-dir").hide();
    }
}
function calculaTotal(){
     var valor_total = 0;
     $(".item_entrada").each(function(){
         valor_total = valor_total + (parseFloat($(this).data("valor")) * parseInt($(this).data("qtde")));
     });
     return valor_total;
}
function editar(pro_codigo_lote,ite_codigo){
    
    var produto_lote = pro_codigo_lote.split("|");
    
    if($("#edit").val() != ""){
        mensagem("Erro","Só pode ser editado um item por vez",300,150);
        return false;
    }
    
    
    pro_codigo_lote = produto_lote[0]+produto_lote[1];
    
    if(verificaSeJaRealizouMovimentacao($("."+pro_codigo_lote).data("lote").replace("'",""),ite_codigo)){
        $("#pro_codigo").val($("."+pro_codigo_lote).data("pro"));
        $("#pro_nome").val($("."+pro_codigo_lote).data("nome"));
        $("#ite_quantidade").val($("."+pro_codigo_lote).data("qtde"));
        $("#ite_lote").val($("."+pro_codigo_lote).data("lote").replace("'",""));
        $("#ite_doses").val($("."+pro_codigo_lote).data("doses"));
        $("#ite_codigo").val($("."+pro_codigo_lote).data("ite"));
        $("#edit").val("1");
        if($("#mov_tipo").val() == "S" || $("#mov_tipo").val() == "T"){
            carregaComboLote($("."+pro_codigo_lote).data("lote"),$("."+pro_codigo_lote).data("qtde"),$("."+pro_codigo_lote).data("ite"));
            $("#div_lotes").show();

        }else if($("#mov_tipo").val() == "E"){
            $("#ite_validade").val($("."+pro_codigo_lote).data("val"));
            $("#ite_vlrunid").val($("."+pro_codigo_lote).data("valor"));
            $("#pro_frmmin").val($("."+pro_codigo_lote).data("frm"));
            $("#fab_codigo").val($("."+pro_codigo_lote).data("fab"));
            $("#fab_descricao").val($("."+pro_codigo_lote).data("nfab"));
            $("#lote_validade_entrada").show();

        }

        $("."+pro_codigo_lote).remove();
        if($(".item_entrada").length <= 0){
            $("#div_finalizar").hide();
            $(".itens-entrada-dir").hide();
        }
    }else{
        mensagem("Erro","Já existem movimentações pra esse produto",300,150);
    }
    
    $("#vlr_total").html("Valor Total: R$"+calculaTotal().toFixed(2));
    montaZebrado();
    
}

function finalizaMovimentacao(){
    if($("#edit").val() != ""){
        mensagem("Erro","Há itens a serem editados",300,150);
        return false;
    }
    mensagemSemOk("carregando-ate1", "Aguarde", "Carregando...", 280, 80);

    var itens = new Array();
    
    $(".item_entrada").each(function(){
		
        if($("#mov_tipo").val() == "S" || $("#mov_tipo").val() == "T"){
            itens.push
                (new Item($(this).data("pro"),
                          $(this).data("lote").replace("'",""),
                          $(this).data("qtde"),
                          $(this).data("val"),"",
                          $(this).data("doses") ,
                          $("#mov_codigo").val(),
                          $(this).data("ite"),"",""));

        }else if($("#mov_tipo").val() == "E"){
            itens.push
                (new Item($(this).data("pro"),
                          $(this).data("lote").replace("'",""),
                          $(this).data("qtde"),
                          $(this).data("val"),
                          $(this).data("valor"),
                          $(this).data("doses") ,
                          $("#mov_codigo").val(),
                          $(this).data("ite"),
                          $(this).data("vlrtotal"),
                          $(this).data("fab"),
                          $(this).data("frm")));
        }
        
    });
    
    var itens_deletar_banco = new Array();
    $(".ite_codigo_del").each(function(){
        itens_deletar_banco.push($(this).val());
    });
    
    
    
    $("#carregando-ate1").dialog("destroy").remove();
    confirme("Confirme:", "Deseja realmente gerar movimentação?", 300, 150, function(){
        if($("#finalizar_all").val() == 1){
            salvarCadastro();
        }

        $("#div_finalizar").hide();

        $.ajax({
            url: baseUrl+"/materiais/movimentacao/salvar-itens/",
            type: "POST",
            data: {itens:itens,
                   itens_deletar_banco:itens_deletar_banco},
            success: function(txt){

                $("#carregando-ate1").dialog("destroy").remove();

                if(txt.id == "" || txt.id == null || txt.id == "undefined"){
                    mensagem("Erro",txt.msg,300,150);
                    $("#div_finalizar").show();
                }else{
                    if ($("#config_imp").val() == 1) {
                        if($("#mov_tipo").val() == "E"){
                            window.open(baseUrl+"/materiais/movimentacao/imprime-entradas/mov_codigo/"+$("#mov_codigo").val(),'page','toolbar=no,left=0,top=0,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=750,height=700');  
                        }
                        if($("#mov_tipo").val() == "S"){
                            window.open(baseUrl+"/materiais/movimentacao/imprime-saidas/mov_codigo/"+$("#mov_codigo").val(),'page','toolbar=no,left=0,top=0,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=750,height=700');  
                        }
                        if($("#mov_tipo").val() == "T"){
                            window.open(baseUrl+"/materiais/movimentacao/imprime-transferencias/mov_codigo/"+$("#mov_codigo").val(),'page','toolbar=no,left=0,top=0,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=750,height=700');  
                        }
                    }
                    sucesso_salvar();
                }
            }
        });
    
    
    
    });
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

function sucesso_salvar(){
    window.location = baseUrl + "/materiais/movimentacao";
}

function Item(pro_codigo, ite_lote, ite_quantidade, ite_validade, ite_vlrunid, ite_doses, mov_codigo,ite_codigo,ite_vlrtotal,fab_codigo,pro_frmmin){
    this.pro_codigo = pro_codigo;
    this.ite_lote = ite_lote;
    this.ite_quantidade = ite_quantidade;
    this.ite_validade = ite_validade;
    this.ite_vlrunid = ite_vlrunid;
    this.ite_doses = ite_doses;
    this.mov_codigo = mov_codigo;
    this.fab_codigo = fab_codigo;
    this.ite_vlrtotal = ite_vlrtotal;
    this.pro_frmmin = pro_frmmin;
    if(ite_codigo){
        this.ite_codigo = ite_codigo;
    }

}

function verificaSeJaRealizouMovimentacao(ite_lote,ite_codigo){
    var val = "";
    $.ajax({
            url: baseUrl+"/materiais/movimentacao/verifica-se-movimentou/",
            type: "POST",
            data: {ite_lote:ite_lote,
                   ite_codigo:ite_codigo},
            async:false,
            success: function(txt){
                if(txt >= 1){
                    val = 0;
                }else{
                    val = 1;
                }
                
            }
        });
        
    //if aqui por que booleano nao funciona dentro do succes pra retorno
    if(val == 1){
        return true;
    }else{
        return false;
    }
}

function validaData(e){
    if($(e).val().length > 0){
       if(VerificaData(e)){
            return true;
        }else{
            $("#ite_validade").val("");
             setTimeout(function() { $('#ite_validade').focus() }, 500);$("#ite_validade").focus();
        }
    }
    
}