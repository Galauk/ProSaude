function buscaProfissionais() {
     $("#profs_part_nome").buscar({
            url: baseUrl+'/default/usuarios/buscar-profissionais-equipes',
             template : function(ul, item) {
                     return $("<li/>").data("item.autocomplete", item).append(
                             "<a>" + item.label + "</a>").appendTo(ul);
             },
             callback: function(event, ui){
               return true;
             }
         });

}