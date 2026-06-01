$(document).ready(function () {
    //$("tr:odd").addClass("odd");

    setTimeout(function () {
        //Força a limpa o contador dos profissionais e usuario responsaveis
        if ($("#codFicha").val() == "") {
            $("#profs_part_qtd").val("");
            $("#usus_part_qtd").val("");
        }

        // Desabilita de acordo com  a atividade
        if ($("#codFicha").val() != "") {
            if ($("#atividade:checked").val() <= 3) {
                desabilitaPubPratEdicao();
            } else {
                desabilitaTemasEdicao();
            }
        }

        // Desativa PCNT, pois só pode habilitar quando a opção estiver checkada em Práticas/Temas
        // $("#part_fuma").attr("disabled",true);
        // $("#part_grupo").attr("disabled",true);
        // Carrega CNES para edição
        var usrCodigo = $("#prof_resp_codigo").val();
        if (usrCodigo) { carregaCnes(usrCodigo) }
    }, 150);

    $("#form-busca").validate({
        rules: {
            tipo_busca: {required: true},
            busca: {required: true},
        },
        messages: {
            tipo_busca: {required: "(*)Obrigatório"},
            busca: {required: "(*)Obrigatório"}
        }
    });

    // $("#ativ-coletiva").validate({
    //     rules: {
    //         uni_codigo:{required: true},
    //         dt_atividade: {required: true},
    //         hr_inicio: {required: true},
    //         hr_fim: {required: true},
    //         prof_resp_codigo: {required: true},
    //         conf_ativ: {required: true},
    //         conf_prof: {required: true},
    //         //conf_part: {required: true},
    //         num_particip: {required: true},
    //         conf_temas: {required: true},
    //         conf_pub: {required: true},
    //         conf_prat: {required: true},
    //         cod_cnes_uni: {required: true}
    //         //cod_equipe: {required: true}
    //     },
    //     messages: {
    //         uni_codigo: {required: "(*)Obrigatório"},
    //         dt_atividade: {required: "(*)Obrigatório"},
    //         hr_inicio: {required: "(*)Obrigatório"},
    //         hr_fim: {required: "(*)Obrigatório"},
    //         prof_resp_codigo: {required: "(*)Obrigatório"},
    //         conf_ativ: {required: "(*)Obrigatório"},
    //         conf_prof: {required: "(*)Obrigatório"},
    //         //conf_part: {required: "(*)Obrigatório"},num_particip
    //         conf_temas: {required: "(*)Obrigatório"},
    //         num_particip: {required: "(*)Obrigatório"},
    //         conf_pub: {required: "(*)Obrigatório"},
    //         conf_prat: {required: "(*)Obrigatório"},
    //         cod_cnes_uni: {required: "(*)Obrigatório"}
    //         //cod_equipe: {required: "(*)Obrigatório"}
    //     }
    // });


    $("#prof_resp_codigo").change(function(){
        $.ajax({
            url: baseUrl + "/default/unidade/carrega-cnes",
            type: "POST",
            data: {
                usr_codigo: $("#prof_resp_codigo").val()
            },
            success: function (txt) {

                var codCnesEdit = $("#cod_cnes_edit").val();
                if(txt.length > 1){
                    $("#cod_cnes_uni").append("<option value=''>Selecione</option>");
                    carregaIne(txt[0].uni_codigo, $("#prof_resp_codigo").val())
                }
                
                var checked = "";

                $.each(txt, function (key, value) {
                    checked = "";
                    if($("#cod_cnes_edit").val() == value['uni_cnes'] || txt.length == 1){
                        checked = "selected=selected";
                        carregaIne(value['uni_codigo'],$("#prof_resp_codigo").val());
                    }
                    $("#cod_cnes_uni").append("<option "+checked+" value=\""+validaCampoEmBranco(value['uni_cnes'])+"\" onclick='carregaIne("+value['uni_codigo']+","+$("#prof_resp_codigo").val()+")'>"+value['uni_desc']+"\</option>");
                })
            }
        })
    })
})

function abrirGrupos() {
    var recebeQuantidadePrevista = $("#num_participantes").val();

    var tpl =
        "<table class='grid ui-widget ui-widget-content ui-corner-all' width='100%'>" +
        "    <tr class='ui-widget-header'>" +
        "        <th width='55%'>Nome</th>" +
        "        <th width='15%'>Qtde. Part.</th>" +
        "        <th width='15%'>Status</th>" +
        "        <th width='15%'>Opções</th>" +
        "    </tr>";
    $.ajax({
        url: baseUrl + "/programasfederais/grupo-atividade-coletiva/listar-grupos-ativos",
        type: "GET",
        success: function (ret) {

            if (ret.length > 0) {
                ret.forEach(function (dados) {
                    tpl += "<tr onclick='fechaModal("+dados.gac_codigo+','+dados.qtd_part+")'>" +
                        "   <td class='ui-state-default'>"+dados.gac_descricao+"</td>" +
                        "   <td class='ui-state-default' align='center'>"+dados.qtd_part+"</td>" +
                        "   <td class='ui-state-default' align='center'>"+(dados.gac_status ? "Ativo" : "Inativo")+"</td>" +
                        "   <td class='ui-state-default c' width='80'>" +
                        "       <a style='cursor: pointer;' class='editar'>" +
                        "           <img src="+baseUrl+'/public/images/icons/selecionar.png'+" alt='Selecionar Grupo' title='Selecionar Grupo' />" +
                        "       </a>" +
                        "   </td>" +
                        "</tr>";
                });
            } else {
                tpl += "<td colspan='6'>Nenhum item encontrado</td>";
            }
            tpl += "</table>";

            $("body").append("<div id='gac_modal' title='Seleção de grupo' ></div>");
            $("#gac_modal")
            .html(tpl)
            .dialog({
                modal: true,
                height: 500,
                width: "75%",
                title: "Grupos de Atividade Coletiva",
                resizable: false,
                close: function () {

                    if (recebeQuantidadePrevista < $(this).data('qtd_part')) {
                        alert("Grupo maior que a quantidade prevista !")
                        return false;
                    }
                    selecionaGrupoAtividadeColetiva($(this).data('gac_codigo'));
                }
            });
        }
    });
}

function fechaModal(gac_codigo, qtd_part) {
    $("#gac_modal").data('gac_codigo', gac_codigo);
    $("#gac_modal").data('qtd_part', qtd_part);
    $("#gac_modal").dialog('close');
}

