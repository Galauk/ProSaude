<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<SCRIPT Language="Javascript">

var gdtInicial
var gdtFinal
var ghrInicial
var ghrFinal
var gUnidade
var gHora
var gHoje
var maxDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);

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
/*
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
*/

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


   gdtInicial=document.frm_AtPAM.dt_inicial.value
   gdtFinal  =document.frm_AtPAM.dt_final.value


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
   window.open('exa_relatorio.php?dt_inicial=' + gdtInicial +
                               '&dt_final=' + gdtFinal   +
                             '&acao=gera' +
                               '&hr_final=' + ghrFinal   +
                             '&uni_codigo=' + gUnidade
                               , null
                               ,"height=400,width=850,resizable=yes,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
</script>
<script>
        function imprimir()
        {
                window.print();
                //para limpar os campos do agendamento.
                window.opener.limpar();
                //
        }
</script>
<link href="estilo_exame.css" rel="stylesheet" type="text/css">
<?

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

reglog($id_login,"Acessando Digitacao do Resultado");

if(empty($acao)) {

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	cabecario();
            echo monta_calendario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo " <form name=\"frm_AtPAM\" method=\"get\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "<input type=hidden name=acao value=gera>";
echo "   <legend>Relat&oacute;rio Atendimento PAM</legend> \n";
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td width=15% align=right> Inicio </td>\n";
echo "      <td width= 8% align=right> Data </td>\n";
echo "      <td>

            <table cellspacing=0 cellpadding=0 border=0>
            <tr>
                <td width=50><input class='box' type='text' name='dt_inicial' id='dt_inicial' size='12' value='$dt_inicial'onKeypress=\"return Ajusta_Data(this, event);\"/>
                <td>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('dt_inicial');return false;\"></td>
            </tr>
            </table>

            </td>\n";
echo "      <td width=15%>&nbsp;</td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td align=right> Fim&nbsp;&nbsp; </td>\n";
echo "      <td align=right> Data </td>\n";
echo "      <td>

            <table cellspacing=0 cellpadding=0 border=0>
            <tr>
                <td width=50><input class='box' type='text' name='dt_final' id='dt_final' size='12' value='$dt_final'onKeypress=\"return Ajusta_Data(this, event);\"/>
                <td>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('dt_final');return false;\">
</td>
            </tr>
            </table>

            </td>\n";
echo "      <td> &nbsp; </td>\n";
echo "      <td> &nbsp; </td>\n";
echo "     </tr>\n";

echo "     <tr><td colspan=8>&nbsp;</td></tr>\n";
echo "     <tr>\n";
echo "      <td>&nbsp;</td>";
echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()' name='enviar' value='ENVIAR'></td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";



}


if($acao==gera) {
echo "<body onload='imprimir();'>";
echo "<table width=100% cellspacing=1 cellpadding=4 border=0>
	<tr bgcolor=c9c9c9>
	 <td>Descricao do Exame</td>
	 <td>Codigo (SUS)</td>
	 <td>Mes/Ano de Ref.</td>
	 <td align=center>Qtd</td>
	</tr>";
$sql = pg_query("select distinct(it.proc_codigo),proc.proc_nome,count(it.proc_codigo) as total,to_char(mat.mlz_datadacoleta,'MM/YYYY') as mlz_datacoleta from itensdoexame as it left join procedimento as proc on it.proc_codigo = proc.proc_codigo left join materialdeanalise as mat on mat.itx_codigo = it.itx_codigo where to_char(mat.mlz_datadacoleta,'DD/MM/YYYY') >= '$dt_inicial' and  to_char(mat.mlz_datadacoleta,'DD/MM/YYYY') <= '$dt_final' group by it.proc_codigo,proc.proc_nome,mat.mlz_datadacoleta");
while($row=pg_fetch_array($sql)) {
$exa = pg_fetch_array(pg_query("SELECT tp.tma_codigo,cat.cte_cargo,tp.txa_codigo,p.proc_nome,p.proc_classificacao_sus from tipodeexame as tp left join procedimento as p on tp.proc_codigo = p.proc_codigo left join categoriadeexames as cat on tp.cte_codigo = cat.cte_codigo where p.proc_codigo = $row[proc_codigo] order by proc_nome"));
 echo "<tr>
	<td style='border-bottom: 1px solid;border-color:c9c9c9;'>$row[proc_nome]</td>
	<td style='border-bottom: 1px solid;border-color:c9c9c9;'>$exa[proc_classificacao_sus]</td>
	<td style='border-bottom: 1px solid;border-color:c9c9c9;'>$row[mlz_datacoleta]</td>
	<td  style='border-bottom: 1px solid;border-color:c9c9c9;' align=center>$row[total]</td>
	</tr>";
$qtd += $row[total];
}
echo "	<tr bgcolor=c9c9c9>
	 <td colspan=3><b>Total</b></td>
	 <td align=center><b>$qtd</b></td>
	</tr>
	</table>";

}
