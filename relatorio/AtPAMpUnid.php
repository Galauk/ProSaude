<script type="text/javascript" src="../funcoes.js"></script> 
<SCRIPT Language="Javascript">

var gdtInicial
var gdtFinal
var ghrInicial
var ghrFinal
var gUnidade
var gHora
var gHoje
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);

function CheckHour(ch , MSG) {
   var hHora=ch
   var hHora=parseInt( hHora.split( ":" )[0].toString() + hHora.split( ":" )[1].toString() )
   if (hHora>2400) {
       alert("Hora " + MSG + " INVALIDA")
       return 2 
   }
   if (MSG=="FINAL") { 
       var vCharBar=vCharSlash=0
       var vAnyChar=new String(" ")
       for (var i = 0; i < gdtInicial.length; i++) {
            if (gdtInicial.charAt(i) == "-") { vCharBar+=1 }
            if (gdtInicial.charAt(i) == "/") { vCharSlash+=1 }
       }
       if (vCharBar>0) { vAnyChar="-" }  else  { vAnyChar="/" } 
       tData_I = parseInt( gdtInicial.split( vAnyChar )[2].toString() + 
                           gdtInicial.split( vAnyChar )[1].toString() + 
                           gdtInicial.split( vAnyChar )[0].toString()  )
       tData_F = parseInt( gdtFinal.split( vAnyChar )[2].toString() + 
                           gdtFinal.split( vAnyChar )[1].toString() + 
                           gdtFinal.split( vAnyChar )[0].toString() ) 
       if (tData_I == tData_F) {
           tHora_I = parseInt( ghrInicial.split( ":" )[0].toString() + ghrInicial.split( ":" )[1].toString() )
           thora_F = parseInt( ghrFinal.split( ":" )[0].toString()   + ghrFinal.split( ":" )[1].toString()   ) 
           if (tHora_I > thora_F) {
               alert("Hora Inicial(" + ghrInicial + ") maior que Final(" + ghrFinal + ")")
               return 2 
           }
       }
   }
}


function CheckTime(ct , MSG) {
   gHora=ct
   var vAux=gHora.length
   if (vAux==5) {
       if (CheckHour(gHora, MSG)==2) { return 1 }
       else                          { return 0 }
   }
   if (vAux==1) { 
       if (!isNaN(gHora)) {
           if (MSG=="INICIAL") { document.frm_AtPAM.hr_inicial.value=ghrInicial="0" + gHora + ":00"  }
           else                { document.frm_AtPAM.hr_final.value  =ghrFinal  ="0" + gHora + ":00"  }
           return 0
       } 
   } else
   if (vAux==2) { 
       if (!isNaN(gHora)) {
           if (MSG=="INICIAL") { document.frm_AtPAM.hr_inicial.value=ghrInicial= gHora + ":00"  }
           else                { document.frm_AtPAM.hr_final.value  =ghrFinal  = gHora + ":00"  }
           return 0
       } 
   } else
   if (vAux>2) {
       if (!isNaN(gHora)) {
           alert ("Hora " + MSG + " invalida (" + gHora + ")")
           return 1
       }
   }
   vAux=0
   for (var i = 0; i < gHora.length; i++) {
        if (gHora.charAt(i) == ":") { vAux=vAux+1 }
   }
   if (vAux>1) { 
       alert ("Hora " + MSG + " invalida (" + gHora + ")")
       return 1
   }
   for (var i = 0; i < gHora.length; i++) {
        if (gHora.charAt(i) == ":") {
            if (i==0) {
                var vAux=gHora.charAt(i+1) + gHora.charAt(i+2)
                if (vAux>60) {
                    alert ("Hora " + MSG + " 1 - minutos maior que 60 (" + vAux + ")") 
                    return 1
                } 
                else {
                      if (vAux<10) { vAux= vAux + "0" }
                      if (MSG=="INICIAL") { document.frm_AtPAM.hr_inicial.value=ghrInicial="00:" +  vAux  }
                      else                { document.frm_AtPAM.hr_final.value  =ghrFinal  ="00:" +  vAux  }
                      return 0 
                }
            } 
            else {
              if (i==1) {
                  var vAux=gHora.charAt(i+1) + gHora.charAt(i+2)
                  if (vAux>60) {
                     alert ("Hora " + MSG + " 2 -  minutos maior que 60 (" + vAux + ")")
                     return 1
                  } 
                  else {
                        if (vAux>0 && vAux<10) { vAux=vAux = "0" + vAux }
                        if (vAux<1)            { vAux=vAux = "00" }
                        if (MSG=="INICIAL") { document.frm_AtPAM.hr_inicial.value=ghrInicial="0" 
                                                                                         + gHora.charAt(0) + ":" + vAux  }
                        else                { document.frm_AtPAM.hr_final.value  =ghrFinal  ="0" 
                                                                                         + gHora.charAt(0) + ":" + vAux  }
                        return 0
                  }
              }
              else {
                if (i==2) {
                    var vAux=gHora.charAt(i+1) + gHora.charAt(i+2)
                    if (vAux>0 && vAux<10) { vAux=vAux = "0" + vAux }
                    if (vAux<1)            { vAux=vAux = "00" }
                    if (MSG!="FINAL") { document.frm_AtPAM.hr_inicial.value=ghrInicial=
                                                                        gHora.charAt(i-2) + gHora.charAt(i-1) +":" + vAux  }
                    else              { document.frm_AtPAM.hr_final.value  =ghrFinal  =
                                                                        gHora.charAt(i-2) + gHora.charAt(i-1) +":" + vAux  }
                    return 0
                }
                else {
                      alert ("Hora " + MSG + " STRING com tamanho maior que 2  (" + vAux + ")")
                      return 1
                }
              }
           }
        }
   }
   return 0
}

