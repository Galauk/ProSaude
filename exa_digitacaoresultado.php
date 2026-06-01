<?
session_start();
echo "<pre>".print_r($_REQUEST,1);
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

cabecario( $hotkey = true);
$common = new commonClass();
echo $common->incJquery();

reglog($id_login,"Acessando Digitacao do Resultado");


if(empty($acao)) {
//
//-> 

  echo "<fieldset>
            <legend>Opções</legend>
	    <a href=exa_listapedidoexame.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	    <a href='#' OnClick='window.open(\"exa_listapedidoexame_printall.php?cad_exame=$cad_exame&proc_codigo=$row[proc_codigo]\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a>
       </fieldset>
      <br>";
	$sql = "select TRANSLATE(proc.proc_nome, 'ZZZ-', '') as procnomenew,
							proc.proc_codigo as codigo,
							*
					   from materialdeanalise as mlz 
				  left join itensdoexame as itx 
				         on itx.itx_codigo = mlz.itx_codigo 
			      left join procedimento as proc 
				         on proc.proc_codigo = itx.proc_codigo 
				  left join tipodeexame as tp 
				         on tp.proc_codigo = itx.proc_codigo 
				  left join cadastrodoexame as cadex
				         on cadex.cad_exame = mlz.cad_exame
					  where mlz.cad_exame = $cad_exame 
					    and itx.itx_status = 'C' 
						and cadex.usu_codigo = $usu_codigo";
	$exec = pg_query($sql);
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	  <td>Cod.</td>
	  <td>Exame</td>
	  <td>Dt.Coleta</td>
	  <td colspan='2'>&nbsp;</td>
	</tr>";
	if(pg_num_rows($exec) == 0){
		echo "<tr>
				<td colspan='4'>
					Por favor realize a coleta do Exame para emitir um laudo!
				</td>
			  </tr>";
	}
while($row=pg_fetch_array($exec)) {
$res = "select *from resultadoexame where cad_exame = '$row[cad_exame]' and proc_codigo = '$row[codigo]'";

$queryRes = pg_query($res);
$arrRes = pg_fetch_array($queryRes);
$teste = pg_num_rows($queryRes);
$dtc = explode("-",$row[mlz_datadacoleta]);
$datadacoleta = "$dtc[2]/$dtc[1]/$dtc[0]";
if(trim($row[mlz_coletado])=="S") {
 echo "<tr>
	  <td align=center>$row[proc_codigo]</td>
	  <td>$row[procnomenew]</td>
	  <td>$datadacoleta</td>";
 if(pg_num_rows($queryRes)<=0) {
	  echo"<td align='right'>";
	  	$pegaUsu = "select * from cadastrodoexame where cad_exame = $cad_exame ";
		$queryUsu = pg_query($pegaUsu);
		$linhaUsu = pg_fetch_array($queryUsu);
		$usu_codigo = $linhaUsu['usu_codigo'];
	   //echo $common->commonButton("DIGITAR LAUDO","$PHP_SELF?acao=form_add&cad_exame=$cad_exame&usu_codigo=$usu_codigo&proc_codigo=$row[codigo]","laudoAdd.png");
	   echo $common->commonButton("DIGITAR LAUDO",$_SESSION[linkroot].$_SESSION[modulo]."laudos/laudo2.php?acao=form_add&cad_exame=$cad_exame&usu_codigo=$usu_codigo&proc_codigo=$row[codigo]&itx_codigo=$row[itx_codigo]&id_login=$id_login","laudoAdd.png");
	   echo "</td>";
 } else {
	  echo"<td width=70% align=right colspan='2'>";
	  			echo $common->commonButton("EDITAR LAUDO",$_SESSION[linkroot].$_SESSION[modulo]."laudos/laudo2.php?acao=form_add&cad_exame=$cad_exame&proc_codigo=$row[codigo]&itx_codigo=$row[itx_codigo]&id_login=$id_login&usu_codigo=$usu_codigo","laudo.png");
	  echo "</td>";
 }
}



if(trim($row[mlz_coletado])=="N") {
 echo "<tr>
	  <td align=center>$row[proc_codigo]</td>
	  <td width=90%>$row[proc_nome]</td>
 	  <td colspan=3><font color=red><b>NAO COLETADO</b></font></td>";
}
 echo "</tr>";
}
 echo "</table>";



}	

