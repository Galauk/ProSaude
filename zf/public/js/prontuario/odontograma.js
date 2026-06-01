/* -----------------------------------/ 
 * FUNÇÕES GERAIS                     /  
 * ----------------------------------*/
// Função que carrega ao abrir a página
var $nome,
msgHoverOut = "<strong>Selecione um dente</strong>";
$(function(){
  $nome = $('#nome');
  $(".adulto, .crianca").click(function(){
    $('#odo_proc_dtprogramada').live('focus', function(){
      $(this).datepicker({ changeMonth: true, changeYear: true }) 
    });
    // Pegando o ID do dente
    var denteNum = $(this).data("dente");
    // Pega o Nome do dente, de acordo com a função getNome
    var dente = getNome(denteNum);
    // Exibindo nome do dente 
    $("body").append("<div id=\"dente-dialog\" title=\"Dente: "+dente+"\"></div>");
    $("#dente-dialog")
    .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Cadastro de Procedimento...\" />")
    .dialog({
      modal: true,
      width: 640,
      height: 380,
      buttons:{
        Cancelar: function(){
          $("#dente-dialog").dialog( "close" );
        },
        Salvar: function(){
          if (validarTratamento() == true) {   
            //mensagemSemOk("salvando-dados-procedimento", "Aguarde", "Salvando dados do procedimento ...", 280, 80);
            if($("#proc_codigo option:selected").hasClass("0414020146") || $("#proc_codigo option:selected").hasClass("0307030016") || $("#proc_codigo option:selected").hasClass("0307030024") ||$("#proc_codigo option:selected").hasClass("0307030032") ){ // procedimentos de sextante.
              var contador = 0;
              if($("#odo_proc_dentenum").val() >= 11 && $("#odo_proc_dentenum").val() <= 18){
                contador = 1;
                for(i = 11; i<=18;i++){
                 ajaxSalvar(i,$("#proc_codigo").val(),$("#odo_proc_denteanot").val(),$("#odo_proc_dtprogramada").val());
                }
              }else if($("#odo_proc_dentenum").val() >= 21 && $("#odo_proc_dentenum").val() <= 28){
                for(i = 21; i<=28;i++){
                 ajaxSalvar(i,$("#proc_codigo").val(),$("#odo_proc_denteanot").val(),$("#odo_proc_dtprogramada").val());
                }
              }else if($("#odo_proc_dentenum").val() >= 42 && $("#odo_proc_dentenum").val() <= 48){
                for(i = 42; i<=48;i++){
                 ajaxSalvar(i,$("#proc_codigo").val(),$("#odo_proc_denteanot").val(),$("#odo_proc_dtprogramada").val());
                }
              }else if ($("#odo_proc_dentenum").val() >= 38 && $("#odo_proc_dentenum").val() <= 41){
                for(i = 38; i<=41;i++){
                 ajaxSalvar(i,$("#proc_codigo").val(),$("#odo_proc_denteanot").val(),$("#odo_proc_dtprogramada").val());
                }
                  
              }
            }else{
              ajaxSalvar($("#odo_proc_dentenum").val(),$("#proc_codigo").val(),$("#odo_proc_denteanot").val(),$("#odo_proc_dtprogramada").val());
            }
            mensagem("Confirmação de Cadastro","Procedimento cadastrado com sucesso!", 350, 120);
            fecharMensagemSemOk("salvando-dados-procedimento");
            $("#dente-dialog").dialog("destroy").remove();
            //
          }
        }
      },
      close: function( event, ui ) {
        $("#dente-dialog").dialog("destroy").remove();
      }
    })
    .load(baseUrl+"/prontuario/odontograma/cadastra-procedimento/dente/"+denteNum, function(){
      // Depois vai ter que pintar os dentes  
      //incluiProcedimento();
      bindFaces();
    });
  }).hover(function(){
    $nome.html("<strong>Dente:</strong> "+getNome($(this).data("dente")));
  }, function(){
    $nome.html(msgHoverOut);
  });

  $(".sextante").click(function(){
    $('#odo_proc_dtprogramada').live('focus', function(){
      $(this).datepicker({ changeMonth: true, changeYear: true }) 
    });
    // Pegando o ID do dente
    var sextante = $(this).data("dente");
    // Exibindo nome do dente 
    $("body").append("<div id=\"dente-dialog\" title=\"Sextante: "+SEXTANTES[sextante].nome+"\"></div>");
    $("#dente-dialog")
    .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Cadastro de Procedimento...\" />")
    .dialog({
      modal: true,
      width: 640,
      height: 380,
      buttons:{
        Cancelar: function(){
          $("#dente-dialog").dialog( "close" );
        },
        Salvar: function(){
          if (validarTratamento() == true) {   
            for(var i in SEXTANTES[sextante].dentes){
             ajaxSalvar(SEXTANTES[sextante].dentes[i],$("#proc_codigo").val(),$("#odo_proc_denteanot").val(),$("#odo_proc_dtprogramada").val());
            }
            mensagem("Confirmação de Cadastro","Procedimento cadastrado com sucesso!", 350, 120);
            fecharMensagemSemOk("salvando-dados-procedimento");
            $("#dente-dialog").dialog("destroy").remove();
          }
        }
      },
      close: function( event, ui ) {
        $("#dente-dialog").dialog("destroy").remove();
      }
    })
    .load(baseUrl+"/prontuario/odontograma/cadastra-procedimento/dente/"+SEXTANTES[sextante].dentes[0], function(){
      // Depois vai ter que pintar os dentes  
      //incluiProcedimento();
      bindFaces();
    });
  }).hover(function(){
    $nome.html('<strong>Sextante:</strong> '+SEXTANTES[$(this).data('dente')].nome);
  }, function(){
    $nome.html(msgHoverOut);
  });
  // Carrega o histórico de procedimentos realizados pelo paciente
  carregaHistDeProcReal();    
});

