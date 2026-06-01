

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

    //dialog add acoes
    



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
            $("#ciap").empty()
        },
        template: function (ul, item) {
            ul.hide()
            $("<option />").val(item.id).html(item.label).appendTo("#ciap")
            return false
        },
        callback: function (event, ui) {
            $("#ciap").focus()
        }
    })

    $("input[name=egr_inter]", "#ate-simplificado").ready(function () {
        if ($("input[name=egr_inter]:checked", "#ate-simplificado").val() == 'S') {
            $("#div_data_inter").removeAttr("style").show()
            $("#div_motivo_inter").removeAttr("style").show()
            // console.log($("#ate_inter_data_formatado").val())
            if ($("#ate_inter_data_formatado").val() == "01/01/1900") {
                $("#ate_inter_data_formatado").val("")
            }
        }
    })

    $("input[name=egr_inter]", "#ate-simplificado").change(function () {
        //console.log($("input[name=egr_inter]:checked", "#ate-simplificado").val());
        if ($("input[name=egr_inter]:checked", "#ate-simplificado").val() == 'S') {
            //console.log("here");
            $("#div_motivo_inter").removeAttr("style").show()
            // console.log($("#ate_inter_data_formatado").val());
            if ($("#ate_inter_data_formatado").val() == "01/01/1900") {
                $("#ate_inter_data_formatado").val("")
            }
            $("#div_data_inter").removeAttr("style").show()
            $("#div_motivo_inter").removeAttr("style").show()
        } else {
            $("#div_data_inter").removeAttr("style").hide()
            $("#div_motivo_inter").removeAttr("style").hide()
        }
    })

    //  $("#ate_inter_data_formatado").blur(function(){
    //     if($("#ate_inter_data_formatado").val() == null || $("#ate_inter_data_formatado").val() == ""){
    //         $("#ate_inter_data_formatado").val();
    //     mensagemValidaAdd("select-tipo", "Erro", "Data de internação incorreta.", 250, 150);
    //     }
    // });

    $("#ate_inter_motivo").blur(function () {
        if ($("#ate_inter_motivo").val() == null || $("#ate_inter_motivo").val() == "") {
            $("#ate_inter_motivo").val("")
            mensagemValidaAdd("select-tipo", "Erro", "Campo motivo da internação está em branco.", 250, 150)
        }
    })

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

    $("#raas_acoes").validate({
        rules: {
            procedimento: { required: true }
        },
        messages: {
            procedimento: { required: "Selecione o motivo da visita." }
        }
    });

    $(".ate_tipo_atendimento").change(function () { });

    $("#usr_nome").keyup(() => {
        $("#usr_nome").buscar({
            url: baseUrl + '/default/usuarios/buscar-usuarios-por-unidade?unidade=' + $("#uni_codigo").val(),
            template: function (ul, item) {
                //console.log($("#uni_codigo").val());
                return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function (event, ui) {
                /*$("#usr_nome").val(ui.item.value)
                delete ui.item.data.uni_codigo*/
                carregaEspecialidade();
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

function salvarEditar(parametro) {

    var recebeForm = $("#raas").serializeArray().reduce((m, o)=> {m[o.name] = o.value; return m});
    console.log(parametro);
    $.ajax({
        url: baseUrl+'/atendimento/atendimento-simplificado/editar-ficha-raas',
        type: 'POST',
        data: {
            recebeId : parametro,
            recebeForm: recebeForm
        },
        success:function(retorno){
            console.log(retorno);
        }
    })
    
}

function meuDialog(parametro) {
    var param = parametro
    $("#finaldialog").dialog({
        title:'Finalizar prontuario ' + param,
        width: 700,
        height:300,
        buttons:{
            Salvar: function(){
                var valor = $("#motivosaida").val();
                $.ajax({
                    url: baseUrl + '/atendimento/atendimento-simplificado/finalizar-ficha-raas',
                    type: 'POST',
                    data: {
                        valor,
                        param
                    },
                })
                window.location.href = baseUrl + '/atendimento/atendimento-simplificado/index-ficha-raas';
                window.location.reload();
                window.location.href = baseUrl + '/atendimento/atendimento-simplificado/index-ficha-raas#tabs3-2';
            },
            Cancelar: function () {
                $(this).dialog("close")
            }
        }
    })
}

function redirectTab(parametro){
    var valor = parametro;
    $.ajax({
        url:baseUrl + '/atendimento/atendimento-simplificado/excluir-ficha-raas',
        type: 'POST',
        data: {
            valor
        },
        success: function(param){
            if(param.status===true){
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Excluído com sucesso!',
                    type: 'success',
                    confirmButtonText: 'Ok'
                }).then(result => {
                    if(result){
                        $("#frameprincipal").context.location.reload();
                    }
                })
            }
        }
    })
    
    
}




var metodo = "";
function buscaCid(){
    //if ($("#ate_codigo").val()!="") {  
        // metodo = adicionaCidBanco;
    // } else {
        metodo = adicionaCid;
    // }
    
    $("#buscar").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo //ou chama direto adicionaCid;
    });
}


function buscaUnidade() {
    $("#usr_nome").val('')
    $("#usr_codigo").val('')
    var uni_codigo = $("#uni_codigo").val()
    $("#esp_codigo").html("").append("<option disabled readonly selected>Informe o profissional</option>")
    if ($("#uni_desc").val() == "") {
        $("#usr_nome").prop('placeholder', 'Informe a unidade')
    } else {
        $("#usr_nome").prop('placeholder', 'Informe o profissional')
    }

    $("#uni_codigo").val('');

    var uniCnes = uni_cnes;

    $("#uni_desc").buscar({
        url: baseUrl + "/unidade/buscar-raas",
        minLength: 3,
        template: function (ul, item) {
 
            return $("<li></li>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul)
        },
        callback: (ui, item) => {
            carregaEspecialidade()
        }


    })
    $("#uni_cnes").val(uniCnes); //COD CNES UNIDADE
    // $("#uni_codigo_ibge").val(uni_codigo_ibge); //COD IBGE UNIDADE
    // $("#uni_uf_ibge").val(uni_uf_ibge); //CODIGO UF IBGE

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

function retornaPac(usu_codigo, usu_nome) {
    $("#usu_codigo").val(usu_codigo);
    $("#usu_nome").val(usu_nome);
}

/*function adicionaCidBanco(){
    var nomeCid = $("#buscar").val();
    var codCid = $("#cd10_codigo_cid").val();
    
    if($(".tb_cids_"+$("#cd10_codigo").val()).length == 0){
        if($('.tb_cids tr').length < 3) {
            $.ajax({
               url: baseUrl+"/prontuario/atendimento/atualizar-cids/",
               type: "POST",
               data:{
                   ate_codigo: $("#ate_codigo").val(),
                   cd10_codigo: $("#cd10_codigo").val()
               },
               success:function(txt){
                    $(".tb_cids").show();
                    $(".tb_cids").append("<tr class='tb_cids_"+$("#cd10_codigo").val()+"'>\n\
                        <td>\n\
                                "+(nomeCid.indexOf(codCid)=="-1" ? codCid : "")+" "+nomeCid+"\n\
                            <input type='hidden' name='cid_codigo[]' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                        </td>\n\
                            <td>\n\
                                <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCid("+$("#cd10_codigo").val()+")\" \>\n\
                            </td>\n\
                        </tr>");
               }
            });

        } else {
            $(".ui-state-error").remove();
            $("#erro").prepend("<span class='ui-state-error'>Máximo de 5 CID(s) por atendimento!</span>");
        }
    } 
    //$("#cidp").val(codCid);
    $("#buscar").val("");
    //$("#cd10_codigo").val("");
}*/


function excluiCidBanco(cidCodigo){
    $.ajax({
        url:baseUrl+"/prontuario/atendimento/excluir-cids/",
        type: "POST",
        data:{
            ate_codigo: $("#ate_codigo").val(),
            cd10_codigo: cidCodigo
        },
        success:function(txt){
            $(".ui-state-error").remove();
            $(".tb_cids_"+cidCodigo).remove();
            if($('.tb_cids tr').length == 0) {
                $(".tb_cids").hide();
            }
        }
    });
    
}


//var num_registros = 0;
function adicionaCid(){
    var nomeCid = $("#buscar").val();
    var codCid = $("#cd10_codigo_cid").val();
    if($(".tb_cids_"+$("#cd10_codigo").val()).length == 0){
        if($('.tb_cids tr').length < 5) {
            $(".tb_cids").show();
            $(".tb_cids").append("<tr class='tb_cids_"+$("#cd10_codigo").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid.indexOf(codCid)=="-1" ? codCid : "")+" "+nomeCid+"\n\
                                        <input type='hidden' name='cid_codigo[]' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCid("+$("#cd10_codigo").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
            $("#cidp").val(codCid);
        } else {
            $(".ui-state-error").remove();
            $("#erro").prepend("<span class='ui-state-error'>Máximo de 5 CID(s) por atendimento!</span>");
        }
    }
    $("#buscar").val("");


    //$("#cd10_codigo").val("");
}
    
function excluiCid(cidCodigo){
    $(".ui-state-error").remove();
    $(".tb_cids_"+cidCodigo).remove();
    if($('.tb_cids tr').length == 0) {
        $(".tb_cids").hide();
    }
}









function buscaCidEditar(){
    //if ($("#ate_codigo").val()!="") {  
        // metodo = adicionaCidBanco;
    // } else {
        metodo = adicionaCidEditar;
    // }
    
    $("#buscar1").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo //ou chama direto adicionaCid;
    });
}

//var num_registros = 0;
function adicionaCidEditar(){
    var nomeCid1 = $("#buscar1").val();
    var codCid1 = $("#cd10_codigo_cid").val();
    if($(".tb_cids1_"+$("#cd10_codigo1").val()).length == 0){
        if($('.tb_cids1 tr').length < 1) {
            $(".tb_cids1").show();
            $(".tb_cids1").append("<tr class='tb_cids1_"+$("#cd10_codigo1").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid1.indexOf(codCid1)=="-1" ? codCid1 : "")+" "+nomeCid1+"\n\
                                        <input type='hidden' name='cid_codigo1' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidEditar("+$("#cd10_codigo1").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
            $("#cidp1").val(codCid1);
        } else {
            $(".ui-state-error").remove();
            $("#erro1").prepend("<span class='ui-state-error'>Máximo de 1 CID(s)!</span>");
        }
    }
    $("#buscar1").val("");


    //$("#cd10_codigo").val("");
}
    
function excluiCidEditar(cidCodigo1){
    $(".ui-state-error").remove();
    $(".tb_cids1_"+cidCodigo1).remove();
    if($('.tb_cids1 tr').length == 0) {
        $(".tb_cids1").hide();
    }
}



function buscaCidEditar2(){
    //if ($("#ate_codigo").val()!="") {  
        // metodo = adicionaCidBanco;
    // } else {
        metodo = adicionaCidEditar2;
    // }
    
    $("#buscar2").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo //ou chama direto adicionaCid;
    });
}

