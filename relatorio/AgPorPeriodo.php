
<script src=script.js></script>
<script>

var gdtInicial
var gdtFinal
var gmedico
var gespecial
var gunidade
var gTipAgenda
var gMostAgente
var gHoje

function CompData(d1 , d2) {

//   ( d1 )   NÃO PODE SER MAIOR QUE   ( d2 )

   var data1 = d1;
   var data2 = d2;

   for (var i = 0; i < data1.length; i++) {
        if (data1.charAt(i) == "-") {
           if (  parseInt( data1.split( "-" )[2].toString() + data1.split( "-" )[1].toString() + data1.split( "-" )[0].toString() ) > parseInt( data2.split( "-" )[2].toString() + data2.split( "-" )[1].toString() + data2.split( "-" )[0].toString() ) )
              { return false }   else    { return true  }
        } else if (data1.charAt(i) == "/") {
                  if (  parseInt( data1.split( "/" )[2].toString() + data1.split( "/" )[1].toString() + data1.split( "/" )[0].toString() ) > parseInt( data2.split( "/" )[2].toString() + data2.split( "/" )[1].toString() + data2.split( "/" )[0].toString() ) )
                     { return false }   else   { return true  }
          }
   }
}

function VerData() {

   gdtInicial = document.frm_AgPorPeriodo.dt_inicial.value;
   gdtFinal   = document.frm_AgPorPeriodo.dt_final.value;
   gmedico    = document.frm_AgPorPeriodo.med_codigo.value;
   gespecial  = document.frm_AgPorPeriodo.esp_codigo.value;
   gunidade   = document.frm_AgPorPeriodo.uni_codigo.value;
   gTipAgenda = document.frm_AgPorPeriodo.TpAgenda.value;
   gMostAgente= document.frm_AgPorPeriodo.MtAgente.value;

   gdtFinal = gdtInicial;
   if (gdtInicial == '') {
       alert("Periodo INVALIDO");
       document.frm_AgPorPeriodo.dt_inicial.focus();
       return false;
   }
   window.open('AgendaPorPeriodo.php?dt_inicial='+gdtInicial +
                                     '&dt_final='+gdtFinal   +
					               '&med_codigo='+gmedico    +
					               '&esp_codigo='+gespecial  +
					               '&uni_codigo='+gunidade   +
					               '&TpAgendame='+gTipAgenda +
					               '&MostAgente='+gMostAgente
					               ,null
		                           ,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

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
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo " <form name='frm_AgPorPeriodo' method='post' action='$PHP_SELF'>\n";
echo "  <fieldset>";
echo "   <legend>Relat&oacute;rio Agendamento por Periodo</legend> \n";
echo "    <table whidht=90% border=0 cellspacing=2 cellpadding=1>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' style='width:10%'>Data </td>\n";
echo "      <td><input class='box' type='text'   name='dt_inicial' size='12' value='$dt_inicial'/>\n";
echo "          <input class='box' type='hidden' name='dt_final'   size='12' value='$dt_final'  /></td>\n";
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

echo "     <tr>\n";
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
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Especialidade</td>\n";
echo "      <td><div id='select_esp'><select name='esp_codigo' >\n";
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

echo "     <tr>\n";
echo "      <td valign='bottom'>Unidade</td>\n";
echo "      <td><select name='uni_codigo' value='$uni_codigo' class=box>\n";
echo "           <option value=''> --- Todas Unidades --- </option>\n";
		            $query=pg_query("SELECT uni_desc, uni_codigo FROM Unidade ORDER BY uni_desc");
		            while($Unidade=pg_fetch_array($query)) {
		                  echo ($uni_codigo==$Unidade[uni_codigo])?"<option value='$Unidade[uni_codigo]' selected> $Unidade[uni_desc]</option>":"<option value='$Unidade[uni_codigo]' > $Unidade[uni_desc]</option>\n";
		            }
echo "          </select>\n";
echo "      </td>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;</td>\n";
echo "     </tr>\n";

echo "	   <tr>\n";
echo "	    <td>Tipo de Agendamento</td>\n";
echo "     <td>\n";
echo "      <select name='TpAgenda' value ='$TpAgenda'>\n";
echo "        <option value='' >...</option>\n";
echo "		  <option value='PC'>PC</option>\n";
echo "        <option value='GE'>GE</option>\n";
echo "		  <option value='RT'>RT</option>\n";
echo "		  <option value='AL'>AL</option>\n";
echo "		  <option value='CA'>CA</option>\n";
echo "      </select>\n";
echo "     </td>\n";
echo "	   <td>&nbsp;</td>\n";
echo "     <td>&nbsp;</td>\n";
echo "	  </tr>\n";

echo "	  <tr>\n";
echo "	   <td>Mostra Agente</td>\n";
echo "     <td>\n";
echo "      <select name='MtAgente' value ='$MtAgente'>\n";
echo "        <option value='' >...</option>\n";
echo "		  <option value='0'>SIM</option>\n";
echo "          <option value='1'>NAO</option>\n";
echo "      </select>\n";
echo "     </td>\n";
echo "	   <td>&nbsp;</td>\n";
echo "     <td>&nbsp;</td>\n";
echo "	  </tr>\n";

echo "    <tr>\n";
echo "      <td><a href='../rel_index.php?id_login=$id_login#tabs-1'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg  name='voltar' border=0></a></td>";
echo "     <td><img  src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg\" OnClick=\"VerData()\"  name='enviar' value='ENVIAR'> </td>\n";
echo "    </tr>\n";

echo "   </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
