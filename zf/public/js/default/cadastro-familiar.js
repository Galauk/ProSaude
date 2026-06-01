function moeda(a, e, r, t) {
    let n = ""
      , h = j = 0
      , u = tamanho2 = 0
      , l = ajd2 = ""
      , o = window.Event ? t.which : t.keyCode;
    if (13 == o || 8 == o)
        return !0;
    if (n = String.fromCharCode(o),
    -1 == "0123456789".indexOf(n))
        return !1;
    for (u = a.value.length,
    h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
        ;
    for (l = ""; h < u; h++)
        -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
    if (l += n,
    0 == (u = l.length) && (a.value = ""),
    1 == u && (a.value = "0" + r + "0" + l),
    2 == u && (a.value = "0" + r + l),
    u > 2) {
        for (ajd2 = "",
        j = 0,
        h = u - 3; h >= 0; h--)
            3 == j && (ajd2 += e,
            j = 0),
            ajd2 += l.charAt(h),
            j++;
        for (a.value = "",
        tamanho2 = ajd2.length,
        h = tamanho2 - 1; h >= 0; h--)
            a.value += ajd2.charAt(h);
        a.value += r + l.substr(u - 2, u)
    }
    return !1
}


function cadastrarNovaFamilia(){
    // $("#formularioParaCadastroFamiliar input").val("");
    var numeroDoProntuarioFamiliar = null ;
    $.ajax({
        url: baseUrl+"/default/cadastro-familiar/salvar",
        type: "POST",
        success:function(elemento){
            // console.log("aqui : "+ typeof(elemento)
            $("#numeroDoProntuarioFamiliar").val(elemento);
        }
    });
    
    setTimeout(() => {
        numeroDoProntuarioFamiliar = $("#numeroDoProntuarioFamiliar");
    }, 1000)

    $("#formularioParaCadastroFamiliar").dialog({
        modal: true,
        title: 'Cadastre uma nova familía . ',
        width: 800,
        height: 350,
        buttons:{
                Cancelar: function(){
                    $.ajax({
                        url: baseUrl+"/default/cadastro-familiar/excluir-familia/numeroDoProntuarioFamiliar/"+numeroDoProntuarioFamiliar[0].value,
                        type: "GET",
                        data: numeroDoProntuarioFamiliar,
                        success: function(){
                            alert("Cadastro Familiar Cancelado !")
                        }
                    });
                    $(this).dialog('close');
                },

                Finalizar: function(){
                    $.ajax({
                        url: baseUrl+"/default/cadastro-familiar/atualizar-renda-familiar-e-numero-de-membros/numeroDoProntuarioFamiliar/"+numeroDoProntuarioFamiliar[0].value,
                        type: 'GET',
                        data: numeroDoProntuarioFamiliar,
                        success:function(){
                            alert("Família cadastrada com sucesso !");
                            setTimeout(() => {
                              location.reload();
                            }, 500)
                        }
                    })
                    
                }
        }
    });

    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            $.ajax({
                url: baseUrl+"/default/cadastro-familiar/excluir-familia/numeroDoProntuarioFamiliar/"+numeroDoProntuarioFamiliar[0].value,
                type: "GET",
                data: numeroDoProntuarioFamiliar,
                success: function(){
                    alert("Cadastro Familiar Cancelado !")
                  location.reload();            
                }
            });
        }
    });
}

function salvarResponsavelFamiliar(){
    var form = $("#formularioDoResponsavel").serializeArray().reduce((m, o)=> {m[o.name] = o.value; return m});
    var numeroDoProntuarioFamiliar = $("#numeroDoProntuarioFamiliar").val();
    var codigoDoUsuario = $("#usuCodigoResponsavel").val();

    $.ajax({
        url: baseUrl+"/default/tb-composicao-familiar/verifica-se-usuario-ja-e-responsavel-ou-membro-de-outra-familia",
        type: "GET",
        data: {codigoDoUsuario : codigoDoUsuario},
        success:function(respostaAjax){
            var resposta = respostaAjax
            if (resposta == "false") {

                setTimeout(() => {
                    $.ajax({
                        url: baseUrl+"/default/cadastro-familiar/salvar-responsavel-familiar",
                        type: "POST",
                        data: {
                            formulario: form,
                            numeroDoProntuarioFamiliar : numeroDoProntuarioFamiliar,
                            codigoUsuario : codigoDoUsuario
                        },
                        success:function(){
                            alert("Nova Família salva com sucesso !")
                            $("#salvarResponsavel").hide();
                            setTimeout(() => {
                               $(".fieldsetDoNovoMembro").removeAttr('disabled');
                            }, 1000)
                        }
                    });

                }, 500)

            } else{
                alert("Este Usuario já é responsavel ou membro de outra Familía");
            }
        }
    })
        
}