function buscarProcedimentoOdontologico(){
  $("#proc_nome_buscar").buscar({
    url: baseUrl+'/prontuario/odontograma/buscar-procedimento-odontologico/term/'+$("#proc_nome_buscar").val(),
    suffix: '_2',
    search: function(){
      $("#proc_codigo").empty();
    },
    template : function(ul, item) {
      ul.hide();			
      $("<option />").val(item.id).html(item.label).appendTo("#proc_codigo");
      return false;
    },
    callback: function(event, ui){
      $("#proc_codigo").focus();
    }
  });
}

function ajaxSalvar(odo_proc_dentenum,proc_codigo,odo_proc_denteanot,odo_proc_dtprogramada){
  $.ajax({
    url:baseUrl+"/prontuario/odontograma/salvar-procedimento",
    type: "POST",
    data: {
      odo_proc_dentenum: odo_proc_dentenum,
      odo_proc_denteface: retornaFaces(),
      proc_codigo: proc_codigo,
      odo_proc_denteanot: odo_proc_denteanot,
      odo_proc_status: retornaStatus(),
      odo_proc_dtprogramada: odo_proc_dtprogramada
    },
    success:function(txt){
      $("#dente-dialog").dialog("destroy").remove();
     
      if (txt != "procedimentoInserido") {
        incluiProcedimentoRealizado(txt);
        $("#dente-dialog").dialog("destroy").remove();
      }
        
    }
  });
}

// Função que retorna faces selecionadas

function mostra_dt_programada(){
  $(".data-programada").show();
}

function esconde_dt_programada(){
  $(".data-programada").hide();
}

