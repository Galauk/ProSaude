$(function(){
	
	// antes de enviar deve selecionar todas os itens do select
	$(".salvar-icon").bind("click",function(e){
		e.preventDefault();
		$("#duplicados option").attr("selected","selected");		
		$(this).parents("form").trigger("submit");
	});
	
	$("#rua_nome").buscar({
            url: baseUrl+'/rua/buscar/',
            template : function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + ""
                    + "<br/><strong>Bairro: </strong>"+ item.data.bai_nome
                    + "<br/><strong>Distrito: </strong>"+ item.data.dis_nome
                    + "</a>&nbsp;").appendTo(ul);
            }, 
            callback: function(){
                    // se o item selecionado como rua certa já estiver na lista de ruas duplicadas, deve ser removida
                    $("#duplicados option[value="+$("#rua_codigo").val()+"]").remove();
            }
	});
	
	$("#rua_nome_2").buscar({
		url: baseUrl+'/rua/buscar/',
		suffix: '_2',
		search: function(){
			$("#busca").empty();
		},
		template : function(ul, item) {
			if($("#rua_codigo").val() == item.id || jaEstaSelecionado(item.data))
				return;
			
			ul.hide();			
			$("<option />").val(item.id).html(item.label+" - "+item.data.bai_nome).appendTo("#busca").attr({title: item.label+" - "+item.data.bai_nome});
			return false;
		},
		callback: function(event, ui){
			$("#busca").focus();
		}
	});
	
	$("#busca").dblclick(function(){		
		adicionar($("option:selected", this));		
	}).keydown(function(e){
		
		if(e.keyCode && e.keyCode != 39 || e.charCode){
			return 
		}
		
		e.preventDefault();
		adicionar($("option:selected", this));	
	});
	
	$("#duplicados").dblclick(function(){		
		$("option:selected", this).appendTo($("#busca"));	
		$("#busca").val('');
	}).keydown(function(e){
		
		if(e.keyCode && e.keyCode != 37 || e.charCode){
			return 
		}
		
		e.preventDefault();
		$("option:selected", this).appendTo($("#busca"));	
		$("#busca").val('');		
	});
	
});

// adiciona os itens no select
function adicionar(obj){	
	if(!obj.size())
		return;

	var data = {};
	data.rua_codigo = obj.val();
	data.rua_nome = obj.html();
		
	if( ehOItemCerto(data) || jaEstaSelecionado(data))
		return false;
	else {
		$("#duplicados").append("<option value=\""+data.rua_codigo	+"\">"+data.rua_nome+"</option>");
		obj.remove();
		return true;
	}
	
}

function jaEstaSelecionado(data){
	
	var retorno = false;
	$("#duplicados option").each(function(){
		if($(this).val() == data.rua_codigo ){
			window.console && console.log("o item já está selecionado");
			retorno = true;
		}
		
	});
	
	return retorno;
}

function ehOItemCerto(data){
	var retorno = data.rua_codigo == $("#rua_codigo").val();
	window.console && console.log("É o item certo? "+retorno);
	return retorno;
}