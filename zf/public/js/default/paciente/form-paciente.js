var formValido = false;
var camposAbas = {};
$(function(){
    // Somente letras maiúsculas e minúsculas
    $("#nome").keyup(function() {
        var valor = $("#nome").val().replace(/[^a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÒÖÚÇÑ ]+/g,'');
        $("#nome").val(valor);
    });

    //faz cache da relacao dos elementos e abas
    $('.abas .abas-aba input, .abas .abas-aba select').each(function(i){
        var self = $(this);
        var aba = self.closest('.abas-aba').data('n');
        var nomeCampo = self.attr('name');
        if(!camposAbas.hasOwnProperty(aba)){
            camposAbas[aba] = [];
        }
        if(nomeCampo != undefined && nomeCampo.indexOf('[') == -1){
            camposAbas[aba].push(self.attr('name'));
        }
    });

    $("#pep_mae").keyup(function() {
        var valor = $("#pep_mae").val().replace(/[^a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÒÖÚÇÑ ]+/g,'');
        $("#pep_mae").val(valor);
    });

    //validaCadastroDeDomicilio();
    $(".pep_obito").change(function(){
        if($(this).val() == "t"){
            $("#data_obito").show();
        }else{
            $("#data_obito").hide();
        }
    });

    $("#usu_sit_rua").change(function(){
        if($(this).val() == "t"){
            $("#tempoEmRua").show();
        }else{
            $("#tempoEmRua").hide();
        }
    });

    carregaUnidadesProfissional();


    $('.masterTooltip').hover(function(){
            // Hover over code
            var title = $(this).attr('title');
            $(this).data('tipText', title).removeAttr('title');
            $('<p class="tooltip"></p>')
            .text(title)
            .appendTo('body')
            .fadeIn('slow');
    }, function() {
            // Hover out code
            $(this).attr('title', $(this).data('tipText'));
            $('.tooltip').remove();
    }).mousemove(function(e) {
            var mousex = e.pageX + 20; //Get X coordinates
            var mousey = e.pageY + 10; //Get Y coordinates
            $('.tooltip')
            .css({ top: mousey, left: mousex })
    });

    $("#estc_codigo").change(function(){
        if($("#estc_codigo").val() == 2 || $("#estc_codigo").val() == 5){
            $("#conjuge").show('low');
        }else{
            $("#conjuge").hide('low');
        }
    });

    if($("#usu_deficiencia:checked").val()=="f") {
        desabilitaDeficiencias();
    }
    if($("#usu_doenca:checked").val()=="f") {
        desabilitaDoencas();
    }

});



/*function carregaUnidadesProfissional(){
    var combo = "";
    var selected = "";
    $.ajax({
        url: baseUrl + '/default/unidade/get-unidades-por-profissional',
        data:{usr_codigo: $("#usr_codigo").val()},
        success:function(txt){
            combo += "<option value=\"\">Selecione</option>";
            for(var i in txt){
                if(txt[i].uni_codigo == txt[i].uni_login){
                    selected = "selected=selected";
                }else{
                    selected = "";
                }
                combo += "<option value=\""+txt[i].uni_codigo+"\" "+selected+">"+txt[i].uni_desc+"</option>";
            }
            combo += "</option>";
            $("#uni_codigo").html(combo);
        }
    });
}*/

function validaEspacoNome(){
    var nome = $("#nome").val();
    if(nome.indexOf(" ")==-1)  {
        mensagem("Atenção","Obrigatório Nome e Sobrenome e um espaço entre Nome do paciente!",300,150, function(){
            $("#nome").focus();
        });
        // console.log("focusou");
        //$("#nome").val("");
    }
}

function validaNomeMae(){
    var nomeMae = $("#pep_mae").val();
    if(nomeMae.indexOf(" ") === -1)  {
        mensagem("Atenção","Obrigatório Nome e Sobrenome e um espaço entre Nome da mãe!",300,150, function(){
            $("#pep_mae").focus();
        });
    }
}

function validaEspacamento(idElemento) {
    var el = $('#'+ idElemento);
    var valor = el.val();
    var espaco = "  ";
    var verificaPalavraComposta = false;

    for (var i=0; i<valor.length; i++) {
        if (valor.indexOf(espaco) !== -1) {
            var resposta = valor.replace(espaco, " ");
            el.val(resposta);
            verificaPalavraComposta = true;
        }
        espaco += " ";
    }

    if (verificaPalavraComposta) {
        validaEspacamento(idElemento);
    }
}

function carregaUnidadesProfissional(){
    var combo = "";
    var selected = "";
    var edit = $("#uni_codigo_edit").val();
    $.ajax({
        url: baseUrl + '/default/unidade/get-unidades-por-profissional',
        data:{usr_codigo: $("#usr_codigo").val()},
        success:function(txt){
            combo += "<option value=\"\">Selecione</option>";
            for(var i in txt){
                if(txt[i].uni_codigo == txt[i].uni_login){
                    selected = "selected=selected";
                }else{
                    selected = "";
                }
                combo += "<option value=\""+txt[i].uni_codigo+"\" "+selected+">"+txt[i].uni_desc+"</option>";
            }
            combo += "</option>";
            $("#uni_codigo").html(combo);
        }
    });
}

function buscaProfissionais() {
    $("#profs_part_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-profissionais-equipes',
            template : function(ul, item) {
                 return $("<li/>").data("item.autocomplete", item).append(
                             "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(event, ui){
                carregaEspecialidade(ui.item.id, $("#uni_codigo").val());
               //getTipoMedico();
               return true;
            }
    });

}

function carregaEspecialidade(usrCodigo, uniCodigo) {
    $("#profs_part_esp option").remove();
    $("#td_profs_part_esp").show();
    $("#td_profs_part_conf").show();

    $.ajax({
        url: baseUrl + "/default/especialidade/lista-especialidade-por-profissional",
        type: "POST",
        data: {
            usrCodigo: usrCodigo,
            uniCodigo: uniCodigo
        },
        success: function (txt) {
            $.each(txt, function (key, value) { 
                $("#profs_part_esp").append(
                    "<option title=\"" + value['esp_nome'] + "\" value=\"" + value['cod_cbo'] + "\">" +value['esp_nome'] + "</option>"
                );
            })
            carregaCnes(usrCodigo)
        }
    });
}


