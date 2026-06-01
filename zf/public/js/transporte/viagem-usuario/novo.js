function buscaParticipante() {
    //console.log("teste");
    //var usuNome = $("#usu_nome").val();
    var tipoDeBusca = $("#tipo_de_busca").val();
    $("#usu_nome").buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl+'/paciente/buscar/tipo_de_busca/'+tipoDeBusca,
        //url: baseUrl+'/paciente/buscar',
        callback: function (event, ui) {
            var recebeRua = ui.item.data.rua_nome
            var recebeBairro = ui.item.data.rua_bairro
            var recebeNumero = parseInt(ui.item.data.dom_numero)
            var recebeDomCodigo = ui.item.data.dom_codigo

            if (recebeDomCodigo == null || recebeDomCodigo == 'undefined' || recebeDomCodigo == false) {
                $("#casaDoPaciente").prop({
                    'checked': false,
                    'disabled': true,
                })
                alert("Este munícipe não possui um domicilio vinculado !")
                return false
            } else {
                $("#casaDoPaciente").prop("disabled", false)
            }

            $("#recebeDomCodigo").val(recebeDomCodigo)

            if (recebeRua == null || recebeRua == 'undefined') {
                $("#recebeNomeDaRua").val("Não Informado")
            } else {
                $("#recebeNomeDaRua").val(recebeRua)
            }

            if (recebeBairro == null || recebeBairro == 'undefined') {
                $("#recebeNomeDoBairro").val("Não Informado")
            } else {
                $("#recebeNomeDoBairro").val(recebeBairro)
            }

            if (recebeNumero == null || recebeNumero == 'undefined' ){
                $("#recebeNumeroRua").val("N/I")
            } else {
                $("#recebeNumeroRua").val(recebeNumero)
            }

            return true
        }
    });
}

function buscaParticipanteTipo() {
    var tipoDeBusca = $("#tipo_de_busca").val();
    $("#usu_nome").buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl+'/paciente/buscar/tipo_de_busca/'+tipoDeBusca,
        //url: baseUrl+'/paciente/buscar',
        callback: function (event, ui) {
            var recebeRua = ui.item.data.rua_nome
            var recebeBairro = ui.item.data.rua_bairro
            var recebeNumero = parseInt(ui.item.data.dom_numero)
            var recebeDomCodigo = ui.item.data.dom_codigo

            if (recebeDomCodigo == null || recebeDomCodigo == 'undefined' || recebeDomCodigo == false) {
                $("#casaDoPaciente").prop({
                    'checked': false,
                    'disabled': true,
                })
                alert("Este munícipe não possui um domicilio vinculado !")
                return false
            } else {
                $("#casaDoPaciente").prop("disabled", false)
            }

            $("#recebeDomCodigo").val(recebeDomCodigo)

            if (recebeRua == null || recebeRua == 'undefined') {
                $("#recebeNomeDaRua").val("Não Informado")
            } else {
                $("#recebeNomeDaRua").val(recebeRua)
            }

            if (recebeBairro == null || recebeBairro == 'undefined') {
                $("#recebeNomeDoBairro").val("Não Informado")
            } else {
                $("#recebeNomeDoBairro").val(recebeBairro)
            }

            if (recebeNumero == null || recebeNumero == 'undefined' ){
                $("#recebeNumeroRua").val("N/I")
            } else {
                $("#recebeNumeroRua").val(recebeNumero)
            }

            return true
        }
    });
}

