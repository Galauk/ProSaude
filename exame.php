<?
/**
 * brief Colocando a janela de calendario
 */
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<SCRIPT LANGUAGE="JavaScript">

	function changeLocation(menuObj)
	{
	   var i = menuObj.selectedIndex;
	
	   if(i > 0)
	   {
		  window.location = menuObj.options[i].value;
	   }
	}

	function gradata(hr) {
		 document.forms.age_data.value = hr;
	}

	function gralimpa() {
		 document.forms.age_data.value = '';
	}
	
 function buscaferiado(ver) {
  var data = ver;
  var url = 'age_ajax/busca_feriado.php?data='+data;
	 if(data!='') {
		ajax(url);
	 }
 }

</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>

    echo monta_calendario();

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
reglog($id_login,"Acesando RECEPCAO");
  echo "<fieldset><legend>CADASTRO / EXAMES</legend>";
  echo "
           <fieldset>
            <legend>Opções</legend>
                ".ChmodBtn($id_login,'fazeragendamento','agendar_exame.php?')."
                ".ChmodBtn($id_login,'manutencao_agenda_exames','manutencao_exame.php?')."
                ".ChmodBtn($id_login,'manutencao_exames','manutencao_exame_mensal.php?')."
				".ChmodBtn($id_login,'procedimento','procedimento.php?')."
				".ChmodBtn($id_login,'laboratorio','laboratorio.php?')."
           </fieldset>";

//-> Botoes
 echo "<form name=forms method=post action=$PHP_SELF>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=med_codigo value=$med_codigo>
	<table width=100% align=center cellspacing=2 cellpadding=4 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
         <tr>
          <td>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
		<td width=122 align=right>Laboratorio</td>
		<td width=320>	
		<select name=uni_null class=boxr onChange=\"javascript:changeLocation(this)\">";
		echo "<option>...</option>";
		
		$sql = pg_query("select * from medico where prestador_servico='S' order by med_nome");
		
	  	while($med=pg_fetch_array($sql)) {
	  	echo ($med[med_codigo]==$med_codigo)?"<option value='$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]' selected>$med[med_nome]</option>":"<option value='$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]'>$med[med_nome]</option>";
	  }
	$agesel = pg_fetch_array(pg_query("select usr_codigo_cad,to_char(dt_cadastro,'DD/MM/YYYY') as dt_cadastro from agendamento where age_codigo = '$age_codigo'"));
	$seluser = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$agesel[usr_codigo_cad]'"));
	
$exp=explode("/",$id_dia);
$ALLSEMANA = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
switch($ALLSEMANA) {
case 1:
$dia_da_semana = "Segunda Feira";
break;

case 2:
$dia_da_semana = "Terça Feira";
break;

case 3:
$dia_da_semana = "Quarta Feira";
break;

case 4:
$dia_da_semana = "Quinta Feira";
break;

case 5:
$dia_da_semana = "Sexta Feira";
break;

case 6:
$dia_da_semana = "Sábado";
break;

case 0:
$dia_da_semana = "Domingo";
break;

}
	
	echo "</select>
	
	</td>
    </tr>
</table>";

// valida a data se é feriado ou se é final de semana.

echo"
<form name='diamanutencao' method=post action=$PHP_SELF>
	<table width=100% align=left cellspacing=3 cellpadding=0 border=0>
	<tr>
		<td width=140 align=right>Data</td>
		<td width=5 valign=middle><input type=text name=id_dia class=boxn id='data' size=12 maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" OnChange=\"buscaferiado(this.value);\"></td>
		<td width=40><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('data');return false;buscaferiado(document.getElementById('data').value);\"></td>
        <td width=250 align=left valign=middle><div id='horario'></div></td>
    </tr>
	</table>
</form>";

echo"</td>";
echo"</tr>"; 
echo"</table>";


//------------------------------------------------------------------>
//-> Tabela do iframe
//------------------------------------------------------------------>
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0 align=center>
			  <tr>
				  <td align=center>
					  <iframe name=frameprincipal src=exame_iframe.php?id_dia=$id_dia&med_codigo=$med_codigo&proc_codigo=$proc_codigo&id_login=$id_login frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=220></iframe>
				  </td>
			  </tr>
		  </table>";
//------------------------------------------------------------------>

?>
</fieldset>