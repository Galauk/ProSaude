<?php
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
		$med_codigo = $_GET["med_codigo"];
	
	$selectMedico = "SELECT usr_tipo_medico
					   FROM usuarios
					  WHERE usr_codigo = $med_codigo";
	$queryMedico = pg_query($selectMedico);
	$linhaMedico = pg_fetch_array($queryMedico);
	$med_espec = $linhaMedico[usr_tipo_medico];
	if($med_espec == "E"){
		$palavra = "enferm";
	}else if($med_espec == "M"){
		$palavra = "Médico";
		$and2 = "or esp_nome ILIKE '%Fisiotera%' OR esp_nome ilike '%Fonoa%'";
	}else if($med_espec == "D"){
		$palavra = "Dentist";
	}else if($med_espec == "P"){
		$palavra = "psicó";
	}else if($med_espec == "F"){
		$palavra = "Farma";
	}
	else if($med_espec == "A"){
		$palavra = "Auxiliar de enfer";
	}
	$sl = "SELECT esp_codigo, 
				  esp_nome,cod_cbo 
			 FROM especialidade 
			WHERE retira_acentos(esp_nome) ILIKE retira_acentos('%$palavra%') $and2
			  AND esp_codigo NOT IN (SELECT esp_codigo 
			  						   FROM medico_especialidade 
			  						  WHERE med_codigo = $med_codigo) order by esp_nome";
	//echo $sl."---".$selectMedico;
	$sql = pg_query($sl);
	
	echo "<select name='esp_codigo' class='boxr'>";
		while($linhaEspecialidade = pg_fetch_row($sql)){
			echo "<option value='$linhaEspecialidade[0]'>".trim($linhaEspecialidade[2])." - ".trim($linhaEspecialidade[1])."</option>";
			//echo  $linhaEspecialidade[1]."aaaa";
		}
	echo "</select>";
	
?>
