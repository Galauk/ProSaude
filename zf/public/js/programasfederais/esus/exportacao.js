$(function (){
    ativaExportacao();
});

function ativaExportacao(){
    var link = "../../../../WebSocialSaude/e-sus/DadosEsus.php";
    mensagemSemOk("carregando-ate", "Aguarde", "Gerando arquivo de exportação do E-SUS ...", 350, 100);
    $.ajax({
        url: link,
        data:{},
        success: function(txt){
            fecharMensagemSemOk("carregando-ate");
            $("#aguarde-esus").hide();
            $("#ok-esus").show();
        }
    });
}


