<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/calendario.js"></script>
<link rel="stylesheet" type=text/css href="js/jquery-ui-1.7.2.custom.css" />
</head>
<body>
<?
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once "funcao.calendarioAg.php";
$med_nome = $_GET['pac_nome'];
$med_codigo = $_GET['pac_codigo'];

echo "
	<fieldset>
	
	</fieldset>
	<table border='0'>

		<tr>
			<td class='primeiroNome' width='10px'>
				M&eacute;dico:
			</td>
			<td class='segundoNome' width='130px'>
				$med_nome 
			</td>
			<td class='primeiroNome' width='10px'>
				Unidade:
			</td>
			<td class='segundoNome'>
				UBS Central
			</td>
		</tr>
		<tr>
			<td class='primeiroNome' width='10px'>
				Especialidade:
			</td>
			<td class='segundoNome'>";
			$stmt = "select med.med_codigo,med.med_nome,esp.esp_nome 
					   from medico as med
					   join medico_especialidade as medesp
						 on med.med_codigo = medesp.med_codigo
					   join especialidade as esp
						 on esp.esp_codigo = medesp.esp_codigo
					  where med.med_codigo = $med_codigo";
			$qry = pg_query( $stmt );
			echo "
			<select id='esp_codigo' class='boxa'>
				<option value='0'>.....</option>";		
					while( $esp = pg_fetch_array($qry) )
					{
						echo "<option value='{$esp[0]}'>{$esp[2]}</option>";
					}	
			echo "</select>";
		echo"</td>
			<td rowspan='2'>
			</td>
		</tr>
		<tr>
			<td>
				<span onClick='chamaAgenda()'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazer_agenda_on.jpg'></span>
			</td>
		</tr>
	</table>";
	
	
	
?>
</body>
</html>