function selecionaGrupoAtividadeColetiva(gac_codigo) {
    $("#usus_part").empty(" ");
    $("#num_particip").val(0);
    $.ajax({
        url: baseUrl + "/programasfederais/grupo-atividade-coletiva/listar-participantes-por-grupo?gac_codigo=" + gac_codigo,
        type: "GET",
        success: function (ret) {
            if (ret.length > 0) {
                // var cont = parseInt($("#usus_part_qtd").val());
                // console.log(cont);
                var cont = 0 ;
                $("#conf_part").val(cont);
                var part_duplicado = 0;

                ret.forEach(function (dados) {
                    cont++;
                    var fumaEsc = (dados.gap_cessou_habito_fumar == 1) ? "SIM" : "NÃO";
                    var avalEsc = (dados.gap_avaliacao_alterada == 1) ? "SIM" : "NÃO";
                    var class_aval;
                    if (dados.gap_avaliacao_alterada == 1) {
                        class_aval = "aval";
                    }
                    
                    var grupoEsc = (dados.gap_abandonou_grupo == 1) ? "SIM" : "NÃO";

                    if (validaConfirmacaoPart(dados.usu_codigo) != 1) {

                        $("#usus_part").show();
                        $("#usus_part_qtd0").remove();
                        $("#usus_part").append(
                            '<tr id="usu_part_qtd'+cont+'" class=\"participantes '+class_aval+'\" >' +
                            "   <td>"+dados.usu_nome+
                            "       <input type='hidden' name='usus_part["+cont+"][usu_codigo]' value=\""+dados.usu_codigo+"\" />"+
                            "   </td>" +
                            "   <td align='center'>"+formataData(dados.gap_dt_nascimento)+
                            "       <input type='hidden' name='usus_part["+cont+"][dt_nascimento]' value=\""+dados.gap_dt_nascimento+"\"/>" +
                            "   </td>" +
                            "   <td align='center'>"+avalEsc+
                            "       <input type='hidden' name='usus_part["+cont+"][st_avaliacao_alterada]' value=\""+parseInt(dados.gap_avaliacao_alterada)+"\" />" +
                            "   </td>" +
                            "   <td align='center'>"+number_format(dados.gap_peso, 3, ",", ".") +
                            "       <input type='hidden' name='usus_part["+cont+"][nu_peso]' value=\""+parseInt(dados.gap_peso)+"\" />" +
                            "   </td>" +
                            "   <td align='center'>"+dados.gap_altura+
                            "       <input type='hidden' name='usus_part["+cont+"][nu_altura]' value=\""+dados.gap_altura+"\"/>"+
                            "   </td>" +
                            "   <td align='center'>"+fumaEsc+
                            "       <input type='hidden' name='usus_part["+cont+"][st_cessou_habito_fumar]' value=\""+parseInt(dados.gap_cessou_habito_fumar)+"\" />" +
                            "   </td>" +
                            "   <td align='center'>"+grupoEsc+
                            "       <input type='hidden' name='usus_part["+cont+"][st_abandonou_grupo]' value=\""+parseInt(dados.gap_abandonou_grupo)+ "\"/>" +
                            "   </td>" +
                            "   <td align='center'>" +
                            "       <a class='excluir'>" +
                            '           <img src="'+baseUrl+'/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="excluirConfirmacaoPart('+cont+');" />' +
                            "       </a>" +
                            "   </td>" +
                            "</tr>");
                        validaQtdParticipantes();
                        $("#usus_part_qtd").val(cont);
                        var num_part = $("#num_particip").val();
                        $("#num_particip").val((parseInt(num_part) + 1));
                    } else {
                        part_duplicado++;
                    }

                    $("#confirm_paciente").hide();
                    // $("#num_aval").val($(".aval").size());

                    habilitaNovaConfirmacaoPart();
                });
                if (part_duplicado > 0) {
                    mensagem("Atenção", "Não foram adicionados os participantes do grupo que já estão incluídos!")
                }
            }
        }
    });
}


function abrirCadastroGrupos() {
    location.href = baseUrl + "/programasfederais/grupo-atividade-coletiva";
}

function validaHoraInicial(){
    var hora = $('#hr_inicio').val();
    var validaHora = new RegExp(/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/gi);
    if(!hora.match(validaHora)) {
        mensagem("Erro", "Horário inicial inválido", 250, 150);
        $('#hr_inicio').val("");
    }
}


function validaHoraFinal(){
    var hora = $('#hr_fim').val();
    var hora_ini = $('#hr_inicio').val();
    if( hora_ini.replace(":", "") > hora.replace(":", "")){
         mensagem("Erro", "Horário final inválido", 250, 150,function(){
             $('#hr_fim').focus();
         });
        $('#hr_fim').val("");

    }

    var validaHora = new RegExp(/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/gi);
    if(!hora.match(validaHora)) {
        mensagem("Erro", "Horário final inválido", 250, 150);
        $('#hr_fim').val("");
    }

}

function validaTemas() {
    var cont = 0;
    $("#tab-temas").find("input[type=checkbox][name='temas[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_temas").val(""); } else { $("#conf_temas").val(cont); }
}

function validaPublicoAlvo() {
    var cont = 0;
    $("#tab-pub-alvo").find("input[type=checkbox][name='pubAlvo[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_pub").val(""); } else { $("#conf_pub").val(cont); }
}

function validaPraticas() {
    var cont = 0;
    $("#tab-prat").find("input[type=checkbox][name='praticas[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_prat").val(""); } else { $("#conf_prat").val(cont); }
}

function buscaProfissionais() {
    $("#profs_part_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-profissionais-equipes',
            template : function(ul, item) {
                     return $("<li/>").data("item.autocomplete", item).append(
                             "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(event, ui){
                carregaEspecialidade(ui.item.id);
               // getTipoMedico();
               return true;
            }
    });

}

function buscaProfissionaisGeral() {
    $("#profs_part_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-profissionais-equipes',
            template : function(ul, item) {
                     return $("<li/>").data("item.autocomplete", item).append(
                             "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(event, ui){
                carregaEspecialidadeGeral(ui.item.id);
               // getTipoMedico();
               return true;
            }
    });

}


function buscaProfissionaisNovo() {
    $("#profs_part_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-profissionais-equipes',
            template : function(ul, item) {
                     return $("<li/>").data("item.autocomplete", item).append(
                             "<a>" + item.label + "</a>").appendTo(ul);
            }
    });

}

function carregaEspecialidade(usrCodigo) {
    $("#profs_part_esp option").remove();
    $("#td_profs_part_esp").show();
    $("#td_profs_part_conf").show();

    uniCodigo = $("select#uniCodigo option:selected").val()
    $.ajax({
        url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional",
        type: "POST",
        data: {
            usrCodigo: usrCodigo,
            uniCodigo: uniCodigo
        },
        success: function (txt) {
            $.each(txt, function (key, value) {
                $("#profs_part_esp").append("<option title=\"" + value['esp_nome'] + "\" value=\"" + value['cod_cbo'] + "\">" + value['esp_nome'] + "</option>");
            })
        }
    });
}

