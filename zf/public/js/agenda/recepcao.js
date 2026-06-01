$(function(){    
    $( "table.sortable tbody" ).sortable({
		revert: true,
		axis: "y",
		stop: function(e,u){
			var arr = $("input[name^=ordem]");
			var ordem = [];
			arr.each(function(){
				ordem.push(this.value);
			});
                        window.console && console.log('enviando...');
			$.ajax({
				url: "../../portadeEntrada/ordem.php",
				type: 'post',
				data: {
					ordem: ordem
				},
				success: function() {
					window.console && console.log('reordenado!');
				}
			});

		}
	});
	$( "td, th" ).disableSelection();
        
    $.ajax({
            url: baseUrl+"/agenda/recepcao/buscar-profissionais",
            type: "POST",
            data: {
                uni_codigo: $("#uni_codigo").val()
            },
            success: function(txt){
               $.each( txt, function( key, value ) {                    
                    $("#usuarios").append("<option title=\""+value['usr_nome']+"\" value=\""+value['usr_codigo']+"\">"+value['usr_nome']+"</option>");
                })           
           }
    });
    
    $("#usuarios").change(function(){
           $("#especialidade").val('');
           $("#especialidade").html('');
           $.ajax({
                url: baseUrl+"/agenda/recepcao/carrega-especialidade",
                type: "POST",
                data: {
                        uni_codigo: $("#uni_codigo").val(),
                        usr_codigo : $(this).val()
                },
                success: function(txt){               
                  $.each( txt, function( key, value ) {                    
                      $("#especialidade").append("<option onclick='atualizaConiCodigo("+value['coni_codigo']+")' title=\""+value['esp_nome']+"\" value=\""+value['esp_codigo']+"\">"+value['esp_nome']+"</option>");
                  })
                  $("#esp").show('fast');
                  buscaSetaDadosConvenio($("#uni_codigo").val(),$("#usuarios").val(),$("#especialidade").val());
               }
            });
    });
    
    $(".detalhes").click(function(){          
        if($("#usuarios").val() === ""){
            $("#message").show('fast');
            $("#message").html('Campo Profissional Obrigatório')    
           return false
        }
        if(!$("#especialidade").val()){            
            $("#message").show('fast');
            $("#message").html('Campo Especialidade Obrigatório')           
           return false
        }
        if($("#age_data").val() === ""){
            $("#message").show('fast');
            $("#message").html('Campo Data Obrigatório') 
            return false
        }
        $("#message").hide();
        $(".tr").remove();
        $("#tabs-1").append("<div id=\"carregando\" title=\"Carregando\" />");
        $("#carregando")
        .html(imgCarregando);
            $.ajax({
                url: baseUrl+"/agenda/recepcao/carrega-pacientes-agendados",
                type: "POST",
                data: {
                    uni_codigo: $("#uni_codigo").val(),
                    usr_codigo: $("#usuarios").val(),
                    esp_codigo: $("#especialidade").val(),
                    age_data: $("#age_data").val(),
                    data_inicial : brToSql($("#age_data").val()), 
                    coni_codigo: $("#coni_codigo").val(),
                    conv_codigo: $("#conv_codigo").val()
                },
                success: function(txt){

                    if(txt != ''){
                        i=0;
                        $.each(txt,function(key,value) {
                            i++;
                            // Tratamento de Telefone
                            if (!value['usu_celular']) { 
                                if (!value['dom_telefone']) {
                                        var telefone = "-";
                                } else {
                                        var telefone = value['dom_telefone'];
                                }
                            } else { 
                                var telefone = value['usu_celular'];
                            }
                            var situacao = value['status'];
                            linhaPaciente = "<tr class='tre"+value['age_codigo']+" tr'>"+
                            "<td class=\"ui-state-default item\">";
                            if(value['status'] != "A"){
                                linhaPaciente += "<input type=checkbox name='item[]' id=item"+value['age_codigo']+" value='"+value['age_codigo']+"' onchange='habalitaDesabilita()' class='item'>";
                            } 
    var en;                        
if(value['age_encaixe']=="S") {
     en = '<font color=orange><b>SIM</b>';
}  else {
     en = '<font color=blue><b>NAO</b>';
}                          
                            
linhaPaciente += "</td>"+
"<td  class=\"ui-state-default\" style='background-color: orange !important' width=\"10\" align='center'>"+i+"</td>"+
"<td style='color:"+value['cor']+"' class=\"ui-state-default\" width=\"120\">"+value['usu_prontuario']+"</td>"+
"<td style='color:"+value['cor']+"' class=\"ui-state-default\" >"+value['usu_nome']+"</td>"+
"<td style='color:"+value['cor']+"' class=\"ui-state-default\" >"+value['idade']+"</td>"+
"<td style='color:"+value['cor']+"' class=\"ui-state-default\" >"+telefone+"</td>"+
"<td style='color:"+value['cor']+"' class=\"ui-state-default\" >"+value['usu_mae']+"</td>"+                                              
"<td style='color:"+value['cor']+"' class=\"ui-state-default\"  >"+value['age_horario']+"</td>"+
"<td style='color:"+value['cor']+"' class='age"+value['age_codigo']+" ui-state-default'>"+value['age_atendido']+"</td>"+
"<td style='color:"+value['cor']+"' class=\"ui-state-default\">"+(value['est_nome'] ? value['est_nome'] : "")+"</td>"+
"<td style='color:"+value['cor']+"' class='age"+value['age_encaixe']+" ui-state-default'>"+en+"</td>"+
"<td class=\"ui-state-default\" ><input type=\"hidden\" name=\"ordem[]\" value=\""+value['age_codigo']+"\" /><a href='#'><img src="+baseUrl+"/public/images/recepcao.png   class='verifica_"+value['age_codigo']+"' style='cursor:pointer'; title=\"\"/ onclick=recepcionar("+value['age_codigo']+"); ></a></td>"+
"</tr>";
                            $("#tabelaPac").append(linhaPaciente);
                            if(($("#data_atual").val() != $("#age_data").val()) || situacao == "R"){
                                $(".verifica_"+value['age_codigo']).addClass('ui-state-disabled');
                                $(".verifica_"+value['age_codigo']).attr('onclick','').unbind('click');
                            }
                            
                            
                            if(situacao != "S" && situacao != "N"){
                                $(".verifica_"+value['age_codigo']).addClass('ui-state-disabled');
                                $(".verifica_"+value['age_codigo']).attr('onclick','').unbind('click');
                            } 
                         })
                         $("#carregando").remove(); 
                    }else{
                         linhaPaciente = "<tr class='tr'>"+
                                         "<td class=\"ui-state-default\" colspan='9'>Nenhum Paciente encontrado</td>"+
                                          "</tr>";
                        $("#tabelaPac").append(linhaPaciente);
                        $("#carregando").remove();
                    }
                    $(".excluir3").addClass('ui-state-disabled');
                    $(".excluir3").removeAttr('onclick','modal');
		    $("#tabela").show('fast')
               }
        });
    });
    
    $(".detalhes").click(function(){
       $(".tr").remove();
    });
	// Metodo de Impressão aos pacientes agendados
	$(".imprimir-pacientes").click(function(){
		var agendamentos = "";
                var indagendamentos = 0;
                agendamentosSelecionados = new Array();
                $("input[type=checkbox][name='item[]']:checked").each(function(){
                        agendamentosSelecionados.push($(this).val());
                });
                $.each(agendamentosSelecionados, function( key, value ) {
                    if (indagendamentos > 0) {
                        agendamentos += "-"+value;
                    } else {
                        agendamentos += value;
                    }
                    indagendamentos++;
                });
                
                var uni_codigo = $("#uni_codigo").val();
		var usr_codigo = $("#usuarios").val();
		var esp_codigo = $("#especialidade").val();
		var age_data = $("#age_data").val().split("/");
		var age_data_trat = age_data[0]+"-"+age_data[1]+"-"+age_data[2];
                // Redirecionando url pra exibição do relatório
		if (agendamentosSelecionados.length > 0) {
                    window.open(baseUrl+"/agenda/recepcao/imprime-pacientes-agendados/agendamentos/"+agendamentos+"/uni/"+uni_codigo+"/usr/"+usr_codigo+"/esp/"+esp_codigo+"/age/"+age_data_trat,'','width=750,height=700');
                } else {
                    window.open(baseUrl+"/agenda/recepcao/imprime-pacientes-agendados/uni/"+uni_codigo+"/usr/"+usr_codigo+"/esp/"+esp_codigo+"/age/"+age_data_trat,'','width=750,height=700');
                }
        });
	$("#conv_nome").buscar({
		url: baseUrl+'/agenda/convenio/buscar/',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	
    });
    var conv_codigo = $("#conv_codigo").val();
    $("#usr_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-profissionais-saude/conv_codigo/'+conv_codigo,
         template : function(ul, item) {
                 return $("<li/>").data("item.autocomplete", item).append(
                         "<a>" + item.label + "</a>").appendTo(ul);
         },
         callback: function(){
           mostraEspecialidade();
           return true;
         }
     });

});
    function atualizaConiCodigo(coniCodigo){
        $("#coni_codigo").val(coniCodigo);
    }
    
    function mostraEspecialidade(){
        var conv_codigo = $("#conv_codigo").val();
        var usr_codigo = $("#usr_codigo").val();
        var esp_codigo_sessao = $("#esp_codigo_sessao").val();
        if(esp_codigo_sessao != null || esp_codigo_sessao != ""){
            var selected = "selected=selected";
        }else{
            var selected = "";
        }
        $.ajax({
                url: baseUrl+'/agenda/convenio-itens/carrega-especialidade-por-convenio',
                type: "POST",
                data: {
                     conv_codigo: conv_codigo,
                     usr_codigo:usr_codigo
                },
                success: function(json){
                     $("#esp_nome22").append("<option title='SELECIONE'>--SELECIONE--</option>");
                     $.each( json, function( key, value ){                    
                         //alert(value['esp_nome']);
                        $("#esp_nome22").append("<option title=\""+value['esp_nome']+"\"  value=\""+value['coni_codigo']+"\" "+selected+">"+value['esp_nome']+"</option>");
                      
                     })
                     var coni_codigo = $('#esp').find('option').filter(':selected').val();
                     $("#coni_codigo").val(coni_codigo);
                     //alert(teste);
                     if( $("#coni_codigo").val() == "" || $("#coni_codigo").val() == "" || !$("#coni_codigo").val()){
                         $("#med_esp").show('slow');
                         $("#med_esp").html("<em>O profissional selecionado não possui especialidade</em>");
                         return false;
                     }
                }
        });
    }
    
    function modal() {
        agendamentosSelecionados = new Array();
        $("input[type=checkbox][name='item[]']:checked").each(function(){
                agendamentosSelecionados.push($(this).val());
        });
        
        $("#msg").dialog({
                modal: true,
                width: 400,
                height: 250,
                buttons: {
                        "Não Cancelar": function(){
                                $(".excluir3").addClass('ui-state-disabled');
                                $("#msg").dialog("destroy");				

                        },
                        "Cancelar Agendamento": function(){
                                $.ajax({
                                        url: baseUrl+"/agenda/recepcao/cancelar-ou-falta-agendamento",
                                        type: "POST",
                                        data: {
                                                age_codigos: agendamentosSelecionados,
                                                motivo: $("#msg input:checked").val()
                                        },
                                        success: function(txt){
                                            $.each(agendamentosSelecionados, function( key, value ) {
                                                if($("#msg input:checked").val() == "M"){
                                                    valor = "Falta Médica";
                                                    $(".age"+value).html(valor);
                                                }else{
                                                    valor = "Faltou";
                                                    $(".age"+value).html(valor);
                                                }                                          
                                                if($("#msg input:checked").val() == "C"){                                                                                           
                                                  $(".tre"+value).remove();
                                                }

                                                $(".excluir3").addClass('ui-state-disabled');
                                                //$(".excluir3").removeAttr('onclick','modal');    
                                            });
                                            $("#msg").dialog("destroy");
                                        }
                                });

                                return false;
                        }
                }
        });

    }
    
    function imprimiPacientesSelecionados() {
        
        agendamentosSelecionados = new Array();
        $("input[type=checkbox][name='item[]']:checked").each(function(){
                agendamentosSelecionados.push($(this).val());
        });
        
        var html = "";
        $.ajax({
            url: baseUrl+"/agenda/recepcao/imprime-pacientes-agendados",
            type: "GET",
            data: {
                age_codigos: agendamentosSelecionados,
                uni: $("#uni_codigo").val(),
                usr: $("#usuarios").val(),
                esp: $("#especialidade").val(),
                age: brToSql($("#age_data").val())
            },
            success: function(txt){
                html += txt;
            }
        });
        
        $("#lista_agendados").dialog("option", "position", "top");
        $("body").append("<div style='margin:0; padding:0;' id=\"lista_agendados\" title=\"Realocar Agendamentos\"> </div>");
        $("#lista_agendados")
        .html(imgCarregando);
        $("#lista_agendados").dialog({
                modal: true,
                width: 600,
                height: 500,
                buttons: {
                        "Cancelar": function(){
                         
                        },
                        "Imprimir": function(){
                                /*$.ajax({
                                    url: baseUrl+"/agenda/recepcao/imprime-pacientes-agendados",
                                    type: "GET",
                                    data: {
                                        age_codigos: agendamentosSelecionados,
                                        uni: $("#uni_codigo").val(),
                                        usr: $("#usuarios").val(),
                                        esp: $("#especialidade").val(),
                                        age: brToSql($("#age_data").val())
                                    },
                                    success: function(txt){

                                    }
                                });
                                return false;*/
                        }
                }
        });
        //alert(html);
        $("#lista_agendados").html(html);

    }

    function modalTransferencia() {      
        carregaListaAgendados($('#coni_codigo').val(),$('#data').val());
    }
    
    function checar(){
        if($("#tudo").is(":checked") == true){           
            $(".item").attr('checked', true);
        }else{
            $(".item").attr('checked', false);
        }
    }
    
    function habalitaDesabilita(){
        $("input[type=checkbox][name='item[]']").each(function(){
            if($("input[type=checkbox][name='item[]']:checked").val()){                
               $(".excluir3").removeClass('ui-state-disabled');               
               $(".excluir3").attr('onclick','modal()');
            }else{
              $(".excluir3").addClass('ui-state-disabled');
             
              $(".excluir3").removeAttr('onclick','modal');            
            }            	
	});
      
    }
  
function recepcionar(age_codigo){
    $.ajax({
            url: baseUrl+"/agenda/recepcao/altera-situacao",
            type: "POST",
            data: {
                    age_codigo: age_codigo
                   
            },
            success: function(txt){
                if(txt == "S"){
                    txt = "Recepcionado"
                    color = "blue";
                }if(txt == "N"){
                    txt = "Agendado"
                    color = "#2e6e9e";
                }
                $(".age"+age_codigo).html(txt);
                $(".age"+age_codigo).css("color", color);
                $('.detalhes').trigger('click');
              //  $("#tabelaPac").
              
           }
        });
}

function buscaSetaDadosConvenio(uniCod,usrCod,espCod){
    $.ajax({
       url: baseUrl+"/agenda/convenio/get-dados-conv-agendamento-estabelecimento-de-saude",
       type: "POST",
       data: {
           uni_codigo: uniCod,
           usr_codigo: usrCod,
           esp_codigo: espCod
       },
       success: function(txt) {
           $("#conv_codigo").val(txt["conv_codigo"]);
           $("#coni_codigo").val(txt["coni_codigo"]);
           $("#usr_codigo").val(txt["usr_codigo"]);
       }
    });
}