function carregaCnes(usrCodigo){
    $("#cod_cnes_uni option").remove();
    $("#cod_cnes_uni").removeAttr("disabled");
    $.ajax({
        url: baseUrl + "/default/unidade/carrega-cnes",
        type: "POST",
        data: {
            usr_codigo: usrCodigo
        },
        success: function (txt) {
            console.log(txt)
            var codCnesEdit = $("#cod_cnes_edit").val();
            if(txt.length > 1){
                $("#cod_cnes_uni").append("<option value=''>Selecione</option>");
                carregaIne(txt[0].uni_codigo, usrCodigo)
            }
            var checked = "";

            $.each(txt, function (key, value) {
                checked = "";
                console.log(codCnesEdit == value['uni_cnes'] || txt.length == 1)
                if(codCnesEdit == value['uni_cnes'] || txt.length == 1){
                    checked = "selected=selected";
                    carregaIne(value['uni_codigo'], usrCodigo);
                }
                $("#cod_cnes_uni").append("<option "+checked+" value=\""+validaCampoEmBranco(value['uni_cnes'])+"\" onclick='carregaIne("+value['uni_codigo']+","+usrCodigo+")'>"+value['uni_desc']+"\</option>");
            })
        }
    });
    
}

function carregaIne(uniCodigo, usrCodigo){
    console.log(uniCodigo);
    console.log(usrCodigo);
    
    $("#cod_equipe option").remove();
    $("#cod_equipe").removeAttr("disabled");
    $.ajax({
        url: baseUrl + "/default/usuarios/carrega-equipes",
        type: "POST",
        data: {
            uniCodigo: uniCodigo,
            usrCodigo: usrCodigo
        },
        success: function (txt) {
            var recebeCodigoIne = txt;

            if (recebeCodigoIne == null){
                $('#cod_equipe').append(`<option>Sem Cód INE</option>`)
            }
            
            $('#cod_equipe').append(`<option>${recebeCodigoIne}</option>`)
        }
    });
}

function validaCampoEmBranco(texto){
    if (texto == "" || texto == null || texto == "null" || texto == "undefined"){
        return "";
    } else {
        return texto;
    }
}

function  buscarOcupacoes(){
    $("#no_ocupacao").buscar({
        url: baseUrl+'/default/paciente/buscar-ocupacao/',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(event, ui){
            return true;
        }
    });
}

function getProntuarioDuplicado(val){
    $.ajax({
        url: baseUrl+"/default/paciente/get-prontuario-duplicado/",
        type: "GET",
        data: {
            prontuario: val
        },
        success: function(json){
            if(json >= 1){
                $("#pep_prontuario_val").val(1);
            }else{
                $("#pep_prontuario_val").val(0);
            }
        }
     });
}

