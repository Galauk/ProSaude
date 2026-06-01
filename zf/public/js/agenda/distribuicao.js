$(function(){
	// Salvar por ajax
        
	$("#salvar-agenda").ajaxForm(afterSubmit);

	$("#procedimentos")
	.bind('dblclick', selecionarProcedimento)
	.bind('keypress', selecionarProcedimento);
	
	$("#procedimentos-selecionados")
	.bind('dblclick', deselecionarProcedimento)
	.bind('keypress', deselecionarProcedimento);
	
	$("#atualizar-grid").click(carregarCalendario);	
	
	$("#med_nome").buscar({
		url: baseUrl+'/agenda/convenio/buscar/',
		categoria: 'categoria',
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			mensagemSemOk("carregando-conis", "Aguarde", "Carregando lista de exames", 280, 80);
			$("#procedimentos-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum exame selecionado</option>');
			var conv_codigo = $("#conv_codigo").val();
			$.ajax({
				url: baseUrl+'/agenda/convenio-itens/procedimentos-ajax',
				type: "POST",
				data: {
                                    conv_codigo: conv_codigo
				},
				success: function(json){
                                    listarProcedimento(json);
				}
			});
		}
	});
        
        $("td.calend").live('click', function(){
            var tipo = $("#prestador_servico").val();
            if(tipo == "U"){
                $("td.c").each(function(){
                    $(this).removeClass("marcada");
                });
                
                if($(this).hasClass("agenda_sem_alteracao")){
                    $(this).removeClass("agenda_sem_alteracao");
                    $(this).addClass("marcada");
                    $(this).addClass("agenda_sem_alteracao");
                }
                
                
                
                if($(this).hasClass("agenda_com_alteracao")){
                    //$(this).removeClass("agenda_com_alteracao");
                    $(this).addClass("marcada");
                    $(this).addClass("agenda_com_alteracao");
                }
                
                if($(this).hasClass("agenda_alterando")){
                    $(this).removeClass("agenda_alterando");
                    $(this).addClass("marcada");
                    $(this).addClass("agenda_alterando");
                }
                var coni_codigo = $(this).data("coni");
                var data_selecionada = $(this).data("dia");
                
                carregarHorario(coni_codigo,data_selecionada);
                
            }
            
        });
        
        $(".tempo_periodo").click(function(){
                // Zerando os dados de horário e colocando os valores nos campos
                $(".horario").remove();
                $("#coni_intervalo_int").val("");
                $("#coni_intervalo").val("");
                $("#hr_inicio1").val("");
                $("#hr_fim1").val("");
                
                var qtde = $("td.marcada input").val();
                var horarios = [];
                $("#coni_cota_dia").val(qtde);
                
                if($("#manut_periodo").hasClass("ui-state-disables")){
                    return false;
                }
                
                $.ajax({
                    url:baseUrl+'/agenda/distribuicao/get-grade-periodo',
                    type: "POST",
                    data:{
                        coni_codigo:$("td.marcada").data("coni"),
                        dia:$("td.marcada").data("dia")
                    },
                    success: function(json){
                        var i = 0;
                        for(var a in json){
                            if(json[i]){
                                //if(i >= 1){
                                //    $('#addPac').trigger('click');
                                //}
                                i++;
                                //$("#hr_inicio"+i).val(json[a].grap_hora_inicial.substring(0,5));
                                //$("#hr_fim"+i).val(json[a].grap_hora_final.substring(0,5));
                                //$("#grap_"+i).val(json[a].grap_codigo);
                                
                                //if ($("#hr_inicio1").val() != "" )
                                
                                // Function chama calculo do convênio itens
                                //chamaCalculo();
                                
                            }
                            
                        }
                        /*if(json['intervalo']){
                            $("#coni_intervalo").val(json['intervalo']);
                            $("#coni_intervalo_int").val(parseInt(json['intervalo']));
                        }*/
                    }
                });
                
		$("#tempo_periodo").dialog({
                    modal: true,
                    width: 500,
                    height: 225,
                    close: function(){
                            $(this).dialog('close');
                    },
                    buttons: {
                            Executar: function(){
                                if($("#hr_inicio1").val() != "" && $("#hr_fim1").val() != ""){
                                    var i = 1;
                                    $(".ini").each(function(){
                                        horarios.push($(this).val()+"|"+$("#hr_fim"+i).val()+"|"+$("#grap_"+i).val());
                                        i++;
                                    });
                                    var intervalo = $("#coni_intervalo").val();
                                    var dia = $("td.marcada").data("dia");
                                    var coni_codigo = $("td.marcada").data("coni");
                                    $.ajax({
                                            url: baseUrl+'/agenda/distribuicao/salvar-periodo',
                                            type: "POST",
                                            data: {
                                                dia: dia,
                                                intervalo:intervalo,
                                                horarios:horarios,
                                                coni_codigo:coni_codigo,
                                                qtde:qtde
                                            },
                                            success: function(){
                                                carregarCalendario();
                                                //carregarHorario(coni_codigo,dia);
                                                carregaListaAgendados(coni_codigo,dia);
                                                $("#tempo_periodo").dialog('close');
                                                if($("td.marcada").hasClass("agenda_alterando")){
                                                    $("#salvar-agenda").ajaxForm(afterSubmit);
                                                }
                                                

                                            }
                                    });
                                } else {
                                    $("body").append("<div id=\"dialog\" title=\" Erro\">Informe o Horário Inicial e de Término</div>");
                                    $("#dialog").dialog({
                                        modal: true,
                                        width: 300,
                                        height: 150,
                                        close: function() {
                                            $(this).dialog("close");
                                        },
                                        buttons: {
                                            Ok: function() {
                                                $(this).dialog("close");

                                            }
                                        }
                                    });
                                }
                            },
                            Cancelar: function(){
                                    $(this).dialog('close');
                            }
                    }
		});
        });
        
        $(".tempo_pausa").click(function(){
                var horarios = [];
                var dia = $("td.marcada").data("dia");
                var intervalo = $("#coni_intervalo").val();
                var coni_codigo = $("td.marcada").data("coni");
                var qtde = $("td.marcada input").val();
                var i = 0;
                
                /*Pega os motivos e monta o combo*/
                
                var combo = "";
                $.ajax({url: baseUrl+'/agenda/distribuicao/get-motivos',
                    type: "POST",
                    data: {
                        dia: dia,
                        coni_codigo:coni_codigo
                    },
                    success: function(txt){
                       //alert(txt[0][1]);
                       combo += "<label style='width:110px' class='ui-corner-bl ui-corner-tr'>Motivo:</label>&nbsp;"+
                                "<select id=\"mof_codigo\">"+
                                    "<option value=\"\">Selecione</option>";
                       for(var i in txt){
                           combo += "<option value=\""+txt[i].mof_codigo+"\">"+txt[i].mof_descricao+"</option>";
                       }
                       combo += "</option>";
                       $("#combo").html(combo);
                    }
                });
                //alert("xd");
                var cont = 1;
                var html = "<table border=0 class=\"grid ui-widget ui-widget-content ui-corner-all tb_horarios\" width=\"100%\">"+
                                "<tr>"+
                                    "<td colspan=9>"+
                                        "<div id=combo style=\"float:left;\"></div>"+
                                        "<div style=\"float:left;margin-left:40px;\">"+
                                            "<img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/selecionar.png' onClick=\"marcaTodos()\" title=\"Marcar Todos\">"+
                                        "</div>"+
                                    "</td>"+
                                "</tr>"+
                                "<tbody class=first><tr class=\"checkb linha_"+cont+"\">";
                if($("td.hora_per").hasClass("marcada")){
                    
                    $(".marcada.hora_per").each(function(){
                        
                        var hora = $(this).data("hora");
                        //.replace("_", ":")
                        if(i == 9){
                            cont++; // contador para marcar as class da TR
                            i = 0;
                            html += "</tr>"+
                                    "<tr class=\"checkb linha_"+cont+"\">";
                        }
                        html +="<td class=\"horas col_hora_"+hora+"\" data-hora=\"hora_"+hora+"\">"+
                                    "<input type=checkbox id=\"hora_"+hora+"\" class=\"checkbox_horario\" name=\"hora_marcada[]\" checked=checked value=\""+hora+"\" data-grah=\"\">"+hora.replace("_", ":")
                                "</td>";
                        i++;
                    });
                    
                    html+="</tr></tbody>"+
                          "<tr>"+
                                "<td colspan=9>"+
                                    "<textarea cols=\"80\" rows=\"3\" name=\"grah_motivo\" id=\"grah_motivo\" onclick=\"javascript:limpaTextArea()\" onblur=\"javascript:sujaTextArea()\">Especifique o motivo</textarea>"+
                                "</td>"+
                          "</tr>"+
                        "</table><br />";
                    
                   
                    var grah_codigos = "" // envia como vazio para que na funcao esse parametro nao seja undefined 
                    montaPeriodos(dia,coni_codigo,grah_codigos);
                    
                    $("#tempo_pausa").html(html);
                    $("#tempo_pausa").dialog({
                        modal: true,
                        width: 600,
                        height: 425,
                        close: function(){
                                $(this).dialog('close');
                        },
                        buttons: {
                                Executar: function(){
                                   
                                    /*
                                     *INICIO DAS VALIDACOES
                                     */
                                    
                                    if($("#mof_codigo").val() == ""){
                                        $("body").append("<div id=\"dialog-mof_codigo\" title=\"Aviso\">Informe um motivo!</div>");
                                        $("#dialog-mof_codigo").dialog({
                                                modal:true,
                                                width:300,
                                                height:150,
                                                close:function(){
                                                    $(this).dialog("close");
                                                },
                                                buttons:{
                                                    Ok:function(){
                                                        $(this).dialog("close");
                                                    }
                                                }
                                       });
                                       return false;
                                   }
                                   
                                   if($("#mof_codigo").val() == 5 && $("#grah_motivo").val() == "Especifique o motivo"){
                                        $("body").append("<div id=\"dialog-grah_motivo\" title=\"Aviso\">Especifique o motivo!</div>");
                                        $("#dialog-grah_motivo").dialog({
                                                modal:true,
                                                width:300,
                                                height:150,
                                                close:function(){
                                                    $(this).dialog("close");
                                                },
                                                buttons:{
                                                    Ok:function(){
                                                        $(this).dialog("close");
                                                    }
                                                }
                                       });
                                       return false;
                                   }
                                   
                                   
                                   if($("#grah_motivo").val() == "Especifique o motivo"){
                                       var motivo = "";
                                   }else{
                                       var motivo = $("#grah_motivo").val();
                                   }
                                   
                                   var horarios = new Array();
                                   var horarios_salvar = new Array();
                                   var horarios_deletar = new Array();
                                   var grah_codigos = new Array();
                                   $("input[type=checkbox][name='hora_marcada[]']:checked").each(function(index, element){
                                       //grah_codigos.push($(this).data("grah"));
                                       var condicao = "hora_"+$(this).val();
                                       $(".col_"+condicao).remove();
                                       horarios.push($(this).val().replace("_", ":"));
                                       horarios_salvar.push($(this).val().replace("_", ":")+"|"+$(this).data("grah"));
                                   });
                                   
                                   //$(this).parent().find("input").attr('checked') == true
                                   $(this).parent().find("td.edit input[type=checkbox]").attr('checked', false).each(function(index, element){
                                       horarios_deletar.push($(this).data("grah"));
                                   });
                                   if(horarios_deletar.length >= 1){
                                      $.ajax({
                                            url: baseUrl+'/agenda/distribuicao/deletar-horario',
                                            type: "POST",
                                            data: {
                                                hora: horarios_deletar
                                            },
                                            success: function(txt){
                                                
                                            }
                                    });
                                   }
                                   if(horarios.length < 1){
                                       $("body").append("<div id=\"dialog-hora-check\" title=\"Aviso\">Selecione pelo menos um horário</div>");
                                        $("#dialog-hora-check").dialog({
                                                modal:true,
                                                width:300,
                                                height:150,
                                                close:function(){
                                                    $(this).dialog("close");
                                                },
                                                buttons:{
                                                    Ok:function(){
                                                        $(this).dialog("close");
                                                    }
                                                }
                                       });
                                       return false;
                                   }
                                   
                                   /*
                                    *FIM DAS VALIDACOES
                                    */
                                   
                                    $.ajax({
                                            url: baseUrl+'/agenda/distribuicao/salvar-horario',
                                            type: "POST",
                                            data: {
                                                dia: dia,
                                                coni_codigo:coni_codigo,
                                                hora: horarios_salvar,
                                                motivo:motivo,
                                                mof_codigo:$("#mof_codigo").val()
                                            },
                                            success: function(txt){
                                                var grah_codigos = txt;
                                                $("#grah_motivo").val("Especifique o motivo");
                                                montaInativados(horarios,$('#mof_codigo').find('option').filter(':selected').text(),motivo,grah_codigos,$("#mof_codigo").val());
                                            }
                                    });
                              
                                },
                                Concluir: function(){
                                        var dia = $("td.marcada").data("dia");
                                        var coni_codigo = $("td.marcada").data("coni");
                                        carregarHorario(coni_codigo,dia);
                                        $(this).dialog('close');
                                        
                                }
                        }
                    });
                }else{
                    $("body").append("<div id=\"dialog-hora-fim\" title=\"Aviso\">Selecione pelo menos um horário!</div>");
                    $("#dialog-hora-fim").dialog({
                            modal:true,
                            width:300,
                            height:150,
                            close:function(){
                                $(this).dialog("close");
                            },
                            buttons:{
                                Ok:function(){
                                    $(this).dialog("close");

                                }
                            }
                   });
                   return false;
                }
        });
	
        
});

