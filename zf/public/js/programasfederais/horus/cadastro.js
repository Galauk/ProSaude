$(function (){
    $("#form-cadastro").validate({
        rules: {
            hor_cad_login: {
                required: true,
                email: true
            },
            hor_cad_senha: {
                required: true
            },
            hor_cad_ambiente: "required",
            hor_cad_ativo: "required"
        },
        messages: {
            hor_cad_login: {
                required: "Campo Obrigatório",
                email: "Coloque o e-mail no formato correto"
            },
            hor_cad_senha: {
                required: "Campo Obrigatório"
            },
            hor_cad_ambiente: "Campo Obrigatório<br />",
            hor_cad_ativo: "Campo Obrigatório<br />"
        },
        errorPlacement: function(error, element) {
            if (element.is(":radio")) {
                error.prependTo(element.parent());
                error.css("float","right");
            }
            else { // This is the default behavior of the script for all fields
                error.insertAfter( element );
            }
        }
    });
});