function carregaEspecialidadeGeral(usrCodigo) {
    $("#profs_part_esp option").remove();
    $("#td_profs_part_esp").show();
    $("#td_profs_part_conf").show();

    $.ajax({
        url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional-geral",
        type: "POST",
        data: {
            usrCodigo: usrCodigo
        },
        success: function (txt) {
            $.each(txt, function (key, value) {
                $("#profs_part_esp").append("<option title=\"" + value['esp_nome'] + "\" value=\"" + value['cod_cbo'] + "\">" + value['esp_nome'] + "</option>");
            })
        }
    });
}

function confirmarProfsPart() {
    var cont = new Number($("#conf_prof").val()) + 1;
    var usrCodigo = $("#usr_codigo").val();
    var usrNome = $("#profs_part_nome").val();
    var codCbo = $("#profs_part_esp option:selected").val();
    $("#conf_prof").val(cont);
    if (validaConfirmacaoResp(usrCodigo) == 0) {
        $("#profs_part").show();
        // Cria um contador pro array de campos, através de um hidden
        $("#profs_part_qtd").val(cont);
        $("#profs_part_qtd0").remove();
        $("#profs_part").append(
                '<tr id="profs_part_qtd' + cont + '">' +
                "<td>" + usrNome +
                "<input type='hidden' name='profs_part[" + cont + "][usr_codigo]' value=\"" + usrCodigo + "\" />" +
                "</td>" +
                "<td>" + codCbo +
                "<input type='hidden' name='profs_part[" + cont + "][cbo]' value=\"" + codCbo + "\" />" +
                "</td>" +
                "<td>" +
                "<a href='#' class='excluir'>" +
                '<img src="' + baseUrl + '/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="excluirConfirmacaoResp(' + cont + ')" />' +
                "</a>" +
                "</td>" +
                "</tr>");

        $("#prof_resp_codigo").append("<option value="+usrCodigo+" onclick=carregaCnes("+usrCodigo+")>"+usrNome+"</option>");
        habilitaNovaConfirmacao();
    } else {
        mensagem("Erro", "Responsável já cadastrado", 250, 150);
    }
}

function habilitaNovaConfirmacao() {
    setTimeout(function () {
        $("#profs_part_esp option").remove();
        $("#td_profs_part_esp").hide();
        $("#td_profs_part_conf").hide();
        $("#profs_part_nome").val("");
        $("#profs_part_nome").focus();
    }, 150);
}

function validaConfirmacaoResp(term) {
    var cont = new Number($("#profs_part_qtd").val()) + 1;
    var table = $('#profs_part');
    var retorno = "";
    if (cont > 1) {
        table.find('tr').each(function (indice) {
            $(this).find('td input[type="hidden"]').each(function (indice) {
                if (term == $(this).val()) {
                    retorno = 1;
                }
            });
        });
    }
    return retorno;
}

function excluirConfirmacaoResp(id) {
    var cont = new Number($("#conf_prof").val()) - 1;
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#profs_part_qtd" + id).remove();
        if(cont==0) { cont = ""; }
        $("#conf_prof").val(cont);
    });
}

function validaQtdParticipantes(){
    var qtdParticipantes = parseInt($(".participantes").size());
    if (qtdParticipantes == 0) {
        // Validação Atividade, se for as 3 primeiras retirar obrigatoriedade de Participantes
        if ($("#atividade:checked").val()== 1 || $("#atividade:checked").val()== 2 || $("#atividade:checked").val()== 3) {
            $("#conf_part").val("1");
        } else {
            $("#conf_part").val("");
        }
    } else {

        $("#conf_part").val(parseInt(qtdParticipantes));
    }
}

function confirmaParticipante() {
    var recebeQuantidadePrevista = $("#num_participantes").val();
    var cont = new Number($(".participantes").size()) + 1;
    var usuNome = $("#part_nome").val();
    var usuCodigo = $("#part_codigo").val();
    var dtNasc = $("#part_dtnasc").val();
    var peso = $("#part_peso").val();
    var altura = $("#part_altura").val();
    // var aval = $("#part_aval").val();
    var cessouHabitoDeFumar;
    var abandonouGrupo;

    if ($("#part_fuma").is(":checked") ) {
        cessouHabitoDeFumar = "SIM";
    } else{
        cessouHabitoDeFumar = "NÃO";
    }

    if ($("#part_grupo").is(":checked") ) {
        abandonouGrupo = "SIM";
    } else{
        abandonouGrupo = "NÃO";
    }

    if(!usuCodigo){
        return false;
    }

    if ($("#part_aval").is(':checked')) {
        var avalEsc = "SIM";
        var class_aval = "aval";
    } else {
        var avalEsc = "NÃO";
        aval = "0";
    }
    if (validaConfirmacaoPart(usuCodigo) == 0) {
        $("#usus_part").show();
        $("#usus_part_qtd0").remove();
        $("#usus_part_qtd").val(cont);
        $("#usus_part").append(
                '<tr id="usu_part_qtd' + cont + '" class=\"participantes '+class_aval+'\" >' +
                "<td>" + usuNome +
                "<input type='hidden' name='usus_part[" + cont + "][usu_codigo]' value=\"" + usuCodigo + "\" />" +
                "</td>" +
                "<td>" + dtNasc +
                "<input type='hidden' name='usus_part[" + cont + "][dt_nascimento]' value=\"" + dtNasc + "\" />" +
                "</td>" +
                "<td>" + avalEsc +
                "<input type='hidden' name='usus_part[" + cont + "][st_avaliacao_alterada]' value=\"" + avalEsc + "\" />" +
                "</td>" +
                "<td>" + peso +
                "<input type='hidden' name='usus_part[" + cont + "][nu_peso]' value=\"" + peso + "\" />" +
                "</td>" +
                "<td>" + altura +
                "<input type='hidden' name='usus_part[" + cont + "][nu_altura]' value=\"" + altura + "\" />" +
                "</td>" +
                "<td>" + cessouHabitoDeFumar +
                "<input type='hidden' name='usus_part[" + cont + "][st_cessou_habito_fumar]' value=\"" + cessouHabitoDeFumar + "\" />" +
                "</td>" +
                "<td>" + abandonouGrupo +
                "<input type='hidden' name='usus_part[" + cont + "][st_abandonou_grupo]' value=\"" + abandonouGrupo + "\" />" +
                "</td>" +
                "<td>" +
                "<a href='#' class='excluir'>" +
                '<img src="' + baseUrl + '/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="excluirConfirmacaoPart(' + cont + ');" />' +
                "</a>" +
                "</td>" +
                "</tr>");
        validaQtdParticipantes();
    } else {
        mensagem("Erro","Participante já cadastrado", 250, 150);
    }
    $('html, body').animate({scrollTop: $('#usu_part_qtd' + cont).offset().top}, 'slow');

    $("#confirm_paciente").hide();
    $("#num_particip").val( (parseInt($("#num_particip").val()) + parseInt(1)));
    // $("#num_aval").val( $(".aval").size());

    var recebeQuantidadePresente = $("#num_particip").val();

    if (parseInt(recebeQuantidadePresente) > parseInt(recebeQuantidadePrevista)){
        $("#teste").hide();
        mensagem("Erro", "Número de participantes não pode ser maior que o limite", 250, 150);
    }

    contabilizarAvaliacoesAlteradas();
    habilitaNovaConfirmacaoPart();
}

