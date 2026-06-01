$(function (){
    var tam = 0;
    var tamControla = 600;
    var cabecalho = "";
   
   $(".procedimento").each(function(){
       
      tam += $(this).height();
      if(tam >= tamControla){
          cabecalho =  "<div class=\"cabecalho\">"+
                            "Pac.:&nbsp;&nbsp;<b>"+$(this).data("pac")+
                            "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp"+
                            $(this).data("sexo")+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp"+
                            $(this).data("nasc")+"</b><br/>"+
                            "Med.:&nbsp;&nbsp;<b>"+$(this).data("nasc")+"</b>"+
                            "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp"+
                            "Data:"+ $(this).data("nasc") +
                            "<br/>Categoria:&nbsp;&nbsp;<b>"+$(this).data("cargo")+"</b>"+
                        "</div>";
                    
          //alert($(this).data("sexo"));
          $(this).before("<p style=\"page-break-before: always;\"></p>");
           $(this).before(cabecalho);
          tam = 0;
      }
   });
    
});
