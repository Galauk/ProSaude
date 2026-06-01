<?php
/**
* @version Anderson 2007-07-05 BRT 10:46:33
* @update	Marcos Ramos 2007-06-21 09:48 
* @update	Leandro 2007-06-19 BRT 10:11:62
* @author Anderson 16/05/2007 08:50
* @brief Adaptando a query e o layout para gerar o relatorio
* competência no cabecalho e db_query( $stmt, $LOG = false )
* esta listando o mesmo paciente e o mesmo AIH porque nao esta filtrando por aih_ativo=S - querys alteradas | total de aih por prestador e total geral;
*/
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";

$Tit="Relacao de AIH por prestador";
$dtIni=$data_ini;
$dtFin=$data_fim;
//$btprint=1;
$dados_compet = 'COMPETENCIA: '.$_GET['mes_comp'].'/'.$_GET['ano_comp'];
include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

$colunas = "<table width='100%' border=0 style=\"font-size:12px;font-family:Tahoma,Arial;\">
				<tr class='nome_coluna'><td><b>Prestador</b></td>
					<td><b>Paciente</b></td>
					<td align='center'><b>AIH</b></td></tr>";

$med_codigo = $_GET["prestador"];
$paciente = $_GET['paciente'];
$mes_comp = $_GET['mes_comp'];
$ano_comp = $_GET['ano_comp'];
$total_por_unidade=0;
$total_geral=0;
	if (isset($med_codigo) and ($med_codigo==-1)) {
		if ($paciente == "") {
			// Todos os pacientes
			
			/*		$sql_statement = "SELECT a.med_nome, b.usu_nome, c.med_codigo_solicitante, c.aih_numero_aih
			FROM medico a, usuario b, aih c
			WHERE c.med_codigo_solicitante = a.med_codigo
			AND b.usu_codigo = c.usu_codigo
			AND c.aih_mes_compet = $mes_comp AND c.aih_ano_compet = $ano_comp
			AND c.aih_ativo='S'  
			ORDER BY med_codigo,b.usu_nome"; 
			*/
			//echo $sql_statement;		
			
			$sql_statement = "select solicitante as med_nome, 
									 pac_nome as usu_nome, 
									 aih_numero_aih,
									 med_codigo_solicitante
								from view_aih
							   where aih_mes_compet = $mes_comp  and aih_ano_compet = $ano_comp
								 and aih_ativo='S'
							   order by med_codigo_solicitante,usu_nome";		
			
			$sql = db_query($sql_statement, $LOG = false);
			echo $colunas;
			$total_geral=pg_num_rows($sql);
			while ($reg = pg_fetch_array($sql)) {
				$codigo = ($reg["med_codigo_solicitante"] == $codigo) ? false: true;
				if ($codigo) {
					echo $total_por_unidade=($total_por_unidade==0) ? '' : "<tr><td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td></tr>";
					echo "<tr>
							<td colspan=3><hr>
							</td>
						  </tr>
						  <tr>
							<td><b>$reg[med_nome]</b></td>
							<td>$reg[usu_nome]</td>
							<td align='right'>$reg[aih_numero_aih]</td>
						  </tr>";
					$total_por_unidade = 0;
				} else {
					echo "
					<tr>					
						<td></td>
						<td>$reg[usu_nome]</td>
						<td align='right'>$reg[aih_numero_aih]</td>
					</tr>
					";
				}
				$codigo=$reg["med_codigo_solicitante"];
				$total_por_unidade++;
			}
			echo "<tr>
					<td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td>
				  </tr>";
			echo "<tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan='3'><hr></td>
				  </tr>
				  <tr>
					<td><b>Total Geral = ".$total_geral."</b></td>
				  </tr>";
		 echo "</table>";
		} else {
			// Paciente determinado
			/*
			$sql_statement = "SELECT a.med_nome, b.usu_nome, c.med_codigo_solicitante, c.aih_numero_aih
			FROM medico a, usuario b, aih c
			WHERE c.med_codigo_solicitante = a.med_codigo
			AND b.usu_codigo = c.usu_codigo
			AND c.usu_codigo = $paciente
			AND c.aih_mes_compet = $mes_comp AND c.aih_ano_compet = $ano_comp
			AND c.aih_ativo='S'  				
			ORDER BY med_codigo,b.usu_nome";*/
			$sql_statement ="select solicitante as med_nome, 
									pac_nome as usu_nome, 
									aih_numero_aih,
									med_codigo_solicitante
							   from view_aih					
							  where aih_mes_compet = $mes_comp  and aih_ano_compet = $ano_comp
								and pac_codigo = $paciente
								and aih_ativo='S'
							  order by med_codigo_solicitante,usu_nome";
			$sql = db_query($sql_statement, $LOG = false);
			echo $colunas;
			$total_geral=pg_num_rows($sql);
			while ($reg = pg_fetch_array($sql)) {
				$codigo = ($reg["med_codigo_solicitante"] == $codigo) ? false: true;
				if ($codigo) {
					echo $total_por_unidade=($total_por_unidade==0) ? '' : "<tr><td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td></tr>";
					echo "<tr>
							<td colspan=3><hr></td>
						  </tr>
						  <tr>
							<td>$reg[med_nome]</td>
							<td>$reg[usu_nome]</td>
							<td align='right'>$reg[aih_numero_aih]</td>
						  </tr>";
					$total_por_unidade=0;
				} else {
					echo "
					<tr>					
						<td></td>
						<td>$reg[usu_nome]</td>
						<td align='right'>$reg[aih_numero_aih]</td>
					</tr>
					";
				}
				$codigo = $reg["med_codigo_solicitante"];
				$total_por_unidade++;			
			}
			echo "<tr>
					<td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td>
				  </tr>";
			echo "<tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan='3'><hr></td>
				  </tr>
				  <tr>
					<td><b>Total Geral = ".$total_geral."</b></td>
				  </tr>";
		echo "</table>";
		}
	} else {
		if ($paciente == "") {
			// Todos os pacientes
			$sql_statement = "SELECT a.med_nome, 
									 b.usu_nome, 
									 c.med_codigo_solicitante, 
									 c.aih_numero_aih
								FROM medico a, 
									 usuario b, 
									 aih c
							   WHERE c.med_solicitante_proc = a.med_codigo
								 AND c.med_solicitante_proc = $med_codigo 
								 AND b.usu_codigo = c.usu_codigo
								 AND c.aih_mes_compet = $mes_comp 
								 AND c.aih_ano_compet = $ano_comp
								 AND c.aih_ativo='S'  				
							   ORDER BY med_codigo,b.usu_nome"; 
			if(pg_num_rows(pg_query($sql_statement))==0) { 
				$sql_statement = "SELECT a.med_nome, 
									b.usu_nome, 
									c.med_codigo_solicitante, 
									c.aih_numero_aih
										FROM aih c
									join medico a
									on c.med_codigo_solicitante = a.med_codigo
									left join usuario b
									on  b.usu_codigo = c.usu_codigo
										WHERE c.med_codigo_solicitante = $med_codigo
									AND c.aih_mes_compet = $mes_comp
									AND c.aih_ano_compet = $ano_comp
									AND c.aih_ativo='S'  				
									ORDER BY med_codigo,b.usu_nome"; 
	}
		
/*		$sql_statement="select solicitante as med_nome, pac_nome as usu_nome, aih_numero_aih,med_codigo_solicitante
from view_aih
where aih_mes_compet = $mes_comp  and aih_ano_compet = $ano_comp
and med_codigo_solicitante = $med_codigo
and aih_ativo='S'
order by med_codigo_solicitante,usu_nome";
*/
			$sql = db_query($sql_statement, $LOG = false);
			echo $colunas;
			$total_geral=pg_num_rows($sql);
			while ($reg=pg_fetch_array($sql)){
				$codigo = ($reg["med_codigo_solicitante"] == $codigo) ? false: true;
				if ($codigo) {
					echo $total_por_unidade=($total_por_unidade==0) ? '' : "<tr><td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td></tr>";
					echo "<tr>
							<td colspan=3><hr></td>
						  </tr>
						  <tr>
							<td>$reg[med_nome]</td>
							<td>$reg[usu_nome]</td>
							<td align='right'>$reg[aih_numero_aih]</td>
						  </tr>";
					$total_por_unidade=0;
				} else {
					echo "
					<tr>					
						<td></td>
						<td>$reg[usu_nome]</td>
						<td align='right'>$reg[aih_numero_aih]</td>
					</tr>";
				}
				$codigo = $reg["med_codigo_solicitante"];
				$total_por_unidade++;			
			}
			echo "<tr><td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td></tr>";
			echo "<tr><td>&nbsp;</td></tr><tr><td colspan='3'><hr></td></tr><tr><td><b>Total Geral = ".$total_geral."</b></td></tr>";						
			echo "</table>";
		} else {
// Um paciente determinado
/* $sql_statement = "SELECT a.med_nome, b.usu_nome, c.med_codigo_solicitante, c.aih_numero_aih
FROM medico a, usuario b, aih c
WHERE c.med_codigo_solicitante = a.med_codigo
AND c.med_codigo_solicitante = $med_codigo
AND b.usu_codigo = c.usu_codigo
AND c.usu_codigo = $paciente
AND c.aih_mes_compet = $mes_comp AND c.aih_ano_compet = $ano_comp
AND c.aih_ativo='S'  				
ORDER BY med_codigo,b.usu_nome"; */
			$sql_statement= "select solicitante as med_nome, 
									pac_nome as usu_nome, 
									aih_numero_aih,
									med_codigo_solicitante
							   from view_aih
							  where aih_mes_compet = $mes_comp 
							    and aih_ano_compet = $ano_comp
							    and med_codigo_solicitante = $med_codigo
							    and pac_codigo = $paciente
							    and aih_ativo='S'
							  order by med_codigo_solicitante,usu_nome";
			$sql = db_query($sql_statement, $LOG = false);
			echo $colunas;
			$total_geral=pg_num_rows($sql);
			while ($reg=pg_fetch_array($sql)){
				$codigo = ($reg["med_codigo_solicitante"] == $codigo) ? false: true;
				if ($codigo) {
					echo $total_por_unidade=($total_por_unidade==0) ? '' : "<tr><td><b>Total de AIH do prestador = ".$total_por_unidade."	</b></td></tr>";
					echo "<tr>
							<td colspan=3><hr></td>
						  </tr>
						  <tr>
							<td>$reg[med_nome]</td>
							<td>$reg[usu_nome]</td>
							<td align='right'>$reg[aih_numero_aih]</td>
						  </tr>";
					$total_por_unidade=0;
				} else {
					echo "
					<tr>					
						<td></td>
						<td>$reg[usu_nome]</td>
						<td align='right'>$reg[aih_numero_aih]</td>
					</tr>
					";
				}
				$codigo = $reg["med_codigo_solicitante"];
				$total_por_unidade++;			
			}
			echo "<tr>
					<td><b>Total de AIH do prestador = ".$total_por_unidade."</b></td>
				  </tr>";
			echo "<tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
				  	<td colspan='3'><hr></td>
				  </tr>
				  <tr>
				  	<td><b>Total de Geral = ".$total_geral."</b></td>
				  </tr>";
			echo "</table>";
		}
	}

?>
