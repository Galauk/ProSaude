<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	//**E: EXTERNO 
	//**I: INTERNO
	$acao = $_GET['acao'];
	if($acao == "E"){
		$select = "SELECT med_codigo, 
						  upper(med_nome) as med_nome 
					 FROM medico 
					WHERE prestador_servico IS null 
					   OR prestador_servico = 'M' 
					ORDER BY med_nome";
		$js = "onclick='xx();'";
		$value = "E";
		$checked = "checked='checked'";
	}
	if($acao == "I"){
		$select = "SELECT usr_codigo, 
						  upper(usr_nome) as usr_nome 
					 FROM usuarios 
					WHERE usr_tipo_medico in ('M','D') 
					ORDER BY usr_nome";
		$js = "onclick='xi();'";
		$value = "I";
		$checked = "";
	}
	$query = pg_query($select);
	echo "<select id=medico name=med_codigo class=box style='width:210px;'>
			<option value='0'>..:: Selecione o Medico ::..</option>";
	while($registro = pg_fetch_row($query)){
		echo "<option value='$registro[0]'>$registro[1] </option>";
	}
	echo "</select>&nbsp;<input type ='checkbox' $js name='cad_medico_externo' id='flag_medico' value='$value' $checked>&nbsp;<b>Somente Externos</b>";
?>