function CheckDate(d,MSG) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + MSG)
       return 1
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + MSG)
       return 1
   }
   if (date_array[2] < 2006) {
       alert ("Ano invalido da data " + MSG)
       return 1
   }
}

function CheckCall() {
   gHoje = new Date();
   gHoje = gHoje.getDate() + "-" + (gHoje.getMonth() + 1) + "-" + gHoje.getFullYear()

   gdtInicial=document.frm_AtPAM.dt_inicial.value
   gdtFinal  =document.frm_AtPAM.dt_final.value
   ghrInicial=document.frm_AtPAM.hr_inicial.value
   ghrFinal  =document.frm_AtPAM.hr_final.value
   gUnidade  =document.frm_AtPAM.uni_codigo.value


   if (gdtInicial == '') {
       alert ("Informe Data e Hora Inicio");
       document.frm_AtPAM.dt_inicial.focus();
       return false;
   }
   if (gdtFinal == '') {
       gdtFinal=document.frm_AtPAM.dt_final.value=document.frm_AtPAM.dt_inicial.value
       ghrFinal=document.frm_AtPAM.hr_final.value="24:00"
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
       document.frm_AtPAM.dt_inicial.focus()
       return false
   }
   if (CheckTime(ghrInicial,"INICIAL")==1) {
       document.frm_AtPAM.hr_inicial.focus();
       return false;
   }
   if (CheckDate(dat2,"FINAL")==1) { 
       document.frm_AtPAM.dt_final.focus()
       return false
   }
   if (CheckTime(ghrFinal,"FINAL")==1) {
       document.frm_AtPAM.hr_final.focus();
       return false;
   }
   if  (dat1 > dat2) { 
        alert("Data Inicial(" + gdtInicial + ") maior que Final(" + gdtFinal + ")")
        document.frm_AtPAM.dt_inicial.focus()
        return false
   }
/*
   var Hoje = new Date();
   Hoje     = Hoje.getDate() + "-" + (Hoje.getMonth() + 1) + "-" + Hoje.getFullYear();
   if (!CompData(gdtInicial,Hoje)) {
       alert("Data Inicial(" + gdtInicial + ") maior que Hoje(" + Hoje + ")");
       document.frm_AtPAM.dt_inicial.focus();
       return false;
   }
   if (CompData(ghrInicial,ghrFinal)) {
       alert("Hora Inicial(" + ghrInicial + ") maior que Final(" + ghrFinal + ")");
       return false;
   }
*/
   window.open('AtendimPAMpUnid.php?dt_inicial=' + gdtInicial +
                               '&dt_final=' + gdtFinal   +
                             '&hr_inicial=' + ghrInicial +
                               '&hr_final=' + ghrFinal   +
                             '&uni_codigo=' + gUnidade   
                               , null
                               ,"height=400,width=750,resizable=yes,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
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
echo " <form name=\"frm_AtPAM\" method=\"get\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Relat&oacute;rio Atendimento PAM por Unidade</legend> \n";
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width= 8% align=right> Data Inicio </td>\n";
echo "      <td><input class='box' type='text' name='dt_inicial' size='12' value='$dt_inicial' maxlength=\"10\" onkeypress=\"return Ajusta_Data(this,event);\"/></td>\n";
echo "      <td width=20%> &nbsp; </td>\n";
echo "      <td valign='bottom' width=15% align=right> </td>\n";
echo "      <td><input class='box' type='hidden' name='hr_inicial' size='5' value='$hr_inicial'/></td>\n";
echo "      <td width=15%>&nbsp;</td>\n";
echo "      <td width=15%>&nbsp;</td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' align=right> Data Fim </td>\n";
echo "      <td><input class='box' type='text' name='dt_final' size='12' value='$dt_final' maxlength=\"10\" onkeypress=\"return Ajusta_Data(this,event);\"/></td>\n";
echo "      <td> &nbsp; </td>\n";
echo "      <td valign='bottom' align=right></td>\n";
echo "      <td><input class='box' type='hidden' name='hr_final' size='5' value='$hr_final'/></td>\n";
echo "      <td> &nbsp; </td>\n";
echo "      <td> &nbsp; </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom' align=right>Unidade</td>\n";
echo "      <td colspan=6><select name='uni_codigo' class=box>\n";
		          $query=pg_query("SELECT uni_codigo, uni_desc FROM Unidade ORDER BY uni_desc");
		          while($Unidade=pg_fetch_array($query)) {
		                echo ($uni_codigo==$Unidade[uni_codigo])?"<option value='$Unidade[uni_codigo]' selected> $Unidade[uni_desc]</option>":"<option value='$Unidade[uni_codigo]' > $Unidade[uni_desc]</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr><td colspan=8>&nbsp;</td></tr>\n";
echo "     <tr>\n";
echo "      <td><a href='../rel_index.php?id_login=$id_login#tabs-2'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg  name='voltar' border=0></a></td>";
echo "      <td> <input type=\"image\" src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()' name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";

?>