function validaCdastro(){
    $.validator.addMethod("validaBairro", function(validaBairro, element){
        if ($("#rua_nome").val() != "" && $("#rua_bairro").val() == ""){
            return false;
        } else {
            return true;
        }
    },"Campo Obrigatório!");

    $.validator.addMethod("validaSexo", function(validaSexo, element){
        if ($("#pep_sexo").val() != "" && $("#pep_sexo").val() == ""){
            return false;
        } else {
            return true;
        }
    },"Campo Obrigatório!");

    $.validator.addMethod("validaResponsavel", function(validaResponsavel, element){
        if ($("#rua_nome").val() != "" && $("#proprio_responsavel:checked").val() == "N"){
            if($("#usu_nome_responsavel").val() == "") {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    },"Campo Obrigatório!");

    $.validator.addMethod("validaNumDomicilio", function(validaNumDomicilio, element){
        if ($("#rua_nome").val() != "" && $("#dom_numero").val() == ""){
            return false;
        } else {
            return true;
        }
    },"Campo Obrigatório!");

    $.validator.addMethod("validaCpf", function(validaCpf, element){
        //var strCPF = "06069060962"; alert(TestaCPF(strCPF))
        var cpf = $("#cnpj_cpf").val()
        cpf = cpf.replace(".","").replace(".","").replace("-","");
        //alert(TestaCPF(cpf));
        if (TestaCPF(cpf) || cpf == ""){
            return true;
        } else {
            return false;
        }
    },"Cpf Inválido");

    $.validator.addMethod("validaProntuario", function(validaProntuario, element){
        if ($("#pep_prontuario_val").val() == "1"){
            return false;
        } else {
            return true;
        }
    },"Prontuario ja Existe");

    $.validator.addMethod("validaCep", function(validaCep, element){
        if ($("#rua_nome").val() != "" && $("#rua_cep").val() == ""){

            return false;
        } else {

            return true;
        }
    },"Campo Obrigatório!");

    $.validator.addMethod("validaRua", function(validaRua, element){
        //alert($("#rua_nome").val()+ "!=" +""+ "+&&"+ $("#dom_numero").val() +"!="+ ""+" && "+$("#rua_codigo").val()+ "=="+ "");
        if ($("#rua_nome").val() != "" && $("#dom_numero").val() != "" && $("#rua_codigo").val() == ""){
            return false;
        } else {
            return true;
        }
    },"Campo Obrigatório!");

    $.validator.addMethod("validaOrgaoEmissor", function(validaOrgaoEmissor, element){
        var orgaoemissor = $("#orgaoemissor").val().length;
        if (orgaoemissor > 6){
            return false;
        } else {
            return true;
        }
    },"Máximo de 6 digitos!");

    $.validator.addMethod("validaDeficiencia", function(validaDeficiencia, element){
        var deficiencia = $("#usu_deficiencia::checked").val();
        if (deficiencia == "t" && ($("#conf_def").val() == "" || $("#conf_def").val() == 0)){
            return false;
        } else {
            return true;
        }
    },"Selecione a(s) deficiência(s)");
    $.validator.addMethod("validaDoenca", function(validaDoenca, element){
        var doenca = $("#usu_doenca::checked").val();
        if (doenca == "t" && ($("#conf_doenca").val() == "" || $("#conf_doenca").val() == 0)){
            return false;
        } else {
            return true;
        }
    },"Selecione a(s) doênça(s)");
    formValido = $("#form").validate({
        rules: {
            nome:{required: true,  minlength: 4},
            pep_cartao_sus:{required: true},
            pep_prontuario:{required:$('#pep_prontuario').hasClass('obrigatorio'),validaProntuario:true},
            datanascimento:{required: true},
            pep_mae:{required: true, minlength: 4},
            pep_sexo:{required: true},
            rua_nome:{required:true},
            rua_bairro:{ validaBairro:true },
            rua_cep:{validaCep:true},
            rua_codigo:{validaRua:true},
            dom_numero:{validaNumDomicilio:true},
            uni_codigo:{required:true},
            cnpj_cpf:{required:false, validaCpf:true},
            orgaoemissor : {validaOrgaoEmissor:true},
            rac_codigo : {required:true},
            usr_codigo : {required:true},
            cd_nacionalidade:{required:true},
            pep_frenquencia_escolar:{required:true},
            usu_sit_rua:{required:true},
            usu_deficiencia: {required:true},
            conf_def: {validaDeficiencia:true},
            conf_doenca: {required:true, validaDoenca:true},
            usu_nome_responsavel: {validaResponsavel:true},
        },
        messages: {
            pep_cartao_sus: { required: "Campo Obrigatório",minlength : "Coloque no minimo 4 caracteres" },
            pep_prontuario:{ required: "Campo Obrigatório" },
            nome: { required: "Campo Obrigatório", minlength : "Coloque no minimo 4 caracteres" },
            datanascimento:{ required: "Campo Obrigatório" },
            pep_mae:{ required: "Campo Obrigatório" ,minlength : "Coloque no minimo 4 caracteres"},
            pep_sexo:{ required: "Campo Obrigatório" },
            rg:{ required: "Campo Obrigatório" },
            dataemissao:{ required: "Campo Obrigatório" },
            cnpj_cpf:{ required: "Campo Obrigatório" },
            uni_codigo:{required: "Campo Obrigatório"},
            rac_codigo:{required:"Campo Obrigatório"},
            usr_codigo:{required:"Campo Obrigatório"},
            cd_nacionalidade:{required:"Campo Obrigatório"},
            pep_frenquencia_escolar:{required:"Campo Obrigatório"},
            usu_sit_rua:{required:"Campo Obrigatório"},
            usu_deficiencia: { required: "Campo Obrigatório" },
            rua_nome: { required: "Campo Obrigatório" },
        },
        submitHandler: function() { salvarCadastro(); }
    });
    validaAbas();
}

function habilitaCidade(){
    var nacionalidade = $("#cd_nacionalidade option:selected").val();
    if (nacionalidade == "B"){
        $("#div_cidade").show();
    } else {
        $("#div_cidade").hide();
    }
}

function desabilitaDeficiencias(){
    $("#deficiencias").each(function (indice) {
        $(this).find('input[type="checkbox"]').each(function (indice) {
            $("#co_pergunta_detalhe"+$(this).val()).prop('checked', false);
            $("#co_pergunta_detalhe"+$(this).val()).attr("disabled",true);
        });
    });
}

function desabilitaDoencas(){
    $("#doencas").each(function (indice) {
        $(this).find('input[type="checkbox"]').each(function (indice) {
            $("#co_pergunta_detalhe"+$(this).val()).prop('checked', false);
            $("#co_pergunta_detalhe"+$(this).val()).attr("disabled",true);
        });
    });
}

function habilitaDeficiencias(){
    $("#deficiencias").each(function (indice) {
        $(this).find('input[type="checkbox"]').each(function (indice) {
            $("#co_pergunta_detalhe"+$(this).val()).removeAttr("disabled");
        });
    });
}
function habilitaDoencas(){
    $("#doencas").each(function (indice) {
        $(this).find('input[type="checkbox"]').each(function (indice) {
            $("#co_pergunta_detalhe"+$(this).val()).removeAttr("disabled");
        });
    });
}

function validaDeficiencias() {
    var cont = 0;
    $("#deficiencias").find("input[type=checkbox][name='deficiencias[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_def").val(""); } else { $("#conf_def").val(cont); }
}
function validaDoencas() {
    var cont = 0;
    $("#doencas").find("input[type=checkbox][name='doencas[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_doenca").val(""); } else { $("#conf_doenca").val(cont); }
}

function addRua(){
    window.open(baseUrl + "/rua/novo/popup/1","_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
}

function editarRua(){
    var rua_codigo = $("#rua_codigo").val();
    if(rua_codigo){
        window.open(baseUrl + "/rua/editar/popup/1/id/"+rua_codigo,"_blank", "scrollbars=1,height=800,width=900",'width=850,height=700');
    }else{
        mensagem("Atenção","Rua não selecionada para edição",300,150);
    }
}

function retornaRua(id,nome,cep,bai_codigo,bai_nome,cid,dist){
    //alert("haaaa mulek");
    $("#rua_cep").val(cep);
    $("#rua_codigo").val(id);
    $("#rua_nome").val(nome);
    $("#bai_codigo").val(bai_codigo);
    $("#rua_bairro").val(bai_nome);
    $("#localidade").val( cid + " - Distrito: "+dist);
    $("#rua_cep").prop('readonly', true);
    getEnderecos();
    //$("#rua_cep").prop('readonly', true);
}

function validaPaciente(){
    if($("#novo").is(" :checked")){
        $("#nome").autocomplete({disabled: true});
        $("#nome").val("");
        $("#nome").focus();
    }else{
        $("#nome").autocomplete({disabled: false});
        $("#nome").val("");
        $("#nome").focus();
        $("#nome").buscar({
            url: baseUrl+'/default/paciente/buscar-pessoa/',
            template : function(ul, item) {
                        if(item.data.datanascimento == "" || item.data.datanascimento == "undefined"){
                            item.data.datanascimento = "Sem Informação";
                        }else{
                            item.data.datanascimento = dataToBr(item.data.datanascimento);
                        }

                        if(item.data.cnpj_cpf == "" || item.data.cnpj_cpf == "undefined"){
                            item.data.cnpj_cpf = "Sem Informação";
                        }

                        return $("<li/>").data("item.autocomplete", item).append("<a><strong>" + item.label + "</strong>"
                                + "<br><strong>Data Nasc.:</strong> "
                                + item.data.datanascimento
                                + " <strong>CPF:</strong> " + item.data.cnpj_cpf
                                + "</a>&nbsp;").appendTo(ul);
            },
            callback: function(event, ui){
                    return true;
            }
        });
    }
}

function buscaCidade(){
    $("#cidade").buscar({
        url: baseUrl+'/default/cidade/buscar/',
        template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
        callback: function(event, ui){
            getEnderecos();
        }
    });
}

function buscaCidades(){
    $("#cidade").buscar({
        url: baseUrl+'/cidade/buscar/',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>"+item.label+" - "+item.data.uf_sigla+"</a>").appendTo(ul);
        },
        callback: function(ul, item){
            return false;
        }
    });
}

