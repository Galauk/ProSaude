<?
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.db.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
cabecario( $hotkey = true);

reglog($id_login,"Acessando Fazer EXAME");


?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="g_script.js"></script>
<script>

function trataResultado()
{
	
		data_previsao = document.getElementById('data_previsao').value;
		laboratorio = document.getElementById('laboratorio').value;
		unidade = document.getElementById('unidade').selectedIndex;
		medico = document.getElementById('medico').selectedIndex;	

		
		
		if(data_previsao  == "")
		{
			//alert('lab: '+laboratorio+' unidade: '+unidade+' medico: '+medico);
			alert("Preencha a data de previsão");
			document.getElementById('data_previsao').focus();
			return false;
		}
		if(laboratorio == "")
		{
			alert("Informe o Laboratório");
		//	document.getElementId('laboratorio').focus();
			return false;
		}
		if(unidade == "")
		{
				alert("Informe a Unidade");
			//	document.getElementId('unidade').focus();
				return false;
		}
		if(medico == "")
		{
				alert("Informe o Médico");
				//document.getElementById('medico').focus();
				return false;
		}
		

}

function validaPaciente(){
	paciente = document.getElementById('pac_nome').value;
	
		if(paciente == "")
		{
				alert("Informe o Paciente");
				document.getElementById('pac_nome').focus();		
				return false;
		}
	
}
function xi(){
	url = "exa_medicoExterno.php?acao=E";
	ajax_tudo(url,retornoMedico);
	
}

function xx(){
	url = "exa_medicoExterno.php?acao=I";
        ajax_tudo(url,retornoMedico);
}
function retornoMedico(txt){
	div = document.getElementById('oc');
	div.innerHTML = txt;
}

</script>
<form method=post action="<?=$PHP_SELF?>">
<input type=hidden name=id_login value="<?=$id_login?>">

<?

if(($acao=="form_add" OR $acao=="form_edit")) {
echo "<input type=hidden name=acao value=continua1>";
   $sel = pg_fetch_array(pg_query("SELECT cad_exame, to_char(cad_datapedido,'DD/MM/YYYY') as cad_datapedido, id_login, labm_codigo, uni_codigo, usu_codigo, to_char(cad_previsaoentrega,'DD/MM/YYYY') as cad_previsaoentrega, med_codigo, cad_retiradapedidoexame, cad_dadosclinicos FROM cadastrodoexame where cad_exame = '$cad_exame'"));
   
   $pac = pg_fetch_array(pg_query("select *from usuario where usu_codigo = '$sel[usu_codigo]'"));
                        echo "<fieldset>";
                                echo "<legend>Dados do Paciente</legend>";
                                echo "<table width=100% cellspacing=0 cellpadding=1 border=0>";
                                        echo "<tr>";
                                                echo "<td width=110>Numero do Paciente</td>";
                                                echo "<td width=40>";
                                                        echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxl size=10 readonly value='$pac[usu_codigo]'>";
                                                        echo "</td>";


                                                echo "<td width=40>Paciente</td>";
                                                echo "<td>";
                                                        echo "<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxl size=60 onkeyasdfup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
                                                echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";



                                                echo divBuscaPaciente();                                       
                                                        echo "<a href='#' OnClick='window.open(\"paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1\",null,\" height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg align=absmiddle id=ficha border=0></a>";
                                                echo "</td>";
                                                echo "<td>Nascimento</td>";
                                                echo "<td width=230>";
                                                        echo "<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxl size=15 onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nascimento'), 'buscar_data');return Ajusta_Data(this, event);\" maxlength=\"10\">";
                                                        echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nascimento'), 'buscar_data')\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
                                                echo "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                                echo "<td width=70 >Mãe</td>";
                                                echo "<td width=100 colspan=3>";
                                                        echo "<input type=text name=pac_mae value='$pac[usu_mae]' id=pac_mae class=boxl size=50 readonly>";
                                                echo "</td>";
                                                echo "<td width=20>Cidade</td>";
                                                echo "<td width=80>";
                                                        echo "<input type=text name=pac_cidade id=pac_cidade value='$pac[usu_end_cidade]' class=boxl size=23 readonly>";
                                                echo "</td>";
                                        echo "</tr>";
                                echo "</table>";
			echo "<div align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg onclick=\"return validaPaciente()\"></div>";
                        echo "</fieldset>";
					
						
						echo"</form>";
}

