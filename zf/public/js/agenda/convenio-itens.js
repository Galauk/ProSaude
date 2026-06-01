/* -----------------------------------------------------------------
* MÉTODOS CONVÊNIOS ITENS AGENDAMENTO ESTABELECIMENTOS DE SAUDE
* ----------------------------------------------------------------*/

// Função responsável por abrir e fechar a Inclusão de Horários
function abreFechaInclusaoDeHorarios(){
    $("#inclusao-horarios-det").toggle("slow");
}

// Função responsável por abrir e fechar os dia da semana
function abreFechaDiasDaSemana(diaDaSemana){
    for(i=0; i<=7; i++){
        if (i==diaDaSemana) {
            $("#"+diaDaSemana+"-dia-semana-det").find("input,textarea").prop("readonly",true);
            $("#encaixe"+i+"").attr("disabled","disabled");
            $("#"+diaDaSemana+"-dia-semana-det").toggle( "slow" );
        } else {
            $("#"+i+"-dia-semana-det").slideUp();
        }
    }    
}

// Função responsavel por manipular o combo de dias da semana
function populaComboDiasDaSemana(id,nomeDia){
    // Mostra Inclusão de horarios
    $("#inclusao-horarios").show();
    $("#inclusao-horarios-det").show();
    // Verifica se o dia esta checkado
    if ($("#"+id+"").is(":checked") == true) {
        // Alimenta o indice oculto de dia
        $("#dia"+id+"").val(""+id+"");
        // Inclui o dia no combo de select
        $("#dias").append("<option value='"+id+"'>"+nomeDia+"</option>");
    // Se não esta checkado
    } else {
        // Elimina indice oculto de dia
        $("#dia"+id+"").val("");
        // Remove dia do combo
        $("#dias option[value='"+id+"']").remove();
        // Função responsavel por ver se existe algum elemento no combo de dias
        validaComboDiasDaSemana();
    }
}

// Função responsavel por ver se existe algum elemento no combo de dias
function validaComboDiasDaSemana(){
    // Se não tiver oculta o combo
    if ($("#dias option").length == 0) {
        $("#inclusao-horarios").hide();
        $("#inclusao-horarios-det").hide();
    }
}

