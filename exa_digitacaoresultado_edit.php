<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$common = new commonClass();
echo $common->incJquery();

cabecario( $hotkey = true);

reglog($id_login,"Acessando Digitacao do Resultado");

if(empty($acao)) {
//
//-> Botoes
  echo "<fieldset>
            <legend>Opções</legend>
	    <a href=exa_listapedidoexame.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	    <a href='#' OnClick='window.open(\"exa_listapedidoexame_printall.php?cad_exame=$cad_exame&proc_codigo=$row[proc_codigo]\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a>
       </fieldset>
      <br>";
$sql = pg_query("select TRANSLATE(proc.proc_nome, 'ZZZ-', '') as procnomenew,*from materialdeanalise as mlz left join itensdoexame as itx on itx.itx_codigo = mlz.itx_codigo left join procedimento as proc on proc.proc_codigo = itx.proc_codigo left join tipodeexame as tp on tp.proc_codigo = itx.proc_codigo where mlz.cad_exame = $cad_exame");
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	  <td>Cod.</td>
	  <td>Exame</td>
	  <td>Dt.Coleta</td>
	  <td>&nbsp;</td>
	</tr>";
while($row=pg_fetch_array($sql)) {
$res = pg_query("select *from resultadoexame where cad_exame = '$row[cad_exame]' and proc_codigo = '$row[proc_codigo]'");
$tp = pg_num_rows($res);
$arrRes = pg_fetch_array($res);
$dtc = explode("-",$row[mlz_datadacoleta]);
$datadacoleta = "$dtc[2]/$dtc[1]/$dtc[0]";
if(trim($row[mlz_coletado])=="S") {
 echo "<tr>
	  <td align=center>$row[proc_codigo]</td>
	  <td>$row[procnomenew]</td>
	  <td>$datadacoleta</td>";

	  echo"<td width=70% align=right colspan='2'>";
	  			echo $common->commonButton("EDITAR LAUDO","exa_digitacaoresultado_edit.php?acao=form_edit&cad_exame=$cad_exame&proc_codigo=$row[codigo]","laudo.png");
	  echo "</td>";
 
}
if(trim($row[mlz_coletado])=="N") {
 echo "<tr>
	  <td align=center>$row[proc_codigo]</td>
	  <td width=90%>$row[proc_nome]</td>
 	  <td colspan=2><font color=red><b>NAO COLETADO</b></font></td>";
}
 echo "</tr>";
}
 echo "</table>";



}	

