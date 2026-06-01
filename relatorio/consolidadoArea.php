<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<SCRIPT Language="Javascript">
var gdtInicial
var gdtFinal
var gMedico
var gProc
var gTipoRel
var gSinAnal
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

function CheckCall() {

   gdtInicial=document.frm_RelQtExPorLaboratorio.dt_inicial.value
   gdtFinal  =document.frm_RelQtExPorLaboratorio.dt_final.value
   gMedico   =document.frm_RelQtExPorLaboratorio.med_codigo.value
   gProc     =document.frm_RelQtExPorLaboratorio.proc_codigo.value


   if (gdtInicial == '') {
       alert ("Informe Data e Hora Inicio");
       document.frm_RelQtExPorLaboratorio.dt_inicial.focus();
       return false;
   }
   if (gdtFinal == '') {
       gdtFinal=document.frm_RelQtExPorLaboratorio.dt_final.value=document.frm_RelQtExPorLaboratorio.dt_inicial.value
   }
   var d1=gdtInicial
   var d2=gdtFinal
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
       document.frm_RelQtExPorLaboratorio.dt_inicial.focus()
       return false
   }
   if (CheckDate(dat2,"FINAL")==1) { 
       document.frm_RelQtExPorLaboratorio.dt_final.focus()
       return false
   }
   if  (dat1 > dat2)    { 
        alert("Data Inicial(" + gdtInicial + ") maior que Final(" + gdtFinal + ")")
        document.frm_RelQtExPorLaboratorio.dt_inicial.focus()
        return false
   }
// alterar o formulario
 // window.open('RelQtExAgendados.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&med_codigo='+gMedico+'&proc_codigo='+gProc+'&TipRel='+gTipoRel+'&SinAnal='+gSinAnal,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
  window.open('relexame_custo.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&med_codigo='+gMedico+'&proc_codigo='+gProc+'&TipRel='+gTipoRel+'&SinAnal='+gSinAnal,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}

function valor(indice)
{
   document.frm_RelQtExPorLaboratorio.LabProc.value = document.frm_RelQtExPorLaboratorio.val[indice].value;
}

</script>


<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
 	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
 	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<script>\n";
echo "	 function getIDmedico() {\n";
echo "	       document.frm_RelQtExPorLaboratorio.codMed.value = document.frm_RelQtExPorLaboratorio.med_codigo.value;\n";
echo "		}\n";
echo "</script>\n";

//echo "<script language="javascript" src="teotokos.js" type="text/javascript">
//--------------------------------------------------------------------------------------------------------------------------
 $common = new commonClass();
 echo $common->incJquery();
 echo $common->menuTab(array('Consolidado Area'));
 	echo $common->bodyTab('1');

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_RelQtExPorLaboratorio\" method=\"post\" action=\"$PHP_SELF\">\n";
 
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width='70'>Data Incial</td>\n";
echo "      <td><input class='inputForm' type='text'   name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n"; 
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final</td>\n";
echo "      <td><input class='inputForm' type='text'   name='dt_final'   size='12' value='$dt_final'  / maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
// ver SQL daqui pra frente
echo "      <td valign='bottom' align='left'>Area</td>\n";
echo "      <td> <input type='text' name='micro' id='micro' value='' class='inputForm'>   </td>\n 
			</tr>
			<tr>";
echo "      <td colspan='2'><span style='cursor:pointer' <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";

echo " </form>\n";
echo $common->closeTab();
