<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src=script.js></script>
<script>

var gdtInicial
var gdtFinal
var gCE
var gSetor
var gGrupo
var gConsVal
var gCurvA
var gCurvB
var gCurvC
var gHoje
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);

function CompData(data1 , data2) {

//   Se ( data1 )  MAIOR QUE   ( data2 )    FALSE    //

   var d1 = data1
   var d2 = data2

   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           if ( parseInt( d1.split( "-" )[2].toString() + d1.split( "-" )[1].toString() + d1.split( "-" )[0].toString() )
                >
                parseInt( d2.split( "-" )[2].toString() + d2.split( "-" )[1].toString() + d2.split( "-" )[0].toString() ) )
              { return false }   else    { return true  }
        } else
        if (d1.charAt(i) == "/") {
           if ( parseInt( d1.split( "/" )[2].toString() + d1.split( "/" )[1].toString() + d1.split( "/" )[0].toString() )
                >
                parseInt( d2.split( "/" )[2].toString() + d2.split( "/" )[1].toString() + d2.split( "/" )[0].toString() ) )
              { return false }   else   { return true  }
        }
   }
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

   for (i=0;i<document.frm_CurABCConsumo.ConVal.length;i++) {
        if (document.frm_CurABCConsumo.ConVal[i].checked == true) { gConsVal=i }
   }
   gdtFinal   = document.frm_CurABCConsumo.dt_final.value
   gCE        = document.frm_CurABCConsumo.CE_codigo.value
   gGrupo     = document.frm_CurABCConsumo.gru_codigo.value
   gCurvA     = document.frm_CurABCConsumo.Curv_A.value
   gCurvB     = document.frm_CurABCConsumo.Curv_B.value
   gCurvC     = document.frm_CurABCConsumo.Curv_C.value

   if (gdtFinal == '') {
       alert ("Informe Data final");
       document.frm_CurABCConsumo.dt_final.focus();
       return false;
   }
   var d1=gdtFinal
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
   //   if (CheckDate(dat1,"INICIAL")==1) {
   //    document.frm_CurABCConsumo.dt_inicial.focus()
   //    return false
   //}
   if (CheckDate(dat2,"FINAL")==1) {
       document.frm_CurABCConsumo.dt_final.focus()
       return false
   }
   //if  (dat1 > dat2) {
   //     alert("Data Inicial(" + gdtInicial + ") maior que Final(" + gdtFinal + ")")
   //     document.frm_CurABCConsumo.dt_inicial.focus()
   //     return false
   //}

   if (gCurvA=='') { gCurvA=0 } else if (gCurvB=='') { gCurvB=0 } else if (gCurvC=='') { gCurvC=0 }
   if (gCurvA==0 && gCurvB==0 && gCurvC==0) {
       alert("Algum PERCENTUAL tem que ser informado")
       document.frm_CurABCConsumo.Curv_A.focus()
       return false
   }

   window.open('CurvaABCEstoque.php?dt_final=' + gdtFinal+
					               '&CE_codigo=' + gCE        +
					              '&gru_codigo=' + gGrupo     +
					                 '&ConsVal=' + gConsVal   +
					                   '&CurvA=' + gCurvA     +
					                   '&CurvB=' + gCurvB     +
					                   '&CurvC=' + gCurvC
					              ,null
		                          ,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

   return true
}

function AtualizProduto(p){

    url = 'ComboAtualizaProduto.php?valor='+p;
    IdentBrowser(url,2);
}

</script>

<?

//$number = 1234.56;
//setlocale(LC_ALL, 'pt_BR');
//echo money_format("%=*(#10.2n", $number);
//exit();

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo " <form name='frm_CurABCConsumo' method='post' action='$PHP_SELF'>\n";
echo "  <fieldset>";
echo "   <legend>Curva ABC de Estoque</legend> \n";
echo "    <table whidht=100% border=0 cellspacing=2 cellpadding=1>\n";
//echo "     <tr>\n";
//echo "      <td valign='bottom'>Data Inicial</td>\n";
//echo "      <td><input class='box' type='text' name='dt_inicial' size='12' value='$dt_inicial' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
//echo "     </tr>\n";

echo"<input type=hidden name=ConVal value=0>";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final</td>\n";
echo "      <td><input class='box' type='text' name='dt_final'   size='12' value='$dt_final'   maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Centro Estocador</td>\n";
echo "      <td><select name='CE_codigo' value='$CE_codigo' class=box >\n";
					/*$query=pg_query("SELECT set_codigo, set_nome FROM setor WHERE set_estoque='S' ORDER BY set_nome");*/
					$select = "select uni_codigo from usuarios where usr_codigo = $id_login";
					$uni = db_get($select);
					if($uni != "")
					{
					  $and_sql = " AND uni_codigo = $uni ";
					}
					$sql = "SELECT s.set_codigo, 
								   set_nome 
							  FROM Setor s
							  JOIN usuarios_setores us
								on us.set_codigo=s.set_codigo
							WHERE set_estoque = 'S'
							  AND usr_codigo = ".$_SESSION[id_login]."
							$and_sql
							ORDER BY set_nome";
					$query = db_query($sql);
					while($CentroEstoq=pg_fetch_array($query)) {
						  echo ($CentroEstoq==$CentroEstoq[set_codigo])?
						        "<option value='$CentroEstoq[set_codigo]' selected> $CentroEstoq[set_nome]</option>" :
								"<option value='$CentroEstoq[set_codigo]'         > $CentroEstoq[set_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Grupo Produto</td>\n";
echo "      <td><select name='gru_codigo' value='$gru_codigo' class=box onChange='javascript:AtualizProduto(this.value);'>\n";
echo "           <option value=''> --- Todos Grupos --- </option>\n";
		            $query=pg_query("SELECT gru_codigo, gru_nome FROM grupo ORDER BY gru_nome");
		            while($Grupo=pg_fetch_array($query)) {
		                  echo ($gru_codigo==$Grupo[gru_codigo])?
                                "<option value='$Grupo[gru_codigo]' selected> $Grupo[gru_nome]</option>" :
                                "<option value='$Grupo[gru_codigo]'         > $Grupo[gru_nome]</option>\n";
		            }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td colspan=2>&nbsp;</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td class='tdtitle'>&nbsp;&nbsp;&nbsp;Informe Percentuais:&nbsp;&nbsp;&nbsp;</td>\n";
//echo "      <td>\n";
//echo "        <input type=radio name=ConVal value=0 checked>Consumo \n";
//echo "        <input type=radio name=ConVal value=1        >Valor \n";
//echo "      </td>\n";
echo "     </tr>\n";
$ConVal = 1;

echo "     <tr>\n";
echo "      <td colspan=2>&nbsp;</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;&nbsp;Curva A  <input class='box' type='text' name='Curv_A' size='3' value='$Curv_A'/> %</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;&nbsp;Curva B  <input class='box' type='text' name='Curv_B' size='3' value='$Curv_B'/> %</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;&nbsp;Curva C  <input class='box' type='text' name='Curv_C' size='3' value='$Curv_C'/> %</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td colspan=2>&nbsp;</td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
echo "      <td><img  src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg\" OnClick=\"CheckCall()\"  name='enviar' value='ENVIAR' style='cursor:pointer;'> </td>\n";
echo "      <td align='right'><a href='../rel_index.php?opcao=7'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border=0></a>
			</td>\n";
echo "     </tr>\n";

echo "   </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";

