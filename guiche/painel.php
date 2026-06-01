<!DOCTYPE html>
<head>
    <title>Guichê</title>
    <meta http-equiv="refresh" content="1200">
    <script type="text/javascript">
        var baseUrl = '/WebSocialSaude';
        </script>
    <link rel="stylesheet" href="guiche.css">
    <script src="/WebSocialSaude/zf/public/js/jquery-1.6.2.min.js"></script>
    <script src="/WebSocialSaude/zf/public/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="/WebSocialSaude/zf/public/js/jquery.cookie.js"></script>
    <script src="/WebSocialSaude/zf/public/js/jquery.fullscreen.min.js"></script>
</head>
<body>
    <div id="page">
        <div id="central">
            <!--<div id="topo">
                <div id="topo-esquerda">            
                    <img class='img-tempo'>
                    <div class='graus'></div>
                    <div class='tempo' style='text-transform: capitalize;'></div>
                    <h3 class='city'></h3>
                    <div id='hora' class='hora'></div>
                    <div class='date'><?= date('d/m/Y');?></div>          
                </div>
            </div>-->
            <div id="esquerda">        
                <div id="">
                    <!-- <video id="mediaElement" width="100%" height="621" src="" controls autoplay loop>
                        Seu navegador não suporta o elemento <code>video</code>.
                    </video> -->
                    <figure style="margin: 0 !important; padding: 0 !important;">
                        <img src="../zf/public/images/manchester.jpg" width="100%" style="height: 540px;">
                    </figure>
                    <!--<embed src="C:\Variedades\VideoDoidao\MOV01741.AVI" width="50" height="50"> </embed>-->
                </div>
            </div>
            <div id="direita">
                <div class="espera">
                    2º Chamada:
                    <div>
                        <div id="cor-2" style="width: 35px; height: 35px; float: left; margin-right: 10px;"></div>
                        <div id="pac-2" style="margin-left:10px;   font-size: 30px;"></div>
                    </div>
                <div>
                        <div class="setor">Setor: &nbsp;</div> <div id="set-2"></div>
                    </div>
                </div>
                <div class="espera">
                    3º Chamada:
                    <div>
                        <div id="cor-3" style="width: 35px; height: 35px; float: left; margin-right: 10px;"></div>
                        <div id="pac-3" style="margin-left:10px; font-size: 30px;"></div>
                    </div>
                    <div>
                        <div class="setor">Setor: &nbsp;</div> <div id="set-3"></div>
                    </div>
                </div>
                <div class="espera">
                    4º Chamada:
                    <div>
                        <div id="cor-4" style="width: 35px; height: 35px; float: left; margin-right: 10px;"></div>
                        <div id="pac-4" style="margin-left:10px;  font-size: 30px;"></div>
                    </div>
                    <div>
                        <div class="setor">Setor: &nbsp;</div> <div id="set-4"></div>
                    </div>
                </div>            
            </div>
            <div id="proximo">        
                <div id="pac-1" style="padding-left: 30px; padding-top: 10px;">
                    <div id="cor-1" style="width: 60px; height:60px; /*background-color: ;*/ float: left; margin-right:5px; padding-left: 30px; padding-top: 10px;"></div>
                    <div id="paciente" style="margin-left:10px; font-size: 55px; font-family: Verdana"></div>       
                </div>
                <div style="padding-left: 30px">
                    <div id="set-pac1" style="font-size: 30px;color:#5FBAFF;left:65px;position: relative"><b></b></div>
                </div>
            </div>
        </div>
    </div>

    <audio id='chatAudio'></audio>


    <script src="text-to-speech/lib/index.js"></script>

    <script>
    function carregaDadosIniciais() {
        setInterval(function () {
            buscarChamadas()
        }, 3000)
    }
    <?php
    session_start();
    ?>

    function buscarChamadas() {
        $.ajax({
            url: baseUrl + "/guiche/consulta.php?uni_codigo=<?=$_SESSION['unidade']?>",
            type: 'GET',
            success: function (res) {
                var dados = JSON.parse(res)
                
                var templateAtual = ""
                
                if (dados.length > 0) {
                    // Chamada atual
                    var cor = ''
                    if(dados[0].cor != null) {
                        cor = dados[0].cor
                    } else {
                        cor = '#7c7c7c'
                    }

                    templateAtual = ""+
                    "<div id='pac-1' style='padding-left: 30px; padding-top: 10px;'>"+
                    "   <div id='cor-1' style='width: 60px;height:60px; background-color:"+cor+";float: left; margin-right:5px;'>"+
                    "   </div>"+
                    "   <div id='paciente' style='margin-left:10px; font-size: 55px;'>"+dados[0].age_paciente+"</div>"+       
                    "</div>"+
                    "<div style='padding-left: 30px; padding-top: 5px;'>"+
                    "   <div id='set-pac1' style='font-size: 30px; color: #5FBAFF; margin-left: 65px; max-width: 90%; position: relative'><b>"+dados[0].set_nome+"</div>"+
                    "</div>"
                    
                    if(dados[0].cha_status == "C"){
                        
                        //setTimeout(() => {
                            let setor = ""
                            let chamada = ""

                            setor = dados[0].set_nome.includes('FARMACIA') ? dados[0].set_nome.replace('FARMACIA', 'FARMÁCIA') : dados[0].set_nome
                            chamada = dados[0].age_paciente+"; favor encaminhar-se à "+setor
                            
                            $.ajax({
                                url: 'getChamada.php',
                                type: 'POST',
                                data: {'text': chamada},
                                success: retorno => {
                                    if(retorno == "ok"){
                                        $.get('chamada.mp3', audio => {
                                            document.querySelector('audio').src = "chamada.mp3"
                                            $("#proximo").html(templateAtual)
                                            document.querySelector('audio').play()
                                        })
                                    }
                                }
                            })
                        //}, 300)
                        
                        $("#pac-2").html(dados[1].age_paciente);
                        $("#set-2").html(dados[1].set_nome);
                        $("#cor-2").css('background-color', dados[1].cor);
                        
                        $("#pac-3").html(dados[2].age_paciente);
                        $("#set-3").html(dados[2].set_nome);
                        $("#cor-3").css('background-color', dados[2].cor);
                        
                        $("#pac-4").html(dados[3].age_paciente);
                        $("#set-4").html(dados[3].set_nome);
                        $("#cor-4").css('background-color', dados[3].cor);
                                                    
                        alteraStatus(dados[0].age_codigo);
                    }

                    //Chamadas anteriores
                    
                    $.each(dados, function (index, chamada) {
                        
                        if (index > 0) {
                            $("#pac-2").html(dados[1].age_paciente);
                            $("#set-2").html(dados[1].set_nome);
                            $("#cor-2").css('background-color', dados[1].cor);
                            
                            $("#pac-3").html(dados[2].age_paciente);
                            $("#set-3").html(dados[2].set_nome);
                            $("#cor-3").css('background-color', dados[2].cor);
                            
                            $("#pac-4").html(dados[3].age_paciente);
                            $("#set-4").html(dados[3].set_nome);
                            $("#cor-4").css('background-color', dados[3].cor);
                        }
                    })
                } else {
                    templateAtual = "<p class='paciente'>Não há pacientes na fila no momento.</p>"
                }
                
                $("#proximo").html(templateAtual)

                $("#cor-1").css('background-color', cor)
            }
        })
    }

    function alteraStatus(age_codigo){    
        $.ajax({
            url: baseUrl+"/guiche/altera-status.php",
            data: {
                age_codigo: age_codigo
            },
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
        
        return i;
    }

    $(function () {
        startTime()
        carregaDadosIniciais()
    })

    /*window.onload=() => {
        $('#page').fullscreen()
        alert("teste");
    }*/

    $(document).ready(() => {
        
    })
    </script>