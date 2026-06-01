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

   gdtInicial =document.frm_MedPorEspecialidade.dt_inicial.value;
   gdtFinal   =document.frm_MedPorEspecialidade.dt_final.value;
   gespecial  =document.frm_MedPorEspecialidade.esp_codigo.value;
 
   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_MedPorEspecialidade.dt_inicial.focus();
       return false;
   }
    if (gdtFinal == '') {
       alert ("Informe Data Final");
       document.frm_MedPorEspecialidade.dt_final.focus();
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
       document.frm_MedPorEspecialidade.dt_inicial.focus()
       return false
   }
	if (CheckDate(dat2,"FINAL")==1) {
       document.frm_MedPorEspecialidade.dt_final.focus()
       return false
   }   
   window.open('EncPorEspecialidade.php?dt_inicial='+gdtInicial+'&dt_final='+gdtFinal+'&esp_codigo='+gespecial, null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

  return true
}

function AtualizEspecialidad(p){
    url = 'ComboAtualizaEspecialidade.php?valor='+p;
    IdentBrowser(url,1);
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
echo " <form name='frm_MedPorEspecialidade' method='post' action='$PHP_SELF'>\n";
echo "  <fieldset>";
echo "   <legend>Relat&oacute;rio Medico por especialidade</legend> \n";
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
//echo "     <tr>\n";
//echo "      <td valign='bottom'>Data Final</td>\n";
//echo "      <td><input class='box' type='text' name='dt_final'   size='12' value='$dt_final' /></td>\n";
//echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
//echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
//echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
//echo "     </tr>\n";

/*echo "     <tr>\n";
echo "      <td valign='bottom'>Médico</td>\n";
echo "      <td><select name='med_codigo' value='$med_codigo' class=box onChange='javascript:AtualizEspecialidad(this.value);'>\n";
echo "           <option value=''> --- Todos Medicos ---</option>\n";
					$query=pg_query("SELECT med_codigo, med_nome FROM medico ORDER BY med_nome");
					while($medico=pg_fetch_array($query)) {
						  echo ($med_codigo==$medico[med_codigo])?
						        "<option value='$medico[med_codigo]' selected> $medico[med_nome]</option>" :
								"<option value='$medico[med_codigo]' > $medico[med_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";*/

echo "     <tr>\n";
echo "      <td valign='bottom'>Especialidade</td>\n";
echo "      <td><div id='select_esp'><select name='esp_codigo' class=box>\n";
echo "           <option value=''> --- Todas Especialidades ---</option>\n";
					if (!$med_codigo) {
					    $query=pg_query("SELECT esp_codigo, esp_nome FROM especialidade ORDER BY esp_nome");
					} else {
					  	    $query=pg_query("SELECT especialidade.esp_codigo, especialidade.esp_nome
							                   FROM especialidade ,  medico_especialidade
											  WHERE especialidade.esp_codigo=medico_especialidade.esp_codigo
									  		    AND medico_especialidade.med_codigo=$med_codigo
										   ORDER BY esp_nome");
                    }
					while($especial=pg_fetch_array($query)) {
					      echo ($esp_codigo==$especial[esp_codigo])?
		                        "<option value='$especial[esp_codigo]' selected> $especial[esp_nome]</option>":"<option value='$especial[esp_codigo]' > $especial[esp_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td> </div> \n";
echo "     </tr>\n";
echo "    <tr>\n";
echo "     <td>&nbsp;&nbsp;&nbsp;</td>\n";
echo "     <td><img  src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg\" OnClick=\"CheckCall()\"  name='enviar' value='ENVIAR'> </td>\n";
echo "    </tr>\n";

echo "   </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