function calend(){
    $('.data').live('focus', function () { $(this).datepicker({ changeMonth: true, changeYear: true }) });
}

function marcaTodos(){
    if( $(".horas").find('input[type=checkbox]').is(':checked')){
        $(".horas").find('input[type=checkbox]:checked').removeAttr('checked'); 
    }else{
        $(".horas").find('input[type=checkbox]').attr("checked", true); 
    }
     
}

function montaPeriodos(dia,coni_codigo,grah_codigos){

    $.ajax({url: baseUrl+'/agenda/distribuicao/get-periodos',
        type: "POST",
        data: {
            dia: dia,
            coni_codigo:coni_codigo,
            grah_codigos:grah_codigos
        },
        success: function(json){
          /*
           *Validacoes abaixo
           *Primeiro ele coloca as variaveis zeradas para forcar entrar no primeiro if poois vazio é diferente do primeiro resultado
           *O if que verifica se tem coisas no array é pra definir quando ele chama a funcao pois, a primeira vez ele sempre vai entrar
           *soh que não vai ter nada e ai ele montaria uma tabela em branco, portanto ele soh monta a listagem quando for diferente e nao
           *for vazio.
           *o Else if é pra colocar os horarios sempre dentro de um array pois os horarios é um array de horario ou seja,
           *mesmo que o motivo seja o mesmo é necessário inserir o horario.*/

          var horarios_preenchidos = new Array();
          var grah_codigos = new Array();
          var grah_motivo = "";
          var mof_codigo = "";
          var j = 0;
          for (var j in json){
              if(mof_codigo != json[j].mof_codigo || grah_motivo != json[j].grah_motivo){
                   //alert(horarios_preenchidos);

                   if(horarios_preenchidos.length != 0){
                       //chama a funcao
                       //zera as variaveis
                       montaInativados(horarios_preenchidos,mof_descricao,grah_motivo,grah_codigos,mof_codigo,dia,coni_codigo);
                       var horarios_preenchidos = new Array();
                       var grah_codigos = new Array();
                       var grah_motivo = "";
                       var mof_codigo = "";
                       var mof_descricao = "";
                   }
                   horarios_preenchidos.push(json[j].grah_hora);
                   grah_codigos.push(json[j].grah_codigo);
                   mof_codigo = json[j].mof_codigo; 
                   grah_motivo = json[j].grah_motivo;
                   mof_descricao = json[j].mof_descricao;

              }else if(mof_codigo == json[j].mof_codigo && grah_motivo == json[j].grah_motivo){
                  
                  horarios_preenchidos.push(json[j].grah_hora);
                  grah_codigos.push(json[j].grah_codigo);
                  mof_codigo = json[j].mof_codigo; 
                  grah_motivo = json[j].grah_motivo;
                  mof_descricao = json[j].mof_descricao;
              }

          }

          if(horarios_preenchidos.length != 0){
              //ESSE IF É PARA CASO O FOR PERCORRA APENAS UMA VEZ OU SEJA
              //TENHA APENAS UM MOTIVO DIFERENTE AI ELE NAO ENTRA NO PRIMEIRO IF DO FOR E OS VALORES FICAM ARMAZENADOS
              //COMO ELE NAO ENTRA A FUNÇÃO DEVE SER CHAMADA
              
              montaInativados(horarios_preenchidos,mof_descricao,grah_motivo,grah_codigos,mof_codigo,dia,coni_codigo);
          }
        }
    });
    
    
}

