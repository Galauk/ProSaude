function listarPerguntasFicha() {
    var recebeCodigoFicha = $("#valoresFichaDeEstratificacao").val()
    
    $("#recebeSomaTotal").val(0);

    $.ajax({
        type: "POST",
        url: baseUrl + "/estratificacao/estratificacao/carrega-perguntas-dos-grupos-por-ficha",
        data: {
            recebeCodigoFicha : recebeCodigoFicha
        },
        success: function (retorno) {

            $("#perguntas").empty();

            var receberetorno = retorno;

            var obj = JSON.parse(receberetorno);

            console.log(obj);

            
            

            var recebeCodigoGrupo = obj[0].id_grupoperg;
            
            window.matrizResposta = new Array();

            for (let contador = 0; contador < obj.length; contador++) {
                
                matrizResposta.push( [ obj[contador].id_perg, "F", obj[contador].est_idgrupo ] );
                
            }

            console.log(matrizResposta);
            

            if ( $("#codigoDoGrupo"+recebeCodigoGrupo).length > 0) {
                
                alert("Este grupo Já foi adicionado a ficha !")
                
            } else{

                var comparador = 9999;

                var tableSuperior = `<table style="width:100%">

                <tr>

                    <th bgcolor = "#528f53" class = "titulo">Pergunta </th> 
                    <th bgcolor = "#528f53" class = "titulo">Resposta</th>
                    
                </tr>`

                var fechamentoTable = `</table>`

                var tableInferiorConteudo = [];

                var nomeDoGrupo = '';

                for (index = 0; index < obj.length; index++) {                    
                    
                    if (comparador != obj[index].est_idgrupo) {

                        nomeDoGrupo =  obj[index].est_gruponome

                    } else{

                        nomeDoGrupo = '';

                    }
                    
                    tableInferiorConteudo[index] = `
                                                    <tr>
                                                        <td bgcolor="#89bee9" class = "nomeDoGrupo" COLSPAN="3"> ${nomeDoGrupo.toUpperCase()} </td>
                                                    </tr>

                                                    <tr>
                                                        <td class = "linhaVerde" bgcolor = "#aed1b3" > ${obj[index].est_pergunta.toUpperCase()} </td>
                                                        
                                                        <td class = "linhaVerde" bgcolor = "#aed1b3">

                                                            <input name = "pergunta${obj[index].id_perg}" onchange = "somarValores(${obj[index].est_pergvalue});atualizarMatrizResposta(${obj[index].id_perg}, 'T')" type="radio">Sim
                                                    
                                                            <input checked name = "pergunta${obj[index].id_perg}"  onchange = "subtrairValores(${obj[index].est_pergvalue});atualizarMatrizResposta(${obj[index].id_perg}, 'F')" type="radio">Não

                                                        </td>
                                                        
                                                    </tr>
                                                    
                                                    `;
                    
                    comparador = obj[index].est_idgrupo;
        
                }

                $("#perguntas").append(tableSuperior + tableInferiorConteudo + fechamentoTable);

                montarTableDeResultados(obj, recebeCodigoFicha);

            }
            
        }
    });
    
}

function atualizarMatrizResposta(id, novoValor) {
    
    window.matrizResposta.filter(function (item) { 

        if (item[0] == id) {
            
            item[1] = novoValor

        }

    })

}

function excluirLista(recebeCodigoLista) {
    var recebeCodigo = recebeCodigoLista

    $("#codigoDoGrupo"+recebeCodigo).remove();
    
}