function buscaEstado(){
    $("#uf").buscar({
        url: baseUrl+'/default/estado/buscar-por-nome/',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(ul, item){
            return false;
        }
    });
}

function clearCidade(){
    var string  = $("#cidade").val();
    if(string.length == 0){
        $("#cid_codigo").val("");
    }
}

function clearRua(){
    setTimeout(function() {
        if($("#rua_nome").length == 0){
            //mensagem("Atenção","Rua não localizada",300,150);
            $("#editar_rua").hide();
            $("#rua_codigo").val("");
            $("#rua_cep").val("");
            $("#rua_nome").val("");
            $("#rua_nome").focus();

        }
    }, 500);

}




function clearBairro(){
    var string  = $("#rua_bairro").val();
    if(string.length == 0){
        $("#bai_codigo").val("");
    }
}

function buscarRua(){
    console.log("oi paciente")
    $("#rua_codigo").val("");
    //$("#rua_cep").val("");
    $("#rua_nome").buscar({
        url: baseUrl+'/rua/buscar',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + ""
                    + "<br/><strong>Bairro:</strong>"+ item.data.bai_nome
                    + "</a>&nbsp;").appendTo(ul);
        },
        callback: function(event,ui){
            $("#rua_codigo").val(ui.item.id);
            $("#rua_cep").val(ui.item.data.rua_cep);
            $("#rua_bairro").val(ui.item.data.bai_nome);
            $("#bai_codigo").val(ui.item.data.bai_codigo);
            $("#rua_bairro").attr("disabled");
            $("#localidade").val( ui.item.data['cid_nome'] + " - Distrito: "+ui.item.data['dis_nome']);
            $("#editar_rua").show();
            getEnderecos();
        }
    });
}

function getEnderecos(){
    //alert($("#dom_numero_b").val());
     $.ajax({
        url: baseUrl+"/default/paciente/buscar-numeros-de-domicilio-por-endereco/",
        type: "POST",
        data: {
            rua_codigo: $("#rua_codigo").val(),
            rua_cep: $("#rua_cep").val(),
            rua_bairro: $("#rua_bairro").val(),
            dom_numero: $("#dom_numero").val(),
            cid_codigo: $("#cid_codigo").val(),
            co_tipo_logradouro: $("#co_tipo_logradouro").val(),
            rua_nome:$("#rua_nome").val()
        },
        success: function(json){
              var tr = "<tr class=\"notfirst\">"+
                            "<th class=\"gridtable\">Logradouro </th>"+
                            "<th class=\"gridtable\">Nº </th>"+
                            "<th class=\"gridtable\">CEP </th>"+
                            "<th class=\"gridtable\">Bairro </th>"+
                            "<th class=\"gridtable\">Responsável </th>"+
                            "<th class=\"gridtable\">Opções </th>"+
                        "</tr>";
              for(var i in json){
                  if(json[i].usu_nome == "null" || json[i].usu_nome == "" || json[i].usu_nome == null){
                      json[i].usu_nome = "Não Informado";
                  }
                  if(json[i].dom_numero == "null" || json[i].dom_numero == "" || json[i].dom_numero == null || json[i].dom_numero == 0){
                      json[i].dom_numero = "S/N";
                  }

                  tr += "<tr class=\"regis notfirst hover_class\" >"+
                            "<td class=\"gridtable\">"+json[i].rua_nome+"</td>"+
                            "<td class=\"gridtable\">"+json[i].dom_numero+"</td>"+
                            "<td class=\"gridtable\">"+json[i].rua_cep+"</td>"+
                            "<td class=\"gridtable\">"+json[i].rua_bairro+"</td>"+
                            "<td class=\"gridtable\">"+json[i].usu_nome+"</td>"+
                            "<td class=\"gridtable\" width=\"50\"><img src=\""+baseUrl+"/public/images/selecionar_on.jpg\" onclick=\"selecionaEndereco('"+json[i].dom_numero+"','"+json[i].co_tipo_logradouro+"','"+json[i].ds_tipo_logradouro+"','"+json[i].rua_nome+"','"+json[i].rua_codigo+"','"+json[i].rua_cep+"','"+json[i].rua_bairro+"','"+json[i].dom_codigo+"','"+json[i].usu_codigo+"','"+json[i].usu_nome+"','"+json[i].cid_nome.replace("'", "")+"','"+json[i].cid_codigo+"','"+json[i].bai_codigo+"')\"></td>"+
                        "</tr>";
              }
              $("#results").html(tr);
        }
     });
}

function changeSn(){
    alert($(this).val());
}

function selecionaEndereco(dom_numero,co_tipo_logradouro,ds_tipo_logradouro,rua_nome,rua_codigo,rua_cep,rua_bairro,dom_codigo,usu_codigo,usu_nome,cid_nome,cid_codigo,bai_codigo){
    if(bai_codigo == "null"){
        bai_codigo = "";
        rua_bairro = "";
    }
    $("#rua_nome").val(rua_nome);
    $("#rua_cep").val(rua_cep);
    $("#rua_bairro").val(rua_bairro);
    $("#dom_numero").val(dom_numero);
    $("#dom_codigo").val(dom_codigo);
    $("#usu_nome_responsavel").val(usu_nome);
    $("#usu_codigo").val(usu_codigo);
    $("#rua_codigo").val(rua_codigo);
    $("#bai_codigo").val(bai_codigo);
    //$("#co_tipo_logradoro").val(co_tipo_logradouro);

    $('select#co_tipo_logradoro').find('option').each(function() {
        //alert(this.val());
        if(co_tipo_logradouro == $(this).val()){
            $(this).attr("selected","selected");
        }
    });
    //$("#cidade").val(cid_nome);
    //$("#cid_codigo").val(cid_codigo);
    $("#dom_complemento").removeAttr("readonly");
    $("#busca-endereco").remove();
    $("#results").html("");
    $(".campos_dom").prop('readonly', true);
    $(".campos_dom_combo").attr("disabled");

    $("#acoes").show("slow");
    return false;
}


