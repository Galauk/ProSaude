$(function (){
    
    $("#form-configuracao").validate({
        rules: {
            proc_codigo: { required: true },
            categoria_de_exames : { required: true }
        },
        messages: { 
            proc_codigo: { required: "Selecione um Procedimento." },
            categoria_de_exames: { required: "Selecione uma Categoria." }
       }
    });
    
    $("tr:odd").addClass("odd");
    
    $( "table.sortable tbody" ).sortable({
        revert: true,
        axis: "y",
        stop: function(e,u){
            var arr = $("input[name^=ordem]");
            var ordem = [];
            arr.each(function(){
                ordem.push(this.value);
            });
            window.console && console.log('enviando...');
            $.ajax({
                    url: baseUrl+"/laboratorio/categoria-de-exames/atualiza-ordem-configuracoes/",
                    type: 'post',
                    data: {
                        ordem: ordem
                    },
                    success: function(txt) {
                        window.console && console.log('reordenado!');
                    }
            });
        }
    });
        
    //$( "tr, td" ).disableSelection();
    
});

