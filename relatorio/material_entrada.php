<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../json.js"></script>
<script language="JavaScript" type="text/javascript" src="../produto_.js"></script>
<script>
var gFornecedor
var gDate_i
var gDate_f
var gProduto
var gGrupo
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);

function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 2006) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}

function CheckCall()
{
     gFornecedor    = document.frm_AgPorAgente.for_codigo.value
     gDate_i         = document.frm_AgPorAgente.dt_ini.value
     gDate_f         = document.frm_AgPorAgente.dt_fim.value
     gProduto      = document.frm_AgPorAgente.pro_codigo.value
     gGrupo         = document.frm_AgPorAgente.gru_codigo.value
     gCE                = document.frm_AgPorAgente.CE_codigo.value
     gTipo_mov   = document.frm_AgPorAgente.tipomovim.value
     gMov_nr        = document.frm_AgPorAgente.mov_nr_nota.value
     gAgrupar       = document.frm_AgPorAgente.agrupar.value
     
     if( gDate_i == "" || gDate_f == "" )
     {
             alert('Preencha a data corretamente.');
             return false;
     }
     
     window.open('material_entrada_list.php?for_codigo='+gFornecedor+'&dt_ini='+gDate_i+'&dt_fim='+gDate_f+'&pro_codigo='+gProduto+'&gru_codigo='
                              +gGrupo+'&ce_codigo='+gCE+'&tipomovim='+gTipo_mov+'&mov_nr_nota='+gMov_nr+'&agrupar='+gAgrupar,null,
                              "height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes");
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

echo " <form name=\"frm_AgPorAgente\" method=\"post\" action=\"$PHP_SELF?id_login=$id_login\">\n";
echo "  <fieldset>";
echo "   <legend>Movimento de Entradas</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";

echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Fornecedor: </td>\n";
echo "      <td colspan='2'><select name='for_codigo' class=box>\n";
echo "           <option value='-1' selected> ---- TODOS ---- </option>\n";
                         $stmt = "SELECT for_codigo, for_nome FROM fornecedor ORDER BY 2";
                         $query = db_query( $stmt );
                         
                         while($gFornecedor=pg_fetch_array($query))
                         {
                              echo "<option value='{$gFornecedor[0]}' > ".$gFornecedor[1]."</option>\n";
                         }
                         
echo "          </select>\n";
echo "      </td>\n";

echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Produtos: </td>\n";
echo "      <td colspan='2'><select name='pro_codigo' id='codigo_produto' class=box>\n";
echo "           <option value='-1' selected>---- TODOS ---- </option>\n";
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Setor</td>\n";
echo "      <td><select name='CE_codigo' value='$CE_codigo' class=box >\n";
					/*$query=pg_query("SELECT set_codigo, set_nome FROM setor WHERE set_estoque='S' ORDER BY set_nome");*/
					$select = "select uni_codigo from usuarios where usr_codigo = $id_login";
					$uni = db_get($select);
					if($uni != "")
					{
					  $and_sql = " AND uni_codigo = $uni ";
					}
					$sql = "SELECT s.set_codigo, 
								   set_nome 
							  FROM setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							$and_sql
							ORDER BY set_nome";
					$query = db_query($sql);
					while($CentroEstoq=pg_fetch_array($query)) {
						  echo ($CentroEstoq==$CentroEstoq[set_codigo])?
						        "<option value='$CentroEstoq[set_codigo]' selected> $CentroEstoq[set_nome]</option>" :
								"<option value='$CentroEstoq[set_codigo]'         > $CentroEstoq[set_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Tipo de Consumo</td>\n";
echo "      <td><select name='tipomovim' class='box'>\n";
echo "           <option value=''> --- Todos Tipos de Consumo --- </option>\n";
echo "           <option value='A'> Ajuste</option>\n";
echo "           <option value='V'> Devolução</option>\n";
echo "           <option value='D'> Doação</option>\n";
echo "           <option value='M'> Emprestimo</option>\n";
echo "           <option value='I'> Inventario</option>\n";
echo "           <option value='E'> Nota Fiscal de Entrada</option>\n";
echo "           <option value='O'> Outras Entradas</option>\n";
echo "           <option value='P'> Permuta</option>\n";
echo "           <option value='T'> Transferência</option>\n";
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Movimentação: </td>\n";
echo "      <td width='10'><input type='text' name='mov_nr_nota' id='mov_nr_nota' value='' class='box' size='10' maxlength='10'>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Data Inicial: </td>\n";
echo "      <td width='10'><input type='text' name='dt_ini' id='dt_ini' value='' class='box' size='10' maxlength='10' onkeypress=\"return Ajusta_Data(this,event);\">\n";
//echo "      <td>&nbsp;<!--<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('dt_ini');return false;\">--></td>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final: </td>\n";
echo "      <td width='10'><input type='text' name='dt_fim' id='dt_fim' value='' class='box' size='10' maxlength='10' onkeypress=\"return Ajusta_Data(this,event);\">\n";
//echo "       <td align='left'>&nbsp;<!--<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('dt_fim');return false;\">--></td>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Agrupar</td>\n";
echo "      <td><input type='radio' checked value='1, 2' name='juntar'
                    onclick='document.frm_AgPorAgente.agrupar.value = this.value'> Fornecedor\n";
echo "           <input type='radio' value='2, 1' name='juntar'
                    onclick='document.frm_AgPorAgente.agrupar.value = this.value'> Medicamento\n";
echo "           <input type='hidden' value='1, 2' name='agrupar'>\n";
echo "      </td> \n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>";
echo "      <td align=\"left\"><input type=\"image\" src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> &nbsp; <a href=\"../rel_index.php?opcao=7#tabs-3\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif\" border=0></a></td>";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
