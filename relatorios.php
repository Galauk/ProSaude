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

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
 <tr>
  <td>
   <fieldset>
    <legend>Opções</legend>
    <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
      <tr>
            <td align=right><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
          </tr>
        </table>
   </fieldset>
  </td>
 </tr>
        </table><br>";

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em RELATORIOS");
//------------------------------------------------------------------>

  echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0 style='border-left:1px dotted;border-right:1px dotted;border-top:1px dotted;border-bottom:1px dotted;border-color:909090'>
	 <tr bgcolor=e1e1e1>
	  <td>Relatórios Gerenciais</td>
	 <tr>
	</table>";

//	 <tr bgcolor=ffffff>
//	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/atendimento.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Atendimento</a></td>
//	 <tr>
  echo "<table width=98% align=center cellspacing=0 cellpadding=4 border=0>
	 <tr bgcolor=ffffff>
	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/AgPorMedico.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Agendamento por Médico</a></td>
	 <tr>
	 <tr bgcolor=ffffff>
	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/AgPorUnidade.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Agendamento por Unidade</a></td>
	 <tr>
         <tr bgcolor=ffffff>
 	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/AgPorPeriodo.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Agendamento por Período</a></td>
	 <tr>
         <tr bgcolor=ffffff>
 	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/FaltPorPeriodo.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Faltosos por Período</a></td>
	 <tr>
	 <tr>
         <tr bgcolor=ffffff>
 	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/EfetAtendPorMedico.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Efetivo Atendimento Por Médico</a></td>
	 <tr>
	 <tr>
         <tr bgcolor=ffffff>
 	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/AgPorAgente.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Agendamento Por Agente</a></td>
	 <tr>
<!--     <tr>
         <tr bgcolor=ffffff>
 	  <td style='border-bottom:1px dotted;border-color:909090'><a href='relatorio/teste.php'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ico_relatorio.jpg align=absmiddle border=0>&nbsp;Teste do Agata</a></td>
	 <tr> -->

	</table>";
?>

