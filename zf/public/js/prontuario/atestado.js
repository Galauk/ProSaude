$(function(){
    // Validações CID e impressão
    var cid_obrigatorio = $("#cid_obrigatorio").val();
    var print = $("#imprimir").val();

    /*if (cid_obrigatorio == 1) {
        $("#form-atestado").validate({
            rules: {
                ate_cd10_descricao: {required: true}
            },
            messages: {
                ate_cd10_descricao: {required: "Informe um CID no atendimento para gerar o atestado! Ou recarregue-o ->"},
            }
        });
    }*/

    if(print == 1){
        popup(baseUrl+'/prontuario/atestado/imprimir','atestado',630,540);
    }
});

function atualizarCid(){
    $.ajax({
        url: baseUrl+"/prontuario/atendimento/get-cid-atendimento",
        success: function(txt){
            $("#ate_cd10_codigo").val("");
            $("#ate_cd10_descricao").val("");
            $("#ate_cd10_codigo").val(txt["cidCodigo"]);
            $("#ate_cd10_codigo_novo").val(txt["cidCodigoNovo"]);
            $("#ate_cd10_descricao").val(txt["cidDesc"]);
        }
    });
}

function excluirCid(){
    confirme("Confirme:", "Deseja realmente remover este item?", 300, 150, function(){
        $("#ate_cd10_codigo").val("");
        $("#ate_cd10_descricao").val("");
    });
}