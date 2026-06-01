$(function(){
    
	// validações
	$("form:first").validate({
		rules: {
			ale_desc: {
				required: true
			}
		},
		messages: {
			ale_desc: {
				required: "Infome um alerta"
			}
		}
	});
    
});