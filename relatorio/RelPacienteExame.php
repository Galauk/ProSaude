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
   gTpRel     =document.frm_RelQtExPorLaboratorio.tp_rel.value



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
  if(gTpRel == 1) {
	window.open('relatorio/relexamepaciente.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&med_codigo='+gMedico+'&proc_codigo='+gProc+'&TipRel='+gTipoRel+'&SinAnal='+gSinAnal,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
  }else{
	  var dt1 = gdtInicial.replace("/", "-").replace("/","-")
	  var dt2 = gdtFinal.replace("/", "-").replace("/","-")
	  window.open('zf/relatorio/laboratorio/rel-paciente-exames/proc_codigo/'+gProc+'/med_codigo/'+gMedico+'/data_inicial/'+dt1+'/data_final/'+dt2,null,"height=400,width=750,status=yes,toolbar=yes,menubar=yes,location=yes,scrollbars=yes");
  }

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
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<script>\n";
echo "	 function getIDmedico() {\n";
echo "	       document.frm_RelQtExPorLaboratorio.codMed.value = document.frm_RelQtExPorLaboratorio.med_codigo.value;\n";
echo "		}\n";
echo "</script>\n";

//echo "<script language="javascript" src="teotokos.js" type="text/javascript"></script>\n";
//--------------------------------------------------------------------------------------------------------------------------


echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_RelQtExPorLaboratorio\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Relat&oacute;rio de Quantidade de Pacientes Agendamento/Exames</legend> \n";
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Incial</td>\n";
echo "      <td><input class='box' type='text'   name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final</td>\n";
echo "      <td><input class='box' type='text'   name='dt_final'   size='12' value='$dt_final'  / maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Laboratorio</td>\n";
echo "      <td><select name='med_codigo' class=box>\n";
echo "           <option value=''> --- Todos Laborat�rios---</option>\n";
					$query=pg_query("select med_nome,m.med_codigo from convenio as c join medico as m on m.med_codigo=c.med_codigo order by m.med_nome");
					while($medico=pg_fetch_array($query)) {
						  echo ($med_codigo==$medico[med_codigo])?"<option value='$medico[med_codigo]' selected> $medico[med_nome]</option>":"<option value='$medico[med_codigo]' > $medico[med_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Procedimentos</td>\n";
echo "      <td><select name='proc_codigo' id='proc_codigo' class=box>\n";
echo "           <option value=''> --- Todas Procedimentos ---</option>\n";
					$query=pg_query("SELECT proc_codigo, proc_nome FROM procedimento ORDER BY proc_nome");
					while($proced=pg_fetch_array($query)) {
						  echo ($proc_codigo==$proced[proc_codigo])?"<option value='$proced[proc_codigo]' selected> $proced[proc_nome]</option>":"<option value='$proced[proc_codigo]' > $proced[proc_nome]</option>\n";
	//				while($unidade=pg_fetch_array($query)) {
	//					  echo ($uni_codigo==$unidade[uni_codigo])?"<option value='$unidade[uni_codigo]' selected> $unidade[uni_desc]</option>":"<option value='$unidade[uni_codigo]' > $unidade[uni_desc]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr>
				<td>Tipo de Relatorio</td>
				<td>
					<input type=radio value=1 name=tp_rel id=tp_rel checked=checked>Sint&eacute;tico<input type=radio value=2 name=tp_rel id=tp_rel>Anal&iacute;tico
				</td>
		   </tr>";

/*echo "    <tr>\n";
echo "	   <td>Organiza��o do Relatorio</td>\n";
echo "     <td>\n";
echo "      <select name='TipoRelat' value ='$TipoRel' class='box'>\n";
echo "		  <option value='0' selected>         Por Laboratorio</option>\n";
echo "            <option value='1'>      Por Procedimento</option>\n";
echo "      </select>\n";
echo "      </td>\n";
echo "   </tr>\n";
echo "      <td>$nbsp</td>\n";


echo "    <tr>\n";
echo "	   <td>Tipo do Relatorio</td>\n";
echo "     <td>\n";
echo "      <select name='SintetAnal' value ='$SinAnal' class='box'>\n";
echo "		  <option value='0' selected>         Sint�tico</option>\n";
echo "            <option value='1'>      Anal�tico</option>\n";
echo "      </select>\n";
echo "      </td>\n";
echo "   </tr>\n";
*/
/*

echo "     <tr>\n";
echo "      <td colspan='3' class='tdtitle'>Tipo de Relat�rio \n";
echo "        <input type='hidden' name='SinAnal' class='box'>\n";
echo "        <input type='radio'  name='val'   value='0'            onClick='valor2(0)' class='box'>Sint�tico \n";
echo "        <input type='radio'  name='val'   value='1'  checked       onClick='valor2(1)' class='box'>Anal�tico \n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n"; */
echo "      <td>&nbsp;</td>";
echo "        <td><a href='../rel_index.php?opcao=5#tabs-7'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border='0'></a>
			 <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
