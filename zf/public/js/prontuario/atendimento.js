$(function() {
    
    
    if($("#ate_somente_procedimento").val() == "t"){
        somenteProcedimento();
    }
    
    if($("#uni_tipo").val() == "H"){
        somenteProcedimento();
    }
    // Validação se for dentista, preenche os campos CIAP e conduta qie não usado pelo dentista
    if($("#usr_tipo_medico").val()=="D") {
        $("#conf_ciap").val("1");
        $("#conf_cond_ate").val("1");
    // Validação se não for dentista, já seta os campos dele como preenchido
    } else {
        // Preenchendo conduta e vigilância de saúde bucal
        $("#conf_vig").val("1");
        $("#conf_cond").val("1");
    }
    
    $.validator.addMethod("ate_reclamacao", function(ate_reclamacao, element) {
        if ($("#ate_reclamacao").val() == "") {
            return false;
        } else {
            return true;
        }
    }, "Campo Obrigatório");
    
    $("#form-atendimento").validate({
        rules: {
            ate_reclamacao: { ate_reclamacao: true },
            conf_cond: {required: true},
            conf_vig: {required: true},
            conf_ciap: { required: true },
            conf_cond_ate: {required: true},
            proc_codigo: {required: true}

        },
        messages: {
            ate_reclamacao: { required: "Campo Obrigatório." },
            conf_cond: { required: "Campo Obrigatório." },
            conf_vig: { required: "Campo Obrigatório." },
            conf_ciap: { required: "Campo Obrigatório." },
            conf_cond_ate: { required: "Campo Obrigatório." },
            proc_codigo: { required: "Campo Obrigatório." }
       }
    });
    
    $("#ds_ciap").buscar({

        url: baseUrl+'/prontuario/atendimento/buscar-ciap/',
        suffix: '_2',
        search: function(){
                $("#ciap").empty();
        },
        template : function(ul, item) {
                        ul.hide();			
                        $("<option />").val(item.id).html(item.label).appendTo("#ciap");
                        return false;
        },
        callback: function(event, ui){
                $("#ciap").focus();
        }

    });
    
    if ($("#ate_codigo").val()!="") {
        carregaCid($("#ate_codigo").val());
    }
    //$("#cd10_codigo").val("");
    /*$.validator.addMethod("ate_reclamacao", function(ate_reclamacao, element) {
        if ($("#ate_reclamacao").val() == "") {
            return false;
        } else {
            return true;
        }
    }, "Campo Obrigatório");
    if ($("#cid_obrigatorio").val() == "1") {
        $("#form-multiplo").validate({
            rules: {
                cd10_codigo: {required: true},
                ate_reclamacao: {ate_reclamacao: true}
            },
            messages: {
                cd10_codigo: {required: "Campo Obrigatório"},
                ate_reclamacao: "Campo Obrigatório"
            }
        });
    } else {
        $("#form-multiplo").validate({
            rules: {
                ate_reclamacao: {ate_reclamacao: true}
            },
            messages: {
                ate_reclamacao: "Campo Obrigatório"
            }
        });
    }*/
    
    $("#ciap")
	.bind('dblclick', selecionarCiap)
	.bind('keydown', selecionarCiap);
	
    $("#ciap-selecionados")
    .bind('dblclick', deselecionarCiap)
    .bind('keydown', deselecionarCiap);
    
    $("#form-unico").validate({
        rules: {
            cd10_codigo: {
                required: true
            }, 
            proc_codigo: {
                required: true
            }
        },
        messages: {
            cd10_codigo: {
                required: "Campo Obrigatório"
            },
            proc_codigo: {
                required: "Campo Obrigatório"
            }
        }
    });
    $('textarea.tinymce_atendimento').tinymce({
        // Location of TinyMCE script

        script_url: '/WebSocialSaude/zf/public/js/tiny_mce.js',
        // General options
        //theme : "../css/tinymce/advanced",
        theme: "advanced",
        setup: function(ed) {
            ed.onKeyUp.add(function(ed, l) {
                $.cookie("ate_reclamacao", $("#ate_reclamacao").val(), {expires: 10});
                $.cookie("ate_exame_fisico", $("#ate_exame_fisico").val(), {expires: 10});
                $.cookie("ate_diagnostico", $("#ate_diagnostico").val(), {expires: 10});
                $.cookie("ate_tratamento", $("#ate_tratamento").val(), {expires: 10});
                $.cookie("ate_curativo", $("#ate_curativo").val(), {expires: 10});
            });
        },
        skin: "o2k7",
        //plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_buttons4: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true
    });

    $("input[name=ate_gravida]:checked", "#form-atendimento").ready(function(){
        if($("input[name=ate_gravida]:checked", "#form-atendimento").val() == 'S'){           
            $("#ate_idade_gest").val($("#is_gest").val()); //set idade_gest com valor calculado no controller
            $("#ate_idade_gest_label").text($("#is_gest").val()); //set idade_gest com valor calculado no controller
            $("input[name=ate_gravida]", "#form-atendimento").attr('disabled','disabled'); //block radio
            $("#div_gest_intrp").removeAttr("style"); //show radio para interromper gravidez
            $("#div_grupo_2").removeAttr("style").show();
            $("#is_grupo_2").val(1);
            if($("#is_gest_status").val() == "N"){
                console.log("here");
                $("#ate_idade_gest").attr('disabled','disabled'); //block idade_gest input text
                $("#div_idade_gest").removeAttr("style"); //show idade_gest input text
            } else {
                console.log("here2");
                $("#div_idade_gest_label").removeAttr("style"); //show idade_gest input text
            }
        }
    });

    $("input[name=ate_gravida]", "#form-atendimento").change(function(){
        console.log($("input[name=ate_gravida]:checked", "#form-atendimento").val());
        if($("input[name=ate_gravida]:checked", "#form-atendimento").val() == 'S'){
            $("#div_idade_gest").removeAttr("style").show();           
            $("#is_gest_status").val($("input[name=ate_gravida]:checked", "#form-atendimento").val()); 
            $("#is_gest").val($("#ate_idade_gest").val());
            $("#div_grupo_2").removeAttr("style").show();
            $("#is_grupo_2").val(1);
        } else {
            $("#div_idade_gest").removeAttr("style").hide();
            $("#is_gest_status").val($("input[name=ate_gravida]:checked", "#form-atendimento").val()); 
            $("#is_gest").val($("#ate_idade_gest").val());
            $("#div_grupo_2").removeAttr("style").hide(); 
        }
    });

    $("#ate_idade_gest").blur(function(){
        if($("#ate_idade_gest").val() < 0 || $("#ate_idade_gest").val() >= 44 || $("#ate_idade_gest").val() == ""){
            $("#ate_idade_gest").val("");
        mensagemValidaAdd("select-tipo", "Erro", "Quandidade de semanas não permitido.", 250, 150);
        }
    });

    $("#is_grupo_1").ready(function(){
    //console.log($("#grupo_sit_desc").val());   
        if($("#is_grupo_1").val() == 1){
            console.log($("#grupo_sit_desc").val());
            $("#div_grupo_sit_desc").removeAttr("style").show();
            $("#div_grupo_1").removeAttr("style").show();
            $("#ate_grupo_sit_desc").text($("#grupo_sit_desc").val());
            if($("#grupo_val_g1").val() != 0){
                $("#er_grupo_1").val($("#grupo_val_g1").val());
            }
        }
    });

    $("#is_grupo_2").ready(function(){
        if($("#is_grupo_2").val() == 1){
            $("#div_grupo_sit_desc").removeAttr("style").show();
            $("#div_grupo_2").removeAttr("style").show();
            $("#ate_grupo_sit_desc").text($("#grupo_sit_desc").val());
            if($("#grupo_val_g2").val() != 0){
                $("#er_grupo_2").val($("#grupo_val_g2").val());
            }
        }
    });

    $("#ver-mais-pre-consultas").click(verMaisPreConsultas);

    $(".pre-consulta").click(function() {
        var pc_codigo = $(this).data("pc");
        $("body").append("<div id=\"pre-consulta-dialog\" title=\"Pré-Consulta\" />");
        $("#pre-consulta-dialog")
                .html(imgCarregando())
                .load(baseUrl + "/prontuario/pre-consulta/ver/id/" + pc_codigo + "/sem-layout/1", function() {
            megaBind("#pre-consulta-dialog");
        })
                .dialog({
            modal: true,
            width: 610,
            height: 500,
            close: function() {
                $(this).remove();
            },
            buttons: {
                Ok: function() {
                    $(this).dialog('close');
                }
            }
        });
    });

    //scrollpane parts
    var scrollPane = $(".scroll-pane"),
            scrollContent = $(".scroll-content");

    //build slider
    var scrollbar = $(".scroll-bar").slider({
        slide: function(event, ui) {
            if (scrollContent.width() > scrollPane.width()) {
                scrollContent.css("margin-left", Math.round(
                        ui.value / 100 * (scrollPane.width() - scrollContent.width())
                        ) + "px");
            } else {
                scrollContent.css("margin-left", 0);
            }
        }
    });

    //append icon to handle
    var handleHelper = scrollbar.find(".ui-slider-handle")
            .mousedown(function() {
        scrollbar.width(handleHelper.width());
    })
            .mouseup(function() {
        scrollbar.width("100%");
    })
            .append("<span class='ui-icon ui-icon-grip-dotted-vertical'></span>")
            .wrap("<div class='ui-handle-helper-parent'></div>").parent();

    //change overflow to hidden now that slider handles the scrolling
    scrollPane.css("overflow", "hidden");

    //size scrollbar and handle proportionally to scroll distance
    function sizeScrollbar() {
        var remainder = scrollContent.width() - scrollPane.width();
        var proportion = remainder / scrollContent.width();
        var handleSize = scrollPane.width() - (proportion * scrollPane.width());
        scrollbar.find(".ui-slider-handle").css({
            width: handleSize,
            "margin-left": -handleSize / 2
        });
        handleHelper.width("").width(scrollbar.width() - handleSize);
    }

    //reset slider value based on scroll content position
    function resetValue() {
        var remainder = scrollPane.width() - scrollContent.width();
        var leftVal = scrollContent.css("margin-left") === "auto" ? 0 :
                parseInt(scrollContent.css("margin-left"));
        var percentage = Math.round(leftVal / remainder * 100);
        scrollbar.slider("value", percentage);
    }
    
    function selecionarCiap(e){
        $("#conf_ciap").val("1");
	// só pode ser a tecla 39 (seta para direita)
	if(e.keyCode && e.keyCode != 39 || e.charCode)
		return;
	
	if(!$("#ciap option:selected").size())
		return;

	// se o primeiro for 0, limpar select
	if($("#ciap-selecionados option:first").val() == "0"){
		$("#ciap-selecionados").empty();
	}
	
	// add
	$("#ciap-selecionados").append(
		$("#ciap option:selected")
	);
	
}

function mensagemValidaAdd(id, titulo, mensagem, x, y){
    $("body").append("<div id=\""+id+"\" title=\""+titulo+"\"><div class=\"c\">"+mensagem+"</div></div>");
    $("#"+id).dialog({
        modal: true,
        resizable: false,
        width: x,
        height: y,
    close: function(){
              $(this).remove();
          },
    buttons: {
              OK: function(){
                  $(this).dialog('close');
              }
          },
    });
}

function deselecionarCiap(e){
	
	// só pode ser a tecla 39 (seta para esquerda)
        
	if(e.keyCode && e.keyCode != 37 || e.charCode)
		return;
	
	// remover
	$("#ciap-selecionados option:selected").appendTo("#ciap");
	
	// se não houver mais opções, add "Nenhum"
	if($("#ciap-selecionados option").size() == 0){
		$("#ciap-selecionados").empty().append('<option value="0" disabled="disabled">Nenhum ciap selecionado</option>');
                $("#conf_ciap").val("");
	}
	
}

    //if the slider is 100% and window gets larger, reveal content
    function reflowContent() {
        var showing = scrollContent.width() + parseInt(scrollContent.css("margin-left"), 10);
        var gap = scrollPane.width() - showing;
        if (gap > 0) {
            scrollContent.css("margin-left", parseInt(scrollContent.css("margin-left"), 10) + gap);
        }
    }

    //change handle position on window resize
    $(window).resize(function() {
        resetValue();
        sizeScrollbar();
        reflowContent();
    });
    //init scrollbar size
    setTimeout(sizeScrollbar, 10);//safari wants a timeout

});

