<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="cidades.js"></script>
<script type="text/javascript" src="../json.js"></script>
<script type="text/javascript">
var gUnidade
var gCidade
var gData_i
var gData_f
var gMes
var gAno

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
	
	
	gUnidade	=document.frm_AgPorAgente.uni_codigo.value
	gCidade		=$F('cid_codigo');
	gData_i		=document.frm_AgPorAgente.dt_ini.value
	gData_f		=document.frm_AgPorAgente.dt_fim.value
	gMes		=document.frm_AgPorAgente.mes.value
	gAno		=document.frm_AgPorAgente.ano.value
	proc_codigo = document.frm_AgPorAgente.proc_codigo.value;
//	if(document.frm_AgPorAgente.filtro.value == ""){
//		alert("Preencha o filtro");
//		document.frm_AgPorAgente.filtro.focus();
//		return false
//
//	}
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
	if( ( gData_i != '' && gData_f == '' ) || ( gData_i == '' && gData_f != '' ) )
	{
		alert ("Entre com as duas datas");
		return false;
	}

  window.open('apac_qtd_procedimento_list.php?uni_codigo='+gUnidade+'&pac_codigo='+gCidade+'&dt_inicial='+gData_i+'&dt_final='+gData_f+'&mes='+gMes+'&ano='+gAno+'&proc_codigo='+proc_codigo,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
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

function inv_data($dat) {
   $d=explode("-",$dat);
   $dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
   return "$dat";
 }
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
	echo monta_calendario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_AgPorAgente\" method=\"post\" action=\"$PHP_SELF?id_login=$id_login\" onSubmit=\"CheckCall()\">\n";
echo "  <fieldset>";
echo "   <legend>APAC - Quantidade de procedimentos por Prestador por Município</legend> \n";
echo "    <table width=80% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Procedimento: </td>\n";
echo "      <td colspan='2'>";
		echo "<select name='proc_codigo' id='proc_codigo' class='box'>\n";
			echo "<option value=''> ---- TODOS ---- </option>\n";
		          $query = db_query("(select proc_codigo, proc_nome from
									apac_procedimento_cad)
									union all
									(SELECT proc_codigo, proc_nome
									 FROM procedimento ORDER BY proc_nome)
									 order by 2", false);
		          while($row = pg_fetch_array($query))
		          {
		               echo ($proc_codigo == $row[proc_codigo])?
		               "<option value='$row[proc_codigo]' selected> ".$row[proc_nome]."</option>":
		               "<option value='$row[proc_codigo]' > ".$row[proc_nome]."</option>\n";
		          }
echo "          </select>\n";
echo "      </td>";

echo "     <tr>\n";
echo "      <td valign='bottom' width='100px'>Prestador: </td>\n";
echo "      <td colspan='2'><select name='uni_codigo' class=box>\n";
echo "           <option value='' selected> ---- TODOS ---- </option>\n";
                         //$query=pg_query("SELECT * FROM medico ORDER BY med_nome ASC");
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
                              echo "<option value='$gUnidade[med_codigo]' > ".$gUnidade[med_nome]."</option>\n";
                         }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";?>
        <td width="100">Munic&iacute;pio</td>
		<td>
			<select name="estado" id="estado" class="box" onchange="atualiza_cidade(this,'cid_codigo','cid_nome')">
				<option value="0">..</option>
				<?php
					$sql = db_query("SELECT DISTINCT uf_sigla FROM cidade ORDER BY 1");
					while ( $uf = pg_fetch_array($sql) )
					{
						echo "\n\t\t\t<option>{$uf[0]}</option>";
					}
				?>
			</select>
			<select name="cid_codigo" id="cid_codigo" class="box" style="width:150px;">
				<option value="">...Todos...</option>
			</select>
		</td><?
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Filtrar por: </td>\n";
echo "      <td colspan='2'>Competencia <input type='radio' name='filtro' id='filtro' value=''  onchange=\"muda('tp_1')\">";
echo "          &nbsp;Período <input type='radio' name='filtro' id='filtro' value='' onchange=\"muda('tp_2', 'tp_3')\">\n";
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
//echo "          &nbsp;<input type='text' name='ano' id='ano' value='".date("Y")."' class='box' size='4' maxlength='4'>\n";
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

echo "     <tr>\n";
//echo "      <td>&nbsp;</td>";
echo "      <td colspan='2'> <input type=\"image\" src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td><a href=\"../rel_index.php?id_login=$id_login&opcao=6#tabs-6\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif\" border=0></a></td>";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
