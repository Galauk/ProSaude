<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
var dtInicial
var dtFinal
var codSetor
var codProduto

function VerData() {

 // dtInicial = document.frm_PosEstLocalEstoq.dt_fim.value;
 // dtFinal   = dtInicial;
  codSetor  = document.frm_PosEstLocalEstoq.set_codigo.value;
  codProduto  = document.frm_PosEstLocalEstoq.pro_codigo.value;
  prosCodigo  = document.frm_PosEstLocalEstoq.pros_codigo.value;
  zerado  = document.frm_PosEstLocalEstoq.zerado.value;

  if (dtFinal == '') {
      alert ("Data Estoque INVALIDO");
      document.frm_PosEstLocalEstoq.dt_fim.focus();
      return false;
  }
  
  window.open('relatorio/PosicaoEstLoteEstoqNovo.php?set_codigo=' + codSetor    +
                                      '&pro_codigo=' + codProduto  +
									  '&pros_codigo=' + prosCodigo  +
                                      '&zerado=' + zerado    
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
echo "   <legend>Posi&ccedil;&atilde;o Estoque Por Centro Estocador</legend> \n";

echo "    <table style=\"width:85%;margin-left:70px;margin-right:0px;\" border='0'  cellspacing='1' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Produto/Grupo</td>\n";
echo "      <td><div id='select_prod'><select name='pro_codigo' value='$pro_codigo' class=box>\n";
echo "           <option value=''> --- Todos Produtos --- </option>\n";
					$selectgrupro = "(SELECT '1' as tipo,
									   g.gru_codigo as codigo, 
									   g.gru_nome as nome
								  FROM grupo g
								 ORDER BY g.gru_nome asc )
									   
									UNION ALL
									   
							   (SELECT '2' as tipo,
									   p.pro_codigo as codigo, 
									   p.pro_nome as nome
								  FROM produto p
								 ORDER BY p.pro_nome asc)";
		            $query=pg_query($selectgrupro);
                    while($Produto = pg_fetch_array($query)) {
	                     //echo ($pro_codigo == $Produto[pro_codigo])?
                              //"<option value='$Produto[tipo]_$Produto[codigo]' selected>".substr($Produto[nome],0,60)."</option>" :
                              echo "<option value='$Produto[tipo]_$Produto[codigo]'         >".substr($Produto[nome],0,60)."</option>\n";
		            }
echo "          </select>\n";
echo "      </td> </div> \n";
echo "     </tr>\n";
echo "<tr>
			<td width=70>Subgrupo: </td>
			<td>
				<select name='pros_codigo' class='box'>
					<option value=''>--- Selecione o Subgrupo ---</option>";
					$sqlSub = "select * from produto_subgrupo";
					$query_sub = pg_query($sqlSub);
					while($regSub = pg_fetch_array($query_sub)){
						echo "<option value='$regSub[pros_codigo]'>$regSub[pros_descricao]</option>";
					}
echo"			</select>
			</td>
	</tr>";
echo "     <tr>\n";
echo "      <td valign='middle'>Setor</td>\n";
echo "      <td><select name='set_codigo' class=box>\n";
		          $UniQuery=pg_query("SELECT set_codigo, set_nome FROM Setor where set_estoque = 'S' ORDER BY set_nome");
					#$select = "select uni_codigo from usuarios where usr_codigo = $id_login";
					##$uni = db_get($select);
					#if($uni != "")
					#{
					##  $and_sql = " AND uni_codigo = $uni ";
					#}
					/*$sql = "SELECT s.set_codigo, 
								   set_nome 
							  FROM Setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							$and_sql
							ORDER BY set_nome";*/
					//$UniQuery = db_query($sql);
		          while($SetArray=pg_fetch_array($UniQuery)) {
		               echo ($set_codigo==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_nome]</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "	  <tr>\n";
echo "	   <td>Lista Itens Zerados</td>\n";
echo "     <td>\n";
echo "      <select name='zerado' value ='$zerado' class='box'>\n";
echo "		  <option value='SIM'>SIM</option>\n";
echo "          <option value='NAO' selected>NAO</option>\n";
echo "      </select>\n";
echo "     </td>\n";
echo "	   <td>&nbsp;</td>\n";
echo "     <td>&nbsp;</td>\n";
echo "	  </tr>\n";

echo "     <tr>\n";
echo "      <td> <input type=image  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='VerData()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td colspan=2> <a href=../rel_index.php?id_login=$id_login&opcao=7#tabs-3><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border =0> </a></td></tr>\n";
echo "     </tr>\n";
echo "    </table>\n";

echo "  </fieldset>\n";
echo " </form>\n";