//var num_registros = 0;
function adicionaCidEditar2(){
    var nomeCid2 = $("#buscar2").val();
    var codCid2 = $("#cd10_codigo_cid").val();
    if($(".tb_cids2_"+$("#cd10_codigo2").val()).length == 0){
        if($('.tb_cids2 tr').length < 1) {
            $(".tb_cids2").show();
            $(".tb_cids2").append("<tr class='tb_cids2_"+$("#cd10_codigo2").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid2.indexOf(codCid2)=="-1" ? codCid2 : "")+" "+nomeCid2+"\n\
                                        <input type='hidden' name='cid_codigo2' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidEditar2("+$("#cd10_codigo2").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
            $("#cidp2").val(codCid2);
        } else {
            $(".ui-state-error").remove();
            $("#erro2").prepend("<span class='ui-state-error'>Máximo de 1 CID(s)!</span>");
        }
    }
    $("#buscar2").val("");


    //$("#cd10_codigo").val("");
}
    
function excluiCidEditar2(cidCodigo2){
    $(".ui-state-error").remove();
    $(".tb_cids2_"+cidCodigo2).remove();
    if($('.tb_cids2 tr').length == 0) {
        $(".tb_cids2").hide();
    }
}



function buscaCidEditar3(){
    //if ($("#ate_codigo").val()!="") {  
        // metodo = adicionaCidBanco;
    // } else {
        metodo = adicionaCidEditar3;
    // }
    
    $("#buscar3").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo //ou chama direto adicionaCid;
    });
}

