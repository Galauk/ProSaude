var _pagina = 0;
var _paginas = 0;

$(function(){
        
    $("#tudo").disableSelection();
    
    $("ul li span").hide();
	
    $("ul li a").hover(
        function(){
            var title = $(this).find("img").attr("alt");
            $(this).after("<span style='max-width:200px; style='display:none'>"+title+"</span>").next().show("normal");
        },
        function(){
            $(this).next().hide("fast", function(){
                $(this).remove();
            });			
        }
        );
		
    _paginas = Math.ceil($("ul li").size()/8);
		
    $("ul li").slice(0, 8).show("fast");
    if(_paginas > 1){
        ocultar(0);
    }
	
    $("#anterior").click(function(){
        if(_pagina > 0){
            _pagina--;
            mostrar();
            ocultar(0);
        }
    });
	
    $("#proximo").click(function(){
        if(_pagina < _paginas-1){
            _pagina++;
            mostrar();
            ocultar(1);
        }
    });
    
	
});

function ocultar(lado){
    if(lado == 0){
        if(_pagina == 0){
            $("#anterior").css({
                "background-image":"none",
                "cursor":"default"
            });
        } else {
            $("#anterior").css({
                "background-image":"url('"+baseUrl+"/public/images/prints/anterior.png')",
                "cursor":"pointer"
            });            
        }
        $("#proximo").css({
            "background-image":"url('"+baseUrl+"/public/images/prints/proximo.png')",
            "cursor":"pointer"
        });  
    } else {
        if(_pagina == _paginas-1){
            $("#proximo").css({
                "background-image":"none",
                "cursor":"default"
            });
        } else {
            $("#proximo").css({
                "background-image":"url('"+baseUrl+"/public/images/prints/proximo.png')",
                "cursor":"pointer"
            });            
        }
        $("#anterior").css({
            "background-image":"url('"+baseUrl+"/public/images/prints/anterior.png')",
            "cursor":"pointer"
        });  
    }
}

function mostrar(){    
    var de = _pagina*8.
    var ate = de+8;
    $("ul li").hide("fast").slice(de, ate).show("normal"); // cat jump
}