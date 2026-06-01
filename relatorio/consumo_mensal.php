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
	data_mes = document.getElementById('data_mes').value;
	data_ano = document.getElementById('data_ano').value;
	setor = document.getElementById('setor').value;
	grupo = document.getElementById('grupo').value;
	tipo_saida = document.getElementById('tipo_saida').value;
	
	parent.open("rel_consumo_mensal.php?setor="+setor+"&grupo="+grupo+"&tipo_saida="+tipo_saida+"&data_mes="+data_mes+"&data_ano="+data_ano,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
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
			<td>Setor</td>
			<td><select id="setor" name="setor" class="box">
				<option value="-1" selected>...</option>
				<?
				$sql = "SELECT * FROM setor WHERE set_farmacia = 'S' ORDER BY set_nome ASC";
				$query = db_query($sql);
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
			<td>Grupo</td>
			<td><select id="grupo" name="grupo" class="box">
				<option value="-1" selected>...</option>
				<?
				$sql = "SELECT * FROM grupo ORDER BY gru_nome ASC";
				$query = db_query($sql);
				$resultado = pg_num_rows($query);
					if ($resultado != 0) {
						while($row = pg_fetch_array($query)) {				
				?>
				<option value="<?=$row[gru_codigo]?>"><?=$row[gru_nome]?></option>
				<?
						}
					}
				?>
			</select></td>
		</tr>
		
		<tr>
			<td>Tipo de Sa&iacute;da</td>
			<td><select id="tipo_saida" name="tipo_saida" class="box">
				<option value="-1">...</option>
				<option value="E">Entrada</option>
				<option value="S">Sa&iacute;da</option>
			</select></td>
		</tr>
		
		<tr>
			<td width='10%'>M&ecirc;s: </td>
			<td><select name="data_mes" id="data_mes" class="box">
                <option value="01" <? if (date("m") == "01") echo "selected"; ?>>01</option>
                <option value="02" <? if (date("m") == "02") echo "selected"; ?>>02</option>
                <option value="03" <? if (date("m") == "03") echo "selected"; ?>>03</option>
                <option value="04" <? if (date("m") == "04") echo "selected"; ?>>04</option>
                <option value="05" <? if (date("m") == "05") echo "selected"; ?>>05</option>
                <option value="06" <? if (date("m") == "06") echo "selected"; ?>>06</option>
                <option value="07" <? if (date("m") == "07") echo "selected"; ?>>07</option>
                <option value="08" <? if (date("m") == "08") echo "selected"; ?>>08</option>
                <option value="09" <? if (date("m") == "09") echo "selected"; ?>>09</option>
                <option value="10" <? if (date("m") == "10") echo "selected"; ?>>10</option>
                <option value="11" <? if (date("m") == "11") echo "selected"; ?>>11</option>
                <option value="12" <? if (date("m") == "12") echo "selected"; ?>>12</option>
            </select></td>
		</tr>
		
		<tr>
			<td width='10%'>Ano: </td>
			<td><select name="data_ano" id="data_ano" class="box">
                <?
                $data = date("Y");
                for ($i = 0; $i < 5; $i++) {
                    echo "<option value='".$data."'>".$data."</option>";
                    $data++;
                }                
                ?>
            </select>
            </td>
		</tr>
		
		<tr>
			<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg"></td>
			<td>
				<a href='../rel_index.php?id_login=$id_login&opcao=7'>
					<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif border" =0>
				</a>
			</td>
		</tr>
	</table>

</form>
</fieldset>
</body>