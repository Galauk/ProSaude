<?php
session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario($hotkey = true);
echo "<style type='text/css'>
			.quebra_pagina{
			page-break-before:always;
			}
			tr{
			font-size:12px;
			}
			</style>";
echo "<link href='".$_SESSION[root].$_SESSION[modulo]."estilo.css' rel='stylesheet' type='text/css'>\n";			

?>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=script.js></script>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript">
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);
function CheckDate(d,t) {
	   date_array = new Array(3);
	   date_array[0]=(String(d).substr(0,2))    // dia
	   date_array[1]=(String(d).substr(3,2))    // mes
	   date_array[2]=(String(d).substr(6,4))    // ano
	//alert(date_array[0]+"~~~~"+date_array[1]+"~~~~"+date_array[2]);
	   if (date_array[0] > maxDay[date_array[1]-1]) {
	       alert ("Dia invalido da data " + t)
	       return 1;
	   }
	   if (date_array[1] > 12) {
	       alert ("Mes invalido da data " + t)
	       return 1;
	   }
	   if (date_array[2] < 2006) {
	       alert ("Ano invalido da data " + t)
	       return 1;
	   }
	}
function Emite_relatorio() {
	data_ini = document.getElementById('data_ini').value;
	data_fim = document.getElementById('data_fim').value;
    set_codigo = document.getElementById('set_codigo').value;

	if(data_ini == ""){
		alert('O campo data Inical esta vazia!')
		document.getElementById('data_ini').focus()
		return false
	}
	if(data_ini == ""){
		alert('O campo data Final esta vazia!')
		document.getElementById('data_fim').focus()
		return false
	}
	

    if (CheckDate(data_ini,"INICIAL")==1) {
	    document.getElementById('data_ini').focus()
      	return false
	 }
	if (CheckDate(data_fim,"FINAL")==1) {
	    document.getElementById('data_fim').focus()
      	return false
	 }
	
	window.open("rel_LibroPsico.php.php?set_codigo="+set_codigo+"&data_ini="+data_ini+"&data_fim="+data_fim,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return false;
}
</script>
</head>
<body>
<fieldset>
<legend>Pacientes IMC</legend>
<form method="post" action="relatorio/rel_imc.php" target='_blank'>
	<table>
		
       
        <tr>
			<td width='30' align=right>Data In&iacute;cio: </td>
			<td><input type="text" name="data_ini" id="data_ini" size="15" class="box" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>
		
		<tr>
			<td width='100' align=right>Data Final: </td>
			<td><input type="text" name="data_fim" id="data_fim" size="15" class="box" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>
		
		<tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
		</tr>
	</table>

</form>
</fieldset>
</body>