function contabilizarAvaliacoesAlteradas() {
    if ($("#part_aval").prop('checked')) {
        $("#num_aval").val( (parseInt($("#num_aval").val()) + parseInt(1)));
    }
}

function validaConfirmacaoPart(term) {
    var cont = new Number($("#usus_part_qtd").val()) + 1;
    var table = $('#usus_part');
    var retorno = "";
    if (cont > 1) {
        table.find('tr').each(function (indice) {
            $(this).find('td input[type="hidden"]').each(function (indice) {
                if (term == $(this).val()) {
                    retorno = 1;
                }
            });
        });
    }
    return retorno;
}

function excluirConfirmacaoPart(id) {
    //var cont = new Number($("#conf_part").val()) - 1;

    $('html, body').animate({scrollTop: $('#usu_part_qtd' + id).offset().top}, 'slow');
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#usu_part_qtd"+id).remove();
        var recebeQuantidadePresente = $("#num_particip").val() -1;
        validaQuantidadeDeParticipantesPresentes(recebeQuantidadePresente)
    });

}

function validaQuantidadeDeParticipantesPresentes(recebeQuantidadePresente) {
    var recebeQuantidadePrevista = $("#num_participantes").val();
    var recebeQuantidadePresente = (($("#num_particip").val()) - 1);
    $("#num_particip").val(recebeQuantidadePresente);
    if (recebeQuantidadePresente <= recebeQuantidadePrevista){
        $("#teste").show();
    }
}

function buscaParticipante() {
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
    var ativCol = $("#ativCol").val();

    
    $("#"+idNome).buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl+'/paciente/buscar',
        callback: function(event, ui){
            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var dtNasc = ui.item.data.usu_datanasc;
            var usuNasc = ui.item.data.cd_nacionalidade;
            var usuRaca = ui.item.data.rac_codigo;
            var usuDom = ui.item.data.dom_codigo;
            
            var usuPeso = ui.item.data.ate_peso;
            var usuAltura = ui.item.data.ate_altura;

            if ($("#usuPeso").length) {
                $("#part_peso").val(usuPeso);
            }

            if ($("#usuAltura").length) {
                $("#part_altura").val(usuAltura);
            }

            if ((cns!="" && cns!=null && cns!="undefined") && (validaNacionalidade(usuNasc)=="true") && (validaRaca(usuRaca)=="true") && (validaCnsDigitado(cns)=="true") && (validaEspacoNome(nome)=="true") && (validaEspacoNomeMae(nomeMae)=="true")){
                    if (idNome!="" && idNome!="null" && idNome!="undefined") {
                        $("#"+idNome).val(nome);    
                    }
                    if (idCodigo!="" && idCodigo!="null" && idCodigo!="undefined") {
                        $("#"+idCodigo).val(usuCodigo);
                    }
                    if (idData!="" && idData!="null" && idData!="undefined") {
                        $("#"+idData).val(dtNasc);
                    }
                    if (idButton!="" && idButton!="null" && idButton!="undefined") {
                        $("#"+idButton).show();
                    }
                    // A - Agendamento
                    if (tipo=='A') {
                        carregarHistoricoDoPaciente();
                    }
            } else {
                atualizaCnsParticipante(usuCodigo,idNome,idData,ativCol);
            }
        }

    });
}

function habilitaNovaConfirmacaoPart() {
    setTimeout(function () {
        $("#part_codigo").val("");
        $("#part_nome").val("");
        $("#part_dtnasc").val("");
        $("#part_peso").val("");
        $("#part_altura").val("");
        $("#part_aval").attr("checked", false);
        // $("#part_fuma").attr("checked", false);
        // $("#part_grupo").attr("checked", false);
        $("#part_nome").focus();
    }, 150);
}


function carregaCnes(usrCodigo){
    setTimeout(function () {
        $("#cod_cnes_uni option").remove();
        $("#cod_cnes_uni").removeAttr("disabled");
        $.ajax({
            url: baseUrl + "/default/unidade/carrega-cnes",
            type: "POST",
            data: {
                usr_codigo: usrCodigo
            },
            success: function (txt) {

                var checked = "";

                $.each(txt, function (key, value) {
                    checked = "";
                    if($("#cod_cnes_edit").val() == value['uni_cnes'] || txt.length == 1){
                        checked = "selected=selected";
                        carregaIne(value['uni_codigo'],usrCodigo);
                    }
                    $("#cod_cnes_uni").append("<option "+checked+" value=\""+validaCampoEmBranco(value['uni_cnes'])+"\" onclick='carregaIne("+value['uni_codigo']+","+usrCodigo+")'>"+value['uni_desc']+"\</option>");
                })

            }
        });
    }, 500);

}

function selecioneUnidade() {
    mensagem("Aviso","Selecione o a unidade correspondente",250,150);
    $("#cod_cnes_uni").focus();
}

function carregaIne(uniCodigo,usrCodigo){
    var uniCodigo = $("#uniCodigo").val()
    var usrCodigo = usrCodigo;
    setTimeout(function () {
        $("#cod_equipe option").remove();
        $("#cod_equipe").removeAttr("disabled");
        $.ajax({
            url: baseUrl + "/default/usuarios/carrega-equipes",
            type: "POST",
            data: {
                uniCodigo: uniCodigo,
                usrCodigo: usrCodigo
            },
            success: function (txt) {
                var recebeCodigoIne = txt;

                if (recebeCodigoIne == null){
                    $('#cod_equipe').append(`<option>Sem Cód INE</option>`)
                }
                
                $('#cod_equipe').append(`<option>${recebeCodigoIne}</option>`)
            }
        });
    }, 300);
}


function validaCampoEmBranco(texto){
    if (texto == "" || texto == null || texto == "null" || texto == "undefined"){
        return "";
    } else {
        return texto;
    }
}

function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;
    if((tecla>47 && tecla<58)) return true;
    else{
    	if (tecla==8 || tecla==0) return true;
	else  return false;
    }
}

function validaIne(){
    if($("#cod_equipe").val().length != 10) {
        mensagem("Erro","INE inválido!",250,150);
        $("#cod_equipe option[value='']").attr("selected","selected");
    }
}

function validaAltura(){
    if ($("#part_altura").val() <= 0.20){
        mensagem("Erro","Altura mínima 20 cm",250,150);
        $("#part_altura").val("");
        $("#part_altura").focus();
    }
}

