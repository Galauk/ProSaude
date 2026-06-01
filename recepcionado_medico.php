<html xmlns="http://www.w3.org/1999/xhtml">
 <head>

 </head>

 <body bgcolor="#FFFFFF">

<?php 

 	$unidades = array('P' => 'PAM',
					  'O' => 'ÓBITO',
					  'E' => 'ESCOLA DA GESTANTE',
					  'C' => 'CENTRO INFANTIL',
	  				  'N' => 'NATTA',
					  'EM' => 'EM TRÂNSITO'				 
					  );
?>
<script>
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;
	
   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}

function alteraLocation(menuObj, url, id_login, med_codigo, esp_codigo){
	var i = menuObj.selectedIndex;
	alert(menuObj.options[i].value);
	if(i > 0){
		window.location = url+"?id_login="+id_login+"&uni_codigo="+menuObj.options[i].value+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo;
	}
}

 function msg(id_login,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,usu_codigo) {
	 //if(confirm("Deseja iniciar atendimento para esse paciente?"))
     	location.href="prontuario.php?pagina=99&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&age_codigo="+age_codigo+"&usu_codigo="+usu_codigo+"&id_login="+id_login;
 }
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once "authlib.inc.php";
		verauth($id_login);
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
echo "<body>
      <link href='estilo.css' rel='stylesheet' type='text/css'>
      <link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
	  <link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />";
//------------------------------------------------------------------>

 $med = pg_fetch_array(pg_query("select * from usuarios where usr_codigo='$id_login'"));
 $med_codigo = $med[med_codigo];
$form = new classForm();
$common = new commonClass();
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
         reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>

#
# Configuracoes do medico
#

 $data_hoje = date("Y-m-d");


 $Sve = pg_query("  SELECT us.usr_codigo,
					       l.esp_codigo,
					       l.uni_codigo 
					  FROM usuarios us 
					  JOIN logon l 
					    ON id_login = usr_codigo 
					  JOIN unidade uni 
					    ON uni.uni_codigo = l.uni_codigo 
					  JOIN medico_especialidade me 
					    ON us.usr_codigo = me.med_codigo 
					  JOIN especialidade e 
					    ON e.esp_codigo = me.esp_codigo 
					 WHERE usr_codigo = $id_login");
 
 
 $Vconf = pg_fetch_array($Sve);

 //
 //-> Vars Globais
 	  $med_codigo = $Vconf[usr_codigo];
      $esp_codigo2 = $Vconf[esp_codigo];
      $uni_codigo=$Vconf[uni_codigo];
 

	echo $common->incJquery();
	echo $common->menuTab(array('Pacientes Agendados'));
	echo $common->bodyTab('1');
	$table = new tableClass();
	echo $table->openTable("lista", "100%");
	echo $table->criaLinha(array("C&oacute;digo Pac.", "Paciente", "Idade", "M&atilde;e", "Situa&ccedil;&atilde;o", "Munic&iacute;pio"), null, null, "S");

	if($age_data=="") {
		$age_data = date("d/m/Y");
	}
	$dtn = explode("/",$age_data);
	$grm_data = "$dtn[2]-$dtn[1]-$dtn[0]";
	
	$sqlTipoMedico = "SELECT * 
    					FROM usuarios
    				   WHERE usr_codigo = $id_login";
    $queryTipoMedico = pg_query($sqlTipoMedico);
	$reg = pg_fetch_array($queryTipoMedico);
	
	if($reg['usr_tipo_medico'] == 'E' || $reg['usr_tipo_medico'] == 'A'){
		$sql = pg_query("SELECT a.*
					   FROM agendamento AS a
					   JOIN medico_especialidade AS m
					     ON m.med_codigo=a.med_codigo
					   JOIN especialidade AS e
					     ON e.esp_codigo=m.esp_codigo 
					  WHERE age_data='$grm_data' 
					   -- AND med_codigo = '$med_codigo' 
					   -- AND esp_codigo = '$esp_codigo2' 
					    AND uni_codigo = '$uni_codigo' 
					    AND age_atendido != 'A'
						AND age_atendido in ('S','P')
						AND e.esp_pre_consulta=true
					  ORDER BY age_codigo");
		
	}else{
		// verifica se a especialidade do médico pede pre-consulta
		$sql = "SELECT esp_pre_consulta 
		          FROM especialidade
		         WHERE esp_codigo=$esp_codigo2";
	
		$query = pg_query($sql);
		$age_atendido = (pg_result($query,0)=="t")?"('P','E')":"('P','E','S')";		
		
		$sql = pg_query("SELECT *
						   FROM agendamento 
						  WHERE age_data='$grm_data' 
						    AND med_codigo = '$med_codigo' 
						    AND esp_codigo = '$esp_codigo2' 
						    AND uni_codigo = '$uni_codigo'
						    AND age_atendido IN $age_atendido
						  ORDER BY age_codigo");
	}
	if(pg_num_rows($sql)=="0") {
		echo $table->criaLinha(array("Nenhum Agendamento Para Esta Data"),null, array(6));
	}
    while($row=pg_fetch_array($sql)) {
    $pac=pg_fetch_array(pg_query("select *from usuario where usu_codigo='$row[usu_codigo]'"));
    $calcdt=date("Y");
    $strip=explode("-",$pac[usu_datanasc]);
    $result_idade=($calcdt-$strip[0]);
	if($row[age_atendido] == "S") { 
		$bold_font_open="<font color=blue><b>"; $bold_font_close="</font></b>"; 
	}
	if($row[age_atendido] == "N") { 
		$bold_font_open=""; $bold_font_close=""; 
	}
	if($row[age_atendido] == "F") { 
		$bold_font_open="<font color=red><b>"; $bold_font_close="</font></b>"; 
	}
	if($row[age_atendido] == "T") { 
		$bold_font_open="<font color=orange><b>"; $bold_font_close="</font></b>"; 
	}
    if($row[age_atendido] == "P") { 
		$bold_font_open="<font color=blue><b>"; $bold_font_close="</font></b>"; 
	}
  	$arrayConteudo = array("$bold_font_open $row[usu_codigo] $bold_font_close", "$bold_font_open $pac[usu_nome] $bold_font_close", "$bold_font_open $result_idade(A) $bold_font_close", "$bold_font_open $pac[usu_mae] $bold_font_close", "$bold_font_open &nbsp; $bold_font_close", "$bold_font_open $pac[usu_end_cidade] $bold_font_close");
  	
	echo $table->criaLinha($arrayConteudo, null, null, "N", "onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$row[usu_codigo]');\"");
}
echo $table->closeTable();
echo $common->closeTab();
?>
 </body>
</html>