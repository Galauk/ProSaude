$(function(){

	$("#busca1").buscar({
            url: baseUrl+'/cidade/buscar/',
            template : function(ul, item) {
			return $("<li/>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
            callback: function(event, ui){
                    return true;
            }
    });
    
});