function inativaResponsavel(){
    if($("#proprio_responsavel:checked").val() == "S"){
        $("#usu_nome_responsavel").prop('readonly', true);
        $("#usu_codigo").val("");
        $("#usu_nome_responsavel").val("");
    }else{
        $("#usu_nome_responsavel").removeAttr("readonly");
    }

}

/*function selecionaEnderecoButton(){

    //alert($("tr.regis").length > 0);
    //alert($("tr.selecionada").length <= 0);

    if($("tr.selecionada").length > 0){
        $("#dom_complemento").removeAttr("readonly");
        selecionaEndereco();
    }else if($("tr.notfirst").length <= 1){

       var error = false;
       var msg = "";
       if($("#cid_codigo").val() == ""){
           error = true;
           msg += "Preencha a Cidade. ";
       } else if($("#rua_nome_b").val() == ""){
           error = true;
           msg += "Preencha o nome da rua. ";
       }else if($("#rua_cep_b").val() == ""){
           error = true;
           msg += "Preencha o CEP. ";
       }else if($("#rua_bairro_b").val() == ""){
           error = true;
           msg += "Preencha o Bairro. ";
       }else if($("#dom_numero_b").val() == ""){
           error = true;
           msg += "Preencha o Número do domicilio. ";
       }else if($("#co_tipo_logradouro_b").val() == ""){
           error = true;
           msg += "Preencha o Tipo de Logradouro. ";
       }

       if(error){
           $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">"+msg+"</div>")
             $("#mensagem-dialog").dialog({
                     modal: true,
                     width: 250,
                     height: 150,
                     close: function(){
                             $(this).remove();
                     },
                     buttons: {
                             "Ok": function(){
                                $(this).dialog('close');
                             }
                     }
             });
            return false;
       }

       $("#rua_nome").val($("#rua_nome_b").val());
       $("#rua_cep").val($("#rua_cep_b").val());
       $("#rua_bairro").val($("#rua_bairro_b").val());
       $("#dom_numero").val($("#dom_numero_b").val());
       $("#co_tipo_logradouro").val($("#co_tipo_logradouro_b").val());
       $("#ds_tipo_logradouro").val( $('option:selected', $("#co_tipo_logradouro_b")).text());
       $("#usu_nome_responsavel").val($("#usu_nome").val());
       //alert($("#usu_codigo").val());
       $("#usu_codigo_responsavel").val($("#usu_codigo").val());
       $(".campos_dom").removeAttr("readonly");
       $(".campos_dom").addClass("ui-state-default");
       $("#dom_complemento").removeAttr("readonly");
       $("#busca-endereco").remove();
    }else if($("tr.regis").length > 0 && $("tr.selecionada").length <= 0){
         $("body").append("<div id=\"mensagem-dialog\" title=\"Erro\">Não há campos selecionados</div>")
             $("#mensagem-dialog").dialog({
                     modal: true,
                     width: 250,
                     height: 150,
                     close: function(){
                             $(this).remove();
                     },
                     buttons: {
                             "Ok": function(){
                                $(this).dialog('close');
                             }
                     }
             });
            return false;
    }
    return true;
}*/

function marca(e){
    $(".selecionada").each(function(){
        $(this).removeClass("selecionada");
    });

    $(e).addClass("selecionada");
}

function liberaCampos(){
    $(".campos_dom").removeAttr("readonly");
    $(".campos_dom_combo").removeAttr("disabled");
    $("#editar_rua").show();
    $("#add_rua").show();
    $("#acoes").hide("slow");
}

function informaSn(e){
    var checado = false;
    if($(e).attr("checked")=="checked"){
       checado=true;
    }else{
       checado = false;
    }

    if(checado){
        $("#dom_numero").val("0");
        getEnderecos();
        $("#dom_numero").val("S/N");
        $("#dom_numero").attr("disabled");
    }else{
        $("#dom_numero").removeAttr("disabled");
        $("#dom_numero").val("");
        //getEnderecos();
    }

}

function removeDom(){
    $("#dom_codigo").val("");
    $("#dom_numero").val("");
    $("#usu_nome_responsavel").val("");
    $("#usu_codigo").val("");
    $("#rua_codigo").val("");
    $("#rua_nome").val("");
    $("#rua_cep").val("");
    $("#rua_bairro").val("");
    $("#bai_codigo").val("");
    $("#dom_complemento").val("");
    $("#dom_ponto_referencia").val("");
    $("#dom_telefone").val("");
    $("#acoes").hide("slow");
     $("#add_rua").show();
    $(".campos_dom").removeAttr("readonly");
    $(".campos_dom_combo").removeAttr("disabled");
}

