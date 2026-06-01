$(() => {

    let arrayDados = []
    $("#especi").buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl+'/estratificacao/estratificacao/buscar',
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: (ul, item) => {
            var EspCod = item.item.data.esp_codigo;
            var EspNome = item.item.data.esp_nome;

            arrayDados.push({'esp_codigo': item.item.data.esp_codigo, 'esp_nome': item.item.data.esp_nome})
            
            $(".tb_cids").show();
            $(".tb_cids").append("<tr class='tb_cids_"+item.item.data.esp_codigo+"'>\n\
                                    <td>\n\
                                        "+(EspNome.indexOf(EspCod)=="-1" ? EspCod : "")+" "+EspNome+"\n\
                                        <input type='hidden' name='esp_codigo[]' value='"+EspCod+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluirEsp("+EspCod+")\" \>\n\
                                    </td>\n\
                                </tr>");
        }
        
    });


})

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min;
}


function abrePergunta() {

    var idDinamico = getRandomInt(1, 999);

    $("#dadosPerguntas").append(
        `
        </style>
        <div class='atePerg' id='atePerg${idDinamico}'>
            
            <div>
                <label style= "padding: 5px;padding-right: 0px; text-align: left; font-size: 14px; width: 150px; background-color: rgb(255, 225, 225)!important; height: 20px;">Pergunta:</label>
                <input class="ui-state-default" type="text" name="pergunta[pergunta][]" id="pergunta[]" style="width: 300px; height: 20px; padding-top: 2px; padding-bottom: 5px;">
            </div>

            <div>
                <label  style= "padding: 5px;padding-right: 0px; text-align: left; font-size: 14px; width: 150px; background-color: rgb(255, 225, 225)!important; height: 20px;">Valor:</label>
                <input class="ui-state-default required" type="number"  name="pergunta[valor][]" id="valor[]" style="width: 50px; height: 20px; padding-top: 2px; padding-bottom: 5px;">
            </div>
            <br/>

            <div>
                <a class="ui-button" onclick = "apagarPergunta('atePerg${idDinamico}')">
                    <div>
                        <img src="/WebSocialSaude/zf/public/images/icons/excluir3.png">
                    </div>

                    Apagar Pergunta
                </a>

            </div>

        </div>`);

}

