const TEMPO_LIMITE = 15 * 60; // 15 minutos em segundos
const TEMPO_ALERTA = 60;      // Mostrar alerta faltando 1 minuto

let tempoRestante = TEMPO_LIMITE;
let intervalo;
let tempoInatividade;

const contador = document.getElementById(
    'contador-sessao'
);

function atualizarContador() {

    const minutos = Math.floor(
        tempoRestante / 60
    );

    const segundos = tempoRestante % 60;

    contador.textContent =
        `${String(minutos).padStart(2, '0')}:` +
        `${String(segundos).padStart(2, '0')}`;

    // Alterar cor conforme o tempo restante
    contador.className = 'badge';

    if (tempoRestante > 300) {
        contador.classList.add('bg-success');
    } else if (tempoRestante > 60) {
        contador.classList.add('bg-warning');
    } else {
        contador.classList.add('bg-danger');
    }

    if (tempoRestante === TEMPO_ALERTA) {

        alert(
            'Sua sessão expirará em 1 minuto por inatividade.'
        );
    }

    if (tempoRestante <= 0) {

        clearInterval(intervalo);

        alert(
            'Sua sessão expirou por inatividade.'
        );

        window.location.href = '/logout';
    }

    tempoRestante--;
}

function reiniciarTimer() {

    clearTimeout(tempoInatividade);

    tempoRestante = TEMPO_LIMITE;

    atualizarContador();

    tempoInatividade = setTimeout(() => {

        clearInterval(intervalo);

        alert(
            'Sua sessão expirou por inatividade.'
        );

        window.location.href = '/logout';

    }, TEMPO_LIMITE * 1000);
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

intervalo = setInterval(
    atualizarContador,
    1000
);

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