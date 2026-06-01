<script type="text/javascript" src="../funcoes.js"></script>
<script type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="../recepcao.js.php"></script>
<script>
function val () {
 if(document.form.med_codigo.value == "0") { alert("Selecione um Laboratorio"); return false; }
 if(document.form.age_data.value == "") { alert("Por favor - Preencha a Data"); return false; }
}
function cancelaAgendamentoExame(agex_codigo,id_login,acao,age_data,med_codigo,agexl_status){
	decisao = confirm("Deseja realmente excluir este agendamento?");
	if (decisao){
		window.location.href = "exame/exa_listapedidoexame.php?action=exc_age&agex_codigo="+agex_codigo+"&id_login="+id_login+"&acao="+acao+"&age_data="+age_data+"&med_codigo="+med_codigo+"&agexl_status="+agexl_status+"";
	} else {
		//alert ("Voc� clicou no bot�o CANCELAR,\n"+"porque foi retornado o valor: "+decisao);
	}
}
</script>
<?
	session_start();
	include_once '../global.php';
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	$common = new commonClass();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario( $hotkey = true);

//	reglog($id_login,"Acessando LISTA DE EXAMES");
	if($action=="sw") {
		//echo "<pre>".print_r($_REQUEST,1)."</pre>";exit;
		//echo "<pre>".print_r($_POST,1)."</pre>";exit;
		if($agexl_status == "R") {
		$status = "A";
		} else {
			$status = "R";
			$controle = 1;
			$dd = pg_fetch_array(pg_query("SELECT to_char(current_date,'DD/MM/YYYY') AS dataatual,
												  (usr_data_insert + interval '7 days') AS dataini,
												  usr_data_insert  AS agexl_dt_atualizacao
											  FROM agenda
											  ORDER BY usr_data_insert LIMIT 1"));
			$id_dia = $dd[dataatual];
			$exp=explode("/",$id_dia);
			$ALLSEMANA = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
		}

	$select = "UPDATE agenda_itens
				  SET agei_status = '$status',
				  	  usr_codigo = '$id_login'
				WHERE age_codigo = $agex_codigo
				  AND agei_data = '$age_data'
				 ";
//die($acao);
$selectInformacoes = "SELECT c.med_codigo medico,
						a.usu_codigo,
						u.usu_nome,
						ai.agei_data,
						ai.agei_status,
						a.age_codigo,
						a.med_codigo,
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
				   AND agei_data = '$age_data'
			       AND c.med_codigo = '$med_codigo'
				 GROUP BY
					   c.med_codigo,
					   a.usu_codigo,
					   u.usu_nome,
					   agei_data,
					   ai.agei_status,
					   a.age_codigo,
					   a.med_codigo,
					a.usr_codigo_medico
				ORDER BY usu_nome";

$query=pg_query($selectInformacoes);
$res = pg_fetch_array($query);
//echo "<pre>".print_r($selectInformacoes,1)."</pre>";exit;
	//die($a."<BR> MEDICO".$med_codigo."<BR>CODIGO".$agex_codigo."<BR>DATA".$age_data."<BR>LOGIN".$id_login."<BR>CODIGO".$pac_codigo."<BR>SOLICTANTE".$med_codigo_solicitante."<BR>MEDICO".$usr_codigo_medico);
	$sql = pg_query($select) or die(pg_last_error());
	if($agexl_status != "A"){
		echo "
		<SCRIPT LANGUAGE=\"JavaScript\">
			setTimeout(\"location='exame/exa_listapedidoexame.php?id_login=$id_login&acao=$acao&age_data=$age_data&med_codigo=$med_codigo&cod'\", 1);
		</SCRIPT>";
	}else{
		echo "
		<SCRIPT LANGUAGE=\"JavaScript\">
			setTimeout(\"location='exame/exa_coleta.php?id_login=$id_login&age_data=$res[agei_data]&med_codigo=$res[medico]&age_codigo=$agex_codigo&usr_codigo_medico=$res[usr_codigo_medico]'\", 1);
		</SCRIPT>";
	}

}

	if($action=="exc_age") {
		// Exclus�o do agendamento, caso n�o haja algum liberado, do caso contr�rio pode excluir
		// Lendo os itens agendados e removendo
		$sql_agei = "SELECT agei_codigo FROM agenda_itens WHERE age_codigo = '".$agex_codigo."'";
		$query_agei = pg_query($sql_agei);
		while($row_agei = pg_fetch_array($query_agei)) {
			// Coletas realizadas, se foi s�o exclu�das
			$sql_del_col = "DELETE FROM coleta WHERE agei_codigo = '".$row_agei["agei_codigo"]."'";
			$query_del_col = pg_query($sql_del_col);
			// Resultado de exame, se tiver s�o excluidos
			$sql_del_res = "DELETE FROM resultadoexame WHERE agei_codigo = '".$row_agei["agei_codigo"]."'";
			$query_del_res = pg_query($sql_del_res);
			// Item agendado, se tiver � exclu�do
			$sql_del_agei = "DELETE FROM agenda_itens WHERE agei_codigo = '".$row_agei["agei_codigo"]."'";
			$query_del_agei = pg_query($sql_del_agei);
		}
		// Excluindo agendamento
		$sql_del_age = "DELETE FROM agenda WHERE age_codigo = '".$agex_codigo."'";
		$query_del_age = pg_query($sql_del_age);
		echo "<script type='text/javascript'>window.location.href = 'exa_listapedidoexame.php?id_login=$id_login&acao=$acao&age_data=$age_data&med_codigo=$med_codigo&cod'</script>";
	}

//
//-> Botoes
?>
<?
//echo "<pre>".print_r($_REQUEST,1);
//<a href=../zf/agenda/distribuicao><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencaoagenda_on.jpg' border=0></a>
  echo "<fieldset>
            <legend>Op&ccedil;&otilde;es</legend>
            <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
		    <tr>
				<td width=120><a href='index.php?link=zf/agenda/agenda' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazeragendamento_on.jpg border=0></a></td>
				<td width=205><a href=exame/exa_lab_valor.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/labquotavlr.png' border=0></a></td>
				<td width=205><a href=zf/agenda/convenio><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/labquotauni.png' border=0></a></td>
				<td width=205></td>
			</tr>
			<tr>";
			$sqlConfPermLaudo = "select
									p.perm_descricao, p.perm_programa, up.nivel_i, up.nivel_a, up.nivel_d, up.nivel_l, up.nivel_b, up.perm_set
								from
									usuarios_permissoes as up
								left join
									permissoes as p on up.perm_codigo = p.perm_codigo
								where
									up.usr_codigo = '$id_login' and p.perm_programa = 'exa_listapedidoexame.php'";
			$queryConfPermLaudo = pg_query($sqlConfPermLaudo);
			$numConfPermLaudo = pg_num_rows($queryConfPermLaudo);
			if ($numConfPermLaudo > 0) {
				echo "<td width=205>
						<a href=exa_listapedidoexame.php?id_login=$id_login>
							<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/laudos.jpg' border=0 sty>
						</a>
					</td>";
			} else {
				echo "<td width=205>
							<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/laudos_off.jpg' border=0 sty>
					</td>";
			}
			echo "<td width=205>".$common->commonButton("Controle de Exames", "exame/controleExames.php?id_login=$id_login", "vacina.png")."</td>
				</form>
              </tr>
             </table>
       </fieldset>
      <br>";

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>

           <fieldset>
            <legend>Parametros da Recepcao/Coletas de Exames</legend>
            <form method=post action='$PHP_SELF' name='form' onSubmit='return val();'>
	    <input type=hidden name=acao value=listar>
	    <input type=hidden name=id_login value=$id_login>
            <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0'>
              <tr bgcolor=FFFFFF>
		 <td>Laboratorio:</td>
		 <td><select name=med_codigo class=box>
	             <!--<option value=0>:: Selecione um Laboratorio ::</option>-->
               ";
$sql2 = "SELECT DISTINCT m.med_codigo,med_nome
		  FROM medico m
		  JOIN convenio c
		    ON c.med_codigo = m.med_codigo
		  JOIN convenio_itens i
		    ON i.conv_codigo = c.conv_codigo
		  JOIN agenda_itens ai
		    ON ai.coni_codigo = i.coni_codigo
		 ORDER BY med_nome asc";

$query = pg_query($sql2);
  while($rr = pg_fetch_array($query)) {
	echo ($med_codigo==$rr[med_codigo])?"<option value=$rr[med_codigo] selected>$rr[med_nome]</option>":"<option value=$rr[med_codigo]>$rr[med_nome]</option>";
}
$dataAgora = date('d/m/Y');
	echo " </select></td>
		</tr>
                <tr>
                  <td width='15' align='right'>Data</td>
                  <td width='85'><input type='text' class='box' size='12' name=age_data id='age_data' maxlength='10' value='$dataAgora' onkeypress=\"return Ajusta_Data(this, event);\"></td>";
                  $sqlConfPermLista = "select
									p.perm_descricao, p.perm_programa, up.nivel_i, up.nivel_a, up.nivel_d, up.nivel_l, up.nivel_b, up.perm_set
								from
									usuarios_permissoes as up
								left join
									permissoes as p on up.perm_codigo = p.perm_codigo
								where
									up.usr_codigo = '$id_login' and p.perm_programa = 'exa_listapedidoexame.php'";
					$queryConfPermLista = pg_query($sqlConfPermLista);
					$numConfPermLista = pg_num_rows($queryConfPermLista);
					if ($numConfPermLista > 0) {
						echo "<td><input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/listarpacientes_on.jpg' id='btn_lista_paciente' alt='Listar Pacientes' border='0' style='cursor:pointer;border:0 px solid;'></td>";
					} else {
						echo "<td>
								<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/listarpacientes_off.jpg' border='0' alt='Listar Pacientes' >
							</td>";
					}
            echo "</tr>
	     </table></form>
	    </fieldset>
	   </td>
	  </tr>
         </table>";


if($acao=="listar") {
	//echo "<pre>".print_r($_POST,1)."</pre>";

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Listando Recep&ccedil;&atilde;o/Coletas de Exames</legend>
             <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
              <tr bgcolor=FFFFFF>
                   <td width='50'>C&oacute;d. Controle</td>
                   <td width='50'>Prontu&aacute;rio</td>
                   <td width='*'>Nome</td>
                   <td width='*'>Dt.Pedido</td>
                   <td colspan=2 width='65' align='center'>&nbsp;</td>
                  </tr>";
/*
	  $sql = "  SELECT  ai.agei_codigo as cod_controle,
						c.med_codigo,
						a.usu_codigo as usu_prontuario,
						u.usu_nome,
						ai.agei_data as agexl_data,
						ai.agei_status as agexl_status,
						a.age_codigo as agex_codigo
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
          --JOIN coleta col
            --ON col.agei_codigo = ai.agei_codigo
				   AND agei_data = '$age_data'
			       AND c.med_codigo = '$med_codigo'
				 GROUP BY
            cod_controle,
            --col.col_codigo,
					   c.med_codigo,
					   a.usu_codigo,
					   u.usu_nome,
					   agexl_data,
					   agexl_status,
					   agex_codigo
				ORDER BY usu_nome
	  ";
*/	  
	  $sql = "  SELECT  
						c.med_codigo,
						a.usu_codigo as usu_prontuario,
						u.usu_nome,
						ai.agei_data as agexl_data,
						ai.agei_status as agexl_status,
						a.age_codigo as agex_codigo
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
          --JOIN coleta col
            --ON col.agei_codigo = ai.agei_codigo
				   AND agei_data = '$age_data'
			       AND c.med_codigo = '$med_codigo'
				 GROUP BY
            
            --col.col_codigo,
					   c.med_codigo,
					   a.usu_codigo,
					   u.usu_nome,
					   agexl_data,
					   agexl_status,
					   agex_codigo
				ORDER BY usu_nome
	  ";	  
	  //die($sql);

   $query = pg_query($sql) or die(pg_last_error());
           while($row=pg_fetch_array($query)) {
           //	echo"<pre>".print_r($row,1)."</pre>";
		 $dt = explode("-",$row[agexl_data]);
		 $datapedido = "$dt[2]/$dt[1]/$dt[0]";
		if($row[agexl_status] == "R") {
        //  echo $row[cod_controle];
           echo "<tr>
                           <td width='50' align='center'><font size=3 color=green>$row[cod_controle]</font></td>
                           <td width='50' align='center'><font size=3 color=green> $row[usu_prontuario] </font></td>
                           <td width='*'><font size=3 color=green>$row[usu_nome] </font></td>
                           <td width='*'><font size=3 color=green>$datapedido </font></td>
                           <td width='65' align='center'>
                            <a href='exame/exa_listapedidoexame.php?action=sw&agex_codigo=$row[agex_codigo]&id_login=$id_login&acao=$_REQUEST[acao]&age_data=$age_data&med_codigo=$med_codigo&agexl_status=$row[agexl_status]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/desmarcar_recepcao_coleta.jpg border=0></a>
                           </td>
                           <td width='65' align='center' >
                           <form method=post action=exame/exa_listapedidoexame.php>
						   <input type=hidden name=action value=sw>
						   <input type=hidden name=agex_codigo value=$row[agex_codigo]>
						   <input type=hidden name=id_login value=$id_login>
						   <input type=hidden name=acao value=$acao>
						   <input type=hidden name=age_data value=$age_data>
						   <input type=hidden name=med_codigo value=$med_codigo>
						   <input type=hidden name=agexl_status value=$row[cod_controle]>
						   <input type=hidden name=agexl_status value=$row[usu_prontuario]>
						   <input type=hidden name=agexl_status value=$row[usu_nome]>
						   <input type=hidden name=agexl_status value=$datapedido>
						    <input type=hidden name=med_codigo value=$med_codigo>
		   					<input type=hidden name=agexl_status value='A'>


                           <br><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/coletar.jpg border=0>&nbsp;<input type=hidden name=cod_controle class=box size=4>
                           </form>
                           </td><td width='65' align='center' >";
								$sql_conf_cancelar = "SELECT * FROM
														agenda_itens AS agei
													INNER JOIN
														agenda AS age ON agei.age_codigo=age.age_codigo
													WHERE
														agei.usr_codigo_bioquimico IS NOT NULL AND
														age.age_codigo = '".$row[agex_codigo]."'";
								$query_conf_cancelar = pg_query($sql_conf_cancelar);
								$num_conf_cancelar = pg_num_rows($query_conf_cancelar);
								if ($num_conf_cancelar > 0) {
									echo "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/excluir_off.jpg border=0 style='cursor: none'>";
								} else {
									echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/excluir_on.jpg' style='cursor:pointer;' onclick=cancelaAgendamentoExame(".$row['agex_codigo'].",$id_login,'$acao','$age_data',$med_codigo,'".$row['agexl_status']."'); />";
									//echo "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cancelar_on.jpg border=0 style='cursor:pointer' onclick='cancelaAgendamentoExame(".$row[agex_codigo].",".$id_login.",".$acao.",".$age_data.",".$med_codigo.",".$row[agexl_status].")'>";
									//cancelaAgendamentoExame
									/*echo"<a href='$PHP_SELF?action=exc_age&agex_codigo=$row[agex_codigo]&id_login=$id_login&acao=$acao&age_data=$age_data&med_codigo=$med_codigo&agexl_status=$row[agexl_status]'>
											<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cancelar_on.jpg border=0>
										</a>";*/
								}
						    echo "</td>
                           </tr>";
		  } else {
           echo "<tr><form method=post action=$PHP_SELF>
                           <td width='50' align='center'><font size=3>$row[cod_controle]</font></td>
                           <td width='50' align='center'>$row[usu_prontuario]</td>
                           <td width='*'>$row[usu_nome]</td>
                           <td width='*'>$datapedido</td>
			   <input type=hidden name=action value=sw>
			   <input type=hidden name=agex_codigo value=$row[agex_codigo]>
			   <input type=hidden name=id_login value=$id_login>
			   <input type=hidden name=acao value=$acao>
			   <input type=hidden name=age_data value=$age_data>
			   <input type=hidden name=med_codigo value=$med_codigo>
			   <input type=hidden name=agexl_status value=$row[agexl_status]>

                           <td width='225' align='center'>
                           <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/recepcao_coleta.jpg border=0>&nbsp;<input type=hidden name=cod_controle class=box size=4></td></form>
                        <td width='65' align='center' >";
								$sql_conf_cancelar = "SELECT * FROM
														agenda_itens AS agei
													INNER JOIN
														agenda AS age ON agei.age_codigo=age.age_codigo
													WHERE
														agei.usr_codigo_bioquimico IS NOT NULL AND
														age.age_codigo = '".$row[agex_codigo]."'";
								$query_conf_cancelar = pg_query($sql_conf_cancelar);
								$num_conf_cancelar = pg_num_rows($query_conf_cancelar);
								if ($num_conf_cancelar > 0) {
									echo "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/excluir_off.jpg border=0 style='cursor: default'>";
								} else {
									//".$row['agex_codigo'].",".$id_login.",".$acao.",".$age_data.",".$med_codigo.",".$row['agexl_status']."
									echo "<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/excluir_on.jpg' style='cursor:pointer;' onclick=cancelaAgendamentoExame(".$row['agex_codigo'].",$id_login,'$acao','$age_data',$med_codigo,'".$row['agexl_status']."'); />";
									/*echo"<a href='$PHP_SELF?action=exc_age&agex_codigo=$row[agex_codigo]&id_login=$id_login&acao=$acao&age_data=$age_data&med_codigo=$med_codigo&agexl_status=$row[agexl_status]'>
											<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/cancelar_on.jpg border=0>
										</a>";*/
								}
						    echo "</td>
						 </tr>";
		  }
                 }

           echo "</table>
           </fieldset>
          </td>
         </tr>
      </table>";
}
