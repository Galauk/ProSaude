<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script>
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
       document.frm_AgPorAgente.dt_ini.focus();
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       document.frm_AgPorAgente.dt_ini.focus();
       return 1;
   }
   if (date_array[2] < 2006) {
       alert ("Ano invalido da data " + t)
       document.frm_AgPorAgente.dt_ini.focus();
       return 1;
   }
}


function CheckCall() {

   gdtInicial =document.frm_AgPorAgente.dt_ini.value;
   gdtFinal   =document.frm_AgPorAgente.dt_ini.value;
   gunidade   =document.frm_AgPorAgente.uni_codigo.value;
   gmes		=document.frm_AgPorAgente.mes.value;
   gano		=document.frm_AgPorAgente.ano.value;

   var d1=gdtInicial
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
        }
        else 
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_AgPorPaciente.dt_inicial.focus()
       return false
   }
//   window.open('hp?dt_inicial=' + gdtInicial +
//                                      '&dt_final=' + gdtFinal   +
//					                '&med_codigo=' + gmedico    +
//					                '&esp_codigo=' + gespecial  +
//					                '&uni_codigo=' + gunidade   +
//					                '&TpAgendame=' + gTipAgenda +
//					                '&MostAgente=' + gMostAgente
//					                ,null
//		                            ,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
   window.open('apac_prestador_list.php?uni_codigo='+gunidade+'&mes='+gmes+'&ano='+gano+'&dt_ini='+gdtInicial+'&dt_fim='+gdtFinal,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

  return true
}
var gUnidade
var gmes
var gano
var gdt_ini
var gdt_fim
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

//function CheckCall() {
//   gUnidade	=document.frm_AgPorAgente.uni_codigo.value
//   gmes		=document.frm_AgPorAgente.mes.value
//   gano		=document.frm_AgPorAgente.ano.value
//   gdt_ini	=document.frm_AgPorAgente.dt_ini.value
//   gdt_fim	=document.frm_AgPorAgente.dt_fim.value
//
//  window.open('apac_prestador_list.php?uni_codigo='+gUnidade+'&mes='+gmes+'&ano='+gano+'&dt_ini='+gdt_ini+'&dt_fim='+gdt_fim,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
//}
     function muda()
     {
          for(i = 1; i < 4; i++)
          {
               id = document.getElementById(new String("tp_"+i)).style.display = 'none';
          }
          for(x = 0; x < arguments.length; x++)
          {
               document.getElementById(new String(arguments[x])).style.display = '';
          }
     }
</script>


<? 
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	echo monta_calendario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_AgPorAgente\" method=\"post\" action=\"$PHP_SELF?id_login=$id_login\">\n";
echo "  <fieldset>";
echo "   <legend>APAC Por Prestador</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Prestador: </td>\n";
echo "      <td colspan='2'><select name='uni_codigo' class=box>\n";
echo "           <option value='' selected> ---- TODOS ---- </option>\n";
                         //$query=pg_query("SELECT * FROM medico ORDER BY med_nome ASC");
                         /*$stmt = "(SELECT med_codigo, med_nome, 'medico'
                                        FROM medico
                                        WHERE prestador_servico = 'S' ORDER BY 2)
                                   UNION ALL
                                   (SELECT uni_codigo AS med_codigo, uni_desc AS med_nome, 'apac_medico'
                                        FROM apac_unidade ORDER BY 2)
                                   ORDER BY 2";*/
                         
                         $stmt = "(SELECT uni_codigo as med_codigo, uni_desc as med_nome, 'medico'
                                        FROM unidade
                                        ORDER BY 2)
                                   UNION ALL
                                   (SELECT uni_codigo AS med_codigo, uni_desc AS med_nome, 'apac_medico'
                                        FROM apac_unidade ORDER BY 2)
                                   ORDER BY 2";

                         $query = db_query( $stmt );
                         while($gUnidade=pg_fetch_array($query))
                         {
                              echo "<option value='{$gUnidade[0]};{$gUnidade[2]}' > ".$gUnidade[1]."</option>\n";
                         }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Filtrar por: </td>\n";
echo "      <td colspan='2'>Competencia <input type='radio' name='filtro' id='filtro' onchange=\"muda('tp_1')\">";
echo "          &nbsp;Período <input type='radio' name='filtro' id='filtro' onchange=\"muda('tp_2', 'tp_3')\">\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr id='tp_1' style='display: none;'>\n";
echo "      <td valign='bottom'>Competência: </td>\n";
echo "      <td colspan='2'><select name='mes' class=box>\n";
echo "           <option value='' selected> -- mes -- </option>\n";
echo "           <option value='01'> Janeiro </option>\n";
echo "           <option value='02'> Fevereiro </option>\n";
echo "           <option value='03'> Março </option>\n";
echo "           <option value='04'> Abril </option>\n";
echo "           <option value='05'> Maio </option>\n";
echo "           <option value='06'> Junho </option>\n";
echo "           <option value='07'> Julho </option>\n";
echo "           <option value='08'> Agosto </option>\n";
echo "           <option value='09'> Setembro </option>\n";
echo "           <option value='10'> Outubro </option>\n";
echo "           <option value='11'> Novembro </option>\n";
echo "           <option value='12'> Dezembro </option>\n";
echo "          </select>";
echo "          &nbsp";
$ano = date("Y");
echo "          <select name='ano' id='ano' class='box'>";
				for($i = ($ano - 5); $i <= $ano; $i++)
				{
					if($i == $ano)
					{
						echo "<option value='$i' selected>$i</option>";
					} else {
						echo "<option value='$i'>$i</option>";
					}
				}
echo "          </select>";
//               <input type='text' name='ano' id='ano' value='".date("Y")."' class='box' size='4' maxlength='4'>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr id='tp_2' style='display: none;'>\n";
echo "      <td valign='bottom'>Data Inicial: </td>\n";
echo "      <td width='10'><input type='text' name='dt_ini' id='dt_ini' value='' class='box' size='10' maxlength='10' onkeypress=\"return Ajusta_Data(this,event);\"><td>&nbsp;<!--<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('dt_ini');return false;\">--></td>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr id='tp_3' style='display: none;'>\n";
echo "      <td valign='bottom'>Data Final: </td>\n";
echo "      <td width='10'><input type='text' name='dt_fim' id='dt_fim' value='' class='box' size='10' maxlength='10' onkeypress=\"return Ajusta_Data(this,event);\"><td align='left'>&nbsp;<!--<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('dt_fim');return false;\">--></td>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
//echo "      <td>&nbsp;</td>";
echo "      <td colspan='2'> <input type=\"image\" src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td><a href=\"../rel_index.php?id_login=$id_login&opcao=6#tabs-6\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif\" border=0></a></td>";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
