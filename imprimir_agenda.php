<?php
	include 'global.php';
	$sql = "select age_codigo,
					   ag.usu_codigo, 
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
					   age_item,
					   age_paciente,
					   TO_CHAR(age_data,'dd/mm/yyyy') as age_data,
					   usu_nome_resp
				  from agendamento as ag
				  join usuario u
				    on u.usu_codigo = ag.usu_codigo
				 where age_data = '".($age_data == '/undefined/undefined' ? 'NOW()' : $age_data)."'
				   and med_codigo = {$_GET['med_codigo']}
				   and esp_codigo = {$_GET['esp_codigo']}
				   and uni_codigo = {$_GET['uni_codigo']}
				   and age_atendido != 'A'
				   and age_atendido = 'S'
				 order by age_ordem,age_hora,age_codigo";
//	echo $sql."<br/>";exit;
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
								<b>Ficha Manual de Atendimento</b>								
							</td>
							
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					Atendente: $regMedico[usr_nome]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Data:".($age_data == '/undefined/undefined' ? date('d/m/Y') : $age_data)."
				</td>
				
			</tr>
			<tr style='border: solid 1px'>
				<td>
					<table border=0 style='border: solid 1px'>
						<tr style='border: solid 1px'>
							<td width=40 style='border: solid 1px'>
								<b>Seq.</b>
							</td>
							<td width=50 style='border: solid 1px'>
								<b>Pront.</b>
							</td>
							<td width=350 style='border: solid 1px'>
								<b>Nome</b>
							</td>
							<td width=40 style='border: solid 1px'>
								<b>Idade</b>
							</td>
							<td style='border: solid 1px'>
								<b>Encaminhamentos/Exames</b>
							</td>
							<td width=300 style='border: solid 1px'>
								<b>Diagnostico provavel</b>
							</td>
							<td width=100 style='border: solid 1px'>
								<b>CID.10</b>
							</td>
						</tr>";
						$i = 0;
						while($reg = pg_fetch_array($exec)){
							$anosUsuario = "SELECT usu_sexo, ((DATE_PART('YEAR', AGE(NOW(), usu_datanasc)))) AS anos from usuario WHERE usu_codigo = $reg[usu_codigo]";
							$queryAnosUsuario = pg_query($anosUsuario);
							$regAnosUsuario = pg_fetch_array($queryAnosUsuario);
							$i++;
							$sqlUsuProntuario = "SELECT usu_prontuario FROM usuario WHERE usu_codigo = $reg[usu_codigo]";
							$queryUsuProntuario = pg_query($sqlUsuProntuario);
							$ru = pg_fetch_array($queryUsuProntuario);
							echo 
								"<tr style='border: solid 1px'>
									<td align=center  style='border: solid 1px; font-size:14px'>
										$i
									</td>
									<td align=center  style='border: solid 1px; font-size:14px';>
										$ru[usu_prontuario]
									</td>
									<td style='width:300px; border: solid 1px; font-size:14px';>
										$reg[age_paciente]&nbsp;
									</td>
									<td align=center  style='width:40px; border: solid 1px;'>
										$regAnosUsuario[anos]
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
								</tr>";
						}
						if($i < 18){
							$result = 18 - $i;
							for($j=0;$j<$result;$j++){
								$i++;
								echo 
								"<tr style='border: solid 1px'>
									<td align=center style='border: solid 1px'>
										$i
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
									<td style='border: solid 1px'>
										&nbsp;
									</td>
								</tr>";
								
							}
						}
						echo "
					</table>
				</td>
			</tr>
		</table>";
?>