function somarValores(valor) {
    


    var recebeValor = valor;

    var recuperaSomaTotal = parseInt($("#recebeSomaTotal").val());
    
    var valorFinal = recebeValor + recuperaSomaTotal;    

    $("#recebeSomaTotal").val(valorFinal);
    
    var nivelBaixoInicio = parseInt($("#nivelBaixoInicio").val());
    var nivelBaixoFim = parseInt($("#nivelBaixoFim").val());
    
    var nivelMedioInicio = parseInt($("#nivelMedioInicio").val());
    var nivelMedioFim = parseInt($("#nivelMedioFim").val());

    var nivelAlto = parseInt($("#nivelAlto").val());
    

    if ( valorFinal == nivelBaixoInicio ||  valorFinal <= nivelBaixoFim ) {
        
        $("#linhaDeBaixoRisco").addClass("estiloBaixoNivel");

        $("#linhaDeMedioRisco").removeClass("estiloMedioNivel");
        $("#linhaDeAltoRisco").removeClass("estiloAltoNivel");                
        

    } else{

        $("#linhaDeBaixoRisco").removeClass("estiloBaixoNivel");

    }

    if ( valorFinal <= nivelMedioFim || valorFinal >= nivelMedioInicio ) {
        
        $("#linhaDeAltoRisco").removeClass("estiloAltoNivel");     
        $("#linhaDeBaixoRisco").removeClass("estiloBaixoNivel");
        
        $("#linhaDeMedioRisco").addClass("estiloMedioNivel");


        
    } else{

        $("#linhaDeMedioRisco").removeClass("estiloMedioNivel");        

    }

    if ( valorFinal >= nivelAlto ) {
        
        $("#linhaDeAltoRisco").addClass("estiloAltoNivel");  

        $("#linhaDeBaixoRisco").removeClass("estiloBaixoNivel");
        $("#linhaDeMedioRisco").removeClass("estiloMedioNivel");        
        
    } else{

        $("#linhaDeAltoRisco").removeClass("estiloAltoNivel");        

    }

}


function subtrairValores(valor) {
    
    var recebeValor = valor;

    var recuperaSomaTotal = parseInt($("#recebeSomaTotal").val());    
    
    var valorFinal =  recuperaSomaTotal - recebeValor;
    
    $("#recebeSomaTotal").val(valorFinal);
    
    var nivelBaixoInicio = parseInt($("#nivelBaixoInicio").val());
    var nivelBaixoFim = parseInt($("#nivelBaixoFim").val());
    
    var nivelMedioInicio = parseInt($("#nivelMedioInicio").val());
    var nivelMedioFim = parseInt($("#nivelMedioFim").val());

    var nivelAlto = parseInt($("#nivelAlto").val());
    

    if ( valorFinal == nivelBaixoInicio ||  valorFinal <= nivelBaixoFim ) {
        
        setTimeout(() => {
            $("#linhaDeMedioRisco").removeClass("estiloMedioNivel");
            $("#linhaDeAltoRisco").removeClass("estiloAltoNivel");                
            
            $("#linhaDeBaixoRisco").addClass("estiloBaixoNivel");
        }, 250);

        

    } else{

        $("#linhaDeBaixoRisco").removeClass("estiloBaixoNivel");

    }

    if ( valorFinal <= nivelMedioFim || valorFinal >= nivelMedioInicio ) {
        
        $("#linhaDeAltoRisco").removeClass("estiloAltoNivel");     
        $("#linhaDeBaixoRisco").removeClass("estiloBaixoNivel");
        
        $("#linhaDeMedioRisco").addClass("estiloMedioNivel");


        
    } else{

        $("#linhaDeMedioRisco").removeClass("estiloMedioNivel");        

    }

    if ( valorFinal >= nivelAlto ) {
        
        $("#linhaDeAltoRisco").addClass("estiloAltoNivel");  

        $("#linhaDeBaixoRisco").removeClass("estiloBaixoNivel");
        $("#linhaDeMedioRisco").removeClass("estiloMedioNivel");        
        
    } else{

        $("#linhaDeAltoRisco").removeClass("estiloAltoNivel");        

    }
}


