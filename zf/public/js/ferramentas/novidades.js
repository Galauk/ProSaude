$(function(){
    carregarItensDoLocal();

    $("#nov-itens")
	.bind('dblclick', selecionarBK)
    .bind('keydown', selecionarBK);

    $("#deletar").click(function(){
        console.log($("#nov_nome").val());
        if($("#nov_nome").val()!=null && $("#nov_nome").val()!=""){
            $.ajax({
                url: baseUrl+'/ferramentas/novidades/deletar',
                type: "POST",
                data:{nov_nome:$("#nov_nome").val()},
                success: function(json){
                        window.location.href = baseUrl+"/ferramentas/novidades/novidades-form";
                }
            });
        } else {
            okfunc();
        }
    });

    $("#editar").click(function(){
        console.log($("#nov_nome").val());
        if($("#nov_nome").val()!=null && $("#nov_nome").val()!=""){
            $.ajax({
                url: baseUrl+'/ferramentas/novidades/editar',
                type: "POST",
                data:{nov_nome:$("#nov_nome").val()},
                success: function(data){
                    console.log(data);
                    $.each(data,function(id,value){
                        console.log(id+" : "+value);
                        if(id == "versao"){
                            console.log("here");
                            $("#versao").val(value);
                        } else if (id == "autor") {
                            $("#autor").val(value);
                        } else if (id != "date" && id != undefined && id != null && id != "") {
                            $("#desc").append(value + "\n");
                        }
                    });
                    //window.location.href = baseUrl+"/ferramentas/novidades/novidades-form";
                },
                error: function(){
                    console.log("error");
                }
            });
        } else {
            okfunc();
        }
    });

});

function carregarItensDoLocal(){
	mensagemSemOk("carregando-conis", "Aguarde", "Carregando lista de versões...", 280, 80);
	$.ajax({
		url: baseUrl+'/ferramentas/novidades/novidades-ajax',
		type: "POST",
		success: function(json){
            json.splice('.', 1);
            json.splice('..', 1);
			listarNov(json);
		}
	});
}

function listarNov(json){
    //console.log(json);
	var select = $("#nov-itens").empty();
    var loop = 0;
    
    for (var bk in json) {
        select.append("<option value=\""+json[bk]+"\" >"+json[bk]+"</option>");
        loop++;
    }

	if(loop == 0){
		select.append("<option value=\"0\" disabled=\"disabled\">Nenhum arquivo disponível</option>");
	}

	fecharMensagemSemOk("carregando-conis");

}

function selecionarBK(){ 
    console.log($("#nov-itens option:selected").val()); 
    if($("#nov-itens option:selected").val() != null && $("#nov-itens option:selected").val() != ""){
        
        $("#nov_nome").val($("#nov-itens option:selected").val());
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