function excluirAgendamento(age_codigo){
    var age_codigos = [];
    age_codigos.push(age_codigo);
    $.ajax({
            url: baseUrl+"/agenda/recepcao/cancelar-ou-falta-agendamento",
            type: "POST",
            data: {
                    age_codigos: age_codigos,
                    motivo: 'C'
            },
            success: function(txt){
                $(".age_"+age_codigo).remove();
            }
    });
}

// Função responsável por trazer a lista de agendado para troca de horários
function carregaListaAgendados(coni_codigo,dia,usr_codigo_sel){
    //Se não for manutenção exibe tela de carregamento
    if($("#man_exc").val()=="0"){
        $("body").append("<div id=\"carregando-realocamento\" title=\"Carregando Realocação de Agendamentos\"> </div>");
        $("#carregando-realocamento")
        .html(imgCarregando);
        $("#carregando-realocamento").dialog({
                modal:true,
                width:400,
                height:100,
                close:function(){
                    $(this).dialog("close");
                }
        });
        $("#carregando-realocamento").dialog("option", "position", "top");
    }
    
    if(dia.indexOf("-")!=-1) {
        dia = dataToBr(dia);
    }
    if (dia=="") {
        dia = $("#age_data").val();
    }
    // Pega os agendamentos selecionados
    var agendamentosSelecionados = new Array();
    $("input[type=checkbox][name='item[]']:checked").each(function(){
        agendamentosSelecionados.push($(this).val());
    });
    // Busca o código do usuário pelo coni_codigo
    $.ajax({
        url: baseUrl+"/agenda/convenio-itens/get-dados-coni-codigo",
        type: "POST",
        async: false,
        data: {
            coni_codigo:coni_codigo
        },
        success: function(txt){
           usr_codigo_sel = txt;
        }
    });
    
    var valida_modal;
    // Unidade selecionada
    var uni_codigo = $("#codigo_convenio").val(); 
    // Lista todos os medicos que tem vinculo com a unidade
    var medicos = montaArrayComboMedicos(uni_codigo);
    // Monta comdo de médicos
    var combo_medico = montaComboMedicos(medicos,usr_codigo_sel,uni_codigo);
    // Monta combo de Especialidade, que na verdade carrega o ID do conicodigo, conforme logica antiga
    var combo_esp = montaComboEspecialidades(medicos,usr_codigo_sel,uni_codigo,coni_codigo);
    // Monta combo de Especialidade vinculados ao coni codigo para saber qual a especialidade na hora de salvar
    var input_esp = montaInputsEspecialidades(medicos,usr_codigo_sel,uni_codigo,coni_codigo);
    var html ="<br />"+
              "<form>"+
              "<table border=0 class=\"table_agendados\" width=100%>"+
                    "<tr align=left>"+
                        "<th>"+
                             "<label class='ui-corner-bl ui-corner-tr' style='font-weight:normal;'>Profissional: </label><span style='margin-left:06px' />"+combo_medico+"</span>"+ 
                        "</th>"+
                     "</tr>"+
                     "<tr align=left>"+
                        "<th>"+
                            "<label style='float:left; font-weight:normal;'>Especialidade: </label>\n\
                            <div class='combo_especialidade' style='float:left margin-left:10px'>\n\
                                <span style='margin-left:06px' />"+combo_esp+"</span>\n\
                            </div>"+
                            "<div class='input_especialidade'>"+input_esp+"</div>"+
                        "</th>"+
                     "</tr>"+
                     "<tr align=left>"+
                        "<th>"+
                            "<label style='font-weight:normal;'>Data: </label>"+
                            "<input name='data_hora' style='margin-left:06px; width: 100px;' class='data ui-state-default' id='data-pesquisa' onClick='calend()' value='"+dia+"' onchange='montaArrayComboHorariosDisponiveis()' />"+
                        "</th>"+
                        "<th>"+
                            
                        "</th>"+
                     "</tr>"+
               "</table>"+
               "<br />";
    html += "<table border=0 class=\"grid ui-widget ui-widget-content ui-corner-all table_agendados\" width=100%>"+
                    "<tr style='margin-top:10px' class='ui-widget-header'>"+
                        "<th>"+
                            "<b>Nome</b>"+
                        "</th>"+
                        "<th>"+
                            "Celular"+
                        "</th>"+
                         "<th>"+
                            "Endereço"+
                        "</th>"+
                        "<th>"+
                            "Horário Atual"+
                        "</th>"+
                        "<th>"+
                            "Horários Disponível"+
                        "</th>"+
                    "</tr>";
    // Verifica se existe paciente agendado para o profissional de acordo com o dia e retorna a lista dos mesmos
    $.ajax({
        // Pega os dados dos pacientes cadastrado no dia para mostrar em tela
        url: baseUrl+'/agenda/distribuicao/get-agendados',
        type: 'POST',
        async: false,
        data:{
            coni_codigo:coni_codigo,
            dia:dia,
            codsAge:agendamentosSelecionados 
        },
        success:function(json){
            // Se não tem paciente o modal não é ativado
            if(json.length <= 0){
               valida_modal = false;
            }
            // Código Convênio
            var cod_convenio = $("#conv_codigo").val(); 
            // Lista todos os horários disponiveis naquele dia
            var horas = carregaArrayComboHorariosDisponiveis(coni_codigo,dia,cod_convenio);
            // Lista todos os medicos que tem vinculo com a unidade
            //var medicos = montaArrayComboMedicos($("#codigo_convenio").val());
            for(var i in json){
                // Monta combo de horários
                var combo = montaCombo(horas,json[i].age_codigo);
                // Monta comdo de médicos
                //var combo_medico = montaComboMedicos(medicos,json[i].age_codigo,$("#codigo_convenio").val(),coni_codigo,dia);
                // Html de Realocamento de Horários
                html += "<tr class=\"age_"+json[i].age_codigo+" linha_agendados ui-state-default item\" data-age=\""+json[i].age_codigo+"\">"+
                            "<td >"+
                                json[i].usu_nome+
                            "</td>"+
                            "<td>"+
                                json[i].usu_celular+
                            "</td>"+
                            "<td>"+
                                json[i].rua_nome +"&nbsp;&nbsp; Nº:"+json[i].dom_numero+
                            "</td>"+
                            "<td>"+
                                json[i].age_horario+
                            "</td>"+
                             "<td>"+
                                "<div class=\"combo_horario_"+json[i].age_codigo+"\" style=\"float:left;margin-left:10px;\">"+combo+"</div>"+
                                "<div style=\"float:left; margin-left:10px;\"><img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluirAgendamento('"+json[i].age_codigo+"')\"></div>"+
                            "</td>"+
                        "</tr>"+
                        "</form>";   
            }
            calend();
            html += "</table>";
        }
    });
    // Carrega tela de realocamento de horários
    if(valida_modal != false){
        $("#carregando-realocamento").dialog("destroy").remove();
        $("#lista_agendados").dialog("option", "position", "top");
        $("body").append("<div style='margin:0; padding:0;' id=\"lista_agendados\" title=\"Realocar Agendamentos\"> </div>");
        $("#lista_agendados")
        .html(imgCarregando);
        $("#lista_agendados").dialog({
                modal:true,
                width:1200,
                height:600,
                close:function(){
                    $(this).dialog("close");
                },
                buttons:{
                    Salvar:function(){
                        // Montando array de dados para salvar os novos horaários de agendamento
                        var array_age = new Array();
                        $(".linha_agendados").each(function(){
                             var age_codigo = $(this).data("age");
                             var coni_codigo = $(".convenioItem").val();
                             var esp_codigo = $(".esp_codigo"+coni_codigo).val();
                             var med_codigo = $("#medicos").val();
                             var dia = $("#data-pesquisa").val();
                             var hora = $(this).find("select.hora_"+age_codigo).val();
                             var dados  = '{';
                                 dados += ' "dados":[';
                                 dados += ' {"med_codigo":"'+med_codigo+'","coni_codigo":"'+coni_codigo+'","dia":"'+dia+'","hora":"'+hora+'","age_codigo":"'+age_codigo+'","esp_codigo":"'+esp_codigo+'"}';
                                 dados += '    ]';
                                 dados += '}';
                            
                            var obj = $.parseJSON(dados);
                            array_age.push(obj);
                        });
                        // Chama método de atualização
                        $.ajax({
                            url: baseUrl+'/agenda/distribuicao/transferencia',
                            type: "POST",
                            data: {
                                agendamentos:array_age
                            },
                            success: function(txt){
                                $("#lista_agendados").dialog("destroy").remove();
                                // Chama o mesmo click do listar Pacientes, no arquivo recepcao.js
                                $('.detalhes').trigger('click');
                                //$("#atualizar-grid").trigger('click');
                            }
                        });
                    }
                }
        });
        $("#lista_agendados").html(html);
    }
}

