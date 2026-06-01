<script>
 function imprimir() {
    window.print();
 }
</script>
<?
//
// Connect to PostgreSQL.
//
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$row = pg_fetch_array (pg_query("select *from unidade where uni_codigo='$uni_codigo'"));
	$med = pg_fetch_array (pg_query("select med_codigo, med_nome from medico where med_codigo in (select med_codigo from medico_especialidade where esp_codigo='$esp_codigo"));
	$agt = pg_fetch_array (pg_query("select *from agente where agt_codigo='$agt_codigo'"));
	$usu = pg_fetch_array (pg_query("select usu_end_nr,usu_cisvir,usu_prontuario,usu_nome,usu_codigo,usu_end_rua,usu_end_cidade, muni_cd_cod_ibge_resid from usuario where usu_codigo='$usu_codigo'"));
	$cid = pg_fetch_array(pg_query("select cid_nome from cidade where cid_codigo_ibge = $usu[muni_cd_cod_ibge_resid]"));
	$cod = pg_fetch_array (pg_query("select *from atendimento order by ate_codigo desc limit 1"));
	#$nli = pg_fetch_array (pg_query("select nextval('seq_age_codigo')"));
	#$age_codigo = ($nli[0]-1);
	$ref = pg_fetch_array (pg_query("select to_char(age_data, 'DD/MM/YYYY') as age_data,med_codigo,esp_codigo,age_hora,age_tipo from agendamento where age_codigo='$age_codigo'"));
	$esp = pg_fetch_array (pg_query("select *from especialidade where esp_codigo='$ref[esp_codigo]'"));
	$ned = pg_fetch_array (pg_query("select *from medico where med_codigo='$med_codigo'"));
	$sepagt = explode("-",$agt[agt_responsavel]);
?>
<!--<body OnLoad='imprimir()'>-->
<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td><font size=3 face='courier'><?=$row[uni_desc]?></font></td>
	</tr>
	<tr>
	 <td><font size=3 face='courier'><?=$row[uni_localizacao]?></font></td>
	</tr>
	</table>
 </td>
 <td>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td width=74%>&nbsp;</td>
	 <td><iframe name=codigo src='codigo.php?id_login=<?=$id_login?>&age_codigo=<?=$age_codigo?>' frameborder=no marginheight=0 marginwidth=0 scrolling=no width=150 height=80></iframe>
	</tr>
	</table>
  </td>
 </tr>
</table>

<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	 <td><font face=verdana size=2>--------------------------------------------------------------------------------------------------------</font></td>
	</tr>
</table>

<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td width=70><font size=3 face='courier'>CISVIR&nbsp;-&nbsp;</font></td>
 <td><font size=3 face='courier'><?=$usu[usu_cisvir]?></td>
 <td width=200>&nbsp;</td>
 <td width=155><font size=3 face='courier'>AGENTE DE SAUDE:</font></td>
 <td><font size=3 face='courier'><?=$sepagt[0]?> - <?=$agt[agt_descricao]?></td>
</tr>
</table>
<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td width=20><font size=3 face='courier'>&nbsp;-&nbsp;</font></td>
 <td><font size=3 face='courier'>&nbsp;</td>
 <td width=200>&nbsp;</td>
 <td width=150><font size=3 face='courier'>&nbsp;</font></td>
 <td><font size=3 face='courier'>&nbsp;</td>
</tr>
<tr>
 <td colspan=5><font face=verdana size=2>-------------------------------------------------------------------------------------------------------</font></td>
</tr>
</table>

<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td><font size=3 face='courier'><?=$usu[usu_nome]?></font></td>
 <td width=180><font size=3 face='courier'>N&uacute;mero do Paciente:</font></td>
 <td width=130><font size=3 face='courier'><?=$usu[usu_prontuario]?></td>
</tr>
</table>

<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td width=65><font size=3 face='courier'>Endere&ccedil;o:</font></td>
 <td><font size=3 face='courier'>&nbsp;<?=$usu[usu_end_rua]?> <?=$usu[usu_end_nr]?></font></td>
</tr>
<tr>
 <td width=65><font size=3 face='courier'>Cidade&nbsp;&nbsp;:</font></td>
 <td><font size=3 face='courier'>&nbsp;<?/*=$usu[usu_end_cidade]*/?><?=$cid['cid_nome']?></font></td>
</tr>
</table>
<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td colspan=2><font face=verdana size=2>-------------------------------------------------------------------------------------------------------</font></td>
</tr>
</table><br>
<font face='courier' size=3>Informamos abaixo, os dados referentes ao agendamento:</font>

<br><BR>
<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr>
 <td width=65><font size=3 face='courier'>Especialidade.:</font></td>
 <td><font size=3 face='courier'>&nbsp;<?=$esp[esp_nome]?></font></td>
</tr>
<tr>
 <td width=65><font size=3 face='courier'>Data..........:</font></td>
 <td><font size=3 face='courier'>&nbsp;<?=$ref[age_data]?> <?=$ref[age_hora]?></font></td>
</tr>
<tr>
 <td width=65><font size=3 face='courier'>Profissional..:</font></td>
 <td><font size=3 face='courier'>&nbsp;<?=$ned[med_nome]?></font></td>
</tr>
<tr>
 <td width=65><font size=3 face='courier'>Endere&ccedil;o......:</font></td>
 <td><font size=3 face='courier'>&nbsp;<?=$row[uni_localizacao]?></font></td>
</tr>
<tr>
 <td width=65><font size=3 face='courier'>Procedimento..:</font></td>
 <td><font size=3 face='courier'>&nbsp;CONSULTA M&Eacute;DICA</font></td>
</tr>
</table>
<br>

<font face='courier' size=3>O NAO COMPARECIMENTO NO DIA E HORA MARCADO, INVALIDARA A SUA CONSULTA</font>
</body>

