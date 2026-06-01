$(function () {
    mensagem("Atenção", "Clique na tecla F11 do teclado para maximizar a tela!", 300, 250, function () {
        $('#page').fullscreen();
    });
    carregaDadosIniciais();
}); 


function buscarChamadas(uni_codigo) {
    $("<audio id='chatAudio'>"
        + "<source src='" + baseUrl + '/public/sounds/chamada.mp3' + "' type='audio/ogg'>"
        + "</audio>").appendTo('body');
    $('#chatAudio')[0].play();
    $.ajax({
        url: baseUrl + "/default/guiche/buscar-chamadas/uni_codigo/" + uni_codigo,
        type: 'GET',
        success: function (dados) {
            var templateAtual = "";
            if (dados.length > 0) {
                //Chamada atual
                var cor = '';
                if(dados[0].cor!=null) { cor = dados[0].cor; } else { cor = '#7c7c7c'; }
templateAtual = "<div id='pac-1'>"+
                "<div id='cor-1' style='width: 60px;height:60px; background-color:"+cor+";float: left; margin-right:5px;'></div>"+
                "<div id='paciente' style='margin-left:10px; font-size: 55px;'>"+dados[0].age_paciente+"</div>"+       
                "</div>"+
                "<div>"+
                "<div id='set-pac1' style='font-size: 30px;color:#5FBAFF;left:65px;position: relative'><b>"+dados[0].set_nome+"</div>"+
                "</div>";

console.log(dados[0]);

                //Chamadas anteriores
                var templateAnteriores = "<ul class='event-list'>";
                $.each(dados, function (index, chamada) {
                    if (index > 0) {
                        templateAnteriores += "<li>"
                            + "   <time style='background-color: " + chamada.cor + ";'>"
                            + "       <span class='month'>" + chamada.cha_usu_prontuario + "</span>"
                            + "   </time>"
                            + "   <div class='info'>"
                            + "       <h2 class='title'>" + chamada.age_paciente + "</h2>"
                            + "       <p class='desc'>" + chamada.set_nome + "</p>"
                            + "   </div>"
                            + "   <div class='social'>"
                            + "<ul>"
                            + "<li><i class='fa fa-2x fa-" + (chamada.cha_status == 'C' ? 'thumbs-o-up text-primary' : 'thumbs-o-down text-danger' ) + "'></i></li>"
                            + "</ul>"
                            + "   </div>"
                            + " </li>";
                    }
                });
                templateAnteriores += "</ul>";
                $(".ultimas-chamadas").html(templateAnteriores);
            } else {
                templateAtual = "<p class='paciente'>Não há pacientes na fila no momento.</p>"
            }
            $("#proximo").html(templateAtual);
            $("#cor-1").css('background-color', cor);

        }
    })
}
function carregaDadosIniciais() {
    $.ajax({
        url: baseUrl + "/default/guiche/carrega-dados-iniciais",
        type: 'GET',
        success: function (dados) {
            $.cookie('uni_codigo', dados.uni_codigo);
            $.cookie('uni_desc', dados.uni_desc);
            $.cookie('uni_endereco', dados.uni_endereco);
            $.cookie('uni_numero', dados.uni_numero);
            $.cookie('uni_bairro', dados.uni_bairro);
            $.cookie('cid_nome', dados.cid_nome);
            $.cookie('uf_sigla', dados.uf_sigla);

            var tpl = "<h1>" + $.cookie('uni_desc') + "</h1>"
                + "<p>" + $.cookie('uni_endereco') + ", " + $.cookie('uni_numero') + "</p>"
                + "<p>" + $.cookie('uni_bairro') + " " + $.cookie('cid_nome') + " - " + $.cookie('uf_sigla') + "</p>"
            $('#unidade').html(tpl);

            startTime();

            carregaVideos();

            verificaAlteracoes($.cookie('uni_codigo'));
            setInterval(function () {
                verificaAlteracoes($.cookie('uni_codigo'));
            }, 3000);

        }
    });
}
setInterval('carregarProximo()', 1000);
function carregarProximo(){
       //$("#id_chamada").load(baseUrl+"/chamada/buscar-chamadas/");
//       var som = new Audio(baseUrl+'/public/sounds/dingdong.wav');
    $("<audio id='chatAudio'>"
        + "<source src='" + baseUrl + '/public/sounds/chamada.mp3' + "' type='audio/ogg'>"
        + "</audio>").appendTo('body');
    
       $.ajax({
           url: baseUrl+"/guiche/buscar-proximo/",
           type: "GET",
           success:function(txt){
                var mediaElement = document.getElementById('mediaElement');
templateAtual = "<div id='pac-1'>"+
                "<div id='cor-1' style='width: 60px;height:60px; background-color:"+txt.cor+";float: left; margin-right:5px;'></div>"+
                "<div id='paciente' style='margin-left:10px; font-size: 55px;'>"+txt.age_paciente+"</div>"+       
                "</div>"+
                "<div>"+
                "<div id='set-pac1' style='font-size: 30px;color:#5FBAFF;left:65px;position: relative'><b>"+txt.set_nome+"</div>"+
                "</div>";                
                // if(Math.round(mediaElement.duration) == Math.round(mediaElement.currentTime)){
                //     $.ajax({
                //         url: baseUrl+"/guiche/retorna-video/",
                //         type: "GET",
                //         success:function(txt2){                            
                //              $("#mediaElement").attr('src', baseUrl+'/public/videos/'+txt2);
                //         }
                //     });
                  
                //     //baseUrl+'/public/sounds/dingdong.wav
                   //  alert('aaaaaaaaaaleeeeeeeluia');
                // }
               if(txt.cha_status == "C"){
                    $.ajax({
                        url: baseUrl+"/guiche/buscar-pacientes/",
                        type: "GET",
                        success:function(txt2){
                             $("#pac-2").html(txt2[1].age_paciente);
                             $("#set-2").html(txt2[1].set_nome);
                             $("#cor-2").css('background-color', txt2[1].cor);
                             
                             $("#pac-3").html(txt2[2].age_paciente);
                             $("#set-3").html(txt2[2].set_nome);
                             $("#cor-3").css('background-color', txt2[2].cor);
                             
                             $("#pac-4").html(txt2[3].age_paciente);
                             $("#set-4").html(txt2[3].set_nome);
                             $("#cor-4").css('background-color', txt2[3].cor);
                             
                             $("#pac-5").html(txt2[4].age_paciente);
                             $("#set-5").html(txt2[4].set_nome);
                             $("#cor-5").css('background-color', txt2[4].cor);
                             
                             // $("#pac-6").html(txt2[5].age_paciente);                           
                             // $("#set-6").html(txt2[5].set_nome);                           
                             // $("#cor-6").css('background-color', txt2[5].cor);                           
                                                   
                        }
                    });
                 $("#proximo").html(templateAtual);
                 var mediaElement = document.getElementById('mediaElement');
              //   alert(mediaElement.currentTime);
                 
                 
                 // mediaElement.currentTime = 122
                // mediaElement.seekable.end();
                 //mediaElement.played.end();
                // alert(mediaElement.seekable.end());
                // mediaElement.volume-=1;
               //  alert(mediaElement.seekable.end());
               //mediaElement.seekable.start();  // Retorna o tempo em que o arquivo começa (em segundos)
               // mediaElement.seekable.end();    // Retorna o tempo em que o arquivo termina (em segundos)
              //  mediaElement.currentTime = mediaElement.seekable.end(); ; // Ir para 122 segundos
               // mediaElement.played.end();      // Retorna o numero de segundos que o navegador reproduziu
                $('#chatAudio')[0].play();
                // mediaElement.volume+=1;
               //  mediaElement.volume=10;
                 alteraStatus(txt.age_codigo);
                 
               }
                //alert(txt.cha_status)
                //som.play();
                //alteraStatus(txt.age_codigo);
            
              //alert(conteudo);
            
              
           }
       });
       
}


