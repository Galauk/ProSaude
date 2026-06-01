<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
var dtInicial
var dtFinal
var codSetor
var codProduto

function VerData() {

  dtInicial = document.frm_PosEstLocalEstoq.dt_inicial.value;
  dtFinal   = document.frm_PosEstLocalEstoq.dt_fim.value;
  codSetor  = document.frm_PosEstLocalEstoq.set_codigo.value;
  codGrupo  = document.frm_PosEstLocalEstoq.gru_codigo.value;

  if (dtFinal == '') {
      alert ("Data Estoque INVALIDO");
      document.frm_PosEstLocalEstoq.dt_fim.focus();
      return false;
  }
  window.open('PosicaoEstMinimo.php?dt_inicial=' + dtInicial   +
                                        '&dt_final=' + dtFinal     +
                                      '&set_codigo=' + codSetor    +
                                      '&gru_codigo=' + codGrupo
                                      , null
                                      ,"height=400,width=750,status=yes,resizable=yes, toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>

<?
$data = date("d/m/Y");

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo " <link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_PosEstLocalEstoq\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "<input type=hidden name=id_login value=$id_login>";
echo "  <fieldset>";
echo "   <legend>Produtos que atingiram o Estoque Minimo</legend> \n";

echo "    <table style=\"margin-left:70px;margin-right:0px;\" border='0'  cellspacing='1' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <input class='box' type='hidden' name='dt_inicial' size='12' value='01-01-1901'/>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td width=20px>Data</td>\n";
echo "      <td width=100px><input type='text' class='box' name='dt_fim' size='12' value='$data' onKeypress='return Ajusta_Data(this, event);'></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td width=20px>Grupo de Produto</td>\n";
echo "      <td width=100px><select name='gru_codigo' class=box>\n";
echo "           <option value=''> --- Selecione o Grupo de Produto ---</option>\n";
		          $UniQuery=pg_query("SELECT gru_codigo, gru_nome FROM grupo ORDER BY gru_nome");
		          while($SetArray=pg_fetch_array($UniQuery)) {
		               echo ($gru_codigo==$SetArray[gru_codigo])?"<option value='$SetArray[gru_codigo]' selected> $SetArray[gru_nome]</option>":"<option value='$SetArray[gru_codigo]' > $SetArray[gru_nome]</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td width=20px>Setor</td>\n";
echo "      <td width=100px><select name='set_codigo' class=box>\n";
		          /*$UniQuery=pg_query("SELECT set_codigo, set_nome FROM Setor where set_estoque = 'S' ORDER BY set_nome");*/
					$select = "select uni_codigo from usuarios where usr_codigo = $id_login";
					$uni = db_get($select);
					if($uni != "")
					{
					  $and_sql = " AND uni_codigo = $uni ";
					}
					$sql = "SELECT set_codigo, set_nome FROM Setor
							WHERE set_estoque = 'S'
							$and_sql
							ORDER BY set_nome";
					$UniQuery = db_query($sql);
		          while($SetArray=pg_fetch_array($UniQuery)) {
		               echo ($set_codigo==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_nome]</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";


echo "     <tr>\n";
echo "      <td> <input type=image  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='VerData()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td colspan=2> <a href=../rel_index.php?id_login=$id_login&opcao=7#tabs-3><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border =0> </a></td></tr>\n";
echo "     </tr>\n";
echo "    </table>\n";

echo "  </fieldset>\n";
echo " </form>\n";
