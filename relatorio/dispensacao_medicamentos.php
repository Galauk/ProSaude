<?php
	session_start();
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
echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";			

?>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=script.js></script>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>GPS - Software de Gestão Pública</title>
<script src="funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript">
function Emite_relatorio(){
	cod_pac = document.getElementById('pac_nome').value;
	data_ini = document.getElementById('data_ini').value;
	data_fim = document.getElementById('data_fim').value;
	
	parent.open("rel_dispensacao_medicamentos.php?cod_pac="+cod_pac+"&data_ini="+data_ini+"&data_fim="+data_fim,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return true;
}

function pacientes(codigo,nome,nascimento,mae,cidade) {
	document.getElementById("pac_nome").value = codigo;
}

function hotkey(eventname) {
	if( eventname.keyCode == 118 ) {
		window.open('../list_pacientes.php?id_login=$id_login',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
        return false;
	}
}
</script>
</head>
<body>
<fieldset>
<legend>Filtros</legend>
<form method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
	<table>
		<tr>
			<td width="20%">Paciente: </td>
			<td><input type="text" name="pac_nome" id="pac_nome" size="15" class="box"><a href='#' OnClick="window.open('../list_pacientes.php?id_login=$id_login',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');"> <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg" align="absmiddle" border="0"></a> (F7)</td>
		</tr>
		
		<tr>
			<td>Data Inicial: </td>
			<td><input type="text" name="data_ini" id="data_ini" class="box" size="15" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>
		
		<tr>
			<td>Data Final: </td>
			<td><input type="text" name="data_fim" id="data_fim" class="box" size="15" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>
		
		<tr>
			<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
		</tr>
	</table>

</form>
</fieldset>
</body>


