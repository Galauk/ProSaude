<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
var gdtInicial
var gdtFinal
var gUnidade
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
   if (date_array[2] < 1998) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}

function CheckCall() {
   gdtInicial = document.frm_EfetAtendPorMedico.dt_inicial.value;
   gdtFinal   = document.frm_EfetAtendPorMedico.dt_final.value;
   gUnidade   = document.frm_EfetAtendPorMedico.uni_codigo.value;

   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_EfetAtendPorMedico.dt_inicial.focus();
       return false;
   }
   if (gdtFinal == '') {
       gdtFinal=document.frm_EfetAtendPorMedico.dt_final.value=document.frm_EfetAtendPorMedico.dt_inicial.value
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
       document.frm_EfetAtendPorMedico.dt_inicial.focus()
       return false
   }
   if (CheckDate(dat2,"FINAL")==1) { 
       document.frm_EfetAtendPorMedico.dt_final.focus()
       return false
   }
   if  (dat1 > dat2)    { 
        alert("Data Inicial(" + gdtInicial + ") maior que Final(" + gdtFinal + ")")
        return false
   }

  window.open('EfetivAtendPorMedico.php?dt_inicial=' + gdtInicial +
                                        '&dt_final=' + gdtFinal   +
                                      '&uni_codigo=' + gUnidade
                                      ,null
                                      ,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>

<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_EfetAtendPorMedico\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Relat&oacute;rio Efetividade Por Medico</legend> \n";
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
echo "      <td valign='bottom'>Unidade</td>\n";
echo "      <td><select name='uni_codigo' class=box>\n";
echo "           <option value=''> --- Todas Unidades --- </option>\n";
				  $query=pg_query("SELECT uni_codigo, uni_desc FROM Unidade ORDER BY uni_desc");
				  while($Unidade=pg_fetch_array($query)) {
		          echo ($uni_codigo==$Unidade[uni_codigo])?"<option value='$Unidade[uni_codigo]' selected> $Unidade[uni_desc]</option>":"<option value='$Unidade[uni_codigo]' > $Unidade[uni_desc]</option>\n";
		    }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>";
echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
