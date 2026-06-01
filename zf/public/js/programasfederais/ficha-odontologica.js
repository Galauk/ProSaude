$(function () {

    $("tr:odd").addClass("odd");
    
    $("#form-busca").validate({
        rules: {
            busca: {required: true},
            tipo_busca: {required: true},
        },
        messages: {
            busca: {required: "(*)Obrigatório"},
            tipo_busca: {required: "(*)Obrigatório"}
        }
    });
    
    $("#ate-simplificado").validate({
        rules: {
            data_atendimento: {required: true},
            usu_codigo: {required: true},
            usr_codigo: {required: true},
            esp_codigo: {required: true},
            proc_codigo: {required: true},
            // conf_ta: {required: true},
            // conf_tc: {required: true},
            // conf_cond: {required: true},
            // conf_vig: {required: true}
        },
        messages: {
            data_atendimento: {required: "Campo obrigatório."},
            usu_codigo: {required: "Selecione um Paciente."},
            usr_codigo: {required: "Selecione um Profissional."},
            esp_codigo: {requires: "Selecione uma Especialidade."},
            proc_codigo: {required: "Selecione um Procedimento."},
            // conf_ta: {required: "Campo obrigatório."},
            // conf_tc: {required: "Campo obrigatório."},
            // conf_cond: {required: "Campo obrigatório."},
            // conf_vig: {required: "Campo obrigatório."}
        }
    });

    $(".ate_tipo_atendimento").change(function () {
        if ($(this).val() == "V") {
            carregaModalVisita();
        } else {

        }
    });

    $("#usr_nome").ready(function(){
        if($("#usr_nome").val() != null && $("#usr_nome").val() != ""){
            carregaEspecialidade();
        }
    });

    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            carregaEspecialidade();
        }
    });

    // $(".salvar").click(function(){
    //     alert($("#proc_codigo").val());
    // });

    $("#ate_edit_proc").ready(function(){

        // console.log($("#ate_edit_proc").val());
        if($("#ate_edit_proc").val() != null && $("#ate_edit_proc").val() != ""){

            $('#co_local_atend option[value="'+$("#uni_codigo_aux").val()+'"]').attr("selected", "selected");

            //Inserir os procedimentos salvos puxados para edição
            $.ajax({
                url: baseUrl + "/programasfederais/ficha-odontologica/procedimentos-odonto-ajax",
                type: "POST",
                data: {
                    ate_codigo: $("#ate_edit_proc").val()
                },
                success: function (txt) {
                    
                    $.each(txt, function (key, value) {
                        console.log(value['odo_preal_codigo']);
                        $("#dadosProcAtendSimp").append("\
                            <div class='procAtendSimp' id='procAtendSimp" + value['proc_codigo'] +"-"+ $(".procAtendSimp").length + "'>\n\
                                <span class='titProcAtendSimp'>\n\
                                    " + value['proc_codigo'] + " - " + value['proc_nome'].substr(0, 32) + " ...\n\
                                </span>\n\
                                <div class='excProcAtendSimp'>\n\
                                    <img src='" + baseUrl + "/public/images/icons/excluir.png' onclick=\"excluiProcedimento('" + value['proc_codigo'] +"-"+ $(".procAtendSimp").length + "')\" title='Excluir Horários' alt='Clique aqui para excluir os horários do dia' style='cursor: pointer' />\n\
                                    <input type='hidden' name='procedimento[]' value='" + value['proc_codigo'] + "' />\n\
                                    <input type='hidden' name='odo_preal_codigo[" + value['proc_codigo'] + "]' value='" + value['odo_preal_codigo'] + "' />\n\
                                </div>\n\
                            </div>");
                    })
                }
            });
            limparCampos();
            $.ajax({
                url: baseUrl + "/programasfederais/ficha-odontologica/conduta-ajax",
                type: "POST",
                data: {
                    ate_codigo: $("#ate_edit_proc").val()
                },
                success: function (txt) {
                    
                    $.each(txt, function (key, value) {
                        console.log(value['tp_cds_encam_odonto']);
                        $("input[name='conduta[]'][type=checkbox][value="+value['tp_cds_encam_odonto']+"]").prop("checked",true);
                    });
                    if(txt != null){
                        $("#conf_cond").val("1");
                    }
                }
            });
            

            $.ajax({
                url: baseUrl + "/programasfederais/ficha-odontologica/vigilancia-ajax",
                type: "POST",
                data: {
                    ate_codigo: $("#ate_edit_proc").val()
                },
                success: function (txt) {
                    
                    $.each(txt, function (key, value) {
                        console.log(value['tp_cds_vig_saude_bucal']);
                        $("input[name='vigilancia[]'][type=checkbox][value="+value['tp_cds_vig_saude_bucal']+"]").prop("checked",true);
                    });
                    if(txt != null){
                        console.log("entrou");
                        $("#conf_vig").val("1");
                    }
                }
            });
            

            $.ajax({
                url: baseUrl + "/programasfederais/ficha-odontologica/tipo-consulta-ajax",
                type: "POST",
                data: {
                    ate_codigo: $("#ate_edit_proc").val()
                },
                success: function (txt) {
                    console.log(txt['tat_codigo']);
                    $("input[name='tipo_atend'][value="+txt['tat_codigo']+"]").prop("checked",true);
                    validaTipoAtendimento();
                    $("input[name='tipo_cons'][value="+txt['tp_cod']+"]").prop("checked",true);
                    validaTipoConsulta();
                }
            });
        }
        validaTipoVigilancia();
        validaTipoConduta();
    });

    $(".paciente").click(function () {
        var usu_codigo = $("#usu_codigo").val();
        var cadastro_aise = $("#cadastro_aise").val();
        var link = "";
        if (cadastro_aise == 1) {
            link = baseUrl + "/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
        } else {
            link = baseUrl+"/default/paciente/form-paciente/poupup/1";
            //link = "../../../../WebSocialSaude/paciente.php?acao=form&poupup=1&usu_codigo=" + usu_codigo;
        }
        window.open(link, "name", "scrollbars=1,height=800,width=900", 'width=850,height=700');
    });

});

