<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	echo monta_calendario();
?>
<html>
<head>
<title></title>
<script src="../ajax_motor.js"></script>
<script src="../funcoes.js"></script>
<script src="cidades.js" type="text/javascript"></script>
<script src="../json.js" type="text/javascript"></script>
<script language="JavaScript">
function Choice(item){
	switch (item){
		case '1':document.getElementById("periodo").style.display='';
					document.getElementById("competencia").style.display='none';
					document.getElementById("acao").value="periodo";
					break;
		case '2':document.getElementById("competencia").style.display='';
					document.getElementById("periodo").style.display='none';
					document.getElementById("periodo_ini").value='';
					document.getElementById("periodo_fim").value='';	
					document.getElementById("acao").value="competencia";					
					break;
		default: return false;
	}
}

function eh_vazio(){
	if (document.getElementById('periodo_ini').value==''){
		document.getElementById('periodo_ini').focus();
		alert("Por Favor, preencha o campo Data Inicial");
		return false;
	}
	if (document.getElementById('periodo_fim').value==''){
		document.getElementById('periodo_fim').focus();
		alert("Por Favor, preencha o campo Data Final");
		return false;
	}	
	return true;
}

function Emite_relatorio()
{
	periodo_ini	= document.getElementById("dt_ini").value;
	periodo_fim	= document.getElementById("dt_fim").value;
	mes	= document.getElementById("mes").value;
	ano	= document.getElementById("ano").value;
	if(document.getElementById('filtro1').checked)
	{
		acao = "competencia";
		if(mes == "")
		{
			alert("Por Favor, escolha o mes!");
			document.getElementById("mes").focus();
			return false;
		}
	} else if(document.getElementById('filtro2').checked) {
		acao = "periodo";
		if (periodo_ini=='')
		{
			document.getElementById('dt_ini').focus();
			alert("Por Favor, preencha o campo Data Inicial");
			return false;
		}
		if (periodo_fim=='')
		{
			document.getElementById('dt_fim').focus();
			alert("Por Favor, preencha o campo Data Final");
			return false;
		}
	} else {
		alert("Escolha o filtro");
		return false;
	}
	
	cid10 = document.getElementById("cid10").value;
	municipio = document.getElementById("municipio").value;
	
	url = "rel_item7.php?periodo_ini="+periodo_ini+"&periodo_fim="+periodo_fim+"&mes="+mes+"&ano="+ano+"&cid10="+cid10+"&municipio="+municipio+"&acao="+acao;
	
	window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	
	return true;
}
	
	function muda()
	{
		for(i = 1; i < 4; i++)
		{
			id = document.getElementById(new String("tp_"+i)).style.display = 'none';
		}
		for(x = 0; x < arguments.length; x++)
		{
			document.getElementById(new String(arguments[x])).style.display = '';
		}
	}

</script>
</head>
<body>
<fieldset>
<legend>Interna&ccedil;&atilde;es por CID 10, per&iacute;odo ou compet&ecirc;ncia</legend>
<form method="post" name="form" action="<?php echo $PHP_SELF;?>"  onsubmit="return Emite_relatorio()">
	<?php
	/*$sql1=db_query("SELECT distinct(aih_ano_compet) FROM aih");
	while ($reg1=pg_fetch_array($sql1)){
		echo "<option value=$reg1[aih_ano_compet]>$reg1[aih_ano_compet]</option>";
	}*/
	?>
	<table>
		
		<tr>
			<td width="100">Munic&iacute;pio</td>
			<td colspan="2">
				<select name="estado" id="estado" class="box" onchange="atualiza_cidade(this,'municipio')">
					<option value="0">..</option>
					<?php
						$sql = db_query("SELECT DISTINCT uf_sigla FROM cidade ORDER BY 1");
						while ( $uf = pg_fetch_array($sql) )
						{
							echo "\n\t\t\t<option>{$uf[0]}</option>";
						}
					?>
				</select>
				<select name="municipio" id="municipio" class="box" style="width:150px;">
					<option value="-1">...Todos...</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="cid10">CID10</label>
			<td colspan='2'>
				<select name="cid10" id="cid10" class="box">
				<option value=-1>------Todos-----</option>
				<?php 
					$sql=db_query("SELECT cd10_codigo, cd10_descricao FROM cid10 order by cd10_descricao LIMIT 10");
					while ($reg=pg_fetch_array($sql)){
						echo "<option value='$reg[cd10_codigo]'>$reg[cd10_descricao]</option>";
					}
				?>
				</select>
			</td>
			<input type="hidden" name="acao" id="acao">
		</tr>
		<tr>
			<td valign='bottom'>Filtrar por: </td>
			<td colspan='2'>
				Compet&ecirc;ncia <input type='radio' name='filtro' value='1' id='filtro1' onchange="muda('tp_1')" class="box">
				&nbsp;Per&iacute;odo <input type='radio' name='filtro' value='2' id='filtro2' onchange="muda('tp_2', 'tp_3')" class="box">
			</td>
		</tr>
		<tr id='tp_1' style="display: none;">
			<td valign='bottom'>Compet&ecirc;ncia: </td>
			<td colspan='2'>
				<select name='mes' id='mes' class='box'>
					<?php echo meses_select( ); ?>
				</select>
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
		<tr id='tp_2' style="display: none;">
			<td valign='bottom'>Data Inicial: </td>
			<td width='10px'>
				<input type='text' name='dt_ini' id='dt_ini' value='' class='box' size='10' maxlength='10' onkeypress="return Ajusta_Data(this,event);">
			</td>
		</tr>
		<tr id='tp_3' style="display: none;">
			<td valign='bottom'>Data Final: </td>
			<td width='10px'>
				<input type='text' name='dt_fim' id='dt_fim' value='' class='box' size='10' maxlength='10' onkeypress="return Ajusta_Data(this,event);">
			</td>
			
		</tr>
		<tr>
			<td colspan='2'>
				<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" />
			</td>
			<td>
				<a href="../rel_index.php?id_login=<?=$id_login?>&opcao=5#tabs-5"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a>		</td>
			</td>
		</tr>	
	</table>
</form>
</fieldset>
</body>
</html>