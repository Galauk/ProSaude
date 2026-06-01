<?
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
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>GPS - Software de Gest�o P�blica</title>
<script src="funcoes.js"></script>
<script language="JavaScript">
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);

function CheckDate(d,t) {
	   date_array = new Array(3);
	   date_array[0]=(String(d).substr(0,2))    // dia
	   date_array[1]=(String(d).substr(3,2))    // mes
	   date_array[2]=(String(d).substr(6,4))    // ano
//alert(date_array[0]+"---"+date_array[1]+"---"+date_array[2])
	   if (date_array[0] > maxDay[date_array[1]-1]) {
	       alert ("Dia invalido da data! Por favor, verifique!! " + t);
	       return 1;
	   }
	   if (date_array[1] > 12) {
	       alert ("Mes invalido da data! Por favor, verifique! " + t);
	       return 1;
	   }
	   if (date_array[2] < 2006) {
	       alert ("Ano invalido da data! Por favor, verifique! " + t);
	       return 1;
	   }
	}
function Emite_relatorio(){
	cod_pac = document.getElementById('pac_nome').value;
	cod_pro = document.getElementById('cbo_produto').value;
	data_ini = document.getElementById('data_ini').value;
	data_fim = document.getElementById('data_fim').value;
	centro_estocador = document.getElementById('centro_estocador').value;

	if (CheckDate(data_ini,"INICIAL")==1) {
	    document.getElementById('data_ini').focus()
      	return false
	 }
	if (CheckDate(data_fim,"FINAL")==1) {
	    document.getElementById('data_fim').focus()
      	return false
	 }
	if (data_ini.length < 10 || data_fim.length < 10) {
		alert('Informe as datas corretamente!');
		return false;
	} else {
		parent.open("relatorio/rel_pacientes_medicamentos.php?centro_estocador="+centro_estocador+"&cod_pac="+cod_pac+"&cod_pro="+cod_pro+"&data_ini="+data_ini+"&data_fim="+data_fim,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
		return true;
	}
}

function pacientes(codigo,nome,nascimento,mae,cidade) {
	document.getElementById("pac_nome").value = codigo;
}

function hotkey(eventname) {
	if( eventname.keyCode == 118 ) {
		window.open('../paciente_medicamentos?id_login='+<?=$id_login?>,null,'height=460,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
        return false;
	}
}
</script>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=script.js></script>

</head>
<body>
<fieldset>
<legend>Pacientes por Medicamento</legend>
<form method="post" onSubmit="return Emite_relatorio()">
	<table>
		<tr>
			<td width="20%">Paciente: </td>
			<td><input type="text" name="pac_nome" id="pac_nome" size="15" class="box"><a OnClick="window.open('list_pacientes.php?id_login=$id_login',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');"> <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg" align="absmiddle" border="0"></a> (F7)</td>
		</tr>

		<tr>
			<td width="20%">Produto: </td>
			<td><select name="cbo_produto" class="box" id="cbo_produto">
				<option value="-1">----TODOS----</option>
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
			<td>Data Inicial: </td>
			<td><input type="text" name="data_ini" id="data_ini" class="box" size="15" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>

		<tr>
			<td>Data Final: </td>
			<td><input type="text" name="data_fim" id="data_fim" class="box" size="15" maxlength="10" onKeypress="return Ajusta_Data(this, event);"></td>
		</tr>

        <tr>
			<td>Centro Estocador: </td>
			<td><select name="centro_estocador" id="centro_estocador" class="box">
                <option value="-1">----Todos----</option>
                <?
				// Lista os produtos
				$sql = "SELECT s.set_codigo,
								   set_nome
							  FROM Setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							ORDER BY set_nome";
				$query = pg_query($sql) or die(pg_last_error());
				$resultado = pg_num_rows($query);
					if ($resultado != 0) {
						while($row = pg_fetch_array($query)) {
				?>
				<option value="<?=$row[set_codigo]?>"><?=$row[set_nome]?></option>
				<?
						}
					}
				?>
            </select></td>
		</tr>

		<tr>
			<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
			<td align="right"><a href="../rel_index.php?id_login=<?=$id_login?>&opcao=8#tabs-4"><img src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif border =0></a></td>
		</tr>
	</table>

</form>
</fieldset>
</body>
