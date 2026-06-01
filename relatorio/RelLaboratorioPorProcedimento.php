<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
 
var gMedico
var gProced
var gLabProc
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);

function CheckCall()
{
   gMedico   = document.frm_RelLaboratorioPorProcedimento.med_codigo.value
   gProced   = document.frm_RelLaboratorioPorProcedimento.proc_codigo.value
   gLabProc  = document.frm_RelLaboratorioPorProcedimento.TipoRel.value
   window.open('RelP_LaboratorioPorProcedimento.php?med_codigo='+gMedico+'&proc_codigo='+gProced+'&TipoRel='+gLabProc,null,"height=400,width=900,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
//  Marco Alterado agora -  window.open('LaboratorioPorProcedimento.php?med_codigo='+gMedico+';proc_codigo='+gProced+';TipRel='+gLabProc,null,"height=400,width=1050,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
//Marco Alterado - window.open('LaboratorioPorProcedimento.php?$med_codigo='+gMedico+'&proc_codigo='+gproced+'&TipRel='+gLabProc,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes")
}
  
function valor(indice)
{
   document.frm_LaboratorioPorProcedimento.labProc.value = document.frm_RelLaboratorioPorProcedmento.val[indice].value;
}
</script>
 
 <?

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<script>\n";
echo "	 function getIDmedico() {\n";
echo "	       document.frm_RelLaboratorioPorProcedimento.codMed.value = document.frm_RelLaboratorioPorProcedimento.med_codigo.value;\n";
echo "		}\n";
echo "</script>\n";

//echo "<script language="javascript" src="teotokos.js" type="text/javascript"></script>\n";
//--------------------------------------------------------------------------------------------------------------------------


echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

//echo " <form name=\"frm_RelLaboratorioPorProcedimento\" method=\"post\" action=\"$PHP_SELF\">\n";
echo " <form name='frm_RelLaboratorioPorProcedimento' method='post' action='$PHP_SELF'>\n";
echo "  <fieldset>";
echo "   <legend>Relat&oacute;rio de Procedimentos por Laborat&oacute;rio</legend> \n";
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
// ver SQL daqui pra frente
echo "      <td valign='bottom'>Laboratorio</td>\n";
echo "      <td><select name='med_codigo' class=box>\n";
echo "           <option value=''> --- Todos ---</option>\n";
					$query=pg_query("SELECT med_codigo, med_nome FROM medico WHERE prestador_servico = 'S' ORDER BY med_nome");
					while($medico=pg_fetch_array($query))
                    {
						  echo ($med_codigo==$medico[med_codigo])?"<option value='$medico[med_codigo]' selected> $medico[med_nome]</option>":"<option value='$medico[med_codigo]' > $medico[med_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
//echo " $query";
echo "     <tr>\n";
echo "      <td valign='bottom'>Procedimento</td>\n";
echo "      <td><select name='proc_codigo' class=box>\n";
echo "           <option value=''> --- Todos Procedimentos ---</option>\n";
					$query2=pg_query("SELECT proc_codigo, Proc_nome FROM Procedimento Where procedimento.proc_ativo = 'A' ORDER BY Proc_nome");
					while($procedimento=pg_fetch_array($query2))
                                        { echo ($proc_codigo==$procedimento[proc_codigo])?"<option value='$procedimento[proc_codigo]' selected> $procedimento[proc_nome]</option>":"<option value='$procedimento[proc_codigo]' > $procedimento[proc_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo " $query";
echo "    <tr>\n";
//echo "      <td colspan='3' class='tdtitle'>Tipo do Relatório \n";
//echo "        <input type='hidden' name='TipoRel' class='box'>\n";
//echo "        <input type='radio'  name='TipoRel'   value='0' checked onClick='valor(0)' class='box'>Por Laboratório \n";
//echo "        <input type='radio'  name='TipoRel'   value='1'         onClick='valor(1)' class='box'>Por Procedimento \n";
echo "	   <td>Tipo da Impressao do Relatorio</td>\n";
echo "     <td>\n";
echo "      <select name='TipoRel' value ='$TipoRel' class='box'>\n";
echo "		  <option value='0' selected>         Por Laboratorio</option>\n";
echo "            <option value='1'>      Por Procedimento</option>\n";
echo "      </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "      <td>$nbsp</td>\n";
echo "     <tr>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>";
echo "      <td><input type=\"image\"  src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg\" OnClick=\"CheckCall()\"  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
?>