// Função responsavel por Incluir os horários e os dados de cada dia de acordo com o que foi preenchido
function adicionaHorariosDiaDaSemana(){
    // Se todos campos obrigatórios forem preenchidos adiciona
    if (($("#dias").val() != "" || $("#dias").val() != null || $("#dias").val() != "undefined") && $("#hr_fim1").val() != "" && $("#hr_inicio1").val() != "" && $("#coni_cota_dia").val() != "" && $("#coni_intervalo_int") != "") {
        // Jogando valores de dias em um array
        ArrayDeDias = $("#dias").val();
        // Percorrendo array de dias 
        for(var i=0;i<ArrayDeDias.length;i++){
            // Primeiro campo somente joga os valores, porque os mesmos são estastico
            $("#"+ArrayDeDias[i]+"hr_inicio1").val($("#hr_inicio1").val());
            $("#"+ArrayDeDias[i]+"hr_fim1").val($("#hr_fim1").val());
            $("#"+ArrayDeDias[i]+"cie_quantidade").val($("#cie_quantidade").val());
            // Segundo campo em diante faz um append porque são adicionados automaticamente pelo+
            if ($(".qtd-hor").size() > 1) {
                for (cont = 2; cont <= $(".qtd-hor").size(); cont++) {
                    $("#hrs"+ArrayDeDias[i]+"").append("<div class='horario"+ArrayDeDias[i]+"'>\n\
                                                            <label>Hora de Início: </label>&nbsp;" +
                                                            "<input type='text' name='"+ArrayDeDias[i]+"_hora_inicial_"+cont+"' id='"+ArrayDeDias[i]+"hr_inicio"+cont+"' style='width:58px;' class='ui-state-default' value='"+$("#hr_inicio"+cont+"").val()+"' />" +
                                                            "<label style='margin-left:4px; width:110px;'>Hora de Término:</label>&nbsp;" +
                                                            "<input type='text' name='"+ArrayDeDias[i]+"_hora_final_"+cont+"' id='"+ArrayDeDias[i]+"hr_fim"+cont+"' style='width:58px;' class='fim ui-state-default' value='"+$("#hr_fim"+cont+"").val()+"' />\n\
                                                        </div>");
                }
            }
            // Jogando demais valores dos campos
            $("#coni_cota_mes"+ArrayDeDias[i]+"").val($("#coni_cota_mes").val());
            $("#coni_cota_dia"+ArrayDeDias[i]+"").val($("#coni_cota_dia").val());
            $("#coni_intervalo"+ArrayDeDias[i]+"").val($("#coni_intervalo").val());
            $("#coni_intervalo_int"+ArrayDeDias[i]+"").val($("#coni_intervalo_int").val());
            // Validação de encaixe
            if ($("#encaixe").is(":checked")){
                $("#encaixe"+ArrayDeDias[i]+"").attr("checked",true);
                $("#qtd-enc"+ArrayDeDias[i]+"").show();
                $("#coni_encaixe"+ArrayDeDias[i]+"").val($("#coni_encaixe").val());
            }
            // Removendo dia do select que realiza as inclusão
            $("#dias option[value='"+ArrayDeDias[i]+"']").remove();
            // Chama função responsavel por desabilitar o campo checkbox
            desabilitaCheckBoxDiaDaSemana(ArrayDeDias[i]);
            // Carregando dados do dia da semana
            $("#"+ArrayDeDias[i]+"-dia-semana").show();
        }
        // Limpando os campos de Inclusão de Usuários
        $("#inclusao-horarios-det").find("input,textarea,button").val("");
        $("#encaixe").attr('checked', false);
        $("#coni_encaixe").val("0");
        $("#checkbox_div").hide();
        // Remove os campos de horários
        for(i=1; i <= $(".horarios").size(); i++) {
            $(".horarios").find("input,label").remove();
            $(".horarios").remove();
        }
        // Remove a inclusão de horários se não tiver nenhum dia pra inserir
        validaComboDiasDaSemana();
    // Se não verifica erros e imprime
    } else {
        var msg_erro = "";
        if ($("#dias").val() == "" || $("#dias").val() == null || $("#dias").val() == "undefined") {
            msg_erro += " - Preencha o campo Dias!<br />";
        }
        if ($("#hr_inicio1").val() == "") {
            msg_erro += " - Preencha o campo Hora de Início!<br />";
        }
        if ($("#hr_fim1").val() == "") {
            msg_erro += " - Preencha o campo Hora de Término!<br />";
        }
        if ($("#coni_cota_dia").val() == "") {
            msg_erro += " - Preencha o campo Quantidade de vagas dia!<br />";
        }
        if (msg_erro != "") {
            mensagem("Erro ao adicionar!",msg_erro,"450","200");
        }
    }
}

// Função responsavel por incluir os campos de hora inicial e final quando clica no + da tela de Inclusao de Horarios
function adicionaCamposHoraInicialFinalInclusaoDeHorarios(){
    // Valida se o campo está no formato correto, se não informa um erro
    for (cont = 1; cont <= $(".qtd-hor").size(); cont++) {
        if ($("#hr_inicio"+cont).val().length == 5 && $("#hr_fim"+cont).val().length == 5) {
            msg = true;
        } else {
            msg = false;
        }
    }
    // Se não conter erros começa o processo de inserção dos campos de hora inicial e final
    if (msg) {
        // Incrementa os IDS dos campos por padrão já vem 1 do primeiro campo existente
        i = $(".qtd-hor").size() + 1;
        
        if ($("#modal_valida").val() == "T") {
            alert("MODAL VALIDA");
            var func = "chamaCalculo()";
        }
        // Inserindo os campo na Div de Horas
        $("#hrs").append("<div class='horarios'>\n\
                            <label class='ui-corner-bl ui-corner-tr'>Hora de Início: </label>&nbsp;" +
                            "<input type='text' name='hr_inicio"+i+"' id='hr_inicio"+i+"' style='width:58px;' class='ini qtd-hor ui-state-default' onkeypress='mask()' maxlength='5' value='' class='focus' />" +
                            "<label class='ui-corner-bl ui-corner-tr' style='margin-left:4px; width:110px'>Hora de Término:</label>&nbsp;" +
                            "<input type='text' name='hr_fim"+i+"' id='hr_fim"+i+"' style='width:58px;'  class='fim ui-state-default'  value='' class='focus' />\n\
                        </div>");
        // Colocando mascará
        $(".ini").mask('99:99');
        $(".fim").mask('99:99');
        $("#hr_inicio"+i).focus();
        // Alimenta o contador de qtd de horas não sei pra que ???????
        $("#qtd_hr").val(i);
    } else {
        // Incrementa os IDS dos campos por padrão já vem 1 do primeiro campo existente
        i = $(".qtd-hor").size();
        // Tela de Erro
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
                    $("#hr_inicio"+i).focus();
                    $(this).dialog("close");
                }
            }
        });
    }
}

