$(function(){
    $("#form-num-paciente-atendido-por-medicamentos").validate({
        rules:{
            data_inicial: {required:true},
            data_final: {required:true}
        },
        messages: {
            data_inicial: "Campo Obrigatório",
            data_final: "Campo Obrigatório"
        }
    });
});