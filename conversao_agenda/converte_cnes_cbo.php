<?php
include "../global.php";
include "../../WebSocialComum/library/php/funcoes.db.php";
set_time_limit(0);

function atualizacnes_convenio() {
  $sql = "select *from usuarios as usr
INNER JOIN medico_especialidade AS mes ON usr.usr_codigo=mes.med_codigo 
INNER JOIN especialidade AS esp ON mes.esp_codigo=esp.esp_codigo 
order by usr_nome";
$query = pg_query($sql);
while($rr = pg_fetch_array($query)){
	$up = pg_query("update convenio_itens set esp_codigo = ".$rr['esp_codigo']." where usr_codigo = ".$rr['usr_codigo']);
 }
  return "ok";
}


echo atualizacnes_convenio();
?>