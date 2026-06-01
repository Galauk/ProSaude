$(function(){
    carregarItensDoLocal();

    $("#backup-itens")
	.bind('dblclick', selecionarBK)
    .bind('keydown', selecionarBK);
    
    $("#bk_nome").prop('disabled', true);

    $("#deletar").click(function(){
        if($("#bk_nome").val()!=null && $("#bk_nome").val()!=""){
            $.ajax({
                url: baseUrl+'/ferramentas/backup/deletar',
                type: "POST",
                data:{bk_nome:$("#bk_nome").val()},
                success: function(json){
                    window.location.href = baseUrl+"/ferramentas/backup/index";
                }
            });
        } else {
            console.log("hereac");
            okfunc();
        }
    });

    $("#download").click(function(){
        console.log($("#root").val() );
        console.log($("#modulo").val() );
        var url = baseUrl +'/../e-sus/download.php?arquivo='+ $("#root").val() + $("#modulo").val() +'backup/'+$("#bk_nome").val();
        if($("#bk_nome").val()!=null && $("#bk_nome").val()!=""){
            window.open(url);
        } else {
            okfunc();
        }
    });
});

function carregarItensDoLocal(){
	mensagemSemOk("carregando-conis", "Aguarde", "Carregando lista de backups...", 280, 80);
	$.ajax({
		url: baseUrl+'/ferramentas/backup/backups-ajax',
		type: "POST",
		success: function(json){
            json.splice('.', 1);
            json.splice('..', 1);
			listarBackup(json);
		}
	});
}

function listarBackup(json){
    console.log(json);
	var select = $("#backup-itens").empty();
    var loop = 0;
    
    for (var bk in json) {
        select.append("<option value=\""+json[bk]+"\" >"+json[bk]+"</option>");
        loop++;
    }

	if(loop == 0){
		select.append("<option value=\"0\" disabled=\"disabled\">Nenhum backup disponível</option>");
	}

	fecharMensagemSemOk("carregando-conis");

}

function selecionarBK(){ 
    if($("#backup-itens option:selected").val() != null && $("#backup-itens option:selected").val() != ""){ 
        $("#bk_nome").val($("#backup-itens option:selected").val());
        $("#bk_nome_bk").val($("#backup-itens option:selected").val());
        $("#bk_nome").prop('disabled', false);
    }
}

function okfunc(){
    $("body").append("<div id=\"mensagem-dialog\" title=\"Selecione um Arquivo\" />");
    $("#mensagem-dialog")
    .dialog({
        modal: true,
        width: 240,
        height: 120,
        buttons: {
            Ok: function(){
                $(this).dialog('close');
            }
        }
    });
}