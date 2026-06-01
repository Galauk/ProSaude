<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="relatorio/funcoes.js"></script>
<script src=relatorio/script.js></script>
<style>
 .tblall td {
	 border:1px solid;
	 height:35px;
	 padding: 5px;
 }
</style>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$form = new classForm();
$common = new commonClass();
$table = new tableClass();

#if(($_REQUEST['periodo']!=0 OR $_REQUEST['periodo']!="")) {
//	$and = " and io_data_cadastro > localtimestamp - INTERVAL '".$_REQUEST['periodo']."' ";
	$and = " and to_char(io_data_cadastro,'dd/mm/yyyy') = '".date('d/m/Y')."'";
#}
$sql = pg_query("select to_char(usu_datanasc,'dd/mm/yyyy') as datanasc,to_char(io_data_cadastro,'dd/mm/yyyy hh24:mi') as data_cadastro,*from internacao_observacao as io
join unidade as uni on uni.uni_codigo = io.uni_codigo
join agendamento as age on age.age_codigo = io.age_codigo
join usuario as usu on usu.usu_codigo = age.usu_codigo
join usuarios as usr on usr.usr_codigo = io.med_codigo
where age.age_codigo = '".$_REQUEST[age_codigo]."' ".$and." order by io_data_cadastro") or die(pg_last_error());

$r = pg_fetch_array($sql);
$rw = pg_fetch_array(pg_query("select apt_codigo from internacao_observacao as io join quarto as qt on qt.qua_codigo = io.qua_codigo where age_codigo = '".$_REQUEST[age_codigo]."' order by io_codigo desc"));
if($r['usu_sexo']==0) { $sex = "Masculino"; } else { $sex ="Feminino"; }
  echo "<table width=100% cellpacing=0 cellspacing=0 border=0>
		<tr>
		  <td width=120><img src='../imgs/brasao.png' size=120></td>
		  <td valign='top'>
			   <table width=100% cellpacing=0 cellspacing=0 border=0>
			    <tr>
				 <td><h1>".$r['uni_desc']."</h1></td>
				 <td><h2>Prescricao Medica N:".$r['io_codigo']."</h2></td>
				 <td>&nbsp;</td>
				</tr>
			    <tr>
				 <td colspan=3><h2>Paciente: ".$r['usu_nome']."</h2></td>
				</tr>
			    <tr>
				 <td colspan=3>
					<table width=100% cellpacing=0 cellspacing=0 border=0 style='border:1px solid;' >
					   <tr>
					     <td width=50% style='border-right:1px solid;'>
						    <table width=100% cellpacing=0 cellspacing=0 border=0>
							<tr>
							  <td width=70><b>Prontuario:</b></td>
							  <td>".$r['usu_prontuario']."</td>
							 </tr>
							<tr>
							  <td><b>Sexo:</b></td>
							  <td>".$sex."</td>
							 </tr>
							<tr>
							  <td><b>Idade:</b></td>
							  <td>".$r['datanasc']."</td>
							 </tr>
							<tr>
							  <td><b>Internado:</b></td>
							  <td><b>".$r['data_cadastro']."</b></td>
							 </tr>
							 </table>
						  </td>
					     <td>
						    <table width=100% cellpacing=0 cellspacing=0 border=0>
							<tr>
							  <td width=70><b>Medico:</b></td>
							  <td>".$r['usr_nome']."</td>
							 </tr>
							  <td width=70><h2>Localizacao:&nbsp;</h2></td>
							  <td><h2>".$rw['apt_codigo']."</h2></td>
							 </tr>
							 </table>						 
						 </td>
						</tr>
					</table>
				 
				 </td>
				</tr>
				<tr>
				<td></table>
		  </td>
		</tr>
		</table>
		<table width=100% cellpacing=0 cellspacing=0 border=0 class='tblall'>
		<tr>
		 <td width=10%><h2>Prescricao Medica</h2></td>
		 <td width=30%><h2>Horario</h2></td>
		 <td><h2>Evolucao Clinica</h2></td>
		 </tr>";
 $q = pg_query("select to_char(io_data_cadastro,'dd/mm/yyyy') as data,to_char(io_data_cadastro,'hh24:mi') as hora,* from internacao_observacao where age_codigo = '$r[age_codigo]' ".$and." ");
while($rr = pg_fetch_array($q)) {
echo "<tr>
		 <td width=30% valign='top'><font size=3><table>";
$sq = pg_query("select *from internacao_prescricao as ip join produto as p on p.pro_codigo=ip.pro_codigo join tb_administracao_produto as adp on adp.adm_codigo = ip.adm_codigo join frequencia_medicacao as fr on fr.frq_codigo = ip.frq_codigo join unidmedida as um on um.umed_codigo = p.umed_codigo where io_codigo = ".$rr['io_codigo']."");
while($m=pg_fetch_array($sq)) {
	echo "<tr><td valign='top' style='height:40px'>";
	
echo "$m[pro_nome], <b>$m[inp_qtde_dose] $m[umed_nome]</b>, "; echo (trim($m[inp_velocidade]!=''))?"$m[inp_velocidade],":","; echo" $m[adm_sigla], $m[frq_nome], "; echo (trim($m[inp_hrini])!='')?"Inicio em : $m[inp_hrini],":""; echo" $m[inp_observacao]&nbsp;&nbsp;";
	echo "</tr></td>";
	$hr .= "<div style='width:100%;height:39px;border:1px solid;margin: 1px 1px 1px 1px;'></div>";
}	
		 echo "</table></font></td>
		<td valign='top' align=center style='height:35px;padding:6px'>$hr</td>
		 <td width=40%><font size=3>$rr[io_observacao]</font></td>
		 </tr>";
		 $hr="";
}
$cid = pg_fetch_array(pg_query("select *from secretaria order by codigo_secretaria desc"));
  $mes = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
  $numero_mes = date('m')*1;
echo "</table>
		
		<BR><BR><BR>
		<table width=100% cellpacing=0 cellspacing=0 border=0 style='border-top:1px solid;'>
		<tr>
		 <td align=center><h2>$cid[nome_cidade],".date('d')." de ".$mes[$numero_mes]." de ".date('Y').".</h2></td>
		 </tr>
		<tr>
		 <td align=center>Data do Lacamento</td>
		 </tr>
		</table>		 
			<br><br>
		<table width=100% cellpacing=10 cellspacing=40 border=0>
		<tr>
		 <td valign=top align=center style='border-top:1px solid;'>Medico responsavel/Carimbo</td>
		 <td valign=top align=center style='border-top:1px solid;'>Farmaceutico(a) ou Tecnico responsavel/Carimbo</td>
		 <td valign=top align=center style='border-top:1px solid;'>Enfermeiro(a) ou Tecnico responsavel/Carimbo</td>
		 </tr>
		
		";


?>