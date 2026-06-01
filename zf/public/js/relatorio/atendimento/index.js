$(function() {
    
    $("#form").validate({
        rules: {
            uni_desc: {
                required: true
            }

        },
        messages: {
            uni_desc: {
                required: "Campo Obrigatório"
            }

        }
    });

    $("#buscar6").buscar({
        url: baseUrl + '/medico-externo/buscar/prestador/L/prestador/H/',
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });
    
});


function mostraMedico() {
    $('#escondida').show();
    //var conv_codigo = $("#conv_codigo").val();
    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar-usuarios-saude/',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            mostraEspecialidade();
            return true;
        }
    });
}


function mostraEspecialidade() {
    var usr_codigo = $("#usr_codigo").val();
    var esp_codigo_sessao = $("#esp_codigo_sessao").val();
    var esp_codigo_selecionado = $("#esp_codigo_selecionado").val();
    if (esp_codigo_sessao != null || esp_codigo_sessao != "") {
        var selected = "selected=selected";
    } else {
        var selected = "";
    }
    $.ajax({
        url: baseUrl + '/relatorio/atendimento/carrega-especialidade-por-medico',
        type: "POST",
        data: {
            usr_codigo: usr_codigo
        },
        success: function(json) {
            //$("#esp").append("<option title='SELECIONE'>--SELECIONE--</option>");
            $("#esp").html("");
            $.each(json, function(key, value) {
                //alert(value['esp_codigo']);
                var especialidade = value['esp_codigo'];
                $("#esp_codigo_config").val(especialidade)
                // alert(especialidade);
                $("#esp").append("<option title=\"" + value['esp_nome'] + "\"  value=\"" + value['esp_codigo'] + "\" " + selected + ">" + value['esp_nome'] + "</option>");
                $("#med_esp").show('slow');



            });
        }
    });
}