function buscaProcedimentos(){
    $("#proc_nome").buscar({
        url: baseUrl + "/procedimento/recupera-procedimentos-odonto",
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            adicionaProcedimentos();
            limparCampos();
        }
    });
}

function buscaParticipante() {
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
    var ativCol = $("#ativCol").val();

    var tipoDoAtendimento = $("#tipoDoAtendimento").val();
    // console.log(typeof(tipoDoAtendimento), "valor : " + tipoDoAtendimento);
    // var converteTipoAtendimento = parseInt(tipoDoAtendimento);
    // console.log(converteTipoAtendimento);

    $("#"+idNome).buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl+'/paciente/buscar',
        callback: function(event, ui){
            console.log(ui.item.data);
            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var dtNasc = ui.item.data.usu_datanasc;
            var usuNasc = ui.item.data.cd_nacionalidade;
            var usuRaca = ui.item.data.rac_codigo;
            var usuDom = ui.item.data.dom_codigo;
            var usuSexo = ui.item.data.usu_sexo;
            var usuEstaGestante = ui.item.data.usu_esta_gestante;
            var usuSexo = ui.item.data.usu_sexo;

            $("#id_data").val(dtNasc);

            console.log(usuSexo+","+tipoDoAtendimento)
            if (usuSexo == "F") {
                $("#historicoPreNatal").show();
            } else{
                $("#historicoPreNatal").hide();
            }

            $("#perguntaGestante, #div_gestante, #estrat_gestante").hide();
            

            if (usuSexo == "F" || usuSexo == 1 && tipoDoAtendimento == "FO") {
                $("#perguntaGestante input").attr('checked', false);
                $("#perguntaGestante").show();

                validarSexoIdade(usuCodigo, dtNasc);
            }  

            if (usuEstaGestante) {
                $("#usu_esta_gestante_true").prop("checked",true);
            } else{
                $("#usu_esta_gestante_false").prop("checked",true);
            }
            
            // console.log(usuNasc + " " + usuRaca + " " + dtNasc);
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

function validarSexoIdade(id, dataNasc){
    
    var dados;
    
    $.ajax({
        data : {idUsuario : id , dataNascimento : dataNasc},
        url : baseUrl+"/paciente/buscar-idade-sexo",
        type : "GET",
        success : function(callback){
            console.log(callback);            
            if (callback != 'false') {
                var dados = JSON.parse(callback);
                $("#perguntaGestante").show();
                $("#estrat_gestante").show();
                $("#div_gestante").show();
                if (dados.gestas_previas == null) {
                    $(".primeiraGestacao").hide();
                    $(".gestasPartos").hide();
                } else{
                    $(".gestasPartos").show();
                }

                $(".usu_esta_gestante[value = 'T']").prop('checked', true);
                $("#consulta_pre_natal[value = '1']").prop('checked', true);
                $('#dum').val(new Date(dados.dum + "T00:00:00").toLocaleDateString('pt-br') );
                $("#gravidez_planejada[value = 't']").prop('checked', true);
                $('#idade_gestacional').val(dados.idade_gestacional);
                $('#tipo_consulta').val(dados.tipo_consulta);
                $('#gestas_previas').val(dados.gestas_previas);
                $('#partos').val(dados.partos);
                var option = dados.risco_gestacao;
                
                switch (option) {
                    case 'N': $("#estrat_gestante select").val("N"); break;
                    case 'H': $("#estrat_gestante select").val("H"); break;
                    case 'I': $("#estrat_gestante select").val("I"); break;
                    case 'A': $("#estrat_gestante select").val("A"); break;
                    default: break;
                }    
            }
        }
    }); 
}

function buscaPaciente() {
    var tipo_busca = $("#tipo_busca").val();
    $("#usu_nome").buscar({
        url: baseUrl + '/paciente/buscar/tipo_busca/' + tipo_busca,
        callback: function () {
            return true;
        }
    });
}

// Pega o procedimento e o código selecionado e coloca o valor em um campo 
// oculto para realizar a inserção
function adicionaProcedimentos() {
    //alert($("#procAtendSimp"+$("#proc_codigo").val()).length);
        $("#dadosProcAtendSimp").append("\
            <div class='procAtendSimp' id='procAtendSimp" + $("#proc_codigo").val()+"-"+ $(".procAtendSimp").length + "'>\n\
                <span class='titProcAtendSimp'>\n\
                    " + $("#proc_codigo_sus").val() + " - " + $("#proc_nome").val().substr(0, 32) + " ...\n\
                </span>\n\
                <div class='excProcAtendSimp'>\n\
                    <img src='" + baseUrl + "/public/images/icons/excluir.png' onclick=\"excluiProcedimento('" + $("#proc_codigo").val() +"-"+ $(".procAtendSimp").length + "')\" title='Excluir Horários' alt='Clique aqui para excluir os horários do dia' style='cursor: pointer' />\n\
                    <input type='hidden' name='procedimento[]' value='" + $("#proc_codigo").val() + "' />\n\
                </div>\n\
            </div>");

 }

function excluiProcedimento(procCodigo) {
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#procAtendSimp" + procCodigo).remove();
    });
}

