<?php

session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$paciente = $_GET['paciente'];
$id_login = $_GET['id_login'];
//echo "<pre>".print_r($_REQUEST,true)."</pre>";
echo"
	<table border='0' class='tabCarteirinha'>
		<tr>
			<td class='corTdOff'></td>
			<td class='corTdDose'>
				<b>1&deg; Dose</b>
			</td>
			<td class='corTdDose'>
				<b>2&deg; Dose</b>
			</td>
			<td class='corTdDose'>
				<b>3&deg; Dose</b>
			</td>
			<td class='corTdDose'>
				<b>4&deg; Dose</b>
			</td>
			<td class='corTdDose'>
				<b>5&deg; Dose</b>
			</td>
			<td class='corTdDose'>
				<b>Refor&ccedil;o</b>
			</td>
			<td class='loteQntTittle' align='center'>
				<b>Lotes Quantidades</b>
			</td>
			<td class='doseQntTittle' align='center'>
				<b>Doses</b>
			</td>
			<td class='doseQntTittle' align='center'>
				<b>Descartar</b>
			</td>
		</tr>";
		// sql das colunas .
		$sql = "select produto.pro_codigo, 
				   produto.pro_nome,
				   carteirinha.dose_um,
				   carteirinha.dose_dois,
				   carteirinha.dose_tres,
				   carteirinha.dose_quatro,
				   carteirinha.dose_cinco,
				   carteirinha.reforco
				   FROM produto JOIN carteirinha
				ON produto.pro_codigo = carteirinha.pro_codigo";
		$consulta = pg_query($sql);
		//fim dos sql's das colunas.

		$conta = 0;


		while($row = pg_fetch_array($consulta) )
		{
			$cont2 = 1;
			$cont = str_pad($conta,2,0,STR_PAD_LEFT);
			$logon = "SELECT cod_setor 
						FROM logon 
					   WHERE id_login = $id_login ";
						
			$querylogon = pg_query($logon);
			$res2 = pg_fetch_array($querylogon);
			$set_codigo = $res2[cod_setor];
			
			$sqlSaldo = " SELECT * 
							FROM saldo 
						   WHERE pro_codigo = $row[pro_codigo] 
						     AND sal_validade > CURRENT_DATE 
						     AND sal_qtde > 0 
						     AND set_codigo = $set_codigo
						   ORDER BY sal_validade";
			//echo "<br>".$logon." <-> ".$sqlSaldo."<br>";
			$querySql = pg_query($sqlSaldo);
			$res = pg_fetch_array($querySql);
			$qtde = $res[sal_qtde];
			//echo $qtde."<br>"; 
			$lote = $res[sal_lote];
			//echo $sqlSaldo."<br>";
			$validade = $res[sal_validade];
			
			

		$selectIteCodigo = "SELECT * 
							  FROM itens_movimento im
							  JOIN controlefracionado c
							    ON c.ite_codigo = im.ite_codigo
							 WHERE im.pro_codigo = $row[pro_codigo]
							   AND set_codigo = $set_codigo  
							   AND cont_dose > 0";
		//die($selectIteCodigo);
		$querySelectIte = pg_query($selectIteCodigo);
		$resQueryIte = pg_fetch_array($querySelectIte);
		$ite_codigo = $resQueryIte['ite_codigo'];
		$dose2 = $resQueryIte['cont_dose'];
		//$dose = $resQueryIte['cont_codigo'];
		//echo $dose."<br>";
			
			
			
			
//			$sqlDose = "SELECT * 
//									  FROM itens_movimento 
//									 WHERE pro_codigo = $row[pro_codigo] 
//									   AND ite_lote = '$lote'";
//			//die ($sqlDose);
//						$querySqlDose = pg_query($sqlDose);
//						$res = pg_fetch_array($querySqlDose);
//						
//						$sqlVerefica = "SELECT cont_dose 
//										  FROM controlefracionado 
//										 WHERE set_codigo = $set_codigo 
//										   AND ite_codigo = $res[ite_codigo] 
//										   AND cont_dose > 0 ";
//						$queryVerifica = pg_query($sqlVerefica);
//						$res3 =  pg_fetch_array($queryVerifica);
//						$dose = $res3[cont_dose];
			
			echo"<tr>
					<td class='corTdOn'><b>{$row['pro_nome']}</b>
						<input type='hidden' name='pro_codigo$cont' id='pro_codigo$cont' value='$row[pro_codigo]'>
						<input type='hidden' name='ite_codigo' id='ite_codigo' value='$res[ite_codigo]'>
					</td>";
					
					echo "<script>alert('a $acao');  </script>";
					// EXEMPLO DE CONCAT GANBIARRA SINISTRA
					/*$sql_usu = "select to_char(vac_data,'dd/mm/yyyy') as data, vac.*, u.uni_desc from vacina_usuario AS vac LEFT JOIN unidade AS u 
   										ON u.uni_codigo = substring(vac.vac_unidade from 1 for position('|' in CONCAT(vac.vac_unidade,'|') )-1 )::integer where usu_codigo = '$paciente' and vac_dose = '1' and pro_codigo =".$row['pro_codigo'];*/
					if($row['dose_um'] == 'S'){
						$sql_usu = "select to_char(vac_data,'dd/mm/yyyy') as data, vac.*, u.uni_desc from vacina_usuario AS vac LEFT JOIN unidade AS u 
   										ON u.uni_codigo = CAST(vac.vac_unidade AS bigint) where usu_codigo = '$paciente' and vac_dose = '1' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						$class = "dose_um";
						
						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//	echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\">$acao</td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff dose_um'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_dois'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,vac.*, u.uni_desc from vacina_usuario AS vac LEFT JOIN unidade AS u 
   										ON u.uni_codigo = CAST(vac.vac_unidade AS bigint) where usu_codigo = '$paciente' and vac_dose = '2' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						$class = "dose_dois";

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff dose_dois'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_tres'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,vac.*, u.uni_desc from vacina_usuario AS vac LEFT JOIN unidade AS u 
   										ON u.uni_codigo = CAST(vac.vac_unidade AS bigint) where usu_codigo = '$paciente' and vac_dose = '3' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						$class = "dose_tres";

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff dose_tres'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_quatro'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,vac.*, u.uni_desc from vacina_usuario AS vac LEFT JOIN unidade AS u 
   										ON u.uni_codigo = CAST(vac.vac_unidade AS bigint) where usu_codigo = '$paciente' and vac_dose = '4' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						$class = "dose_quatro";

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff dose_quatro'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_cinco'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,vac.*, u.uni_desc from vacina_usuario AS vac LEFT JOIN unidade AS u 
   										ON u.uni_codigo = CAST(vac.vac_unidade AS bigint) where usu_codigo = '$paciente' and vac_dose = '5' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						$class = "dose_cinco";

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff dose_cinco'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['reforco'] == 'S'){
						$sql_usu = "SELECT to_char(vac.vac_data,'dd/mm/yyyy') AS data,
										   vac.*,
										   u.uni_desc
									  FROM vacina_usuario AS vac
								 LEFT JOIN unidade AS u 
   										ON u.uni_codigo = CAST(vac.vac_unidade AS bigint)
									 WHERE usu_codigo = '$paciente' 
									   AND vac_dose = '6' 
									   AND pro_codigo = ".$row['pro_codigo']."
									 ORDER BY vac_data DESC";
						$qry_vac = pg_query($sql_usu);
						$total = pg_num_rows($qry_vac);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						$class = "reforco";
						if( $total > 1){
							$class .= " variosReforcos";	
						#echo "<td><pre>".$row_vac['vac_unidade']."\n".print_r($row_vac,1);exit;
						}		


						include $_SESSION[root].$_SESSION[modulo]."case.php";
						
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff reforco'> </td>";
					}
					$cont2++;
						
						if($lote != ''){
							echo "<td id='vacina".$cont.$cont2."' align=center class='outras'> <b> $lote / $qtde</b></td>";	
						}else{
							echo "<td id='vacina".$cont.$cont2."' align=center class='outras'> </td>";
						}
						
						
						$cont2++;
//						$sqlDose = "SELECT * 
//									  FROM itens_movimento 
//									 WHERE pro_codigo = $row[pro_codigo] 
//									   AND ite_lote = '$lote'";
//						$querySqlDose = pg_query($sqlDose);
//						$res = pg_fetch_array($querySqlDose);
//						
//						$sqlVerefica = "SELECT cont_dose 
//										  FROM controlefracionado 
//										 WHERE set_codigo = $set_codigo 
//										   AND ite_codigo = $res[ite_codigo] 
//										   AND cont_dose > 0 ";
//						$queryVerifica = pg_query($sqlVerefica);
//						$res3 =  pg_fetch_array($queryVerifica);
//						$dose = $res3[cont_dose];
						//echo $dose2."aaaaaa";
						
						if($dose2 > 0){
							echo"<td id='vacina".$cont.$cont2."' class='outras'>  
							  <b>$dose2</b>
							  </td>";
						}else{
							echo"<td id='vacina".$cont.$cont2."' class='outras'>
							<a href='#' onclick=\"return abrir('$row[pro_codigo]','$lote',$id_login,'$validade','vacina".$cont.$cont2."');\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/aberto.png' title='Abrir Frasco'></a>
							  </td>";
						}
							echo"<td id='vacina".$cont.$cont2."' class='outras'>
							<a href='#' onclick=\"return abrir('$row[pro_codigo]','$lote',$id_login,'$validade','vacina".$cont.$cont2."','D');\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/fechado.png' title='Descartar Frasco'></a>
							  </td>";
				echo"</tr>";
			$conta++;
		}
			
echo"
</table>";
?>