function trim(str) {
    if (str!=null){
        return str.replace(/^\s+|\s+$/g,"");
    }
}

var codsCid = "";
function carregaCid(codAtend){
    //alert(codAtend);
    $.ajax({
       url: baseUrl+"/prontuario/atendimento/lista-cids-atendimento",
       type: "POST",
       data: {
           codAtend:codAtend
       },
       success:function(txt){
           $(".tb_cids").show();
           if(txt["cd10_codigo"]!= null) {
                $(".tb_cids").append("<tr class='tb_cids_"+txt["cd10_codigo"]+"'>\n\
                     <td>\n\
                         "+(txt["cd10_codigo_desc"].indexOf(txt["cd10_codigo_cid"]) == "-1" ? txt["cd10_codigo_cid"] : "")+" "+txt["cd10_codigo_desc"]+"\n\
                         <input type='hidden' name='cid_codigo[]' value='"+txt["cd10_codigo"]+"' /> \n\
                     </td>\n\
                     <td>\n\
                         <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidBanco("+txt["cd10_codigo"]+")\" \>\n\
                     </td>\n\
                 </tr>");
           }
           if (txt["cd10_codigos"]!= null) {
                $(".tb_cids").append("<tr class='tb_cids_"+txt["cd10_codigos"]+"'>\n\
                                         <td>\n\
                                             "+(txt["cd10_codigos_desc"].indexOf(txt["cd10_codigos_cid"]) == "-1" ? txt["cd10_codigos_cid"] : "")+" "+txt["cd10_codigos_desc"]+"\n\
                                             <input type='hidden' name='cid_codigo[]' value='"+txt["cd10_codigos"]+"' /> \n\
                                         </td>\n\
                                         <td>\n\
                                             <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidBanco("+txt["cd10_codigos"]+")\" \>\n\
                                         </td>\n\
                                     </tr>");
           }
           if (txt["cd10_codigot"]!= null) {
                $(".tb_cids").append("<tr class='tb_cids_"+txt["cd10_codigot"]+"'>\n\
                                         <td>\n\
                                             "+(txt["cd10_codigot_desc"].indexOf(txt["cd10_codigot_cid"]) == "-1" ? txt["cd10_codigot_cid"] : "")+" "+txt["cd10_codigot_desc"]+"\n\
                                             <input type='hidden' name='cid_codigo[]' value='"+txt["cd10_codigot"]+"' /> \n\
                                         </td>\n\
                                         <td>\n\
                                             <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCidBanco("+txt["cd10_codigot"]+")\" \>\n\
                                         </td>\n\
                                     </tr>");
           }
       }
    });
}