function formataPeso(){
    $('#part_peso').priceFormat({
        prefix: '',
        centsSeparator: '.',
        centsLimit: 3,
        thousandsSeparator: '.'
    });
}

function formataAltura(){
    jQuery('#part_altura').priceFormat({
        prefix: '',
        centsSeparator: '.',
        centsLimit: 2,
        thousandsSeparator: '.'
    }); 
}

function validaPeso() {
    if ($("#part_peso").val() <= 0.5) {
        mensagem("Erro","Peso mínima 0.500 km",250,150);
        $("#part_peso").val("");
        $("#part_peso").focus();
    }
}

// function desabilitaPnct() {
//     var pcnt25 = $("#praticas225").is(":checked");
//     var pcnt26 = $("#praticas226").is(":checked");
//     var pcnt27 = $("#praticas227").is(":checked");
//     var pcnt28 = $("#praticas228").is(":checked");

//     if (pcnt25==true || pcnt26==true || pcnt27==true || pcnt28==true) {
//         $("#part_fuma").removeAttr("disabled");
//         $("#part_grupo").removeAttr("disabled");
//     } else {
//         $("#part_fuma").attr("disabled",true);
//         $("#part_grupo").attr("disabled",true);
//     }

//     var contPratica = "";
//     $("#tab-prat").each(function (indice) {
//         $(this).find('td input[type="checkbox"]').each(function (indice) {
//             if($("#praticas"+$(this).val()+":checked").val() > 0) { contPratica++; };
//             $("#praticas"+$(this).val()).removeAttr("disabled");
//         });
//     });

//     $("#tab-prat2").each(function (indice) {
//         $(this).find('td input[type="checkbox"]').each(function (indice) {
//             if($("#praticas2"+$(this).val()+":checked").val() > 0) { contPratica++; };
//             $("#praticas2"+$(this).val()).removeAttr("disabled");
//         });
//     });
//     $("#conf_prat").val(contPratica);

// }

function desabilitaPubPratEdicao() {
    $("#conf_prat").val("1");
    $("#conf_pub").val("1");

    $("#tab-pub-alvo").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#pubAlvo"+$(this).val()).attr("disabled",true);
            $("#pubAlvo"+$(this).val()).attr("checked",false);
        });
    });

    $("#tab-prat").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#praticas"+$(this).val()).attr("disabled",true);
            $("#praticas"+$(this).val()).attr("checked",false);
        });
    });

    $("#tab-prat2").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#praticas2"+$(this).val()).attr("disabled",true);
            $("#praticas2"+$(this).val()).attr("checked",false);
        });
    });
}




function desabilitaPubPrat() {
    //$("#conf_part").val("1");
    validaQtdParticipantes();
    $("#conf_prat").val("1");
    $("#conf_pub").val("1");
    $("#conf_ativ").val("1");
    var cont = "";
    $("#tab-temas").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            if($("#temas"+$(this).val()+":checked").val()>0) { cont++; };
            $("#temas"+$(this).val()).removeAttr("disabled");
        });
    });
    $("#conf_temas").val(cont);


    $("#tab-pub-alvo").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#pubAlvo"+$(this).val()).attr("disabled",true);
            $("#pubAlvo"+$(this).val()).attr("checked",false);
        });
    });

    $("#tab-prat").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#praticas"+$(this).val()).attr("disabled",true);
            $("#praticas"+$(this).val()).attr("checked",false);
        });
    });

    $("#tab-prat2").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#praticas2"+$(this).val()).attr("disabled",true);
            $("#praticas2"+$(this).val()).attr("checked",false);
        });
    });
}

function desabilitaTemasEdicao() {
    $("#conf_temas").val("1");
    $("#tab-temas").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#temas"+$(this).val()).attr("disabled",true);
            $("#temas"+$(this).val()).attr("checked",false);
            $("#temas"+$(this).val()).attr("checked",false);
        });
    });
}

function desabilitaTemas() {
    validaQtdParticipantes()
    $("#conf_temas").val("1");
    $("#conf_ativ").val("1");
    $("#tab-temas").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            $("#temas"+$(this).val()).attr("disabled",true);
            $("#temas"+$(this).val()).attr("checked",false);
        });
    });

    var contPub = "";
    $("#tab-pub-alvo").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            if($("#pubAlvo"+$(this).val()+":checked").val() > 0) { contPub++; };
            $("#pubAlvo"+$(this).val()).removeAttr("disabled");
        });
    });
    $("#conf_pub").val(contPub);

    var contPratica = "";
    $("#tab-prat").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            if($("#praticas"+$(this).val()+":checked").val() > 0) { contPratica++; };
            $("#praticas"+$(this).val()).removeAttr("disabled");
        });
    });

    $("#tab-prat2").each(function (indice) {
        $(this).find('td input[type="checkbox"]').each(function (indice) {
            if($("#praticas2"+$(this).val()+":checked").val() > 0) { contPratica++; };
            $("#praticas2"+$(this).val()).removeAttr("disabled");
        });
    });
    $("#conf_prat").val(contPratica);

}

function validaData(e){
    if($(e).val().length > 0){
       if(VerificaData(e)){
            return true;
        }else{
            $("#dt_atividade").val("");
             setTimeout(function() { $('#dt_atividade').focus() }, 500);$("#dt_atividade").focus();
        }
    }
}