function apagarPergunta(valor) {
    
    var recebeValor = valor;
    
    console.log(recebeValor);
    
    $("#"+recebeValor).remove();
    
    console.log($("#"+recebeValor));
    

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

function limparCampos() {
    $("#proc_nome").val("");
}

function adiconaGrupos(){
    
    var recebeCodigoDosGrupos = $("#estratificacaoGruposId").val()

    $.ajax({
        type: "POST",
        url: baseUrl + "/estratificacao/estratificacao/carrega-perguntas-dos-grupos",
        data: {
            recebeCodigoDosGrupos : recebeCodigoDosGrupos
        },
        success: function (retorno) {
            var receberetorno = retorno;

            var obj = JSON.parse(receberetorno);

            var recebeCodigoGrupo = obj[0].id_grupoperg;
            
            if ( $("#codigoDoGrupo"+recebeCodigoGrupo).length > 0) {
                
                alert("Este grupo Já foi adicionado a ficha !")
                
            } else{
                var body = ""

                var cabecalho = `<div style = "width: 477px;margin-bottom: 50px;" id = "codigoDoGrupo${obj[0].id_grupoperg}" >
                                    <div>
                                        <legend style = "font-size : 18px">${obj[0].est_gruponome}</legend>
                                    </div>
                                    `;
                
                var footer = `<button style = "float : right" type = "button" onclick = "excluirGrupoDaLista('${obj[0].id_grupoperg}')">
                                    <img src="/WebSocialSaude/zf/public/images/icons/excluir.png">        
                            </button>
                                </div>
                <input type = "hidden" value = "${obj[0].id_grupoperg}" name = "codigoGrupos[]" id = "codigoGrupos[]"></input>`
                
                var conteudo = [];
    
                for (index = 0; index < obj.length; index++) {
                    conteudo[index] = `<label style = "display: block;margin-left: 164px;" >${obj[index].est_pergunta}</label>`;
    
                }
                
                body = cabecalho

                // $("#perguntas").append(cabecalho);
    
                conteudo.forEach(function(label){
                    // $("#perguntas").append(label);
                    body += label
                })
                
                body += footer
                $("#perguntas").append(body);
                
            }
            


        }
    });
    

}

function excluirGrupoDaLista(recebeCodigoDosGrupos) {
    var recebeCodigo = recebeCodigoDosGrupos
    console.log(recebeCodigo)
    
    $("#codigoDoGrupo"+recebeCodigo).remove();
    
}

function escolhaUmGrupo() {

    $("#escolherGrupo").dialog({
        modal: true,
        width: 350,
        height: 220,
        close: function() {
            $("#escolherGrupo").close();
        },
        buttons: {
            "Fechar": function(){
                $(this).dialog('close')
            },
            "Ok": function() {
                
                if( $("#gruposelect").val() == 0 ){
                    
                    alert("Escolha um grupo valido");
                    
                } else{

                    $("#gruposelect").attr('disabled', true);
                    $("#botoes").show();
                    $(this).dialog('close');

                }


            }
        }
    })
    
}


function alteraTituloPergunta(codigoPergunta){
            
    var recebeCodigo = codigoPergunta;
    var recebeTituloPergunta = $("#tituloPergunta"+recebeCodigo).val();
    var recebeValorPergunta = $("#valorPergunta"+recebeCodigo).val();
    

    $("#alteracaoPergunta").dialog({
        modal: true,
        width: 350,
        height: 220,
        close: function() {
            $(this).hide()
        },
        buttons: {
            "Fechar": function(){
                $(this).dialog('close')
            },
            "Ok": function() {
                $(this).dialog('close')
                
                $.ajax({
                    type: "POST",
                    url: baseUrl+"/estratificacao/estratificacao/atualiza-dados-pergunta",
                    data: {
                        recebeCodigo : recebeCodigo,
                        recebeTituloPergunta : recebeTituloPergunta,
                        recebeValorPergunta : recebeValorPergunta
                    },
                    success: function (response) {
                        alert("Dados Atualizados");
                    }
                });

            }
        }
    })
}
    
function removerPergunta(codigoPergunta) {

    var recebeCodigo = codigoPergunta;
    
    $("#excluirPergunta").dialog({
        modal: true,
        width: 350,
        height: 220,
        close: function() {
            $(this).hide()
        },
        buttons: {
            "Fechar": function(){
                $(this).dialog('close')
            },
            "Ok": function() {
                $(this).dialog('close')
                
                $.ajax({
                    type: "POST",
                    url: baseUrl+"/estratificacao/estratificacao/excluir-pergunta",
                    data: {
                        recebeCodigo : recebeCodigo
                    },
                    success: function (response) {
                        $("#divPerguntas"+recebeCodigo).remove();
                        alert("Dados Atualizados");
                    }
                });

            }
        }
    })
}


function alterarNomeDoGrupo(codigoGrupo) {
    
    var recebeCodigoDoGrupo = codigoGrupo;
    var recebeNomeGrupo = $("#nomeDoGrupo").val();

    	$("#alterarNomeDoGrupo").dialog({
            modal: true,
            width: 350,
            height: 220,
            close: function() {
                $(this).hide()
            },
            buttons: {
                "Fechar": function(){
                    $(this).dialog('close')
                },
                "Ok": function() {
                    $(this).dialog('close')
                    
                    $.ajax({
                        type: "POST",
                        url: baseUrl+"/estratificacao/estratificacao/atualiza-nome-grupo",
                        data: {

                            recebeCodigoDoGrupo : recebeCodigoDoGrupo,
                            recebeNomeGrupo : recebeNomeGrupo                            
                        
                        },
                        success: function (response) {
                            alert("Nome Atualizado ");
                        }
                    });
    
                }
            }
        })
    

}

function desativarGrupo(codigoGrupo) {
    var recebeCodigoDoGrupo = codigoGrupo;
    
    $("#botaoDesativar"+recebeCodigoDoGrupo).remove();

    var botaoAtivar = `<img title = "Ativar Grupo" onclick = "ativarGrupo(${recebeCodigoDoGrupo})" id = "botaoAtivar${recebeCodigoDoGrupo}" src="/WebSocialSaude/zf/public/images/icons/atualizar.png" alt="">`;
    var botaoDesativar = `<img title = "Desativar Grupo" onclick = "desativarGrupo(${recebeCodigoDoGrupo})" id = "botaoDesativar${recebeCodigoDoGrupo}" src="/WebSocialSaude/zf/public/images/icons/remove.png" alt="">`
    
    $("#desativarGrupo").dialog({
        modal: true,
        width: 450,
        height: 220,
        close: function() {
            $(this).hide()
        },
        buttons: {
            "Fechar": function(){

                setTimeout(() => {
                    $("#linkDesativar"+recebeCodigoDoGrupo).append(`${botaoDesativar}`) 
                }, 250);

                $(this).dialog('close')
            },
            "Ok": function() {
                $(this).dialog('close')
                
                $.ajax({
                    type: "POST",
                    url: baseUrl+"/estratificacao/estratificacao/desativar-grupo",
                    data: {

                        recebeCodigoDoGrupo : recebeCodigoDoGrupo
                    
                    },
                    success: function (response) {

                        setTimeout(() => {
                            $("#linkAtivar"+recebeCodigoDoGrupo).append(`${botaoAtivar}`)
                        }, 250);

                        alert("Grupo desativado ");
                    }
                });

            }
        }
    })
}

function ativarGrupo(codigoGrupo) {

    var recebeCodigoDoGrupo = codigoGrupo;

    $("#botaoAtivar"+recebeCodigoDoGrupo).remove();

    var botaoDesativar = `<img title = "Desativar Grupo" onclick = "desativarGrupo(${recebeCodigoDoGrupo})" id = "botaoDesativar${recebeCodigoDoGrupo}" src="/WebSocialSaude/zf/public/images/icons/remove.png" alt="">`

    var botaoAtivar = `<img title = "Ativar Grupo" onclick = "ativarGrupo(${recebeCodigoDoGrupo})" id = "botaoAtivar${recebeCodigoDoGrupo}" src="/WebSocialSaude/zf/public/images/icons/atualizar.png" alt="">`;

    	$("#ativarGrupo").dialog({
            modal: true,
            width: 450,
            height: 220,
            close: function() {
                $(this).hide()
            },
            buttons: {
                "Fechar": function(){

                    setTimeout(() => {
                        $("#linkDesativar"+recebeCodigoDoGrupo).append(`${botaoAtivar}`) 
                    }, 250);

                    $(this).dialog('close')
                },
                "Ok": function() {
                    $(this).dialog('close')
                    
                    $.ajax({
                        type: "POST",
                        url: baseUrl+"/estratificacao/estratificacao/ativar-grupo",
                        data: {

                            recebeCodigoDoGrupo : recebeCodigoDoGrupo
                        
                        },
                        success: function (response) {

                            setTimeout(() => {
                                $("#linkDesativar"+recebeCodigoDoGrupo).append(`${botaoDesativar}`) 
                            }, 250);

                            alert("Grupo ativado ");
                        }
                    });
    
                }
            }
        })
}