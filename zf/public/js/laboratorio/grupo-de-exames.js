$(function (){
    
    $("tr:odd").addClass("odd");
    
    $("#form-configuracao").validate({
        rules: {
            proc_codigo: { required: true },
            grupo_de_exames : { required: true }
        },
        messages: { 
            proc_codigo: { required: "Selecione um Procedimento." },
            grupo_de_exames: { required: "Selecione um Grupo." }
       }
    });
    
    $( "table.sortable tbody" ).sortable({
        revert: true,
        axis: "y",
        stop: function(e,u){
            var arr = $("input[name^=ordem]");
            var ordem = [];
            arr.each(function(){
                ordem.push(this.value);
            });
            window.console && console.log('enviando...');
            $.ajax({
                    url: baseUrl+"/laboratorio/grupo-de-exames/atualiza-ordem-configuracoes/",
                    type: 'post',
                    data: {
                        ordem: ordem
                    },
                    success: function(txt) {
                        window.console && console.log('reordenado!');
                    }
            });
        }
    });
    
    $("#novo-grupo-de-exames").click(function(){
        $("body").append("<div id=\"cadastro-grupo-de-exames\" title=\"Cadastro de Grupo de Exames \"></div>");
        $("#cadastro-grupo-de-exames")
        .html("<img src=\""+baseUrl+"/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando Cadastro de Grupo de Exames...\" />")
        .dialog({
            modal: true,
            width: 450,
            height: 160,
            buttons:{
                Cancelar: function(){
                    $("#cadastro-grupo-de-exames").dialog("destroy").remove();
                },
                Salvar: function(){
                    $.ajax({
                        url:baseUrl+"/laboratorio/grupo-de-exames/salvar-form-grupo-de-exame",
                        type: "POST",
                        data: {
                            grupo_exame_nome: $("#grupo_exame_nome").val(),
                        },
                        success:function(txt){
                            if (txt.indexOf("Erro")=="-1") {
                                $("#grupo_de_exames").append("<option value='"+txt+"' selected='selected'>"+$("#grupo_exame_nome").val()+"</option>");
                                $("#cadastro-grupo-de-exames").dialog("destroy").remove();
                                $("#proc_nome").focus();
                            } else {
                                mensagem("Erro",txt);
                            }
                        }
                    });
                }
            }
        })
        .load(baseUrl+"/laboratorio/grupo-de-exames/form");
    });
    
});