function verificaProgramaEscola(){
    var educacao = $("#saude-escola-educacao").is(':checked');
    var saude = $("#saude-escola-saude").is(':checked');

    $("#uni_codigo").val("");
    $("#num_inep").val("");
    $("#ds_local").val("");

    if ($("#temasaude18").is(':checked') && !saude) {
      $("#saude-escola-educacao").attr('checked', true);
    }

    if(educacao || saude){
        $("#uni_codigo").attr("disabled", "disabled");
        $("#ds_local").attr("disabled", "disabled");
        $("#num_inep").removeAttr("disabled");

        if (educacao && !saude) {
            $("#atividade1").attr("disabled", "disabled");
            $("#atividade2").attr("disabled", "disabled");
            $("#atividade3").attr("disabled", "disabled");
            $("#atividade5").attr("disabled", "disabled");
            $("#temas1").attr("disabled", "disabled");
            $("#temas2").attr("disabled", "disabled");
            $("#temas3").attr("disabled", "disabled");
            $("#temas4").attr("disabled", "disabled");
            $("#temas5").attr("disabled", "disabled");
            $("#temas6").attr("disabled", "disabled");
            $("#temas7").attr("disabled", "disabled");
            $("#praticas2").attr("disabled", "disabled");
            $("#praticas9").attr("disabled", "disabled");
            $("#praticas25").attr("disabled", "disabled");
            $("#praticas26").attr("disabled", "disabled");
            $("#praticas27").attr("disabled", "disabled");
            $("#praticas28").attr("disabled", "disabled");
            $("#praticas24").attr("disabled", "disabled");
            $("#outro_procedimento_coletivo").attr("disabled", "disabled");

        } else {
            $("#atividade1").removeAttr("disabled");
            $("#atividade2").removeAttr("disabled");
            $("#atividade3").removeAttr("disabled");
            $("#atividade5").removeAttr("disabled");
            $("#temas1").removeAttr("disabled");
            $("#temas2").removeAttr("disabled");
            $("#temas3").removeAttr("disabled");
            $("#temas4").removeAttr("disabled");
            $("#temas5").removeAttr("disabled");
            $("#temas6").removeAttr("disabled");
            $("#temas7").removeAttr("disabled");
            $("#praticas2").removeAttr("disabled");
            $("#praticas9").removeAttr("disabled");
            $("#praticas25").removeAttr("disabled");
            $("#praticas26").removeAttr("disabled");
            $("#praticas27").removeAttr("disabled");
            $("#praticas28").removeAttr("disabled");
            $("#praticas24").removeAttr("disabled");
            $("#outro_procedimento_coletivo").removeAttr("disabled");
        }
    }
    else{
        $("#uni_codigo").removeAttr("disabled");
        $("#num_inep").attr("required", false);
        $("#num_inep").removeAttr("disabled");
        $("#ds_local").removeAttr("disabled");
        $("#atividade1").removeAttr("disabled");
        $("#atividade2").removeAttr("disabled");
        $("#atividade3").removeAttr("disabled");
        $("#atividade5").removeAttr("disabled");
        $("#temas1").removeAttr("disabled");
        $("#temas2").removeAttr("disabled");
        $("#temas3").removeAttr("disabled");
        $("#temas4").removeAttr("disabled");
        $("#temas5").removeAttr("disabled");
        $("#temas6").removeAttr("disabled");
        $("#temas7").removeAttr("disabled");
        $("#praticas2").removeAttr("disabled");
        $("#praticas9").removeAttr("disabled");
        $("#praticas25").removeAttr("disabled");
        $("#praticas26").removeAttr("disabled");
        $("#praticas27").removeAttr("disabled");
        $("#praticas28").removeAttr("disabled");
        $("#praticas24").removeAttr("disabled");
        $("#outro_procedimento_coletivo").removeAttr("disabled");
    }

}
// ---------------------
function validacaoPseEducacao(){
    $("#fundoTemasSaude").css("background-color", "#f45959");
    $("#fundoTemasSaude").css("-webkit-transition" ," background-color 1000ms linear");
    $("#fundoTemasSaude").css("-ms-transition" ," background-color 1000ms linear");
    $("#fundoTemasSaude").css("transition" ," background-color 1000ms linear");

    $("#atividades1").prop('disabled', true);
    $("#atividades2").prop('disabled', true);
    $("#atividades3").prop('disabled', true);
    $("#atividades5").prop('disabled', true);

    $("#temasParaReuniao1").prop('disabled', true);
    $("#temasParaReuniao1").prop('checked', false);
    $("#temasParaReuniao2").prop('disabled', true);
    $("#temasParaReuniao2").prop('checked', false);
    $("#temasParaReuniao3").prop('disabled', true);
    $("#temasParaReuniao3").prop('checked', false);
    $("#temasParaReuniao4").prop('disabled', true);
    $("#temasParaReuniao4").prop('checked', false);
    $("#temasParaReuniao5").prop('disabled', true);
    $("#temasParaReuniao5").prop('checked', false);
    $("#temasParaReuniao6").prop('disabled', true);
    $("#temasParaReuniao6").prop('checked', false);
    $("#temasParaReuniao7").prop('disabled', true);
    $("#temasParaReuniao7").prop('checked', false);

    $("#praticas2").prop('disabled', true);
    $("#praticas9").prop('disabled', true);
    $("#praticas25").prop('disabled', true);
    $("#praticas26").prop('disabled', true);
    $("#praticas27").prop('disabled', true);
    $("#praticas28").prop('disabled', true);
    $("#praticas24").prop('disabled', true);

    $("#ds_local").val(" ");
    $("#ds_local").prop('disabled', true);
}

function educacaoSaudeObrigatorio() {
    if ( $("#temasParaSaude18").prop('checked')){
        var body = $("html, body");
        body.stop().animate({scrollTop:0}, 500, 'swing', function() { 
           $("#programasSaude").css('background-color', '#FFE1E1')
        });
    }
}

function limparRadios () {
    $(".radiosAtividadeColetiva").prop({
        disabled: false,
        checked: false,
    })
    $(".numeroDoInep").css('background-color', '#DAEBF9');
    $("#educacaoEscola").prop('checked', false)
    $("#saudeEscola").prop('checked', false)
    $("#fundoAtividades").css('background-color', '#eff4f8');
    $("#fundoTemas").css('background-color', '#f7eff8');
    $("#fundoPublico").css('background-color', '#eff8f8');
    $("#fundoTemasSaude").css('background-color', '#f8f4ef');
    $("#fundoPraticas").css('background-color', '#f8f3ef');
    $("#fundoOutros").css('background-color', '#eff8f1');

    $("#ds_local").prop('disabled', false);
}

function validacaoPseSaude(){

    // $(".temasParaSaude").css("background-color", "#FFE1E1");
    $(".numeroDoInep").css("background-color", "#FFE1E1");

    $("#fundoTemasSaude").css("background-color", "#f45959");
    $("#fundoTemasSaude").css("-webkit-transition" ," background-color 1000ms linear");
    $("#fundoTemasSaude").css("-ms-transition" ," background-color 1000ms linear");
    $("#fundoTemasSaude").css("transition" ," background-color 1000ms linear");

    $("#atividades1").prop('disabled', true);
    $("#atividades2").prop('disabled', true);
    $("#atividades3").prop('disabled', true);
    $("#atividades5").prop('disabled', true);

    $("#temasParaReuniao1").prop('disabled', false);
    $("#temasParaReuniao2").prop('disabled', false);
    $("#temasParaReuniao3").prop('disabled', false);
    $("#temasParaReuniao4").prop('disabled', false);
    $("#temasParaReuniao5").prop('disabled', false);
    $("#temasParaReuniao6").prop('disabled', false);
    $("#temasParaReuniao7").prop('disabled', false);

    $("#praticas2").prop('disabled', false);
    $("#praticas9").prop('disabled', false);
    $("#praticas25").prop('disabled', false);
    $("#praticas26").prop('disabled', false);
    $("#praticas27").prop('disabled', false);
    $("#praticas28").prop('disabled', false);
    $("#praticas24").prop('disabled', false);

    if ( $("#atividades6").prop("checked") ) {
        $("#praticas2").prop('disabled', false);
        $("#praticas9").prop('disabled', false);
    } else{
        $("#praticas2").prop('disabled', true);
        $("#praticas9").prop('disabled', true);
    }

    $("#tableDoProfissional").show();
    $("#divDoResponsavel").show();
    
    
    $("#temasParaSaude18").prop('checked', true);
    
    $("#ds_local").val(" ");
    $("#ds_local").prop('disabled', true);
}