var metodo = "";
function buscaCid(){
    if ($("#ate_codigo").val()!="") {  
        metodo = adicionaCidBanco;
    } else {
        metodo = adicionaCid;
    }
    
    $("#buscar").buscar({
        url: baseUrl + '/prontuario/cid/buscar/',
        delay: 10,
        minLength: 3,
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback:metodo
    });
}

//var num_registros = 0;
function adicionaCidBanco(){
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
                            <input type='hidden' name='cid_codigo[]' value='"+$("#cd10_codigo").val()+"' /> \n\
                        </td>\n\
                            <td>\n\
                                <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCid("+$("#cd10_codigo").val()+")\" \>\n\
                            </td>\n\
                        </tr>");
               }
            });
        } else {
            $(".ui-state-error").remove();
            $("#erro").prepend("<span class='ui-state-error'>Máximo de 3 CID(s) por atendimento!</span>");
        }
    } 
    $("#buscar").val("");
    //$("#cd10_codigo").val("");
}

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
        if($('.tb_cids tr').length < 3) {
            $(".tb_cids").show();
            $(".tb_cids").append("<tr class='tb_cids_"+$("#cd10_codigo").val()+"'>\n\
                                    <td>\n\
                                        "+(nomeCid.indexOf(codCid)=="-1" ? codCid : "")+" "+nomeCid+"\n\
                                        <input type='hidden' name='cid_codigo[]' value='"+$("#cd10_codigo").val()+"' /> \n\
                                    </td>\n\
                                    <td>\n\
                                        <img style=\"cursor:pointer;\" src='"+baseUrl+"/public/images/icons/excluir2.png' onClick=\"excluiCid("+$("#cd10_codigo").val()+")\" \>\n\
                                    </td>\n\
                                </tr>");
        } else {
            $(".ui-state-error").remove();
            $("#erro").prepend("<span class='ui-state-error'>Máximo de 3 CID(s) por atendimento!</span>");
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

function verMaisPreConsultas() {
    $("#ver-mais-pre-consultas").html("Menos")
            .unbind("click")
            .click(verMenosPreConsltas);

    $("#historico-pre-consulta").show("normal");
}

function verMenosPreConsltas() {
    $("#ver-mais-pre-consultas").html("Ver mais")
            .unbind("click")
            .click(verMaisPreConsultas);

    $("#historico-pre-consulta").hide("fast");
}

function selecionarTodos(){
    
    
    $("#ciap-selecionados option").each(function(){
        $(this).attr('selected', 'selected')
        //alert($(this).val());
    })
}

function validaTipoVigilancia() {
    var cont = 0;
    $("#vigilancia").find("input[type=checkbox][name='vigilancia[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_vig").val(""); } else { $("#conf_vig").val(cont); }
}