function limparCampos() {
    $("#proc_nome").val("");
    $("#proc_codigo").val("0");
}

function carregaEspecialidade() {
    $("#especialidade option[value!='0']").remove();
    $("#especialidade").show();
    $.ajax({
        url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional",
        type: "POST",
        data: {
            usrCodigo: $("#usr_codigo").val(),
            uniCodigo: $("#uni_codigo").val()
        },
        success: function (txt) {
            $.each(txt, function (key, value) {
                $("#esp_codigo").append("<option title=\"" + value['esp_nome'] + "\" value=\"" + value['esp_codigo'] + "\">" + value['esp_nome'] + "</option>");
            })
        }
    });
}

function buscaUnidade() {
    $("#uni_desc").buscar({
        url: baseUrl + "/unidade/buscar",
        template: function (ul, item) {
            return jQuery("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        }
    });
}

function validaTipoConduta() {
    var cont = 0;
    $("#conduta").find("input[type=checkbox][name='conduta[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_cond").val(""); } else { $("#conf_cond").val(cont); }
}

function validaTipoVigilancia() {
    var cont = 0;
    $("#vigilancia").find("input[type=checkbox][name='vigilancia[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_vig").val(""); } else { $("#conf_vig").val(cont); }
}

// function validaTipoAtendimento(){
//     $("#conf_ta").val("1");
//     //CONSULTA AGENDADA
//     var cont = "";
//     if ($("#tipo_atend:checked").val()==2) {
//         $("#tipo_consulta").each(function (indice) {
//             $(this).find('input[type="radio"]').each(function (indice) {
//                 $("#tipo_cons"+$(this).val()).removeAttr("disabled");
//                 if ($("#tipo_cons"+$(this).val()+":checked").val() > 0) { 
//                     cont++; 
//                 }
//             });
//         });
//         $("#conf_tc").val(cont);
//     }
//     //ESCUTA INICIAL / ORIENTAÇÃO
//     if ($("#tipo_atend:checked").val()==4) {
//         $("#conf_tc").val("1");
//         $("#tipo_consulta").each(function (indice) {
//             $(this).find('input[type="radio"]').each(function (indice) {
//                 $("#tipo_cons"+$(this).val()).attr("disabled",true);
//                 $("#tipo_cons"+$(this).val()).attr("checked",false);
//             });
//         });
//     }
//     //CONSULTA NO DIA
//     if ($("#tipo_atend:checked").val()==5) {
//         $("#conf_tc").val("1");
//         $("#tipo_consulta").each(function (indice) {
//             $(this).find('input[type="radio"]').each(function (indice) {
//                 $("#tipo_cons"+$(this).val()).removeAttr("disabled");
//             });
//         });
//     }
//     //ATENDIMENTO DE URGÊNCIA
//     if ($("#tipo_atend:checked").val()==6) {
//         $("#conf_tc").val("1");
//         $("#tipo_consulta").each(function (indice) {
//             $(this).find('input[type="radio"]').each(function (indice) {
//                 if ($(this).val()==2) {
//                     $("#tipo_cons"+$(this).val()).attr("disabled",true);
//                     $("#tipo_cons"+$(this).val()).attr("checked",false);
//                 } else {
//                     $("#tipo_cons"+$(this).val()).removeAttr("disabled");
//                 }
//             });
//         });
//     }
// }