// Monta Combo de Arrays vinculados a unidade
function montaArrayComboMedicos(uni_codigo){
    var combo_medico = new Array;
    $.ajax({
        url: baseUrl+"/agenda/recepcao/buscar-profissionais",
        type: "POST",
        async: false,
        data: {
            uni_codigo:uni_codigo
        },
        success: function(txt){
           $.each( txt, function( key, value ) {
                combo_medico.push(value['usr_codigo']+"|"+value['usr_nome']+"|"+value['coni_codigo']);
           })
       }
    });
    return combo_medico;
}

function montaComboMedicos(medicos,usr_codigo_sel,uni_codigo){
    // Inicia Combo de Médicos
    //var comboMedico = "<select name=\"medico_agendamento[]\" class='med_"+age_codigo+" medicos' style=\"width:200px;\" onChange=\"carrega_especialidade('"+uni_codigo+"',this.options[this.selectedIndex].value,'"+age_codigo+"')\">";
    var comboMedico = "<select name=\"medico_agendamento[]\" id='medicos' style=\"width:200px;\" onChange=\"carrega_especialidade('"+uni_codigo+"',this.options[this.selectedIndex].value)\">";
    // Lendo informações dos médicos e montando os combos
    for(var i = 0; i < medicos.length;i++){
        var medicos_separa = medicos[i].split("|");
        var usr_codigo = medicos_separa[0];
        var usr_nome = medicos_separa[1];
        var coni_codigo_medico = medicos_separa[2];
        // Confere médico selecionado
        if(usr_codigo_sel == usr_codigo){
            comboMedico+= "<option value=\""+usr_codigo+"\" selected='selected' style=\"width:200px;\" title=\""+usr_nome+"\">"+usr_nome+"</option>";
            var med_selecionado = medicos_separa[0];
        } else {
            comboMedico+= "<option value=\""+usr_codigo+"\" style=\"width:200px;\" title=\""+usr_nome+"\">"+usr_nome+"</option>";
        }
    }
    comboMedico+= "</select>";
    // Retorna pra exibiçã no HTML da function carregaListaAgendados
    return comboMedico;
}

// Carrega as especialidade de acordo com o médico
function carrega_especialidade(uni_codigo,usr_codigo,age_codigo,dia){
    // Cria combo de especialidade de acordo com a unidade e o médico selecionado
    $.ajax({
         url: baseUrl+"/agenda/recepcao/carrega-especialidade",
         type: "POST",
         data: {
            uni_codigo: uni_codigo,
            usr_codigo: usr_codigo
         },
         success: function(txt){
           // Iniciando combo de especialidade
           var input_especialidade = "";
           var combo_especialidade = "<select name=\"esp_codigo_transf\" class='convenioItem' style=\"width:200px;\" onchange='montaArrayComboHorariosDisponiveis()'>";
           // Lendo array de dados retornado
           $.each( txt, function( key, value ) {
                combo_especialidade += "<option title=\""+value['esp_nome']+"\" value=\""+value['coni_codigo']+"\" style=\"width:200px;\">"+value['esp_nome']+"</option>";
                input_especialidade += "<input type='hidden' name='esp_codigo' class='esp_codigo"+value['coni_codigo']+"' value='"+value['esp_codigo']+"' />";
           });
           // Exibindo o combo de especialidade na div de especialidade da function carregaListaAgendados
           $(".combo_especialidade").html(combo_especialidade);
           $(".input_especialidade").html(input_especialidade);
           // Atualiza os combos de horários
           montaArrayComboHorariosDisponiveis();
        }
     });
}

