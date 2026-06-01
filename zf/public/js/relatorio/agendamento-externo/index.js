$(function(){
   $("#buscar6").buscar({
        url: baseUrl+'/medico-externo/buscar/prestador/L/prestador/H/',
        template : function(ul, item) {
                return $("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
                return true;
        }
    });
    
    $("#buscar2").buscar({
        url: baseUrl+'/especialidade/buscar/',
        template : function(ul, item) {
                return $("<li></li>").data("item.autocomplete", item).append(
                        "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function(){
                return true;
        }
    });
});

