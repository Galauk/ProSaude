<? 
include_once '../../global.php';
	


$conexao = ibase_connect('localhost:C:\gdb\Hiper.gdb',"SYSDBA","masterkey") or die("??".ibase_errmsg());

function ib_query($query){
	global $conexao;
	return ibase_query($conexao,$query);
}
function usr_codigo($usr_nome){	
	$seqUsr = "select nextval('seq_usr_codigo_9041') as usr_codigo";
	$seqUsr_query = pg_query($seqUsr);
	$usr_codigo = pg_fetch_array($seqUsr_query);
	$usr_codigo = $usr_codigo[usr_codigo];
	
	$sql = "INSERT INTO usuarios(usr_codigo, 
								 usr_login, 
								 usr_nome,
								 usr_senha, 
								 usr_ativo, 
								 usr_tipo,  
								 usr_dtinsert, 
								 usr_alter, 
								 usr_dtalter, 
								 usr_tipo_medico,								 
								 usr_funcao)
                        VALUES ($usr_codigo,
                                '".substr($usr_nome,0,10)."', 
                               	'$usr_nome', 
                                '".md5('123')."', 
                                'S', 
                                'U', 
                                CURRENT_DATE, 
                                0, 
                                CURRENT_DATE, 
                                'E',                                  
                                'NULL'
                                )
                                ";
	                          	                            
	pg_query($sql) or die("\n\n\nErro no ID (antigo): {$usr_codigo}\n\n".pg_last_error()."\n\n$sql");
	return $usr_codigo;
}
$selectNossaBase = "select usu_nome,usu_datanasc,usu_mae,usu_pai from usuario";
$queryNossaBase = pg_query($selectNossaBase);
$cont = 0;
//while($resultadoNossabase = pg_fetch_array($queryNossaBase)){
 	$selectHiperdia = "  SELECT 
                             A.ST_ANTECEDENTES_FAMILIARES,
                             A.ST_DIABETES_1,
                             A.ST_DIABETES_2,
                             A.ST_TABAGISMO,
                             A.ST_SEDENTARISMO,
                             A.ST_SOBREPESO,
                             A.ST_HIPERTENSAO,
                             A.ST_INFARTO,
                             A.ST_OUTRAS_CORONARIOPATIAS,
                             A.ST_AVC,
                             A.ST_PE_DIABETICO,
                             A.ST_AMPUTACAO_DIABETE,
                             A.ST_DOENCA_RENAL,
                             A.DT_CADASTRAMENTO,                            
                             A.TP_SEXO,
                             A.TP_RACA_COR,
                             A.DT_NASCIMENTO,
                             A.CO_NACIONALIDADE,
                             A.NU_NCNS,
                             A.NU_CPF,
                             A.NO_MAE,
                             A.NO_PAI,
                             A.NO_PESSOA,
                             A.NU_IDENTIDADE,
                             C.CO_SEQ_CONSULTA,
                             C.VL_PRESSAO_SISTOLICA,
                             C.VL_PRESSAO_DIASTOLICA,
                             C.VL_CINTURA_CM,
                             C.VL_PESO_KG,
                             C.VL_ALTURA_CM,
                             C.VL_GLICEMIA,
                             C.TP_OUTROS_MEDICAMENTOS,
                             C.VL_INSULINA,
                             C.ST_NAO_EXISTEM_COMPLIC,
                             C.ST_ANGINA,
                             C.ST_AVC,
                             C.ST_PE_DIAB,
                             C.TP_CONSULTA,
                             C.ST_AMPUTACAO,
                             C.ST_DOENCA_RENAL,
                             C.ST_FUNDO_OLHO,
                             C.ST_HB_GLICOSILADA,
                             C.ST_CREATININA_SERICA,
                             C.ST_COLESTEROL_TOTAL,
                             C.ST_ECG,
                             C.ST_HIPERTENSAO,
                             C.ST_DIABETES,
                             C.ST_TRIGLICERIDES,
                             C.ST_URINA_TIPO_1,
                             C.ST_IAM,
                             C.DT_CONSULTA,
                             C.ST_FUNDO_OLHO,
                             C.ST_MICROALBUMINURIA,                             
                             C.ST_MEDICAMENTOSO,
                             P.NO_PROFISSIONAL
                        FROM TB_PESSOA_HIPER A
                       INNER JOIN TB_CONSULTA C
                          ON A.CO_SEQ_PESSOA = C.CO_PESSOA
                       INNER JOIN TB_PROFISSIONAL_SAUDE P
						  ON C.CO_MUNICIPIO_IBGE = P.CO_MUNICIPIO_IBGE
						 AND C.CO_DISTRITO = P.CO_DISTRITO
						 AND C.CO_UNIDADE = P.CO_UNIDADE
						 AND C.CO_PROFISSIONAL = P.CO_SEQ_PROFISSIONAL
						  ORDER BY C.CO_PESSOA,C.TP_CONSULTA DESC                      	
					";
	$queryHiperdia = ib_query($selectHiperdia);
	while($resultadoHiperdia= ibase_fetch_assoc($queryHiperdia)){
 //echo "<FONT COLOR=BLUE>". $selectHiperdia. "</FONT>"; exit;
		
		
			
		
		$seqHiperAcompa = "select nextval('hiperdia_acompanhamentos_hiperac_codigo_seq') as hiperac_codigo";
		$seqHiperAcompa_query = pg_query($seqHiperAcompa);
		$hiperac_codigo = pg_fetch_array($seqHiperAcompa_query);
		$hiperac_codigo = $hiperac_codigo[hiperac_codigo];
		
		
		$selectUsuariosEnfermeiros = "SELECT * FROM USUARIOS WHERE USR_NOME = '$resultadoHiperdia[NO_PROFISSIONAL]'";
		$queryUsuariosEnfermeiros = pg_query($selectUsuariosEnfermeiros);
		$resUsuariosEnfermeiros = pg_fetch_array($queryUsuariosEnfermeiros);
		$usr_codigo = $resUsuariosEnfermeiros[usr_codigo];
		
		if($resultadoHiperdia[TP_CONSULTA] == 'C'){

			$seq = "select nextval('seq_usu_codigo') as usu_codigo";
			$seq_query = pg_query($seq);
			$usu_codigo = pg_fetch_array($seq_query);
			$usu_codigo = $usu_codigo[usu_codigo];
			
				$insert = "INSERT INTO USUARIO 
										(USU_CODIGO,
										USU_SEXO,
										RAC_CODIGO,
										USU_DATANASC,
										CD_NACIONALIDADE,
										USU_CARTAO_SUS,
										USU_CPF,
										USU_MAE,
										USU_PAI,
										USU_NOME,
										USU_RG)
								VALUES( $usu_codigo,
										".($resultadoHiperdia[TP_SEXO] == '' ? 'NULL' : "'$resultadoHiperdia[TP_SEXO]'").",
										".($resultadoHiperdia[TP_RACA_COR] == '' ? 'NULL' : "'$resultadoHiperdia[TP_RACA_COR]'").",
										".($resultadoHiperdia[DT_NASCIMENTO] == '' ? 'NULL' : "'$resultadoHiperdia[DT_NASCIMENTO]'").",
										".($resultadoHiperdia[CO_NACIONALIDADE] == '' ? 'NULL' : "'$resultadoHiperdia[CO_NACIONALIDADE]'").",
										".($resultadoHiperdia[NU_NCNS] == '' ? 'NULL' : "'$resultadoHiperdia[NU_NCNS]'").",
										".($resultadoHiperdia[NU_CPF] == '' ? 'NULL' : "'$resultadoHiperdia[NU_CPF]'").",
										".($resultadoHiperdia[NO_MAE] == '' ? 'NULL' : "'$resultadoHiperdia[NO_MAE]'").",
										".($resultadoHiperdia[NO_PAI] == '' ? 'NULL' : "'$resultadoHiperdia[NO_PAI]'").",
										".($resultadoHiperdia[NO_PESSOA] == '' ? 'NULL' : "'$resultadoHiperdia[NO_PESSOA]'").",
										".($resultadoHiperdia[NU_IDENTIDADE] == '' ? 'NULL' : "'$resultadoHiperdia[NU_IDENTIDADE]'")."
										)";
				echo $insert.";" ."<br>"."<br>";
				pg_query($insert)or die(pg_last_error());;
				
				$seqHiper = "select nextval('hiperdia_hiper_codigo_seq') as hiper_codigo";
				$seqHiper_query = pg_query($seqHiper);
				$hiper_codigo = pg_fetch_array($seqHiper_query);
				$hiper_codigo = $hiper_codigo[hiper_codigo];
			
				$insertHipertensos = "INSERT INTO hiperdia(hiper_codigo,
												  usu_codigo ,
												  hiper_data,
												  hiper_status ,
												  hiper_antecedentes_familiares ,
												  hiper_diabetes_1 ,
												  hiper_diabetes_2 ,
												  hiper_tabagismo ,
												  hiper_sedentarismo ,
												  hiper_sobrepeso ,
												  hiper_infarto ,
												  hiper_outras_coronariopatias,
												  hiper_avc ,
												  hiper_pe_diabetico ,
												  hiper_amputacao ,
												  hiper_doenca_renal ,
												  hiper_hipertensao,
												  hiper_pa_sistolica,
												  hiper_pa_diastolica,
												  hiper_cintura,
												  hiper_altura,
												  hiper_peso,
												  hiper_glicemia_capilar,
												  usr_codigo
												  
												  )
										  VALUES($hiper_codigo,
												 $usu_codigo,
										  		".($resultadoHiperdia[DT_CADASTRAMENTO] == '' ? 'NULL' : "'$resultadoHiperdia[DT_CADASTRAMENTO]'").",
										  		'H',
										  		".($resultadoHiperdia[ST_ANTECEDENTES_FAMILIARES] == '' ? 'NULL' : "'$resultadoHiperdia[ST_ANTECEDENTES_FAMILIARES]'").",
										  		".($resultadoHiperdia[ST_DIABETES_1] == '' ? 'NULL' : "'$resultadoHiperdia[ST_DIABETES_1]'").",
										  		".($resultadoHiperdia[ST_DIABETES_2] == '' ? 'NULL' : "'$resultadoHiperdia[ST_DIABETES_2]'").",
										  		".($resultadoHiperdia[ST_TABAGISMO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_TABAGISMO]'").",
										  		".($resultadoHiperdia[ST_SEDENTARISMO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_SEDENTARISMO]'").",								  		
										  		".($resultadoHiperdia[ST_SOBREPESO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_SOBREPESO]'").",
										  		".($resultadoHiperdia[ST_INFARTO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_INFARTO]'").",
										  		".($resultadoHiperdia[ST_OUTRAS_CORONARIOPATIAS] == '' ? 'NULL' : "'$resultadoHiperdia[ST_OUTRAS_CORONARIOPATIAS]'").",
										  		".($resultadoHiperdia[ST_AVC] == '' ? 'NULL' : "'$resultadoHiperdia[ST_AVC]'").",
										  		".($resultadoHiperdia[ST_PE_DIABETICO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_PE_DIABETICO]'").",
										  		".($resultadoHiperdia[ST_AMPUTACAO_DIABETE] == '' ? 'NULL' : "'$resultadoHiperdia[ST_AMPUTACAO_DIABETE]'").",
										  		".($resultadoHiperdia[ST_DOENCA_RENAL] == '' ? 'NULL' : "'$resultadoHiperdia[ST_DOENCA_RENAL]'").",
										  		".($resultadoHiperdia[ST_HIPERTENSAO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_HIPERTENSAO]'").",
										  		".($resultadoHiperdia[VL_PRESSAO_SISTOLICA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_PRESSAO_SISTOLICA]'").",
										  		".($resultadoHiperdia[VL_PRESSAO_DIASTOLICA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_PRESSAO_DIASTOLICA]'").",
										  		".($resultadoHiperdia[VL_CINTURA_CM] == '' ? 'NULL' : "'$resultadoHiperdia[VL_CINTURA_CM]'").",
										  		".($resultadoHiperdia[VL_ALTURA_CM] == '' ? 'NULL' : "'$resultadoHiperdia[VL_ALTURA_CM]'").",
										  		".($resultadoHiperdia[VL_PESO_KG] == '' ? 'NULL' : "'$resultadoHiperdia[VL_PESO_KG]'").",
										  		".($resultadoHiperdia[VL_GLICEMIA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_GLICEMIA]'").",
										  		".($usr_codigo == ''? usr_codigo($resultadoHiperdia[NO_PROFISSIONAL]) : $usr_codigo)."
										  		)";
				pg_query($insertHipertensos)or die("Hiperdia".pg_last_error());;
				
				 //echo "<font color=red>".$insertHipertensos.";"."</font>"."<br>"."<br>";
				$selectMedicamentos = "  SELECT * 
				 						   FROM  TB_CONSULTA C
		                       		 INNER JOIN RL_CONSULTA_MEDICAMENTO RL
		                                     ON RL.CO_CONSULTA = C.CO_SEQ_CONSULTA
		                                  WHERE RL.CO_CONSULTA = $resultadoHiperdia[CO_SEQ_CONSULTA]";
				////echo $selectMedicamentos;
				$queryMedicamentos = ib_query($selectMedicamentos);
				while($resultadoMedicamento = ibase_fetch_assoc($queryMedicamentos)){
					
					$insertMedicamentos = "INSERT INTO hiperdia_medicamentos(	
													  hiper_codigo,
													  hipermed_medicamentoso,
													  pro_codigo,
													  hipermed_insulina_dia,
													  hipermed_outros,
													  hipermed_dosagem)
												VALUES($hiper_codigo,
													   ".($resultadoHiperdia[ST_MEDICAMENTOSO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_MEDICAMENTOSO]'").",
													   ".($resultadoMedicamento[CO_MEDICAMENTO] == '' ? 'NULL' : "'$resultadoMedicamento[CO_MEDICAMENTO]'").",
													   ".($resultadoHiperdia[VL_INSULINA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_INSULINA]'").",
													   ".($resultadoHiperdia[TP_OUTROS_MEDICAMENTOS] == '' ? 'NULL' : "'$resultadoHiperdia[TP_OUTROS_MEDICAMENTOS]'").",
													   ".($resultadoMedicamento[QT_REMEDIO] == '' ? 'NULL' : "'$resultadoMedicamento[QT_REMEDIO]'")."							   
													   									   
													   )";
					pg_query($insertMedicamentos)or die("Medicamento".pg_last_error());;
					//echo "<font color=red>".$insertMedicamentos.";"."</font>"."<br>"."<br>";							
		
				}
			}else{
			$insertHipertensosAcompanhamentos = "INSERT INTO 
														hiperdia_acompanhamentos(hiperac_codigo,
																				  hiper_codigo,
																				  hiperac_pasistolica,
																				  hiperac_padiastolica,
																				  hiperac_cintura,
																				  hiperac_peso,
																				  hiperac_altura,
																				  hiperac_exame_glicemia,
																				  hiperac_sem_complicacoes,
																				  hiperac_angina,
																				  hiperac_iam,
																				  hiperac_avc,
																				  hiperac_amputacao_diabetes,
																				  hiperac_doenca_renal,
																				  hiperac_retinopatia,
																				  hiperac_pe_diabetico,
																				  hiperac_data_consulta,
																				  hiperac_hipertenso,
																				  hiperac_diabetico,
																				  usr_codigo)
																		VALUES($hiperac_codigo,
																			   $hiper_codigo,
																		  		".($resultadoHiperdia[VL_PRESSAO_SISTOLICA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_PRESSAO_SISTOLICA]'").",
																		  		".($resultadoHiperdia[VL_PRESSAO_DIASTOLICA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_PRESSAO_DIASTOLICA]'").",
									  											".($resultadoHiperdia[VL_CINTURA_CM] == '' ? 'NULL' : "'$resultadoHiperdia[VL_CINTURA_CM]'").",
									  											".($resultadoHiperdia[VL_PESO_KG] == '' ? 'NULL' : "'$resultadoHiperdia[VL_PESO_KG]'").",
									  											".($resultadoHiperdia[VL_ALTURA_CM] == '' ? 'NULL' : "'$resultadoHiperdia[VL_ALTURA_CM]'").",
									  											".($resultadoHiperdia[VL_GLICEMIA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_GLICEMIA]'").",
									  											".($resultadoHiperdia[ST_NAO_EXISTEM_COMPLIC] == '' ? 'NULL' : "'$resultadoHiperdia[ST_NAO_EXISTEM_COMPLIC]'").",
									  											".($resultadoHiperdia[ST_ANGINA] == '' ? 'NULL' : "'$resultadoHiperdia[ST_ANGINA]'").",
									  											".($resultadoHiperdia[ST_IAM] == '' ? 'NULL' : "'$resultadoHiperdia[ST_IAM]'").",
									  											".($resultadoHiperdia[ST_AVC] == '' ? 'NULL' : "'$resultadoHiperdia[ST_AVC]'").",
									  											".($resultadoHiperdia[ST_AMPUTACAO_DIABETE] == '' ? 'NULL' : "'$resultadoHiperdia[ST_AMPUTACAO_DIABETE]'").",
									  											".($resultadoHiperdia[ST_DOENCA_RENAL] == '' ? 'NULL' : "'$resultadoHiperdia[ST_DOENCA_RENAL]'").",
					  															".($resultadoHiperdia[ST_FUNDO_OLHO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_FUNDO_OLHO]'").",
									  											".($resultadoHiperdia[ST_PE_DIABETICO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_PE_DIABETICO]'").",
									  											".($resultadoHiperdia[DT_CONSULTA] == '' ? 'NULL' : "'$resultadoHiperdia[DT_CONSULTA]'").",
									  											".($resultadoHiperdia[ST_HIPERTENSAO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_HIPERTENSAO]'").",
									  											".($resultadoHiperdia[ST_DIABETES_1] == '' ? 'NULL' : "'$resultadoHiperdia[ST_DIABETES_1]'").",
									  											".($usr_codigo == ''? usr_codigo($resultadoHiperdia[NO_PROFISSIONAL]) : $usr_codigo)."																
																			)";
			pg_query($insertHipertensosAcompanhamentos) or die("Acompanhamento".pg_last_error());
			//echo "<font color=green>".$insertHipertensosAcompanhamentos.";"."</font>"."<br>"."<br>";
			
			$insertExames = "INSERT INTO hiperdia_exames(hiperac_codigo,
													  	 hiperac_hb_glicosada,
														 hiperac_creatinina_serica,
														 hiperac_colesterol_total,
														 hiperac_ecg,
														 hiperac_triglicerides,
														 hiperac_urina ,
														 hiperac_micro_albuminuria)
							VALUES($hiperac_codigo,
								   ".($resultadoHiperdia[ST_HB_GLICOSILADA] == '' ? 'NULL' : "'$resultadoHiperdia[ST_HB_GLICOSILADA]'").",
								   ".($resultadoHiperdia[ST_CREATININA_SERICA] == '' ? 'NULL' : "'$resultadoHiperdia[ST_CREATININA_SERICA]'").",
								   ".($resultadoHiperdia[ST_COLESTEROL_TOTAL] == '' ? 'NULL' : "'$resultadoHiperdia[ST_COLESTEROL_TOTAL]'").",
								   ".($resultadoHiperdia[ST_ECG] == '' ? 'NULL' : "'$resultadoHiperdia[ST_ECG]'").",
								   ".($resultadoHiperdia[ST_TRIGLICERIDES] == '' ? 'NULL' : "'$resultadoHiperdia[ST_TRIGLICERIDES]'").",
								   ".($resultadoHiperdia[ST_URINA_TIPO_1] == '' ? 'NULL' : "'$resultadoHiperdia[ST_URINA_TIPO_1]'").",
								   ".($resultadoHiperdia[ST_MICROALBUMINURIA] == '' ? 'NULL' : "'$resultadoHiperdia[ST_MICROALBUMINURIA]'")."
							)
							  ";
			pg_query($insertExames);
			//echo "<font color=green>".$insertExames.";"."</font>"."<br>"."<br>";
			
			$selectMedicamentosAcompanhamento = "    SELECT * 
							 						   FROM TB_CONSULTA C
					                       		 INNER JOIN RL_CONSULTA_MEDICAMENTO RL
					                                     ON RL.CO_CONSULTA = C.CO_SEQ_CONSULTA
					                                  WHERE RL.CO_CONSULTA = $resultadoHiperdia[CO_SEQ_CONSULTA]";
			////echo $selectMedicamentosAcompanhamento;
			$queryMedicamentosAcompanhamentos = ib_query($selectMedicamentosAcompanhamento);
			while($resultadoMedicamentoAcompanhamentos = ibase_fetch_assoc($queryMedicamentosAcompanhamentos)){
				
			$insertMedicamentosAcompanhamento = "INSERT INTO 
														hiperdia_medicamentos_acompanhamento(hiperac_codigo,
																							  hipermedac_medicamentoso,
																							  hipermedac_insulina_dia,
																							  hipermedac_outros,
																							  hipermedac_dosagem,
																							  pro_codigo)
																					VALUES($hiperac_codigo,
																						   ".($resultadoHiperdia[ST_MEDICAMENTOSO] == '' ? 'NULL' : "'$resultadoHiperdia[ST_MEDICAMENTOSO]'").",
																						   ".($resultadoHiperdia[VL_INSULINA] == '' ? 'NULL' : "'$resultadoHiperdia[VL_INSULINA]'").",
																						   ".($resultadoHiperdia[TP_OUTROS_MEDICAMENTOS] == '' ? 'NULL' : "'$resultadoHiperdia[TP_OUTROS_MEDICAMENTOS]'").",
																						   ".($resultadoMedicamentoAcompanhamentos[QT_REMEDIO] == '' ? 'NULL' : "'$resultadoMedicamento[QT_REMEDIO]'").",
																						   ".($resultadoMedicamentoAcompanhamentos[CO_MEDICAMENTO] == '' ? 'NULL' : "'$resultadoMedicamento[CO_MEDICAMENTO]'")."
																						  )";
			pg_query($insertMedicamentosAcompanhamento)or die("Exame".pg_last_error());;
			
			//echo "<font color=green>".$insertMedicamentosAcompanhamento.";"."</font>"."<br>"."<br>";
			
			}
		}
		//exit;
	}	
//}
//echo $selectHiperdia;
echo "<b>".$cont."</b>"
?>