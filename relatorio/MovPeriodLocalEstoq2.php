<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
var dtInicial
var dtFinal
var codSetor
var codProduto

function VerData() {

  dtInicial = document.frm_MovPeriodLocalEstoq.dt_inicial.value;
  dtFinal   = document.frm_MovPeriodLocalEstoq.dt_final.value;
  codSetor  = document.frm_MovPeriodLocalEstoq.set_codigo.value;
  codProduto= document.frm_MovPeriodLocalEstoq.pro_codigo.value;
  mov_tipo =  document.frm_MovPeriodLocalEstoq.mov_tipo.value;
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
  window.open('MovimentPeriodLocalEstoqVelho.php?dt_inicial='+dtInicial+'&dt_final='+dtFinal+'&set_codigo='+codSetor+'&pro_codigo='+codProduto+'&mov_tipo='+mov_tipo,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
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
echo "   <legend>Per&iacute;odo Movimento Centro Estocador</legend> \n";

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
echo "      <td valign='middle'>C.Estocador</td>\n";
echo "      <td><select name='set_codigo' class=box>\n";
					$select = "select uni_codigo from usuarios where usr_codigo = $id_login";
					$uni = db_get($select);
					if($uni != "")
					{
					  $and_sql = " AND uni_codigo = $uni ";
					}
					$sql = "SELECT s.set_codigo, 
								   set_nome 
							  FROM Setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							$and_sql
							ORDER BY set_nome";
					$UniQuery = db_query($sql);
					while($SetArray=pg_fetch_array($UniQuery))
					{
						echo ($set_codigo==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_nome]</option>\n";
					}
echo "          </select>\n";
				//echo "<pre>$select  -  $sql</pre>";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='middle'>Produto</td>\n";
echo "      <td><select name='pro_codigo' class=box>\n
				<option value=''>--TODOS--</option>";
		          $ProQuery=pg_query("SELECT pro_codigo, pro_nome FROM Produto ORDER BY pro_nome");
		          while($ProArray=pg_fetch_array($ProQuery)) {
		               echo ($set_codigo==$ProArray[pro_codigo])?"<option value='$ProArray[pro_codigo]' selected> $ProArray[pro_nome]</option>":"<option value='$ProArray[pro_codigo]' > $ProArray[pro_nome]</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "	   <tr>
			 <td valign='middle'>Tipo de Entrada</td>\n
			 <td><select name='mov_tipo' class=box>\n
			 		<option value=''>--TODOS--</option>
			 		<option value='E'>Entrada</option>
			 		<option value='S'>Saida</option>
			 		<option value='T'>Transfer&eecirc;ncia</option>
			 	 </select>
			  </td> 
		   </tr>";

echo "     <tr>\n";
echo "      <td> <input  type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='VerData()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td colspan=2> <a href=../rel_index.php?id_login=$id_login&opcao=7#tabs-3><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border =0> </a></td></tr>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