function salvarCadastro(){
    var metodo = "";
    if($("#aise").val() == 1){
        metodo = "salvar";
    }else{
        metodo = "salvar-usuario";
    }
    var poupup = $("#poupup").val();
    mensagemSemOk("carregando-ate", "Aguarde", "Carregando...", 280, 80);
    var usu_nome = $("#nome").val();
    var valoresForm = $('#form').serialize();
    //alert("aa");
    $.ajax({
        url: baseUrl+"/default/paciente/lista-cadastros-duplicados/",
        type: "POST",
        data: {
            nome: $("#nome").val(),
            datanascimento: $("#datanascimento").val(),
            pep_mae: $("#pep_mae").val()
        },
        success: function(txt){
            if (txt != "" && $("#pessoa-edita").val() == "") { // && $("#pessoa-edita").val() == "" condição antiga feita pelo vinícius Ps:sem nexo mas talvez de pau um dia
                $("body").append("<div id='conf-cadastro' title='Conferência de cadastro duplicado!' ></div>");
                var table = "<table width=\"100%\" class=\"gridtable hoverTable\">"+
                                "<tr class=\"notfirst\">"+
                                    "<th class=\"gridtable\">Prontuário</th>"+
                                    "<th class=\"gridtable\">Nome</th>"+
                                    "<th class=\"gridtable\">Data Nascimento</th>"+
                                    "<th class=\"gridtable\">Nome Mãe</th>"+
                                    "<th class=\"gridtable\">Situação</th>"+
                                "</tr>";
                for(var i in txt){
                    table +=  "<tr class=\"regis notfirst hover_class\" data-pessoa=\""+i+"\" onclick=\"marca(this)\">"+
                                    "<td class=\"gridtable\">"+txt[i].prontuario+"</td>"+
                                    "<td class=\"gridtable\">"+txt[i].nome+"</td>"+
                                    "<td class=\"gridtable\">"+txt[i].datanascimento+"</td>"+
                                    "<td class=\"gridtable\">"+txt[i].pep_mae+"</td>"+
                                    "<td class=\"gridtable\">"+txt[i].inativo+"</td>"+
                                "</tr>";
                }
                table += "</table>";

                $("#conf-cadastro").html(table);
                $("#conf-cadastro").dialog({
                    modal:true,
                    width: 800,
                    height: 500,
                    close: function(){
                            //$(this).remove();
                    },
                    buttons: {
                        "Atualizar Cadastro":function(){
                            if($("tr.selecionada").length <= 0){
                                $("body").append("<div id=\"mensagem-dialog-usuarios\" title=\"Erro\">Não há itens selecionados</div>")
                                    $("#mensagem-dialog-usuarios").dialog({
                                            modal: true,
                                            width: 250,
                                            height: 150,
                                            close: function(){
                                                    $(this).remove();
                                            },
                                            buttons: {
                                                    "Ok": function(){
                                                       $(this).dialog('close');
                                                    }
                                            }
                                    });
                                   return false;
                           }else{
                               var url = "";
                               if(poupup == 1){
                                   url = baseUrl + "/default/paciente/form-paciente/pessoa/"+$("tr.selecionada").data("pessoa")+"/poupup/1";
                               }else{
                                   url = baseUrl + "/default/paciente/form-paciente/pessoa/"+$("tr.selecionada").data("pessoa");
                               }
                                window.location = url;
                           }
                        },
                        "Efetuar Cadastro":function(){
                            $.ajax({
                                url: baseUrl+"/default/paciente/"+metodo+"/",
                                type: "POST",
                                data: valoresForm,
                                success: function(txt){
                                    if(txt.id == "" || txt.id == null || txt.id == "undefined"){
                                        $("#conf-cadastro").dialog("destroy").remove();
                                        mensagem("Erro!",txt,300,150,function(){sucesso_salvar()});
                                    } else {
                                        $("#conf-cadastro").dialog("destroy").remove();
                                        mensagem("Confirmação de Cadastro",txt.msg,300,150,function(){sucesso_salvar(txt.id,usu_nome,poupup)});
                                        $(":text").each(function () {
                                            $(this).val("");
                                        });
                                   }
                                }
                            });
                        },
                        "Cancelar Cadastro":function(){
                           window.location = "../../../../WebSocialSaude/paciente.php";
                        }//"../../portadeEntrada/ordem.php"
                    }
                });
                //.load(baseUrl+"/default/paciente/lista-cadastros-duplicados/",{ "dadosPessoa[]": [$('#nome').val(), $('#datanascimento').val(), $('#pep_mae').val()]});
            } else {
                //return false;
                if ($("#pessoa-edita").val() == ""){
                    endUrl = baseUrl+"/default/paciente/"+metodo+"/";
                } else {
                    endUrl = baseUrl+"/default/paciente/"+metodo+"/pessoa/"+$("#pessoa-edita").val()+"/";
                }
                $.ajax({
                    url: endUrl,
                    type: "POST",
                    data: valoresForm,
                    success: function(txt){
                        console.log(txt);
                        if(txt.id == "" || txt.id == null || txt.id == "undefined"){
                            $("#conf-cadastro").dialog("destroy").remove();
                            mensagem("Erro!",txt,300,150,function(){
                                fecharMensagemSemOk("carregando-ate");
                            });
                        } else {
                            $("#conf-cadastro").dialog("destroy").remove();
                            fecharMensagemSemOk("carregando-ate");

                            mensagem("Confirmação de Cadastro",txt.msg,300,150, function(){
                                sucesso_salvar(txt.id,usu_nome,poupup);
                            });
                            if ($("#pessoa-edita").val() == "") {
                                $(":text").each(function () {
                                    $(this).val("");
                                });
                            }
                        }
                    }
                 });
            }
        }
    })
}

function sucesso_salvar(id,usu_nome,poupup){

    if(poupup == 1){
        window.opener.retornaPac(id,usu_nome.toUpperCase());
        window.close();
    }else{
        //window.location = baseUrl + "/default/paciente/form-paciente"+param
        //alert();
        window.location = baseUrl+"/../paciente.php";
    }
}
function validaAbas(){
    for(var aba in camposAbas){
        // console.log(aba);
        for(var campo in camposAbas[aba]){
            if(formValido.invalid.hasOwnProperty(camposAbas[aba][campo])){
                $(".abas").tabs("select", Number(aba));
                return;
            }
        }
    }
   // console.log(formValido);
}

function buscarCep(){

    $("#rua_cep").buscar({
        url: baseUrl+'/default/paciente/buscar-cep/',
        //suffix: '_solicitante',
        template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
                    $("#rua_cep").val("");
                    $("#rua_cep").val($("#rua_cep_hidden").val());
                    return true;
        }
    });
}

function buscaRua(){
    $("#rua_nome").buscar({
            url: baseUrl+'/default/paciente/buscar-rua/',
            //suffix: '_solicitante',
            template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(){
                /*$("#rua_cep").val("");
                $("#rua_cep").val($("#rua_cep_hidden").val());*/
                return true;
            }
    });

}