function desabilitaTemasReuniao () {
      // $("#saude-escola-educacao").attr('checked', true);

    if ( $("#atividades4").prop("checked") || $("#atividades5").prop("checked") || 
        $("#atividades6").prop("checked") ||$("#atividades7").prop("checked") ) {
            $("#temasParaReuniao1").prop('disabled', true);
            $("#temasParaReuniao1").prop('checked', false);
            $("#temasParaReuniao2").prop('disabled', true);
            $("#temasParaReuniao2").prop('checked', false);
            $("#temasParaReuniao3").prop('disabled', true);
            $("#temasParaReuniao3").prop('checked', false);
            $("#temasParaReuniao4").prop('disabled', true);
            $("#temasParaReuniao4").prop('checked', false);
            $("#temasParaReuniao5").prop('disabled', true);
            $("#temasParaReuniao5").prop('checked', false);
            $("#temasParaReuniao6").prop('disabled', true);
            $("#temasParaReuniao6").prop('checked', false);
            $("#temasParaReuniao7").prop('disabled', true);
            $("#temasParaReuniao7").prop('checked', false);
        
        $("#fundoTemas").css("background-color", "#f7eff8");
        $("#fundoTemas").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoTemas").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoTemas").css("transition" ," background-color 1000ms linear");
        // $("#temasParaReuniao1").prop('checked', false);

    } else{
        // $("#fundoTemas").css("background-color", "#FFE1E1");

        $("#fundoTemas").css("background-color" ," #f45959");
        $("#fundoTemas").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoTemas").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoTemas").css("transition" ," background-color 1000ms linear");
        // $("#temasParaReuniao1").prop('checked', true);

        $("#temasParaReuniao1").prop('disabled', false);
        $("#temasParaReuniao2").prop('disabled', false);
        $("#temasParaReuniao3").prop('disabled', false);
        $("#temasParaReuniao4").prop('disabled', false);
        $("#temasParaReuniao5").prop('disabled', false);
        $("#temasParaReuniao6").prop('disabled', false);
        $("#temasParaReuniao7").prop('disabled', false);
    }
}

function desabilitaPublicoAlvo () {
    
    if ( $("#atividades1").prop("checked") || $("#atividades2").prop("checked") || $("#atividades3").prop("checked") ) {

        $("#fundoPublico").css("background-color", "#eff8f8");
        $("#fundoPublico").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoPublico").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoPublico").css("transition" ," background-color 1000ms linear"); 
        // $("#temasParaSaude1").prop('checked', false);       

        for (var i = 1; i <= 18 ; i++) {
            $("#publicoAlvo"+i).prop('checked', false)
            $("#publicoAlvo"+i).prop('disabled', true);
        }
        
    } else{
        
        // $(".divPublicoAlvo").css("background-color", "#FFE1E1"); 
        $("#fundoPublico").css("background-color", "#f45959");
        $("#fundoPublico").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoPublico").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoPublico").css("transition" ," background-color 1000ms linear");
        // $("#temasParaSaude1").prop('checked', true);       
        // $(".divTemasParaSaude").css("background-color", "#FFE1E1");        publico alvo div 
        
        for (var i = 1; i <= 18 ; i++) {
            $("#publicoAlvo"+i).prop('disabled', false);
        }
    }
}

function desabilitaTemasParaSaude () {
    
    if ( $("#atividades1").prop("checked") || $("#atividades2").prop("checked") || $("#atividades3").prop("checked") ) {
        
        // $(".temasParaSaude").css("background-color", "white");
        $("#fundoTemasSaude").css("background-color", "#f8f4ef");
        $("#fundoTemasSaude").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoTemasSaude").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoTemasSaude").css("transition" ," background-color 1000ms linear");

        $("#temasParaSaude29").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude19").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude1").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude4").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude5").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude7").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude8").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude10").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude13").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude14").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude15").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude6").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude16").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude17").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude18").prop({
            'disabled': true,
            'checked': false,
        })
        $("#temasParaSaude21").prop({
            'disabled': true,
            'checked': false,
        })

    } else{ fundoTemasSaude
        // $(".temasParaSaude").css("background-color", "#FFE1E1");
        $("#fundoTemasSaude").css("background-color", "#f45959");
        $("#fundoTemasSaude").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoTemasSaude").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoTemasSaude").css("transition" ," background-color 1000ms linear");     
        // $("#publicoAlvo1").prop('checked', true);

        $("#temasParaSaude29").prop({
            'disabled': false
        })
        $("#temasParaSaude19").prop({
            'disabled': false
        })
        $("#temasParaSaude1").prop({
            'disabled': false
        })
        $("#temasParaSaude4").prop({
            'disabled': false
        })
        $("#temasParaSaude5").prop({
            'disabled': false
        })
        $("#temasParaSaude7").prop({
            'disabled': false
        })
        $("#temasParaSaude8").prop({
            'disabled': false
        })
        $("#temasParaSaude10").prop({
            'disabled': false
        })
        $("#temasParaSaude13").prop({
            'disabled': false
        })
        $("#temasParaSaude14").prop({
            'disabled': false
        })
        $("#temasParaSaude15").prop({
            'disabled': false
        })
        $("#temasParaSaude6").prop({
            'disabled': false
        })
        $("#temasParaSaude16").prop({
            'disabled': false
        })
        $("#temasParaSaude17").prop({
            'disabled': false
        })
        $("#temasParaSaude18").prop({
            'disabled': false
        })
        $("#temasParaSaude21").prop({
            'disabled': false
        })
    }
}