function alteraStatus(age_codigo){    
     $.ajax({
           url: baseUrl+"/guiche/altera-status/",
           data: {
               age_codigo:age_codigo},
           type: "GET",
           success:function(txt){
             //  console.log(txt);
           }
     });
}

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    $('#hora').html(h + ":" + m);
    setTimeout(startTime, 500);
}

function checkTime(i) {
    if (i < 10) {
        i = "0" + i
    }
    ;  // add zero in front of numbers < 10
    return i;
}

var videos = [];

function carregaVideos() {
    $.ajax({
        url: baseUrl + "/default/guiche/retorna-video",
        type: 'GET',
        success: function (dados) {
            videos = dados;
            $('#player-video').attr('src', dados[0]);
            //Dados do Tempo a cada 2 minutos
            getDadosTempo($.cookie('cid_nome'));
            setInterval(function () {
                getDadosTempo($.cookie('cid_nome'));
            }, 120000);
        }
    });
}

function videoEnded() {
    var number = Math.floor(Math.random() * videos.length);
    $('#player-video').get(0).src = videos[number];
    $('#player-video').get(0).load();
    $('#player-video').get(0).play();
}

function getDadosTempo(cid_nome) {
    var cidade = cid_nome ? cid_nome : "Maringá";
    var pais = "BR"; //Two digit country code
    var keyCode = "1892706d77b2369fc4871b985552e60d";
    $('.city').html(cidade);
    $('.graus').html('');
    $('.tempo').html('');
    $.ajax({
        url: "http://api.openweathermap.org/data/2.5/weather?q=" + cidade + "," + pais + "&units=metric&cnt=7&lang=pt&APPID=" + keyCode,
        type: 'GET',
        success: function (dados) {
            var grau = parseFloat(dados.main.temp);
            $('.img-tempo').attr('src', "http://openweathermap.org/img/w/" + dados.weather[0].icon + ".png");
            $('.graus').append(dados.main.temp.toFixed(1));
            $('.tempo').append(dados.weather[0].description);
        }
    });
}

function verificaAlteracoes(uni_codigo) {
    $.ajax({
        url: baseUrl + "/default/guiche/get-last-index/uni_codigo/" + uni_codigo,
        type: 'GET',
        success: function (txt) {
            console.log();
            if (txt.max !== null) {
                if (data_hora !== txt.max) {
                    data_hora = txt.max;
                    buscarChamadas(uni_codigo);
                    var t = buscarChamadas(uni_codigo);
            console.log('TESTE:'+uni_codigo);
                }
            } else {
                if (!passou) {
                    var tpl = (txt.max !== null ? txt : "<h1><p class='medico'>Não há pacientes na lista de espera.</p></h1>")
                    $(".chamada").html(tpl);
                    passou = true;
            console.log();
                }
            }
        }
    })
}
var data_hora = "";
var passou = false;