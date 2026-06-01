$(function(){
	
	// antes de enviar deve selecionar todas os itens do select
	$(".salvar-icon").bind("click",function(e){
		e.preventDefault();
		$("#duplicados option").attr("selected","selected");
                $("#frm").attr("action",baseUrl+"/duplicacao/produto/");
                $("#frm").submit();
		//$(this).parents("form").trigger("submit");
	});
        $("#buscar1").buscar({
		url: baseUrl+'/produto/buscar/',
		template : function(ul, item) {
					return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a><strong>" + item.label + "</strong></a>").appendTo(ul);
		},
		callback: function(){
			// se o item selecionado como rua certa já estiver na lista de ruas duplicadas, deve ser removida
			$("#duplicados option[value="+$("#pro_codigo").val()+"]").remove();
		}
	});
	
	$("#buscar2").buscar({
		url: baseUrl+'/produto/buscar/',
		suffix: '_2',
		template : function(ul, item) {
			if($("#pro_codigo").val() == item.id || jaEstaSelecionado(item.data))
				return;			
	
			return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a><strong>" + item.label + "</strong></a>").appendTo(ul);
		},
		callback: function(event, ui){
			$("#busca").focus();
		}
	});
	
	$("#buscar3").buscar({
		url: baseUrl+'/produto/buscar/horus/1/',
		template : function(ul, item) {
					return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a><strong>" + item.label + "</strong></a>").appendTo(ul);
		},
		callback: function(){
			// se o item selecionado como rua certa já estiver na lista de ruas duplicadas, deve ser removida
			$("#duplicados option[value="+$("#pro_codigo").val()+"]").remove();
		}
	});
	
	$("#buscar4").buscar({
		url: baseUrl+'/produto/buscar/movimento/1',
		suffix: '_2',
                
		template : function(ul, item) {
			if($("#pro_codigo").val() == item.id || jaEstaSelecionado(item.data))
				return;			
	
			return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a><strong>" + item.label + "</strong></a>").appendTo(ul);
		},
		callback: function(event, ui){
			$("#busca").focus();
		}
	});
	
	$(".add").click(function(){		
		adicionar(
			$("#pro_codigo_2").val(),
			$("#pro_nome_2").val()
		);
	});
	
	$("#duplicados").dblclick(function(){		
		$("option:selected", this).remove();
	})
	
});

// adiciona os itens no select
function adicionar(value,text){	
	if(value == "" || text == "")
		return;

	var data = {};
	data.usu_codigo = value;
	data.usu_nome = text;
		
	if( ehOItemCerto(data) || jaEstaSelecionado(data))
		return false;
	else {
		$("#duplicados").append("<option value=\""+data.usu_codigo	+"\">"+data.usu_nome+"</option>");
		$("#errados input").val("");
		$("#buscar2").focus();
		return true;
	}
	
}

function jaEstaSelecionado(data){
	
	var retorno = false;
	$("#duplicados option").each(function(){
		if($(this).val() == data.usu_codigo ){
			window.console && console.log("o item já está selecionado");
			retorno = true;
		}
		
	});
	
	return retorno;
}

function ehOItemCerto(data){
	var retorno = data.usu_codigo == $("#usu_codigo").val();
	window.console && console.log("É o item certo? "+retorno);
	return retorno;
}