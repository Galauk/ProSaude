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
function Emite_relatorio() {
	pro_cod = document.getElementById('pro_cod').value;
	qtd_dia = document.getElementById('qtd_dia').value;

    if ( qtd_dia != "" ) {
        parent.open("relatorio/rel_PacientesAtendidos.php?pro_cod="+pro_cod+"&qtd_dia="+qtd_dia,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
        return true;
    } else {
        alert('Favor informar uma quantidade de dias.');
        return false;
    }
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

function ocultar_mostra(condicao) {
    document.getElementById('qtd_dia').style.visibility = condicao;
}
</script>
</head>
<body>
<fieldset>
<legend>Filtros</legend>
<form method="post" onsubmit="return Emite_relatorio()">
	<table>

		<tr>
			<td width='30'>Medicamentos: </td>
			<td>
            <select name="pro_cod" id="pro_cod" class="box">
                <option value="-1">----Todos----</option>
                <?
		// Lista os produtos
		$sql = "SELECT produto.pro_codigo,produto.pro_nome
			FROM produto
			ORDER BY pro_nome ASC";
		$query = pg_query($sql) or die(pg_last_error());
		$resultado = pg_num_rows($query);
			if ($resultado != 0) {
				while($row = pg_fetch_array($query)) {
		?>
		<option value="<?=$row[pro_codigo]?>"><?=$row[pro_nome]?></option>
		<?
				}
			}
		?>
            </select>
            </td>
		</tr>

        <tr>
			<td width='30'>Per&iacute;odo em dias:</td>
			<td>
            <input type="text" name="qtd_dia" id="qtd_dia" class="box">
            </td>
		</tr>

		<tr>
			<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
			<td align="right"><a href="../rel_index.php?id_login=<?=$id_login?>&opcao=8"><img src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif border =0></a></td>
		</tr>
	</table>

</form>
</fieldset>
</body>
