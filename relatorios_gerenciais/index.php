<html>
<head>
<title>Relatorios Gerenciais</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilo.css" rel="stylesheet" type="text/css">
</head>
<br><br><br>
<table width=50% cellspacing=0 cellpadding=0 border=0 align=center>
<tr>
 <td>
<?
if($user=="vp") {
  echo "<font color=black size=4>Olá Valter Pegorer, seja bem vindo aos Relatórios Gerenciais</font>";
}

if($user=="jpv") {
  echo "<font color=black size=4>Olá José Plinio Vicentini, seja bem vindo aos Relatórios Gerenciais</font>";
}

if($user=="gilberto") {
  echo "<font color=black size=4>Olá Gilberto Clemente, seja bem vindo aos Relatórios Gerenciais</font>";
}

?>
<br><br><body bgcolor="#FFFFFF">
  <fieldset style="width:50px;align:center">
  <legend><font color=black size=3>Agendamento/Atendimento</font></legend>
 <table width="20%" border="0" cellspacing="0" align=center cellpadding="5">
  <tr>
    <td><a href=EfetividadeMedico.php?user=<? echo $user; ?>><img src="eficiencia_medico.gif" width="279" height="50" border=0></a></td>
    <td><a href=EfetividadeEspecialidade.php?user=<? echo $user; ?>><img src="eficiencia_medico_especiali.gif" width="279" height="51" border=0></a></td>
  </tr>
  <tr>
    <td><a href=FaltasPeriodo.php?user=<? echo $user; ?>><img src="indice_faltas.gif" width="279" height="51" border=0></a></td>
    <td><a href=AtendimPAM.php?user=<? echo $user; ?>><img src="atendimento_pam.gif" width="279" height="51" border=0></a></td>
  </tr>
  <tr>
    <td ><a href=CustosAtendPAM.php?user=<? echo $user; ?>><img src="custo_atendimento_pam.gif" width="242" height="50" border=0></a></td>
    <td ><a href=ConsultasPaciente.php?user=<? echo $user; ?>><img src="botao_pacientes.gif" width="242" height="50" border=0></a></td>
  </tr>
  <tr>
<!--    <td ><a href=CidadePAM.php?user=<? echo $user; ?>><img src="atendimento_pam_por_cidade_on.jpg" width="242" height="50" border=0></a></td> -->
    <td ><a href=CidadePAM.php?user=<? echo $user; ?>><img src="atendimento_pam_por_cidade_on
.jpg" width="351" height="50" border=0></a></td>
    <td ></td>
  </tr>
</table>
 </fieldset>

  <fieldset style="width: 50px">
  <legend><font color=black size=3>Estoque</font></legend>
 <table width="100%" border="0" align=center cellspacing="0" cellpadding="5">
  <tr>
    <td><a href=ConsumoPAM.php?user=<? echo $user; ?>><img src="posicao_financeire_do_estoq.gif" width="398" height="51" border=0></a></td>
    <td><a href=EstoqueHabitante.php?user=<? echo $user; ?>><img src="estoque_por_habitante.gif" width="229" height="51" border=0></a></td>
  </tr>
  <tr>
    <td><a href=PosicaoFinanceira.php?user=<? echo $user; ?>><img src="posicao_financeira_estoque_.gif" width="469" height="50" border=0></a></td>
    <td><a href=GiroCobertura.php?user=<? echo $user; ?>><img src="giro_on.jpg" width="229" height="51" border=0></a></td>
  </tr>
</table>
  </fieldset>
 </td>
 </tr>
</table>
</body>
</html>
