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

	periodo_ini = document.getElementById("dt_ini").value;
	periodo_fim = document.getElementById("dt_fim").value;
	mes = document.getElementById("mes").value;
	ano = document.getElementById("ano").value;
	municipio = document.getElementById("municipio").value;
	faixa_etaria = document.getElementById("faixa_etaria").value;
	filtro1 = document.getElementById("filtro1").checked; 
	filtro2 = document.getElementById("filtro2").checked; 
	if(filtro1 == '' && filtro2 == ''){
		document.getElementById('filtro1').focus();
		alert("Por Favor, preencha o campo filtro");
		return false;
	}
	if(document.getElementById('filtro1').checked)
	{
		acao = "competencia";
		if(mes == "")
		{
			alert("Por Favor, escolha o mes!");
			document.getElementById("mes").focus();
			return false;
		}	
	} else {
		acao = "periodo";
		
		if (periodo_ini == ''){
			document.getElementById('dt_ini').focus();
			alert("Por Favor, preencha o campo Data Inicial");
			return false;
		}
		if (periodo_fim == ''){
			document.getElementById('dt_fim').focus();
			alert("Por Favor, preencha o campo Data Final");
			return false;
		}
	}
	
	url = "rel_item8.php?periodo_ini="+periodo_ini+"&periodo_fim="+periodo_fim+"&mes="+mes+"&ano="+ano+"&acao="+acao+"&municipio="+municipio+"&faixa_etaria="+faixa_etaria;
	
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
<legend>Interna&ccedil;&otilde;es por faixa et&aacute;ria, sexo, munic&iacute;pio, por compet&ecirc;ncia ou per&iacute;odo</legend>
<form method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
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
			<td>
				Faixa Et&aacute;ria
			</td>
			<td colspan='2'>
				<select name="faixa_etaria" id="faixa_etaria" class="box">
					<option value="-1">Todas</option>
					<option value="0">0 a 1 ano</option>
					<option value="1">1 a 5 anos</option>
					<option value="5">5 a 12 anos</option>
					<option value="12">12 a 19 anos</option>
					<option value="19">19 a 25 anos</option>
					<option value="25">25 a 49 anos</option>
					<option value="49">49 a 65 anos</option>
					<option value="65">acima de 65 anos</option>
				</select>
			</td>
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
					<option value='' selected> -- M&ecirc;s -- </option>
					<option value='01'> Janeiro </option>
					<option value='02'> Fevereiro </option>
					<option value='03'> Mar&ecirc;o </option>
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
				<select name='ano' id='ano' class='box'>
				<?
				/*$sql1=db_query("SELECT distinct(aih_ano_compet) FROM aih");
				while ($reg1=pg_fetch_array($sql1)){
				echo "<option value=$reg1[aih_ano_compet]>$reg1[aih_ano_compet]</option>";
				}*/
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
			<td>
			</td>
		</tr>
		<tr id='tp_3' style="display: none;">
			<td valign='bottom'>Data Final: </td>
			<td width='10px'>
				<input type='text' name='dt_fim' id='dt_fim' value='' class='box' size='10' maxlength='10' onkeypress="return Ajusta_Data(this,event);">
			</td>
			<td align='left'>
			</td>
		</tr>
		<tr>
			<td>
				<input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" />
			</td>
			<td colspan='2'>
				<a href="../rel_index.php?opcao=5#tabs-5"><img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif' border='0'></a>
			</td>
		</tr>
	</table>
</form>
</fieldset>