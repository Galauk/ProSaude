<?php
session_start();
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
include_once "authlib.inc.php";
verauth($id_login);

include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
Cabecario();
//------------------------------------------------------------------>
	echo monta_calendario();
?>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript">
function hotkey(eventname,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,id_login,hora)
{
    var teste = +age_codigo;
	// f7
  /*  if(eventname.keyCode == 118)
	{
		if(teste=="")
		{
			alert("ERRO: Voce deve selecionar o paciente antes de executar esta acao.");
			return false;
		}
		d = new Date();
		dia = d.getDate();
		mes = d.getMonth()+1;
		ano = d.getFullYear();
		if (dia < 10)
		{
			dia = "0" + dia
		}
		if(mes < 10)
		{
			mes = "0" + mes;
		}
		data = dia+"/"+mes+"/"+ano;
		if(data == age_data)
		{
			endereco = "<?=$PHP_SELF?>?id_login="+id_login+"&acao=mostra_age&f7=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
			//alert(endereco);
			self.location.href="<?=$PHP_SELF?>?id_login="+id_login+"&acao=mostra_age&f7=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
		} else {
			return false;
		}
    }*/

}	
function hotkey2(eventname,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,id_login, tipo, hora)
{
    var teste = +age_codigo;
	
	// f7
	if(eventname == 118)
	{
		if(tipo == 0)
		{
			self.location.href="<?=$PHP_SELF?>?id_login="+id_login+"&acao=mostra_age&f7=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&age_codigo="+age_codigo+"&hora="+hora; 
		} else if(tipo == 1){
			self.location.href="<?=$PHP_SELF?>?id_login="+id_login+"&acao=mostra_age&f7dois=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&age_codigo="+age_codigo+"&hora="+hora; 
		}
    }
         
}	

function msg(id_login,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data, hora)
{ 
	self.location.href="<?=$PHP_SELF?>?id_login="+id_login+"&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
}
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
</script>
<script>
function gradata(hr) {
     document.forms.age_data.value = hr;
}
function gralimpa() {
     document.forms.age_data.value = '';
}

	function atualizar( campo )
	{
		uni = document.getElementById("uni_null").value;
		med = document.getElementById("med_null").value;
		esp = document.getElementById("esp_null").value;
		data = campo.value;
		endereco = "buscarHorario.php?esp_codigo="+esp.value+"&med_codigo="+med+"&uni_codigo="+uni+"&data="+data;
		ajax_tudo( endereco, popular_horario );
	}
	
	function popular_horario(txt)
	{
		d = document.getElementById('hora');
		d.innerHTML = "";
		aux = txt.split("-");
		for(k = 0; k < aux.length; k++)
		{
			if(aux[k] != "")
			{
				d.options[d.options.length]=new Option(aux[k],aux[k]);
			}
		}
		d.focus();
	}
	
	function verificar()
	{
		
		if(document.getElementById('hora').value == '')
		{
			alert('Preencha a Hora');
			return false;
		} else {
			return true;
		}		
		
	}

</script>
<body onkeydown='hotkey(event,"<?=$age_codigo?>","<?=$uni_codigo?>","<?=$esp_codigo?>","<?=$med_codigo?>","<?=$age_data?>","<?=$id_login?>","<?=$hora?>")'>
<fieldset><legend>LISTAR RECEPCIONADOS</legend>
<?php