//var num_registros = 0;
function adicionaCidEditar3(){
    var nomeCid3 = $("#buscar3").val();
    var codCid3 = $("#cd10_codigo_cid").val();
    if($(".tb_cids3_"+$("#cd10_codigo3").val()).length == 0){
        if($('.tb_cids3 tr').length < 1) {
            $(".tb_cids3").show();
            $(".tb_cids3").append("<tr class='tb_cids3_"+$("#cd10_codigo3").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid3.indexOf(codCid3)=="-1" ? codCid3 : "")+" "+nomeCid3+"\n\
                                        <input type='hidden' name='cid_codigo3' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidEditar3("+$("#cd10_codigo3").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
            $("#cidp3").val(codCid3);
        } else {
            $(".ui-state-error").remove();
            $("#erro3").prepend("<span class='ui-state-error'>Máximo de 1 CID(s)!</span>");
        }
    }
    $("#buscar1").val("");


    //$("#cd10_codigo").val("");
}
    
function excluiCidEditar3(cidCodigo3){
    $(".ui-state-error").remove();
    $(".tb_cids3_"+cidCodigo3).remove();
    if($('.tb_cids3 tr').length == 0) {
        $(".tb_cids3").hide();
    }
}



function buscaCidEditar4(){
    //if ($("#ate_codigo").val()!="") {  
        // metodo = adicionaCidBanco;
    // } else {
        metodo = adicionaCidEditar4;
    // }
    
    $("#buscar4").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo //ou chama direto adicionaCid;
    });
}

