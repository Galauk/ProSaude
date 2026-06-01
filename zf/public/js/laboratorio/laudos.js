$(function (){
    var tamDiferenca = 0;
    var tamControla = 0;
    $.ajax({
       url: baseUrl+"/laboratorio/categoria-de-exames/lista-categorias-de-exames-por-procedimentos/",
       type: "POST",
       data: { procs_codigo: $("#procs_codigo").val() },
       success: function(txt){
           //console.log(txt); 
            //var qtdDivCategoria = 0;
            for(var i in txt){
                var qtdDivCategoria = $("#"+txt[i].cte_codigo).find("div.conteudo-"+txt[i].cte_codigo).size();
                console.log(qtdDivCategoria);
                var categoria = txt[i].cte_codigo; 
                console.log(categoria);
                // Percorrendo as DIVS de bioquimica
                for (i=0; i<qtdDivCategoria; i++) {
                    // Controlando o tamanho da página
                    tamControla = tamControla+$("#exame-"+categoria+"-"+i).height();
                    /*if (tamControla < 180) {
                        $("#exame-"+categoria+"-"+i).height(180);
                    }*/
                    console.log(tamControla);
                    if (tamControla > 400) {
                        //$(".bioquimicos-"+categoria+"-"+(i-1)).show();
                        $(".quebra-"+categoria+"-"+i).show();
                        // Controla proxima categoria 
                        tamControla = $("#exame-"+categoria+"-"+i).height();
                        if (i == (qtdDivCategoria-1)) { 
                           //alert("Nova Categoria"); 
                           tamControla = 0;
                        }
                    }
                    
                    // Se for uma nova categoria, tamanho da pagina = 0
                    if (i == (qtdDivCategoria-1)) { 
                       //$("#exame-"+categoria+"-"+i).height(900-$("#exame-"+categoria+"-"+i).height());
                        //$(".quebra-"+categoria).show();
                       tamControla = 0;
                    }
                }    
            }
        }
    });
    
});

function salvar(){
	//alert("asdfasdf");
    var array_bio = new Array();
    $(".bioquimicos_resp:checked").each(function(){
        array_bio.push($(this).val());
    });
    $.ajax({
        url: baseUrl+"/laboratorio/laudos/salvar-lista-responsaveis-laudos/",
        type: "POST",
        data: { 
            age_codigo: $("#age_codigo").val(),
            bioquimicos: array_bio 
        },
        success: function(txt){
            mensagem("Confirmação de Cadastro","Bioquímicos cadastrado com sucesso!",300,120, function(){window.close()});
        }
    });
}