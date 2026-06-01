setInterval('carregarProximo()', 3000);

function carregarProximo(){
       //$("#id_chamada").load(baseUrl+"/chamada/buscar-chamadas/");
       var som = new Audio(baseUrl+'/public/sounds/dingdong.wav');
       $.ajax({
           url: baseUrl+"/chamada/buscar-chamadas/",
           type: "GET",
           success:function(txt){
               for(var i in txt){
                    if(i == 0){
                        if(txt[i].cha_status == "C"){
                               /* $.ajax({
                                        url: baseUrl+"/chamada/ler",
                                        type: "POST",
                                        data: {
                                                usu_nome: "teste"
                                        },
                                        success: function(txt){
                                            alert(txt);
                                            var sound = $("<embed id='sound' type='audio/mpeg' />");
                                            sound.attr('src', txt);
                                            sound.attr('loop', false);
                                            sound.attr('hidden', true);
                                            sound.attr('autostart', true);
                                            $('body').append(sound);      

                                       }
                                });*/
                            som.play();
                            alteraStatus(txt[i].age_codigo);
                        }
                       var conteudo = "<div id=\"div_superior\">"+
                                        "<b>"+txt[i].age_paciente+"</b>"+
                                      "</div>"+
                                      "<div id=\"div_setor\">"+
                                        "<b>"+txt[i].set_nome+"</b>"+
                                    "</div>"+
                                    "<div >"+
                                    "</div>";
                    }else {
                       conteudo += "<div class=\"anterior_1\">"+
                                    "<br/>"+
                                    //<?=$this->abreviaNome($chamada[age_paciente],24)?>
                                    txt[i].age_paciente+
                                    "<br/>"+
                                   "<font color=\"red\"><b>"+txt[i].set_nome+"</b></font>"+
                                   "</div>";
                    }
//                                echo $this->action("altera-status", "chamada", "default", array("age_codigo" =>  $chamada[age_codigo]));
               }
              //alert(conteudo);
              $("#id_chamada").html(conteudo);
              
           }
       });
       
}


function alteraStatus(age_codigo){
    
     $.ajax({
           url: baseUrl+"/chamada/altera-status/",
           data: {
               age_codigo:age_codigo},
           type: "GET",
           success:function(txt){
               
           }
     });
}