if($acao=="continua1") {
echo "<script> function x(){alert('aaaa')}  </script>";
echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Listando Recepcao/Coletas de Exames</legend>
             <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='50'>Prontuario</td>
                   <td width='*'>Nome</td>
                   <td width='*'>Dt.Pedido</td>
                   <td colspan=2 width='65' align='center'>&nbsp;</td>
                  </tr>";
$sqlRecep = "SELECT med_nome,
					c.med_codigo,
			        agei_data as agexl_data,
					usu_prontuario,
					usu_nome,
					agei_status as agexl_status,
					ai.agei_codigo as agex_codigo,
					a.med_codigo as med_codigo_solicitante,
					a.usr_codigo_medico				
			  FROM medico m 
			  JOIN convenio c
			    ON c.med_codigo = m.med_codigo
			  JOIN convenio_itens i
			    ON i.conv_codigo = c.conv_codigo
			  JOIN agenda_itens ai
			    ON ai.coni_codigo = i.coni_codigo
			  JOIN agenda a
			    ON a.age_codigo = ai.age_codigo
			  JOIN usuario u 
			    ON u.usu_codigo = a.usu_codigo
			 WHERE a.usu_codigo = '$pac_codigo' 
			   and agei_status = 'R' 
			GROUP BY c.med_codigo,
			    u.usu_prontuario,
			    u.usu_nome,
			    ai.agei_data,
			    agei_data,
			    a.age_codigo,
			    med_nome,
			    ai.agei_status,
			    ai.agei_codigo,
			    a.med_codigo,
			    a.usr_codigo_medico
			ORDER BY med_nome asc";
//echo $sqlRecep;
   $query = pg_query($sqlRecep) or die(pg_last_error());
           while($row=pg_fetch_array($query)) {
                if($row[agexl_status] == "R") { 
                   $s1 = "<b><font color=green>";
                   $s2 = "</b></font>";
                }
                 $dt = explode("-",$row[agexl_data]);
                 $datapedido = "$dt[2]/$dt[1]/$dt[0]";
			
			echo "<tr>
                           <td width='50' align='center'>$s1 $row[usu_prontuario] $s2</td>
                           <td width='*'>$s1 $row[usu_nome] $s2</td>
                           <td width='*'>$s1 $datapedido $s2</td>
                           <td width='65' align='center'>
                           <a href='$PHP_SELF?med_codigo=$row[med_codigo]&agex_codigo=$row[agex_codigo]&age_data=$row[agexl_data]&id_login=$id_login&acao=continua2&pac_codigo=$pac_codigo&med_codigo_solicitante=$row[med_codigo_solicitante]&usr_codigo_medico=$row[usr_codigo_medico]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/continuar_cadastro.jpg' alt='Continuar Cadastro' title= 'Continuar Cadastro' border='0'></a></td>
                         </tr>";		
                 }
            if(pg_num_rows($query) == 0){
				echo "<tr>
					<td colspan='5'>Paciente não recepcionado</td>
			    </tr>";
            }
                 

           echo "</table>
           </fieldset>
          </td>
         </tr>
      </table>";

}
if($acao=="continua2") {
	//echo "<pre>".print_r($_REQUEST,1)."</pre>";
 $dtp = explode("-",$age_data);
 $dtpedido = "$dtp[2]/$dtp[1]/$dtp[0]"; 
echo "<form method=post action='$PHP_SELF'>
      <input type=hidden name=id_login value='$id_login'>
      <input type=hidden name=agex_codigo value='$agex_codigo'>
      <input type=hidden name=med_codigo value='$med_codigo'>
      <input type=hidden name=age_data value='$age_data'>
      <input type=hidden name=pac_codigo value='$pac_codigo'>";
// MEXER AQUI
$sel = pg_query("select to_char(cad_previsaoentrega,'DD/MM/YYYY') as previsaoentrega,
		*from cadastrodoexame where usu_codigo = $pac_codigo and age_data = '$age_data' and agex_codigo = '$agex_codigo'") or die(pg_last_error());


if(pg_num_rows($sel)==0) {
	// se enviar dados ele cai aqui.
echo "<input type=hidden name=acao value=add>";
} else {
	//se finalizar o paciente ja foi adicionado ele cai aki
$cx = pg_fetch_array($sel);
echo "<input type=hidden name=acao value=edit>";
echo "<input type=hidden name=cad_exame value=$cx[cad_exame]>";
if($cx[cad_retiradapedidoexame]=="EN") {
   $vl1="selected";
   $vl2="";
} else {
   $vl1="";
   $vl2="selected";
}
}
?>
<fieldset>
<legend>Pedido do Exame<span id='pac_busca_status' style='font-style:italic;'>&nbsp;</span></legend>
	<table cellpadding='1'>
		<tr>
	 		<td align=right>Laborat&oacute;rio:</td>
		 	<td>
			 	<?php 
			 	$sql = pg_query("select * from medico where med_codigo = $_GET[med_codigo]");
			    $lab=pg_fetch_array($sql);
			 
			 	echo "<span>$lab[med_nome]</span>";
			 	?>
			 	<input type=hidden id=med_codigo name=med_codigo value='<?=$lab[med_codigo]?>'>
			 	<input type=hidden id=med_codigo_solicitante name=med_codigo_solicitante value='<?=$_GET[med_codigo_solicitante]?>'>
			 	<input type=hidden id=usr_codigo_medico name=usr_codigo_medico value='<?=$_GET[usr_codigo_medico]?>'>
		 	</td>
		</tr>
		<tr>
			<td align=right>Medico Solicitante:</td>
			<td>		
				<div id='oc'>
					<?
					if($_GET[med_codigo_solicitante] != ''){
						 $sle = "SELECT med_codigo as codigo,
						 				UPPER(med_nome) as nome
									FROM medico
								   WHERE med_codigo = $_GET[med_codigo_solicitante]";
					}else{
						$sle = "SELECT usr_codigo codigo,
									   usr_nome nome
								 FROM usuarios
								 WHERE usr_codigo = $_GET[usr_codigo_medico] ";
					}
					$select = pg_query($sle);
					$med=pg_fetch_array($select);
					
					echo "<span>$med[nome]</span>";	
					?>
				 	<input type=hidden id=med_codigo name=med_codigo value='<?=$med[codigo]?>'>
					
				</div>
			</td>
		</tr>
		<tr>
			<td align=right>Local de Coleta:</td>
			<td>
	  			<?php echo "<span>$lab[med_nome]</span>";?>	 
	 		</td>
		</tr>
    	<tr>
        	<td align=right>Tipo:</td>
        	<td>
        		<select name=cad_retiradapedidoexame class=box>
				  <option value='EN' <?=$vl1?> >ENTRADA DO PEDIDO</option>
				  <option value='RE' <?=$vl2?> >RETIRADA DO PEDIDO</option>
	  		    </select>
	  		 </td>
        	</tr>
        <tr>
			<td align=right width=15%>Data do Pedido:</td>
		 	<td>
		 		<input type=text name=cad_datapedido value='<?=$dtpedido?>' size=12 class=box onkeypress='Ajusta_Data(this, event);' maxlength=10 readonly>
		 	</td>
		</tr>
        <tr>
			<td align=right>Data Previs&atilde;o Entrega:</td>	 
			<td>
				<input type=text id=data_previsao name=cad_previsaoentrega value='<?=$cx[previsaoentrega]?>' size=12 class=box onkeypress='Ajusta_Data(this, event);' maxlength=10>
			</td>
		</tr>
<?

if(pg_num_rows($sel)==0) {
	$referencia = "naoMostra";
echo "<tr>
       <td>&nbsp;</td>
       <td><a href=exa_listapedidoexame.php><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;
	   <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg class=boxx onclick=\"return trataResultado()\"></td>
      </tr>";
} else {
	
echo "<tr>
       <td>&nbsp;</td>
       <td><a href=exa_listapedidoexame.php><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;
	   		<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar _pedido_on.jpg'title=Finalizar Pedido' value='Finalizar Pedido' class=boxx onclick=\"return trataResultado()\"></td>
      </tr>";
	$referencia = "mostra";
}
?>
</form>
</table>
</fieldset>


 <td>
   <fieldset>
        <legend>Material de Analise<span id='pac_busca_status' style='font-style:italic;'>&nbsp;</span></legend>
        <table cellpadding='0' class='table'>
        <tr>
	 <td>
<?
	if($referencia == "mostra"){
?>
     <iframe name=frameprincipal src='exa_materialdeanalise_iframeAGE.php?id_login=<?=$id_login?>&cad_exame=<?=$cad_exame?>&labm_codigo=<?=$sel[labm_codigo]?>&age_data=<?=$age_data?>&agex_codigo=<?=$agex_codigo?>&usu_codigo=<?=$pac_codigo?>' frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=400px;></iframe></td>
<?
	}else{
		
	}
?>
    </tr>
	</table>
    </fieldset>
 </td>
</tr>
</table>
<?
 }
if($acao=="add") {
// SQL INSERT

	if(empty($pac_codigo)) {
		 echo "<br><br><br><br><br><br><Br><Br><br><br><br><br><br>
				<table height=100 width=50% align=center cellspacing=0cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
				 <tr bgcolor=f9f9f9>
				   <td align=center><font size=2 color=green><b>Paciente nao selecionado</b></font></td>
				 </tr>
				</table><br>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
				  setTimeout(\"location='exa_pedidoexame.php?acao=form_add&id_login=$id_login&cad_exame=$cad_exame&labm_codigo=$labm_codigo'\", 0);
			  </SCRIPT>";
	exit;
	}


/* $id = pg_fetch_array( pg_query("select max(cad_exame) as cad_exame from cadastrodoexame"));
 $cad_exame = $id[cad_exame]+1;*/
 
 $stmt = "INSERT INTO cadastrodoexame ( 
							agex_codigo,
							age_data,
							cad_datapedido, 
							id_login, 
							labm_codigo, 
							uni_codigo, 
							usu_codigo, 
							cad_previsaoentrega, 
							med_codigo, 
							cad_retiradapedidoexame, 
							cad_dadosclinicos,
							cad_medico_externo
							 ) VALUES ( 
							".$agex_codigo.",
							'".trim($age_data)."',
							'".trim(strtoupper($cad_datapedido))."', 
							".intval($id_login).", 
							".intval($labm_codigo).", 
							".intval($uni_codigo).", 
							".intval($pac_codigo).", 
							'".trim(strtoupper($cad_previsaoentrega))."', 
							".intval($med_codigo).", 
							'$cad_retiradapedidoexame', 
							'".trim(strtoupper($cad_dadosclinicos))."',
							".($cad_medico_externo == "" ? "null":"'$cad_medico_externo'")." )";

	$sql = pg_query($stmt) or die(pg_last_error());

	$id = pg_fetch_array( pg_query("select max(cad_exame) as cad_exame from cadastrodoexame"));
	$cad_exame = $id[cad_exame];

$verificaProc = "select * from agendamento_exame_lista where agex_codigo = $agex_codigo";
$qryVerifica = pg_query($verificaProc);
while($line = pg_fetch_array($qryVerifica)){
		
$stmtItens = "INSERT INTO itensdoexame ( 
						cad_exame, 
						proc_codigo, 
						itx_observacao, 
						itx_urgente 
						 ) VALUES ( 
						'$cad_exame', 
						'$line[proc_codigo]', 
						'$itx_observacao', 
						'P')";
$qryItens = pg_query($stmtItens);
}
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_pedidoexame.php?acao=continua2&id_login=$id_login&med_codigo=$med_codigo&age_data=$age_data&agex_codigo=$agex_codigo&pac_codigo=$pac_codigo'\", 0);
              </SCRIPT>";
}

