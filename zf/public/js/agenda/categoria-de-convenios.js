$(function(){
    
    $("tr:odd").addClass("odd");
    
    $("#form-categoria-conv").validate({
        rules: {
          catc_codigo: { required: true, },
          proc_codigo: { required: true, }
        },
        messages: {
          catc_codigo: { required: "Campo Obrigatório", },
          proc_codigo: { required: "Campo Obrigatório", }
        }
    });
    
    $("#form-categoria").validate({
        rules: {
          catc_nome: { required: true, }
        },
        messages: {
          catc_nome: { required: "Campo Obrigatório", }
        }
    });
    
});

function excluirCategoriaConveniosProcedimentos(id){
    confirme("Confirme","Deseja realmente excluir este item?",300,120,function(){
        mensagemSemOk("excluindo-cat-conv", "Aguarde", "Excluindo procedimento selecionado...", 280, 80);
	$.ajax({
            url: baseUrl+"/agenda/categoria-de-convenios/excluir-categoria-convenios-procedimentos",
            type: "POST",
            data: {
                id: id
            },
            success: function(txt) {
                window.location.href= baseUrl+"/agenda/categoria-de-convenios/categoria-convenios-procedimentos";
                fecharMensagemSemOk("excluindo-cat-conv");
            }
        });
    });
}


function excluir(catc_codigo){
    confirme("Confirme","Deseja realmente excluir este item?",300,120,function(){
        mensagemSemOk("excluindo-cat", "Aguarde", "Excluindo categoria selecionada...", 280, 80);
	$.ajax({
            url: baseUrl+"/agenda/categoria-de-convenios/excluir/",
            type: "POST",
            data: {
                catc_codigo: catc_codigo
            },
            success: function(txt) {
                window.location.href= baseUrl+"/agenda/categoria-de-convenios";
                fecharMensagemSemOk("excluindo-cat");
            }
        });
    });
}


