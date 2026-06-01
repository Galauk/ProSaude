<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../json.js"></script>
<script language="JavaScript" type="text/javascript" src="produto_.js"></script>
<script>
var dtInicial
var dtFinal
var codSetor
var codProduto

function VerData() {

  dtInicial = document.frm_MovPeriodLocalEstoq.dt_inicial.value;
  dtFinal   = document.frm_MovPeriodLocalEstoq.dt_final.value;
//  codSetor  = document.frm_MovPeriodLocalEstoq.set_codigo.value;
  codProduto= document.frm_MovPeriodLocalEstoq.codigo_produto.value;
  codGrupo  = document.getElementById('codigo_grupo').value;
  codFornecedor = document.getElementById('codigo_fornecedor').value;
  

  if ((dtInicial == '') ) {
      alert ("Periodo INVALIDO");
      document.frm_MovPeriodLocalEstoq.dt_inicial.focus();
      return false;
  }
//  if (codProduto == '') {
//      alert ("Informe UM Produto");
//      document.frm_MovPeriodLocalEstoq.pro_codigo.focus();
//      return false;
//  }
  if (dtFinal == '') {
      dtFinal = dtInicial;
  }
  window.open('MovimentoEntradaList.php?dt_inicial='+dtInicial+'&dt_final='+dtFinal+'&pro_codigo='+codProduto+'&codigo_fornecedor='+codFornecedor+'&codigo_grupo='+codGrupo,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
}

function teste(id){
    var obj = document.getElementById(id);
    alert(obj.value);
    
    
}

</script>

<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();


echo " <link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_MovPeriodLocalEstoq\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "<input type=hidden name=id_login value=$id_login>";
echo "  <fieldset>";
echo "   <legend>Movimento de Entradas</legend> \n";

echo "    <table style=\"width:85%;margin-left:70px;margin-right:0px;\" border='0'  cellspacing='1' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='middle' width='20%'>Data Inicial</td>\n";
  if (!$dt_inicial) {
      $dataatual = pg_fetch_array(pg_query("select to_char(date(now()),'dd/mm/yyyy')"));
      $dt_inicial = $dataatual[0];
      $dt_final = $dt_inicial;
  }    
echo "      <td><input class='box' type='text' name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='middle'>Data Final</td>\n";
echo "      <td><input class='box' type='text' name='dt_final'   size='12' value='$dt_final'  / maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='middle'>Fornecedor</td>\n";
echo "      <td><select name='codigo_fornecedor' id='codigo_fornecedor' class=box>\n";
					echo "<option value=-1>------TODOS-----</option>";
					$select = "select for_codigo,for_nome from fornecedor order by for_nome ASC";
					$fornecedor = pg_query($select);
					while($reg1=pg_fetch_array($fornecedor)){
					    echo "<option value=$reg1[for_codigo]>$reg1[for_nome]</option>";
					}
echo "          </select>\n";
echo "		<tr>\n";
echo "		<td valign=\"middle\">Grupo</td>\n";
echo "      <td><select name='codigo_grupo' id='codigo_grupo' class=box onchange='atualiza_produtos(this)'>\n";
			    echo "<option value=-1>------TODOS------</option>";
		          $Query=pg_query("SELECT gru_codigo, gru_nome FROM grupo ORDER BY gru_nome ASC");
		          while($reg2=pg_fetch_array($Query)) {
			    echo "<option value=$reg2[gru_codigo]>$reg2[gru_nome]</option>";
		          }
echo "		</select>";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='middle'>Produto</td>\n";
echo "      <td><select name='codigo_produto' id='codigo_produto' class=box>\n";
			echo "<option value=-1>------TODOS------</option>";
			
/*			
		        $ProQuery=pg_query("SELECT pro_codigo, pro_nome FROM produto where gru_codigo = $reg2[gru_codigo] ORDER BY pro_nome");
		        while($reg3=pg_fetch_array($ProQuery)) {
		            echo "<option value=$reg3[pro_codigo]>$reg3[pro_nome]</option>";
		        }
*/
echo "          </select>\n";
echo "		<tr>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td> <input  type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='VerData()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td colspan=2> <a href=../rel_index.php?id_login=$id_login&opcao=7><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border =0> </a></td></tr>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
?>