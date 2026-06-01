<?php
include "../global.php";
include "../../WebSocialComum/library/php/funcoes.db.php";
set_time_limit(0);

$sql = "select mes.esp_codigo,med_codigo,mes.uni_codigo from usuarios as usr
INNER JOIN medico_especialidade AS mes ON usr.usr_codigo=mes.med_codigo 
INNER JOIN especialidade AS esp ON mes.esp_codigo=esp.esp_codigo 
limit 10";
$query = pg_query($sql);
while($rr = pg_fetch_array($query)){
/*	$up = pg_query("update esus_atendimento_individual set eai_cbo_codigo_2002 = '".$rr['cod_cbo']."' where eai_profissional_cns = '".$rr['cnes_cod_cns']."'");
*/
  echo "update agendamento set esp_codigo = '".$rr['esp_codigo']."' where uni_codigo = '".$rr['uni_codigo']."' and med_codigo  = '".$rr['med_codigo']."' <br>";
}
	echo "FEITO";
?>