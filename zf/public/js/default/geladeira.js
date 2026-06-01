$(function(){
	
	$("#set_nome").buscar({
		url: baseUrl+"/setor/buscar",
		template : function(ul, item) {
			return jQuery("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		}
	});
	$("form").validate({
		rules: {
			set_codigo: {
				required:true
			},
			gel_marca: {
				required:true
			},
                        gel_minima: {
				required:true
			},
                        gel_maxima: {
				required:true
			},
                        gel_patrimonio:{
                            required:true
                        }
		},
		messages: {
			set_codigo: {
				required: "Campo Obrigatório"
			},
                        gel_marca: {
				required: "Campo Obrigatório"
			},
                        gel_minima: {
				required: "Campo Obrigatório"
			},
                        gel_maxima: {
				required: "Campo Obrigatório"
			},
                        gel_patrimonio: {
				required: "Campo Obrigatório"
			}
		}
	});
	
});