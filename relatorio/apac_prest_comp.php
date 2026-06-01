<script type="text/javascript" src="../funcoes.js"></script> 
<script>
var gUnidade
var gMes_ini
var gAno_ini

function CheckCall() {
   gUnidade    =document.frm_AgPorAgente.uni_codigo.value
   gMes_ini    =document.frm_AgPorAgente.mes_ini.value
   gAno_ini    =document.frm_AgPorAgente.ano_ini.value

   if ( (gMes_ini == '') || (gAno_ini == '') ) {
      alert ("Entre com a competência");
      //document.frm_AgPorAgente.dt_inicial.focus();
      return false;
  }

  window.open('apac_prest_comp_list.php?uni_codigo='+gUnidade+'&mes_ini='+gMes_ini+'&ano_ini='+gAno_ini,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
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
echo "   <legend>Número de APACs por Prestador e Competência</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";
/*
echo "     <tr>\n";
echo "      <td valign='bottom'>Prestador: </td>\n";
echo "      <td><select name='uni_codigo' class=box>\n";
echo "           <option value=''> ---- TODOS ---- </option>\n";
		          $query=pg_query("SELECT uni_codigo, uni_desc FROM Unidade ORDER BY uni_desc");
		          while($gUnidade=pg_fetch_array($query))
		          {
		               echo ($uni_codigo==$gUnidade[uni_codigo])?
		               "<option value='$gUnidade[uni_codigo]' selected> ".$gUnidade[uni_desc]."</option>":
		               "<option value='$gUnidade[uni_codigo]' > ".$gUnidade[uni_desc]."</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
*/

echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Prestador: </td>\n";
echo "      <td colspan='2'><select name='uni_codigo' class=box>\n";
echo "           <option value='' selected> ---- TODOS ---- </option>\n";
                         //$query=pg_query("SELECT * FROM medico ORDER BY med_nome ASC");
                         /*$stmt = "(SELECT med_codigo, med_nome, 'medico'
                                        FROM medico
                                        WHERE prestador_servico = 'S' ORDER BY 2)
                                   UNION ALL
                                   (SELECT uni_codigo AS med_codigo, uni_desc AS med_nome, 'apac_medico'
                                        FROM apac_unidade ORDER BY 2)
                                   ORDER BY 2";*/
						 
						 $stmt = "(SELECT uni_codigo as med_codigo, uni_desc as med_nome, 'medico'
                                        FROM unidade
                                        ORDER BY 2)
                                   UNION ALL
                                   (SELECT uni_codigo AS med_codigo, uni_desc AS med_nome, 'apac_medico'
                                        FROM apac_unidade ORDER BY 2)
                                   ORDER BY 2";
                         
                         $query = db_query( $stmt );
                         
                         while($gUnidade=pg_fetch_array($query))
                         {
                              echo "<option value='{$gUnidade[0]};{$gUnidade[2]}' > ".$gUnidade[1]."</option>\n";
                         }
                         
                         //while($gUnidade=pg_fetch_array($query))
                         //{
                           //   echo "<option value='$gUnidade[med_codigo]' > ".$gUnidade[med_nome]."</option>\n";
                         //}
                         
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Competência: </td>\n";
echo "      <td><select id='mes_ini' name='mes_ini' class='box' onchange='document.getElementById('ano_comp').select();'>	
		<option value='' selected> -- Selecione o Mês -- </option>
		<option value=\"1\">Janeiro</option>
		<option value=\"2\">Fevereiro</option>
		<option value=\"3\">Março</option>
		<option value=\"4\">Abril</option>
		<option value=\"5\">Maio</option>
		<option value=\"6\">Junho</option>
		<option value=\"7\">Julho</option>
		<option value=\"8\">Agosto</option>
		<option value=\"9\">Setembro</option>
		<option value=\"10\">Outubro</option>
		<option value=\"11\">Novembro</option>
		<option value=\"12\">Dezembro</option>
                </select>
               <select name='ano_ini' id='ano_ini' class=box>\n;
               <option value=2000>2000</option>
               <option value=2001>2001</option>
               <option value=2002>2002</option>               
               <option value=2003>2003</option>
               <option value=2004>2004</option>
               <option value=2005>2005</option>
               <option value=2006>2006</option>
               <option value=2007 selected=true>2007</option>
               <option value=2008>2008</option>
               <option value=2009>2009</option>
               <option value=2010>2010</option>
               <option value=2011>2011</option>
               <option value=2012>2012</option>
               </select>";                
                
//		<input type='text' name='ano_ini' id='ano_ini' class='box' size='4' maxlength='4' value='".date("Y")."' />\n";


echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
//echo "      <td>&nbsp;</td>";
echo "      <td> <input type=\"image\" src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td><a href=\"../rel_index.php?id_login=$id_login&opcao=6#tabs-6\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif\" border=0></a></td>";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";