<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script>
var gdtInicial
var gdtFinal
var gAgent
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);


function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data! Por favor, verifique!! " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data! Por favor, verifique! " + t)
       return 1;
   }
   if (date_array[2] < 2006) {
       alert ("Ano invalido da data! Por favor, verifique! " + t)
       return 1;
   }
}

function CheckCall() {
   gdtInicial=document.frm_AgExamesPorAgente.dt_inicial.value
   gdtFinal  =document.frm_AgExamesPorAgente.dt_final.value
   gAgent    =document.frm_AgExamesPorAgente.agt_codigo.value

   if (gdtFinal == '') {
       gdtFinal=document.frm_AgExamesPorAgente.dt_final.value=document.frm_AgExamesPorAgente.dt_inicial.value
   }
   var d1=document.frm_AgExamesPorAgente.dt_inicial.value
   var d2=document.frm_AgExamesPorAgente.dt_final.value
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
        }
        else 
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString()) 
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_AtPAM.dt_inicial.focus()
       return false
   }
   if (CheckDate(dat2,"FINAL")==1) { 
       document.frm_AtPAM.dt_final.focus()
       return false
   }
   if  (dat1 > dat2)    { 
        alert("Data Inicial(" + ggdtInicial + ") maior que Final(" + ggdtFinal + ")")
        document.frm_AtPAM.dt_inicial.focus()
        return false
   }
  if ((gdtInicial == '') || (gdtInicial > gdtFinal)) {
      alert ("Periodo INVALIDO");
      document.frm_AgPorAgente.dt_inicial.focus();
      return false;
  }
// --------------------------------------------------------------------  ver formulario
  window.open('AgendaExamesPorAgente.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&agt_codigo='+gAgent,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>


<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_AgExamePorAgente\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Rela&ccedil;&atilde;o Agendamento de Exames Por Agente</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width=15%>Data Inicial</td>\n";
echo "      <td width=75%><input class='box' type='text' name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final</td>\n";
echo "      <td>          <input class='box' type='text' name='dt_final'   size='12' value='$dt_final'  / maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Agente</td>\n";
echo "      <td><select name='agt_codigo' class=box>\n";
echo "           <option value=''> --- Selecione um Agente ---</option>\n";
		          $query=pg_query("SELECT agt_responsavel, agt_codigo,  agt_descricao FROM Agente ORDER BY agt_responsavel");
		          while($gAgente=pg_fetch_array($query)) {
		               echo ($agt_codigo==$gAgente[agt_codigo])?"<option value='$gAgente[agt_codigo]' selected> ".$gAgente[agt_responsavel] . " -> " . $gAgente[agt_descricao]."</option>":"<option value='$gAgente[agt_codigo]' > ".$gAgente[agt_responsavel] . " -> " . $gAgente[agt_descricao]."</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td><a href='../rel_index.php?id_login=$id_login#tabs-1'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg  name='voltar' border=0></a></td>";
echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
