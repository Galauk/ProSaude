$(function(){
	
	$("#for_codigo").buscar({
		url: baseUrl+'/fornecedor/buscar',
		
		template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});	
	// validações
	$("form:first").validate({
		rules: {
			for_codigo: {
				min: 1
			}
		},
		messages: {
			for_codigo: {
				min: "Infome um procedimento"
			}
		}
	});
});