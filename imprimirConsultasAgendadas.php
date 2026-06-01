<?php
	include 'global.php';
	
	$data_inicial = $_GET['di'];
	$data_final = $_GET['df'];
	
	if($data_inicial == null){
		$where = "age_data = '$data_final'";
	}else if($data_final == null){
		$where = "age_data = '$data_inicial'";
	}else if($data_inicial != null && $data_final != null){
		$where = "age_data >= '$data_inicial' and age_data <= '$data_final'";
	}
	
	$uni_codigo = $_GET["uni_codigo"];
	
	if($uni_codigo != 0){
	
		$whereUni = "AND ag.uni_codigo = $uni_codigo";
		
	}
	
	if($med_codigo != 0){
	
		$whereUni .= " AND med_codigo = $med_codigo";
		//die($whereUni);
	}
	
	//die($whereUni);
	$sql = "select age_codigo,
					   ag.usu_codigo, 
					   age_atendido,
					   age_horario,
					   usu_fone,
					   usu_celular,
					   TO_CHAR(ag.age_data,'DD/MM/YYYY') as age_data,
					   (CASE
							when age_atendido = 'S' then 'Recepcionado'
							when age_atendido = 'A' then 'Atendido'
							when age_atendido = 'N' then 'Agendado'
							when age_atendido = 'T' then 'Transferido'
							when age_atendido = 'F' then 'Faltoso'
					   END) as age_atendido_texto, 
					   usr_codigo_cad, 
					   usr_codigo_alt, 
					   age_item,
					   age_paciente,
					   TO_CHAR(age_data,'dd/mm/yyyy') as age_data,
					   (select usu_nome from usuario u2 where usu_codigo = d.usu_codigo_responsavel) as usu_responsavel_dom,
					   usu_nome_resp,
					   to_char(usu_datanasc,'dd/mm/yyyy') as usu_datanasc
				  from agendamento as ag
				  join usuario u
				    on u.usu_codigo = ag.usu_codigo
			      left join domicilio d
				    on d.dom_codigo=u.dom_codigo
				 where 
				    1=1
			        and $where
					$whereUni
				 order by age_ordem,age_hora,age_codigo";
				 //die($sql);
	$exec = pg_query($sql);
	
	$selectMedico = "SELECT * FROM USUARIOS WHERE usr_codigo = {$_GET['med_codigo']}";
	$queryMedico = pg_query($selectMedico);
	$regMedico = pg_fetch_array($queryMedico);
	
	$queryMesesUsuario = pg_query($mesesUsuario);
	$regMesesUsuario = pg_fetch_array($queryMesesUsuario);
	$idadeMeses = $regMesesUsuario[meses];
	
	$secretaria = "SELECT * FROM secretaria";
	$querySecretaria = pg_query($secretaria);
	$regSecretaria = pg_fetch_array($querySecretaria);
		echo 
		"<table>
			<tr>
				<td>";
					echo 
					"<table style='border: solid 1px'>
						<tr style='border: solid 1px'>
							<td width=90 style='border: solid 1px' align='center'>
								<img src='imgs/brasao.jpg'>
							</td>
							<td align=center width=1000 style='border: solid 1px'>
								<b>$regSecretaria[nome_secretaria]</b>
								<br />
								<b>Listagem de Consultas Agendadas</b>								
							</td>
							
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					Atendente: $regMedico[usr_nome]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					if($data_inicial != null || $data_final != null){
						echo "Per&iacute;odo:$data_inicial a $data_final";
					}
			echo"		
					
				</td>
				
			</tr>
			<tr style=''>
				<td>
					<table border=0>
						<tr style=''>
							<td width=40 style=''>
								<b>Seq.</b>
							</td>
							<td width=50 style=''>
								<b>Pront.</b>
							</td>
							<td width=350 style=''>
								<b>Nome</b>
							</td>
							<td width=350 style=''>
								<b>Fone</b>
							</td>
							<td width=350 style=''>
								<b>Respons&aacute;vel</b>
							</td>
							<td style='border: solid 1px'>
								<b>D.Nascimento</b>
							</td>
							<td width=200 style='border: solid 1px'>
								<b>Data Age./Hora</b>
							</td>
						</tr>";
						$i = 0;
						while($reg = pg_fetch_array($exec)){
							$i++;
							$sqlUsuProntuario = "SELECT usu_prontuario,usu_nome FROM usuario WHERE usu_codigo = $reg[usu_codigo]";
							$queryUsuProntuario = pg_query($sqlUsuProntuario);
							$ru = pg_fetch_array($queryUsuProntuario);
							echo 
								"<tr style='border: solid 1px'>
									<td align=center  style='font-size:14px'>
										$i
									</td>
									<td align=center  style='font-size:14px';>
										$ru[usu_prontuario]
									</td>
									<td style='width:300px;  font-size:14px';>
										$ru[usu_nome]&nbsp;
									</td>
									<td style='width:300px;  font-size:14px';>
										$reg[usu_fone]/$reg[usu_celular]&nbsp;
									</td>
									<td style='width:40px; '>";
										if($reg[usu_responsavel_dom]){
											echo $reg[usu_responsavel_dom];
										}else{
											echo $reg[usu_nome_resp];
										}
										
										
							echo "
									</td>
									<td style=''>
										$reg[usu_datanasc]
									</td>
									<td style=''>
										$reg[age_data]/".substr($reg[age_horario],0,5)."
									</td>
								</tr>";
						}
						echo "
					</table>
				</td>
			</tr>
		</table>";
?>