//var num_registros = 0;
function adicionaCidEditar4(){
    var nomeCid4 = $("#buscar4").val();
    var codCid4 = $("#cd10_codigo_cid").val();
    if($(".tb_cids4_"+$("#cd10_codigo4").val()).length == 0){
        if($('.tb_cids4 tr').length < 1) {
            $(".tb_cids4").show();
            $(".tb_cids4").append("<tr class='tb_cids4_"+$("#cd10_codigo4").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid4.indexOf(codCid4)=="-1" ? codCid4 : "")+" "+nomeCid4+"\n\
                                        <input type='hidden' name='cid_codigo4' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidEditar4("+$("#cd10_codigo4").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
            $("#cidp4").val(codCid4);
        } else {
            $(".ui-state-error").remove();
            $("#erro4").prepend("<span class='ui-state-error'>Máximo de 1 CID(s)!</span>");
        }
    }
    $("#buscar4").val("");


    //$("#cd10_codigo").val("");
}
    
function excluiCidEditar4(cidCodigo4){
    $(".ui-state-error").remove();
    $(".tb_cids4_"+cidCodigo4).remove();
    if($('.tb_cids4 tr').length == 0) {
        $(".tb_cids4").hide();
    }
}



function buscaCidEditar5(){
    //if ($("#ate_codigo").val()!="") {  
        // metodo = adicionaCidBanco;
    // } else {
        metodo = adicionaCidEditar5;
    // }
    
    $("#buscar5").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo //ou chama direto adicionaCid;
    });
}