//------------------------------------------------------------------>
// -> IF Das Hotkeys
//------------------------------------------------------------------>
if($f7=="ok")
{
	$rr=pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
	
	if($rr['age_atendido']=="A")
	{ 
		$tipo_age="S"; 
		reglog($id_login,"Cancelado Paciente: $rr[usu_codigo]");
	} else { 
		$tipo_age="A"; 
		reglog($id_login,"Recepcionado Paciente: $rr[usu_codigo]");
   }

	// ele foi atendido no dia de hj !
	//$tipo_age = 'A';
	
	$stmt = "UPDATE agendamento SET
		age_atendido='$tipo_age',
		usr_codigo_alt='$id_login',
		dt_atualizacao=NOW() ,
		age_data_atend = CURRENT_TIMESTAMP
		WHERE age_codigo='$age_codigo'";
		//echo $stmt;
	$sql = pg_query($stmt);
	
	//echo pg_last_error($db);
	//exit();
	echo "<script type=\"text/javascript\">
		setTimeout(\"location='$PHP_SELF?id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&age_data=$age_data&acao=mostra_age&hora=$hora'\", 0);
	</script>";
}
if($f7dois=="ok")
{
	$rr=pg_fetch_array(pg_query("select * from agendamento where age_codigo='$age_codigo'"));
	
	if($rr['age_atendido']=="A")
	{ 
		$tipo_age="S"; 
		reglog($id_login,"Cancelado Paciente: $rr[usu_codigo]");
	} else { 
		$tipo_age="A"; 
		reglog($id_login,"Recepcionado Paciente: $rr[usu_codigo]");
	}

	// ele foi atendido no dia de hj !
	//$tipo_age = 'S';
	
	echo $stmt = "UPDATE agendamento SET
		age_atendido='$tipo_age',
		usr_codigo_alt='$id_login',
		dt_atualizacao=NOW() ,
		age_data_atend = null
		WHERE age_codigo='$age_codigo'";
		//echo $stmt;
	$sql = pg_query($stmt);
	
	//echo pg_last_error($db);
	//exit();
	echo "<script type=\"text/javascript\">
		setTimeout(\"location='$PHP_SELF?id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&age_data=$age_data&acao=mostra_age&hora=$hora'\", 0);
	</script>";
}

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
reglog($id_login,"Acessando RECEPCAO");


echo "
<fieldset>
<legend>Op&ccedil;&ocirc;es</legend>
<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width=126>".ChmodBtn($id_login,'fazeragendamento','fazer_agendamento.php?')."</td>
		<td width=146>".ChmodBtn($id_login,'manutencaoagenda','manutencaomedicos.php?')."</td>
		<td>".ChmodBtn($id_login,'manutencaogrupodeagente','manutencaoagentes.php?')."</td>
	</tr>
</table>
</fieldset>
<br>";

//
//-> Botoes
 echo "
<form name=forms method=get action=$PHP_SELF>

<input type=hidden name=uni_codigo value=$uni_codigo>
<input type=hidden name=id_login value=$id_login>
<input type=hidden name=esp_codigo value=$esp_codigo>
<input type=hidden name=med_codigo value=$med_codigo>
<input type=hidden name=procedimento value=$procedimento>
<input type=hidden name=acao value=mostra_age>

<table width=733 align=center cellspacing=2 cellpadding=4 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
<tr>
	<td>
		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			<tr>
				<td width=122 align=right>Unidade de sa&uacute;de</td>";
				
	$uni_ = pg_fetch_array( pg_query("select *from usuarios where usr_codigo = '$id_login'"));
	$unidade_usuario = $uni_['uni_codigo'];
				
				echo "<td width=320><select name=uni_null id=uni_null class=boxr onChange=\"javascript:changeLocation(this)\">";
				if(empty($unidade_usuario))
				{
					echo "<option>...</option>";
				}

if(!empty($unidade_usuario))
{
	$and_Select = "where uni_codigo = '$unidade_usuario'";
}

$sql = pg_query("select *from unidade $and_Select order by uni_desc");

while($uni=pg_fetch_array($sql))
{
	echo ($uni[uni_codigo]==$uni_codigo) ? 
		"<option value='$PHP_SELF?id_login=$id_login&uni_codigo=$uni[uni_codigo]&med_codigo=$med_codigo&esp_codigo=$esp_codigo' selected>$uni[uni_desc]</option>":
		"<option value='$PHP_SELF?id_login=$id_login&uni_codigo=$uni[uni_codigo]&med_codigo=$med_codigo&esp_codigo=$esp_codigo'>$uni[uni_desc]</option>";
}

$agesel = pg_fetch_array(pg_query("select usr_codigo_cad,to_char(dt_cadastro,'DD/MM/YYYY') as dt_cadastro from agendamento where age_codigo = '$age_codigo'"));
$seluser = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$agesel[usr_codigo_cad]'"));

echo "
					</select>
				</td>
				<td><b><font color=blue>Cadastrado Por:</font></b> $seluser[usr_nome] $agesel[dt_cadastro]</td>
			</tr>
			<tr>
				<td width=122 align=right>Profissional</td>
				<td width=320>";

$select = pg_query("SELECT med_codigo FROM usuarios WHERE usr_codigo=$id_login");
$rows = pg_fetch_array($select);
$medico_codigo = $rows[0];
				
$sql = pg_query("select *from medico order by med_nome");

$med_codigo = ( empty($med_codigo) ? 
				db_get("SELECT med_codigo FROM usuarios WHERE usr_codigo=$id_login"): 
				$med_codigo );
					if(!empty($medico_codigo))
					{
						echo "<select name=med_null id=med_null class=boxr  onChange=\"changeLocation(this);\" disabled>";
					} else {
						echo "<select name=med_null id=med_null class=boxr  onChange=\"changeLocation(this);\">";
						echo "<option value=\"\">...</option>";
					}
					//<option>...</option>

while($med=pg_fetch_array($sql))
{
	echo ($med[med_codigo]==$med_codigo) ? 
		"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo selected>$med[med_nome]</option>":
		"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo>$med[med_nome]</option>";
}

$agesel_u = pg_fetch_array(pg_query("select usr_codigo_alt,to_char(dt_atualizacao,'DD/MM/YYYY') as dt_atualizacao from agendamento where age_codigo = '$age_codigo'"));
$seluser_u = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$agesel_u[usr_codigo_alt]'"));

echo "		
				</select>
			</td>
			<td><b><font color=orange>Alterado Por:</font></b> $seluser_u[usr_nome] $agesel_u[dt_atualizacao]</td>
		</tr>
		<tr>
			<td align=right width=122>Especialidade</td>
			<td><select name=esp_null id=esp_null class=boxr onChange=\"javascript:changeLocation(this);\">
					<option>...</option>";

$sql = pg_query("select medico_especialidade.esp_codigo,esp_nome from medico_especialidade, especialidade where medico_especialidade.esp_codigo=especialidade.esp_codigo and medico_especialidade.med_codigo='$med_codigo'");

while($esp=pg_fetch_array($sql))
{
	echo ($esp[esp_codigo]==$esp_codigo) ? 
		"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&uni_codigo=$uni_codigo=esp_codigo=$esp[esp_codigo] selected>$esp[esp_nome]</option>":
		"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&uni_codigo=$uni_codigo&esp_codigo=$esp[esp_codigo]>$esp[esp_nome]</option>";
}

echo "
			</select></td>
		</tr>
	</table>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width=96>&nbsp;</td>
		<td width=5 align=right>Data</td>
		<!--<td width=15 colspan=2><input type=text name=age_data class='boxn' style='font-weight:bold;' size='10' id='data' maxlength='10' readonly='readonly' value='".( date('d/m/Y') )."' />-->
		<td width=15><input type=text name=age_data class='boxn' size='12' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" value=".(!$_GET[age_data] ? date("d/m/Y") : $_GET[age_data])." onfocus=atualizar(this)>
	        <td width=25><a href=#  onclick=\"abrirCalendario('data');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png border=0></a></td>
 		<td width=10 align=right>&nbsp;&nbsp;&nbsp;&nbsp;Hora</td>
		<td width=70>";

		if($uni_codigo != "")
		{
			$unidade = $uni_codigo;
		} else {
			$unidade = $unidade_usuario;
		}
		//$uni_codigo = $unidade;
		$sql = "SELECT DISTINCT gra_hora_ini FROM grade_medico 
				WHERE esp_codigo = $esp_codigo AND med_codigo = $med_codigo AND uni_codigo = $unidade and gra_data ='". ($age_data ? $age_data : date("d/m/Y"))."'  ORDER BY gra_hora_ini";
			
				$exec_sql = pg_query($sql);
				
		echo "<select name=hora id=hora class=box>";
				
				while($linha = pg_fetch_array($exec_sql))
				{
					if($linha[0] == $hora)
					{
						echo "<option value=$linha[0] selected>$linha[0]</option>";
					} else {
						echo "<option value=$linha[0]>$linha[0]</option>";
					}
				}
				
			echo "</select>
		</td>
		<td width=108><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/listarpacientes_on.jpg alt='Listar Pacientes' onclick=\"return verificar();\"></td>
		<td>&nbsp;</td>
	</tr>
	</table>
	<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	<tr>
		<th>PACIENTES ";
		if($hora)
		{
			echo "DAS ".$hora;
		}
		echo "</th>
		<td align=right><font color=blue><b>Recepcionado </b></font>&nbsp;&nbsp;&nbsp;<font color=green><b>Atendido </b></font>&nbsp;&nbsp;&nbsp;<font color=orange><b>Transferido  </b></font>&nbsp;&nbsp;&nbsp;<font color=red><b>Faltoso </b></font>&nbsp;&nbsp;&nbsp;<font color=purple><b>M&eacute;dico Faltou</b></font>&nbsp;&nbsp;&nbsp;Agendado</td>
		</td>
	</tr>
	</form>
	</table>	   
</td>
</tr>
</table>
<table width=733 align=center cellspacing=0 cellpadding=4 border=0>
	<tr bgcolor='#cccccc'>
		<td width=25 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>N</font></td>
		<td width=80 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>C&oacute;digo Pac.</font></td>
		<td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>Paciente</font></td>
		<td width=30 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>Idade</font></td>
		<td width=120 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>M&atilde;e</font></td>
		<!--<td style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>Situa&ccedil;&atilde;o</font></td>-->
		<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Recep&ccedil;&atilde;o</font></td>
		<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Atendimento</font></td>
		<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Munic&iacute;pio</font></td>
		<td width=20 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>&nbsp;</font></td>
	</tr>
";

if($acao=="mostra_age")
{
	
	print "
	<script type='text/javascript'>
		var url = '{$PHP_SELF}?{$QUERY_STRING}', seg = 60;
		setTimeout( 'document.location.href=url', seg * 1000 );
	</script>";
	
	
	if($age_data=="")
	{
		$age_data = date("d/m/Y");
	}

	$dtn = explode("/",$age_data);
	$grm_data = "$dtn[2]-$dtn[1]-$dtn[0]";

	if(!empty($hora))
	{
		$andHora = " and age_hora = '$hora' ";
	}
	
	if($uni_codigo != "")
	{
		$unidade = $uni_codigo;
	} else {
		$unidade = $unidade_usuario;
	}
	$uni_codigo = $unidade;
	
	//echo "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$unidade' and usu_codigo is not null $andHora order by age_timestamp, age_codigo";
	$sql = pg_query("select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$unidade' and usu_codigo is not null $andHora order by age_timestamp, age_codigo");
	
	if( pg_num_rows($sql)=="0" )
	{
		echo "
		<tr>
			<td colspan=6 bgcolor=f9f9f9 style='border-bottom:1px solid' align=center>
				Nenhum paciente a recepcionar nesta data.
			</td>
		</tr>";
	}
	$k = 0;
    while($row=pg_fetch_array($sql))
	{
		if($row[age_atendido] == "S" && $row[age_falta_medico] != "M")
		{
			$array_s[] = array ($row[age_codigo], "S", $row[usu_codigo]);
		} else if($row[age_atendido] == "N" && $row[age_falta_medico] != "M") {
			$array_n[] = array ($row[age_codigo], "N", $row[usu_codigo]);
		} else if($row[age_atendido] == "F" && $row[age_falta_medico] != "M") {
			$array_f[] = array ($row[age_codigo], "F", $row[usu_codigo]);
		} else if($row[age_atendido] == "T" && $row[age_falta_medico] != "M") {
			$array_t[] = array ($row[age_codigo], "T", $row[usu_codigo]);
		} else if($row[age_atendido] == "A" && $row[age_falta_medico] != "M") {
			$array_a[] = array ($row[age_codigo], "A", $row[usu_codigo]);
		} else if($row[age_falta_medico] == "M") {
			$array_m[] = array ($row[age_codigo], "M", $row[usu_codigo]);
        }
	}
	for($i = 0; $i < count($array_s); $i++)
	{
		$k++;
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_s[$i][0]} order by age_timestamp, age_codigo";
		$exec = pg_query($select);
		$row = pg_fetch_array($exec);
		
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];

		
			$busca = "select * from usuario where usu_codigo='{$array_s[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=blue><b>"; $bold_font_close="</font></b>";
			echo "
			<tr style='cursor: hand;text-transform:upperCase;' bgcolor='#ffffff' onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090;font-weight:bold' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$k
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $row[usu_codigo] $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_nome] $bold_font_close
			</td>
			<td width=10% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".verIdade("$pac[usu_datanasc]")."$bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_mae] $bold_font_close
			</td>
			<!--<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open &nbsp; $situacao $bold_font_close
			</td>-->";
			echo "<td width=5% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td  width=5% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".strtoupper($pac[usu_end_cidade])." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>";
			//eventname,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,id_login
				if($age_data == date("d/m/Y"))
				{
					echo "<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/a_on.jpg' onclick=\"hotkey2(118, '$row[age_codigo]', '$uni_codigo', '$esp_codigo', '$med_codigo', '$age_data', $id_login, 0, '$hora');return false;\">";
				} else {
					echo "&nbsp;";
				}
			echo "</td>";
		echo "</tr>";
	}
	if(!empty($array_s))
	{
		echo "<tr><td colspan=11 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_s)." paciente(s) recepcionado(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_n); $i++)
	{
		$k++;
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_n[$i][0]} order by age_timestamp, age_codigo";
		$exec = pg_query($select);
		$row = pg_fetch_array($exec);
			$busca = "select * from usuario where usu_codigo='{$array_n[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open=""; $bold_font_close=""; $situacao = 'Agendado'; 
			echo "
			<tr style='cursor: hand;text-transform:upperCase;' bgcolor='#ffffff' onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090;font-weight:bold' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$k
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $row[usu_codigo] $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_nome] $bold_font_close
			</td>
			<td width=10% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".verIdade("$pac[usu_datanasc]")." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_mae] $bold_font_close
			</td>
			<!--<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open &nbsp; $situacao $bold_font_close
			</td>-->";
			echo "<td  width=5% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>";
			echo "<td  width=5% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".strtoupper($pac[usu_end_cidade])." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp";
			echo "</td>
		</tr>";
	}
	if(!empty($array_n))
	{
		echo "<tr><td colspan=11 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_n)." paciente(s) n&atilde;o recepcionado(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_a); $i++)
	{
		$k++;
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_a[$i][0]} order by age_timestamp, age_codigo";
		$exec = pg_query($select);
		$row = pg_fetch_array($exec);
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];
		
			$busca = "select * from usuario where usu_codigo='{$array_a[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=green><b>"; $bold_font_close="</font></b>"; $situacao = 'Atendido';
			echo "
			<tr style='cursor: hand;text-transform:upperCase;' bgcolor='#ffffff' onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090;font-weight:bold' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$k
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $row[usu_codigo] $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_nome] $bold_font_close
			</td>
			<td width=10% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".verIdade("$pac[usu_datanasc]")." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_mae] $bold_font_close
			</td>
			<!--<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open &nbsp; $situacao $bold_font_close
			</td>-->";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".strtoupper($pac[usu_end_cidade])." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>";
			//eventname,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,id_login
				if($age_data == date("d/m/Y"))
				{
					echo "<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/a_on.jpg' onclick=\"hotkey2(118, '$row[age_codigo]', '$uni_codigo', '$esp_codigo', '$med_codigo', '$age_data', $id_login, 1, '$hora');return false;\">";
				} else {
					echo "&nbsp;";
				}
			echo "</td>
		</tr>";
	}
	if(!empty($array_a))
	{
		echo "<tr><td colspan=11 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_a)." paciente(s) atendido(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_f); $i++)
	{
		$k++;
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_f[$i][0]} order by age_timestamp, age_codigo";
		$exec = pg_query($select);
		$row = pg_fetch_array($exec);
			$busca = "select * from usuario where usu_codigo='{$array_f[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			 $bold_font_open="<font color=red><b>"; $bold_font_close="</font></b>"; $situacao = 'Faltoso';
			echo "
			<tr style='cursor: hand;text-transform:upperCase;' bgcolor='#ffffff' onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090;font-weight:bold' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$k
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $row[usu_codigo] $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_nome] $bold_font_close
			</td>
			<td width=10% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".verIdade("$pac[usu_datanasc]")." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_mae] $bold_font_close
			</td>
			<!--<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open &nbsp; $situacao $bold_font_close
			</td>-->";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".strtoupper($pac[usu_end_cidade])." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp";
			echo "</td>
		</tr>";
	}
	if(!empty($array_f))
	{
		echo "<tr><td colspan=11 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_f)." paciente(s) faltoso(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_t); $i++)
	{
		$k++;
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_t[$i][0]} order by age_timestamp, age_codigo";
		$exec = pg_query($select);
		$row = pg_fetch_array($exec);
			$busca = "select * from usuario where usu_codigo='{$array_t[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=orange><b>"; $bold_font_close="</font></b>"; $situacao = 'Transferido';
			echo "
			<tr style='cursor: hand;text-transform:upperCase;' bgcolor='#ffffff' onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090;font-weight:bold' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$k
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $row[usu_codigo] $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_nome] $bold_font_close
			</td>
			<td width=10% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".verIdade("$pac[usu_datanasc]")." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_mae] $bold_font_close
			</td>
			<!--<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open &nbsp; $situacao $bold_font_close
			</td>-->";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".strtoupper($pac[usu_end_cidade])." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp";
			echo "</td>
		</tr>";
	}
	if(!empty($array_t))
	{
		echo "<tr><td colspan=11 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_t)." paciente(s) transferido(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_m); $i++)
	{
		$k++;
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_m[$i][0]} order by age_timestamp, age_codigo";
		$exec = pg_query($select);
		$row = pg_fetch_array($exec);
			$busca = "select * from usuario where usu_codigo='{$array_m[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=purple><b>"; $bold_font_close="</font></b>"; $situacao = 'Falta do M&eacute;dico';
			echo "
			<tr style='cursor: hand;text-transform:upperCase;' bgcolor='#ffffff' onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090;font-weight:bold' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$k
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $row[usu_codigo] $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_nome] $bold_font_close
			</td>
			<td width=10% style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".verIdade("$pac[usu_datanasc]")." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open $pac[usu_mae] $bold_font_close
			</td>
			<!--<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open &nbsp; $situacao $bold_font_close
			</td>-->";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data', '$hora');\">
				$bold_font_open ".strtoupper($pac[usu_end_cidade])." $bold_font_close
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>&nbsp";
			echo "</td>
		</tr>";
	}
	if(!empty($array_m))
	{
		echo "<tr><td colspan=11 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_m)." consulta(s) com falta do m&eacute;dico.";
		echo "</td></tr>";
	}
	echo "\n</table>";

}

print "
</body>
</html>";

/* fim */
?>
</fieldset>
