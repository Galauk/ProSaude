const TEMPO_LIMITE = 15 * 60 * 1000; // 15 minutos

let tempoInatividade;

function reiniciarTimer() {

    clearTimeout(tempoInatividade);

    tempoInatividade = setTimeout(() => {

        alert(
            'Sua sessão expirou por inatividade.'
        );

        window.location.href = '/logout';

    }, TEMPO_LIMITE);
}

[
    'mousemove',
    'keypress',
    'click',
    'scroll'
].forEach(evento => {

    document.addEventListener(
        evento,
        reiniciarTimer
    );

});

reiniciarTimer();

/*
----------------------
#####codigo antigo####
----------------------
var timerID = null;
var timerRunning = false;
function stopclock() {
    if(timerRunning){
        clearTimeout(timerID)
	}
    timerRunning = false;
}

function startclock(){
    stopclock();
    showtime();
}

function showtime(){
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var timeValue = "" + ((hours > 12) ? hours - 12 : hours);
    timeValue  += ((minutes < 10) ? ":0" : ":") + minutes;
    timeValue  += ((seconds < 10) ? ":0" : ":") + seconds;
    timeValue  += (hours >= 12) ? " P.M." : " A.M.";
    document.clock.face.value = timeValue;
    timerID = setTimeout("showtime()",1000);
    timerRunning = true;
}


var tempo = new Number();
// Tempo em segundos
tempo = 1200;

function startCountdown(){

	// Se o tempo n�o for zerado
	if((tempo - 1) >= 0){

		// Pega a parte inteira dos minutos
		var min = parseInt(tempo/60);
		// Calcula os segundos restantes
		var seg = tempo%60;

		// Formata o n�mero menor que dez, ex: 08, 07, ...
		if(min < 10){
			min = "0"+min;
			min = min.substr(0, 2);
		}
		if(seg <=9){
			seg = "0"+seg;
		}

		// Cria a vari�vel para formatar no estilo hora/cron�metro
		horaImprimivel = '00:' + min + ':' + seg;
		//JQuery pra setar o valor
		//$("#sessao").html("Sua sess�o vai expirar em:  "+ horaImprimivel);

		// Define que a fun��o ser� executada novamente em 1000ms = 1 segundo
		setTimeout('startCountdown()',1000);

		// diminui o tempo
		tempo--;

	// Quando o contador chegar a zero faz esta a��o
	} else {
		//window.open('logoff.php', '_self');
	}
}
startCountdown()
*/