function validaTipoConduta() {
    var cont = 0;
    $("#conduta").find("input[type=checkbox][name='conduta[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_cond").val(""); } else { $("#conf_cond").val(cont); }
}

function validaTipoCondutaAte() {
    var cont = 0;
    $("#conduta_ate").find("input[type=checkbox][name='conduta_ind[]']:checked").each(function(){
        if($(this).val()) { cont++; }
    });
    if (cont==0) { $("#conf_cond_ate").val(""); } else { $("#conf_cond_ate").val(cont); }
}


function somenteProcedimento(){
    $("#info-sus").hide();
    if($("#uni_tipo").val() != "H"){
        $("#ate_reclamacao").val("Procedimento");
        $("#ate_individual").show();
    }
    $("#conf_cond").val("1");
    $("#conf_vig").val("1");
    $("#conf_cond_ate").val("1");
    $("#conf_ciap").val("1");
    $("#somente").hide();
    $("#local_esus").hide();
    
    $("#ate_somente_procedimento").val("t");
}

function atendimentoIndividual(){
    $("#info-sus").show();
    $("#ate_reclamacao").val("");
    $("#conf_cond").val("");
    $("#conf_vig").val("");
    $("#conf_cond_ate").val("");
    $("#conf_ciap").val("");
    $("#ate_individual").hide();
    $("#somente").show();
    $("#ate_somente_procedimento").val("f");
}