if(($acao=="form_add" OR $acao=="form_edit")) {
$i=-1;
$sql = pg_query("select *from materialdeanalise as mlz left join itensdoexame as itx on itx.itx_codigo = mlz.itx_codigo left join procedimento as proc on proc.proc_codigo = itx.proc_codigo left join tipodeexame as tp on tp.proc_codigo = itx.proc_codigo where mlz.cad_exame = $cad_exame and proc.proc_codigo = $proc_codigo order by mlz_codigo");
echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Digitacao de Resultado</legend>";
while($row=pg_fetch_array($sql)) {
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	<td><b><font color=blue>$row[proc_nome]</font></b></td>
       </tr>
      </table>";
$query = pg_query("select *from subexame where txa_codigo = '$row[txa_codigo]'");
$qq = pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo ");

#
# NAO POSSUI SUBEXAME
#
if((pg_num_rows($query)=="0" AND pg_num_rows($qq)!="0")) {
echo "
<form method=post action=$PHP_SELF>
<input type=hidden name=acao value=edit>
<input type=hidden name=cad_exame value=$cad_exame>
     <table width='100%' align='center' cellspacing='2' cellpadding='5' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='20' style='height:25px;background-color:#c9c9c9'>Cód.</td>";
                  // if ($ite[ite_itemdoexame]!="") {
			echo "<td width='*' style='background-color:#c9c9c9'>Item</td>";
		  // }
                   echo "<td width='*' style='background-color:#c9c9c9'>Valor de Referencia</td>";
                   echo "<td width='*' style='background-color:#c9c9c9'>Resultado</td>
                  </tr>";
 echo "<input type=hidden name=proc_codigo value=$proc_codigo>";
 $qq = pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo");
   while($vlr=pg_fetch_array($qq)) {
	$ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
	 echo "<input type=hidden name=vlr_codigo[] value=$vlr[vlr_codigo]>";

	$sqlValoresResultado = "SELECT *
				  FROM resultadoexame
				 WHERE sex_codigo = $vlr[vlr_codigo] and cad_exame = $cad_exame";
	
	$queryValoresResultado = pg_query($sqlValoresResultado);
	$resultValoresResultado = pg_fetch_array($queryValoresResultado);
	$vOb2 = $resultValoresResultado[res_codigo];

	// isso me da  nojo mais sera um mal neceario  se eu usar o select de cima ele nao edita a concusao, e se eu tira uma das condicoes ele nao edita os valores
	$sqlObse = "SELECT *
                           FROM resultadoexame
                           WHERE cad_exame = $cad_exame";
        $queryObse = pg_query($sqlObse);
        $resultObse = pg_fetch_array($queryObse);
        $vOb80 = $resultObse[res_codigo];
	//echo $vOb3;
             echo "<tr bgcolor=FFFFFF>
                   <td width='20' align=center>$vlr[vlr_codigo]</td>";
                   if ($ite[ite_itemdoexame]!="") {
			echo "<td width='*' style='background-color:#c9c9c9'>$ite[ite_itemdoexame]</td>";

		   }
	if($vlr[vlr_valordereferencia]=="") {  
             echo "<td width='70%'>Sem Valores de Referencia </td>";
	} else {
               echo "<td width='70%'>$vlr[vlr_valordereferencia]</td>";
	}
                   echo "<td width='*'><input type=text name=vlr_valor[] class=box size=8 value=$resultValoresResultado[vlr_valor]></td>
                 </tr>";
}
	echo "</table>";
/*echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=8%>Observacao:</td>
	  <td><input type=text name=res_observacao[] value='$resultValoresResultado[res_observacao]'  class=box size=80></td>
	</tr>
	</table>";*/

}



#
# NAO POSSUI SUBEXAME NEM VALOR DE REFERENCIA
#
if((pg_num_rows($query)=="0" AND pg_num_rows($qq)=="0")) {
echo "
<form method=post action=$PHP_SELF>
<input type=hidden name=acao value=add>
<input type=hidden name=cad_exame value=$cad_exame>
     <table width='100%' align='center' cellspacing='2' cellpadding='5' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='20' style='height:25px;background-color:#c9c9c9'>Cód.</td>";
                   if ($ite[ite_itemdoexame]!="") {
			echo "<td width='*' style='background-color:#c9c9c9'>Item</td>";
		   }
                   echo "<td width='*' style='background-color:#c9c9c9'>Valor de Referencia</td>";
                   echo "<td width='*' style='background-color:#c9c9c9'>Resultado</td>
                  </tr>";
 echo "<input type=hidden name=vlr_codigo[] value=$vlr[vlr_codigo]>";
 echo "<input type=hidden name=proc_codigo value=$proc_codigo>";
             echo "<tr bgcolor=FFFFFF>
                   <td width='20' align=center>
$vlr[vlr_codigo]</td>";
                   if ($ite[ite_itemdoexame]!="") {
			echo "<td width='*' style='background-color:#c9c9c9'>Item</td>";
		   }
	if($vlr[vlr_valordereferencia]=="") {  
               echo "<td width='70%'>Sem Referencia</td>";
	} else {
               echo "<td width='70%'>$vlr[vlr_valordereferencia]</td>";
	}
                   echo "<td width='*'><input type=text name=vlr_valor[] class=box size=8></td>
                  </tr>";
	echo "</table>";
/*echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=8%>Observacao:</td>
	  <td><input type=text name=res_observacao[] class=box size=80></td>
	</tr>
	</table>";*/

}



$j=-1;
while($sub=pg_fetch_array($query)) {
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	<td><font color=red>".strtoupper($sub[sex_subexame])."</font></td>
       </tr>
      </table>
<form method=post action=$PHP_SELF>
<input type=hidden name=acao value=edit>
<input type=hidden name=cad_exame value=$cad_exame>
     <table width='100%' align='center' cellspacing='2' cellpadding='5' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='20' style='height:25px;background-color:#c9c9c9'>Cód.</td>
                   <td width='*' style='background-color:#c9c9c9'>Item do Exame</td>
                   <td width='*' style='background-color:#c9c9c9'>Valor de Referencia</td>
                   <td width='*' style='background-color:#c9c9c9'>Resultado</td>
                  </tr>";
$qq = pg_query("select *from valoresdereferencia where sex_codigo = '$sub[sex_codigo]' order by vlr_codigo");
 while($vlr=pg_fetch_array($qq)) {
$uq = pg_fetch_array(pg_query("select min(res_codigo) as v_min from resultadoexame where cad_exame = $cad_exame and proc_codigo = '$proc_codigo'"));
 $i++;
$valorTotal = ($uq[v_min]+$i);


$rr = pg_fetch_array(pg_query("select *from resultadoexame where res_codigo = $valorTotal"));

 echo "<input type=hidden name=vlr_codigo[] value=$vlr[vlr_codigo]>";
 echo "<input type=hidden name=proc_codigo value=$proc_codigo>";
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
             echo "<tr bgcolor=FFFFFF>
                   <td width='20' align=center>$vlr[vlr_codigo]</td>
                   <td width='30%'>$ite[ite_itemdoexame]</td>";
	if($vlr[vlr_valordereferencia]=="") {  
	} else {
               echo "<td width='70%'>$vlr[vlr_valordereferencia]</td>";
	}
               echo "<td width='*'><input type=text name=vlr_valor[] class=box size=8 value=$rr[vlr_valor]></td>
                  </tr>";
 }

	echo "</table>";
$j++;
$vOb1 = ($uq[v_min]+$j);
 $resb01 = pg_fetch_array(pg_query("select *from resultadoexame where res_codigo = $vOb1"));

/*echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=8%>Observacao:</td>
	  <td><input type=text name=res_observacao[] class=box size=80 value='$resb01[res_observacao]'>
	  	</td>
	</tr>
	</table>
";*/

}
if($resultValoresResultado[res_codigo] != null){
	if($vOb80 != ""){
		$vOb2 = $vOb80;
	}else{
		$vOb2 = $resultValoresResultado[res_codigo];
	}
}else{
	if($vOb2 == "" ){
		$vOb2 = ($uq[v_min]);
	}
}
 $resb02 = pg_fetch_array(pg_query("select *from resultadoexame where res_codigo = $vOb2"));
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=8%>Observa&ccedil;&atilde;o:</td>
	</tr>
	<tr>
	  <td><textarea name=res_conclusoes[] class=box cols=93 rows=5>$resb02[res_conclusoes]</textarea></td>
	</tr>
	<tr>
	 <td>&nbsp;</td>
	</tr>
	<tr>
	  <td align=center>";
		$pegaUsu = "select * from cadastrodoexame where cad_exame = $cad_exame ";
		$queryUsu = pg_query($pegaUsu);
		$linhaUsu = pg_fetch_array($queryUsu);
		$usu_codigo = $linhaUsu['usu_codigo'];
//		echo "exa_digitacaoresultado_edit.php?&acao=edit&cad_exame=$cad_exame&vlr_codigo=$vOb2&id_login=$id_login";
		echo"
	  	<a href=exa_digitacaoresultado.php?cad_exame=$cad_exame&id_login=$id_login&usu_codigo=$usu_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;
		<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg>";
		
echo "
	  </td>
	</tr>
	</table><br>
	</form>";
}
	echo "</fieldset>
	   </td>
	  </tr>
	 </table>"; 
}

