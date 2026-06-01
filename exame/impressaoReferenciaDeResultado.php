<?php 
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
?>
	
<script>
function imprimir()
       {
               window.print();
               //para limpar os campos do agendamento.
               window.opener.limpar();
               //
       }
</script>
	
<?php
$data = explode('-', $datacoleta);
$d1 = $data[0];
$d2 = $data[1];
$d3 = $data[2];
$datacoleta = $d3 ."/". $d2."/".$d1;

//echo "<pre>".print_r($_REQUEST,1); 
$sqlTopo = "SELECT i.proc_codigo as proc_c,
					proc_nome,
					c.med_codigo medico,
					a.usu_codigo,
					u.usu_nome,
					to_char(u.usu_datanasc,'DD/MM/YYYY') as data,
					ai.agei_data,
					ai.agei_status,
					a.age_codigo,
					a.med_codigo AS medico_solicitante,
					a.usr_codigo_medico,
					ai.agei_codigo,
					TO_CHAR(col.col_data_coleta,'DD/MM/YYYY') as col_data_coleta2, 
					med.med_nome,
					m.med_nome as lab,*				
			  FROM medico m 
			  JOIN convenio c
			    ON c.med_codigo = m.med_codigo
			  JOIN convenio_itens i
			    ON i.conv_codigo = c.conv_codigo
			 left JOIN agenda_itens ai
			    ON ai.coni_codigo = i.coni_codigo
			  JOIN agenda a
			    ON a.age_codigo = ai.age_codigo
			  JOIN usuario u
			    ON u.usu_codigo = a.usu_codigo
			  LEFT JOIN usuarios us
			    ON us.usr_codigo = a.usr_codigo_medico
			  JOIN procedimento proc
			    ON proc.proc_codigo = i.proc_codigo
			  JOIN coleta col
			    ON col.agei_codigo = ai.agei_codigo
			  JOIN tipodeexame as tp 
			    ON tp.proc_codigo = i.proc_codigo
			 LEFT JOIN medico med
			    ON med.med_codigo = a.med_codigo
			  lEFT JOIN domicilio as d
					    ON d.dom_codigo = u.dom_codigo 
			WHERE a.age_codigo = $age_codigo 		
			ORDER BY proc_nome";

//echo "<pre>".print_r($sqlTopo,1);
//echo $proc_codigo . "proc";

	$queryTopo = pg_query($sqlTopo);
	$regTopo = pg_fetch_array($queryTopo);
	if(!empty($regTopo[medico_solicitante])){
		$nomeMedico = $regTopo[med_nome];
	}
	if(!empty($regTopo[usr_codigo_medico])){
		$nomeMedico = $regTopo[usr_nome];
	}
	$selectBioquimicos = "SELECT * FROM usuarios 
							WHERE usr_tipo_medico = 'B'";
    $queryBioquimicos  = pg_query($selectBioquimicos);
    $numLinhasBioquimicos = pg_num_rows($queryBioquimicos);
echo "<html>
	<head></head>
	<body onload=imprimir()>";