function buscaBairro(){
    $("#bai_codigo").val("");
    $("#rua_bairro").buscar({
            url: baseUrl+'/default/bairro/buscar/',
            //suffix: '_solicitante',
            template : function(ul, item) {
                    return $("<li/>").data("item.autocomplete", item).append(
                            "<a>" + item.label + "</a>").appendTo(ul);
            },
            callback: function(){
                /*$("#rua_cep").val("");
                $("#rua_cep").val($("#rua_cep_hidden").val());*/
                getEnderecos()
                return true;
            }
    });

}

function buscarNumerosDeDomicilioPorEndereco(){
    $("#dom_numero").buscar({
        url: baseUrl+'/default/paciente/buscar-numeros-de-domicilio-por-endereco/rua_nome/'+$("#rua_nome").val()+'/rua_bairro/'+$("#rua_bairro").val()+'/rua_cep/'+$("#rua_cep").val()+'/',
        template : function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                +"<a><strong>"+item.label+"</strong>"
                +"</a>&nbsp;").appendTo(ul);
        },
        callback: function(event, ui){
            return true;
        }
    });
}



function buscarResponsavelModal(){
   $("#usu_nome_responsavel").buscar({
            url: baseUrl+'/paciente/buscar',
           template : function(ul, item) {
                return jQuery("<li></li>").data("item.autocomplete", item).append(
                        "<a><strong>" + item.label + "</strong>"
                        + "<br><strong>Data Nasc.:</strong> "
                        + item.data.usu_datanasc
                        + " <strong>Mãe:</strong> " + item.data.usu_mae
                        + "</a>&nbsp;").appendTo(ul);
            },
           suffix:'-2',
           callback: function(event,ui){
                $("#usu_codigo").val(ui.item.id);
                //getEnderecos();
                verificaVinculosDomicilios(ui.item.id);
           }

    });
}


function verificaVinculosDomicilios(usu_codigo){
    $.ajax({
        url: baseUrl+ "/paciente/verifica-vinculos-domicilio",
        data:{usu_codigo:usu_codigo},
        success: function (txt){
            var dom_codigo = "";
            if(txt.length > 0){
                var msg = "O paciente selecionado possui vinculo nos domicilios";
                msg += "<table width='100%' border=1>"+
                            "<tr>"+
                                "<th>Rua</th>"+
                                "<th>Numero</th>"+
                                "<th>Bairro</th>"+
                                "<th>Situação</th>"+
                            "</tr>";
                for (var i in txt){
                    if(dom_codigo !== txt[i].dom_codigo){
                        dom_codigo = txt[i].dom_codigo;

                        msg += "<tr>"+
                                    "<td>"+txt[i].ds_tipo_logradouro+" "+txt[i].rua_nome+"</td>"+
                                    "<td>"+txt[i].dom_numero+"</td>"+
                                    "<td>"+(txt[i].bai_nome == null ? "Sem bairro" : txt[i].bai_nome)+"</td>"+
                                    "<td>"+(txt[i].usu_codigo_responsavel == null ? "Integrante" : "Responsável")+"</td>"+
                                "</tr>";
                    }
                }
                msg += "</table>";
                msg += "<br/> <font color='red'>AVISO: Ao selecionar este cidadão como responsável ele se tornará integrante e responsável do domicilio informado!</font>";
                mensagem("Aviso",msg,600,300);
            }
        }
    });
}
//busca o cep
function busca(){
    //alert(s.indexOf("-") != -1);
    var cep = $.trim($('#rua_cep_b').val());
    if (cep.length >= 8 && cep.indexOf("-")==-1){
        $('#erro-cep').show();
    }
    if(cep.length >= 9){
        mensagemSemOk("carregando-log","Carregando...","Carregando dados do logradouro",250,100);
        var url = 'http://clareslab.com.br/ws/cep/json/'+cep+'/';
        $.post(url,{
            cep:cep
        },
        function (rs) {
            rs = $.parseJSON(rs);
            if(rs != 0){
                address = rs.endereco + ', ' + rs.bairro + ', ' + rs.cidade + ', ' + ', ' + rs.uf;
                $('#rua_nome_b').val(rs.endereco);
                $('#rua_bairro_b').val(rs.bairro);
                //validaBairro();
                buscaCidadePeloNome(strReplaceChr(rs.cidade));
                //$('#cidade').val(rs.cidade);
                //$('#uf').val(rs.uf);
                //validaEstado();
                $('#erro-cep').hide();
                //$('#num').focus();
                last_cep = cep;
                fecharMensagemSemOk("carregando-log");
            } else{
                $('#erro-cep').show();
                $('#cep').focus();
                last_cep = 0;
                fecharMensagemSemOk("carregando-log");
            }
        })
    }
   // setTimeout(function() { getEnderecos()}, 500);
}

function buscaCidadePeloNome(cidade){
    $.ajax({
        url:baseUrl+"/default/cidade/busca-cidade-pelo-nome",
        type: "POST",
        data: {cidade:cidade},
        success:function(txt){
            $("#cid_codigo").val(txt.cid_codigo);
            $("#cidade").val(txt.cid_nome);
            $("#cidade_b").val(txt.cid_nome);
        }
    });
}

function strReplaceChr(texto) {
    var chrEspeciais = new Array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë",
                                 "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö",
                                 "ú", "ù", "û", "ü", "ç",
                                 "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë",
                                 "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö",
                                 "Ú", "Ù", "Û", "Ü", "Ç");
    var chrNormais = new Array("a", "a", "a", "a", "a", "e", "e", "e", "e",
                               "i", "i", "i", "i", "o", "o", "o", "o", "o",
                               "u", "u", "u", "u", "c",
                               "A", "A", "A", "A", "A", "E", "E", "E", "E",
                               "I", "I", "I", "I", "O", "O", "O", "O", "O",
                               "U", "U", "U", "U", "C");
    for (index in chrEspeciais) {
            texto = texto.replace(chrEspeciais[index], chrNormais[index]);
    }

    return texto.toUpperCase();
}

function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;
    if((tecla>47 && tecla<58)){
        setTimeout(function() { getEnderecos()}, 500);
        return true;
    } else{
        if (tecla==8 || tecla==0) {
            setTimeout(function() { getEnderecos()}, 500);
            return true;
        } else{
            return false;
        }
    }
}


