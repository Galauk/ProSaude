<?php
/**
 * obrigatório o envio da competência e db_query( $stmt, $LOG = false )
 */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario($hotkey = true);
?>
<html>
<head>
<title>AIH Utilizado por prestador</title>
<script src="ajax.js"></script>
<script src="ajax_motor.js"></script>
<script src="funcoes.js"></script>
<script language="JavaScript">
function Emite_relatorio() {
	med_codigo = document.getElementById('prestador').value;
	/* paciente = document.getElementById('paciente').value; */
	mes_comp = document.getElementById('mes').value;
	ano_comp = document.getElementById('ano').value;
	
	if( mes_comp == '' || ano_comp == '' )
	{
		alert('Entre com a competencia.');
		return false
	}
	
	window.open("rel_item2.php?prestador="+med_codigo+"&mes_comp="+mes_comp+"&ano_comp="+ano_comp,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return true;
}		
	
/*function pacientes(codigo,nome,nascimento,mae,cidade) {
	document.getElementById("paciente").value = codigo;
}*/

function hotkey(eventname) {
	if( eventname.keyCode == 118 ) {
		window.open('../list_pacientes.php?id_login=$id_login',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');
        return false;
	}
}
</script>
</head>	
<body>
<form method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
<fieldset>
<legend>N&uacute;mero de AIH, rela&ccedil;&atilde;o AIHs por Prestador</legend>
<table>
	<tr>
		<td><label for="prestador">Prestador</label></td>
		<td><select name="prestador" id="prestador" class="box">
						<option value=-1>-------Todos-----</option>
		<?php 
			$sql=db_query("SELECT * FROM medico order by med_nome", false);
			while($reg=pg_fetch_array($sql))
			{
				echo"<option value='$reg[med_codigo]'>$reg[med_nome]</option>";
			}
		?>
				</select>
		</td>
	</tr>
	
<!--	<tr>
		<td>Paciente</td>
		<td><input type="text" name="paciente" id="paciente" class="box" size="10"><a href='#' OnClick="window.open('../list_pacientes.php?id_login=$id_login',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');"> <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/localizar.jpg" align="absmiddle" border="0"></a> (F7)</td>
	</tr>-->
	
	<tr>
	<td>Compet&ecirc;ncia</td>
        <td colspan='2'><select name='mes' id='mes' class=box>
        <option value='' selected> -- mes -- </option>
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
        </select>
    &nbsp;
               <select name='ano' id='ano' class=box>
					<option value=2000>2000</option>
					<option value=2001>2001</option>
					<option value=2002>2002</option>               
					<option value=2003>2003</option>
					<option value=2004>2004</option>
					<option value=2005>2005</option>
					<option value=2006>2006</option>
					<option value=2007 selected=true>2007</option>
					<option value=2008>2008</option>
					<option value=2009>2009</option>
					<option value=2010>2010</option>
					<option value=2011>2011</option>
					<option value=2012>2012</option>
               </select> 
	<!--	<td><input type="text" size="3" name="comp_mes" id="comp_mes" class="box" maxlength="2"> / <input type="text" size="6" name="comp_ano" id="comp_ano" class="box" maxlength="4"></td> -->
	</tr>
	
		<tr><td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" />&nbsp;<a href="../rel_index.php?id_login=<?=$id_login?>&opcao=5#tabs-5"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a></td></tr>
</table>
</fieldset>
</form>
</body>

</html>

