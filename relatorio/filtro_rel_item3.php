<?php
/**
 * @brief	Adicionando novas opções para filtro
 */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
?>
<html>
<head>
<title>AIH Utilizado por prestador</title>
<script src="funcoes.js"></script>
<script language="JavaScript">
function Emite_relatorio()
{
	municipio = document.getElementById('municipio').value;
	mes = document.getElementById('mes').value;
	ano = document.getElementById('ano').value;
	
	window.open("rel_item3.php?municipio="+municipio+"&mes="+mes+"&ano="+ano,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}	
		
</script>
</head>	
<body>
<form method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
<fieldset>
<legend>Interna&ccedil;&atilde;o por munic&iacute;pio, N&#186; Absoluto por Compet&ecirc;ncia</legend>
	<table>
		<tr>
			<td width='100px'><label for="municipio">Municipio</label></td>
			<td>
				<select name="municipio" id="municipio" class="box">
					<option value=''>-------Todos-----</option>
					<?php
						$sql=db_query("select * from cidade order by cid_nome");
						while ($reg=pg_fetch_array($sql)) {
							echo "<option value=$reg[cid_codigo_ibge]>$reg[cid_nome]</option>";
						}
					?>
				</select>
			</td>
		<tr id='tp_1'>
			<td valign='bottom'>Compet&ecirc;ncia: </td>
			<td colspan='2'>
				<select name='mes' id='mes' class=box>
					<option value='' selected> -- Selecione o m&ecirc;s -- </option>
					<option value='01'> Janeiro </option>
					<option value='02'> Fevereiro </option>
					<option value='03'> Mar&ccedil;o </option>
					<option value='04'> Abril </option>
					<option value='05'> Maio </option>
					<option value='06'> Junho </option>
					<option value='07'> Julho </option>
					<option value='08'> Agosto </option>
					<option value='09'> Setembro </option>
					<option value='10'> Outubro </option>
					<option value='11'> Novembro </option>
					<option value='12'> Dezembro </option>
				</select> / 
				<select name='ano' id='ano' class='box'>
<?
$ano = date("Y");
for($i = ($ano - 5); $i <= $ano; $i++)
{
	if($i == $ano)
	{
		echo "<option value='$i' selected>$i</option>";
	} else {
		echo "<option value='$i'>$i</option>";
	}
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" />
			</td>
			<td>
				<a href="../rel_index.php?id_login=<?=$id_login?>&opcao=5#tabs-5"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a>
			</td>
		</tr>
	</table>
</fieldset>
</form>
</body>
</html>