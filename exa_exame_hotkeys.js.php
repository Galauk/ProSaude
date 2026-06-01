/**
 * @version 2007-06-11 18:00:00
 * @author Eduardo <dudu@g1ti.com.br>
 * 
 * Arquivo referente ao 'fazer_agendamento.php'
*/
alert('yo')
// atualiza o SELECT dos medicos...
function atualiza_medico()
{
	var esp = $('esp_codigo'), med = $('med_codigo');
	
	if( esp.value == 0 ) return false;
	
	var url = 'fazer_agendamento.ajax.php?acao=busca_medico&esp_codigo='+esp.value;
	ajax_tudo( url, alert );
}