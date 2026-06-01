$(function (){
    
});

function excluir(codUsu){
    confirme("Confirme:", "Deseja realmente excluir este item?", 300, 150, function(){
        $.ajax({
            url:baseUrl+"/programasfederais/horus/excluir-usuario",
            type: "POST",
            data: {
                hor_cad_codigo: codUsu
            },
            success:function(txt) {
                window.location = baseUrl+"/programasfederais/horus/usuarios";
            } 
        });
    });
}