function desabilitaPraticasEmSaude (){

    if ( $("#atividades1").prop("checked") || $("#atividades2").prop("checked") || 
         $("#atividades3").prop("checked") || $("#atividades4").prop("checked") ||
         $("#atividades7").prop("checked")) {

        setTimeout(() => {
            document.getElementById("praticas20").disabled = true ;
            document.getElementById("praticas2").disabled = true ;
            document.getElementById("praticas23").disabled = true ;
            document.getElementById("praticas9").disabled = true ;
            document.getElementById("praticas11").disabled = true ;
            document.getElementById("praticas25").disabled = true ;
            document.getElementById("praticas26").disabled = true ;
            document.getElementById("praticas27").disabled = true ;
            document.getElementById("praticas28").disabled = true ;
            document.getElementById("praticas22").disabled = true ;
            document.getElementById("praticas3").disabled = true ;
            document.getElementById("praticas24").disabled = true ;
            document.getElementById("praticas12").disabled = true ;
            document.getElementById("praticas30").disabled = true ;
        }, 250)   
    } else{
        setTimeout(() => {
            document.getElementById("praticas20").disabled = false ;
            document.getElementById("praticas2").disabled = false ;
            document.getElementById("praticas23").disabled = false ;
            document.getElementById("praticas9").disabled = false ;
            document.getElementById("praticas11").disabled = false ;
            document.getElementById("praticas25").disabled = false ;
            document.getElementById("praticas26").disabled = false ;
            document.getElementById("praticas27").disabled = false ;
            document.getElementById("praticas28").disabled = false ;
            document.getElementById("praticas22").disabled = false ;
            document.getElementById("praticas3").disabled = false ;
            document.getElementById("praticas24").disabled = false ;
            document.getElementById("praticas12").disabled = false ;
            document.getElementById("praticas30").disabled = false ;            
        }, 250) 
    }

}

function praticasObrigatórias() {

    if ( $("#atividades6").prop("checked") ) {

        // $(".divPraticasParaSaude").css("background-color", "#FFE1E1");

        $("#fundoPraticas").css("background-color", "#f45959");
        $("#fundoPraticas").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoPraticas").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoPraticas").css("transition" ," background-color 1000ms linear");     
        // $("#praticas2").prop('checked', true);
    } else{

        // $(".divPraticasParaSaude").css("background-color", "#daebf9");
        $("#fundoPraticas").css("background-color", "#f8f3ef");
        $("#fundoPraticas").css("-webkit-transition" ," background-color 1000ms linear");
        $("#fundoPraticas").css("-ms-transition" ," background-color 1000ms linear");
        $("#fundoPraticas").css("transition" ," background-color 1000ms linear");     
        // $("#praticas2").prop('checked', false);
    }
    
}

function habilitaOutroProcedimentoColetivo () {
    if ( $("#praticas30").prop("checked") ) {
    
    document.getElementById('outrosProcedimentosColetivos4388').disabled = false;
    document.getElementById('outrosProcedimentosColetivos4390').disabled = false;
    document.getElementById('outrosProcedimentosColetivos100002').disabled = false;
    document.getElementById('outrosProcedimentosColetivos100013').disabled = false;
    document.getElementById('outrosProcedimentosColetivos4394').disabled = false;
    document.getElementById('outrosProcedimentosColetivos1004').disabled = false;
    document.getElementById('outrosProcedimentosColetivos10004').disabled = false;
    document.getElementById('outrosProcedimentosColetivos4386').disabled = false;
    document.getElementById('outrosProcedimentosColetivos10001').disabled = false;
    document.getElementById('outrosProcedimentosColetivos100001').disabled = false;
    document.getElementById('outrosProcedimentosColetivos100003').disabled = false;
    
    } else{
        document.getElementById('outrosProcedimentosColetivos4388').disabled = true;
        document.getElementById('outrosProcedimentosColetivos4390').disabled = true;
        document.getElementById('outrosProcedimentosColetivos100002').disabled = true;
        document.getElementById('outrosProcedimentosColetivos100013').disabled = true;
        document.getElementById('outrosProcedimentosColetivos4394').disabled = true;
        document.getElementById('outrosProcedimentosColetivos1004').disabled = true;
        document.getElementById('outrosProcedimentosColetivos10004').disabled = true;
        document.getElementById('outrosProcedimentosColetivos4386').disabled = true;
        document.getElementById('outrosProcedimentosColetivos10001').disabled = true;
        document.getElementById('outrosProcedimentosColetivos100001').disabled = true;
        document.getElementById('outrosProcedimentosColetivos100003').disabled = true;
    }
}                           

function habilitaProgramaNacionalDoTabagismo () {
    if ( $("#praticas25").prop("checked") || $("#praticas26").prop("checked") || $("#praticas27").prop("checked") ||
         $("#praticas28").prop("checked") ) {

        document.getElementById('part_fuma').disabled = false;
        document.getElementById('part_grupo').disabled = false;
    } else{

        document.getElementById('part_fuma').disabled = true;
        document.getElementById('part_grupo').disabled = true;        
    }
}

// ---------------------
setTimeout(() => {
  (function($) {
  var CheckboxDropdown = function(el) {
    var _this = this;
    this.isOpen = false;
    this.areAllChecked = false;
    this.$el = $(el);
    this.$label = this.$el.find('.dropdown-label');
    this.$checkAll = this.$el.find('[data-toggle="check-all"]').first();
    this.$inputs = this.$el.find('[type="checkbox"]');
    
    this.onCheckBox();
    
    this.$label.on('click', function(e) {
      e.preventDefault();
      _this.toggleOpen();
    });
    
    this.$checkAll.on('click', function(e) {
      e.preventDefault();
      _this.onCheckAll();
    });
    
    this.$inputs.on('change', function(e) {
      _this.onCheckBox();
    });
  };
  
  CheckboxDropdown.prototype.onCheckBox = function() {
    this.updateStatus();
  };
  
  CheckboxDropdown.prototype.updateStatus = function() {
    var checked = this.$el.find(':checked');
    
    this.areAllChecked = false;
    this.$checkAll.html('Check All');
    
    if(checked.length <= 0) {
      // this.$label.html('Select Options');
    }
    
    else if(checked.length === 1) {
      this.$label.html(checked.parent('label').text());
    }
    else if(checked.length === this.$inputs.length) {
      this.$label.html('All Selected');
      this.areAllChecked = true;
      this.$checkAll.html('Uncheck All');
    }
    else {
      this.$label.html(checked.length + ' Selected');
    }
  };
  
  CheckboxDropdown.prototype.onCheckAll = function(checkAll) {
    if(!this.areAllChecked || checkAll) {
      this.areAllChecked = true;
      this.$checkAll.html('Uncheck All');
      this.$inputs.prop('checked', true);
    }
    else {
      this.areAllChecked = false;
      this.$checkAll.html('Check All');
      this.$inputs.prop('checked', false);
    }
    
    this.updateStatus();
  };
  
  CheckboxDropdown.prototype.toggleOpen = function(forceOpen) {
    var _this = this;
    
    if(!this.isOpen || forceOpen) {
       this.isOpen = true;
       this.$el.addClass('on');
      $(document).on('click', function(e) {
        if(!$(e.target).closest('[data-control]').length) {
         _this.toggleOpen();
        }
      });
    }
    else {
      this.isOpen = false;
      this.$el.removeClass('on');
      $(document).off('click');
    }
  };
  
  var checkboxesDropdowns = document.querySelectorAll('[data-control="checkbox-dropdown"]');
  for(var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
    new CheckboxDropdown(checkboxesDropdowns[i]);
  }
})(jQuery);
}, 250)


