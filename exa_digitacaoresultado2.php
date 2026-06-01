<?php
session_start();
//echo "<pre>".print_r($_REQUEST,1);
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";

cabecario( $hotkey = true);
$common = new commonClass();
$form = new classForm();
$table = new tableClass();

echo $common->incJquery();

?>
<script type="text/javascript">
	function liberarLaudo(agei_codigo,usu_codigo,id_login,age_codigo,usr_tipo_medico){
		if(usr_tipo_medico == "B"){
			url = "liberarLaudo.php?agei_codigo="+agei_codigo+"&usu_codigo="+usu_codigo+"&id_login="+id_login+"&age_codigo="+age_codigo+"&acao=libera_automatico";
			window.open(url,null,'height=500,width=750,status=yes,toolbar=no,menubar=no,location=no');
		}else{
			url = "liberarLaudo.php?agei_codigo="+agei_codigo+"&usu_codigo="+usu_codigo+"&id_login="+id_login+"&age_codigo="+age_codigo+"&acao=form";
			window.open(url,null,'height=500,width=750,status=yes,toolbar=no,menubar=no,location=no');
		}
	}
	
	function imprimir(linkroot,modulo){
		camposMarcados = new Array();
		$("input[type=checkbox]:checked").each(function(){
		    camposMarcados.push($(this).val());
		});
		if (camposMarcados!="") {
			$.ajax({
				url:linkroot+modulo+"zf/laboratorio/laudos/confere-assinatura-responsaveis",
				type: "POST",
				data: {
					age_codigo: <?=$age_codigo?> 
				},
				success: function(txt) {
					if (txt=="assinado") {
						var link = "./zf/laboratorio/laudos/imprimir/proc_codigos/"+camposMarcados+"/age_codigo/"+<?=$age_codigo?>;
						window.open(link, "name", "scrollbars=1,height=750,width=800",'width=850,height=700');
					} else {
						bioquimicosResponsavel();
					}
				}
			});  
		} else {
			alert("Selecione um exame para imprimir!");
		}
	}
	
	/*function confirmaImpressao(){
		decisao = confirm("Deseja realmente excluir este agendamento?");
		if (decisao){
			window.location.href = "exa_listapedidoexame.php?action=exc_age&agex_codigo="+agex_codigo+"&id_login="+id_login+"&acao="+acao+"&age_data="+age_data+"&med_codigo="+med_codigo+"&agexl_status="+agexl_status+"";
		} else {
			//alert ("Você clicou no botão CANCELAR,\n"+"porque foi retornado o valor: "+decisao);
		}
	}*/
	
	
	function bioquimicosResponsavel(){
		var link = "./zf/laboratorio/laudos/lista-responsaveis-laudos/age_codigo/"+<?=$age_codigo?>;
		window.open(link, "name", "scrollbars=1,height=400,width=500",'width=850,height=700');
	}
	
	function retornaDigitacao(age_codigo){
		alert(age_codigo);
	}
	
	function marcaDesmarca(){
		var j = -1;
		for (i = 0; i < document.requisicoes.elements.length; i++){
			if(document.requisicoes.elements[i].type == "checkbox"){
				if (j == -1){
					j = i; //seleciona o primeiro checkbox para saber qual operação realizar
					marcar = !document.requisicoes.elements[j].checked; //marcar recebe o contrário do primeiro checkbox
				}
				document.requisicoes.elements[i].checked = marcar;// marca todos os checkbox com o contrário do primeiro
			}
		}
		img = document.getElementById('img');
		if (!document.requisicoes.elements[j].checked){
			img.src = "<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/uncheckAll.png";
		}else{
			img.src = "<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/checkAll.png";
		}
	}
	
</script>

<?

