<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script src="../ajax_motor.js"></script>
<script src=script.js></script>
<script>
	var gArmazem;
	var gGrupo;
	var gData_i;
	var gData_f;
	var gProduto;
	
	var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);
	
	function CheckDate(d,t) {
	   date_array = new Array(3);
	   date_array[0]=(String(d).substr(6,2));    // dia
	   date_array[1]=(String(d).substr(4,2));    // mes
	   date_array[2]=(String(d).substr(0,4));    // ano
		
	   if (date_array[0] > maxDay[date_array[1]-1]) {
	       alert ("Dia invalido da data " + t);
	       return 1;
	   }
	   if (date_array[1] > 12) {
	       alert ("Mes invalido da data " + t);
	       return 1;
	   }
	   if (date_array[2] < 1999) {
	       alert ("Ano invalido da data " + t);
	       return 1;
	   }
	}
	
	function CheckCall()
	{
		gArmazem	= document.form_consumo_semestral.set_codigo.value;
		gGrupo		= document.form_consumo_semestral.gru_codigo.value;
		gData_i		= document.form_consumo_semestral.dt_inicial.value;
		gData_f		= document.form_consumo_semestral.dt_final.value;
		gProduto	= document.form_consumo_semestral.pro_codigo.value;
		gTipo		= document.form_consumo_semestral.tipomovim.value;
		
		var d1=gData_i;
		var d2=gData_f;
		for (var i = 0; i < d1.length; i++)
		{
			if (d1.charAt(i) == "-") 
			{
				var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString());
			}
			else 
				if (d1.charAt(i) == "/") 
				{
					var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString());
				}
		}
		for (var i = 0; i < d2.length; i++) 
		{
			if (d2.charAt(i) == "-") 
			{
				var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString());
			}
			else 
				if (d2.charAt(i) == "/") 
				{
					var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString());
				}
		}
		if (CheckDate(dat1,"INICIAL")==1)
		{
			document.form_consumo_semestral.dt_inicial.focus();
			return false;
		}
		if (CheckDate(dat2,"FINAL")==1) 
		{
			document.form_consumo_semestral.dt_final.focus();
			return false
		}
	
		date_array_i = new Array(2);
		date_array_i[0]=(String(gData_i).substr(3,2));    // mes
		date_array_i[1]=(String(gData_i).substr(6,4));    // ano
		
		date_array_f = new Array(2);
		date_array_f[0]=(String(gData_f).substr(3,2));    // mes
		date_array_f[1]=(String(gData_f).substr(6,4));    // ano
		
		if( date_array_f[1] == date_array_i[1] )
		{
			var meses = new Number(date_array_f[0]);
			meses -= new Number(date_array_i[0]);
			if( meses > 5 )
			{
				alert("Utilize um intervalo de data inferior a 6 meses");
				return false;
			}
		}
		else
		{
			var meses = new Number(date_array_f[0]);
			meses += 12;
			meses -= new Number(date_array_i[0]);
			if(  meses > 5 )
			{
				alert("Utilize um intervalo de data inferior a 6 meses");
				return false;
			}
		}
		window.open('ConsumoSemestral_list_transferencia.php?set_codigo='+gArmazem+'&gru_codigo='+gGrupo+'&dt_final='+gData_f+'&dt_inicial='+gData_i+'&pro_codigo='+gProduto+'&meses='+meses+'&tipomovim='+gTipo,
		null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes");
	}
	
	function AtualizProduto(p){
	   
	    url = 'ComboAtualizaProduto.php?valor='+p;
	    IdentBrowser(url,2);
	}
</script>

<?php

function inv_data($dat) {
	$d=explode("-",$dat);
	$dat=$d[2]."-".$d[1]."-".$d[0]."<br>";
	return "$dat";
}

//------------------------------------------------------------------>
// -> Includes
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
	echo monta_calendario();
    
//echo "<body>
//     <link href='../estilo.css' rel='stylesheet' type='text/css'>";

//----------------  Monta Dados Recebidos  ---------------->

$titulo="Consumo Mensal de Produtos";    //       NOME DO RELATÓRIO

//echo "Data INICIAL->".$dt_inicial."<br>";
//echo "Data FINAL  ->".$dt_final."<br>";
//echo "Hora INICIAL->".$hr_inicial."<br>";
//echo "Hora FINAL  ->".$hr_final."<br>";

$hr_inicial = '00:00';
$hr_final = '23:59';

$dias = array("31", "28", "31", "30", "31", "30","31", "31", "30", "31", "30", "31");

$mes = pg_fetch_array(pg_query("select extract (month from date(now()))"));
$ano = pg_fetch_array(pg_query("select extract (year from date(now()))"));

if ($mes[0] < 10) {
	$mes1 = '0'.$mes[0];
}
else {
	$mes1 = $mes[0];
}

if (!$dt_inicial) {
	if ($mes1 <= 5)
	{
		$mes_aux = 12+($mes1-5);
		if ($mes_aux < 10)
		{
			$mes_aux = '0'.$mes_aux;
		}
		$dt_inicial = '01/'.$mes_aux.'/'.($ano[0]-1);
	}
	else
	{
		$mes_aux = $mes1-5;
		if ($mes_aux < 10)
		{
			$mes_aux = '0'.$mes_aux;
		}
		$dt_inicial = '01/'.$mes_aux.'/'.$ano[0];
	}
}

