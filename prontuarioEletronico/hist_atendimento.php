<script>
function msg(id_login,ate_data,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,usu_codigo) {
    location.href="prontuario.php?pagina=19&age_data="+age_data+"&ate_data="+ate_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&age_codigo="+age_codigo+"&usu_codigo="+usu_codigo+"&id_login="+id_login;
}
</script>
<?php

//secho "<pre>".print_r($_GET,TRUE)."</pre>";
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
echo $common->menuTab(Array("Hist. Atendimento"));
	echo $common->bodyTab('1');
	$table = new tableClass();
	echo $table->openTable("lista", "100%");
	echo $table->criaLinha(array("Data.", "Unidade"), null, null, "S");
	$sql = "SELECT  to_char(ate_data,'dd/mm/yyyy') as data, 
					u.uni_desc
				FROM atendimento a 
				JOIN unidade u 
				  ON u.uni_codigo = a.uni_codigo				
				 WHERE usu_codigo=$usu_codigo order by ate_data desc";

	$query = pg_query($sql);
	
/*	$sql="select  a.ate_data,
			  u.uni_desc,
			  m.med_nome,
			  esp.esp_nome,
			  ate_reclamacao,
			  ate_tratamento 
		 FROM atendimento a
		 JOIN unidade u
		   ON u.uni_codigo = a.uni_codigo
		 JOIN medico m
		   ON a.med_codigo  = m.med_codigo 
		 JOIN especialidade esp
		   ON esp.esp_codigo = a.esp_codigo_encaminhamento
	    WHERE usu_codigo=$usu_codigo";*/
	//echo $sql;
	/*if(pg_num_rows($sql)=="0") {
		echo $table->criaLinha(array("Nenhum Agendamento Para Esta Data"),null, array(6));
	}*/
	while($row=pg_fetch_row($query)) {
		//$arrayConteudo = array("$row[ate_data]", "$row[med_nome]", "$row[med_nome]");
	echo $table->criaLinha($row, null, null, "N", "onclick=\"msg($id_login,'$row[0]','$age_codigo','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$usu_codigo');\"");
	}
		
	echo $common->closeTab();
?>