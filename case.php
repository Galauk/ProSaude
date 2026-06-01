<?php
/*$datas = $row_vac['vac_data'];
$datay = explode("-", $datas);
return $datay[2]."/".$datay[1]."/".$datay[0];
echo $datas."-loplo9ok9i9i9i99ç".$datay;*/
//echo "<pre>".print_r($_REQUEST,1);

//echo $res[ite_codigo]."-".$row[pro_codigo]."<br>";
$sql = "SELECT * 
		  FROM controlefracionado
		 WHERE ite_codigo = $row[pro_codigo]";
$query = pg_query($sql);
$res = pg_fetch_array($query);
$dose = $res[cont_dose];
	switch ($acao){
		case "A":
			
			echo "
				<td id='vacina".$cont.$cont2."' class='dosesAplicados $class' onclick=\"return selecionaData('vacina".$cont.$cont2."',$row[pro_codigo]);\">
					".strtoupper($row_vac['uni_desc'])."<br><b>$row_vac[data]</b>
				</td>";	
			break;
			
		case "Z":
			echo"
				<td id='vacina".$cont.$cont2."' class='dosesAprazadas $class' onclick=\"return selecionaData('vacina".$cont.$cont2."',$row[pro_codigo]);\">
					<b>$row_vac[data]</b>
				</td>";	
			break;
		case "P":
			
			echo"
				<td id='vacina".$cont.$cont2."' class='dosesPreenchidas $class' onclick=\"return selecionaData('vacina".$cont.$cont2."',$row[pro_codigo]);\">
					<b>$row_vac[data]</b>
				</td>";	
			break;
		case "C":
			echo"
				<td id='vacina".$cont.$cont2."' class='dosesPreenchidas $class' onclick=\"return selecionaData('vacina".$cont.$cont2."',$row[pro_codigo]);\">
				</td>";	
			break;
		
		default:
			
			echo "
				<td id='vacina".$cont.$cont2."' class='dosesVacina $class' onclick=\"return selecionaData('vacina".$cont.$cont2."',$row[pro_codigo]);\">					
				</td>";	
			break;
	}
?>