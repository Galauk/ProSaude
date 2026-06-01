$(function(){
    $(".paciente").click(function () {
        var usu_codigo = $("#usu_codigo").val();
        if (usu_codigo > 1) {
            var link = baseUrl + "/paciente/form-paciente/pessoa/" + usu_codigo + "/poupup/1";
        } else {
            link = baseUrl + "/default/paciente/form-paciente/poupup/1";
        }
        window.open(link, "name", "scrollbars=1,height=800,width=900", 'width=850,height=700');
    });
});

function confirmaParticipante() {
    console.log('teste mudou')
    var recebeNumeroDeParticipantes = $("#num_participantes").val();
    var usuNome = $("#part_nome").val();
    var usuCodigo = $("#part_codigo").val();
    var peso = $("#part_peso").val();
    var dtNasc = $("#part_dtnasc").val();
    var altura = $("#part_altura").val();
    var aval = $("#part_aval:checked").val();
    var fuma = $("#part_fuma:checked").val();
    var grupo = $("#part_grupo:checked").val();


    // $("#conf_part").val(cont);
    var cont = new Number($("#usus_part_qtd").val()) + 1;
    

    if (!usuCodigo) {
        return false;
    }
    if (aval == 1) {
        var avalEsc = "SIM";
        var class_aval = "aval";
    } else {
        var avalEsc = "NÃO";
        aval = "0";
    }
    if (fuma == 1) {
        var fumaEsc = "SIM";
    } else {
        var fumaEsc = "NÃO";
        fuma = "0";
    }
    if (grupo == 1) {
        var grupoEsc = "SIM";
    } else {
        var grupoEsc = "NÃO";
        grupo = "0";
    }

    var msgValida = validaInformacoes(peso, altura);

    if(msgValida !== ""){
        mensagem("Erro", msgValida, 280, 160);
    } else {
        if (validaConfirmacaoPart(usuCodigo) == 0) {
            $("#usus_part").show();
            $("#usus_part_qtd0").remove();
            $("#usus_part_qtd").val(cont);
            $("#usus_part").append(
                '<tr id="usu_part_qtd' + cont + '" class=\"participantes ' + class_aval + '\" >' +
                "   <td width='24%'>" + usuNome +
                "       <input type='hidden' name='usus_part[" + cont + "][usu_codigo]' value=\"" + usuCodigo + "\" />" +
                "   </td>" +
                "   <td align='center' width='15%'>" + dtNasc +
                "       <input type='hidden' name='usus_part[" + cont + "][gap_dt_nascimento]' value=\"" + dtNasc + "\" />" +
                "   </td>" +
                "   <td align='center' width='10%'>" + avalEsc +
                "       <input type='hidden' name='usus_part[" + cont + "][gap_avaliacao_alterada]' value=\"" + aval + "\" />" +
                "   </td>" +
                "   <td align='center' width='10%'>" + peso +
                "       <input type='hidden' name='usus_part[" + cont + "][gap_peso]' value=\"" + peso + "\" />" +
                "   </td>" +
                "   <td align='center' width='10%'>" + altura +
                "       <input type='hidden' name='usus_part[" + cont + "][gap_altura]' value=\"" + altura + "\" />" +
                "   </td>" +
                "   <td align='center' width='10%'>" + fumaEsc +
                "       <input type='hidden' name='usus_part[" + cont + "][gap_cessou_habito_fumar]' value=\"" + fuma + "\" />" +
                "   </td>" +
                "   <td align='center' width='10%'>" + grupoEsc +
                "       <input type='hidden' name='usus_part[" + cont + "][gap_abandonou_grupo]' value=\"" + grupo + "\" />" +
                "   </td>" +
                "   <td align='center' width='5%'>" +
                "       <a href='#' class='excluir'>" +
                '           <img src="' + baseUrl + '/public/images/icons/excluir.png" alt="Excluir" title="Excluir" onclick="excluirConfirmacaoPart(' + cont + ')" />' +
                "       </a>" +
                "   </td>" +
                "</tr>");
        } else {
            mensagem("Erro", "Participante já cadastrado", 250, 150);
        }
        $('html, body').animate({scrollTop: $('#usu_part_qtd' + cont).offset().top}, 'slow');

        $("#confirm_paciente").hide();

        $("#num_participantes").val(parseInt(recebeNumeroDeParticipantes)+ 1);

        $("#num_aval").val($(".aval").size());

        habilitaNovaConfirmacaoPart();
    }
}

function validaInformacoes(peso, altura) {
    var msg = "";
    //Peso
    if(peso != ""){
        peso = parseFloat(peso.replace(',', '.'));
        if(parseFloat(peso) < 0.5 || parseFloat(peso) > 500){
            msg = msg + " O Peso deve estar entre 0,5kg e 500kg <br/><br/>";
        }
    }
    //Altura
    if(altura != ""){
        altura = parseFloat(altura.replace(',', '.'));
        if(parseFloat(altura) < 20 || parseFloat(altura) > 250){
            msg = msg + " A Altura deve estar entre 20cm e 250cm";
        }
    }
    return msg;
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
    var recebeNumeroDeParticipantes = $("#num_participantes").val();
    
    $('html, body').animate({scrollTop: $('#usu_part_qtd' + id).offset().top}, 'slow');
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function () {
        $("#usu_part_qtd" + id).remove();
        $("#num_particip").val(parseInt(recebeNumeroDeParticipantes) -1);
        $("#num_aval").val($(".aval").size());
    });
}

function desativarGrupo(id) {
    confirme("Confirme:", "Deseja realmente desativar este grupo?", 300, 150, function () {
        $.ajax({
            url: baseUrl + "/programas-federais/grupo-atividade-coletiva/desativar?id=" + id,
            type: "DELETE",
            success: function(){
                mensagem("Sucesso", "Grupo desativado com sucesso!");
                setTimeout(function () {
                    location.reload(true);
                }, 500)
            }
        })
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
        $("#part_fuma").attr("checked", false);
        $("#part_grupo").attr("checked", false);
        $("#part_nome").focus();
    }, 150);
}

function formataPeso() {
    $('#part_peso').priceFormat({
        prefix: '',
        centsSeparator: ',',
        centsLimit: 3,
        thousandsSeparator: ''
    });
}