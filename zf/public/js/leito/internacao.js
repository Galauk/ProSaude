$(function(){
    //abreviaNome("VICTOR HUGO MARQUES DA SILVA",20);
    $(document).ready(function(){
        $('#scrollbar1').tinyscrollbar();
        buscaQuartos();
    });
    $(".radio").bind("click", function(){
        buscaQuartos();
    });
    
    $( ".paciente" ).draggable({ revert: "invalid" });// qual tiver o revert como invalido eh a que será jogada
      
});

function buscaQuartos(){
    var acao = $('.radio:checked').val();
    var params1 = {category: acao};
    var cmd1 = jQuery.param(params1);
    
    $.ajax({
        url:baseUrl + '/leito/internacao/quartos/',
        dataType: 'json',
        async: false,
        data: cmd1,
        cache: false,
        data: {
            acao: acao
        },
        type:'GET',
        success:function(json){
            var tableQuarto = "<table width=\"100%\">"+
                                    "<tr><td> </td></tr>"+
                                    "<tr>";
            var j = 0;
            
            for(var i in json){
                j++;
   
                //alert(j);
                if(j == 4){
                    tableQuarto += "</tr><tr>";
                    var j = 1;
                }
                //alert(json[i].ocupados == json[i].disponiveis);
                if(json[i].ocupados == 0){
                    var imagem = "disponivel.png";
                }else if(json[i].ocupados == json[i].disponiveis){
                   var imagem = "ocupado.png"; 
                }else if(json[i].ocupados != json[i].tudo && json[i].ocupados != 0){
                   var imagem = "reservado.png"; 
                }
                
                // Status de Medicação
                if (json[i].pac_res > 0 && json[i].ocupados != 0) {
                    var status_res = "<img src="+baseUrl+"/public/images/status_res.png alt=\"Quarto\" title='Pacientes aguardando reserva!' />";
                } else {
                    var status_res = "";
                }
                
                if (json[i].pac_ok > 0 && json[i].ocupados != 0) {
                    var status_alta = "<img src="+baseUrl+"/public/images/status_alta.png alt=\"Quarto\" title='Pacientes medicado!' />";
                } else {
                    var status_alta = "";
                }
                
                
                     tableQuarto += "<td width=\"120\" class=\"quarto\">"+
                                    "<font size=\"1\"><b>"+json[i].apt_codigo+"</b></font><br/>"+
                                    "<input type=\"hidden\" name=\"qua[]\" class=\"quarto_id\" value="+json[i].qua_codigo+" />"+
                                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+
                                    "<div style='position:relative; top:0px; left:0px;'>"+
                                        "<a href=\"#\" onclick=\"linkQuarto("+json[i].qua_codigo+")\"><img src="+baseUrl+"/public/images/"+imagem+" alt=\"Quarto\" title=\"\"/ style='position:relative;'></a>"+
                                        "<div style='position:absolute; top:52px; left:42px;'>"+
                                            "<font size=\"1\"><b>"+status_res+"</b></font>"+
                                            "<font size=\"1\"><b>"+status_alta+"</b></font>"+
                                        "</div>"+
                                    "</div>"+
                                "</td>";
            }
            
            //<img src=\""+baseUrl+"'/public/images/disponivel.png');?>\" alt=\"Quarto\"/>
            tableQuarto += "</tr>";
            if(j == 0){
                var tipoQuarto = "Cadastrado";
                
                if(acao == "D"){
                  tipoQuarto = "Disponivel";
                }else if(acao == "P"){
                  tipoQuarto = "Parcialmente Ocupado";
                }else if(acao == "O"){
                  tipoQuarto = "Ocupado";
                }
                tableQuarto += "<tr>"+
                                    "<td>"+
                                        "&nbsp;&nbsp;&nbsp; Nenhum quarto "+tipoQuarto+
                                    "</td>"+
                                "</tr>";
            }
            tableQuarto += "</table>";
            $('#quartos').html('');
            $("#quartos").append( tableQuarto );
            $('#scrollbar1').tinyscrollbar();//chama o scroolbar
            
            
            $( ".quarto" ).droppable({ //cada div que será jogada
                activeClass: "ui-state-hover",
                hoverClass: "ui-state-active",
                drop: function( event, ui ) {
                   var usu_codigo = $(".paciente_id",ui.draggable).val(); // ui.draggable é o elemento que está sendo arrastado
                   var io_codigo = $(".io_id",ui.draggable).val(); // ui.draggable é o elemento que está sendo arrastado
                   var qua_codigo = $(this).find(".quarto_id").val() // pega o elemento que eu estou soltando
                   //alert(usu_codigo + "---" + qua_codigo+ "-----"+io_codigo);
                   $.ajax({
                       url:baseUrl + "/leito/internacao/interna",
                       data:{
                           io_codigo : io_codigo,
                           usu_codigo : usu_codigo,
                           qua_codigo : qua_codigo
                       },
                       type:"GET",
                       success: function(txt){
                           if(txt == "C"){
                               var alerta = "Este quarto não possui leitos disponiveis";
                               var titulo = "Aviso";
                               var largura = 300;
                           }else if(txt == "S"){
                               var alerta = "Internado com Sucesso"
                               var titulo = "Sucesso";
                               var largura = 200;
                           }else if(txt == "E"){
                               var alerta = "Esse Paciente Já está internado"
                               var titulo = "Erro";
                               var largura = 200;
                           }
                           $("body").append("<div id=\"mensagem\" title=\" "+titulo+"\">"+alerta+"</div>");
                           $("#mensagem").dialog({
                               modal:true,
                               width:largura,
                               height:120,
                               close:function(){
                                   $(this).dialog("close");
                               },
                               buttons:{
                                   Ok:function(){
                                       window.location.href = baseUrl+'/leito/internacao/index';
                                       $(this).dialog("close");

                                   }
                               }
                           });
                       },
                       error: function(txt){
                       alert("Erro");
                   }
                   
               });
               
            }});
        
        
             $( ".excluir_lista" ).droppable({ //cada div que será jogada
                activeClass: "excluir1",
                hoverClass: "excluir",
                drop: function( event, ui ) {
                   var usu_codigo = $(".paciente_id",ui.draggable).val(); // ui.draggable é o elemento que está sendo arrastado
                   var io_codigo = $(".io_id",ui.draggable).val(); // ui.draggable é o elemento que está sendo arrastado
                   $.ajax({
                       url: baseUrl + '/leito/internacao/libera-paciente',
                       data:{io_codigo:io_codigo},
                       success:function(txt){
                           if(!txt.id){
                               mensagem("Erro","Erro ao excluir paciente!.<br/>",400,250,function(){window.location = baseUrl + "/leito/internacao/index";},txt.msg);
                           }else{
                               mensagem("Sucesso","Paciente removido da lista com sucesso!.<br/>",400,250,function(){window.location = baseUrl + "/leito/internacao/index";});
                           }
                       }
                   })
               
                }
            });
        }
    });
}