echo "<table style='width:850px;height:500px;  align='center'>";
		$sqlEndereco = "SELECT * FROM medico WHERE prestador_servico = 'L'";
		$queryEndereco = pg_query($sqlEndereco);
		$registroEndereco = pg_fetch_array($queryEndereco);
		echo "<tr>
				<td valign=top style=\"height:1000px;\" colspan=$numLinhasBioquimicos>
					<table width=100% border =0 cellspacing=4>
						<tr>
							<td colspan=2>
								<font size=6><b>$regTopo[lab]</b></font>.<font size=4>Lab. Municipal de An&aacute;lises Cl&iacute;nicas
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<font size=3>$registroEndereco[med_endereco]</font>
							</td>
						</tr>
						<tr>
							<td>
								<font size=3><b>Telefone:</b>$registroEndereco[med_end_telefone_res] </font>
							</td>
							<td>
								<font size=3><b>Data Coleta:</b>$regTopo[col_data_coleta2]</font>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<font size=3><b>Paciente</b></font>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<fieldset>
								<table border=0 width=70%>
									<tr>
										<td width=80%>
											<font size=3><b>Nome:&nbsp;</b>$regTopo[usu_nome]</font>
										</td>
										<td>
											 <font size=3><b>Sexo:&nbsp;</b>$regTopo[usu_sexo]</font>
										</td>
									</tr>
									<tr>
										<td>
											<font size=3><b>Telefone:&nbsp;</b>$regTopo[dom_telefone]</font>
										</td>
										<td>
											 <font size=3><b>Dt. Nascimento:&nbsp;</b>$regTopo[data]</font>
										</td>
									</tr>
								</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<fieldset>
								<table border=0 width=70%>
									<tr>
										<td width=50% colspan=2>
											<font size=3><b>Medico:&nbsp;</b>$nomeMedico</font>
										</td>
									</tr>
									<tr>
										<td colspan=2>
											 <font size=3><b>Convenio:&nbsp;</b>".($regTopo[usu_desc_plano_saude] == "" ? "SUS</font>" : "$regTopo[usu_desc_plano_saude]</font>")."
										</td>
									</tr>
								</table>
								</fieldset>
							</td>
						</tr>";
		
					echo "<tr>
							<td height=100 colspan=2>";
					$sqlCorpo = "SELECT i.proc_codigo as proc_c,
					proc_nome,
					c.med_codigo medico,
					a.usu_codigo,
					u.usu_nome,
					to_char(u.usu_datanasc,'DD/MM/YYYY') as data,
					ai.agei_data,
					ai.agei_status,
					a.age_codigo,
					a.med_codigo AS medico_solicitante,
					a.usr_codigo_medico,
					ai.agei_codigo,
					TO_CHAR(col.col_data_coleta,'DD/MM/YYYY') as col_data_coleta2, 
					med.med_nome,
					m.med_nome as lab,*				
			  FROM medico m 
			  JOIN convenio c
			    ON c.med_codigo = m.med_codigo
			  JOIN convenio_itens i
			    ON i.conv_codigo = c.conv_codigo
			 left JOIN agenda_itens ai
			    ON ai.coni_codigo = i.coni_codigo
			  JOIN agenda a
			    ON a.age_codigo = ai.age_codigo
			  JOIN usuario u
			    ON u.usu_codigo = a.usu_codigo
			  LEFT JOIN usuarios us
			    ON us.usr_codigo = a.usr_codigo_medico
			  JOIN procedimento proc
			    ON proc.proc_codigo = i.proc_codigo
			  JOIN coleta col
			    ON col.agei_codigo = ai.agei_codigo
			  JOIN tipodeexame as tp 
			    ON tp.proc_codigo = i.proc_codigo
			 LEFT JOIN medico med
			    ON med.med_codigo = a.med_codigo
			  lEFT JOIN domicilio as d
					    ON d.dom_codigo = u.dom_codigo 
			WHERE a.age_codigo = $age_codigo 		
			ORDER BY proc_nome";
					$queryCorpo = pg_query($sqlCorpo);
					while($resCoprpo = pg_fetch_array($queryCorpo)){
						$sql = "SELECT i.proc_codigo as proc_c,
											proc_nome,
											c.med_codigo medico,
											a.usu_codigo,
											u.usu_nome,
											ai.agei_data,
											ai.agei_status,
											a.age_codigo,
											a.med_codigo AS medico_solicitante,
											a.usr_codigo_medico,
											ai.agei_codigo,
											TO_CHAR(col.col_data_coleta,'DD/MM/YYYY') as col_data_coleta2, 
											med.med_nome,*				
										  FROM medico m 
										  JOIN convenio c
										    ON c.med_codigo = m.med_codigo
										  JOIN convenio_itens i
										    ON i.conv_codigo = c.conv_codigo
										 left JOIN agenda_itens ai
										    ON ai.coni_codigo = i.coni_codigo
										  JOIN agenda a
										    ON a.age_codigo = ai.age_codigo
										  JOIN usuario u
										    ON u.usu_codigo = a.usu_codigo
										  LEFT JOIN usuarios us
										    ON us.usr_codigo = a.usr_codigo_medico
										  JOIN procedimento proc
										    ON proc.proc_codigo = i.proc_codigo
										  JOIN coleta col
										    ON col.agei_codigo = ai.agei_codigo
										  JOIN tipodeexame as tp 
										    ON tp.proc_codigo = i.proc_codigo
										 LEFT JOIN medico med
										    ON med.med_codigo = a.med_codigo
										  lEFT JOIN domicilio as d
												    ON d.dom_codigo = u.dom_codigo 
										WHERE  a.age_codigo = $age_codigo 
										AND proc.proc_codigo = $resCoprpo[proc_codigo]		
										ORDER BY proc_nome";
						//echo $sql;
$querySql = pg_query($sql);
$resSql = pg_fetch_array($querySql);
						$sqlProcedimento = "SELECT * 
										 	  FROM tipodeexame AS t 
										  	  JOIN procedimento AS p 
										    	ON t.proc_codigo = p.proc_codigo 
										 	 WHERE t.proc_codigo = $resCoprpo[proc_codigo]";
						$queryProcedimento = pg_query($sqlProcedimento);
						$regProcedimento = pg_fetch_array($queryProcedimento);
						echo "	<table width=100% border=0>
									<tr>
										<td>
											<font size=3><b>".$regProcedimento[proc_nome]."</font></b> 
										</td>
										<td>
											<font size=3 align=center><b>V.R</b></font>
										</td>
									</tr
								</table>
							 </td>
						</tr>
						<tr>
							<td colspan=2>";
								$sqlMetodoTipo = "SELECT DISTINCT(proc_codigo),
														 * 
												    FROM tipodeexame AS tp 
												    LEFT join tipodematerial AS tma 
												      ON tp.tma_codigo = tma.tma_codigo 
												    JOIN tipodemetodos AS t 
												      ON t.tpm_codigo = tp.tpm_codigo 
												   WHERE tp.txa_codigo = $regProcedimento[txa_codigo]";
								$queryMetodoTipo= pg_query($sqlMetodoTipo);
								$regMetodoTipo = pg_fetch_array($queryMetodoTipo);
								echo "<font size=2><b>Material:</b>$regMetodoTipo[tma_tipo]</font> <br/>
									  <font size=2><b>M&eacute;todo:</b>$regMetodoTipo[tpm_metodo]</font>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								&nbsp;
							</td>
						</tr>";
						$sqlSubExame = "SELECT * 
										  FROM subexame AS s 
										  JOIN tipodeexame AS t 
										    ON t.txa_codigo = s.txa_codigo 
										 WHERE proc_codigo = $resCoprpo[proc_codigo]";
						$querySubExame = pg_query($sqlSubExame);
						$numLinhasSubExame = pg_num_rows($querySubExame);
						if($numLinhasSubExame > 0){
							while($regSubExame = pg_fetch_array($querySubExame)){
								echo "<tr>
										<td colspan=2>";
								echo"		<font size=4>&nbsp;&nbsp;&nbsp;$regSubExame[sex_subexame]</font>
											<table width=100% border=0 cellspacing=5> ";
												$sqlItens = "SELECT ite_codigo,
																	ite_itemdoexame,
																	ite_tipo_medida
															   FROM itensanalise AS i
															   JOIN subexame AS s
															     ON s.sex_codigo = i.sex_codigo
															   JOIN tipodeexame AS t
															     ON t.txa_codigo = s.txa_codigo
															   JOIN procedimento AS p
															     ON p.proc_codigo = t.proc_codigo
															  WHERE t.proc_codigo = $resCoprpo[proc_codigo]
															    AND s.sex_codigo = $regSubExame[sex_codigo]
																	  ORDER by ite_codigo";
														$queryItens = pg_query($sqlItens);
														while($regItens = pg_fetch_array($queryItens)){
															
														echo"<tr>
																<td width=33%>";
															echo "<font size=3>".$regItens[ite_itemdoexame]."<br/>
																</td>";
																$sqlResultado = "SELECT * 
																				   FROM resultadoexame 
																				  WHERE ite_codigo = $regItens[ite_codigo] 
																				    AND agei_codigo = $resSql[agei_codigo] 
																				    AND proc_codigo = $resCoprpo[proc_codigo]";
																//echo $sqlResultado."<br>";
																$querySqlResultado = pg_query($sqlResultado);
																$regSqlResultado = pg_fetch_array($querySqlResultado);
																$observacaoTxt = $regSqlResultado['res_observacao'];
															if($resCoprpo[proc_codigo] != 4629){
																$tabela = "<font size=3>". $regSqlResultado[vlr_valor]."</font>";
															}else{
																$nbsp = preg_replace("/[[:space:]]+/", " ", $regSqlResultado[vlr_valor]);
																$nbsp = explode(" ",$nbsp);
															
																if($nbsp[1] == ""){
																	$tabela = "<font size=3>$regSqlResultado[vlr_valor] $regItens[ite_tipo_medida]</font>";
																}else{
																	$tabela = "<table width=100%><tr><td width=50%><font size=3>$nbsp[0]</font></td><td align=center><font size=3>$nbsp[1] $regItens[ite_tipo_medida]</font></td></tr></table>";
																}
															}
															echo "<td width=34%>
																	 $tabela
																  </td>
																  <td align=right>";
																	 $mesesUsuario = "SELECT usu_sexo,
																							((DATE_PART('YEAR', AGE(NOW(), usu_datanasc))*12)+DATE_PART('MONTH', AGE(NOW(), usu_datanasc))) AS meses from usuario
													 								  WHERE usu_codigo = $regTopo[usu_codigo]";
																	$queryMesesUsuario = pg_query($mesesUsuario);
																	$regMesesUsuario = pg_fetch_array($queryMesesUsuario);
																	$idadeMeses = $regMesesUsuario[meses];
																	
																 	$sqlValoresDeReferencia = "SELECT * 
																 								 FROM valoresdereferencia 
																 								WHERE ite_codigo = $regItens[ite_codigo]
																 								AND (vlr_faixa_etaria_inicio <= $idadeMeses
															  									 	 OR vlr_faixa_etaria_inicio IS NULL)
															  									 AND (vlr_faixa_etaria_fim > $idadeMeses
															  									  	  OR vlr_faixa_etaria_fim IS NULL)
															  									 AND (vlr_sexo = '$regMesesUsuario[usu_sexo]' OR vlr_sexo is null)";
																 	$queryValoresDeReferencia = pg_query($sqlValoresDeReferencia);
																 	//echo $sqlValoresDeReferencia."<br/>";
																 	$regValoresDeReferencia = pg_fetch_array($queryValoresDeReferencia);
																 	echo "<font size=1>".$regValoresDeReferencia[vlr_valordereferencia].$regItens[ite_tipo_medida]."<br/>";  	 
															echo"
																  </td>";
													   echo" </tr>";
														}
											echo"</table>
										</td>
									</tr>";
							}
						}else{
							#n tem subexame
							$sqlTipoDoExameItem = "SELECT * 
													 FROM itensanalise as i
													 JOIN tipodeexame as t
													   ON t.txa_codigo = i.txa_codigo
													 JOIN procedimento AS p
													   ON p.proc_codigo = t.proc_codigo 
													WHERE t.proc_codigo = $resCoprpo[proc_codigo]
													ORDER BY ite_codigo";
							$queryTipoDoExameItem = pg_query($sqlTipoDoExameItem);
							while($regTipoDoExameItem = pg_fetch_array($queryTipoDoExameItem)){
								
								echo "<tr>
										<td colspan=2>
											<table width=100%>
												<tr>
													<td>
														<font size=3>".$regTipoDoExameItem[ite_itemdoexame]."
													</td>
													<td colspan=2 width=33%>";
														$sqlResultado = "SELECT *
																		   FROM resultadoexame
																		  WHERE ite_codigo = $regTipoDoExameItem[ite_codigo] 
																		    AND agei_codigo = $resSql[agei_codigo] 
																		    AND proc_codigo = $resCoprpo[proc_codigo]";
														$querySqlResultado = pg_query($sqlResultado);
														$regSqlResultado = pg_fetch_array($querySqlResultado);
														$observacaoTxt = $regSqlResultado['res_observacao'];
														echo "<font size=3>$regSqlResultado[vlr_valor] $regTipoDoExameItem[ite_tipo_medida]<br/>";
								echo"
													</td>
													<td width=33% align=\"right\">";
														$sqlValoresDeReferencia = "SELECT * FROM valoresdereferencia WHERE ite_codigo = $regTipoDoExameItem[ite_codigo]";
													 	$queryValoresDeReferencia = pg_query($sqlValoresDeReferencia);
													 	$numRowsValoreDeReferencia = pg_num_rows($queryValoresDeReferencia);
													 	$regValoresDeReferencia = pg_fetch_array($queryValoresDeReferencia);
													 	echo "<font size=1>$regValoresDeReferencia[vlr_valordereferencia]".($numRowsValoreDeReferencia == 0 ? "" : "$regTipoDoExameItem[ite_tipo_medida]" )." <br/>";
														
								echo"
													</td>
									  			</tr>
									  		</table>
									  	</td>
									  </tr>";
							}		
						}
						echo "
									<tr>
										<td>
											<font size=3><b>Obs.:</b></font>
										</td>
									</tr>
									<tr>
										<td>";
										$sqlObs = " SELECT res_observacao 
												  	  FROM resultadoexame
												     WHERE ite_codigo = ".$regTipoDoExameItem['ite_codigo']." 
												       AND agei_codigo = $agei_codigo ";
										#$queryObs = pg_query($sqlObs) or die(pg_last_error());
										#while($regObs = pg_fetch_array($queryObs)){
											echo "<font size=2> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$observacaoTxt."&nbsp;";
										#}
								   echo"</td>
								  	</tr>";
								   echo "<br/><br/>";
					}//fim do foreach
						
							echo"</td>
						</tr>
					</table>
				</td>
			  </tr>";
	echo "</table>
		</td>
	</tr>
	<tr>";
	// o sql desse while esta no topo do arquivo para dar colspan    

	echo"
		
	</tr>
</table>";
echo"</body>
</html>";
?>