if (!$dt_final) {
    $diafinal = $dias[$mes[0]-1];
    $dt_final = $diafinal.'/'.$mes1.'/'.$ano[0];
}

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo "     
  <form method=\"post\" action=\"$PHP_SELF?id_login=$id_login\" name=\"form_consumo_semestral\">
	<input type=hidden name=user value=$user>

	<fieldset>
	    <legend>Dados do Relat&oacute;rio</legend>
	<table width=98% align=center cellspacing=2 cellpadding=0 border=0>
    <tr>
               <td width=70>Local de Armazenagem:</td>
               <td>
                   <select name=set_codigo class=boxr>";
                   /*$sql = pg_query("select * from setor where set_estoque = 'S' order by set_nome");*/
					$select = "select uni_codigo from usuarios where usr_codigo = $id_login";
					$uni = db_get($select);
					if($uni != "")
					{
					  $and_sql = " AND uni_codigo = $uni ";
					}
					$sql = "SELECT set_codigo, set_nome FROM Setor
							WHERE set_estoque = 'S'
							$and_sql
							ORDER BY set_nome";
					$sql = db_query($sql);
                   while($uni=pg_fetch_array($sql)) {
				   		if($_REQUEST['set_codigo'] == $uni['set_codigo']){
                        	echo "<option value='$uni[set_codigo]' selected> $uni[set_nome]</option>";
						}else{
                        	echo "<option value='$uni[set_codigo]'> $uni[set_nome]</option>";
						
						}
                   }
             echo "</select>
              </td>
        </tr>";
echo "     <tr>\n";
echo "      <td valign='bottom'>Grupo Produto</td>\n";
echo "      <td><select name='gru_codigo' value='$gru_codigo' class=box onChange='javascript:AtualizProduto(this.value);'>\n";
echo "           <option value=''> --- Todos os Grupos --- </option>\n";
		            $query=pg_query("SELECT gru_codigo, gru_nome FROM grupo ORDER BY gru_nome");
		            while($Grupo=pg_fetch_array($query)) {
		                  echo ($gru_codigo==$Grupo[gru_codigo])?
                                "<option value='$Grupo[gru_codigo]' selected> $Grupo[gru_nome]</option>" :
                                "<option value='$Grupo[gru_codigo]'         > $Grupo[gru_nome]</option>\n";
		            }
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>

         <tr>\n";
echo "      <td valign='bottom'>Produto</td>\n";
echo "      <td><div id='select_prod'><select name='pro_codigo' value='$pro_codigo' class=box>\n";
echo "           <option value=''> --- Todos os Produtos --- </option>\n";
		            $query=pg_query("SELECT pro_codigo, pro_nome FROM produto ORDER BY pro_nome");
                    while($Produto=pg_fetch_array($query)) {
	                     echo ($pro_codigo==$Produto[pro_codigo])?
                              "<option value='$Produto[pro_codigo]' selected>".substr($Produto[pro_nome],0,60)."</option>" :
                              "<option value='$Produto[pro_codigo]'         >".substr($Produto[pro_nome],0,60)."</option>\n";
		            }
echo "          </select>\n";
echo "      </td> </div> \n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Tipo de Consumo</td>\n";
echo "      <td><select name='tipomovim' value='$tipomovim'' class=box '>\n";
echo "           <option value='' selected> --- Todos Tipos de Consumo --- </option>\n";
echo "		 	 <option value='A'>Ajuste</option>";
echo "		 	 <option value='M'>Empr&eacute;stimo</option>";
echo "	 		 <option value='I'>Invent&aacute;rio</option>";
echo "	 	 	 <option value='R'>Perdas</option>";
echo "		 	 <option value='P'>Permuta</option>";
echo "		 	 <option value='S'>Sa&iacute;da de Consumo</option>";
echo "	 		 <option value='T'>Transfer&ecirc;ncia</option>";
echo "	 	 	 <option value='O'>Outras Sa&iacute;das</option>";
echo "           <option value='ST'>Sa&iacute;da de Consumo + Transfer&ecirc;ncia</option>";
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo	"     <tr>
		    <td width=110>Data Inicial:</td>
		    <td>
            
            <table cellspacing=0 cellpadding=0 border=0>
            <tr>
                <td width=10><input type=text name=dt_inicial id=dt_inicial class=box size=13 value='$dt_inicial' onKeypress=\"return Ajusta_Data(this, event);\"></td>
            </tr>
            </table>
            
			</td>
	     </tr>
	     
	     <tr>
		    <td width=110>Data Final:</td>
		    <td>
            
            <table cellspacing=0 cellpadding=0 border=0>
            <tr>
                <td width=10><input type=text name=dt_final id=dt_final class=box size=13 value='$dt_final' onKeypress=\"return Ajusta_Data(this, event);\"></td>
            </tr>
            </table>
            
			</td>
	     </tr>";

echo "      <tr> <td> &nbsp;&nbsp;</td>
              <td > <input type='image'  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "      <td > <a href='../rel_index.php?id_login=$id_login&opcao=7#tabs-3'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif' border =0> </a></td></tr>\n";

echo "	
        </table>
        </fieldset>
        </form>";
?>
