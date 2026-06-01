<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	echo monta_calendario();
?>
<html>
<head>
<title>AIH Utilizado por prestador</title>
<script src="../ajax_motor.js"></script>
<script src="../funcoes.js"></script>
<script language="JavaScript">

var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);

function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(0,2))    // dia
   date_array[1]=(String(d).substr(3,2))    // mes
   date_array[2]=(String(d).substr(6,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 1999) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}

function Emite_relatorio() {
	med_codigo = document.getElementById('prestador').value;
	mes_comp = document.getElementById('comp_mes').value;
	ano_comp = document.getElementById('comp_ano').value;
	data_ini = document.getElementById('data_ini').value;
	data_fim = document.getElementById('data_fim').value;
	//quantidade = document.getElementById('quantidade').value;
	
	if ( mes_comp == "" && data_ini == "" )
	{
		alert('Preencha todos os campos!');
		return false;
	}
        if( data_ini != "" )
        {
                if (CheckDate(data_ini,"INICIAL")==1)
                {
                        alert('Preencha a data corretamente!');
                        return false
                }
                else if (CheckDate(data_fim,"FINAL")==1) 
                {
                        alert('Preencha a data corretamente!');
                        return false
                }
        }
	
        window.open("rel_item1.php?prestador="+med_codigo+"&mes_comp="+mes_comp+
                        "&ano_comp="+ano_comp+"&data_ini="+data_ini+"&data_fim="+data_fim,null,
                        "height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
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
<form method="post" action="<?=$PHP_SELF;?>" onsubmit="return Emite_relatorio()">
<fieldset>
<legend><b>Numeros de AIH utilizados por prestador</b></legend>
<table>
<tr>
	<td><label for="prestador">Prestador</label></td>
	<td><select name="prestador" id="prestador" class="box">
		<option value=-1>-----Todos------</option>
		<?php 
			$sql_statement = "SELECT * FROM medico WHERE prestador_servico = 'H' ORDER BY med_nome ASC";
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
        <td valign='bottom'>Filtrar por: </td>
        <td colspan='2'>Competencia <input type='radio' name='filtro' id='filtro' onchange="muda('tp_1')">
                &nbsp;Per&iacute;odo <input type='radio' name='filtro' id='filtro'  onchange="muda('tp_2', 'tp_3')">
        </td>
</tr>

<tr id='tp_1' style='display: none;'>
	<td>Compet&ecirc;ncia</td>
        <td colspan='2'><select name='comp_mes' id='comp_mes' class=box>
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
<?php  
$ano = date("Y");
echo "          <select name='comp_ano' id='comp_ano' class='box'>";
				for($i = ($ano - 5); $i <= $ano; $i++)
				{
					if($i == $ano)
					{
						echo "<option value='$i' selected>$i</option>";
					} else {
						echo "<option value='$i'>$i</option>";
					}
				}
echo "          </select>";

?>   
<!--	<td><input type="text" size="3" name="comp_mes" id="comp_mes" class="box" maxlength="2"> / <input type="text" size="6" name="comp_ano" id="comp_ano" class="box" maxlength="4"></td> -->
</tr>

<tr id='tp_2' style='display: none;'>
	<td>Data Inicial</td>
	<td>
	
	<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="10"><input type="text" size="15" name="data_ini" id="data_ini" class="box" maxlength='10' onkeypress="return Ajusta_Data(this,event);"></td>
		<td>&nbsp;<!--<input type=image src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario.png onclick="abrirCalendario('data_ini');return false;">--></td>
	</tr>
	</table>
	
	</td>
</tr>

<tr id='tp_3' style='display: none;'>
	<td>Data Final</td>
	<td>
		
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="10"><input type="text" size="15" name="data_fim" id="data_fim" class="box" maxlength='10' onkeypress="return Ajusta_Data(this,event);">
		<td>&nbsp;<!--<input type=image src=<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario.png onclick="abrirCalendario('data_fim');return false;">--></td>
	</tr>
	</table>
		
	</td>
</tr>
<!--
<tr>
	<td>Quantidade</td>
	<td><input type="text" size="5" name="quantidade" id="quantidade" class="box" maxlength="10"></td>
</tr>
-->
<tr>
<td><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/gerar_relatorio_on.jpg" name="emitir" value="Emitir" /></td>
<td align="left"><a href="../rel_index.php?id_login=$id_login&opcao=5#tabs-5"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border=0></a></td>
</tr>
</table>
</fieldset>
</form>
</body>

</html>