function salvarNovoMembroFamiliar(formulario){
    var form = $(formulario).serializeArray().reduce((m, o)=> {m[o.name] = o.value; return m});
    var numeroDoProntuarioFamiliar = $("#numeroDoProntuarioFamiliar").val();
    var codigoDoUsuario = $("#usuCodigoNovoMembro").val();
    

    $.ajax({
        url: baseUrl+"/default/tb-composicao-familiar/verifica-se-usuario-ja-e-responsavel-ou-membro-de-outra-familia",
        type: "GET",
        data: {codigoDoUsuario: codigoDoUsuario},
        success:function(respostaAjax){
            var resposta = respostaAjax
            if (resposta == "false") {

                setTimeout(() => {
                    $.ajax({
                        url: baseUrl+"/default/cadastro-familiar/salvar-membro",
                        type: "POST",
                        data: {
                            formulario: form,
                            numeroDoProntuarioFamiliar : numeroDoProntuarioFamiliar,
                            codigoUsuario : codigoDoUsuario
                        },
                        success:function(){
                            $(formulario).find("button").hide();
                            alert("Novo paciente adicionado a família !");

                            $.ajax({
                                url: baseUrl+"/default/cadastro-familiar/carregar-formulario-dos-demais-membros",
                                type: "GET",
                                success:function(retorno){
                                    $("#formularioParaCadastroFamiliar").append(retorno)
                                }
                            });
                        }
                    });

                }, 500)

            } else{
                alert("Este Usuario já é responsavel por outra Familía");
            }

        }
    });
    
}

function salvarNovoMembroFamiliarBotao(formulario){
    var form = $(formulario).serializeArray().reduce((m, o)=> {m[o.name] = o.value; return m});
    var numeroDoProntuarioFamiliarBotao = $("#tcf_prontuario_familiar").val();
    var codigoDoUsuario = $("#usuCodigoNovoMembro").val();
    
    $.ajax({
        url: baseUrl+"/default/tb-composicao-familiar/verifica-se-usuario-ja-e-responsavel-ou-membro-de-outra-familia",
        type: "GET",
        data: {codigoDoUsuario: codigoDoUsuario},
        success:function(respostaAjax){
            var resposta = respostaAjax
            if (resposta == "false") {

                setTimeout(() => {
                    $.ajax({
                        url: baseUrl+"/default/cadastro-familiar/salvar-membro-botao",
                        type: "POST",
                        data: {
                            formulario: form,
                            numeroDoProntuarioFamiliar : numeroDoProntuarioFamiliarBotao,
                            codigoUsuario : codigoDoUsuario
                        },
                        success:function(){
                            $(formulario).find("button").hide();
                            alert("Novo paciente adicionado a família !")
                            $.ajax({
                                url: baseUrl+"/default/cadastro-familiar/carregar-formulario-dos-demais-membros-botao",
                                type: "GET",
                                success:function(retorno){
                                    $("#inserir_membro").append(retorno)
                                }
                            });
                        }
                    });

                }, 500)

            } else{
                alert("Este Usuario já é responsavel por outra Familía");
            }

        }
    });
    
}


function inserirIntegrantes(data) {
    var recebe_prontuario_familiar = data;
    // console.log(recebe_prontuario_familiar);
    $("#page").hide();

    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            $("#page").show();
        }
    });

    $("#inserir_membro").dialog({
        modal: true,
        width: 900,
        height: 500,
        title : "Adicionar Integrante.",
        buttons: {
            Fechar: function () {
                $("#page").show();
                $(this).dialog('close');
            },

            Finalizar: function(){
                $.ajax({
                    url: baseUrl+"/default/cadastro-familiar/atualizar-renda-familiar-e-numero-de-membros",
                    type: 'GET',
                    data: {numeroDoProntuarioFamiliar : recebe_prontuario_familiar},
                    success:function(){
                        alert("Dados atualizados !");
                        setTimeout(() => {
                          location.reload();
                        }, 500)
                    }
                })
                
            }
        }
    });
    $("#tcf_prontuario_familiar").val(recebe_prontuario_familiar);

}