// Exclui os dados do Dia
function excluiHorariosDiasDaSemana(dia){
    $("body").append("<div id='exclui-horarios-dias' title='Confirmação de exclusão'></div>");
    $("#exclui-horarios-dias")
    .html("<span>Deseja realmente excluir este item?</span>")
    .dialog({
        modal: true,
        width: 300,
        height: 120,
        buttons:{
            Excluir: function(){
                limpaCamposDiaDaSemana(dia);
                habilitaCheckBoxDiaDaSemana(dia);
                $("#"+dia+"-dia-semana-det").hide();
                $("#"+dia+"-dia-semana").hide();
                $("#exclui-horarios-dias").dialog("destroy").remove();
            },
            Cancelar: function(){
                $("#exclui-horarios-dias").dialog("destroy").remove();
            }
        }
    });
}

// Função responsável por limpar os campos quando o mesmo é excluido
function limpaCamposDiaDaSemana(dia){
    $("#dia"+dia+"").val("");
    $("#coni_cota_mes"+dia+"").val("");
    $("#coni_cota_mes_original"+dia+"").val("");
    $("#condi_age_cota_dia_original"+dia+"").val("");
    $("#coni_cota_dia"+dia+"").val("");
    $("#encaixe"+dia+"").attr("checked",false);
    $("#coni_encaixe"+dia+"").val("");    
    $("#qtd-enc"+dia+"").hide();
    $("#"+dia+"hr_inicio1").val("NULL");
    $("#"+dia+"hr_fim1").val("NULL");
    $("#coni_intervalo_int"+dia+"").val("");
    $("#coni_intervalo"+dia+"").val("");
    for(i=1; i <= $(".horario"+dia+"").size(); i++) {
        $(".horario"+dia+"").find("input,label").remove();
        $(".horario"+dia+"").remove();
    }
}

// Habilita o check quando os campos de horários é excluido
function habilitaCheckBoxDiaDaSemana(dia){
    $("#"+dia+"").removeAttr("disabled","disabled");
    $("#"+dia+"").removeAttr("checked");
}

// Função responsável por desabilitar o campo de dia do checkbox
function desabilitaCheckBoxDiaDaSemana(dia){
    $("#"+dia+"").attr("disabled","disabled");
}

// Verifica se a edição está sendo realizada e traz os campos necessários em aberto
function configuraEdicaoDiasDaSemana(){
    // Verifica se a edição está sendo realizada
    if ($("#coni_codigo").val() != ""){
        for (i=1; i<=7; i++) {
            // Verifica se o dia esta checkado
            if ($("#"+i+"").is(":checked")==true){
                // Mostrando os dados do 1 dia da semana
                $("#"+i+"-dia-semana").show();
                //$("#"+i+"-dia-semana-det").show();
                // Desabilitando o checkbox
                $("#"+i+"").attr("disabled","disabled");
                // Setando o valor pro campo oculto de dia, já que o checkbox fica desabilitado
                $("#dia"+i+"").val(""+i+"");
                // Verifica se o encaixe está selecionado e traz ele em aberto
                if($("#coni_encaixe"+i+"").val() != "" && $("#coni_encaixe"+i+"").val() != "0"){
                    $("#encaixe"+i+"").attr("checked","checked"); 
                    $("#qtd-enc"+i+"").show();
                }
            }
        }
    }
}

