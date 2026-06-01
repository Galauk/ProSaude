<link href="estilo.css" rel="stylesheet" type="text/css" />
<link href="estilo2.css" rel="stylesheet" type="text/css" />
<link href="tabela.css" rel="stylesheet" type="text/css" />
<?
/*
*****LEGENDA DAS VACINAS*********
A - Aplicada
P - PREENCHIDA
Z - APRAZADA
C - CANCELADA
*/
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$paciente = $_GET['paciente'];
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
			<b>Lotes
			Quantidades</b>
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
		//echo $sql;
		$consulta = pg_query($sql);
		//fim dos sql's das colunas.

		$cont = 0;
		$cont2 = 1;


		while($row = pg_fetch_array($consulta) )
		{
			echo"<tr>
					<td class='corTdOn'><b>{$row['pro_nome']}</b>
						<input type='hidden' name='pro_codigo$cont' id='pro_codigo$cont' value='$row[pro_codigo]'>
					</td>";
					
					//echo "<script>alert('a $acao');  </script>";
					if($row['dose_um'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,* from vacina_usuario where usu_codigo = '$paciente' and vac_dose = '1' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];
						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//	echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\">$acao</td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_dois'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,* from vacina_usuario where usu_codigo = '$paciente' and vac_dose = '2' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_tres'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,* from vacina_usuario where usu_codigo = '$paciente' and vac_dose = '3' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_quatro'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,* from vacina_usuario where usu_codigo = '$paciente' and vac_dose = '4' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['dose_cinco'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,* from vacina_usuario where usu_codigo = '$paciente' and vac_dose = '5' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff'> </td>";
					}
					$cont2++;
					//////////////////////////////////////
					if($row['reforco'] == 'S'){
						$sql_usu = "select  to_char(vac_data,'dd/mm/yyyy') as data,* from vacina_usuario where usu_codigo = '$paciente' and vac_dose = '6' and pro_codigo =".$row['pro_codigo'];
						$qry_vac = pg_query($sql_usu);
						$row_vac = pg_fetch_array($qry_vac);
						$acao = $row_vac['vac_acao'];

						include $_SESSION[root].$_SESSION[modulo]."case.php";
						//echo "<td id='vacina".$cont.$cont2."' class='dosesVacina' onclick=\"return selecionaData('vacina".$cont.$cont2."');\"></td>";	
					}else{
						echo"<td id='vacina".$cont.$cont2."' class='dosesVacinaOff'> </td>";
					}
					$cont2 = 1;

						echo "<td class='loteQnt'> </td>
				</tr>";
			$cont++;
		}
			
echo"
</table>";
?>
