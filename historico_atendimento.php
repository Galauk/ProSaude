<?php

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
  
	verauth($id_login);

echo "<body>
      <link href='estilo.css' rel='stylesheet' type='text/css'>
      <link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
	  <link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />";
//------------------------------------------------------------------>
$form = new classForm();
$table = new tableClass();
$commom = new commonClass();
 $Age = pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
 $usu_codigo = $Age[usu_codigo];
$array = array( 'Data',
	            'Hora',
	            'Tipo',
	            'Especialidade',
	            'Médico',
         		'Unidade');

$sql=pg_query("select agt_codigo,to_char(age_data,'DD/MM/YYYY') as age_data,age_codigo,med_codigo,age_hora,usu_codigo,age_tipo,age_atendido,age_paciente,uni_codigo,age_item,esp_codigo from agendamento where usu_codigo='$usu_codigo' order by to_char(age_data,'YYYY') desc,to_char(age_data,'MM') desc,to_char(age_data,'DD') desc");

echo $commom->incJquery();
/*echo $table->openTable('lista','900');
echo 	$table->criaLinhaTh($array);
echo $table->closeTable();*/

 echo "<table width=40% class='lista'>
        <tr>
         <th>Data</td>
         <th>Hora</td>
         <th>Tipo</td>
         <th>Especialidade</td>
         <th>Médico</td>
         <th>Unidade</td>
        </tr>";
while($row=pg_fetch_array($sql)) {
  if($row[age_atendido] == "S") { $bold_font_open="<font color=blue><b>Recepcionado</font></b>"; }
  if($row[age_atendido] == "N") { $bold_font_open="Agendado"; }
  if($row[age_atendido] == "F") { $bold_font_open="<font color=red><b>Faltou</font></b>"; }
  if($row[age_atendido] == "T") { $bold_font_open="<font color=orange><b>Transferido</font></b>"; }
 $esp=pg_fetch_array(pg_query("select *from especialidade where esp_codigo='$row[esp_codigo]'"));
 $med=pg_fetch_array(pg_query("select *from medico where med_codigo='$row[med_codigo]'"));
 $uni=pg_fetch_array(pg_query("select *from unidade where uni_codigo='$row[uni_codigo]'"));
  echo "<tr>
         <td><a href='#' OnClick='window.open(\"print_guia_2via.php?uni_codigo=$row[uni_codigo]&esp_codigo=$row[esp_codigo]&agt_codigo=$row[agt_codigo]&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]&med_codigo=$row[med_codigo]\",null,\"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_prontuario.jpg border=0></a></td>
         <td>$row[age_data]</td>
         <td>$row[age_hora]</td>
         <td>$bold_font_open</td>
         <td>$esp[esp_nome]</td>
         <td>$med[med_nome]</td>
         <td>$uni[uni_desc]</td>
        </tr>";
}
echo "</table>";



?>