reglog($id_login,"Acessando Digitacao do Resultado");
if(empty($acao)) {
	// Imprimir antigo
	//<a href='#' OnClick='window.open(\"exa_listapedidoexame_printall.php?age_codigo=$age_codigo&proc_codigo=$row[proc_codigo]\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a>
    echo "<fieldset>
            <legend>Opções</legend>
	    	<a href=exa_listapedidoexame.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg onclick=\"imprimir('".$_SESSION[linkroot]."','".$_SESSION[modulo]."')\" border=0 style='cursor:pointer;'>
			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/bioquimico_responsavel.jpg onclick=\"bioquimicosResponsavel()\" border=0 style='cursor:pointer;'> 
		  </fieldset>
          <br>";
	$sql = "SELECT 
				i.proc_codigo as proc_c,
				proc_nome,
				c.med_codigo medico,
				a.usu_codigo,
				u.usu_nome,
				ai.agei_data,
				ai.agei_status,
				a.age_codigo,
				a.med_codigo,
				a.usr_codigo_medico,
				ai.agei_codigo,
				col.col_data_coleta,*				
			  FROM 
				medico m 
			  JOIN convenio c
				ON c.med_codigo = m.med_codigo
			  JOIN convenio_itens i
				ON i.conv_codigo = c.conv_codigo
			  LEFT JOIN agenda_itens ai
				ON ai.coni_codigo = i.coni_codigo
			  JOIN agenda a
				ON a.age_codigo = ai.age_codigo
			  JOIN usuario u
				ON u.usu_codigo = a.usu_codigo
			  JOIN procedimento proc
				ON proc.proc_codigo = i.proc_codigo
			  JOIN coleta col
				ON col.agei_codigo = ai.agei_codigo
			  JOIN tipodeexame as tp 
				on tp.proc_codigo = i.proc_codigo 
			  AND 
				a.age_codigo = $age_codigo 		
			  ORDER BY 
				proc_nome";
		//		die($sql);
	$exec = pg_query($sql);
	echo $form->openForm("#", "POST", "requisicoes");
	echo "
		<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
		  <tr bgcolor=FFFFFF>
		  <th style='width:5%'><img id='img' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/uncheckAll.png' onClick=\"marcaDesmarca();\"></th>
		  <th style='width:5%'>Cod.</th>
		  <th style='width:55%'>Exame</th>
		  <th style='width:15%'>Dt.Coleta</th>
		  <th style='width:20%' colspan='2'>Opções</th>
		</tr>";
	if(pg_num_rows($exec) == 0){
		echo "
			<tr>
				<td colspan='4'>
					Por favor realize a coleta do Exame para emitir um laudo!
				</td>
			</tr>";
	}
	while($row=pg_fetch_array($exec)) {
		$res = "select *from resultadoexame where agei_codigo = '$row[agei_codigo]' and proc_codigo = '$row[proc_codigo]'";
		$queryRes = pg_query($res);
		$arrRes = pg_fetch_array($queryRes);
		$teste = pg_num_rows($queryRes);
		$dtc = explode("-",$row[col_data_coleta]);
		$datadacoleta = "$dtc[2]/$dtc[1]/$dtc[0]";
		// Validação pra ver se esta liberado
		$sqlConfere = "SELECT * FROM agenda_itens WHERE agei_codigo = '".$row[agei_codigo]."' AND usr_codigo_bioquimico IS NOT NULL";	
		$queryConfere = pg_query($sqlConfere);
		$numConfere = pg_num_rows($queryConfere);
		if ($numConfere == 0) {
			echo "<tr>";
		} else {
			echo "<tr style='background-color: #99FF66;'>";
		}
		// Dados do resultado
		if(pg_num_rows($queryRes)<=0) { 
			echo "<td>Digitar Laudo</td>";
			//echo "<td><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/digitar_laudo.png' style='width:18px; height:18px;'></td>";
		} else { 
			echo"<input type=hidden id=teste value=1>
				<input type=hidden name=datacoleta id=datacoleta value=$row[col_data_coleta]>
				<input type=hidden name=p_codigo[] id=p_codigo[] value=$row[proc_codigo]>
				<input type=hidden name=age_codigo id=age_codigo value=$row[age_codigo]>
				<input type=hidden name=proc_nome value=$row[proc_nome]>
				<td><input type=checkbox name='proc_codigo[$row[proc_codigo]]' value=$row[proc_codigo]></td>";	
		}
		echo "<td align=center>$row[proc_codigo]</td>
	    <td>$row[proc_nome]</td>
	    <td>$datadacoleta</td>";
		// Controla edição e digitação do laudo
		if(pg_num_rows($queryRes)<=0) {
			echo"<td width=100% colspan='2' style='float:left;'>";
				//LOGICA ANTIGA
				//echo $common->commonButton("DIGITAR LAUDO","$PHP_SELF?acao=form_add&cad_exame=$cad_exame&usu_codigo=$usu_codigo&proc_codigo=$row[codigo]","laudoAdd.png");
				echo $common->commonButton("DIGITAR LAUDO",$_SESSION[linkroot].$_SESSION[modulo]."laudos/laudo2.php?acao=form_add&agei_codigo=$row[agei_codigo]&usu_codigo=$row[usu_codigo]&proc_codigo=$row[proc_c]&id_login=$id_login&age_codigo=$row[age_codigo]","laudoAdd.png");
			echo "</td>";
		} else {
			  $sqlConfere = "SELECT * FROM agenda_itens WHERE agei_codigo = '".$row[agei_codigo]."' AND usr_codigo_bioquimico IS NOT NULL";	
			  $queryConfere = pg_query($sqlConfere);
			  $numConfere = pg_num_rows($queryConfere);
			  // Se já foi liberado não edita mais
			  if ($numConfere == 0) {
				echo"<td width=100% colspan='2' style='float:left;'>";
					echo $common->commonButton("EDITAR LAUDO",$_SESSION[linkroot].$_SESSION[modulo]."laudos/laudo2.php?acao=form_add&agei_codigo=$row[agei_codigo]&proc_codigo=$row[proc_c]&id_login=$id_login&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]","laudo.png","style='width:70px;'");	  
				echo "</td>";
			  } else {
				echo"<td width=100% colspan='2' style='float:left;' >";
					echo $common->commonButton("EDITAR LAUDO","","laudo.png","style='width:70px;'");
					//echo $common->commonButton("EDITAR LAUDO '",$_SESSION[linkroot].$_SESSION[modulo]."laudos/laudo2.php?acao=form_add&agei_codigo=$row[agei_codigo]&proc_codigo=$row[proc_c]&id_login=$id_login&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]","laudo.png","style='width:70px;'");	  
				echo "</td>";
			  }
			  // Liberação de Laudo
			  /*echo"<td width=8%>";
				$sqlBioquimico = "SELECT usr_tipo_medico FROM usuarios WHERE usr_codigo = $id_login";
				$queryBioquimico = pg_query($sqlBioquimico);
				$regBioquimico = pg_fetch_array($queryBioquimico);
				echo $common->commonButton("LIBERADO",null,"historico.png","onClick=\"liberarLaudo($row[agei_codigo],$row[usu_codigo],$id_login,$row[age_codigo],'$regBioquimico[usr_tipo_medico]');\"");
				//echo $common->commonButton("LIBERADO",$_SESSION[linkroot].$_SESSION[modulo]."laudos/laudo2.php?acao=form_add&agei_codigo=$row[agei_codigo]&proc_codigo=$row[proc_c]&id_login=$id_login&usu_codigo=$row[usu_codigo]&age_codigo=$row[age_codigo]","historico.png");	  
			  echo "</td>";*/
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
		 echo "<br />";
		 /*echo "<table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
				<tr bgcolor=FFFFFF>
					<td colspan='2'><b>Legenda</b></td>
				</tr>
				<tr bgcolor=FFFFFF>
					<td width='3%' style='background-color: #99FF66;'></td>
					<td><b>Laudo liberado.</b></td>
				</tr>
			  </table>";*/
		  echo $form->closeForm();
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
