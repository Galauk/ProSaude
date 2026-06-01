<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script src=script.js></script>
<SCRIPT Language="Javascript">

var gdtInicial
var gdtFinal
var gmedico
var gespecial
var gunidade
var gTipAgenda
var gMostAgente
var gHoje
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
   if (date_array[2] < 1999) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}


function CheckCall() {

   gdtInicial =document.frm_TempoEsperaPaciente.dt_inicial.value;
   gdtFinal   =document.frm_TempoEsperaPaciente.dt_final.value;
   gunidade   =document.frm_TempoEsperaPaciente.uni_codigo.value;
   gmedico    =document.frm_TempoEsperaPaciente.med_codigo.value;
 
    if (gmedico == ' --- Todos Medicos --- ') {
       alert ("Informe o Medico");
       document.frm_TempoEsperaPaciente.med_codigo.focus();
       return false;
   }

/*   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_TempoEsperaPaciente.dt_inicial.focus();
       return false;
   }
    if (gdtFinal == '') {
       alert ("Informe Data Final");
       document.frm_TempoEsperaPaciente.dt_final.focus();
       return false;
   }

   var d1=gdtInicial;   
   var d2=gdtFinal;   
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
        }
        else 
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
        }
   }
	for (var i = 0; i < d2.length; i++) {
        if (d2.charAt(i) == "-") {
           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
        }
        else 
        if (d2.charAt(i) == "/") {
           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString())
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_TempoEsperaPaciente.dt_inicial.focus()
       return false
   }
	if (CheckDate(dat2,"FINAL")==1) {
       document.frm_TempoEsperaPaciente.dt_final.focus()
       return false
   }   
*/
   window.open('TempoPorPaciente.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&uni_codigo='+gunidade+'&med_codigo='+gmedico, null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

  return true
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
echo " <form name='frm_TempoEsperaPaciente' method='post' action='$PHP_SELF'>\n";
echo "  <fieldset>";
echo "   <legend>Tempo de espera do paciente para ser atendido</legend> \n";
echo "    <table whidht=90% border=0 cellspacing=2 cellpadding=1>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' style='width:10%'>Data Inicial</td>\n";
echo "      <td><input class='box' type='text'   name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\">\n";
echo "       </td>";
echo "      </tr>";
echo "      <tr>";
echo "       <td>";
echo "	Data Final";
echo "       </td>";
echo "       <td>";
echo "          <input class='box' type='text' name='dt_final' size='12' value='$dt_final' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"/></td>\n";
echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Unidade</td>\n";
echo "      <td><select name='uni_codigo' value='$uni_codigo' class='box'>\n";
echo "           <option value=''> --- Todas as Unidades ---</option>\n";
			$query=pg_query("SELECT uni_codigo, uni_desc FROM unidade ORDER BY uni_desc");
			while($unidade=pg_fetch_array($query)) 
			{
		 		echo ($uni_codigo==$unidade[uni_codigo])?
			        "<option value='$unidade[uni_codigo]' selected> $unidade[uni_desc]</option>" :
				"<option value='$unidade[uni_codigo]' > $unidade[uni_desc]</option>\n";
			}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>M&eacute;dico</td>\n";
echo "      <td><select name='med_codigo' value='$med_codigo' class='box'>\n";
echo "           <option value=''> --- Todos Medicos ---</option>\n";
			$query=pg_query("SELECT med_codigo, med_nome FROM medico ORDER BY med_nome");
			while($medico=pg_fetch_array($query)) 
			{
			        echo ($med_codigo==$medico[med_codigo])?
			        "<option value='$medico[med_codigo]' selected> $medico[med_nome]</option>" :
				"<option value='$medico[med_codigo]' > $medico[med_nome]</option>\n";
			}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "      <td><a href='../rel_index.php?id_login=$id_login#tabs-1'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg  name='voltar' border=0></a></td>";
echo "     <td><img  src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg\" OnClick=\"CheckCall()\"  name='enviar' value='ENVIAR'> </td>\n";
echo "    </tr>\n";

echo "   </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
