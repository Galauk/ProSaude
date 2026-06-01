$(function(){
    $("#usr_nome").buscar({
        url: baseUrl+'/default/usuarios/buscar-usuarios-saude',
        template : function(ul, item) {
                return $("<li/>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
                buscaCnesRelacionado();
                return true;
        }
    });
    
    $("#usu_nome").buscar({
        url: baseUrl+'/paciente/buscar'
    });
});

function buscaCnesRelacionado(){
    $.ajax({
        url: baseUrl + '/default/usuarios/get-unidade-usuarios',
        data:{usr_codigo:$("#usr_codigo").val()},
        success:function(txt){
            var options = "";
            for(var i in txt){
                options += "<option>"+txt[i].uni_desc+"</option>"
            }
            $("#co_unidade_saude").html(options);
        }
    });
}