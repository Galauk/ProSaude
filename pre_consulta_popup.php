<?php
/** 
 * pre consulta janela 
*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
include_once $_SESSION[root].$_SESSION[modulo]."anamnese.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
$common = new commonClass();
echo $common->incJquery(); 
/*echo "
<body bgcolor='#E6E6E6'>
<link href='estilo.css' rel='stylesheet' type='text/css'>
<link href='estilo_janela.css' rel='stylesheet' type='text/css'>
";*/


$stmt = "SELECT *, TO_CHAR(pc_data, 'dd/mm/yyyy hh24:mi') as data FROM pre_consulta WHERE pc_codigo=$codigo";
$row = db_getRow($stmt);

$tabela = "

<table class='lista'>
	<tr>
		<td width='225' style='font-weight:bold;'>Temperatura (C)</td>
		<td>$row[pc_temperatura]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Peso (Kg)</td>
		<td>$row[pc_peso]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Altura (cm)</td>
		<td>$row[pc_altura]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Frequencia Cardiaca (pulso/BPM)</td>
		<td>$row[pc_freq_cardiaca]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Freq Respiratoria (MPM)</td>
		<td>$row[pc_freq_respiratoria]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Perimetro_cefalico</td>
		<td>$row[pc_perimetro_cefalico]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Pressao Sistolica (mm/Hg)</td>
		<td>$row[pc_pressao_sistolica]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Pressao Diastolica (mm/Hg)</td>
		<td>$row[pc_pressao_diastolica]</td>
	</tr>
	<tr>
		<td style='font-weight:bold;'>Dados</td>
		<td>".nl2br($row['pc_dados'])."</td>
	</tr>
</table>";

echo $common->openModal("Historico da Pre Consulta ($row[data])", 700, "OK");
	echo $tabela;
echo $common->closeModal();
?>