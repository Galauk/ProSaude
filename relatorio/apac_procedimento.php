<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script>
var gProcedimento
var gData_i
var gData_f
var gMes
var gAno
var gIdade_i
var gIdade_f

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

function CheckCall()
{
	gProcedimento = document.frm_AgPorAgente.proc_codigo.value
   	gData_i	= document.frm_AgPorAgente.dt_ini.value
	gData_f	= document.frm_AgPorAgente.dt_fim.value
	gMes = document.frm_AgPorAgente.mes.value
	gAno = document.frm_AgPorAgente.ano.value
	/*gIdade_i = document.frm_AgPorAgente.idade_i.value
	gIdade_f = document.frm_AgPorAgente.idade_f.value*/
	
	faixa_etaria = document.getElementById("faixa_etaria").value;

	var d1=gData_i;
	var d2=gData_f;
	for (var i = 0; i < d1.length; i++)
	{
		if (d1.charAt(i) == "-") 
		{
			var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
		}
		else 
			if (d1.charAt(i) == "/") 
			{
				var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
			}
	}
	for (var i = 0; i < d2.length; i++) 
	{
		if (d2.charAt(i) == "-") 
		{
			var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
		}
		else 
			if (d2.charAt(i) == "/") 
			{
				var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString())
			}
	}
	if (CheckDate(dat1,"INICIAL")==1)
	{
		document.frm_AgPorAgente.dt_inicial.focus()
		return false
	}
	if (CheckDate(dat2,"FINAL")==1) 
	{
		document.frm_AgPorAgente.dt_final.focus()
		return false
	}
	/*if( ( gData_i != '' && gData_f == '' ) || ( gData_i == '' && gData_f != '' ) )
	{
		alert ("Entre com as duas datas");
		return false;
	}*/
	
	if(document.getElementById('filtro1').checked)
	{
		acao = 'competencia';
		if(gMes == '')
		{
			alert("Por favor escolha o mes.");
			document.frm_AgPorAgente.mes.focus();
			return false;
		}
	} else if(document.getElementById('filtro2').checked) {
		acao = 'periodo';
		if(document.getElementById('dt_ini').value == '')
		{
			alert("Por favor Preencha a data Inicial. ");
			document.getElementById('dt_ini').focus();
			return false;
		}
		if(document.getElementById('dt_fim').value == '') {
			alert("Por favor Preencha a data Final. ");
			document.getElementById('dt_fim').focus();
			return false;
		}
	} else {
		alert("Por favor escolha o filtro.");
		return false;
	}

	//'&idade_i='+gIdade_i+'&idade_f='+gIdade_f

	url = 'apac_procediemnto_list.php?proc_codigo='+gProcedimento+'&dt_inicial='+gData_i+'&dt_final='+gData_f+'&mes='+gMes+'&ano='+gAno+'&faixa_etaria='+faixa_etaria+"&acao="+acao;

	window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}

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

echo " <form name=\"frm_AgPorAgente\" method=\"post\" action=\"$PHP_SELF?id_login=$id_login\" onsubmit='return CheckCall();'>\n";
echo "  <fieldset>";
echo "   <legend>Procedimentos da APAC por faixa etaria</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Procedimento: </td>\n";
echo "      <td colspan='2'><select name='proc_codigo' class=box>\n";
echo "           <option value=''> ---- TODOS ---- </option>\n";
		          $query=pg_query("SELECT proc_codigo, proc_nome FROM Procedimento ORDER BY proc_nome");
		          while($gProcedimento=pg_fetch_array($query))
		          {
		               echo ($proc_codigo==$gProcedimento[proc_codigo])?
		               "<option value='$gProcedimento[proc_codigo]' selected> ". $gProcedimento[proc_nome] ."</option>" : "<option value='$gProcedimento[proc_codigo]' > ". $gProcedimento[proc_nome] ."</option>\n";
		          }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Filtrar por: </td>\n";
echo "      <td colspan='2'>Compet&ecirc;ncia <input type='radio' name='filtro' id='filtro1' onchange=\"muda('tp_1')\">";
echo "          &nbsp;Per&iacute;odo <input type='radio' name='filtro' id='filtro2' onchange=\"muda('tp_2', 'tp_3')\">\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr id='tp_1' style=\"display: none;\">\n";
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
echo "          &nbsp;<input type='text' name='ano' id='ano' value='".date("Y")."' class='box' size='4' maxlength='4'>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr id='tp_2' style=\"display: none;\">\n";
echo "      <td valign='bottom'>Data Inicial: </td>\n";
echo "      <td width='10px'><input type='text' name='dt_ini' id='dt_ini' value='' class='box' size='10' maxlength='10' onkeypress=\"return Ajusta_Data(this,event);\"></td><td>&nbsp;";
//echo"<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png' onclick=\"abrirCalendario('dt_ini');return false;\">";
echo "      </td>\n";
echo "     </tr>\n";
echo "     <tr id='tp_3' style=\"display: none;\">\n";
echo "      <td valign='bottom'>Data Final: </td>\n";
echo "      <td width='10px'><input type='text' name='dt_fim' id='dt_fim' value='' class='box' size='10' maxlength='10' onkeypress=\"return Ajusta_Data(this,event);\"></td><td align='left'>&nbsp;";
//echo "<input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png' onclick=\"abrirCalendario('dt_fim');return false;\">";
echo "      </td>\n";
echo "     </tr>\n";
	echo "
		<tr>
			<td>
				Faixa Et&aacute;ria: 
			</td>
			<td colspan='2'>
				<select name=\"faixa_etaria\" id=\"faixa_etaria\" class=\"box\">
					<option value=\"-1\">Todas</option>
					<option value=\"0\">0 a 1 ano</option>
					<option value=\"1\">1 a 5 anos</option>
					<option value=\"5\">5 a 12 anos</option>
					<option value=\"12\">12 a 19 anos</option>
					<option value=\"19\">19 a 25 anos</option>
					<option value=\"25\">25 a 49 anos</option>
					<option value=\"49\">49 a 65 anos</option>
					<option value=\"65\">acima de 65 anos</option>
				</select>
			</td>
		</tr>";

/*echo "     <tr>\n";
echo "      <td valign='bottom'>Faixa Etária: </td>\n";
echo "      <td colspan='2'><input type='text' name='idade_i' id='idade_i' value='0' class='box' size='5' maxlength='3' onkeypress=\"return Ajusta_Data(this,event);\">&nbsp; a &nbsp;<input type='text' name='idade_f' id='idade_f' value='99' class='box' size='5' maxlength='3' onkeypress=\"return Ajusta_Data(this,event);\"></td>";
echo "     </tr>\n";*/

echo "     <tr>\n";
//echo "      <td>&nbsp;</td>";
echo "      <td colspan='2'> <input type=\"image\" src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg name='enviar' value='ENVIAR'> </td>\n";
echo "      <td><a href=\"../rel_index.php?id_login=$id_login&opcao=6#tabs-6\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif\" border=0></a></td>";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