/* -----------------------------------------------------------------
* MÉTODOS GERAIS UTIL PARA CONVÊNIO DE LABORATORIO OU AGENDAMENTO
* ----------------------------------------------------------------*/

function carregaEspecialidade(usr_codigo, conv_codigo) {
    if (conv_codigo) {
        var metodo = "carrega-especialidade-por-convenio";
    } else {
        var metodo = "carrega-especialidade";
    }
    $.ajax({
        url: baseUrl + '/agenda/convenio-itens/' + metodo,
        type: "POST",
        data: {
            usr_codigo: usr_codigo,
            conv_codigo: conv_codigo
        },
        success: function(json) {
            $.each(json, function(key, value) {
                if ($("#esp_codigo_hidden").val() != "") {
                    if (value['esp_codigo'] == $("#esp_codigo_hidden").val()) {
                        $("#esp").append("<option title=\"" + value['esp_nome'] + "\"  value=\""+value['esp_codigo']+"\">" + value['esp_nome'] + "</option>");
                        $("#med_esp").show('slow');
                    }
                } else {
                    $("#esp").append("<option title=\"" + value['esp_nome'] + "\"  value=\""+value['esp_codigo']+"\" "+(value['esp_codigo'] == $("#esp_codigo_hidden").val() ? "selected=selected" : "")+" >" + value['esp_nome'] + "</option>");
                    $("#med_esp").show('slow');
                }
            })
        }
    });
    return true;
}

function chamaCalculo() {
    var minutos = "";
    minutos = horaEmMinutos();
    if (!minutos) {
        return false;
    }
    var soma = (parseInt($("#coni_cota_dia").val()) + parseInt($("#coni_encaixe").val()));

    var intervalo = minutos / soma;
    if (intervalo < 0) {
        intervalo = intervalo * -1;
    }
    $("#coni_intervalo_int").val(parseInt(intervalo));
    $("#coni_intervalo").val(intervalo);
}

function calculaComEncaixe(){
    chamaCalculo();      
}

function mostraEncaixe() {
    if ($('#encaixe').is(':checked')) {
        $("#checkbox_div").show();
    } else {
        $("#checkbox_div").hide();
        $("#coni_encaixe").val(0);
        chamaCalculo();
    }
}

function horaEmMinutos() {
    minutos = 0
    for (i = 1; i <= $(".ini").size(); i++) {
        if ($("#hr_inicio"+i).val() == "" || $("#hr_inicio"+i).val() == null) {
            return false;
        } else {
            hr_inicio = $("#hr_inicio"+i).val().split(':');
        }

        if ($("#hr_fim"+i).val() == "__:__" || $("#hr_fim"+i).val() == null) {
            return false;
        } else {
            hr_fim = $("#hr_fim" + i).val().split(':');
        }

        minutos_ini = (hr_inicio[0] * 60 + parseInt(hr_inicio[1]));
        minutos_fim = (hr_fim[0] * 60 + parseInt(hr_fim[1]));
        minutos += minutos_fim - minutos_ini;
    }
    return minutos;
}

// Inclui o 2 pontos na 3 casa
function mask() {
    for (i = 1; i <= $(".ini").size(); i++) {
        if ($("#hr_inicio" + i).val().length == 2) {
            $("#hr_inicio" + i).val($("#hr_inicio" + i).val() + ':');
        }
    }
}

/* -----------------------------------------------------------------
* MÉTODOS CONVÊNIOS
* ----------------------------------------------------------------*/


/* -----------------------------------------------------------------
* OUTROS MÉTODOS DE CONVÊNIO QUE NÃO SEI SE ESTÁ SENDO USADO
* ----------------------------------------------------------------*/

pad = function(val, len, str) {
    val = String(val);
    len = len || 2;
    str = str || "0";
    while (val.length < len)
        val = str + val;
    return val;
};