if($acao == "edit"){
	for($i=0; $i <= (count($vlr_valor)-1); $i++) {

		$verObs = "SELECT * from resultadoexame where sex_codigo = $vlr_codigo[$i] and res_observacao = UPPER('$res_observacao[$i]')";
		$qryObs = pg_query($verObs);
		$linObs = pg_fetch_array($verObs);
		// ele verifica se na linha que ira dar update contem o campo observação e o altera.
		if($linObs['res_obervacao'] != "$res_observacao[$i]"){
			$alteraObs = "UPDATE resultadoexame set res_observacao = UPPER('$res_observacao[$i]') where cad_exame = $cad_exame and sex_codigo = $vlr_codigo[$i]";
			$qryAltObs = pg_query($alteraObs);
		}
	
		//$verConc = "SELECT * from resultadoexame where sex_codigo = $vlr_codigo[$i] and res_conclusoes = UPPER('$res_conclusoes[$i]')";
		$verConc = "SELECT * from resultadoexame where sex_codigo = $vlr_codigo[$i])";
		$qryConc = pg_query($verConc);
		$linConc = pg_fetch_array($verConc);
//		if($linConc['red_conclusoes'] != "$res_observacao[$i]"){
			$alteraConc = "UPDATE resultadoexame set res_conclusoes = UPPER('$res_conclusoes[$i]') where cad_exame = $cad_exame and sex_codigo = $vlr_codigo[$i]";
			$qryAltConc = pg_query($alteraConc);
//		}
		  $stmt = "update resultadoexame SET 
					     res_dataresultado = CURRENT_DATE,
					     res_horaresultado = CURRENT_TIME,
					     res_observacao = '".strtoupper($res_observacao[$i])."',
					     vlr_valor = '$vlr_valor[$i]'
				   where cad_exame = $cad_exame and sex_codigo = $vlr_codigo[$i]";
		 $sql = pg_query($stmt);
 	}
		$pegaUsu = "select * from cadastrodoexame where cad_exame = $cad_exame ";
		$queryUsu = pg_query($pegaUsu);
		$linhaUsu = pg_fetch_array($queryUsu);
		$usu_codigo = $linhaUsu['usu_codigo'];
       echo "<script>
       		alert('Editado com Sucesso!');
               window.location = \"exa_digitacaoresultado.php?proc_codigo=$proc_codigo&usu_codigo=$usu_codigo&cad_exame=$cad_exame&id_login=$id_login \";  
              </script>";
}


?>