if($acao=="edit") {
 $stmt = "UPDATE cadastrodoexame SET 
	cad_datapedido = '".trim(strtoupper($cad_datapedido))."', 
	id_login = ".intval($id_login).", 
	labm_codigo = ".intval($labm_codigo).", 
	uni_codigo = ".intval($uni_codigo).", 
	cad_previsaoentrega = '".trim(strtoupper($cad_previsaoentrega))."', 
	med_codigo = ".intval($med_codigo).",
	cad_medico_externo = ".($cad_medico_externo == "" ? "null":"'$cad_medico_externo'").",
	cad_retiradapedidoexame = '$cad_retiradapedidoexame', 
	cad_dadosclinicos = '".trim(strtoupper($cad_dadosclinicos))."'
	WHERE cad_exame = ".intval($cad_exame) ;

$sql = pg_query($stmt);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_pedidoexame.php?acao=finaliza&id_login=$id_login&cad_exame=$cad_exame&med_codigo=$med_codigo&age_data=$age_data&agex_codigo=$agex_codigo&pac_codigo=$pac_codigo'\", 0);
              </SCRIPT>";

}


if($acao=="finaliza") {
         echo "<br><br><br><br><br><br><Br><Br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font size=2 color=green><b>PEDIDO com FINALIZADO</b></font></td>
                 </tr>
                </table><br>";

        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='exa_listapedidoexame.php?id_login=$id_login'\", 2000);
              </SCRIPT>";
}

?>