if(($acao=="form_add" OR $acao=="form_edit")) {
$i=-1;
$sql = "select * from materialdeanalise as mlz 
			left join itensdoexame as itx 
			  on itx.itx_codigo = mlz.itx_codigo 
	   left join procedimento as proc 
	          on proc.proc_codigo = itx.proc_codigo 
	   left join tipodeexame as tp 
	          on tp.proc_codigo = itx.proc_codigo 
		   where mlz.cad_exame = $cad_exame 
		     and proc.proc_codigo = $proc_codigo 
		order by mlz_codigo";
$query = pg_query($sql);
echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Digitacao de Resultado</legend>";
while($row=pg_fetch_array($query)) {
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	<td><b><font color=blue>$row[proc_nome]</font></b></td>
       </tr>
      </table>";
$select = "select *from subexame where txa_codigo = '$row[txa_codigo]'";
$query = pg_query($select);
$selectvalor = "select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo ";
$qq = pg_query($selectvalor);
$vr = pg_fetch_array($qq);
$teste = pg_num_rows($qq);
$ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vr[ite_codigo]'"));


if((pg_num_rows($query)=="0" && pg_num_rows($qq)!="0")) {
//----
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
 echo "<input type=hidden name=proc_codigo value=$proc_codigo>";

 $qq = pg_query("select *from valoresdereferencia where txa_codigo ='$row[txa_codigo]' order by vlr_codigo");

   while($vlr=pg_fetch_array($qq)) {
	echo "<input type=hidden name=vlr_codigo[] value='$vlr[vlr_codigo]'>";

	$ite2 = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));

             echo "$vlr[sex_subexame]<br><tr bgcolor=FFFFFF>
                   <td width='20' align=center>$vlr[vlr_codigo]</td>";
                   if ($ite[ite_itemdoexame]!="") {
			echo "<td width='*' style='background-color:#c9c9c9'>$ite2[ite_itemdoexame]</td>";
		   }
	if($vlr[vlr_valordereferencia]=="") {  
               echo "<td width='70%'>Sem Valores de Referencia</td>";
	} else {
               echo "<td width='70%'>$vlr[vlr_valordereferencia]</td>";
	}
                   echo "<td width='*'><input type=text name=vlr_valor[] class=box size=8></td>
                  </tr>";
}
	echo "</table>";
/*echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=8%>Observacao:</td>
	  <td><input type=text name=res_observacao[] class=box size=80></td>
	</tr>
	</table>";*/
}

/*
 * NAO POSSUI SUBEXAME NEM VALOR DE REFERENCIA
 */
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
          echo "<tr bgcolor=FFFFFF>";
                   if ($ite[ite_itemdoexame]!="") {
					 echo "<td width='*' style='background-color:#c9c9c9'>Item</td>";
		  		   }
					if($vlr[vlr_valordereferencia]=="") {  
				   } else {
               			echo "<td width='70%'>$vlr[vlr_valordereferencia]</td>";
					}
                   echo "
                </tr>";
echo "</table>";
/*echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr>
			  <td width=8%>Observacao:</td>
			  <td><input type=text name=res_observacao[] class=box size=80></td>
			</tr>
	</table>";*/
}
while($sub=pg_fetch_array($query)) {
echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
       <tr bgcolor=FFFFFF>
	<td><font color=red>".strtoupper($sub[sex_subexame])."</font></td>
       </tr>
      </table>
<form method=post action=$PHP_SELF>
<input type=hidden name=acao value=add>
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
 $i++;
 echo "<input type=hidden name=vlr_codigo[] value=$vlr[vlr_codigo]>";
 echo "<input type=hidden name=proc_codigo value=$proc_codigo>";
 $ite = pg_fetch_array(pg_query("select *from itensanalise where ite_codigo = '$vlr[ite_codigo]'"));
#aqui
 echo "<tr bgcolor=FFFFFF>
                   <td width='20' align=center>$vlr[vlr_codigo]</td>
                   <td width='30%'>$ite[ite_itemdoexame]</td>";
	if($vlr[vlr_valordereferencia]=="") {  

	} else {
               echo "<td width='70%'>$vlr[vlr_valordereferencia]</td>";
	}
               echo "<td width='*'><input type=text name=vlr_valor[] class=box size=8></td>
                  </tr>";
 }
	echo "</table>";