// Combo é Montado quando carrega a tela de realocamento
function montaComboEspecialidades(medicos,usr_codigo_sel,uni_codigo,coni_codigo){
    // Pegando o médico selecionado
    for(var i = 0; i < medicos.length;i++){
        var medicos_separa = medicos[i].split("|");
        var usr_codigo = medicos_separa[0];
        if(usr_codigo_sel == usr_codigo){ 
            var med_selecionado = medicos_separa[0]; 
        }
    }
    // Cria combo de especialidade de acordo com a unidade e o médico selecionado
    var combo_especialidade = "";
    $.ajax({
         url: baseUrl+"/agenda/recepcao/carrega-especialidade",
         type: "POST",
         async: false,
         data: {
            uni_codigo: uni_codigo,
            usr_codigo: med_selecionado
         },
         success: function(txt){
           // Iniciando combo de especialidade
           combo_especialidade += "<select name=\"esp_codigo_transf\" class='convenioItem' style=\"width:200px;\" onchange='montaArrayComboHorariosDisponiveis()'>";
           // Lendo array de dados retornado
           $.each( txt, function( key, value ) {
                combo_especialidade += "<option title=\""+value['esp_nome']+"\" value=\""+value['coni_codigo']+"\" id='coni_codigo' style=\"width:200px;\" "+(value['coni_codigo'] == coni_codigo ? "selected=selected" : "")+" >"+value['esp_nome']+"</option>";
           })
           combo_especialidade+= "</select>";
           return false;
         }
     });
     return combo_especialidade; 
}

// Inputs é Montado quando carrega a tela de realocamento para auxiliar no ConiCodigo
function montaInputsEspecialidades(medicos,usr_codigo_sel,uni_codigo,coni_codigo){
    // Pegando o médico selecionado
    for(var i = 0; i < medicos.length;i++){
        var medicos_separa = medicos[i].split("|");
        var usr_codigo = medicos_separa[0];
        if(usr_codigo_sel == usr_codigo){ 
            var med_selecionado = medicos_separa[0]; 
        }
    }
    // Cria combo de especialidade de acordo com a unidade e o médico selecionado
    var input_especialidade = "";
    $.ajax({
         url: baseUrl+"/agenda/recepcao/carrega-especialidade",
         type: "POST",
         async: false,
         data: {
            uni_codigo: uni_codigo,
            usr_codigo: med_selecionado
         },
         success: function(txt){
           // Lendo array de dados retornado
           $.each( txt, function( key, value ) {
                input_especialidade += "<input type='hidden' name='esp_codigo' class='esp_codigo"+value['coni_codigo']+"' value='"+value['esp_codigo']+"' />";
           })
           return false;
         }
     });
     return input_especialidade; 
}

// Cria o Combo de Horários de acordo com os horários criados na montagem do Array
function montaCombo(horas,age_codigo){
    var combo = "";
    combo+= "<select name=\"horario_realocar[]\" onChange=\"removeHorariosCombo(this.options[this.selectedIndex].value,'"+age_codigo+"')\" class=\"hora_combo hora_"+age_codigo+"\" id=\""+age_codigo+"\">";
    //combo += "<option value=\""+$("#"+age_codigo+"_horario_consultas").val().substr(0,5)+"\">"+$("#"+age_codigo+"_horario_consultas").val().substr(0,5)+"</option>";
    combo += "<option value=\"0\">SELECIONE</option>";
    for(var i = 0; i < horas.length;i++){
        combo+= "<option value=\""+horas[i]+"\" style=\"width:40px;\">"+horas[i]+"</option>";
    }
    combo += "</select>";
    combo += "<input type=\"hidden\" name=\"hora_antiga\" class=\"antiga_"+age_codigo+"\" value=\"\">";
    combo += "<input type=\"hidden\" name=\"hora_removida\" class=\"hora_removida_"+age_codigo+"\" value=\"\">"
    return combo;
}

// Lista todos os horários de acordo com as informações
function carregaArrayComboHorariosDisponiveis(coni_codigo,dia,conv_codigo){
    // Validação se for data com / converte
    if(dia.indexOf("/")!=-1) {
        dia = brToSql(dia);
    } 
    var horas = new Array;
    $.ajax({
        url: baseUrl+'/agenda/distribuicao/selecionar-horario',
        type: 'GET',
        async: false, 
        data: {coni_codigo:coni_codigo,
               conv_codigo:conv_codigo,
               ds:dia,
               disponiveis:'s'},
        success:function(txt){
            // Pega os Horários Selecionados
            var selecionados = [];
            $(".hora_combo option:selected").each(function(){
                selecionados.push($(this).val());
            });
            // Inserção de Horas no array de horas, inserindo apenas os horários que não estão selecionados
            for(var i in txt){
                var array_valida = txt[i].split("|");
                if(!array_valida[0]){
                    horas.push(i);
                }
            }
        }
    });
    return horas;
}

// Lista todos os horários de acordo com as informações
function montaArrayComboHorariosDisponiveis(){
    var horas = new Array;
    $.ajax({
        url: baseUrl+'/agenda/distribuicao/selecionar-horario',
        type: 'GET',
        async: false, 
        data: {coni_codigo:$(".convenioItem").val(),
               conv_codigo:$("#conv_codigo").val(),
               ds:brToSql($("#data-pesquisa").val()),
               disponiveis:'s'},
        success:function(txt){
            // Inserção de Horas no array de horas, inserindo apenas os horários que não estão selecionados
            for(var i in txt){
                var array_valida = txt[i].split("|");
                if(!array_valida[0]){
                    horas.push(i);
                }
            }
            // Le todos agendamento e monta os combos
            $(".linha_agendados").each(function(){
                var age_codigo = $(this).data("age");
                var combo = montaCombo(horas,age_codigo);
                $(".combo_horario_"+age_codigo).html(combo);
            });
        }
    });
}


function removeHorariosCombo(hora,age_codigo){
   // Percorre os campos selects da table de agendados com o for
   $("table.table_agendados select.hora_combo").each(function(){
       if($(this).attr('id') != age_codigo){
           var id = $(this).attr('id');
           verificaSeExisteHora(hora,id,age_codigo);
       }
   });
   $(".antiga_"+age_codigo).val(hora);
}