function exibeBotao(r){
    $(r).show();
}

function editarGrauParentesco(id ,grau){
    $.ajax({
        url : baseUrl+"/default/cadastro-familiar/editar-membro/",
        type : "POST",
        data :{id : id , grau : grau} ,

        success: function(success){
            alert("Grau parentesco alterado com sucesso !");
        }

    })
}

function excluirMembroFamiliar(data){
    var recebeId = data;

    $.ajax({
        url : baseUrl+"/default/cadastro-familiar/buscar-membro/id/"+data,
        type : "GET",
        data : recebeId,

        success: function(success){
            alert("Membro excluido com sucesso !");
            setTimeout(() => {
              location.reload(); 
            }, 1000)
        }

    })
    return false;
}

function listarFamiliares(numeroProntuarioFamiliar){
    var recebe_prontuario_familiar = numeroProntuarioFamiliar;

     $.ajax({
        url : baseUrl+"/default/cadastro-familiar/carregar-integrantes/recebe_prontuario_familiar/"+recebe_prontuario_familiar,
        type : "GET",
        data : recebe_prontuario_familiar,
        success: function(success){
            var obj = $.parseJSON(success)

            $("#listaDosFamiliares").empty();

            for (var i = 0 ; i<obj.length ;i++ ) {
                $("#listaDosFamiliares").dialog({
                    modal: true,
                    title: 'Lista de Integrantes . ',
                    width: 850,
                    height: 350,
                    buttons:{
                        Fechar: function () {
                            $(this).dialog('close');
                            location.reload();
                        }
                    }
                    }).append(
                            `
                            <tr id = "dadosDosIntegrantes">
                                <td class="ui-state-default c" width="50" name="" id=""> ${obj[i].usu_nome} </td>

                                <td class="ui-state-default c" width="50" name="" id=""> ${obj[i].tgp_descricao} </td>

                                <td class="ui-state-default c" width="50" name="" id=""> ${obj[i].tcomf_renda_mensal_usuario} </td>
                                
                                <td class="ui-state-default c" width="50" name="" id="">
                                    <button id = "${obj[i].usu_codigo}" class="ui-button ui-corner-bl ui-corner-tr" 
                                        style = "padding: 5px 10px 0px;
                                            background-image: url(/WebSocialSaude/zf/public/images/btn-bg.png);
                                            background-repeat: round;
                                            background-size: contain;
                                            border: 1px solid #ACACAC;
                                            margin-bottom: 3px;
                                            color: #000;"
                                    onclick="excluirIntegranteFamiliar(${obj[i].tcf_prontuario_familiar} , ${obj[i].usu_codigo}, ${obj[i].tcomf_responsavel}, $(this).parent().parent())">Excluir</button>
                                </td>

                            </tr>`
                    )
            }
        }

    })


}

function excluirIntegranteFamiliar(prontuarioFamiliar, codigoIntegrante, responsavel, elemento){
    var prontuarioFamiliar = prontuarioFamiliar;
    var codigoIntegrante = codigoIntegrante;
    var usuarioResponsavel = responsavel;

    if (usuarioResponsavel) {
        $("#alertaDeResponsavel").dialog({
            modal: true,
            title: 'Cadastre uma nova familía . ',
            width: 900,
            height: 150,
            buttons:{
                Cancelar: function(){
                    $(this).dialog('close');                   
                },

                Finalizar: function(){
                    $.ajax({
                        url: baseUrl+"/default/cadastro-familiar/apagar-composicao-familiar/prontuarioFamiliar/"+prontuarioFamiliar,
                        type: 'POST',
                        data: prontuarioFamiliar,
                        success:function(){
                            alert("Dados Apagados com sucesso !");
                            location.reload();
                        }
                    })

                }
        }
        });
    } else{
        $.ajax({
            url: baseUrl+"/default/cadastro-familiar/decrementa-renda-familiar/",
            type: "POST",
            data: {
                prontuarioFamiliar : prontuarioFamiliar,
                codigoIntegrante : codigoIntegrante
        },
        success:function(){
            alert("Integrante excluido com sucesso !")
            $(elemento).remove();
        }
        })
    }
    
}