function retornaFaces(){
 var face = "";
 
  if($("#proc_codigo option:selected").hasClass("0414020120") || $("#proc_codigo option:selected").hasClass("0414020138") || $("#proc_codigo option:selected").hasClass("0414020146") || $("#proc_codigo option:selected").hasClass("0307030016") || $("#proc_codigo option:selected").hasClass("0307030024") ||$("#proc_codigo option:selected").hasClass("0307030032") ){
     $("#face5").prop('checked', true);
     $("#face4").prop('checked', true);
     $("#face3").prop('checked', true);
     $("#face2").prop('checked', true);
     $("#face1").prop('checked', true);
 }
 
 if( $("#face1").is(':checked') ){
  face += $("#face1").val();
 }
 if( $("#face2").is(':checked') ){
  face += $("#face2").val();
 }
 if( $("#face3").is(':checked') ){
  face += $("#face3").val();
 }
 if( $("#face4").is(':checked') ){
  face += $("#face4").val();
 }
 if( $("#face5").is(':checked') ){
  face += $("#face5").val();
 }
 return face;
}
// Função que retorna status selecionadas
function retornaStatus(){
 if($("#odo_proc_statusT").is(':checked')){
   return true;
 } else {
   return false;
 }
}
// Adiciona eventos depois de abrir o dialog(modal) do dente
function bindFaces(){
  $("area").click(function(){
    var area = $(this).data("area");
    var ckeckbox = $("#face"+area);
    ckeckbox.attr('checked',!ckeckbox.is(':checked'));
  }).hover(function(){		
    var area = $(this).data("area");
    $("#face"+area).next().css("font-weight","bold");
  }, function(){
    var area = $(this).data("area");
    $("#face"+area).next().css("font-weight","normal");
  });
}
// globais
var DENTES_ADULTO = new Array( '', 'Incisivo Central', 'Incisivo Lateral', 'Canino', '1º Premolar', '2º Premolar', '1º Molar', '2º Molar', '3º Molar');
var DENTES_CRIANCA = new Array( '', 'Incisivo Central', 'Incisivo Lateral', 'Canino', '1º Molar', '2º Molar' );
var SEXTANTES = {
  se:{nome:'superior esquerdo', dentes:[21,22,23,24,25,26,27,28]},
  sd:{nome:'superior direito', dentes:[11,12,13,14,15,16,17,18]},
  ie:{nome:'inferior esquerdo', dentes:[31,32,33,34,35,36,37,38]},
  id:{nome:'inferior direito', dentes:[41,42,43,44,45,46,48]},
};
// Retorna o nome do dente de acordo com o tipo citado acima
function getNome(n){
  var n = n.toString();
  var q = n.substring(0,1);
  var d = n.substring(1);
  var qs,qp;	

  if( q <= 4 ){ // adulto
    qs  = ( q == 1 || q == 2 ? 'superior' : 'inferior' );
    qp	= ( q == 1 || q == 4 ? 'direito' : 'esquerdo' );
    return DENTES_ADULTO[d] + ' ' + qs + ' ' + qp;

  } else{ // crianca
    qs  = ( q == 5 || q == 6 ? 'superior' : 'inferior' );
    qp	= ( q == 5 || q == 8 ? 'direito' : 'esquerdo' );
    return DENTES_CRIANCA[d] + ' ' + qs + ' ' + qp;
  }
}
function imprimirProcedimentos(){
  $("body").append("<div id='imprimir-procedimentos' title='Impressão de procedimentos realizados e a realizar' ></div>");
  $("#imprimir-procedimentos")
  .html("<img src="+baseUrl+"/public/images/load.gif alt='Carregando' title='Carregando impressão ...' />")
  .dialog({
    modal:true,
    width: 640,
    height: 380,
    buttons: {
      Imprimir:function(){
        //$('.camada_para_impressao').printElement();
        $("#print").printElement();
      }
    }
  })
  .load(baseUrl+"/prontuario/odontograma/imprimir-procedimentos");
}
function imprimirOdontograma(){
  $("body").append("<div id='imprimir-odontograma' title='Impressão de odontograma e procedimentos realizados' ></div>");
  $("#imprimir-odontograma")
  .html("<img src="+baseUrl+"/public/images/load.gif alt='Carregando' title='Carregando impressão ...' />")
  .dialog({
    modal:true,
    width: 760,
    height: 550,
    buttons: {
      Imprimir:function(){
        //$('.camada_para_impressao').printElement();
        $("#print").printElement();
      }
    }
  })
  .load(baseUrl+"/prontuario/odontograma/imprimir-odontograma",function (){
    $(".adulto, .crianca").each(function(){
      // Armazenando o valor de cada dente, de acordo com cada ID
      this.innerHTML = $(this).data("dente");
    })
    carregaHistDeProcReal();
  });
}
// Função que valida os campos obrigatórios
function validarTratamento(){
  var v_checked = $('input:radio[name="odo_proc_status"]:checked').val();
  var msg_erro = "";
  if(v_checked == "F"){
    var data_atual = $("#dt_atual").val();
    var data_programada = $("#odo_proc_dtprogramada").val().split("/");
    var dt_programada = data_programada[2]+"-"+data_programada[1]+"-"+data_programada[0];
    
    if(!$("#odo_proc_dtprogramada").val()){
        msg_erro += " * Informe uma data para o procedimento!<br />";
    }
    
    if(data_atual > dt_programada){
        msg_erro +=  msg_erro += " * A data a ser realizada é maior que a atual!<br />";
    }
  }
  
  if(!$("#proc_codigo").val()){
      msg_erro += " * Selecione um procedimento!<br />";
  }
  
  //alert(retornaFaces());
  if(retornaFaces() == ""){
      msg_erro += " * Face do procedimento não selecionada!";
  }
  if (msg_erro != "") {
      $("#action-errors").remove();
      $("#formDente").prepend("<div class=\"ui-state-highlight erro\" id=\"action-errors\" style=\"width: 556px;\">"+msg_erro+"</div>");
      return false;
  } else {    
      return true;
  }
}
/* -----------------------------------/ 
 * FIM DAS FUNÇÕES GERAIS             /  
 * ----------------------------------*/
/* -----------------------------------/ 
 * FUNÇÕES TRATAMENTOS                /  
 * ----------------------------------*/
