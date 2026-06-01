<script>
var dtInicial
var dtFinal
var agentche

function VerData() {

  dtInicial = document.frm_teste.dt_inicial.value;
  dtFinal   = document.frm_teste.dt_final.value;
  agentche  = document.frm_teste.agt_codigo.value;

  if (dtInicial > dtFinal) {
      alert ("Periodo INVALIDO");
      document.teste.dt_inicial.focus();
      return false;
  }
  if (dtFinal == '') {
      dtFinal = dtInicial;
  }

  window.open('teste.php3?dt_inicial='+dtInicial+'&dt_final='+dtFinal+'&agt_codigo='+agentche,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>


<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_teste\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Rela&ccedil;&atilde;o teste</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width=15%>Data Inicial</td>\n";
echo "      <td width=75%><input class='box' type='text' name='dt_inicial' size='12' value='$dt_inicial'/></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final</td>\n";
echo "      <td>          <input class='box' type='text' name='dt_final'   size='12' value='$dt_final'  /></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Agente</td>\n";
echo "      <td><select name='agt_codigo' class=box>\n";
echo "           <option value=''> --- Selecione um Agente ---</option>\n";
		          $query=pg_query("SELECT agt_codigo, agt_responsavel, agt_descricao FROM Agente ORDER BY agt_responsavel");
		          while($Agente=pg_fetch_array($query)) {
		               echo ($agt_codigo==$Agente[agt_codigo])?"<option value='$Agente[agt_codigo]' selected> ".$Agente[agt_responsavel] . " -> " . $Agente[agt_descricao]."</option>":"<option value='$Agente[agt_codigo]' > ".$Agente[agt_responsavel] . " -> " . $Agente[agt_descricao]."</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>";
echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='VerData()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