function linkQuarto(qua_codigo){
    
    var params = {category: qua_codigo};
    var cmd = jQuery.param(params);
    
    $.ajax({
        url:baseUrl + '/leito/internacao/leitos/',
        dataType: 'json',
        async: false,
        data: cmd,
        cache: false,
        data: {
            id: qua_codigo 
       },
        type:'GET',
        success:function(json){
           
           //alert(json);
           var table = "<table width=\"100%>\"";
                    table += "<tr><td><font size=2><b><a href=\""+baseUrl+"/leito/internacao/index\"><img src="+baseUrl+"/public/images/voltar_on.jpg alt=\"Quarto\" title=\"\"/></a></b></font></td></tr>";
                    table += "<tr><td colspan=3> <div style=\"border:1px solid;\"> </div> </td></tr>";
                    table += "<tr><td><font size=2><b>"+json[0].apt_codigo+"</b></font></td></tr>";
                    table += "<tr>";
            for(var i in json){
                    if(json[i].usu_codigo == 0){
                        table += "<td><img src="+baseUrl+"/public/images/cama3.png alt=\"Quarto\" title=\"\"/></td>";
                    }else if(json[i].usu_codigo != 0){
                    
                    // Status de Medicação
                    if (json[i].pac_res == 0 && json[i].pac_ok == 0) {
                        var status_res = "<img src="+baseUrl+"/public/images/status_res.png alt=\"Quarto\" title='Paciente aguardando reserva!'/>";
                    } else {
                        var status_res = "";
                    }

                    if (json[i].pac_ok > 0) {
                        var status_alta = "<img src="+baseUrl+"/public/images/status_alta.png alt=\"Quarto\" title='Paciente medicado!'/>";
                    } else {
                        var status_alta = "";
                    }
                    
                    table += "<td>"+
                               "<div style='position:relative; top:0px; left:20px; border:0px solid; width:120px; heigth:200px !important;'>"+
                                   "<a href=\""+baseUrl+"/leito/atendimento/index/cod/"+json[i].io_codigo+"\"><img src="+baseUrl+"/public/images/cama2.png  class=\"ui-state-disabled\" alt=\"Quarto\"  title=\""+json[i].usu_nome+"\"/></a>"+
                                   "<div style='position:absolute; top:42px; left:50px;'>"+
                                       "<font size=\"1\"><b>"+status_res+"</b></font>"+
                                       "<font size=\"1\"><b>"+status_alta+"</b></font>"+
                                   "</div>"+
                                   "<br/><b>"+abreviaNome(json[i].usu_nome,10)+"</b>"+
                                   "<input type=\"hidden\" name=\"lei[]\" class=\"io_lei\" value=\" "+json[i].io_codigo+" \">"+
                               "</div>"+
                            "</td>"; 
                }
                if(i == 2){ // na tela sempre vai aparecer 1 a mais do que a quantidade limite aqui, pois a variavel I comeca na posicao 0
                    table += "</tr><tr>";
                    var i = 0;
                }
            }
            table +="</tr></table>";
            
            $("#checkbox").hide();
            $("#leitos").append( table );
            
            $( ".teste" ).draggable({ revert: "invalid" });// qual tiver o revert como invalido eh a que será jogada
            
            $( ".pacientesInternados" ).droppable({ //cada div que será jogada
            activeClass: "ui-state-hover",
            hoverClass: "ui-state-active",
            drop: function( event, ui ) {
               var io_codigo = $(".io_lei",ui.draggable).val(); // ui.draggable é o elemento que está sendo arrastado
               $.ajax({
                   url:baseUrl+'/leito/internacao/cancela',
                   data:{
                       io_codigo : io_codigo
                   },
                   type:'GET',
                   success:function(txt){
                       if(txt == "C"){
                           var mensagem = "Paciente Removido com Sucesso";
                           var title = "Sucesso";
                           var largura = 200;
                       }else if(txt == "E"){
                           var mensagem = "Esse paciente possui atendimentos";
                           var title = "Erro";
                           var largura = 300;
                       }
                       $("body").append("<div id=\"mensagem\" title=\" "+title+"\">"+mensagem+"</div>");
                       
                       $("#mensagem").dialog({
                           modal:true,
                           width:largura,
                           height:120,
                           close:function(){
                               $(this).dialog("close");
                           },
                           buttons:{
                               Ok:function(){
                                   window.location.href = baseUrl+'/leito/internacao/index';
                                   $(this).dialog("close");

                               }
                           }
                       });
                   },
                   error: function(txt){
                       alert("Erro");
                   }
               });
            }});
            //$('#scrollbar1').tinyscrollbar();
            $("#scrollbar2").show();
            $("#scrollbar1").hide();
            
            
            
            
          
            /*o metodo a cima do contex menu eh chamado no read do documento para ocultar o ul e li que esta na index, e chamado aqui para nao perder com o ajax*/
            
        }
    });
	
}