/*echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=8%>Observacao:</td>
	  <td><input type=text name=res_observacao[] class=box size=80></td>
	</tr>
	</table>";*/
}
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
	  <td width=10%>Observa&ccedil;&atilde;o:</td>
	</tr>
	<tr>
	  <td><textarea name=res_conclusoes[] class=box cols=93 rows=5></textarea></td>
	</tr>
	<tr>
	 <td>&nbsp;</td>
	</tr>
	<tr>";
		$pegaUsu = "select * from cadastrodoexame where cad_exame = $cad_exame ";
		$queryUsu = pg_query($pegaUsu);
		$linhaUsu = pg_fetch_array($queryUsu);
		$usu_codigo = $linhaUsu['usu_codigo'];
	echo"
	  <td align=center><a href=exa_digitacaoresultado.php?cad_exame=$cad_exame&usu_codigo=$usu_codigo&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	</tr>
	</table><br></form>";
}
	echo "</fieldset>
	   </td>
	  </tr>
	 </table>";
}


if($acao=="add") {
	echo $ite_codigo."teste";
	# ESSA REGRA QUE SE APLICA ENTRE COMENTARIOS, VALE APENAS PARA O HEMOGRAMA.
	$sqlProcedimento = "SELECT *
						  FROM procedimento
						 WHERE proc_codigo = $proc_codigo
						   AND proc_nome ilike '%HEMOGRAMA COMPLETO%'";
	$queryProcedimento = pg_query($sqlProcedimento);
	$regProcedimento = pg_num_rows($queryProcedimento);
	####
	
	if($regProcedimento == 1){
		include_once $_SESSION[root].$_SESSION[comum]."class/calculosHemogramaClass.php";
		$hemograma = new Hemograma();
		//echo $hemograma->calculoVcm("20", "40");
	}
	//echo "<br/>  aokaokako";
	//exit();
	for($i=0; $i <= (count($vlr_valor)-1); $i++) {
		echo $vlr_codigo[$i]."<br/>";
		$stmt ="INSERT INTO resultadoexame ( 
							sex_codigo, 
							id_login, 
							res_dataresultado, 
							res_horaresultado, 
							res_observacao, 
							res_conclusoes, 
							vlr_valor, 
							cad_exame, 
							proc_codigo
				 ) VALUES ( 
							".($vlr_codigo[$i] ? "'$vlr_codigo[$i]'" : "0" ).",
							".intval($id_login).", 
							".CURRENT_DATE.", 
							".CURRENT_TIME.", 
							'".trim(strtoupper($res_observacao[$i]))."', 
							'".trim(strtoupper($res_conclusoes[$i]))."', 
							'$vlr_valor[$i]', 
							".intval($cad_exame).", 
							".$proc_codigo." )";
	 			//$sql = pg_query($stmt);
	 }
	 exit();
 		$pegaUsu = "select * from cadastrodoexame where cad_exame = $cad_exame ";
		$queryUsu = pg_query($pegaUsu);
		$linhaUsu = pg_fetch_array($queryUsu);

		$usu_codigo = $linhaUsu['usu_codigo'];
	//exit();
        echo "<script>
                window.location = \"exa_digitacaoresultado.php?proc_codigo=$proc_codigo&usu_codigo=$usu_codigo&cad_exame=$cad_exame&id_login=$id_login \";  
              </script>";
}


?>