$(function () {

    $(document).ready(function () {
        //initialize()
    })

    $(".salvar").click(function(){
        if($("#cid_codigo_2").val() == ""){
            alert("Informe a cidade de destino")
            return false
        }
    })

    //daqui pra frente está certo
    $("#usu_nome_2").hide()
    $("#usu_nome_3").hide()
    $("#usu_nome_4").hide()

    $("#form").validate({
        rules: {
            usu_nome: {
                required: true
            },
            // busca1:{
            //     required: true
            // },
            busca2: {
                required: true
            },
            cid_codigo_2:{
                required: true
            }

        },
        messages: {
            usu_nome: {
                required: "Campo Obrigatório"
            },
            // busca1: {
            //         required: "Campo Obrigatório"
            // }, 
            busca2: {
                required: "Campo Obrigatório"
            },
            cid_codigo_2: {
                required: "Campo Obrigatório"
            }

        }
    })




/*     $("#usu_nome").buscar({
        delay: 10,
        minLength: 3,
        url: baseUrl+'/paciente/buscar/tipo_de_busca/'+$("#tipo_de_busca").val(),
        callback: function (event, ui) {
            var recebeRua = ui.item.data.rua_nome
            var recebeBairro = ui.item.data.rua_bairro
            var recebeNumero = ui.item.data.rua_nome
            var recebeDomCodigo = ui.item.data.dom_codigo

            if (recebeDomCodigo == null || recebeDomCodigo == 'undefined' || recebeDomCodigo == false) {
                $("#casaDoPaciente").prop({
                    'checked': false,
                    'disabled': true,
                })
                alert("Este munícipe não possui um domicilio vinculado !")
                return false
            } else {
                $("#casaDoPaciente").prop("disabled", false)
            }

            $("#recebeDomCodigo").val(recebeDomCodigo)

            if (recebeRua == null || recebeRua == 'undefined') {
                $("#recebeNomeDaRua").val("Não Informado")
            } else {
                $("#recebeNomeDaRua").val(recebeRua)
            }

            if (recebeBairro == null || recebeBairro == 'undefined') {
                $("#recebeNomeDoBairro").val("Não Informado")
            } else {
                $("#recebeNomeDoBairro").val(recebeBairro)
            }

            if (recebeNumero == null || recebeNumero == 'undefined' || typeof (recebeNumero) != Number()) {
                $("#recebeNumeroRua").val("N/I")
            } else {
                $("#recebeNumeroRua").val(recebeNumero)
            }

            return true
        }

    }) */


    $("#busca1").buscar({
        url: baseUrl + '/cidade/buscar/',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul)
        },
        callback: function (event, ui) {
            return true
        }
    })

    $("#busca2").buscar({
        url: baseUrl + '/cidade/buscar/',
        suffix: '_2',
        template: function (ul, item) {
            return $("<li/>").data("item.autocomplete", item).append("<a>" + item.label + "</a>").appendTo(ul)
        },
        callback: function (event, ui) {
            return true
        }
    })
    
    // if($("#viausu_codigo").val()){
    //     $.ajax({
    //     url: baseUrl+"/transporte/usuario-acompanhante/get-acompanhante",
    //         type: "POST",
    //         data: {
    //                 viausu_codigo: $("#viausu_codigo").val()
    //         },
    //         success: function(txt){
    //           total = $(".buscaAcom").size()
    //             for( var i in txt){
    //                 $("#usu_nome_"+total+"").val(txt[i].usu_nome_)
    //                 $("#usu_codigo_"+total+"").val(txt[i].usu_codigo_)
    //                 $("#usu_nome_"+total+"").addClass("buscaAcom")
    //                 $("#usu_nome_"+total+"").show()
    //                // alert(total)
    //                 total = $(".buscaAcom").size()+1


    //                 //alert(txt[i].usu_codigo_)

    //             }             
    //         }
    // })
    // }

    $("#addPac").click(function () {
        total = $(".buscaAcom").size() + 1
        total_anterior = $(".buscaAcom").size()
        //  alert("#usu_nome_"+total+"")
        if ($("#usu_codigo_" + total_anterior + "").val() != "") {
            if (total >= $("#disponivel").val()) {
                mensagem("Erro", "Erro ao inserir acompanhante!.<br/>" + "Já excedeu a quantidade de pacientes por veículo!", 300, 150)
                return false
            }
            $("#usu_nome_" + total + "").addClass("buscaAcom")
            $("#usu_nome_" + total + "").show()
        }
    })

    for (i = 1; i <= 4; i++) {
        $("#usu_nome_" + i).buscar({
            suffix: '_' + i,
            url: baseUrl + '/paciente/buscar/',
            callback: function (event, ui) {
                return true
            }
        })
    }
})



function initialize() {
    var directionDisplay = null
    var map = null
    aa = document.getElementById("map_canvas")

    directionsDisplay = new google.maps.DirectionsRenderer()
    var maringa = new google.maps.LatLng('-23.4166645', '-51.91666')
    var myOptions = {
        zoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: maringa
    }
    map = new google.maps.Map(aa, myOptions)
    directionsDisplay.setMap(map)
}

/*
$(document).ready(function(){
    setTimeout(function(){
        getDestino($("#rotas_transporte").val())
    }, 250)
})

function getDestino(value){
    if(value != ""){
        $("#cid_codigo_2").append(`<option>Carregando...</option>`)
        $.get(baseUrl+'/transporte/viagem-usuario/get-destino', {rota: value, veiculo: $("#vei_codigo").val()}, function(response){
            if(!!response){
                if(response.length > 0){
                    $("#cid_codigo_2").empty()
                    $("#cid_codigo_2").append(`<option value="">Selecione...</option>`)
                    response.forEach(item => {
                        $("#cid_codigo_2").append(`<option value="${item.cid_codigo}" >${item.destino} - ${item.uf}</option>`)
                    })
                }
            }
        })
    } else {
        $("#cid_codigo_2").append(`<option>Informe a rota</option>`)
    }
}*/


function tipoViagem(value){
    switch (value) {
        case "1" :
            $("#tv").html(`
                <label>Diário</label>
                1 dia
            `)
        break
        
        case "2":
            $("#tv").html(`
                <label>Período</label>
                <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="de" id="de" style="width: 133px !important;"/>
                <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="ate" id="ate" style="width: 133px !important;"/>
            `)

            $("#tv > input ").datepicker({minDate: new Date()})

            $(".mask").each(function(){
                $(this).mask( $(this).attr("rel") )
            })
        break

        case "3":
            $("#tv").html(`
                <label>Período</label>
                <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="dias[]" id="de" style="width: 133px !important;"/>
                <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="dias[]" id="ate" style="width: 133px !important;"/>
                <span style="position: absolute; margin-left: 5px; margin-top: -2px;">
                    <img src="${baseUrl}/public/images/icons/add.png" id="addPer" style="cursor:pointer; padding-top: 10px;">
                </span> 
                <!--<div style="margin-left: 218px;">
                    <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="dias[]" id="de" style="width: 133px !important;"/>
                    <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="dias[]" id="ate" style="width: 133px !important;"/>
                </div>-->
            `)

            // $("input[name='de[]'], input[name='ate[]']").datepicker({minDate: new Date()})
            
            $("#tv input").datepicker({minDate: new Date()})
            
            $(".mask").each(function(){
                $(this).mask( $(this).attr("rel"))
            })

            document.getElementById("addPer").addEventListener('click', function(){
                $("#tv").append(`
                    <div style="margin-left: 218px; margin-bottom: 2px;">
                        <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="dias[]" style="width: 133px !important;"/>
                        <input type="text" class="mask data" rel="99/99/9999" placeholder="__/__/____" name="dias[]" style="width: 133px !important;"/>
                    </div>
                `)

                // $("input[name='de[]'], input[name='ate[]']").datepicker({minDate: new Date()})

                $("#tv input").each(function(i, el){
                    $(el).datepicker({minDate: new Date()})
                    $(el).mask( $(el).attr("rel"))
                })
            })
        break

        default:
            $("#tv").empty()
        break
    }
}