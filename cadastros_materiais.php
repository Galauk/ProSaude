<?
/**
 * @version Renato 6/6/2007 - 16:42
*/
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
    //       verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


 reglog($id_login,"Acessando Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

$data = date("d/m/Y");

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	        	<td ><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
           <td >".ChmodBtn($id_login,'grupo','grupo.php?acao=form_grupo')."</td>
           <td>".ChmodBtn($id_login,'setor','setor.php?acao=form_setor')."</td>
           <td ><a href=../WebSocialComum/fornecedor.php?acao=form_forn><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fornecedor_on.jpg border=0> </a></td>";
	 // <td width=60><a href=cota_paciente.php?id_login=$id_login>Cota Paciente</a>
	echo " <td align=right>".ChmodBtn($id_login,'periodo_setor','abertura_movimento.php?acao=')."</td>

         </tr>
</table>

	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

?>

