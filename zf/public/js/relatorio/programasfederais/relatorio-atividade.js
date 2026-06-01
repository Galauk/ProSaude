$(function() {


    //#96579 primeira verificação para mostrar o formulario correto inicial
    if($("#tipo_atendimento").val() == "V"){           
        $('#V').attr("checked","checked");
    }else if($("#tipo_atendimento").val() == "A"){
        $('#A').attr("checked","checked");
    }else if($("#tipo_atendimento").val() == "P"){
        $('#P').attr("checked","checked");
    }
 
    //#96579 vai para o metodo para mostrar o formulario
    verificaTipoAtentimento('#tipo_atendimento');
    //#96579 vai ao metodo verificar se é uma alteração e mostrar a especialidade correta do atendimento
    carregaEspecialidade();
    
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
    
    $("#ciap")
	.bind('dblclick', selecionarCiap)
	.bind('keydown', selecionarCiap);
	
    $("#ciap-selecionados")
    .bind('dblclick', deselecionarCiap)
    .bind('keydown', deselecionarCiap);
    
    $.validator.addMethod("validaVisitaDesfecho", function(validaVisitaDesfecho, element){
        //alert($("input[name=ate_tipo_atendimento]:checked").val());
        if ($("input[name=ate_tipo_atendimento]:checked").val() != "V"){
            return true;
        } else {
            if($("input[name=visita_desfecho]:checked").val() == "1" || $("input[name=visita_desfecho]:checked").val() == "2" || $("input[name=visita_desfecho]:checked").val() == "3"){
                return true;
            }else{
                return false;
            }
            return false;
        }
    },"Campo Obrigatório!");
    
    $.validator.addMethod("validaVisitaMotivo", function(validaVisitaDesfecho, element){
        if ($("input[name=ate_tipo_atendimento]:checked").val() == "V"){
            var count = $('input:checkbox:checked').length;    
            if($("input[name=visita_desfecho]:checked").val() == 1){
                if(count > 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
            
        }else{
            return true;
        }
    },"Campo Obrigatório!");
    
    $("#ate-simplificado").validate({
        rules: {
            conf_visita: { required: true },
            conf_ciap: { required: true },
            conf_cond: { required: true },
            uni_codigo: { required: true },
            usu_codigo: { required: true },
            usr_codigo : { required: true },
            proc_codigo: { required: true },
            conf_desfecho: { required: true },
            //visita_desfecho:{validaVisitaDesfecho:true}
        },
        messages: {
            conf_visita: { required: "Selecione o motivo da visita." },
            conf_ciap: { required: "Selecione um Ciap." },
            conf_cond: { required: "Selecione uma Conduta." },
            uni_codigo: { required: "Selecione uma Unidade." },
            usu_codigo: { required: "Selecione um Paciente." },
            usr_codigo: { required: "Selecione um Profissional." },
            proc_codigo: { required: "Selecione um Procedimento." },
            conf_desfecho: { required: "Selecione um Desfecho." }
       }
    });
    
    $(".ate_tipo_atendimento").change(function(){
        
    });
    
    
    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            carregaEspecialidade();
        }
    });

    $(".paciente").click(function(){
        var usu_codigo = $("#usu_codigo").val();
        var cadastro_aise = $("#cadastro_aise").val();
        var link = "";
        if(cadastro_aise == 1){
            link = baseUrl+"/paciente/form-paciente/pessoa/"+usu_codigo+"/poupup/1";
        }else{
            link = baseUrl+"/default/paciente/form-paciente/poupup/1";
            //link = "../../../../WebSocialSaude/paciente.php?acao=form&poupup=1&usu_codigo="+usu_codigo;
        }
        window.open(link, "name", "scrollbars=1,height=800,width=900",'width=850,height=700');
    });
    
    $(".desfecho").change(function(){
       if($(this).val() == 2 || $(this).val() == 3){
           $(".motivo_checkbox").each(function(){
               $(this).prop("checked",false);
               $(this).attr("disabled", true);
               $("#conf_visita").val("1");
           });
       } else{
           $(".motivo_checkbox").each(function(){
               $(this).removeAttr("disabled");
           });
           //$("#conf_visita").val("");
            validaMotivoVisita();
       }
    });
    
});