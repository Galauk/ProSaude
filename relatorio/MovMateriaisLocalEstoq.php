<script>

var dtInicial
var dtFinal
var codSetor
var codProduto

function VerData() {

  dtInicial = document.frm_MovMateriaisLocalEstoq.dt_inicial.value;
  dtFinal   = document.frm_MovMateriaisLocalEstoq.dt_final.value;
  codSetor  = document.frm_MovMateriaisLocalEstoq.set_codigo.value;
  codProduto= document.frm_MovMateriaisLocalEstoq.pro_codigo.value;

  if ((dtInicial == '') || (dtInicial > dtFinal)) {
      alert ("Periodo INVALIDO");
      document.frm_MovMateriaisLocalEstoq.dt_inicial.focus();
      return false;
  }
  if (dtFinal == '') {
      alert ("Data Final INVALIDA");
      document.frm_MovMateriaisLocalEstoq.dt_final.focus();
      return false;
  }
  if (codSetor == '') {
      alert ("Setor NAO INFORMADO");
      document.frm_MovMateriaisLocalEstoq.cod_setor.focus();
      return false;
  }

  window.open('MovimentPeriodLocalEstoq.php?dt_inicial='+dtInicial+'&dt_final='+dtFinal+'&set_codigo='+codSetor+'&pro_codigo='+codProduto,null,"height=400,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

}
</script>

<?

// -> Inclusao principal para montagem do sistema
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

$dt_inicial='01'.date("/m/Y");
$dt_final='30'.date("/m/Y");
$pro_codigo=$proCod;

echo " <link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_MovMateriaisLocalEstoq\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Per&iacute;odo Movimento Centro Estocador</legend> \n";
echo "    <table style=\"width:85%;margin-left:70px;margin-right:0px;\" border='0'  cellspacing='1' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='middle' style=\"width:10%\">Data Final</td>\n";
echo "      <td>\n";
echo "        <input class='box' type='text'   name='dt_final'   size='12' value='$dt_final'  /> \n";
echo "        <input class='box' type='hidden' name='dt_inicial' size='10' value='$dt_inicial'/> \n";
echo "        <input class='box' type='hidden' name='pro_codigo' size='10' value='$pro_codigo'/> \n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='middle'>C.Estocador</td>\n";
echo "      <td><select name='set_codigo' class=box>\n";
echo "           <option value=''> --- Selecione o Centro Estocador ---</option>\n";
		          $UniQuery=pg_query("SELECT set_codigo, set_nome FROM Setor
		                               WHERE set_estoque = 'S'
		                            ORDER BY set_nome");
		          while($SetArray=pg_fetch_array($UniQuery)) {
		               echo ($set_codigo==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_codigo] -> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_codigo] -> $SetArray[set_nome]</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td width=400>&nbsp;</td>";
echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='VerData()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