function montarTableDeResultados(obj, recebeCodigoFicha) {
    var listaDeEstratificacao = new Object(obj);
    
    var recebeCodigoFicha = recebeCodigoFicha;

    var objMonitoramento;

    $.ajax({
        type: "POST",
        url: baseUrl + "/estratificacao/estratificacao/carrega-monitoramento",
        data: {

            recebeCodigoFicha : recebeCodigoFicha

        },
        success: function (retorno) {

            $("#referencia").empty();

            var receberetorno = retorno;
            
            objMonitoramento = JSON.parse(receberetorno);
            
            var pontosDeReferencia = `<table>
                                            <tr>
                                                <th bgcolor = "#528f53" class = "titulo">Pontos</th>
                                                <th bgcolor = "#528f53" class = "titulo">Riscos</th>
                                                <th bgcolor = "#528f53" class = "titulo">Recomendações</th>
                                                <th bgcolor = "#528f53" class = "titulo">Monitoramento</th>
                                            </tr>
        
                                            <tr id = "linhaDeBaixoRisco">
                                            
                                                <td id="testeEstratificacao" class = "linhadescricao">
                                                    ${listaDeEstratificacao[0].est_nivelbaixo_inicio} - ${listaDeEstratificacao[0].est_nivelbaixo_fim}
                                                    <input hidden value = "${listaDeEstratificacao[0].est_nivelbaixo_inicio}" id = "nivelBaixoInicio">
                                                    <input hidden value = "${listaDeEstratificacao[0].est_nivelbaixo_fim}" id = "nivelBaixoFim">
                                                </td>

                                                <td class = "linhadescricao">Baixo Risco</td>

                                                <td class = "linhadescricao">
                                                    ${listaDeEstratificacao[0].est_recomendacao_nivel_baixo}
                                                </td>
                                                
                                                <td class = "linhadescricao">
                                                    ${objMonitoramento[0].baixo}
                                                </td>
                                            </tr>
                                            
                                            <tr id = "linhaDeMedioRisco">
                                                <td class = "linhadescricao">
                                                    ${listaDeEstratificacao[0].est_nivelmedio_inicio} - ${listaDeEstratificacao[0].est_nivelmedio_fim}
                                                    <input hidden value = "${listaDeEstratificacao[0].est_nivelmedio_inicio}" id = "nivelMedioInicio">
                                                    <input hidden value = "${listaDeEstratificacao[0].est_nivelmedio_fim}" id = "nivelMedioFim">
                                                </td>

                                                <td class = "linhadescricao">Médio Risco</td>

                                                <td class = "linhadescricao">
                                                    ${listaDeEstratificacao[0].est_recomendacao_nivel_medio}
                                                </td>    
                                                
                                                <td class = "linhadescricao">
                                                    ${objMonitoramento[0].medio}
                                                </td>
                                            </tr>
                                            
                                            <tr id = "linhaDeAltoRisco">
                                                <td class = "linhadescricao">
                                                    ${listaDeEstratificacao[0].est_nivelalto_inicio}
                                                    <input hidden value = "${listaDeEstratificacao[0].est_nivelalto_inicio}" id = "nivelAlto">
                                                </td>

                                                <td class = "linhadescricao">Alto Risco</td>
                                                
                                                <td class = "linhadescricao">
                                                    ${listaDeEstratificacao[0].est_recomendacao_nivel_alto}
                                                </td>  
                                                
                                                <td class = "linhadescricao">
                                                    ${objMonitoramento[0].alto}
                                                </td>

                                            </tr>
        
                                            
                                        
                                        </table>`
            
                                        $("#referencia").append(pontosDeReferencia);
        }
    });

}

function salvarEstratificacaoDeRisco() {    
    
    var recuperaSomaTotal = parseInt($("#recebeSomaTotal").val());    
    var recebeCodigoFicha = $("#valoresFichaDeEstratificacao").val();
    var recebeUsuCodigo = $("#usu_codigo").val();
    
    $("#finalizarEstratificacao").dialog({
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
                    url: baseUrl+"/prontuario/estratificacao-risco/salvar",
                    data: {

                        recebeCodigoFicha : recebeCodigoFicha,
                        recuperaSomaTotal : recuperaSomaTotal,
                        recebeUsuCodigo : recebeUsuCodigo

                    },
                    success: function (retorno) {
                        var recebeRetorno = retorno;

                        var objectoRetorno = JSON.parse(recebeRetorno);

                        salvarRelacionamentoDeRespostas(objectoRetorno);

                        imprimirEstratificacaoDeRisco(objectoRetorno);

                        // return false;
                        
                    }
                });

            }
        }
    })
    
    
}


function salvarRelacionamentoDeRespostas(objectoRetorno) {
    
    var recebeIdFichaUsuario = objectoRetorno;

    $.ajax({

        type: "post",
        url: baseUrl+"/prontuario/estratificacao-risco/salvar-relacionamento-resposta",
        data: {
            
            recebeIdFichaUsuario : recebeIdFichaUsuario,
            matrizResposta : window.matrizResposta

        },
        success: function (response) {
            
            recebeResponse = response;
            
        }

    });
    

}

function imprimirEstratificacaoDeRisco(objectoRetorno){

    var recebeCodigoFicha = objectoRetorno

    window.open(
        baseUrl+"/prontuario/estratificacao-risco/imprimir/recebeCodigoFicha/"+recebeCodigoFicha,'','width=850,height=800'
    );
    
}