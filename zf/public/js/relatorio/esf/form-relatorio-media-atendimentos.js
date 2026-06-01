$(function() {    
    $("#nu_ine").buscar({
        url: baseUrl + '/domicilio/psf/buscar-ine',
        template: function(ul, item) {
            return $("<li/>").data("item.autocomplete", item).append(
                    "<a>" + item.label + "</a>").appendTo(ul);
        },
        callback: function() {
            return true;
        }
    });
    
    

});