function calculatempo() {
    for (i = 1; i <= $(".ini").size(); i++) {
        var hora = $("#hr_inicio" + i).val();
        console.log('Hora Inicial: ' + hora);
        var cotas = (parseInt($("#coni_cota_dia").val()) + parseInt($("#coni_encaixe").val()));
        var minutos = cotas * $("#coni_intervalo").val();
        var acrescenta = minutos / $(".ini").size();
        console.log('Minutos à acrescentar: ' + acrescenta);

        var regexp = "(?:([01]?[0-9]|2[0-3]):)?([0-5][0-9])"; //Expressão de hora
        var date = hora.match(new RegExp(regexp)); //Executa a expressão
        console.log(date); //Qual resultado mermo?

        date = new Date(0, 0, 0, date[1], date[2], 0, 0); //Cria a data baseado na expressão
        date.setMinutes(date.getMinutes() + acrescenta); //Aumentamos o tempo            

        console.log(date); //Verificamos o date
        horaAcre = pad(date.getHours()) + ':' + pad(date.getMinutes());
        $("#hr_fim" + i).val(horaAcre);
    }
}

function mostraEstratificacao() {
   if ($("#estratificacao option:selected").val() == '') {
        $("#estratificacao_div").hide();
        $("#cie_quantidade").val(0);
    } else {
        $("#estratificacao_div").show();      
       
    }
}
function adicionaNaListagemEstratificacao() {
   $('#listagemEstratificacao').append('<tr><td><input name=cie_coidigo id=cie_coidigo value='+$("#estratificacao option:selected").val()+' /></td><td>'+$("#estratificacao option:selected").html()+'</td><td>'+$("#cie_quantidade").val()+'</td></tr>');
}
$(function() {
    
    configuraEdicaoDiasDaSemana();
    
    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar-usuarios-saude',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            var usr_codigo = $("#usr_codigo").val();
            carregaEspecialidade(usr_codigo);
        }
    });
    
    if ($("#usr_codigo").val()) {
        carregaEspecialidade($("#usr_codigo").val(), $("#conv_codigo").val());
    }
    
    $.validator.addMethod("validaDias",function (validaDias, element){
        if (($("#1hr_inicio1").val() == "" && $("#1hr_fim1").val() == "") && ($("#2hr_inicio1").val() == "" && $("#2hr_fim1").val() == "") && ($("#3hr_inicio1").val() == "" && $("#3hr_fim1").val() == "") && ($("#4hr_inicio1").val() == "" && $("#4hr_fim1").val() == "") && ($("#5hr_inicio1").val() == "" && $("#5hr_fim1").val() == "") && ($("#6hr_inicio1").val() == "" && $("#6hr_fim1").val() == "") && ($("#7hr_inicio1").val() == "" && $("#7hr_fim1").val() == "")) {
            return false;
        } else {
            return true;
        }
    },"Informe o horário de atendimento!");
    
    $("#form-agendamento-profissionais").validate({
       rules: {
           usr_codigo: {required:true},
           qtd_hr: {validaDias:true}
       },
       messages: {
           usr_codigo: { required: "Selecione um profissional" }
       }
    });
    
    $("#proc_nome").buscar({
        url: baseUrl + '/procedimento/buscar',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });
    
    $("form:first").validate({
        rules: {
            proc_codigo: {
                min: 1
            }
        },
        messages: {
            proc_codigo: {
                min: "Infome um procedimento"
            }
        }
    });
    
    // Função utilizada pela Manutenção e Exceção em conjunto com o arquivo distribuição.js
