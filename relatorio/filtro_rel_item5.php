<?php
/**
 * @brief Adicionando novas opçoes para o filtro de busca
 */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
?>

<html>
<head>
<title>Causas de internação por clínica, por município</title>
<script src="funcoes.js"></script>
<script language="JavaScript">
function Emite_relatorio()
{
	clinica	= document.getElementById('clinica').value;
	municipio = document.getElementById('municipio').value;
	mes_comp = document.getElementById('mes_comp').value;
	ano_comp = document.getElementById('ano_comp').value;
	
	window.open("rel_item5.php?clinica="+clinica+"&municipio="+municipio+"&mes_comp="+mes_comp+"&ano_comp="+ano_comp,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return true;
}
</script>
</head>	
<body>
<form method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
<fieldset>
<legend>Causas de interna&ccedil;&atilde;o por cl&iacute;nica, por munic&iacute;pio</legend>
<table>
	<tr>
		<td width="100"><label for="clinica">Clinica</label></td>
		<td><select name="clinica" id="clinica" class="box">
		<option value=-1>-------Todos-----</option>
		<?php 
			$sql=db_query("select * from clinica order by cli_descricao");
			while ($reg=pg_fetch_array($sql))
			{
				echo "<option value=$reg[cli_codigo]>$reg[cli_descricao]</option>";
			}
		?>
		</select>
		</td>
		</tr>
		<tr>
		<td><label for="municipio">Municipio</label></td>
		<td><select name="municipio" id="municipio" class="box">
						<option value=-1>-------Todos-----</option>
		<?php 
			$sql=db_query("select * from cidade order by cid_nome");
			while ($reg=pg_fetch_array($sql))
			{
				echo "<option value=$reg[cid_codigo_ibge]>$reg[cid_nome]</option>";
			}
		?>
		</select>
		</td>
		</tr>
		<tr>
			<td valign='bottom'>Compet&ecirc;ncia: </td>
			<td><select id='mes_comp' name='mes_comp' class='box' onchange=\"document.getElementById('ano_comp').select();\">	
			<option value="1" <?=( date('m') == 1 ? "selected" : "")?>> Janeiro</option>
			<option value="2" <?=( date('m') == 2 ? "selected" : "")?>>Fevereiro</option>
			<option value="3" <?=( date('m') == 3 ? "selected" : "")?>>Mar&ccedil;o</option>
			<option value="4" <?=( date('m') == 4 ? "selected" : "")?>>Abril</option>
			<option value="5" <?=( date('m') == 5 ? "selected" : "")?>>Maio</option>
			<option value="6" <?=( date('m') == 6 ? "selected" : "")?>>Junho</option>
			<option value="7" <?=( date('m') == 7 ? "selected" : "")?>>Julho</option>
			<option value="8" <?=( date('m') == 8 ? "selected" : "")?>>Agosto</option>
			<option value="9" <?=( date('m') == 9 ? "selected" : "")?>>Setembro</option>
			<option value="10" <?=( date('m') == 10 ? "selected" : "")?>>Outubro</option>
			<option value="11" <?=( date('m') == 11 ? "selected" : "")?>>Novembro</option>
			<option value="12" <?=( date('m') == 12 ? "selected" : "")?>>Dezembro</option>		</select>		/

	          <select id='ano_comp' name='ano_comp' class='box'>	
		<option value='<?=date("Y")?>' selected><?=date("Y")?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-4))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-4))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-3))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-3))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-2))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-2))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-1))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-1))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+2))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+2))?></option>


		<option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+0))?>'>
		<?=date("Y", mktime(0,0,0,0,0,date("Y")+0))?></option>

                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+3))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+3))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+4))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+4))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+5))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+5))?></option>
                </select>
      </td>
     </tr>

		<tr><td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" /></td>
			<td><a href="../rel_index.php?id_login=<?=$id_login?>&opcao=5#tabs-5"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a>		</td></td></tr>
</table>
</fieldset>
</form>
</body>

</html>