function verificaSeExisteHora(hora,age_codigo,age_codigo_selecionado){
    var valida = false;
    $("#"+age_codigo_selecionado+" option").each(function() {
        if($(this).val() == hora){
            valida = true;
        }
    });
    // Se verificação ta ativa, realiza verificação
    if(valida == true){
        // Verifica se a data selecionada é igual a data do dia, verifica se o médico selecionado é igual ao médico
        //alert("Data Selecionada"+$(".data_"+age_codigo_selecionado).val()+"Data"+$(".data_"+age_codigo).val()+"Médico Selecionado:"+$(".med_"+age_codigo_selecionado).val()+"Médico"+$(".med_"+age_codigo).val());
        if($(".data_"+age_codigo_selecionado).val() == $(".data_"+age_codigo).val() && $(".med_"+age_codigo_selecionado).val() == $(".med_"+age_codigo).val()){
                // Verifica se já existe hora setada no select
                if($(".antiga_"+age_codigo_selecionado).val() != ""){
                    var posicao = "";
                    // Le todos os option do age código
                    $("#"+age_codigo+" option").each(function(){
                        if($(".antiga_"+age_codigo_selecionado).val() < $(this).val() && $(".antiga_"+age_codigo_selecionado).val() != $(this).val()){
                            posicao = $(this);
                            return false;
                        }
                    });
                    if($(".antiga_"+age_codigo_selecionado).val() != 0){
                        $(posicao).before("<option value=\""+$(".antiga_"+age_codigo_selecionado).val()+"\">"+$(".antiga_"+age_codigo_selecionado).val()+"</option>");
                    }
                }else{
                    $(".antiga_"+age_codigo_selecionado).val(hora);
                }
                if($("#"+age_codigo+" option[value='"+hora+"']").val() != 0){
                    $("#"+age_codigo+" option[value='"+hora+"']").remove();
                    $(".hora_removida_"+age_codigo).val(hora);
                }
        } 
    }
}

function editarFalta(horarios,grah_codigos,motivo,mof_codigo,dia,coni_codigo){
    $(".grah_horarios").remove();
    $(".edit").remove();//remove todas colunas que veio do edit para colocar as do proximo edit
    $(".horas").find('input[type=checkbox]:checked').removeAttr('checked'); //unckeck nos checkbox que nao seja do edit.
    
    montaPeriodos(dia,coni_codigo,grah_codigos);
    $('#mof_codigo option').each(function(){
           if($(this).val() == mof_codigo){
               $(this).attr('selected',true);
           }
    });
    if(motivo){
        $("#grah_motivo").val(motivo);
    }
    var horario = horarios.split(",");
    var cont = ""; // essa variavel vai definir a classe do TR que receberá os campos a serem editados
    var j = 0;
    var horarios_array = new Array();
    if($("tr.checkb").length > cont){
       cont = $("tr.checkb").length;
    }else{
       cont = 1;
    }
    for(var i = 0; i < horario.length;i++){
        var hora = horario[i].replace(":", "_").substring(0,5); //coloca o padrao da hora com _ para montar as TD
        horarios_array
        var html = "";
        var retorno = grah_codigos.split(",");// quebra em array 
        j++; // pra saber quando quebrar a linha e comecar contar novamente ateh a proxima quebra
         if($("tr.linha_"+cont+" input[type=checkbox]").length == 8){
             j = 0;
             cont++;
             var tr = ""+
                     "<tr class=\"checkb linha_"+cont+"\"> </tr>";

             $(".first").append(tr);
         }
        html += "<td class=\"horas col_hora_"+hora+" edit\" data-hora=\"hora_"+hora+"\">"+
                         "<input type=checkbox id=\"hora_"+hora+"\" class=\"checkbox_horario\" name=\"hora_marcada[]\" checked=checked value=\""+hora+"\" data-grah=\""+retorno[i]+"\">"+hora.replace("_", ":")+
                   "</td>";
        horarios_array.push(horario[i]);
        $(".linha_"+cont).append(html);
    }
    
   /*JA ESTA MONTANDO A TABELA DE BAIXO, FALTA APENAS */
   //montaInativados(horarios_array,$('#mof_codigo').find('option').filter(':selected').text(),motivo,grah_codigos,mof_codigo);
}
function montaInativados(horarios,mof_descricao,motivo,grah_codigos,mof_codigo,dia,coni_codigo){

     var html2 = "<table border=0 id=\"table_"+grah_codigos+"\" class=\"grid ui-widget ui-widget-content ui-corner-all grah_horarios table_"+grah_codigos[0]+"\" width='100%'>"+
                        "<tr>"+
                            "<td><b>Hora</td>"+
                            "<td><b>Motivo</td>"+
                            "<td><b>Esp. Motivo</td>"+
                            "<td><b>Acoes</td>"+
                        "</tr>"+
                        "<tr>"+
                            "<td width='240'>";
                                var m = 0;
                                for(var n = 0; n < horarios.length;n++){
                                    if(m == 3){
                                        html2 += "<br/>";
                                        m = 0;
                                    }
                                    m++;
                                    html2 += horarios[n]+"&nbsp;&nbsp;&nbsp";
                                }
                                
                            html2 += "</td>"+
                            "<td width=\"100\">"+mof_descricao +"</td>"+
                            "<td width=\"150\">"+motivo+"</td>"+
                            "<td width=\"50\"><img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/editar.png' onClick=\"editarFalta('"+horarios+"','"+grah_codigos+"','"+motivo+"','"+mof_codigo+"','"+dia+"','"+coni_codigo+"')\"> "+ 
                                             "<img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"deleteGrupoInativado('"+grah_codigos+"')\">"+
                            "</td>"+
                        "</tr>"+
                    "</table>";
        $("#tempo_pausa").append(html2);
    
}

function deleteGrupoInativado(grah_codigos){
   $.ajax({
        url: baseUrl+'/agenda/distribuicao/delete-grade-horario',
        type: "POST",
        data: {
            grah_codigos: grah_codigos
        },
        success: function(){
            var retorno = grah_codigos.split(",");
           // alert();
            $(".table_"+retorno[0]).remove();
        }
    });
}


