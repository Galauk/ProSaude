$(function(){
    $("#form-balanco-prod-set").validate({
		rules: {
                    set_codigo: {required: true},
                    data_inicial: {required: true},
                    data_final: {required: true}
		},
		messages: {
                    set_codigo: { required: "Campo Obrigatório"},
                    data_inicial: {required: "Campo Obrigatório"},
                    data_final: {required: "Campo Obrigatório"}    
		}
	});

});