function consultaObrigatoria(valorInput) {
    var valorDoInput = valorInput;
    
    // REMOVER DISABLED
    $("#tipo_cons1").prop('disabled', false);
    $("#tipo_cons2").prop('disabled', false);
    $("#tipo_cons4").prop('disabled', false);

    // REMOVE VALUE
    $("#tipo_cons1").prop('checked', false);
    $("#tipo_cons2").prop('checked', false);
    $("#tipo_cons4").prop('checked', false);

    if (valorInput == 2) {
        $("#tipo_cons1Nome").css('color', 'red');
        $("#tipo_cons2Nome").css('color', 'red');
        $("#tipo_cons4Nome").css('color', 'red');
        $("#consultaObrigatoriaHidden").val('sim');
        $("#botaoSalvar").hide();
    } else{
        $("#tipo_cons1Nome").css('color', 'black');
        $("#tipo_cons2Nome").css('color', 'black');
        $("#tipo_cons4Nome").css('color', 'black');
        $("#consultaObrigatoriaHidden").val('nao');
        $("#botaoSalvar").show();
    }
}

function liberarBotao(valorInput) {
    var consultaObrigatoriaHidden = $("#consultaObrigatoriaHidden").val();
    var recebeValorInput = valorInput;

    if (consultaObrigatoriaHidden === 'sim' && recebeValorInput == 1 || consultaObrigatoriaHidden === 'sim' && recebeValorInput == 2 ||
            consultaObrigatoriaHidden === 'sim' && recebeValorInput == 4) {
        $("#botaoSalvar").show();
    } else{
        // $("#botaoSalvar").hide();
    }
}

function desabilitaTipoConsulta(valorInput) {
    var recebeValorInput = valorInput;
    if (recebeValorInput == 4) {
        $("#tipo_cons1").prop('disabled', true);
        $("#tipo_cons2").prop('disabled', true);
        $("#tipo_cons4").prop('disabled', true);
    } else{
        $("#tipo_cons1").prop('disabled', false);
        $("#tipo_cons2").prop('disabled', false);
        $("#tipo_cons4").prop('disabled', false);
    }
}

function validaTipoConsulta(valorInput){
    var valorDoInput = valorInput;

    if (valorDoInput == 1 || valorDoInput == 2) {
        $("#conduta15").prop('disabled', false);
        $("#conduta17").prop('disabled', true);
    } else{
        $("#conduta15").prop('disabled', true);
        $("#conduta17").prop('disabled', false);
    }
}

function validaData(e){
    if($(e).val().length > 0){
       if(VerificaData(e)){
            return true;
        }else{
            $("#data_atendimento").val("");
             setTimeout(function() { $('#data_atendimento').focus() }, 500);$("#data_atendimento").focus();
        }
    }
}

function retornaPac(usu_codigo,usu_nome){
    $("#usu_codigo").val(usu_codigo);
    $("#usu_nome").val(usu_nome);
}