/*function copiar(tipo, input){
	switch (tipo) {
		case "copiar-semana":
			//copiarSemana(input);
			break;
		case "copiar-mes":
			//copiarMes(input);
			break;
		case "limpar-dia":
			//limparDia(input);
			break;
		case "limpar-semana":
			//limparSemana(input);
			break;
		case "limpar-mes":
			//limparMes(input);
			break;
		default:
			break;
	}
}*/
/*
function limparDia(input){
	var padrao = $(input).parents("td").data("padrao")==-1?"":$(input).parents("td").data("padrao");
	$(input).val( padrao );
	verificarCores();
}

function limparSemana(input){
	limparDia(input);
	copiarSemana(input);
}

function limparMes(input){
	limparDia(input);
	copiarMes(input);
}

function copiarSemana(input){
	var valor = $(input).val();
	var td = $(input).parents("td");
	var coni = td.data("coni");
	var index = td.data("index");
	var dow = td.data("dow");
	
	for(var i=index-dow; i<index-dow+7; i++){
		$("[data-coni='"+coni+"'][data-index='"+i+"'] input:first").val(valor);
	}	
	
	verificarCores();
}

function copiarMes(input){
	var valor = $(input).val();
	var td = $(input).parents("td");
	var coni = td.data("coni");
	
	$("[data-coni='"+coni+"']").each(function(){
		$(this).find("input:first").val(valor);
	});
	verificarCores();
}
*/
function verificarCores(){
	$(".dia").each(function(){
		// Qtd de agendados
                var agendados = $(this).data("agendados");
                // Qtd de agendamento permitido no mês
                var padrao = $(this).data("padrao");
		var input = $(this).find("input");
		// Qtd atual de agendamentos
                var atual = input.val();
                // Dia Selecionado
                var dia = $(this).data("dia");
                
		
		//window.console && console.log("agendados "+agendados);
		//window.console && console.log("padrao: "+padrao);
		//window.console && console.log("atual: "+atual);
		//window.console && console.log("dia: "+dia);
				
		if(atual != "" && agendados > atual){
			mensagem("Atenção","Já existem "+agendados+" agendamento(s) para o dia "+dataToBr(dia)+".", 335	, 150);
			input.val(agendados);
			atual = agendados;
		}
		
		if((atual == "" && padrao == -1) || atual == padrao)
			$(this).addClass("agenda_sem_alteracao").removeClass("agenda_com_alteracao").removeClass("");
		else if(atual == input.get(0).defaultValue){
			$(this).removeClass("agenda_sem_alteracao").addClass("agenda_com_alteracao").removeClass("");
		} else 
			$(this).addClass("agenda_alterando");
		
	});
	
	$(".gram_mes").each(function(){
		//var agendados = $(this).data("agendados");
		var agendados = $(this).data("agendados");
                var padrao = $(this).data("padrao");
		var input = $(this).find("input");
		var atual = input.val();
		
		if(atual != "" && agendados > atual){
			mensagem("Atenção","Já existem "+agendados+" agendamento(s) para este mês.", 335, 150);
			input.val(agendados);
			atual = agendados;
		}
		
		if((atual == "" && padrao == -1) || atual == padrao)
			$(this).addClass("agenda_sem_alteracao").removeClass("agenda_com_alteracao").removeClass("agenda_alterando");
		else if(atual == input.get(0).defaultValue){
			$(this).removeClass("agenda_sem_alteracao").addClass("agenda_com_alteracao").removeClass("agenda_alterando");
		} else 
			$(this).addClass("agenda_alterando");
		
	});
}

function afterSubmit(json){
	
	if(!json.success){
		if(json.code == 1 || json.code == 2)
			popupLogin();
		
		else
			mensagem(json.titulo,json.mensagem, 300, 150);
		
		return;
	} else {

		$("body").append("<div id=\"mensagem-dialog\" title=\"Sucesso!\" />");
		$("#mensagem-dialog")
		.html(json.alterados+" registros alterados")
		.dialog({
			modal: true,
			width: 250,
			height: 130,
			close: function(){
				window.location.href = baseUrl + "/agenda/distribuicao/";
				$(this).remove();
			},
			buttons: {
				Ok: function(){
					$(this).dialog('close');
				}
			}
		});

	}
}

// Carrega os dias em forma de calendário
function carregarCalendario(){
        // desabilitar btn salvar
	$(".salvar").addClass("ui-state-disabled");
	var selecionados = getProcedimentosSelecionados();
        if(selecionados.length == 1 && selecionados[0] == 0){
            $("#calendario").html("<em>Selecione algum exame</em>");
            return;
	}
	$("#calendario").html(imgCarregando());
	var url = baseUrl + "/agenda/distribuicao/selecionar-data/procs/"+selecionados+"/de/"+brToSql($("#age_data").val());
	$("#calendario").load(url, bindCalendario);	
}

/**
 * Adicionar eventos no grid
 */
function bindCalendario(){
	/*$("#grade tr th").slice(1,2).html("Mês");
	
	$("[data-dow]").each(function(){// cada dia
		var index = $(this).data("index");
		var dow   = $(this).data("dow");
		
		$("[data-index='"+index+"']").addClass("dow"+dow).data("dow",dow);
	})
	
	$("#grade tr td[data-dia]").hover(function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").addClass("destaque");
		
	}, function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").removeClass("destaque");
		
	});
	
	$(".dia input, .gram_mes input").bind("change",function(){
		verificarCores();				
		
	});

	//$(".dia input").contextMenu({menu: 'myMenu', leftButton: true}, copiar);
	//$(".gram_mes input").contextMenu({menu: 'myMenuMes', leftButton: true}, copiar);
        $(".dia input").contextMenu({menu: 'myMenu', leftButton: true});
	$(".gram_mes input").contextMenu({menu: 'myMenuMes', leftButton: true});

        
	verificarCores();
	
	// tooltip
	$(".dia").each(function(){			
		var proc_nome = $(this).parents("tr").find("th:first").html();
                var dia = $(this).data("dia");
		var index = $(this).data("index");
		var dow = $("[data-dow][data-index='"+index+"']").data("dow");
		var semana = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"];
		var agendados = $(this).data("agendados");
		var padrao = $(this).data("padrao");
		padrao = (padrao==-1)?"&infin; ilimitadas":padrao;
		var atual = $(this).data("atual");
		
                if (padrao!=""){
                    var html = "<div><strong>Profissional: </strong>"+proc_nome+"<br />";
			html += "<strong>Data: </strong>"+dataToBr(dia)+" ("+semana[dow].toLowerCase()+")<br />";
			html += "<strong>Agendados: </strong>"+agendados+"<br />";
			html += "<strong>Padrão do dia: </strong>"+padrao+"</div>";
                } else {
                    atual = (atual=="")?"Não informado":atual;
                    var html = "<div><strong>Profissional: </strong>"+proc_nome+"<br />";
			html += "<strong>Data: </strong>"+dataToBr(dia)+" ("+semana[dow].toLowerCase()+")<br />";
			html += "<strong>Agendados: </strong>"+agendados+"<br />";
			html += "<strong>Padrão do dia: </strong>"+atual+"</div>";
                }
		
		$(this).easyTooltip({
			content: html
		});
	});
	
	$(".gram_mes").each(function(){
		var proc_nome = $(this).parents("tr").find("th:first").html();
		var dia = $(this).data("dia");
		var agendados = $(this).data("agendados");
		var padrao = $(this).data("padrao");
                var atual = $(this).data("atual");
		padrao = (padrao==-1)?"&infin; ilimitadas":padrao;
                padrao = (padrao=="")?"&infin; ilimitadas":padrao;
		
		
		var html = "<div><strong>Profissional: </strong>"+proc_nome+"<br />";
			html += "<strong>Mês: </strong>"+dataToBr(dia).substr(3)+"<br />";
			html += "<strong>Agendados: </strong>"+agendados+"<br />";
			html += "<strong>Padrão do mês: </strong>"+padrao+"</div>";
		
		$(this).easyTooltip({
			content: html
		});
	});
	
	carregaValoresAntigos();*/
    $("#grade tr th").slice(1,2).html("Mês");
	
	$("[data-dow]").each(function(){// cada dia
		var index = $(this).data("index");
		var dow   = $(this).data("dow");
		
		$("[data-index='"+index+"']").addClass("dow"+dow).data("dow",dow);
	})
	
	$("#grade tr td[data-dia]").hover(function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").addClass("destaque");
		
	}, function(){
		var data = $(this).data("dia");
		$("td[data-dia="+data+"]").removeClass("destaque");
		
	});
	
	$(".dia input, .gram_mes input").bind("change",function(){
		verificarCores();				
		
	});

	$(".dia input").contextMenu({menu: 'myMenu', leftButton: true}, copiar);
	$(".gram_mes input").contextMenu({menu: 'myMenuMes', leftButton: true}, copiar);

	verificarCores();
	
	// tooltip
	$(".dia").each(function(){			
		var proc_nome = $(this).parents("tr").find("th:first").html();
		var dia = $(this).data("dia");
		var index = $(this).data("index");
		var dow = $("[data-dow][data-index='"+index+"']").data("dow");
		var semana = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"];
		var agendados = $(this).data("agendados");
		var padrao = $(this).data("padrao");
		padrao = (padrao==-1)?"&infin; ilimitadas":padrao;
		
		var html = "<div><strong>Exame: </strong>"+proc_nome+"<br />";
			html += "<strong>Data: </strong>"+dataToBr(dia)+" ("+semana[dow].toLowerCase()+")<br />";
			html += "<strong>Agendados: </strong>"+agendados+"<br />";
			html += "<strong>Padrão do dia: </strong>"+padrao+"</div>";
		
		$(this).easyTooltip({
			content: html
		});
	});
	
	$(".gram_mes").each(function(){
		var proc_nome = $(this).parents("tr").find("th:first").html();
		var dia = $(this).data("dia");
		var agendados = $(this).data("agendados");
		var padrao = $(this).data("padrao");
		padrao = (padrao==-1)?"&infin; ilimitadas":padrao;
		
		
		var html = "<div><strong>Profissional: </strong>"+proc_nome+"<br />";
			html += "<strong>Mês: </strong>"+dataToBr(dia).substr(3)+"<br />";
			html += "<strong>Agendados: </strong>"+agendados+"<br />";
			html += "<strong>Padrão do mês: </strong>"+padrao+"</div>";
		
		$(this).easyTooltip({
			content: html
		});
	});
	
	carregaValoresAntigos();

}

