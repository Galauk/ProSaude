<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";
	
	$json = new Services_JSON;
	
	switch($_GET["acao"])
	{
		
		case "especialidade":
		
			if( $_GET["controle"] == 0)
			{
				$sql = "select me.esp_codigo, 
							   e.esp_nome
						  from medico_especialidade me, 
						  	   especialidade e
						 where me.esp_codigo = e.esp_codigo
						   and me.med_codigo = {$_GET['med_codigo']}";
 			} else {
				$sql = "select me.esp_codigo, 
							   e.esp_nome
						  from medico_especialidade me, 
						  	   especialidade e
						 where me.esp_codigo = e.esp_codigo
						   and me.med_codigo = {$_GET['med_codigo']}";
			}
			//echo "<pre>$sql</pre>"; 
			$exec = db_query($sql);
			
			$resp = array();
			
			while($row = pg_fetch_array($exec))
			{
				$resp[] = $row;
			}
			
			$resp = $json->encode( $resp );
			
			echo $resp;
		
		break;
		
		case "agendados":
		
			echo "
			<table width='100%' align='center' cellspacing='0' cellpadding='4' border='0' class='lista'>
				<thead>
				<tr>
					<th width='70'>
						C&oacute;digo Pac.
					</th>
					<th width='200'>
						Paciente
					</th>
					<th width='50'>
						Idade
					</th>
					<th>
						M&atilde;e
					</th>
					<th width='70'>
						Situa&ccedil;&atilde;o
					</th>
					<th width='80'>
						Munic&iacute;pio
					</th>
					<th width='40'>
						Hor&aacute;rio
					</th>
					<th width='40'>
					       Tipo Agend.	
					</th>
					<th width='120' colspan='5'>Op&ccedil;&otilde;es</td>
				</tr>
				</thead>
				<tbody>";
				
			$age_data = ( (!$_GET["age_data"]) ? date("d/m/Y") : $_GET["age_data"] );
		
			$sql = "select age_codigo,
						   usu_codigo, 
						   age_atendido,
						   age_hora,
						   (CASE
								when age_atendido = 'S' then 'Recepcionado'
								when age_atendido = 'A' then 'Atendido'
								when age_atendido = 'N' then 'Agendado'
								when age_atendido = 'T' then 'Transferido'
								when age_atendido = 'F' then 'Faltoso'
						   END) as age_atendido_texto, 
						   usr_codigo_cad, 
						   usr_codigo_alt, 
						   age_item
					  from agendamento
					 where age_data = '$age_data'
					   and med_codigo = {$_GET['med_codigo']}
					   and esp_codigo = {$_GET['esp_codigo']}
					   and uni_codigo = {$_GET['uni_codigo']}
					 order by age_ordem,age_hora,age_codigo";
			//echo $sql."<br/>";
			$exec = pg_query($sql);
			
			if(pg_num_rows($exec) == 0)
			{
				echo "
				<tr>
					<td colspan='13'>Nenhum Agendamento Para Esta Data</td>
				</tr>";
			}
			
			$style = "style='cursor: pointer;'";
			
			while($row = pg_fetch_array($exec))
			{
				
				$sql = "select to_char(dt_atualizacao,'HH24:MI') as hr_atualizacao,
							   to_char(dt_atualizacao,'DD/MM/YYYY') as dt_atualizacao,
							   to_char(dt_cadastro,'HH24:MI') as hr_cadastro,
							   to_char(dt_cadastro,'DD/MM/YYYY') as dt_cadastro
						  from agendamento 
						 where age_codigo = $row[age_codigo]";
				
				$data = db_getRow($sql);
				
				$data_hora_alt = $data[1]." - ".$data[0];
				$data_hora_cad = $data[3]." - ".$data[2];
				
				if(!empty($row["usr_codigo_cad"]))
				{
					$sql_usu_cad = "select usr_nome
									  from usuarios
									 where usr_codigo = $row[usr_codigo_cad]";
									
					$exec_sql = db_query($sql_usu_cad);
					
					$usu_cad = pg_fetch_array($exec_sql);
				}
				if(!empty($row["usr_codigo_alt"]))
				{
					$sql_usu_alt = "select usr_nome
									  from usuarios
									 where usr_codigo = $row[usr_codigo_alt]";
									
					$exec_sql = db_query($sql_usu_alt);
					
					$usu_alt = pg_fetch_array($exec_sql);
				}
				
				$usu_cadastro = $usu_cad[0]. " - " . $data_hora_cad;
				$usu_alterado = $usu_alt[0]. " - " . $data_hora_alt;
				
				$sql = "SELECT a.usu_codigo, 
					           a.usu_nome, 
					       	   a.usu_mae, 
					       	   a.usu_datanasc, 
					       	   b.cid_nome 
					  	  FROM usuario a 
					      LEFT JOIN  cidade b 
					        ON a.muni_cd_cod_ibge_resid = b.cid_codigo_ibge 
					 	 WHERE a.usu_codigo = {$row['usu_codigo']}";
				//echo $sql;		
				$exec_sql = db_query($sql);
				
				$linha = pg_fetch_array($exec_sql);
				
				$idade = verIdade($linha['usu_datanasc']);
				
				if($row["age_atendido"] == "A")
				{
					echo "<tr class='green'>";
				} else if($row["age_atendido"] == "S") {
					echo "<tr class='blue'>";
				} else if($row["age_atendido"] == "N") {
					echo "<tr class='black'>";
				} else if($row["age_atendido"] == "T") {
					echo "<tr class='orange'>";
				} else if($row["age_atendido"] == "F") {
					echo "<tr class='red'>";
				} else {
					echo $row["age_atendido"];
				}
				
				echo "
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
					<input type=\"hidden\" name=\"ordem[]\" value=\"{$row['age_codigo']}\" />
						$linha[usu_codigo]
					</td>
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
						$linha[usu_nome]
					</td>
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
						$idade
					</td>
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
						$linha[usu_mae]
					</td>
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>";
					
					echo $row["age_atendido_texto"];
					echo "
					</td>
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
						$linha[cid_nome]
					</td>";
					echo "
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
						$row[age_hora]
					</td>";
					echo "
					<td onclick=\"colocar_informacao('$usu_cadastro', '$usu_alterado')\" $style>
						$row[age_item]
					</td>";
				$age_data = explode("/",$age_data);
				$age_data = (int) $age_data[2].$age_data[1].$age_data[0];
				if($age_data == (int) date("Ymd"))
				{		
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"opcao('R',$row[age_codigo]);\" style='cursor:pointer' />
					</td>";
				} else {
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg' />
					</td>";
				}
				if($age_data >= (int) date("Ymd"))
				{
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"opcao('T',$row[age_codigo]);\" style='cursor:pointer'>
					</td>";
				} else {
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg'>
					</td>";
				}
				if($age_data >= (int) date("Ymd"))
//				if($age_data == (int) date("Ymd"))
				{
					
						$aux = "salvar";
				       if($age_data == (int) date("Ymd"))
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"opcao('F',$row[age_codigo]);\" style='cursor:pointer'>
					</td> ";
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg' onclick=\"salvarFaltaMedico($_GET[med_codigo], $row[age_codigo], $id_login, '$aux');\" style='cursor:pointer'>
					</td>";
				} else {
					echo "
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg'>
					</td>
					<td>
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_off.jpg'>
					</td>";
				}
				echo "
				</tr>";
			}
			
			echo "</tbody>
			</table>";
			
		break;
		
		case "opcao":
		
			$sql = "select *
					from agendamento
					where age_codigo = {$_GET['age_codigo']}";
			
			$age = db_getrow($sql);
			
			$esp_codigo = $age["esp_codigo"];
			
			$med_codigo = $age["med_codigo"];
			
			$uni_codigo = $age["uni_codigo"];
			
			$age_data = ( (!$_GET["age_data"]) ? date("d/m/Y") : $_GET["age_data"] );
		
			switch($_GET["opcao"])
			{
				
				case "R":
					
					$tipo_age = ( ($age['age_atendido'] == "S") ? "N" : "S" );
					
					if($age['age_atendido'] == 'F' || $age['age_atendido'] == 'T')
					{
						$sql1 = "select gra_qtde
								from grade_medico
								where med_codigo = $med_codigo
								and esp_codigo = $esp_codigo
								and uni_codigo = $uni_codigo
								and gra_data = '$age_data'";
				
						$exec1 = db_query($sql1);
				
						$row = pg_fetch_array($exec1);
						
						$sql2 = "select count(age_codigo) as qtde
								from agendamento
								where med_codigo = $med_codigo
								and esp_codigo = $esp_codigo
								and uni_codigo = $uni_codigo
								and age_data = '$age_data'
								and age_atendido in ('N', 'S', 'A')";
								
						$exec2 = db_query($sql2);
						
						$linha = pg_fetch_array($exec2);
				
						if($row["gra_qtde"] <= $linha["qtde"])
						{
							echo "false";
							exit();
						}
					}
					
					$sql = "update agendamento set
							age_atendido='$tipo_age',
							usr_codigo_alt='$id_login',
							dt_atualizacao=NOW(),
							age_timestamp=".($tipo_age == "S" ? "CURRENT_TIMESTAMP" : 'null')."
							where age_codigo='$age_codigo'";
					
					$sql = db_query($sql);
				
				break;
			
				case "F":					
				
					$sql = "select * from agendamento where age_codigo='$age_codigo'";
					
					$exec = db_query($sql);
					
					$rr = pg_fetch_array($exec);
					
					if($rr['age_atendido'] == "F")
					{
//ALTERADO - MARCO AURELIO 24/04		$sql1 = "select gra_qtde
						$sql1 = "select sum(gra_qtde) as gra_qtde
								from grade_medico
								where med_codigo = $med_codigo
								and esp_codigo = $esp_codigo
								and uni_codigo = $uni_codigo
								and gra_data = '$age_data'";
				
						$exec1 = db_query($sql1);
				
						$row = pg_fetch_array($exec1);
						
						$sql2 = "select count(age_codigo) as qtde
								from agendamento
								where med_codigo = $med_codigo
								and esp_codigo = $esp_codigo
								and uni_codigo = $uni_codigo
								and age_data = '$age_data'
								and age_atendido in ('N', 'S', 'A')";
								
						$exec2 = db_query($sql2);
						
						$linha = pg_fetch_array($exec2);
						
						//if($row["gra_qtde"] <= $linha["qtde"])
						//ALTERAMOS AQUI - MARCO E LUCIO
						//PROBLEMA DENTISTA
						if($row["gra_qtde"] < $linha["qtde"])
						{
							echo "false";
							exit();
						}
						
						$tipo_age="N";
						
						reglog($id_login,"Desmarcado Falta Paciente: $rr[usu_codigo]");
						
						$vc=explode("/",$age_data);
						
						$ndata = $vc[1]."-".$vc[2];      
						
						$sql = "select *
								from grade_mensal
								where med_codigo = $med_codigo
								and agt_codigo = 384931
								and esp_codigo = $esp_codigo
								and '$age_data' between grm_periodo and grm_periodo+30";
						
						$exec = db_query($sql);
						
						$sel = pg_fetch_array($exec);
						
						if($sel[grm_qtde]<=1)
						{
							$qtde = "0";
						} else {
							$qtde = ($sel[grm_qtde]-1);
						}
						
						$sql = "update grade_mensal set
								grm_qtde = $qtde
								where grm_codigo = $sel[grm_codigo]";
						
						$qur = db_query($sql);
			
					} else {
						
						$tipo_age="F";
						
						reglog($id_login,"Marcado Falta Paciente: $rr[usu_codigo]");
						
						$vc=explode("/",$age_data);
						
						$ndata = $vc[1]."-".$vc[2];
						
						$sql = "select *
								from grade_mensal
								where med_codigo = $med_codigo
								and agt_codigo = 384931
								and esp_codigo= $esp_codigo
								and '$age_data' between grm_periodo and grm_periodo+30";
						
						$exec = db_query($sql);
						
						$sel = pg_fetch_array($exec);
						
						$qtde = ($sel[grm_qtde]+1);
						
						$sql = "update grade_mensal set
								grm_qtde = $qtde 
								where grm_codigo = $sel[grm_codigo]";
						
						$qur = db_query($sql);
			
					}
					
					$sql = "update agendamento set
							age_atendido='$tipo_age',
							usr_codigo_alt='$id_login',
							dt_atualizacao=NOW(),
							age_timestamp=null
							where age_codigo='$age_codigo'";
					echo $sql;
					
					$sql = db_query($sql);
				
				break;
			
				case "T":
					
					$sql = "select * from agendamento where age_codigo = $age_codigo";
					
					$exec = db_query($sql);
					
					$rr=pg_fetch_array($exec);
					
					reglog($id_login,"Cancelado ou Recepcionado Paciente: $rr[usu_codigo]");
					
					if($rr['age_atendido']=="T")
					{
						$sql1 = "select gra_qtde
								from grade_medico
								where med_codigo = $med_codigo
								and esp_codigo = $esp_codigo
								and uni_codigo = $uni_codigo
								and gra_data = '$age_data'";
				
						$exec1 = db_query($sql1);
				
						$row = pg_fetch_array($exec1);
						
						$sql2 = "select count(age_codigo) as qtde
								from agendamento
								where med_codigo = $med_codigo
								and esp_codigo = $esp_codigo
								and uni_codigo = $uni_codigo
								and age_data = '$age_data'
								and age_atendido in ('N', 'S', 'A')";
								
						$exec2 = db_query($sql2);
						
						$linha = pg_fetch_array($exec2);
						
						if($row["gra_qtde"] <= $linha["qtde"])
						{
							echo "false";
							exit();
						}
						
						reglog($id_login,"Desmarcado Transferencia Paciente: $rr[usu_codigo]");
						
						$tipo_age="N";
						
						$vc=explode("/",$age_data);
						
						$ndata = $vc[1]."-".$vc[2];
						
						$sql = "select *
								from grade_mensal
								where med_codigo = $med_codigo
								and agt_codigo = 393519
								and esp_codigo = $esp_codigo
								and '$age_data' between grm_periodo and grm_periodo+30";
						
						$exec = db_query($sql);
						
						$sel = pg_fetch_array($exec);
						
						if($sel['grm_qtde']<=1)
						{
							$qtde = "0";
						} else {
							$qtde = ($sel['grm_qtde']-1);
						}
						
						$sql = "update grade_mensal set
								grm_qtde = $qtde
								where grm_codigo = $sel[grm_codigo]";
						
						$qur = db_query($sql);
						
					} else {
						
						$tipo_age="T";
						
						reglog($id_login,"Transferido Paciente: $rr[usu_codigo]");
						
						$vc=explode("/",$age_data);
						
						$ndata = $vc[1]."-".$vc[2];
						
						$sql = "select *
								from grade_mensal
								where med_codigo = $med_codigo
								and agt_codigo = 393519
								and esp_codigo = $esp_codigo
								and '$age_data' between grm_periodo and grm_periodo+30";
						
						$exec = db_query($sql);
						
						$sel = pg_fetch_array($exec);
						
						$qtde = ($sel['grm_qtde']+1);
						
						$sql = "update grade_mensal set
								grm_qtde = $qtde
								where grm_codigo = $sel[grm_codigo]";
						
						$qur = db_query($sql);
				
					}
					
					$sql = "update agendamento set
					age_atendido = '$tipo_age',
					usr_codigo_alt = '$id_login',
					dt_atualizacao=NOW(),
					age_timestamp=null
					where age_codigo='$age_codigo'";
					
					$sql = db_query($sql);
							
				break;
			
			
			}
	
		break;
		
	}
	
?>