//    $("#addPac").click(function() {
//        for (cont = 1; cont <= $(".ini").size(); cont++) {
//            if ($("#hr_inicio" + cont).val().length == 5 && $("#hr_fim" + cont).val().length == 5) {
//                msg = true;
//            } else {
//                msg = false;
//            }
//        }
//        if (msg) {
//            i = $(".ini").size() + 1;
//            if ($("#modal_valida").val() == "T") {
//                var func = "chamaCalculo()";
//            }
//            $("#hrs").append("<div class='horario'><label class='ui-corner-bl ui-corner-tr'>Hora de Início: </label>&nbsp;<input type=\"hidden\" name=\"grap_" + i + "\" id=\"grap_" + i + "\" value=\"\">" +
//                    "<input type='text' name='hr_inicio" + i + "' id='hr_inicio" + i + "' style='width:50px;' onkeypress='mask()' onchange='validaHora2()' class='ini ui-state-default'  maxlength='5' value='' class='focus' />" +
//                    "<label style='width:110px' class='ui-corner-bl ui-corner-tr'>Hora de Término:</label>&nbsp;" +
//                    "<input type='text' name='hr_fim" + i + "' id='hr_fim" + i + "' style='width:50px;'  class='fim ui-state-default' onchange='chamaCalculo()' value='' class='focus' /></div>");
//
//            $(".fim").mask('99:99');
//            $("#hr_inicio" + i).focus();
//            $("#qtd_hr").val(i);
//
//        } else {
//            $("body").append("<div id=\"dialog\" title=\" Erro\">Informe o Horário Inicial e de Término</div>");
//            $("#dialog").dialog({
//                modal: true,
//                width: 300,
//                height: 150,
//                close: function() {
//                    $(this).dialog("close");
//                },
//                buttons: {
//                    Ok: function() {
//                        $(this).dialog("close");
//
//                    }
//                }
//            });
//        }
//    });

    $("#addPac").click(function() {
        for (cont = 1; cont <= $(".ini").size(); cont++) {
            if ($("#hr_inicio" + cont).val().length == 5 && $("#hr_fim" + cont).val().length == 5) {
                msg = true;
            } else {
                msg = false;
            }
        }
        if (msg) {
            i = $(".ini").size() + 1;
            if ($("#modal_valida").val() == "T") {
                var func = "chamaCalculo()";
            }
            $("#hrs").append("<div class='horario'><label class='ui-corner-bl ui-corner-tr'>Hora de Início: </label>&nbsp;<input type=\"hidden\" name=\"grap_" + i + "\" id=\"grap_" + i + "\" value=\"\">" +
                    "<input type='text' name='hr_inicio" + i + "' id='hr_inicio" + i + "' style='width:50px;' onkeypress='mask()' onchange='validaHora2()' class='ini ui-state-default'  maxlength='5' value='' class='focus' />" +
                    "<label style='width:110px' class='ui-corner-bl ui-corner-tr'>Hora de Término:</label>&nbsp;" +
                    "<input type='text' name='hr_fim" + i + "' id='hr_fim" + i + "' style='width:50px;'  class='fim ui-state-default' onchange='chamaCalculo()' value='' class='focus' /></div>");

            $(".fim").mask('99:99');
            $("#hr_inicio" + i).focus();
            $("#qtd_hr").val(i);

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
    });

    
    
    /*if ($('#encaixe').is(':checked')) {
        mostraEncaixe();
    }*/

    

//    if ($("#prestador").val() == 'U') {
//        $("#form").validate({
//            rules: {
//                hr_inicio1: {
//                    required: true
//                },
//                hr_fim1: {
//                    required: true
//                },
//                coni_cota_dia: {
//                    required: true
//                },
//                coni_intervalo: {
//                    required: true
//                },
//                dias: {
//                    required: true
//                }
//            },
//            messages: {
//                hr_inicio1: {
//                    required: "(*)"
//                },
//                hr_fim1: {
//                    required: "(*)"
//                },
//                coni_cota_dia: {
//                    required: "(*)"
//                },
//                coni_intervalo: {
//                    required: "(*)"
//                },
//                dias : {
//                    required: "(*)"
//                }
//            }
//        });
//    }
    
    /*$(".ini").click(function() {
        $(".ini").val('');
    });*/
    
//    $("#coni_cota_dia").change(function() {
//        chamaCalculo();
//    });

    /*$(".ini").change(function() {
        chamaCalculo();
    });*/
//      
//    $("#coni_intervalo").change(function(){
//         if($("#coni_cota_dia").val() == ""){
//             minutos = horaEmMinutos();           
//             intervalo =  minutos / $("#coni_intervalo").val(); 
//             $("#coni_cota_dia").val(parseInt(intervalo));
//         }else{
//            calculatempo();
//         }         
//      });
});
 