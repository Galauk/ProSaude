<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
?>

<script language="JavaScript">
function Emite_relatorio(){

	municipio = document.form1.municipio.value;
	prestador = document.getElementById('prestador').value;
	ci = document.form1.ci.value;
	mes_ini = document.getElementById('mes_ini').value;
	mes_fin = document.getElementById('mes_fim').value;
	ano_ini = document.getElementById('ano_ini').value;
	ano_fin = document.getElementById('ano_fim').value;
	if (ano_fin == ""|| ano_ini == "" || mes_ini == "" || mes_fin == "")
	{
		alert('Preencha a data corretamente.');
		return false;
	}
	
	url = "rel_item4.php?prestador="+prestador+"&municipio="+municipio+"&ci="+ci+"&mes_ini="+mes_ini+"&mes_fin="+mes_fin+"&ano_ini="+ano_ini+"&ano_fin="+ano_fin;
	window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return false;
}		

function muda( obj, prox )
{
	//alert( obj.value.length  + ":" + obj.maxlength );
	//alert( obj.value )
	//if( obj.value == obj.maxlength )
	//	document.getElementById( prox ).focus();
}
</script>

<form name="form1" method="post" action="?" onsubmit="return Emite_relatorio();">
<fieldset>
<legend>Interna&ccedil;&otilde;es por munic&iacute;pios, N&#176; absoluto por car&aacute;ter de interna&ccedil;&atilde;o por compet&ecirc;ncia</legend>
<table>
	<tr>
		<td>Prestador</td>

		<td><select name="prestador" id="prestador" class="box">
				<option value=-1>-----Todos------</option>
				<?php 
					$sql_statement = "SELECT * FROM medico ORDER BY med_nome ASC";
					$sql = db_query($sql_statement, $LOG = false);
					while($reg = pg_fetch_array($sql))
					{
						echo"<option value='$reg[med_codigo]'>$reg[med_nome]</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr>
		<td><label for="municipio">Municipio</label></td>
		<td>
			<select class="box"  name="municipio" id="municipio" class="box">
				<option value=-1>-------Todos-----</option>
				<?php
					$sql=db_query("select * from cidade order by cid_nome");
					while ($reg=pg_fetch_array($sql)){
						echo "<option value=$reg[cid_codigo_ibge]>$reg[cid_nome]</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><label for="ci">Car&aacute;ter de Interna&ccedil;&atilde;o</label></td>
		<td><select class="box" name="ci" id="ci" style="width:80px;">
						<option value=-1>--Todos--</option>
						<?php
							$sql=pg_query("select * from ci order by ci_descricao");
							while ($reg=pg_fetch_array($sql)){
								echo "<option value=$reg[ci_codigo]>$reg[ci_cod]</option>";
							}
						?>
				</select>
		</td>
	</tr>
	
	<tr>
	<td>Compet&ecirc;ncia</td>
        <td colspan='2'><select name='mes_ini' id='mes_ini' class=box>
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
               <select name='ano_ini' id='ano_ini' class=box>
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
			   &nbsp;At&eacute;&nbsp;
		<select name='mes_fim' id='mes_fim' class=box>
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
               <select name='ano_fim' id='ano_fim' class=box>
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

	<tr>
		<td>
			<input type="image" alt="Enviar" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" />&nbsp;&nbsp;
			<a href="../rel_index.php?id_login=<?=$id_login?>&opcao=5#tabs-5"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a>		</td>
	</tr>
</table>
</fieldset>
</form>

</body>
</html>