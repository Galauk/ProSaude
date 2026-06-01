<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	echo monta_calendario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
?>
<html>
<head>
<title></title>
<script src="../ajax_motor.js"></script>
<script src="../funcoes.js"></script>
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
	if (document.getElementById('data_ini').value==''){
		document.getElementById('data_ini').focus();
		alert("Por Favor, preencha o campo Data Inicial");
		return false;
	}
	if (document.getElementById('data_fim').value==''){
		document.getElementById('data_fim').focus();
		alert("Por Favor, preencha o campo Data Final");
		return false;
	}	
	return true;
}

function Emite_relatorio(){
data_ini	=document.getElementById("periodo_ini").value;
data_fim	=document.getElementById("periodo_fim").value;
mes_comp	=document.getElementById("mes").value;
ano_comp 	=document.getElementById("ano").value;
proced		=document.getElementById("procedimento").value;
municipio	=document.getElementById("municipio").value;
acao		=document.getElementById("acao").value;



if (document.getElementById("escolha1").checked){
	if (data_ini==''){
		document.getElementById('periodo_ini').focus();
		alert("Por Favor, preencha o campo Data Inicial");
		return false;
	}
	if (data_fim==''){
		document.getElementById('periodo_fim').focus();
		alert("Por Favor, preencha o campo Data Final");
		return false;
	}	
}
	window.open("rel_item6.php?periodo_ini="+data_ini+"&periodo_fim="+data_fim+"&mes="+mes_comp+"&ano="+ano_comp+"&procedimento="+proced+"&municipio="+municipio+"&acao="+acao,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
	return true;
}		

</script>
</head>
<body>
<fieldset>
<legend>Interna&ccedil;&otilde;es por procedimento e por munic&iacute;pio por per&iacute;odo ou compet&ecirc;ncia</legend>
<form method="post" action="<?php echo $PHP_SELF;?>" onsubmit="return Emite_relatorio()">
<table>
	<tbody id="t1">
	<tr>
		<td width="100"><label for="escolha1">Periodo</label></td>
		<td><input type="radio" name="escolha" id="escolha1" onclick="Choice('1')" class="box" checked></td>
	</tr>
	<tr>
		<td width="100"><label for="escolha2">Competencia</label></td>
		<td><input type="radio" name="escolha" id="escolha2"  onclick="Choice('2')" class="box"></td>
	</tr>	
</table>
<table>
	<tbody id="periodo" style="display:''">
	<tr>
		<td width="100"><label for="periodo_ini">Data Inicial</legend></td>
		<td>
        
        <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="100"><input type="text" name="periodo_ini" id="periodo_ini" class="box" onkeypress="return Ajusta_Data(this,event)" maxlength="10">
            <!--<td>&nbsp;<input type=image src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario.png onclick="abrirCalendario('periodo_ini');return false;"></td>-->
        </tr>
        </table>
            
        </td>
	</tr>
	<tr>
		<td><label for="periodo_fim">Data Final</legend></td>
		<td>
            
        <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="100"><input type="text" name="periodo_fim" id="periodo_fim" class="box" onkeypress="return Ajusta_Data(this,event)" maxlength="10">
            <!--<td>&nbsp;<input type=image src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario.png onclick="abrirCalendario('periodo_fim');return false;"></td>-->
        </tr>
        </table>
            
        </td>		
	</tr>
	</tbody>
	<tr id="competencia" style="display:none">
		<td width="100"><label for="mes">Competencia</label></td>
		<td><select name="mes" id="mes" class="box">
                        <option value=1>Janeiro</option>
                        <option value=2>Fevereiro</option>
                        <option value=3>Marco</option>
                        <option value=4>Abril</option>
                        <option value=5>Maio</option>
                        <option value=6>Junho</option>
                        <option value=7>Julho</option>
                        <option value=8>Agosto</option>
                        <option value=9>Setembro</option>
                        <option value=10>Outubro</option>
                        <option value=11>Novembro</option>
                        <option value=12>Dezembro</option>
                        </select> /
                <select name="ano" id="ano" class="box">
		<option value='<?=date("Y")?>' selected><?=date("Y")?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-3))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-3))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-2))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-2))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")-1))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")-1))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+2))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+2))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+3))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+3))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+4))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+4))?></option>
                <option value='<?=date("Y", mktime(0,0,0,0,0,date("Y")+5))?>'>
                <?=date("Y", mktime(0,0,0,0,0,date("Y")+5))?></option>
		</select>

		</td>				
	</tr>


	<tr>
		<td width="100"><label for="municipio">Municipio</label>
		<td><select name="municipio" id="municipio" class="box">
				<option value=-1>------Todos-----</option>
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
		<td><label for="procedimento">Procedimento</label>
		<td><select name="procedimento" id="procedimento" class="box">
				<option value=-1>------Todos-----</option>
		<?php 
				$sql=db_query("select * from procedimento order by proc_nome");
				while ($reg=pg_fetch_array($sql)){
					echo "<option value=$reg[proc_codigo]>$reg[proc_nome]</option>";
				}
		?>				
				</select>
		</td>
		<input type="hidden" name="acao" id="acao">
	<tr><td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" /></td>
        <td><a href='../rel_index.php?opcao=5#tabs-5'><img src='<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif' border='0'></a></td></tr>	
</table>
</form>
</fieldset>