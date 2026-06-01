function iniciarPe(cod){
	window.location = baseUrl + "/prontuario/enfermagem/ver/cod/"+cod;// pega o código que eu passei como parametro e joga para a verAction no controller
}
$(function(){
	// Buscar procedimento
	$("#proc_nome").buscar({
		url: baseUrl+'/procedimento/buscar/esp/'+$("#esp_codigo").val()+'/',
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
				"<a>" + item.label + "</a>").appendTo(ul);
		},
		callback: function(){
			return true;
		}
	});
})