function carregaValoresAntigos(){
	$.each(_cacheDataSelecionada,function(coni_codigo,data){
		if(coni_codigo)
			$("#coni_"+coni_codigo).val(data);
	});
	
	habilitarOuNaoBotaoSalvar()
}

function selecionarProcedimento(e){
	// só pode ser a tecla 39 (seta para direita)
	if(e.keyCode && e.keyCode != 39 || e.charCode)
		return;
	
	// se o primeiro for 0, limpar select
	if($("#procedimentos-selecionados option:first").val() == "0"){
		$("#procedimentos-selecionados").empty();
	}
	
	// add
	$("#procedimentos-selecionados").append(
		$("#procedimentos option:selected")
	);
	
	habilitarOuNaoBotaoAtualizarData();
	carregarCalendario();
}

function deselecionarProcedimento(e){
	
	// só pode ser a tecla 39 (seta para esquerda)
	if(e.keyCode && e.keyCode != 37 || e.charCode)
		return;
	
	// remover
	$("#procedimentos-selecionados option:selected").appendTo("#procedimentos");
	
	// se não houver mais opções, add "Nenhum"
	if($("#procedimentos-selecionados option").size() == 0){
		$("#procedimentos-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum exame selecionado</option>');
	}
	
	habilitarOuNaoBotaoAtualizarData();
	carregarCalendario();
}

function listarProcedimento(json){	
	var select = $("#procedimentos").empty();
	var loop = 0;

	for (var proc in json) {
		var jaExiste = false;

		$("#procedimentos-selecionados option").each(function(){
			if($(this).val() == json[proc].coni_codigo){
                           jaExiste = true;
			}
		});
                
                if(json[proc].esp_nome){
                    var esp_nome = "--"+json[proc].esp_nome.toUpperCase();
                    $.trim(esp_nome);
                }else{
                    var esp_nome = "";
                }

		if(!jaExiste){	
			select.append("<option value=\""+json[proc].coni_codigo+"\">"+json[proc].proc_nome+esp_nome+"</option>");			
                        loop++;
		}
	}
	
	if(loop == 0){
		select.append("<option value=\"0\" disabled=\"disabled\">Nenhum procedimento disponível</option>");
	}
	
	fecharMensagemSemOk("carregando-conis");
}

/**
 * @return Array
 */
function getProcedimentosSelecionados(){
    var selecionados = [];
    $("#procedimentos-selecionados option").each(function(){
        selecionados.push($(this).val());
    });
    return selecionados;
}


function habilitarOuNaoBotaoSalvar(){
	var tudo_ok = true;
	
	// verificar se há procedimentos selecionados:
	var sel = getProcedimentosSelecionados();
	if(sel.length == 1 && sel[0] == 0){
		//mensagem("Atenção","Selecione algum exame!", 250, 120);
		//$("#med_nome").select();
		tudo_ok = false;
	}
	
	if(tudo_ok)
		$(".salvar").removeClass("ui-state-disabled");
	else 
		$(".salvar").addClass("ui-state-disabled");
}

function habilitarOuNaoBotaoAtualizarData(){
	var selecionados = getProcedimentosSelecionados();
	if(selecionados.length){
		$("#atualizar-grid").removeClass("ui-state-disabled");
	} else {
		$("#atualizar-grid").addClass("ui-state-disabled");
	}
}


function carregarHorario(coni_codigo,data_selecionada){
    //$(".salvar").addClass("ui-state-disabled");
    var conv_codigo = $("#conv_codigo").val();
    $("#horario").html(imgCarregando());
    var url = baseUrl + "/agenda/distribuicao/selecionar-horario/ds/"+data_selecionada+"/coni_codigo/"+coni_codigo+"/conv_codigo/"+conv_codigo;
    $("#horario").load(url, bindHorario);
}


/**
 * Adicionar eventos no grid
 */

function bindHorario(){
        $("#botoes_tempo").show();
	
	$("#grade tr td[data-hora]").hover(function(){
		var data = $(this).data("hora");
		$("td[data-hora="+data+"]").addClass("destaque");
		
	}, function(){
		var data = $(this).data("hora");
		$("td[data-hora="+data+"]").removeClass("destaque");
		
	})
	.click(marcarHora)
	.disableSelection();
	
	$(".sem-vaga").each(function(){
		var obj = $(this);
		var index = obj.data("index");
                var paciente = $(this).data("paciente");
                if($(this).hasClass("sem-vaga")){
                    var html = "<div><strong>Horário Ocupado: </strong>"+paciente+"<br />";
                    obj.easyTooltip({
                            content: html
                    });
                }
	});
        //carregaValoresAntigos();
}

function marcarHora(){
     if($(this).hasClass("marcada")){
        $(this).removeClass("marcada");
        $(this).addClass("com-vaga");
     }else{
        $(this).removeClass("com-vaga");
        if(!$(this).hasClass("sem-vaga")){
            $(this).addClass("marcada");
        }
     }
     
}

function limpaTextArea(){
    //alert("limpa");
    if($("#grah_motivo").val() == "Especifique o motivo"){
        $("#grah_motivo").val("");
        //$('#grah_motivo').css({color:"#000000"});
    }
    $('#grah_motivo').css({color:"#000000"});
}

function sujaTextArea(){
   if($("#grah_motivo").val() == ""){
       $("#grah_motivo").val("Especifique o motivo");
       $('#grah_motivo').css({color:"#838B8B"});
   }
   
    
}