// Função responsavel por salvar o inicio do tratamento e a carregar o odontograma
function iniciarTratamento() {    
  mensagemSemOk("carregando-ate", "Aguarde", "Iniciando tratamento...", 280, 80);
  $.ajax({
    url: baseUrl+'/prontuario/odontograma/salvar-tratamento',
    type: "POST",
    success: function(txt){
        window.location = baseUrl + "/prontuario/odontograma";	
    },
    error: function(pq){alert(pq)}
  });
}
// Função responsavel por carregar o modal de lista de tratamentos realizados
function listaTratamentosRealizados(){
  $("body").append("<div id='tratamentos-realizados' title='Consulta de listagem de tratamentos realizados' ></div>");
  $("#tratamentos-realizados")
  .html("<img src="+baseUrl+"/public/images/load.gif alt='Carregando' title='Carregando listagem de tratamentos' />")
  .dialog({
    modal:true,
    width: 640,
    height: 380
  })
  .load(baseUrl+"/prontuario/odontograma/lista-tratamentos-realizados");
}
// Função que carrega o tratamento
function consultaTratamento(odo_trat_codigo){
  // Exibindo nome do dente 
  $("body").append("<div id=\"consulta-tratamento\" title=\"Consulta de tratamento\"></div>");
  $("#consulta-tratamento")
  .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando consulta...\" />")
  .dialog({
    modal: true,
    width: 760,
    height: 500,
    buttons: {
      Imprimir:function(){
        //$('.camada_para_impressao').printElement();
        $("#print").printElement();
      }
    }
  })
  .load(baseUrl+"/prontuario/odontograma/consulta-tratamento/tratCodigo/"+odo_trat_codigo,function(){
    $(".consulta-adulto, .consulta-crianca").each(function(){
      // Armazenando o valor de cada dente, de acordo com cada ID
      this.innerHTML = $(this).data("consulta-dente");
    });
    carregaConsultaHistDeProcReal(odo_trat_codigo);
    //carregaHistDeProcReal();
  });
}
// Função responsável por executar a finalização do tratamento
function finalizarTratamento(){
    $.ajax({
        url:baseUrl+"/prontuario/odontograma/finalizar-tratamento",
        type: "POST",
        success:function (txt){
            if (txt == "ok")  {
                mensagemSemOk("carregando-ate","Aguarde","Finalizando Tratamento",280,80);
                window.location = baseUrl+"/prontuario/odontograma/"
            } else {
               mensagem("Erro","Ainda existe procedimentos a serem realizado!", 350, 120);
            }
        }
    });
}
// Limpas dente do odontograma do tratamento carregado
function limparDentesConsulta(){
    $("div[data-consulta-dente] div").remove();
    _b = [];
}
// Pinta dentes do odontograma do tratamento carregado
function pintarDentesConsulta(json){
    for(var i in json){
        var denteNum = json[i].n;
        var faces = json[i].f;
        var proc  = json[i].s;
        var valExo  = json[i].e;
        for(var x in faces){
            addProcedimentoConsulta(denteNum, faces[x], proc, valExo);
        }
    }
}
// Adiciona procedimento do dente do odontograma do tratamento carregado
var _b = [];
function addProcedimentoConsulta(denteNum, face, procedimento, valExo){
    // Controle das faces de cima e de baixo
    if (denteNum >= 11 && denteNum <= 18) {
        var faces = {
             "V" : 3,
             "M" : 2,
             "L" : 1,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
    }
    
    if (denteNum > 18 && denteNum <= 28) {
        var faces = {
             "V" : 3,
             "M" : 4,
             "L" : 1,
             "D" : 2,
             "O" : 5,
             "N" : "full"
         }
    }
    
    if(denteNum > 38 && denteNum <= 48 ) {
        var faces = {
             "V" : 1,
             "M" : 2,
             "L" : 3,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
    }
    
    if (denteNum > 28 && denteNum <= 38) {
        var faces = {
             "V" : 1,
             "M" : 4,
             "L" : 3,
             "D" : 2,
             "O" : 5,
             "N" : "full"
         }
    }
    /*if (denteNum > 28) {
        var faces = {
             "V" : 3,
             "M" : 2,
             "L" : 1,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
     } else {
        var faces = {
             "V" : 1,
             "M" : 2,
             "L" : 3,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
     }*/
     // Setando a div do dente para dente
     var dente = $("div[data-consulta-dente="+denteNum+"]");
     
     if (valExo == 1) {
        //$("div[data-dente="+denteNum+"]").attr('disabled','disabled');
        var imgErro = baseUrl+"/public/images/dente/dentes_erros/dente_exodontia.gif";
        $("div[data-consulta-dente="+denteNum+"]").addClass("ui-state-disabled");
        $("div[data-consulta-dente="+denteNum+"]").attr('onclick','').unbind('click');
        dente.append("<div style='position:absolute; top:11px; right:0px; padding: 1px 4px;'><img src='"+imgErro+"' border=0 />");
     }
     
     var img = baseUrl+"/public/images/dente/dentes_novos/adulto/vermelho/dente_adulto_verm_face"+faces[face]+".gif";
     // Insere imgs azul
     if (denteNum == 18 || denteNum == 28 || denteNum == 15 || denteNum == 12 || denteNum == 22 || denteNum == 25 || denteNum == 47 || denteNum == 44 || denteNum == 41 || denteNum == 33 || denteNum == 36) {
        // Indica onde esta a imagem
         var img = baseUrl+"/public/images/dente/dentes_novos/adulto/azul/dente_adulto_azul_face"+faces[face]+".gif";
     }
     // Insere imgs marrom
     if (denteNum == 17 || denteNum == 14 || denteNum == 11 || denteNum == 23 || denteNum == 26 || denteNum == 48 || denteNum == 45 || denteNum == 42 || denteNum == 32 || denteNum == 35 || denteNum == 38) {
        // Indica onde esta a imagem
        var img = baseUrl+"/public/images/dente/dentes_novos/adulto/marrom/dente_adulto_marrom_face"+faces[face]+".gif";
     }
     // Insere imgs vermelho
     if (denteNum == 16 || denteNum == 13 || denteNum == 21 || denteNum == 24 || denteNum == 27 || denteNum == 46 || denteNum == 43 || denteNum == 31 || denteNum == 34 || denteNum == 37) {
        // Indica onde esta a imagem
        var img = baseUrl+"/public/images/dente/dentes_novos/adulto/vermelho/dente_adulto_verm_face"+faces[face]+".gif";
     }
     // Inserindo onde esta a imagem
     dente.append("<div style='position:absolute; top:11px; right:0px; padding: 1px 4px;'><img src='"+img+"' border=0 />");
}
/* -------------------------------------/
 * FIM DAS FUNÇÕES DE TRATAMENTOS       /
 * ------------------------------------*/
/* ---------------------------------------------/
 * FUNÇÕES DE PROCEDIMENTOS A REALIZAR          /
 * --------------------------------------------*/
// Função responsável por chamar os procedimentos a realizar
function procedimentosaRealizar(){
    // Exibindo nome do dente 
    $("body").append("<div id=\"procedimentos-realizar\" title=\"Lista de procedimentos a realizar\"></div>");
    $("#procedimentos-realizar")
    .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Cadastro de Procedimento...\" />")
    .dialog({
            modal: true,
            width: 700,
            height: 400,
    })
    .load(baseUrl+"/prontuario/odontograma/lista-procedimentos/",function(){
        
    });
}
/* ---------------------------------------------/
 * FIM DAS FUNÇÕES DE PROCEDIMENTOS A REALIZAR  /
 * --------------------------------------------*/
/* -----------------------------------/ 
 * FUNÇÕES PROCEDIMENTOS REALIZADOS   /  
 * ----------------------------------*/
// Função que seta o procedimento como realizado exclui dos pensentes e insere nos realizados
function salvarProcedimentoRealizado(odo_proc_codigo){
    $("#proc-codigo"+odo_proc_codigo)
    .attr("src",baseUrl+"/public/images/loading.gif")
    .attr("title","Carregando");
    $.ajax({
        url: baseUrl+'/prontuario/odontograma/salvar-procedimento-realizado',
        type: "POST",
        data: {
            odo_proc_codigo: odo_proc_codigo
        },      
        success: function(txt){
            var odo_preal_codigo = txt;
            // Coloca nova img
            $("#proc-codigo"+odo_proc_codigo)
            .attr("src",baseUrl+"/public/images/icons/accept.png")
            .attr("title","Realizado");
            // E insere linha em procedimentos realizados, txt é o código a ser inserido
            incluiProcedimentoRealizado(odo_preal_codigo);
        }
    });
}
// Função que inclui linha nos procedimentos realizados
function incluiProcedimentoRealizado(odo_preal_codigo)
{
var listaProcedimentos = "";
$.ajax({url: baseUrl+"/prontuario/odontograma/get-procedimento-realizado",
    type: "POST",
    data:{
        odo_preal_codigo:odo_preal_codigo
    },
    success: function(txt){
        // Validaçoes
        if (txt.odo_preal_denteanot == "") {
            var denteanot = "--";
        } else {
            var denteanot = txt.odo_preal_denteanot;
        }
        // Criando lista de procedimentos
        listaProcedimentos +="<tr id='proc-num"+txt.odo_preal_codigo+"'>"+
                        "<td class='ui-state-default'>"+txt.proc_nome+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_preal_dentenum+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_preal_denteface+"</td>"+
                        "<td class='ui-state-default'>"+denteanot+"</td>"+
                        "<td class='ui-state-default'>"+
                                "<a href='#'>"+
                                        "<img title='Excluir' alt='Excluir' src='/WebSocialSaude/zf/public/images/icons/excluir.png' onclick=excluiProcedimentoRealizado("+txt.odo_preal_codigo+");></img>"+
                                "</a>"+
                        "</td>"+
                        "<td class='ui-state-default'>"+
                                "<a href='#'>"+
                                        "<img title='Editar' alt='Editar' src='/WebSocialSaude/zf/public/images/icons/editar.png' onclick='editaProcedimentoRealizado("+txt.odo_preal_codigo+");'></img>"+
                                "</a>"+
                        "</td>"+
                    "</tr>";
        $("#nenhum-result").remove();
        // Loading de carregando ...
        $("#result")
        .attr("src",baseUrl+"/public/images/load.gif")
        .attr("title","Carregando");
        // Inserindo procedimento realizado na lista
        $("#result").prepend(listaProcedimentos);
        // Pinta o dente do procedimento realizado
        carregaProcReal(odo_preal_codigo);
    }
});
}
// Função que exclui o procedimento realizado
function excluiProcedimentoRealizado(odo_preal_codigo){
    $.ajax({
        url:baseUrl+"/prontuario/odontograma/confere-procedimento-realizado-atendimento",
        type: "POST",
        data: {
            odo_preal_codigo: odo_preal_codigo
        },
        success:function(txt){
            if (txt == "true") {
                confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function(){
                    $("#proc-num"+odo_preal_codigo+"")
                    .attr("src",baseUrl+"/public/images/loading.gif")
                    .attr("title","Carregando");
                    $.ajax({
                        url:baseUrl+"/prontuario/odontograma/excluir-procedimento-realizado",
                        type: "POST",
                        data: {
                            odo_preal_codigo: odo_preal_codigo
                        },
                        success:function(txt){
                            $("#proc-num"+odo_preal_codigo).fadeOut('slow', function(){ $(this).remove(); });
                            //$("#proc-num"+odo_preal_codigo).remove(); 
                            // Carrega a lista de procedimento realizados
                            carregaHistDeProcReal();
                        }
                    });
                });
            } else {
                 mensagem("Erro:","Procedimento não está vinculado com o atendimento atual! Não pode ser apagado!", 320, 180);
            }
        }
    });
}
// Função que edita o procedimento realizado
function editaProcedimentoRealizado(odo_preal_codigo){
    $.ajax({
        url:baseUrl+"/prontuario/odontograma/confere-procedimento-realizado-atendimento",
        type: "POST",
        data: {
            odo_preal_codigo: odo_preal_codigo
        },
        success:function(txt){
            if (txt == "true") {
                // Exibindo nome do dente 
                $("body").append("<div id=\"edita-procedimento\" title=\"Edição de procedimentos realizado\"></div>");
                $("#edita-procedimento")
                .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Edição de Procedimento...\" />")
                .dialog({
                    modal: true,
                    width: 640,
                    height: 380,
                    data: {

                    },
                    buttons:{
                        Cancelar: function(){
                            $("#edita-procedimento").dialog("destroy").remove();
                        },
                        Salvar: function(){
                            mensagemSemOk("editando-dados-procedimento", "Aguarde", "Editando os dados do procedimento ...", 280, 80);
			    $.ajax({
                                url:baseUrl+"/prontuario/odontograma/salvar-posts-procedimento-realizado",
                                type: "POST",
                                data: {
                                    odo_preal_codigo: $("#odo_preal_codigo").val(),
                                    odo_pcon_codigo: $("#odo_pcon_codigo").val(),
                                    odo_preal_denteface: retornaFaces(),
                                    proc_codigo: $("#proc_codigo").val(),
                                    odo_preal_denteanot: $("#odo_preal_denteanot").val()
                                },
                                success:function(txt){
                                    $("#edita-procedimento").dialog("destroy").remove();
                                    mensagem("Confirmação:","Procedimento editado com sucesso!", 350, 120);
                                    $("#proc-num"+odo_preal_codigo).remove();
                                    incluiProcedimentoRealizado(odo_preal_codigo);
                                    fecharMensagemSemOk("editando-dados-procedimento");
                                }
                            });
                        }
                    }
                })
                .load(baseUrl+"/prontuario/odontograma/edita-procedimento-realizado/procRealCodigo/"+odo_preal_codigo+"",function(){
                    bindFaces();
                });
            } else {
                mensagem("Erro:","Procedimento não está vinculado com o atendimento atual! Não pode ser editado!", 320, 180);
            }
        }
    });
    // Carrega a lista de procedimento realizados
    carregaHistDeProcReal();
}
// Carrega o histórico de procedimentos realizados
function carregaHistDeProcReal(odo_trat_codigo){
    //carregandoAba(1);
    limparDentes();
    $.ajax({
        dataType: "json",
        url: baseUrl+"/prontuario/odontograma/lista-procedimentos-realizado",
        type: "POST",
        data: {
            odo_trat_codigo:odo_trat_codigo
        },
        success: function(json){
            pintarDentes(json);
            carregandoAba(0);
        }
    });
}
// Carrega o histórico de procedimentos realizados
function carregaConsultaHistDeProcReal(odo_trat_codigo){
    //carregandoAba(1);
    limparDentesConsulta();
    $.ajax({
        dataType: "json",
        url: baseUrl+"/prontuario/odontograma/lista-procedimentos-realizado-consulta",
        type: "POST",
        data: {
            odo_trat_codigo:odo_trat_codigo
        },
        //url: baseUrl+"/prontuario/odontograma/procedimentos",
        success: function(json){
            pintarDentesConsulta(json);
            carregandoAba(0);
        }
    });
}
// Carrega somente o procedimento realizado especificado
function carregaProcReal(odo_preal_codigo){
    $.ajax ({
        url: baseUrl+"/prontuario/odontograma/lista-procedimentos-realizado",
        type: "POST",
        data:{
            odo_preal_codigo: odo_preal_codigo
        },
        success: function(json) {
            pintarDentes(json);
        }
    });
}