//var num_registros = 0;
function adicionaCidEditar5(){
    var nomeCid5 = $("#buscar5").val();
    var codCid5 = $("#cd10_codigo_cid").val();
    if($(".tb_cids5_"+$("#cd10_codigo5").val()).length == 0){
        if($('.tb_cids5 tr').length < 1) {
            $(".tb_cids5").show();
            $(".tb_cids5").append("<tr class='tb_cids5_"+$("#cd10_codigo5").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid5.indexOf(codCid5)=="-1" ? codCid5 : "")+" "+nomeCid5+"\n\
                                        <input type='hidden' name='cid_codigo5' value='"+$("#cd10_codigo_cid").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidEditar5("+$("#cd10_codigo5").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
            $("#cidp5").val(codCid5);
        } else {
            $(".ui-state-error").remove();
            $("#erro5").prepend("<span class='ui-state-error'>Máximo de 1 CID(s)!</span>");
        }
    }
    $("#buscar5").val("");
    //$("#cd10_codigo").val("");
}
    
function excluiCidEditar5(cidCodigo5){
    $(".ui-state-error").remove();
    $(".tb_cids5_"+cidCodigo5).remove();
    if($('.tb_cids5 tr').length == 0) {
        $(".tb_cids5").hide();
    }
}











//beneficios

function buscarAtivos() {

    $("#proc_nome").buscar({
        url: baseUrl + "/procedimento/buscar-acoes-raas",
        template: function (ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function () {
            adicionaProcedimentosBeneficios();
            return 0;

        /*
            var url = baseUrl + "/prontuario/cid/procedimento/id/" + $("#proc_codigo").val(); //_sus
            $("#cid")

                .attr("disabled", "disabled")
                .html("<option value=\"0\">Carregando...</option>")
                .load(url, function (r) {
                    if (r == "") {
                        //$(this).html("<option value=\"0\">Nenhum CID relacionado</option>");
                        adicionaProcedimentosBeneficios();
                        limparCampos();
                    } else {
                        $("#cid").removeAttr("disabled").focus();
                        $("#cid").prepend("<option value=\"\" selected=selected>--SELECIONE--</option>")

                        $("#cid").change(function () {
                            adicionaProcedimentosBeneficios();
                            $("#cid").html("<option value='0'>-- Selecione um procedimento --</option>");
                            $("#cid").attr("disabled", "disabled");
                            limparCampos();
                        });
                    }
                });console.log(baseUrl);

            return true;*/
        }
    });
}

function buscaParticipante(){
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

     // console.log("aloui");
     // return false;
    $("#"+idNome).buscar({

        delay: 10,
        minLength: 3,
        url: baseUrl+'/paciente/buscar-ras',
        callback: function(event, ui){
            // console.log(ui);
            var cns = ui.item.data.usu_cartao_sus;
            var usuCodigo = ui.item.id;
            var nome = ui.item.label;
            var nomeMae = ui.item.data.usu_mae;
            var dtNasc = ui.item.data.usu_datanasc;
            var usuNasc = ui.item.data.pais_codigo;
            var usuRaca = ui.item.data.rac_codigo;
            var racDescricao = ui.item.data.racDescricao;
            var usuDom = ui.item.data.dom_codigo;
            var usuSexo = ui.item.data.usu_sexo;
            var usuResp = ui.item.data.usu_nome_resp;

            //var usuProntuario = ui.item.data.usu_prontuario;
            var ibgeResid = ui.item.data.muni_codigo_ibge_resid;
            var cidNome = ui.item.data.cid_nome;
            var cidCodigo  = ui.item.data.cid_codigo;
            var etnia = ui.item.data.etnia;
            var domNumero = ui.item.data.dom_numero;
            var ruaNome = ui.item.data.rua_nome;
            var ruaCodigo = ui.item.data.rua_codigo;
            var baiNome = ui.item.data.bai_nome;
            var baiCodigo = ui.item.data.bai_codigo;
            var ruaCep = ui.item.data.rua_cep;
            var domCompl = ui.item.data.dom_complemento;
            var usuCelular = ui.item.data.usu_celular;
            var domTel = ui.item.data.dom_telefone;
            var ufSigla = ui.item.data.uf_sigla;
            var ufCodigo = ui.item.data.uf_codigo;
            //edit\/
            var usuTelefone = ui.item.data.usu_fone;
            //edit/\

            var usuSitRua = ui.item.data.usu_sit_rua;
            var usuAsDrogado = ui.item.data.usu_as_drogado;
            var usuAsAlcoolatra = ui.item.data.usu_as_alcoolatra;

            $("#usu_cartao_sus").val(cns);
            $("#usu_sexo").val(usuSexo);
            $("#usu_datanasc").val(dtNasc);
            $("#pais_codigo").val(usuNasc);
            $("#rac_codigo").val(usuRaca);
            $("#rac_descricao").val(racDescricao);
            $("#usu_mae").val(nomeMae);
            $("#nome_resp").val(usuResp)
            $("#dom_codigo").val(usuDom);
            $("#cid_codigo").val(cidCodigo);
            $("#cid_nome").val(cidNome);
            $("#etnia").val(etnia); //if rac_codigo ==5
            $("#dom_numero").val(domNumero);
            $("#rua_nome").val(ruaNome);
            $("#rua_codigo").val(ruaCodigo);
            $("#bai_nome").val(baiNome);
            $("#bai_codigo").val(baiCodigo);
            $("#rua_cep").val(ruaCep);
            $("#usu_celular").val(usuCelular);
            $("#dom_telefone").val(domTel);
            $("#uf_codigo").val(ufCodigo);
            $("#uf_sigla").val(ufSigla);
            $("#usu_sit_rua").val(usuSitRua);
            $("#usu_as_alcoolatra").val(usuAsAlcoolatra);
            $("#usu_as_drogado").val(usuAsDrogado);
            $("#muni_codigo_ibge_resid").val(ibgeResid);
            $("#usu_fone_2").val(usuTelefone);
            //$("#usu_prontuario").val(usuProntuario);

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
            console.log("teste");return 0;
            $("#dadosProcAtendSimp").append(
            `
            </style>
            <div class='procAtendSimp' id='procAtendSimp`+$("#proc_codigo").val()+`'>
                <div class='excProcAtendSimp'>
                    <img src='`+baseUrl+`/public/images/icons/excluir.png' onclick='excluiProcedimento(`+$(`#proc_codigo`).val()+`)'title='Excluir Horários' alt='Clique aqui para excluir' style='cursor: pointer;position: relative;top: -5px;' />
                    <input type='hidden' name='procedimento[]' value='`+ $(`#proc_codigo`).val()+`'/>
                    <input type='hidden' name='cid[]' value='`+ $(`#cid`).val() + `' />
                </div>
                <div>
                    <span class='titProcAtendSimp'>
                        <font color=#3DA305>`+$("#proc_codigo_sus").val()+`</font>/`+$("#proc_nome").val().substr(0, 80)+` ...
                    </span>
                </div>
                
                <div>
                    <label style="width: 114px!important; background-color: rgb(255, 225, 225)!important;" for="">Quantidade</label>
                    <input style="padding: 5px;border-radius: 4px;" id = "quantidadeTotalDoProcedimento[]" name ="quantidadeTotalDoProcedimento[]" type="number"/>
                </div>
                
                <div>
                    <label style="width: 114px!important; background-color: rgb(255, 225, 225)!important;" for="">Data</label>
                    <input class="dataBeneficio" id="dataDoBeneficio[]" name ="dataDoBeneficio[]" type="date"/>
                </div>
                <br/>
            </div>`);

        }
    });
}

function buscaProcedimentosTipoBeneficio() {
    $("#proc_nome").buscar({
        url: baseUrl + "/procedimento/recupera-beneficio-concedido/esp/" + $("#esp_codigo").val() + "/",
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
                        adicionaProcedimentosBeneficios();
                        limparCampos();
                    } else {
                        $("#cid").removeAttr("disabled").focus();
                        $("#cid").prepend("<option value=\"\" selected=selected>--SELECIONE--</option>")

                        $("#cid").change(function () {
                            adicionaProcedimentosBeneficios();
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

function adicionaProcedimentosBeneficios() {
    console.log($("#procAtendSimp" + $("#proc_codigo").val()).length)
    
    var x = parseInt($("#proc_qnt").val());
    console.log(x);
    if ($("#procAtendSimp" + $("#proc_codigo").val()).length == 0) {
        $("#dadosProcAtendSimp").append(
            `
            </style>
            <div class='procAtendSimp' id='procAtendSimp`+$("#proc_codigo").val()+`'>
                <div class='excProcAtendSimp'>
                    <img src='`+baseUrl+`/public/images/icons/excluir.png' onclick='excluiProcedimento(`+$(`#proc_codigo`).val()+`)'title='Excluir Horários' alt='Clique aqui para excluir' style='cursor: pointer;position: relative;top: -5px;' />
                    <input type='hidden' name='procedimento[]' value='`+ $(`#proc_codigo`).val()+`'/>
                    <input type='hidden' name='cid[]' value='`+ $(`#cid`).val() + `' />
                    <input type="hidden" name='procedimento_cod_sus[]' value='`+ $(`#proc_codigo_sus`).val() + `'/>
                </div>
                <div>
                    <span class='titProcAtendSimp'>
                        <font color=#3DA305>`+$("#proc_codigo_sus").val()+`</font>/`+$("#proc_nome").val().substr(0, 80)+` ...
                    </span>
                </div>
                
                <div>
                    <label style="width: 114px!important;background-color: rgb(255, 225, 225)!important;" for="">Quantidade</label>
                    <input style="padding: 5px;border-radius: 4px;" id = "quantidadeTotalDoProcedimento[]" name ="quantidadeTotalDoProcedimento[]" value='${x !="" ? x : '' }'   type="number"/>
                </div>
                
                <div>
                    <label style="width: 114px!important;background-color: rgb(255, 225, 225)!important;" for="">Data</label>
                    <input class="dataBeneficio" id="dataDoBeneficio[]" name ="dataDoBeneficio[]" value ='${$("#proc_data").val() !="" ? $("#proc_data").val() : '' }' type="date"/>
                </div>
                <br/>
            </div>`);
    }
}

function adicionaProcedimentos() {
    //alert($("#procAtendSimp"+$("#proc_codigo").val()).length);
    console.log($("#procAtendSimp" + $("#proc_codigo").val()).length)
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
    // console.log("entrou aqui");

    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#procAtendSimp" + procCodigo).remove();

        var cont = 0;

        $("#dadosProcAtendSimp").children("[class='procAtendSimp']").each(function () {
            cont++;
        });

        if (cont == 0) { $("#proc_codigo").val(""); } else { $("#proc_codigo").val(cont); }

    });
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

function selecionarTodosOsCiapSelecionados(){
    var optionlist = document.getElementById('ciap-selecionados').options;

    for (var option = 0; option < optionlist.length; option++ ){
        if(option == 1){
            $("#conf_ciap").val("1");
        }
        optionlist[option].selected = true;     
    }
}

function verificaTipoAtentimento(e){
    if($(e).val() == "V"){
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
    } else if($(e).val() == "A") {
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
    } else if($(e).val() == "P") {
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
            carregaEspecialidade();
        }
    });
})


function limparCampos() {
    $("#proc_nome").val("");
}

function formataValor() {
    $('.valorFormatado').priceFormat({
        prefix: '',
        centsSeparator: ',',
        centsLimit: 2,
        thousandsSeparator: ''
    });
}

function habilitaDesabilitaCheckBox(valor) {
    if(valor=='N'){
        $("#checbox input:checkbox").prop("disabled",true)
    }
    else{
        $("#checbox input:checkbox").prop("disabled",false)
    }
}

function habilitaDesabilitaInput(valor) {
    if(valor=='N'){
        $("#divradio input:text").prop("disabled",true)
    }
    else{
        $("#divradio input:text").prop("disabled",false)
    }
}

function recebeProntuario(ras_prontuario) {
    var recebeProntuario = String(ras_prontuario);
    var recebeProntuarioValue = ras_prontuario;

    var recebeAno = recebeProntuario.substr(0,4);
    var recebeId = recebeProntuario.substr(4);

    var concatenarString = recebeId+"/"+recebeAno;

    sessionStorage.setItem('recebeProntuario', concatenarString);
    sessionStorage.setItem('recebeProntuarioValue', recebeProntuarioValue);


}




