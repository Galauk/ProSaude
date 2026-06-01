<?php
include "../global.php";
include "../../WebSocialComum/library/php/funcoes.db.php";
set_time_limit(0);

$sqlIdiota = "select * from agendamento_exame_lista where agex_codigo in (1083,1107,1111,1112,1109,1110,1121,1255) and agexl_codigo not in (select agei_codigo from agenda_itens)";
$query = pg_query($sqlIdiota);
while($registroLista = pg_fetch_array($query)){
	$insertAgendaLista = "INSERT 
                                INTO agenda_itens(agei_codigo,
                                                  age_codigo,
                                                  agei_data,
                                                  agei_hora,
                                                  coni_codigo,
                                                  agei_status)
                                           VALUES ($registroLista[agexl_codigo],
                                                   $registroLista[agex_codigo],
                                                   '$registroLista[agexl_data]',
                                                   ".($registroLista[agexl_hora] == "" ? "null" : "'$registroLista[agexl_hora]'").",
                                                   217,
                                                   '$registroLista[agexl_status]')";
     $queryAgendaLista = pg_query($insertAgendaLista) or die($insertAgendaLista.pg_last_error());
     echo $insertAgendaLista."<br/>";
}
echo "FINISH HIM";