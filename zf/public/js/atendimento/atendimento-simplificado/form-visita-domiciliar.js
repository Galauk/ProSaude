$(function () {
    // $("#erro").hide();
    // $("#uni_desc").focus();
    // $("#cid_principal_erro").hide();
    // $("#A").hide();
    // $("#V").hide();
    // $("#P").hide();
    // $("#atetipo_label").hide();
    // $("#A_label").hide();
    // $("#B_label").hide();
    // $("#C_label").hide();

    //#96579 primeira verificação para mostrar o formulario correto inicial
    if ($("#tipo_atendimento").val() == "V") {
        $('#V').attr("checked", "checked");
    } else if ($("#tipo_atendimento").val() == "A") {
        $('#A').attr("checked", "checked");
    } else if ($("#tipo_atendimento").val() == "P") {
        $('#P').attr("checked", "checked");
    }

    $(function () {

        /*$('#chkveg').multiselect({

        includeSelectAllOption: true

        });*/

        $('#btnget').click(function () {
            alert($('#chkveg').val());
        })

    });

    //#96579 vai para o metodo para mostrar o formulario    
    verificaTipoAtentimento('#tipo_atendimento');
    //#96579 vai ao metodo verificar se é uma alteração e mostrar a especialidade correta do atendimento
    carregaEspecialidade();

    $("#ds_ciap").buscar({
        url: baseUrl + '/prontuario/atendimento/buscar-ciap/',
        suffix: '_2',
        search: function () {
            $("#ciap").empty();
        },
        template: function (ul, item) {
            ul.hide();
            $("<option />").val(item.id).html(item.label).appendTo("#ciap");
            return false;
        },
        callback: function (event, ui) {
            $("#ciap").focus();
        }
    });

    $("#ciap")
        .bind('dblclick', selecionarCiap)
        .bind('keydown', selecionarCiap);

    $("#ciap-selecionados")
        .bind('dblclick', deselecionarCiap)
        .bind('keydown', deselecionarCiap);

    $("input[name=egr_inter]", "#ate-simplificado").ready(function () {
        if ($("input[name=egr_inter]:checked", "#ate-simplificado").val() == 'S') {
            $("#div_data_inter").removeAttr("style").show();
            $("#div_motivo_inter").removeAttr("style").show();
            // console.log($("#ate_inter_data_formatado").val());
            if ($("#ate_inter_data_formatado").val() == "01/01/1900") {
                $("#ate_inter_data_formatado").val("");
            }
        }
    });

    $("input[name=egr_inter]", "#ate-simplificado").change(function () {
        //console.log($("input[name=egr_inter]:checked", "#ate-simplificado").val());
        if ($("input[name=egr_inter]:checked", "#ate-simplificado").val() == 'S') {
            //console.log("here");
            $("#div_motivo_inter").removeAttr("style").show();
            // console.log($("#ate_inter_data_formatado").val());
            if ($("#ate_inter_data_formatado").val() == "01/01/1900") {
                $("#ate_inter_data_formatado").val("");
            }
            $("#div_data_inter").removeAttr("style").show();
            $("#div_motivo_inter").removeAttr("style").show();
        } else {
            $("#div_data_inter").removeAttr("style").hide();
            $("#div_motivo_inter").removeAttr("style").hide();
        }
    });

    //  $("#ate_inter_data_formatado").blur(function(){
    //     if($("#ate_inter_data_formatado").val() == null || $("#ate_inter_data_formatado").val() == ""){
    //         $("#ate_inter_data_formatado").val();
    //     mensagemValidaAdd("select-tipo", "Erro", "Data de internação incorreta.", 250, 150);
    //     }
    // });

    $("#ate_inter_motivo").blur(function () {
        if ($("#ate_inter_motivo").val() == null || $("#ate_inter_motivo").val() == "") {
            $("#ate_inter_motivo").val("");
            mensagemValidaAdd("select-tipo", "Erro", "Campo motivo da internação está em branco.", 250, 150);
        }
    });

    $("#ciap")
        .bind('dblclick', selecionarCiap)
        .bind('keydown', selecionarCiap);

    $("#ciap-selecionados")
        .bind('dblclick', deselecionarCiap)
        .bind('keydown', deselecionarCiap);

    $.validator.addMethod("validaVisitaDesfecho", function (validaVisitaDesfecho, element) {
        //alert($("input[name=ate_tipo_atendimento]:checked").val());
        if ($("input[name=ate_tipo_atendimento]:checked").val() != "V") {
            return true;
        } else {
            if ($("input[name=visita_desfecho]:checked").val() == "1" || $("input[name=visita_desfecho]:checked").val() == "2" || $("input[name=visita_desfecho]:checked").val() == "3") {
                return true;
            } else {
                return false;
            }
            return false;
        }
    }, "Campo Obrigatório!");

    $.validator.addMethod("validaVisitaMotivo", function (validaVisitaDesfecho, element) {
        if ($("input[name=ate_tipo_atendimento]:checked").val() == "V") {
            var count = $('input:checkbox:checked').length;
            if ($("input[name=visita_desfecho]:checked").val() == 1) {
                if (count > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }

        } else {
            return true;
        }
    }, "Campo Obrigatório!");

    $("#visita-domiciliar").validate({

        rules: {
            conf_visita: { required: true },
            // tipo_atendimento_paciente: { required: true },
            // encaminhamento: { required: true },
            // condutaDesfecho: { required: true },
            usu_ate_dom_mod :{required: true},
            ate_rac_saude :{required: true},
            turno: { required: true },
            conf_ciap: { required: true },
            conf_cond: { required: true },
            uni_codigo: { required: true },
            usu_codigo: { required: true },
            visita_tipo_domicilio: { required: true },
            usr_codigo: { required: true },
            proc_codigo: { required: true },
            conf_desfecho: { required: true },
            cd10_codigo: { required: true },
            visita_desfecho: { required: true },

            //visita_desfecho:{validaVisitaDesfecho:true}
        },
        messages: {
            conf_visita: { required: "Selecione o motivo da visita." },
            // encaminhamento: { required: "Selecione um encaminhamento." },
            turno: { required: "Selecione um turno." },
            // condutaDesfecho: { required: "Selecione o desfecho." },
            // tipo_atendimento_paciente: { required: "Tipo de atendimento obrigatório." },
            usu_ate_dom_mod: { required: "Selecione o nivel da atenção domiciliar" },
            ate_rac_saude: { required: "Selecione a Racionalidade" },
            conf_ciap: { required: "Selecione um Ciap." },
            conf_cond: { required: "Selecione uma Conduta." },
            uni_codigo: { required: "Selecione uma Unidade." },
            usu_codigo: { required: "Selecione um Paciente." },
            visita_tipo_domicilio: { required: "Selecione o tipo do domicílio." },
            usr_codigo: { required: "Selecione um Profissional." },
            proc_codigo: { required: "Selecione um Procedimento." },
            visita_desfecho: { required: "Selecione um desfecho." },
            cd10_codigo: { required: "Selecione um Cid." }
        }
    });

    $(".ate_tipo_atendimento").change(function () { });

    $("#usr_nome").keyup(() => {
        $("#usr_nome").buscar({
            url: baseUrl + '/default/usuarios/buscar-usuarios-por-unidade?unidade=' + $("#uni_codigo").val(),
            template: function (ul, item) {
                console.log($("#uni_codigo").val());
                return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function (event, ui) {
                /*$("#usr_nome").val(ui.item.value)
                delete ui.item.data.uni_codigo*/
                carregaEspecialidade(); //aqui um
            }
        });
    })


    $(".paciente").click(function () {
        var usu_codigo = $("#usu_codigo").val();
        var cadastro_aise = $("#cadastro_aise").val();
        var link = "";
        if (cadastro_aise == 1) {
            link = baseUrl + "/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
        } else {
            link = baseUrl + "/default/paciente/form-paciente/poupup/1";
            //link = "../../../../WebSocialSaude/paciente.php?acao=form&poupup=1&usu_codigo="+usu_codigo;
        }
        window.open(link, "name", "scrollbars=1,height=800,width=900", 'width=850,height=700');
    });

    $(".desfecho").change(function () {
        if ($(this).val() == 2 || $(this).val() == 3) {
            $(".motivo_checkbox").each(function () {
                $(this).prop("checked", false);
                $(this).attr("disabled", true);
                $("#conf_visita").val("1");
            });
        } else {
            $(".motivo_checkbox").each(function () {
                $(this).removeAttr("disabled");
            });
            //$("#conf_visita").val("");
            validaMotivoVisita();
        }
    });

    $("#usr_nome").change(function () {
        carregaIne();
    });

    if ($("#usr_nome").val() != "") {
        carregaIne();
    }
})

function formataPeso(){
    jQuery('#ate_peso').priceFormat({
        prefix: '',
        centsSeparator: '.',
        centsLimit: 2,
        thousandsSeparator: '.'
    }); 
}

function formataAltura(){
    jQuery('#ate_altura').priceFormat({
        prefix: '',
        centsSeparator: '.',
        centsLimit: 2,
        thousandsSeparator: '.'
    }); 
}

function showGestante() {
    $("#div_gestante").show();
    $("#estrat_gestante").show();
}

function hideGestante() {
    $("#div_gestante").hide();
    $("#estrat_gestante").hide();
}

function exibeGestasPartos() {
    $(".gestasPartos").show();
    $("#gestas_previas").val(1);
}

function escondeGestasPartos() {
    $(".gestasPartos").hide();
    $("#gestas_previas").val(0);
}

function mensagemValidaAdd(id, titulo, mensagem, x, y) {
    $("body").append("<div id=\"" + id + "\" title=\"" + titulo + "\"><div class=\"c\">" + mensagem + "</div></div>");
    $("#" + id).dialog({
        modal: true,
        resizable: false,
        width: x,
        height: y,
        close: function () {
            $(this).remove();
        },
        buttons: {
            OK: function () {
                $(this).dialog('close');
            }
        }
    });
}

// #96579 no momento que salva, chama essa funcao, selecionadno todos os ciap selecionados, para evitar falha no salvar
function selecionarTodosOsCiapSelecionados() {
    var optionlist = document.getElementById('ciap-selecionados').options;

    for (var option = 0; option < optionlist.length; option++) {
        if (option == 1) {
            $("#conf_ciap").val("1");
        }
        optionlist[option].selected = true;
    }
}

// #96579 retirado da inicialização, chamado toda vez qe se é altera o tipo do formulario
function verificaTipoAtentimento(e) {
    if ($(e).val() == "V") {
        $("#visita_domiciliar").show();
        $("#div_tat_codigo").hide();
        $("#conduta").hide();
        $("#ciap-div").hide();
        $("#div_local").hide();
        $("#conf_ciap").val("1");
        $("#nasf-div").hide();
        $("#conf_cond").val("1");
        validaMotivoVisita();
        validaDesfecho();
    } else if ($(e).val() == "A") {
        $("#div_local").show();
        $("#visita_domiciliar").hide();
        $("#div_tat_codigo").show();
        $("#conduta").show();
        $("#ciap-div").show();
        $("#conf_ciap").val("");
        $("#conf_cond").val("");
        $("#conf_visita").val("1");
        $("#conf_desfecho").val("1");
        //$("#proc_codigo").val("1");
        $("#nasf-div").show();
        validaTipoConduta();
    } else if ($(e).val() == "P") {
        //$("#proc_codigo").val("");
        $("#nasf-div").hide();
        $("#conduta").hide();
        $("#visita_domiciliar").hide();
        $("#ciap-div").hide();
        $("#div_local").show();
        $("#div_tat_codigo").hide();
        $("#conf_ciap").val("1");
        $("#conf_cond").val("1");
        $("#conf_visita").val("1");
        $("#conf_desfecho").val("1");
        //validaMotivoVisita();   
    }
}

function verificaTipoAtentimentoAoCarregar(e) {
    // console.log(e)
    if (e === "V") {
        setTimeout(() => {
            $("#visita_domiciliar").show();
            $("#div_tat_codigo").hide();
            $("#conduta").hide();
            $("#ciap-div").hide();
            $("#div_local").hide();
            $("#conf_ciap").val("1");
            $("#nasf-div").hide();
            $("#conf_cond").val("1");
            validaMotivoVisita();
            validaDesfecho();
        }, 250)
    } else if (e === "A") {
        setTimeout(() => {
            $("#div_local").show();
            $("#visita_domiciliar").hide();
            $("#div_tat_codigo").show();
            $("#conduta").show();
            $("#ciap-div").show();
            $("#conf_ciap").val("");
            $("#conf_cond").val("");
            $("#conf_visita").val("1");
            $("#conf_desfecho").val("1");
            //$("#proc_codigo").val("1");
            $("#nasf-div").show();
            validaTipoConduta();
        }, 250)
    } else if (e === "P") {
        // console.log("entrou no if")
        setTimeout(() => {
            $("#nasf-div").hide();
            $("#conduta").hide();
            $("#visita_domiciliar").hide();
            $("#ciap-div").hide();
            $("#div_local").show();
            $("#div_tat_codigo").hide();
            $("#conf_ciap").val("1");
            $("#conf_cond").val("1");
            $("#conf_visita").val("1");
            $("#conf_desfecho").val("1");

            $("#perguntaGestante").hide();
            $("#estrat_gestante").hide();
            $("#div_gestante").hide();

            //validaMotivoVisita();
        }, 250)
        //validaMotivoVisita();
    }

    if ($("#conf_ciap_controle").val() == '1') {
        $("#conf_ciap").val("1");
    }
}

// function dataFormatada() {
//   var data = new Date();
//   if(data.getDate().maxlength() == 1){
//     dia = "0" + data.getDate();
//   } else {
//     dia = data.getDate();
//   }
//   if(data.getDate().maxlength() == 1){
//     mes = "0" + data.getMonth()+1;
//   } else {
//     mes = data.getMonth()+1;
//   } 
//     ano = data.getFullYear();
//   return [dia, mes, ano].join('/');
// }

function validaMotivoVisita() {
    var cont = 0;
    $("#visita_domiciliar").find("input[type=checkbox][name='visita_motivo[]']:checked").each(function () {
        if ($(this).val()) { cont++; }
    });
    if (cont == 0) { $("#conf_visita").val(""); } else { $("#conf_visita").val(cont); }
}

function validaDesfecho() {
    var cont = 0;
    $("#visita_domiciliar").find("input[type=radio][name='visita_desfecho']:checked").each(function () {
        if ($(this).val()) { cont++; }
    });
    if (cont == 0) { $("#conf_desfecho").val(""); } else { $("#conf_desfecho").val(cont); }
}

function desabilitaMotivoDaVisita () {

    var regraDomicilio = $("#regraDeDomicilio").val();
    var visitaRecusadaAusente = $("#visitaRecusadaAusente").val();


    if ( $("#visita_desfecho:checked").val() == 2 || $("#visita_desfecho:checked").val() == 3) {
        $(".motivoDaVisita").attr("disabled", "disabled");  
        $("#visitaRecusadaAusente").val(true); 
    } else{
        if (regraDomicilio == "true" && $("#visita_desfecho:checked").val() == 1 && visitaRecusadaAusente) {
            visitaRealizadaDeAcordoComODomicilio();
        }
        if (regraDomicilio == "false") {
            visitaRealizadaComum();
        }
    }
}

function visitaRealizadaDeAcordoComODomicilio() {
    $("#visitaRecusadaAusente").val(false); 
   
    $("#motivoDaVisita1").removeAttr("disabled", "disabled");
    $("#controleAmbiental34").removeAttr("disabled");
    $("#controleAmbiental35").removeAttr("disabled");
    $("#controleAmbiental36").removeAttr("disabled");
    $("#controleAmbiental37").removeAttr("disabled");
    $("#outros27").removeAttr("disabled", "disabled");
    $("#outros31").removeAttr("disabled", "disabled");
    $("#outros28").removeAttr("disabled", "disabled");
}

function visitaRealizadaComum() {
    $(".motivoDaVisita").removeAttr('disabled');      
}


function desabilitaCamposDoMotivoDaVisita () {

    if ($(".tipoDoDomicilio").val() == 2 || $(".tipoDoDomicilio").val() == 3 || 
        $(".tipoDoDomicilio").val() == 4 || $(".tipoDoDomicilio").val() == 5 ||
        $(".tipoDoDomicilio").val() == 6 || $(".tipoDoDomicilio").val() == 12 ) {
        // $("#motivoDaVisita1").attr("disabled", "disabled");
        $("#regraDeDomicilio").val(true);
        $("#motivoDaVisita29").attr("disabled", "disabled");
        
        $("#buscaAtiva2").attr("disabled", "disabled"); 
        $("#buscaAtiva3").attr("disabled", "disabled"); 
        $("#buscaAtiva4").attr("disabled", "disabled"); 
        $("#buscaAtiva30").attr("disabled", "disabled");

        $("#acompanhamento5").attr("disabled", "disabled");
        $("#acompanhamento6").attr("disabled", "disabled");
        $("#acompanhamento7").attr("disabled", "disabled");
        $("#acompanhamento8").attr("disabled", "disabled");
        $("#acompanhamento9").attr("disabled", "disabled");
        $("#acompanhamento10").attr("disabled", "disabled");
        $("#acompanhamento11").attr("disabled", "disabled");
        $("#acompanhamento12").attr("disabled", "disabled");
        $("#acompanhamento13").attr("disabled", "disabled");
        $("#acompanhamento14").attr("disabled", "disabled");
        $("#acompanhamento15").attr("disabled", "disabled");
        $("#acompanhamento16").attr("disabled", "disabled");
        $("#acompanhamento17").attr("disabled", "disabled");
        $("#acompanhamento18").attr("disabled", "disabled");
        $("#acompanhamento32").attr("disabled", "disabled");
        $("#acompanhamento33").attr("disabled", "disabled");
        $("#acompanhamento19").attr("disabled", "disabled");
        $("#acompanhamento20").attr("disabled", "disabled");
        $("#acompanhamento21").attr("disabled", "disabled");
        $("#acompanhamento22").attr("disabled", "disabled");
        $("#acompanhamento23").attr("disabled", "disabled");
        $("#acompanhamento24").attr("disabled", "disabled");

        // $("#controleAmbiental34").attr("disabled", "disabled");
        // $("#controleAmbiental35").attr("disabled", "disabled");
        // $("#controleAmbiental36").attr("disabled", "disabled");
        // $("#controleAmbiental37").attr("disabled", "disabled");

        $("#outros25").attr("disabled", "disabled");
        // $("#outros27").attr("disabled", "disabled");
        // $("#outros31").attr("disabled", "disabled");
        // $("#outros28").attr("disabled", "disabled");


    } else{
        $("#regraDeDomicilio").val(false);

        // $("#motivoDaVisita1").attr("disabled", "disabled");
        $("#motivoDaVisita29").removeAttr("disabled", "disabled");
        
        $("#buscaAtiva2").removeAttr("disabled"); 
        $("#buscaAtiva3").removeAttr("disabled"); 
        $("#buscaAtiva4").removeAttr("disabled"); 
        $("#buscaAtiva30").removeAttr("disabled");

        $("#acompanhamento5").removeAttr("disabled");
        $("#acompanhamento6").removeAttr("disabled");
        $("#acompanhamento7").removeAttr("disabled");
        $("#acompanhamento8").removeAttr("disabled");
        $("#acompanhamento9").removeAttr("disabled");
        $("#acompanhamento10").removeAttr("disabled");
        $("#acompanhamento11").removeAttr("disabled");
        $("#acompanhamento12").removeAttr("disabled");
        $("#acompanhamento13").removeAttr("disabled");
        $("#acompanhamento14").removeAttr("disabled");
        $("#acompanhamento15").removeAttr("disabled");
        $("#acompanhamento16").removeAttr("disabled");
        $("#acompanhamento17").removeAttr("disabled");
        $("#acompanhamento18").removeAttr("disabled");
        $("#acompanhamento32").removeAttr("disabled");
        $("#acompanhamento33").removeAttr("disabled");
        $("#acompanhamento19").removeAttr("disabled");
        $("#acompanhamento20").removeAttr("disabled");
        $("#acompanhamento21").removeAttr("disabled");
        $("#acompanhamento22").removeAttr("disabled");
        $("#acompanhamento23").removeAttr("disabled");
        $("#acompanhamento24").removeAttr("disabled");

        // $("#controleAmbiental34").removeAttr("disabled");
        // $("#controleAmbiental35").removeAttr("disabled");
        // $("#controleAmbiental36").removeAttr("disabled");
        // $("#controleAmbiental37").removeAttr("disabled");

        $("#outros25").removeAttr("disabled");
        // $("#outros27").attr("disabled", "disabled");
        // $("#outros31").attr("disabled", "disabled");
        // $("#outros28").attr("disabled", "disabled");


    }
}

function desabilitaPesoAltura() {
    var regraDomicilio = $("#regraDeDomicilio").val();

    if (regraDomicilio == "true") {
        $("#ate_peso").val("").attr("disabled" , "disabled")
        $("#ate_altura").val("").attr("disabled" , "disabled")
    } else{
        $("#ate_peso").val("").removeAttr('disabled');
        $("#ate_altura").val("").removeAttr('disabled');

    }

}

function desabilitaPesoAltura () {
    if ($(".tipoDoDomicilio").val() == 2 || $(".tipoDoDomicilio").val() == 3 ||
        $(".tipoDoDomicilio").val() == 4 || $(".tipoDoDomicilio").val() == 5 ||
        $(".tipoDoDomicilio").val() == 6 || $(".tipoDoDomicilio").val() == 12) {
        
        $("#ate_peso").val("").attr("disabled" , "disabled")
        $("#ate_altura").val("").attr("disabled" , "disabled")

    } else{
        $("#ate_peso").removeAttr("disabled")
        $("#ate_altura").removeAttr("disabled")
    }
}

function desabilitaPesoAlturaDesfecho () {
    setTimeout(() => {  
        
        if ($("#visita_desfecho:checked").val() == 1) {
            $("#visitaRecusadaAusente").val(false);    
        }else {
            $("#visitaRecusadaAusente").val(true);
        }

        var regraDomicilio = $("#regraDeDomicilio").val();
        var visitaRecusadaAusente = $("#visitaRecusadaAusente").val();


        if (regraDomicilio === "false" && visitaRecusadaAusente === "true") {
            $("#ate_peso").val("").attr("disabled" , "disabled");
            $("#ate_altura").val("").attr("disabled" , "disabled");
        }

        if (regraDomicilio === "false" && visitaRecusadaAusente === "false") {
            $("#ate_peso").val("").removeAttr("disabled");
            $("#ate_altura").val("").removeAttr('disabled');
        }

    }, 250)

}


function validaData() {
    var data = $("#data_atendimento").val();
    var dataFormatada = data.split('/');
    var dataFormatada = new Date(dataFormatada[2], dataFormatada[1] - 1, dataFormatada[0]);
    var dataHoje = new Date();

    if (dataFormatada <= dataHoje) {
        $("#data_valida").val(true);
    } else {
        $("#data_valida").val('');
    }
}

function validaTipoConduta() {
    var cont = 0;
    $("#conduta").find("input[type=checkbox][name='conduta[]']:checked").each(function () {
        if ($(this).val()) { cont++; }
    });
    if (cont == 0) { $("#conf_cond").val(""); } else { $("#conf_cond").val(cont); }
}

function selecionarCiap(e) {
    $("#conf_ciap").val("1");
    // só pode ser a tecla 39 (seta para direita)
    if (e.keyCode && e.keyCode != 39 || e.charCode) {
        return;
    }

    if (!$("#ciap option:selected").size()) {
        return;
    }

    // se o primeiro for 0, limpar select
    if ($("#ciap-selecionados option:first").val() == "0") {
        $("#ciap-selecionados").empty();
    }

    // add
    $("#ciap-selecionados").append(
        $("#ciap option:selected")
    );

}

function deselecionarCiap(e) {
    // só pode ser a tecla 39 (seta para esquerda)
    if (e.keyCode && e.keyCode != 37 || e.charCode)
        return;

    // remover
    $("#ciap-selecionados option:selected").appendTo("#ciap");
    // se não houver mais opções, add "Nenhum"
    if ($("#ciap-selecionados option").size() == 0) {
        $("#ciap-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum ciap selecionado</option>');
        $("#conf_ciap").val("");
        $("#ciap-selecionados").hide();
    }

}

function buscaParticipante() {
    var idNome = $("#id_nome").val();
    var idCodigo = $("#id_codigo").val();
    var idData = $("#id_data").val();
    var idButton = $("#id_button").val();
    var tipo = $("#id_tipo").val();
    var ativCol = $("#ativCol").val();
    var tipoDoAtendimento = $("#tipoDoAtendimento").val();
    

    $("#" + idNome).buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl + '/paciente/buscar',
        callback: function (event, ui) {
            // console.log(ui.item.data);

            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var dtNasc = ui.item.data.usu_datanasc;
            var usuNasc = ui.item.data.cd_nacionalidade;
            var usuRaca = ui.item.data.rac_codigo;
            var usuDom = ui.item.data.dom_codigo;

            $("#id_data").val(dtNasc);

            if (ui.item.data.usu_sexo == "F" || ui.item.data.usu_sexo == 1) {
                $("#historicoPreNatal").show();
            } else {
                $("#historicoPreNatal").hide();
            }

            $("#perguntaGestante, #div_gestante, #estrat_gestante").hide();

            if (ui.item.data.usu_sexo == "F" && tipoDoAtendimento == "ai") {
                $("#perguntaGestante input").attr('checked', false);
                $("#perguntaGestante").show();
                validarSexoIdade(usuCodigo, dtNasc);
            }

            // console.log(usuNasc + " " + usuRaca + " " + dtNasc);
            if ((cns != "" && cns != null && cns != "undefined") && (validaNacionalidade(usuNasc) == "true") && (validaRaca(usuRaca) == "true") && (validaCnsDigitado(cns) == "true") && (validaEspacoNome(nome) == "true") && (validaEspacoNomeMae(nomeMae) == "true")) {
                if (idNome != "" && idNome != "null" && idNome != "undefined") {
                    $("#" + idNome).val(nome);
                }
                if (idCodigo != "" && idCodigo != "null" && idCodigo != "undefined") {
                    $("#" + idCodigo).val(usuCodigo);
                }
                if (idData != "" && idData != "null" && idData != "undefined") {
                    $("#" + idData).val(dtNasc);
                }
                if (idButton != "" && idButton != "null" && idButton != "undefined") {
                    $("#" + idButton).show();
                }
                // A - Agendamento
                if (tipo == 'A') {
                    carregarHistoricoDoPaciente();
                }
            } else {
                atualizaCnsParticipante(usuCodigo, idNome, idData, ativCol);
            }
        }
    });

}

function validarSexoIdade(id, dataNasc) {
    var dados;

    $.ajax({
        data: { idUsuario: id, dataNascimento: dataNasc },
        url: baseUrl + "/paciente/buscar-idade-sexo",
        type: "GET",
        success: function (callback) {
            console.log(callback);
            if (callback != 'false') {
                var dados = JSON.parse(callback);
                $("#perguntaGestante").show();
                $("#estrat_gestante").show();
                $("#div_gestante").show();

                if (dados.gestas_previas == null) {
                    $(".primeiraGestacao").hide();
                    $(".gestasPartos").hide();
                } else {
                    $(".gestasPartos").show();
                }

                $(".usu_esta_gestante[value = 'T']").prop('checked', true);
                $("#consulta_pre_natal[value = '1']").prop('checked', true);
                $('#dum').val(new Date(dados.dum + "T00:00:00").toLocaleDateString('pt-br'));
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

function buscaProcedimentos() {
    $("#proc_nome").buscar({
        url: baseUrl + "/procedimento/buscar/esp/" + $("#esp_codigo").val() + "/",
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            var url = baseUrl + "/prontuario/cid/procedimento/id/" + $("#proc_codigo").val();
            $("#cid")
                .attr("disabled", "disabled")
                .html("<option value=\"0\">Carregando...</option>")
                .load(url, function (r) {
                    if (r == "") {
                        //$(this).html("<option value=\"0\">Nenhum CID relacionado</option>");
                        adicionaProcedimentos();
                        limparCampos();
                    } else {
                        $("#cid").removeAttr("disabled").focus();
                        $("#cid").prepend("<option value=\"\" selected=selected>--SELECIONE--</option>")

                        $("#cid").change(function () {
                            adicionaProcedimentos();
                            $("#cid").html("<option value='0'>-- Selecione um procedimento --</option>");
                            $("#cid").attr("disabled", "disabled");
                            limparCampos();
                        });
                    }
                });

            return true;
        }
    });
}

function adicionaProcedimentos() {
    //alert($("#procAtendSimp"+$("#proc_codigo").val()).length);
    if ($("#procAtendSimp" + $("#proc_codigo").val()).length == 0) {
        $("#dadosProcAtendSimp").append("\
            <div class='procAtendSimp' id='procAtendSimp" + $("#proc_codigo").val() + "'>\n\
                <span class='titProcAtendSimp'>\n\
                    <font color=#3DA305>" + $("#proc_codigo_sus").val() + "</font> / " + $("#proc_nome").val().substr(0, 80) + " ...\n\
                </span>\n\
                <div class='excProcAtendSimp'>\n\
                    <img src='" + baseUrl + "/public/images/icons/excluir.png' onclick='excluiProcedimento(" + $("#proc_codigo").val() + ")' title='Excluir Horários' alt='Clique aqui para excluir' style='cursor: pointer;position: relative;top: -5px;' />\n\
                    <input type='hidden' name='procedimento[]' value='"+ $("#proc_codigo").val() + "' />\n\
                    <input type='hidden' name='cid[]' value='"+ $("#cid").val() + "' />\n\
                </div>\n\
            </div>");
    }
}

function excluiProcedimento(procCodigo) {
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#procAtendSimp" + procCodigo).remove();

        var cont = 0;

        $("#dadosProcAtendSimp").children("[class='procAtendSimp']").each(function () {
            cont++;
        });

        if (cont == 0) { $("#proc_codigo").val(""); } else { $("#proc_codigo").val(cont); }

    });
}

function limparCampos() {
    $("#proc_nome").val("");
}

function carregaEspecialidade() {
    if ($("#usr_codigo").val()) {
        $("#especialidade").show();
        $.ajax({
            url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional",
            type: "POST",
            data: {
                usrCodigo: $("#usr_codigo").val(),
                uniCodigo: $("#uni_codigo").val()
            },
            beforeSend: () => {
                $("#esp_codigo").append("<option disabled readonly selected>Carregando...</option>")
            },
            success: function (txt) {
                $("#esp_codigo").html("");
                $.each(txt, function (key, value) {
                    if (value['esp_codigo'] == $("#esp_codigo_editar").val()) {
                        $("#esp_codigo").append("<option selected = '" + "selected" + "' title=\"" + value['esp_nome'] + "\" value=\"" + value['esp_codigo'] + "\">" + value['esp_nome'] + "</option>");
                    } else {
                        $("#esp_codigo").append("<option title=\"" + value['esp_nome'] + "\" value=\"" + value['esp_codigo'] + "\">" + value['esp_nome'] + "</option>");
                    }
                })
            }
        });
    } else {
        $("#esp_codigo").html("");
    }
}

function buscaUnidade() {
    $("#usr_nome").val('')
    $("#usr_codigo").val('')
    $("#esp_codigo").html("").append("<option disabled readonly selected>Informe o profissional</option>")
    if ($("#uni_desc").val() == "") {
        $("#usr_nome").prop('placeholder', 'Informe a unidade')
    } else {
        $("#usr_nome").prop('placeholder', 'Informe o profissional')
    }

    $("#uni_codigo").val('')

    $("#uni_desc").buscar({
        url: baseUrl + "/unidade/buscar",
        minLength: 3,
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul)
        },
        callback: (ui, item) => {
            carregaEspecialidade()
        }
    })
}

function retornaPac(usu_codigo, usu_nome) {
    $("#usu_codigo").val(usu_codigo);
    $("#usu_nome").val(usu_nome);
}

function exibirHistoricoPreNatal() {
    var idPaciente = $("#usu_codigo").val();
    $.ajax({
        url: baseUrl + "/paciente/recupera-dados-da-gestacao",
        type: "GET",
        data: { id: idPaciente },
        success: function (result) {
            var dados = JSON.parse(result);
            var riscoDaGestacao = dados.risco_gestacao;
            var tipoRisco = "";

            var dataProvavelParto = dados.data_provavel_parto;
            var dataProvavel = "";

            var dataUltimaMestruacao = dados.dum;
            var dum = "";

            var gestasPrevias = dados.gestas_previas;
            var numeroDeGestacoes = "";

            var idadeGestacional = dados.idade_gestacional;
            var idadeDaGestacao = "";

            var numeroDePartos = dados.partos;
            var partos = "";


            if (dataProvavelParto != null) {
                dataProvavel = dataProvavelParto;
            } else {
                dataProvavel = "Não Informado ."
            }

            if (dataUltimaMestruacao != null) {
                dum = dataUltimaMestruacao;
            } else {
                dum = "Não Informado ."
            }

            if (gestasPrevias != null) {
                numeroDeGestacoes = gestasPrevias;
            } else {
                numeroDeGestacoes = "Não Informado ."
            }

            if (idadeGestacional != null) {
                idadeDaGestacao = idadeGestacional;
            } else {
                idadeDaGestacao = "Não Informado ."
            }

            if (numeroDePartos != null) {
                partos = numeroDePartos;
            } else {
                partos = "Não Informado ."
            }


            if (riscoDaGestacao != null) {
                switch (riscoDaGestacao) {
                    case 'H':
                        tipoRisco = 'Habitual'
                        break;

                    case 'N':
                        tipoRisco = 'Não estratificado'

                        break;

                    case 'I':
                        tipoRisco = 'Intermediario'

                        break;

                    case 'A':
                        tipoRisco = 'Alto'

                        break;
                }
            } else {
                tipoRisco = 'Não informado'
            }

            $("#apresentacaoDosDadosPreNatal").html(
                `<div>
                    <table style="width:100%">
                        <tr>
                            <th>DUM</th> 
                            <th>Data Provavel do Parto</th>
                            <th>Idade Gestacional</th>
                            <th>Risco Gestacional</th>
                            <th>Gestas Prévias</th>
                            <th>Partos</th>
                        </tr>

                        <tr style='text-align: center'>
                            <td>${dum}</td>
                            <td>${dataProvavel}</td>
                            <td>${idadeDaGestacao}</td>
                            <td>${tipoRisco}</td>
                            <td>${numeroDeGestacoes}</td>
                            <td>${partos}</td>
                        </tr>

                    </table>
                </div>`
            );
            $("#apresentacaoDosDadosPreNatal").dialog({
                modal: true,
                title: 'Dados do Pré Natal . ',
                width: 800,
                height: 140,
                buttons: {}
            });
        },
        error: function (result) {
            alert("Este usuário não participa do Pré Natal !");
        }
    });

}

// $.ajax({
//   url: '/path/to/file',
//   type: 'default GET (Other values: POST)',
//   dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
//   data: {param1: 'value1'},
// })
// .done(function() {
//   console.log("success");
// })
// .fail(function() {
//   console.log("error");
// })
// .always(function() {
//   console.log("complete");
// });

/*function init() {
    var selects = document.querySelectorAll('.js-select');

    selects.forEach(function (select, ind) {
        var head = select.querySelector('.select__head');
        var options = select.querySelectorAll('.select__item');
        select.dataset.selectIndex = ind;
        select.dataset.selectOpen = 'false';
        head.dataset.nSelect = ind;

        select.addEventListener('click', function (e) {
            //e.preventDefault()
            e.stopPropagation()
            if (select.classList.contains('checked')) {
                select.classList.remove('checked');
            }
            openSelect(select.dataset.name);
        });

        options.forEach(function (option, optInd) {
            option.dataset.optionIndex = optInd;
            option.dataset.selectIndex = ind;
           
            option.addEventListener('click', function (e) {
                //e.preventDefault()
                e.stopPropagation()
                
                if (e.target.tagName === 'INPUT') {
                    selectOption(e.currentTarget.dataset.optionIndex, select.dataset.name);
                }
            });
        });       

        // Add events to clear all select on button click
        var clearBtns = select.querySelectorAll('.js-select-clear');
        var headTitle = select.querySelector('.select__title');
        clearBtns.forEach(function (clearBtn) {
            clearBtn.addEventListener('click', onClearClick);
            // TODO: remove event listeners
            function onClearClick(e) {
                e.stopPropagation();
                closeOtherSelects(select.dataset.selectIndex);
                var inputs = select.querySelectorAll('.select__field');
                inputs.forEach(function (input) {
                    input.checked = false;
                });
                select.classList.remove('checked');
                headTitle.textContent = '' + select.dataset.selectName;
                // close select if open
                if (select.classList.contains('opened')) {
                    closeBody(select);
                }
            }
        });

        // Mark select as checked if any items selected on page load
        ifItemsSelected(select.dataset.selectIndex, function (itemsNum) {
            select.classList.add('checked');
            if (select.hasAttribute('data-select-multiple')) {
                var _headTitle = select.querySelector('.select__title');
                _headTitle.textContent = select.dataset.selectName + ': ' + itemsNum;
            }
        });
    });
}*/

function init() {
    var selects = document.querySelectorAll('.js-select')
    var count = 0

    selects.forEach(function (select, ind) {
        var head = select.querySelector('.select__head');
        var options = select.querySelectorAll('.select__item');
        select.dataset.selectIndex = count;
        select.dataset.selectOpen = 'false';
        head.dataset.nSelect = count;
        select.addEventListener('click', function (e) {
            e.stopPropagation();
            if (select.classList.contains('checked')) {
                select.classList.remove('checked');
            }
            openSelect(select.dataset.selectIndex);
        });

        options.forEach(function (option, optInd) {
            option.dataset.optionIndex = optInd;
            option.dataset.selectIndex = count;
            option.addEventListener('click', function (e) {
                e.stopPropagation();
                if (e.target.tagName === 'INPUT') {
                    selectOption(e.currentTarget.dataset.optionIndex, e.currentTarget.dataset.selectIndex);
                }
            });
        });
        
        // Add events to clear all select on button click
        var clearBtns = select.querySelectorAll('.js-select-clear');
        var headTitle = select.querySelector('.select__title');
        clearBtns.forEach(function (clearBtn) {
            clearBtn.addEventListener('click', onClearClick);
            // TODO: remove event listeners
            function onClearClick(e) {
                e.stopPropagation();
                closeOtherSelects(select.dataset.selectIndex);
                var inputs = select.querySelectorAll('.select__field');
                inputs.forEach(function (input) {
                    input.checked = false;
                });
                select.classList.remove('checked');
                headTitle.textContent = '' + select.dataset.selectName;
                // close select if open
                if (select.classList.contains('opened')) {
                    closeBody(select);
                }
            }
        });

        // Mark select as checked if any items selected on page load
        ifItemsSelected(select.dataset.selectIndex, function (itemsNum) {
            select.classList.add('checked');
            if (select.hasAttribute('data-select-multiple')) {
                var _headTitle = select.querySelector('.select__title');
                _headTitle.textContent = select.dataset.selectName + ': ' + itemsNum;
            }
        });
        count++
    });
}

function openSelect(index) {
    var select = document.querySelector('[data-select-index="' + index + '"]');
    closeOtherSelects(index);
    if (select.dataset.selectOpen === 'false') {
        openBody(select);
        document.addEventListener('click', onOutsideClick);
    } else {
        closeBody(select);
        ifItemsSelected(select.dataset.selectIndex, function () {
            select.classList.add('checked');
        });
    }
}

function closeOtherSelects(currentSelectIndex) {
    var openSelects = document.querySelectorAll('[data-select-open="true"]');
    if (openSelects.length) {
        openSelects.forEach(function (openSelect) {
            if (openSelect.dataset.selectIndex !== currentSelectIndex) {
                closeBody(openSelect);
                ifItemsSelected(openSelect.dataset.selectIndex, function () {
                    openSelect.classList.add('checked');
                });
            }
        });
    }
}

function selectOption(optIndex, Index) {
    var select = document.querySelector('[data-select-index="' + Index + '"]');
    var headTitle = select.querySelector('.select__title');
    var label = select.querySelector('[data-option-index="' + optIndex + '"] .select__label');
    if (select.hasAttribute('data-select-multiple')) {
        ifItemsSelected(select.dataset.selectIndex, function (itemsNum) {
            headTitle.textContent = select.dataset.selectName + ': ' + itemsNum;
        }, function () {
            headTitle.textContent = '' + select.dataset.selectName;
            select.classList.remove('checked');
        });
    } else {
        // Change text inside of head on the selected one
        headTitle.textContent = label.textContent;
        closeBody(select);
    }
}

function ifItemsSelected(selectIndex, onSuccess, onError) {
    var number = numberOfItemsSelected(selectIndex);
    if (number) {
        return onSuccess ? onSuccess(number) : undefined;
    } else {
        return onError ? onError(number) : undefined;
    }
}

function numberOfItemsSelected(selectIndex) {
    var select = document.querySelector('[data-select-index="' + selectIndex + '"]');
    var inputs = select.querySelectorAll('.select__field');
    var inputsChecked = 0;
    inputs.forEach(function (input) {
        if (input.checked) {
            inputsChecked += 1;
        }
    });
    if (inputsChecked >= 1) {
        return inputsChecked;
    }
    return 0;
}

function onOutsideClick(e) {
    var selects = document.querySelectorAll('[data-select-open="true"]');
    selects.forEach(function (select) {
        closeBody(select);

        ifItemsSelected(select.dataset.selectIndex, function () {
            select.classList.add('checked');
        });
        document.removeEventListener('click', onOutsideClick);
    });
}

function openBody(select) {
    var body = select.querySelector('.select__body')
    var content = select.querySelector('.select__content')

    var heightCalculated = content.offsetHeight
    body.style.height = heightCalculated + 'px'
    body.style.opacity = '1'
    body.style.zIndex = 1002
    body.style.marginBottom = (heightCalculated / 5) + 'px'
    select.dataset.selectOpen = 'true'
    select.classList.toggle('opened')
    $("body").css('height', ($("body").height() + (heightCalculated / 1.35)) + 'px')
    $('html, body').animate({
        scrollTop: $(body).offset().top - 20
    }, 500)
}

function closeBody(select) {
    select.dataset.selectOpen = 'false';
    select.classList.toggle('opened');
    var body = select.querySelector('.select__body');
    body.style.height = '0px';
    body.style.opacity = '0';
    $("body").removeAttr('style')
}

$(document).ready(() => {
    
    var values = []
    $("#conduta div[data-name='conduta'] .select__body input[type='checkbox']").on('change', ev => {
        if ($(ev.target).is(':checked')) {
            values.push($(ev.target).val())
            $("#conf_cond").val(values)
        } else {
            values.splice(values.indexOf($(ev.target).val()), 1)
            $("#conf_cond").val(values)
        }
    })
    $("#encaminhamento div[data-name='encaminhamentos'] .select__body input[type='checkbox']").on('change', ev => {
        if ($(ev.target).is(':checked')) {
            values.push($(ev.target).val())
            $("#conf_enc").val(values)
        } else {
            values.splice(values.indexOf($(ev.target).val()), 1)
            $("#conf_enc").val(values)
        }
    })

    setTimeout(() => {
        init()
        $("#cod_equipe").on('change', () => {
            carregaMicroarea()
        })
    }, 300)
})

function buscarCiap() {
    $("#ciap_busca").buscar({
        url: baseUrl + '/prontuario/atendimento/novo-buscar-ciap/selecionados/' + $("#ciap-selecionados").val() + '/',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                "<a><strong>" + item.codigoCiap + " - " + item.label + "</strong><br>\n\
                        <font size=\"1\"> Inlc: " + item.ds_inclusao + " \n\
                        <br>\n\
                        Excl: " + item.ds_exclusao + "  \n\
                        <br>\n\
                        </font></a>").appendTo(ul);
        },
        callback: function (event, ui) {
            if (ui.item.id != 0) {
                $("#ciapSel").show();
                if ($("#ciap-selecionados option:first").val() == "0") {
                    $("#ciap-selecionados").empty();
                    $("#conf_ciap").val("1");
                }
                $('#ciap-selecionados').append("<option select=\"selected\" value=" + ui.item.id + ">" + ui.item.label + "</option>");
            }
            $('#ciap_busca').val("");
        }
    });
}

