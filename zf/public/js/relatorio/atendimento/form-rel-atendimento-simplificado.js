$(function() {
    
    $("#usu_nome").buscar({
        url: baseUrl + '/paciente/buscar',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });

    $("#usr_nome").buscar({
        url: baseUrl + '/default/usuarios/buscar',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });

    $("#proc_nome").buscar({
        url: baseUrl + '/procedimento/buscar/',
        template: function(ul, item) {
            return $("<li></li>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });
    

});

