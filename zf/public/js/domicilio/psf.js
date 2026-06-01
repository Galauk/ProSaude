$(function(){
    
    if($("#prof_resp_codigo").val() != ""){
        carregaCnes($("#prof_resp_codigo").val());
    }
    
    $.validator.addMethod("validaPosse", function(validaBairro, element){
        if ($("#valid_66").val() == 1 ){ // se tiver marcado como rural ele entra no validador
            if($(".66").is(':checked') == true){ //se tem pelo menos uma opcao marcada
               return true;
            }
            return false;
        } else {
            return true;
        }
    },"Campo Obrigatório!");
    
    $("#form").validate({
        rules: {
            valid_57: { required: true },
            valid_58: { required: true },
            valid_66: { validaPosse: true },
            prof_resp: { required: true },
            cod_cnes_uni: { required: true }
        },
        messages: {
            valid_57: { required: "Campo Obrigatório!" },
            valid_58: { required: "Campo Obrigatório!" },
            prof_resp: { required: "Campo Obrigatório!" },
            cod_cnes_uni: { required: "Campo Obrigatório!" }
        }
    });
    
    $(".57").change(function(){
       $("#valid_57").val("1");
    });
    
    $(".58").change(function(){
       
        if($(".58:checked").val() == 84){
            $("#valid_66").val("1");
            $("#color_66").html("*");
        }else{
            $("#color_66").html("");
            $("#valid_66").val("0");
        }

       $("#valid_58").val("1");
    });
    
    if ($(".58:checked").val()==83){
        desabilitaAreaRural();
    } else {
        habilitaAreaRural();
    }
    
    if ($(".70:checked").val()==0){ desabilitaAnimais(); } else { habilitaAnimais(); }
    
    /*$(".70").change(function(){
        if($(this).val() == 0)
            $(".filhos").prop( "disabled", true );
        
        if($(this).val() == 1)
            $(".filhos").prop( "disabled", false );
    });*/
    
    $(".70").change(function(){
        if($(this).val() == 0) { desabilitaAnimais(); } else { habilitaAnimais(); }
    });
    
});

function desabilitaAreaRural(){
    $(".66").prop("disabled", true);
    $(".66").prop("checked", false);
}

function habilitaAreaRural(){
    $(".66").prop("disabled", false);
}

function desabilitaAnimais(){
    $(".filhos").prop("disabled", true);
    $(".filhos").prop("checked", false);
    $(".filhos").val("");
}

function habilitaAnimais(){
    $(".filhos").prop( "disabled", false );
}

function buscaProfResp() {
    $('#prof_resp').autocomplete({
        source: baseUrl + '/default/usuarios/buscar-profissionais-equipes',
        minLength: 3,
        delay: 300,
        open: function () {
            $(this).css("background", "none");
        },
        close: function () {
            $(this).css("background", "none");
        },
        select: function (event, ui) {
            $("#prof_resp").val(ui.item.label);
            $("#prof_resp_codigo").val(ui.item.id);
            carregaCnes(ui.item.id);
            //$("#cod_cnes_uni").val(validaCampoEmBranco(ui.item.data.cnes_numero));
            //$("#cod_equipe").val(validaCampoEmBranco(ui.item.data.nu_ine));
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li/>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
    }
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
                var codCnesEdit = $("#cod_cnes_edit").val();
                $.each(txt, function (key, value) {
                    // Valida Edição ao carregar a página
                    var selectedCnes = '';
                    if (codCnesEdit == value['uni_codigo']) { 
                        selectedCnes = "selected='selected'";
                    }
                    
                    $("#cod_cnes_uni").append("<option "+selectedCnes+" value=\""+value['uni_codigo']+"\" onclick='carregaIne("+value['uni_codigo']+","+usrCodigo+")'>"+value['uni_desc']+"\</option>");
                })
            }
        });
        carregaIne($("#cod_cnes_edit").val(),usrCodigo)
    }, 150);
}

function validaCampoEmBranco(texto){
    if (texto == "" || texto == null || texto == "null" || texto == "undefined"){
        return "";
    } else {
        return texto;
    }
}

function carregaIne(uniCodigo,usrCodigo){
    setTimeout(function () {
        $("#cod_equipe option").remove();
        $("#cod_equipe").removeAttr("disabled");
        $.ajax({
            url: baseUrl + "/default/usuarios/carrega-equipes",
            type: "POST",
            data: {
                uni_codigo: uniCodigo,
                usr_codigo: usrCodigo
            },
            success: function (txt) {
                //alert(txt);
                var codIne = $("#cod_equipe_ine").val();
                $.each(txt, function (key, value) {
                    var selectedIne = '';
                    if (codIne == value['co_seq_equipe']) { 
                        selectedIne = "selected='selected'";
                    }
                    $("#cod_equipe").append("<option "+selectedIne+" value=\""+value['co_seq_equipe']+"\">\n\
                                                "+value['nu_ine']+"\
                                              </option>");
                });
            }
        });
    }, 150);
}