function buscarExames() {
    if ($("#exame_solicitado" + $("#exame_codigo").val()).prop("checked") == false &&
        $("#exame_avaliado" + $("#exame_codigo").val()).prop("checked") == false &&
        $("#exame_codigo").val() != '') {
        alert('Para selecionar mais um exame, deve-se informar se o selecionado é solicitado ou avaliado!');
        return;
    }

    $("#exame_nome").buscar({
        url: baseUrl + "/procedimento/buscar-exames/",
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            adicionarExames();
            limparCamposExame();
        }
    })
}

function validaExames(e) {
    var b = $('[id*="exame_solicitado"]');
    b.each(function () {
        if ($("#" + this.id).prop("checked") == false && $("#" + this.id.replace("exame_solicitado", "exame_avaliado")).prop("checked") == false) {
            alert('Para selecionar mais um exame, deve-se informar se o selecionado é solicitado ou avaliado!');
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    return true;
}

function adicionarExames() {

    $("#dadosExames").append("\
        <div id='procExamSimp" + $("#exame_codigo").val() + "' style='height:34px'>\n\
            <div class='procAtendSimp'>\n\
                <span class='titProcAtendSimp'>\n\
                   <font color=#3DA305> " + $("#exame_cod_sus").val() + "</font> / " + $("#exame_nome").val().substr(0, 100) + " ..\n\
                </span>\n\
                <div class='excProcAtendSimp'>\n\
                    <img src='" + baseUrl + "/public/images/icons/excluir.png' onclick='excluiExame(" + $("#exame_codigo").val() + ")' style='cursor: pointer;position: relative;top: -5px;' />\n\
                    <input type='hidden' name='exame[]' value='" + $("#exame_codigo").val() + "' />\
                </div>\
            </div>\
            <div class='tipoexamesolicitado'>\
                <input style=' height:24px; vertical-align: middle;' type='checkbox' id='exame_solicitado" + $("#exame_codigo").val() + "' name='exame_solicitado[" + $("#exame_codigo").val() + "]' value='S" + $("#exame_codigo").val() + "'/> <small>Solicitado</small>\
                <input style=' height:24px; vertical-align: middle;' type='checkbox' id='exame_avaliado" + $("#exame_codigo").val() + "' name='exame_avaliado[" + $("#exame_codigo").val() + "]' value='A" + $("#exame_codigo").val() + "'/> <small>Avaliado</small>\
            </div>\
        </div>"
    );
}

function limparCamposExame() {
    $("#exame_nome").val("");
}

function validacoesExamesCk(idElemento) {
    if (idElemento == 'exame_solicitado') {
        if ($("#exame_solicitado").prop("checked")) {
            $("#exame_avaliado").attr("checked", false);
        } else {
            if ($("#exame_avaliado").prop("checked")) {
                $("#exame_solicitado").attr("checked", false);
            }
        }
    }
}

function excluiExame(procCodigo) {
    $("#procExamSimp" + procCodigo).remove();
}

function carregaIne() {
    setTimeout(function () {
        $("#cod_equipe option").remove();
        $("#cod_equipe").show();
        $.ajax({
            url: baseUrl + "/default/usuarios/carrega-equipes-atendimento-individual",
            type: "POST",
            data: {
                uniCodigo: $("#uni_codigo").val(),
                usrCodigo: $("#usr_codigo").val()
            },
            success: function (txt) {
                if (txt.length > 0) {
                    $("#equipe").show();
                    // $("#cod_equipe").rules("add", "required");
                    var codIne = $("#cod_equipe_ine").val()

                    $("#cod_equipe").val(codIne)
                    $.each(txt, function (key, value) {
                        var selectedIne = '';
                        if (codIne == value['nu_ine']) {
                            selectedIne = "selected='selected'";
                        }
                        if (value['no_equipe']) {
                            $("#cod_equipe").append(`<option ${selectedIne} value="${value["nu_ine"]}" onclick='carregaMicroarea()'>${value["nu_ine"]} - ${value["no_equipe"]}</option>`);
                        }else{
                            $("#cod_equipe").append(`<option ${selectedIne} value="${value["nu_ine"]}" onclick='carregaMicroarea()'>${value["nu_ine"]}</option>`);
                        }
                    })

                    if ($("#dom_microarea_fa:checked").val() != 't') {
                        carregaMicroarea();
                    }

                    carregaMicroarea()

                } else {
                    // $("#cod_equipe").rules("remove", "required");
                    $("#equipe").hide();
                    carregaMicroarea();
                }
            }
        });
    }, 450);
}

function carregaMicroarea() {
    setTimeout(function () {
        $("#usu_microarea option").remove()
        $("#usu_microarea").show()
        var cod_equipe = $("#cod_equipe").val()
        var uni_codigo = $("#uni_codigo").val()

        if(cod_equipe){
            $.ajax({
                url: baseUrl + "/default/especialidade/carrega-microarea",
                type: "POST",
                async: false,
                data: {
                    co_seq_equipe: cod_equipe,
                    uni_codigo: uni_codigo
                },
                success: function (txt) {
                    //if(typeof(txt) == object){
                        if(txt.length > 0){
                            $("#usu_microarea").append("<option value=\"\">Selecione</option>")
                            var codMa = $("#usu_microarea_codigo").val()
                            $.each(txt, function (key, value) {
                                var selectedMa = ''
                                if (codMa == value['mic_codigo']) {
                                    selectedMa = "selected='selected'"
                                }
                                $("#usu_microarea").append("<option " + selectedMa + " value=\"" + value['mic_codigo'] + "\" onclick=''>" + value['mic_descricao'] + ' - ' + value['nu_ine'] + "\</option>")
                            })
                            validaForaArea()
                        } else {
                            $("#usu_microarea").append("<option  value='999' selected>Sem microarea cadastrada</option>")
                        }

                        setTimeout(() => {
                            if ( $("#usu_microarea").val() == 999 ) {
                                $("#usu_microarea_fa").attr("checked" , "checked");
                            }
                        }, 250)
                    /*} else {
                        //$("#usu_microarea").append("<option value=\"\">Selecione</option>")
                    }*/
                }
            })
        } else {
            carregaMicroarea()
        }
    }, 250)
}

function validaForaArea() {
    var checado = false;
    if ($("#usu_microarea_fa").attr("checked") == "checked") {
        checado = true;
    } else {
        checado = false;
    }

    if (checado) {
        $("#usu_microarea").prop('selectedIndex', 0);
        $("#usu_microarea").css("text-decoration", "none");
        $("#usu_microarea").attr("disabled", true);
    } else {
        $("#usu_microarea").attr("disabled", false);
    }
}

function calculaDPP(value) {
    if (value.length == 10) {
        $.get(baseUrl + "/prontuario/pre-natal/calcula-dpp", { dum: value }, response => {
            console.log(response)
        })
    }   
}
// ------------------------------------
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