function limparDentes(){
    $("div[data-dente] div").remove();
    _b = [];
}

function pintarDentes(json){
    for(var i in json){
        var denteNum = json[i].n;
        var faces = json[i].f;
        var proc  = json[i].s;
        var valExo = json[i].e;
        for(var x in faces){
            addProcedimento(denteNum, faces[x], proc, valExo);
        }
    }
}



var _b = [];
function addProcedimento(denteNum, face, procedimento, valExo){
    if (denteNum >= 11 && denteNum <= 18) {
        var faces = {
             "V" : 3,
             "M" : 2,
             "L" : 1,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
    }
    
    if (denteNum > 18 && denteNum <= 28) {
        var faces = {
             "V" : 3,
             "M" : 4,
             "L" : 1,
             "D" : 2,
             "O" : 5,
             "N" : "full"
         }
    }
    
    if(denteNum > 38 && denteNum <= 48 ) {
        var faces = {
             "V" : 1,
             "M" : 2,
             "L" : 3,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
    }
    
    if (denteNum > 28 && denteNum <= 38) {
        var faces = {
             "V" : 1,
             "M" : 4,
             "L" : 3,
             "D" : 2,
             "O" : 5,
             "N" : "full"
         }
    }
    
    
    // Controle das faces de cima e de baixo
    /*if (denteNum > 28) {
        var faces = {
             "V" : 3,
             "M" : 2,
             "L" : 1,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
     } else {
        var faces = {
             "V" : 1,
             "M" : 2,
             "L" : 3,
             "D" : 4,
             "O" : 5,
             "N" : "full"
         }
    }*/
    // Setando a div do dente para dente
    var dente = $("div[data-dente="+denteNum+"]");
    
    if (valExo == 1) {
        var imgErro = baseUrl+"/public/images/dente/dentes_erros/dente_exodontia.gif";
        $("div[data-dente="+denteNum+"]").addClass("ui-state-disabled");
        $("div[data-dente="+denteNum+"]").attr('onclick','').unbind('click');
        dente.append("<div style='position:absolute; top:11px; right:0px; padding: 1px 4px;'><img src='"+imgErro+"' border=0 />");
    } 
    
    var img = baseUrl+"/public/images/dente/dentes_novos/adulto/vermelho/dente_adulto_verm_face"+faces[face]+".gif";
    // Insere imgs azul
     if (denteNum == 18 || denteNum == 28 || denteNum == 15 || denteNum == 12 || denteNum == 22 || denteNum == 25 || denteNum == 47 || denteNum == 44 || denteNum == 41 || denteNum == 33 || denteNum == 36) {
        // Indica onde esta a imagem
         var img = baseUrl+"/public/images/dente/dentes_novos/adulto/azul/dente_adulto_azul_face"+faces[face]+".gif";
     }
     // Insere imgs marrom
     if (denteNum == 17 || denteNum == 14 || denteNum == 11 || denteNum == 23 || denteNum == 26 || denteNum == 48 || denteNum == 45 || denteNum == 42 || denteNum == 32 || denteNum == 35 || denteNum == 38) {
        // Indica onde esta a imagem
        var img = baseUrl+"/public/images/dente/dentes_novos/adulto/marrom/dente_adulto_marrom_face"+faces[face]+".gif";
     }
     // Insere imgs vermelho
     if (denteNum == 16 || denteNum == 13 || denteNum == 21 || denteNum == 24 || denteNum == 27 || denteNum == 46 || denteNum == 43 || denteNum == 31 || denteNum == 34 || denteNum == 37) {
        // Indica onde esta a imagem
        var img = baseUrl+"/public/images/dente/dentes_novos/adulto/vermelho/dente_adulto_verm_face"+faces[face]+".gif";
     }
     // Inserindo onde esta a imagem
     dente.append("<div style='position:absolute; top:11px; right:0px; padding: 1px 4px;'><img src='"+img+"' border=0 />");
}
/* ---------------------------------------------/
 * FIM DAS FUNÇÕES DE PROCEDIMENTOS REALIZADOS  /
 * --------------------------------------------*/


/*
function calend(){
    $(".data").datepicker();
}

function novoTratamento(){
    $(".data").datepicker();
    $("body").append("<div id=\"dente-dialog\" title=\"Odontograma - Cadastro de Tratamento\"></div>");
    $("#dente-dialog")
    .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
    .dialog({
            modal: true,
            width: 600,
            height: 280,
            buttons:{
                    Cancelar: function(){
                        $("#dente-dialog").dialog("destroy").remove();
                    },
                    Salvar: function(){
                        $.ajax({
                            url:baseUrl+"/prontuario/odontograma/salvar-tratamento",
                            type: "POST",
                            data: {
                                odo_trat_titulo: $("#odo_trat_titulo").val(),
                                odo_trat_dtinicial: $("#odo_trat_dtinicial").val(),
                                odo_trat_dtfinal: $("#odo_trat_dtfinal").val(),
                                odo_trat_dt_previsaofim: $("#odo_trat_dt_previsaofim").val(),
                                odo_trat_status: $("#odo_trat_status").val()
                            },
                            success:function(txt){
                                $("#dente-dialog").dialog("destroy").remove();
                                incluiTratamento();
                            }
                        })
                    }
            }
    })
    .load(baseUrl+"/prontuario/odontograma/novo-tratamento",function(){
    });
}

function incluiTratamento()
{
var listaTratamentos = "";
$.ajax({url: baseUrl+"/prontuario/odontograma/lista-tratamento",
    type: "POST",
    success: function(txt){
        listaTratamentos +="<tr id='proc-num"+txt.odo_trat_codigo+"'>"+
                        "<td class='ui-state-default'>"+txt.odo_trat_titulo+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_trat_dtinicial+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_trat_dtfinal+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_trat_dt_previsaofim+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_trat_status+"</td>"+
                        "</tr>";
        $("#result").prepend(listaTratamentos);
    }
});
}
function listaProcedimentos(){
$(function(){
	$(".adulto, .crianca").each(function(){
            // Armazenando o valor de cada dente, de acordo com cada ID
            this.innerHTML = $(this).data("dente");
        }).click(function(){		
            // Pegando o ID do dente
            var denteNum = $(this).data("dente").toString();
            // Pega o Nome do dente, de acordo com a função getNome
            var dente = getNome(denteNum);
            	// Exibindo nome do dente 
                $("body").append("<div id=\"dente-dialog\" title=\"Dente: "+dente+"\"></div>");
                $("input.data").datepicker();
		$("#dente-dialog")
		.html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" />")
		.dialog({
			modal: true,
			width: 640,
			height: 380,
			buttons:{
				Cancelar: function(){
                                    $("#dente-dialog").dialog("destroy").remove();
				},
				Salvar: function(){
                                    $("#formDente").submit();
                                    incluiProcedimento();
                                }
			}
		})
		.load(baseUrl+"/prontuario/odontograma/info/dente/"+denteNum,function(){
                    //incluiProcedimento();
                    bindFaces();
		});
	}).hover(function(){
		$nome.html("<strong>Dente:</strong> "+getNome( $(this).data("dente").toString() ));
		
	}, function(){
		$nome.html("<strong>Selecione um dente</strong>");
	});
        // Carrega o histórico do paciente
        carregarHistorico();
        
        
});

}

insereProcedimentoRealizado
function incluiProcedimento()
{
var listaProcedimentos = "";
$.ajax({url: baseUrl+"/prontuario/odontograma/lista-ultimo-procedimento",
    type: "POST",
    success: function(txt){
        
        listaProcedimentos +="<tr id='proc-num"+txt.odo_proc_codigo+"'>"+
                        "<td class='ui-state-default'>"+txt.proc_nome+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_proc_dentenum+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_proc_denteface+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_proc_data_programado+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_proc_finalizado+"</td>"+
                        "<td class='ui-state-default'>"+txt.odo_proc_denteanot+"</td>"+
                        "<td class='ui-state-default'>"+
                                "<a href='#'>"+
                                        "<img title='Excluir' alt='Excluir' src='/WebSocialSaude/zf/public/images/icons/excluir.png' onclick=excluiProcedimento("+txt.od_hist_codigo+");></img>"+
                                "</a>"+
                        "</td>"+
                        "<td class='ui-state-default'>"+
                                "<a href='#'>"+
                                        "<img title='Editar' alt='Editar' src='/WebSocialSaude/zf/public/images/icons/editar.png' onclick='editarProcedimento("+txt.od_hist_codigo+");'></img>"+
                                "</a>"+
                        "</td>"+
                    "</tr>";
        $("#result").prepend(listaProcedimentos);
        carregarHistorico();
    }
});
}
*/

