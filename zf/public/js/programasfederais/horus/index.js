$(function (){
    $("#aguarde-horus").hide();
    $("#selecione-mes-exportacao").show();
    $("body").append("<div id='horus-dialog' title='Data de Exportação Horus'></div>");
    $("#horus-dialog").
    html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Cadastro Mês de Exportação Horus ...\" />")
    .dialog({
        modal:true,
        width: 400,
        height: 200,
        buttons:{
            Cancelar: function(){
                $("#horus-dialog").dialog("destroy").remove();
            },
            Exportar: function(){
                // Chama a função que gera a movimentação de entrada, e assim por diante chamará as outras
                mensagemSemOk("carregando-ate","Aguarde","EXPORTANDO MOVIMENTAÇÕES PARA O HORUS ...",320,100);
                geraMovimentacaoEntrada($("#mes_exportacao_horus").val(), $("#ano_exportacao_horus").val());
            }
        }
    }).load(baseUrl+"/programasfederais/horus/informa-mes-de-exportacao"); 
});

// Variaveis responsável pela contagem e msgs de erros
var msgErro = "";
var msgOk = "";
function geraMovimentacaoEntrada(mesExp, anoExp){
    $("#selecione-mes-exportacao").hide();
    $("#aguarde-horus").show();
    $.ajax({
        url:baseUrl+"/programasfederais/horus/gera-movimentacao-entrada",
        type: "POST",
        data:{
            mesExp: mesExp,
            anoExp: anoExp
        },
        success:function(txt) {
            $("#horus-dialog").dialog("destroy").remove();
            msgErro = "";
            validaExportacao(txt,'Entrada'); 
            geraMovimentacaoSaida(mesExp, anoExp);
        }
    });
}

function geraMovimentacaoSaida(mesExp, anoExp){
   $.ajax({
        url:baseUrl+"/programasfederais/horus/gera-movimentacao-saida",
        type: "POST",
        data:{
            mesExp: mesExp,
            anoExp: anoExp
        },
        success:function(txt) {
            msgErro = "";
            validaExportacao(txt,'Saida'); 
            geraMovimentacaoDispensacao(mesExp, anoExp);
        }
    });
}

function geraMovimentacaoDispensacao(mesExp, anoExp){
   $.ajax({
        url:baseUrl+"/programasfederais/horus/gera-movimentacao-dispensacao",
        type: "POST",
        data:{
            mesExp: mesExp,
            anoExp: anoExp
        },
        success:function(txt) {
            msgErro = "";
            validaExportacao(txt,'Dispensacao');
            fecharMensagemSemOk("carregando-ate");
            $("#aguarde-horus").fadeOut('slow', function(){ $(this).remove(); });
        }
    });
}

// Função que valida todos os erros
function validaExportacao(txt,tpMov){
    msgErro = validaConfiguracao(txt)+validaConexao(txt,tpMov)+validaQtdRegistros(txt,tpMov)+validaCabecalhoXml(txt,tpMov)+validaErroHorus(txt,tpMov);
    if (msgErro == ""){
        msgOk = "<p class='msgOk'>Movimentações de "+tpMov+" realizada com sucesso,\n\
                             48 horas após o envio, o HORUS disponibiliza os dados para visualização \n\
                             por meio dos nossos relatórios, o número de protocolo para consulta é\n\
                             "+txt+"<p>";
        switch (tpMov){
            case 'Entrada':
                $("#resultado-entrada").prepend(msgOk);
            break;
            case 'Saida':
                $("#resultado-saida").prepend(msgOk);
            break;
            case 'Dispensacao':
                $("#resultado-dispensacao").prepend(msgOk);
            break;
        }
    } else {
        switch (tpMov) {
            case 'Entrada':
                $("#resultado-entrada").prepend(msgErro);
            break;
            case 'Saida':
                $("#resultado-saida").prepend(msgErro);
            break;
            case 'Dispensacao':
                $("#resultado-dispensacao").prepend(msgErro);
            break;
        }
    }
}

// Função que verifica se a configuração está ativa
function validaConfiguracao(txt){
    // Se não conter o erro na variavel de msg o erro é gerado
    if (msgErro.indexOf("Configuração Dia de Exportação")==-1) {
        if (txt == "erroconfiguracao") {
            msgErro += "<p class='msgErro'> - Configuração Dia de Exportação HORUS não esta ativa, \n\
                        realize a sua ativação \n\</p>";
            return msgErro;
        } 
    }
    return "";
}

// Função que verifica se a conexão está OK
function validaConexao(txt,tpMov){
    if(msgErro.indexOf("Falha ao se conectar")==-1){
        if(txt.indexOf("HTTP request failed!")!=-1 || txt.indexOf("failed to open stream")!=-1){
            msgErro += "<p class='msgErro'> - Falha ao enviar as movimentaçãoes de "+tpMov+", não foi possível realizar a conexão com o Web Service HORUS, por favor, tente novamente mais tarde!<p>";
            return msgErro;
        } 
    }
    return "";
}

// Função que verifica se existe registros a ser importado ou não
function validaQtdRegistros(txt,tpMov){
   if(msgErro.indexOf("Não existe nenhuma Movimentação")==-1){
       if(txt=="erronumregistro"){
           msgErro = "<p class='msgErro'> - Não existe nenhuma Movimentação de "+tpMov+" a ser exportada neste momento ou periodo informado! \n\<p>";
           return msgErro;
       } 
   }
   return "";
}

function validaCabecalhoXml(txt,tpMov){
    if(msgErro.indexOf("verifique os cadastros")==-1){
        if(txt=="errocabecalho"){
            msgErro = "<p class='msgErro'> - Falha ao enviar as Movimentações de "+tpMov+", responsável pelo envio e código ibge da unidade não identificado! \n\
            Para corrigir o erro, verifique os cadastros de Usuários e Unidades!<p>";
            return msgErro;
        } 
    }
    return "";
}

function validaErroHorus(txt,tpMov){
    if(msgErro.indexOf("Falha ao enviar as Movimentações")==-1){
       if(txt=="errohorus"){
            msgErro = "<p class='msgErro'> - Falha ao enviar as Movimentações de "+tpMov+" para o Web Service HORUS, por favor, <br />\n\
                        entre em contato com o suporte e nos informe o erro com o seguite título: <br />\n\
                        Erro ao enviar as Movimentações de "+tpMov+"!<p>";
            return msgErro;
       } 
   }
   return "";
}