function validaCns(vlr_cns){
    if(vlr_cns.length > 0){
        $.ajax({
            url: baseUrl+"/default/paciente/valida-cns-duplicado",
            type: "POST",
            data: { cns: vlr_cns},
            success:function(txt){
                if (txt > 0) {
                    mensagem("Atenção","Numero de CNS já existe",300,150);
                    $("#pep_cartao_sus").val("");
                } else {
                    validador_cns(vlr_cns)
                }
            }
        });
    }
}

function validador_cns(vlr_cns){
    if ( (vlr_cns.substring(0,1) != "7")  && (vlr_cns.substring(0,1) != "8") && (vlr_cns.substring(0,1) != "9") ){
        validaCNS(vlr_cns);
    }else{
        ValidaCNS_PROV(vlr_cns);
    }
}

function validaCNS(vlrCNS) {
    // Formulário que contem o campo CNS
    var soma = new Number;
    var resto = new Number;
    var dv = new Number;
    var pis = new String;
    var resultado = new String;
    var tamCNS = vlrCNS.length;
    if ((tamCNS) != 15) {
        mensagem("Atenção","Numero de CNS invalido",300,150);
        $("#pep_cartao_sus").val("");
        return false;
    }
    pis = vlrCNS.substring(0,11);
    soma = (((Number(pis.substring(0,1))) * 15) +
            ((Number(pis.substring(1,2))) * 14) +
                ((Number(pis.substring(2,3))) * 13) +
                ((Number(pis.substring(3,4))) * 12) +
        ((Number(pis.substring(4,5))) * 11) +
        ((Number(pis.substring(5,6))) * 10) +
        ((Number(pis.substring(6,7))) * 9) +
        ((Number(pis.substring(7,8))) * 8) +
        ((Number(pis.substring(8,9))) * 7) +
        ((Number(pis.substring(9,10))) * 6) +
        ((Number(pis.substring(10,11))) * 5));
    resto = soma % 11;
    dv = 11 - resto;
    if (dv == 11) {
            dv = 0;
    }
    if (dv == 10) {
            soma = (((Number(pis.substring(0,1))) * 15) +
                ((Number(pis.substring(1,2))) * 14) +
                    ((Number(pis.substring(2,3))) * 13) +
                    ((Number(pis.substring(3,4))) * 12) +
            ((Number(pis.substring(4,5))) * 11) +
            ((Number(pis.substring(5,6))) * 10) +
            ((Number(pis.substring(6,7))) * 9) +
            ((Number(pis.substring(7,8))) * 8) +
            ((Number(pis.substring(8,9))) * 7) +
            ((Number(pis.substring(9,10))) * 6) +
            ((Number(pis.substring(10,11))) * 5) + 2);
            resto = soma % 11;
    dv = 11 - resto;
    resultado = pis + "001" + String(dv);
    } else {
            resultado = pis + "000" + String(dv);
    }
    if (vlrCNS != resultado) {
        mensagem("Atenção","Numero de CNS invalido",300,150);
        $("#pep_cartao_sus").val("");
        return false;
    } else {
        return true;
    }
}

function ValidaCNS_PROV(Obj)
{
    var pis;
    var resto;
    var dv;
    var soma;
    var resultado;
    var result;
    result = 0;

    pis = Obj.substring(0,15);

    if (pis == "")
       {
          return false
       }

    if ( (Obj.substring(0,1) != "7")  && (Obj.substring(0,1) != "8") && (Obj.substring(0,1) != "9") )
       {
              mensagem("Atenção","Numero de CNS invalido",300,150);
              $("#pep_cartao_sus").val("");
              return false
       }

    soma = (   (parseInt(pis.substring( 0, 1),10)) * 15)
            + ((parseInt(pis.substring( 1, 2),10)) * 14)
            + ((parseInt(pis.substring( 2, 3),10)) * 13)
            + ((parseInt(pis.substring( 3, 4),10)) * 12)
            + ((parseInt(pis.substring( 4, 5),10)) * 11)
            + ((parseInt(pis.substring( 5, 6),10)) * 10)
            + ((parseInt(pis.substring( 6, 7),10)) * 9)
            + ((parseInt(pis.substring( 7, 8),10)) * 8)
            + ((parseInt(pis.substring( 8, 9),10)) * 7)
            + ((parseInt(pis.substring( 9,10),10)) * 6)
            + ((parseInt(pis.substring(10,11),10)) * 5)
            + ((parseInt(pis.substring(11,12),10)) * 4)
            + ((parseInt(pis.substring(12,13),10)) * 3)
            + ((parseInt(pis.substring(13,14),10)) * 2)
            + ((parseInt(pis.substring(14,15),10)) * 1);

    resto = soma % 11;

    if (resto == 0)
       {
         return true;
       }
    else
       {
          mensagem("Atenção","Numero de CNS invalido",300,150);
          $("#pep_cartao_sus").val("");
         return false;
       }
}

function validaData(id){
    var matchdata = new RegExp(/((0[1-9]|[12][0-9]|3[01])\/(0[13578]|1[02])\/[12][0-9]{3})|((0[1-9]|[12][0-9]|30)\/(0[469]|11)\/[12][0-9]{3})|((0[1-9]|1[0-9]|2[0-8])\/02\/[12][0-9]([02468][1235679]|[13579][01345789]))|((0[1-9]|[12][0-9])\/02\/[12][0-9]([02468][048]|[13579][26]))/gi);
    var data = $('#'+id).val();
    if(!data.match(matchdata)) {
        mensagem("Atenção","Data inválida",300,150);
        $('#'+id).val("");
    } else {
        validaDataAtual(data, id);
    }
}

function validaDataAtual(data, id){
    var dataAtual = $("#data_atual").val();
    var arrayDataDig = data.split('/');
    var dataDigInv = arrayDataDig[2]+arrayDataDig[1]+arrayDataDig[0];
    if(dataDigInv > dataAtual) {
        mensagem("Atenção","Data de nascimento maior que a atual",300,150, function(){
            $("#"+id).val("");
        });
    }
}
