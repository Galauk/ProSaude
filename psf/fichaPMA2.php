<html>
<head>
	<script>
		function somaTudo(prefixo){
			var arrayElementos = document.getElementsByTagName("input");
			var soma = new Number(0);
			for(i = 0; i < arrayElementos.length ; i++){
				var nomeCampo = arrayElementos[i].name;
				elemento = nomeCampo.split('-');
				if (elemento[0] == prefixo){
					soma = soma + new Number(arrayElementos[i].value);
				}else if(elemento[0] == "total"){
					if(elemento[1] == prefixo){
						arrayElementos[i].value = soma;
					}
				}
			}
		}
		function somaTotais(){
			var total1;
			var total2;
			total1 = document.getElementById("ativ-foraAreaAbrangencia").value;
			total2 = document.getElementById("total-atividades").value;
			var total3 = document.getElementById("total2-atividades");
			total3.value = new Number(total1)+new Number(total2);
		}
	</script>
</head>
	<body>
		<?
		session_start();
		require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
		require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
		require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
		require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
		
		$common = new commonClass();
		$form = new classForm();
		$table = new tableClass();
		echo $common->incJquery();
		
		//echo "<pre>".print_r($_GET,true)."</pre>";
		$pma2_codigo = $_GET['pma2_codigo'];
		
		echo $common->menuTab(array('Atividades/Produ&ccedil;&atilde;o','Tipo de Atendimento','Solicita&ccedil;&atilde;o de Exames','Encaminhamento','Procedimento','Marcadores','Visitas Domiciliares'));
		echo $common->bodyTab('1');
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$select = "select * from pma2_atividades where pma2_codigo = $_GET[pma2_codigo] ";
				$query = pg_query($select);
				$res_query = pg_fetch_array($query);
				$pate_codigo = $res_query[$pate_codigo];
				$pativ_menor_um = $res_query[pativ_menor_um];
				$pativ_um_quatro = $res_query[pativ_um_quatro];
				$pativ_cinco_nove = $res_query[pativ_cinco_nove];
				$pativ_dez_quatorze = $res_query[pativ_dez_quatorze];
				$pativ_quinze_dezenove = $res_query[pativ_quinze_dezenove];
				$pativ_vinte_trinta_e_nove = $res_query[pativ_vinte_trinta_e_nove];
				$pativ_quarenta_quarenta_e_nove = $res_query[pativ_quarenta_quarenta_e_nove];
				$pativ_cinquenta_cinquenta_e_nove = $res_query[pativ_cinquenta_cinquenta_e_nove];
				$pativ_sessenta_e_mais = $res_query[pativ_sessenta_e_mais];
				$pativ_residentes_fora_area = $res_query[pativ_residentes_fora_area];
				
				
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarAtividades&pma2_codigo=$pma2_codigo#tabs-1");
					$nomeForm = "atividades";
					
			 		$formulario.= $form->hiddenForm("numlinhas", pg_num_rows($query));
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->inputText("$nomeForm-MenorUm","$pativ_menor_um","< 1",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-UmQuatro","$pativ_um_quatro","1-4",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-CincoNove","$pativ_cinco_nove","5-9",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-DezQuatorze","$pativ_dez_quatorze","10-14",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-QuinzeDezenove","$pativ_quinze_dezenove","15-19",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-VinteTrintaENove","$pativ_vinte_trinta_e_nove","20-39",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-QuarentaQuarentaENove","$pativ_quarenta_quarenta_e_nove","40-49",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-CinquentaCinquentaENove","$pativ_cinquenta_cinquenta_e_nove","50-59",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("$nomeForm-SessentaAcima","$pativ_sessenta_e_mais","60 e mais",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario .= $form->inputText("total-$nomeForm","","Total",null,null,"onChange=somaTotais()","text","S");
				echo $table->criaLinha(array("Residentes na &aacute;rea de abrang&ecirc;ncia da equipe", $formulario), array(40, '700'));
					$formulario2 = $form->inputText("ativ-foraAreaAbrangencia", "$pativ_residentes_fora_area", "Residentes fora da &aacute;rea",null,null,"onBlur=somaTudo(\"$nomeForm\");somaTotais();");
					$formulario2 .= $form->inputText("total2-$nomeForm","","Total Geral",null,null,null,"text","S");
					if($res_query[pativ_codigo ]== ""){
						$formulario2 .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario2 .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}
					
					//$formulario2 .= $common->commonButton("Adicionar",null,$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/adicionar_on.png","onClick=Submit()");
					$formulario2 .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
				$formulario2 .= $form->closeForm();
				echo $table->criaLinha(array($formulario2),array(40, 700), array(2,1));
				
			echo $table->closeTable();
		
			if ($acao == "salvarAtividades"){
			
				if($_POST[numlinhas] == 0){
					$pma2_codigo = $_POST['pma2_codigo'];
					$pativ_menor_um = $_POST['atividades-MenorUm'];
					$pativ_um_quatro = $_POST['atividades-UmQuatro'];
					$pativ_cinco_nove = $_POST['atividades-CincoNove'];
					$pativ_dez_quatorze = $_POST['atividades-DezQuatorze'];
					$pativ_quinze_dezenove = $_POST['atividades-QuinzeDezenove'];
					$pativ_vinte_trinta_e_nove = $_POST['atividades-VinteTrintaENove'];
					$pativ_quarenta_quarenta_e_nove = $_POST['atividades-QuarentaQuarentaENove'];
					$pativ_cinquenta_cinquenta_e_nove = $_POST['atividades-CinquentaCinquentaENove'];
					$pativ_sessenta_e_mais = $_POST['atividades-SessentaAcima'];
					$pativ_residentes_fora_area = $_POST['ativ-foraAreaAbrangencia'];
					
					$insertAtividades = "INSERT INTO pma2_atividades (pma2_codigo, 
																			pativ_menor_um, 
																			pativ_um_quatro, 
																			pativ_cinco_nove, 
																			pativ_dez_quatorze, 
																			pativ_quinze_dezenove, 
																			pativ_vinte_trinta_e_nove, 
																			pativ_quarenta_quarenta_e_nove, 
																			pativ_cinquenta_cinquenta_e_nove, 
																			pativ_sessenta_e_mais, 
																			pativ_residentes_fora_area
															  			   )
															  	 VALUES 
															  	 		   ($pma2_codigo, 
																		   ".intval($pativ_menor_um).",
																		    ".intval($pativ_um_quatro).", 
																		    ".intval($pativ_cinco_nove).", 
																		    ".intval($pativ_dez_quatorze).", 
																		    ".intval($pativ_quinze_dezenove).", 
																		    ".intval($pativ_vinte_trinta_e_nove).", 
																		    ".intval($pativ_quarenta_quarenta_e_nove).", 
																		    ".intval($pativ_cinquenta_cinquenta_e_nove).", 
																		    ".intval($pativ_sessenta_e_mais).", 
																		    ".intval($pativ_residentes_fora_area)."
																		   )";
					
					$executaInsert = pg_query($insertAtividades) or die($insertAtividades); 
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de atividades foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-2");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de atividades n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-1");
					}
				}else{
					$pma2_codigo = $_POST['pma2_codigo'];
					$pativ_menor_um = $_POST['atividades-MenorUm'];
					$pativ_um_quatro = $_POST['atividades-UmQuatro'];
					$pativ_cinco_nove = $_POST['atividades-CincoNove'];
					$pativ_dez_quatorze = $_POST['atividades-DezQuatorze'];
					$pativ_quinze_dezenove = $_POST['atividades-QuinzeDezenove'];
					$pativ_vinte_trinta_e_nove = $_POST['atividades-VinteTrintaENove'];
					$pativ_quarenta_quarenta_e_nove = $_POST['atividades-QuarentaQuarentaENove'];
					$pativ_cinquenta_cinquenta_e_nove = $_POST['atividades-CinquentaCinquentaENove'];
					$pativ_sessenta_e_mais = $_POST['atividades-SessentaAcima'];
					$pativ_residentes_fora_area = $_POST['ativ-foraAreaAbrangencia'];
					
					 $stmt = "UPDATE pma2_atividades SET 
										pativ_menor_um = '$pativ_menor_um', 
										pativ_um_quatro = '$pativ_um_quatro', 
										pativ_cinco_nove = '$pativ_cinco_nove', 
										pativ_dez_quatorze = '$pativ_dez_quatorze', 
										pativ_quinze_dezenove = '$pativ_quinze_dezenove', 
										pativ_vinte_trinta_e_nove = '$pativ_vinte_trinta_e_nove', 
										pativ_quarenta_quarenta_e_nove = '$pativ_quarenta_quarenta_e_nove', 
										pativ_cinquenta_cinquenta_e_nove = '$pativ_cinquenta_cinquenta_e_nove', 
										pativ_sessenta_e_mais = '$pativ_sessenta_e_mais', 
										pativ_residentes_fora_area = '$pativ_residentes_fora_area'
										WHERE pma2_codigo = ".intval($pma2_codigo) ;
		 			$executaInsert = pg_query($stmt) or die($stmt); 
					 
				if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de atividades foram Alterados com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-2");
					}else{
						echo $common->modalMsg("ERRO", "Houve umssss erro e os dados de atividades n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-1");
					}
				}	
			}
			
		echo $common->closeTab();
						
		echo $common->bodyTab('2');
		$select2 = "select * from pma2_atendimento where pma2_codigo = $_GET[pma2_codigo] ";
		$query2 = pg_query($select2);
		$res_query2 = pg_fetch_array($query2);
				
		$pate_puericultura = $res_query2[pate_puericultura];
		$pate_pre_natal = $res_query2[pate_pre_natal];
		$pate_prevencao_cancer_cervico_uterino = $res_query2[pate_prevencao_cancer_cervico_uterino];
		$pate_dst_aids = $res_query2[pate_dst_aids];
		$pate_diabetes = $res_query2[pate_diabetes];
		$pate_hipertensao = $res_query2[pate_hipertensao];
		$pate_hanseniase = $res_query2[pate_hanseniase];
		$pate_tuberculose = $res_query2[pate_tuberculose];
				
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarAtendimento#tabs-2");
					$nomeForm = "atendimento";
					$formulario.= $form->hiddenForm("numlinhas", pg_num_rows($query2));
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->inputText("$nomeForm-puericultura","$pate_puericultura","Puericultura","");
					$formulario .= $form->inputText("$nomeForm-preNatal","$pate_pre_natal","Pr&eacute;-Natal","");
					$formulario .= $form->inputText("$nomeForm-prevencaoCancerCervicoUterino","$pate_prevencao_cancer_cervico_uterino","Preven&ccedil;&atilde;o do C&acirc;ncer C&eacute;rvico-Uterino","");
					$formulario .= $form->inputText("$nomeForm-dstAids","$pate_dst_aids","DST/AIDS","");
					$formulario .= $form->inputText("$nomeForm-diabetes","$pate_diabetes","Diabetes","");
					$formulario .= $form->inputText("$nomeForm-hepertensaoArterial","$pate_hipertensao","Hipertens&atilde;o Arterial","");
					$formulario .= $form->inputText("$nomeForm-hanseniase","$pate_hanseniase","Hansen&iacute;ase","");
					$formulario .= $form->inputText("$nomeForm-tuberculose","$pate_tuberculose","Tuberculose","");
					if($res_query2[pate_codigo ]== ""){
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}
					$formulario .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
					
				$formulario .= $form->closeForm();
				echo $table->criaLinha(array("Tipo de Atendimento de M&eacute;dico e de Enfermeiro", $formulario), array(40, '700'));
			echo $table->closeTable();
			
			if ($acao == "salvarAtendimento"){
					if($_POST[numlinhas] == 0){
						$pma2_codigo = $_POST['pma2_codigo'];
						$pate_puericultura = $_POST['atendimento-puericultura'];
						$pate_pre_natal = $_POST['atendimento-preNatal'];
						$pate_prevencao_cancer_cervico_uterino = $_POST['atendimento-prevencaoCancerCervicoUterino'];
						$pate_dst_aids = $_POST['atendimento-dstAids'];
						$pate_diabetes = $_POST['atendimento-diabetes'];
						$pate_hipertensao = $_POST['atendimento-hepertensaoArterial'];
						$pate_hanseniase = $_POST['atendimento-hanseniase'];
						$pate_tuberculose = $_POST['atendimento-tuberculose'];
						
						$insertAtendimento = "INSERT INTO pma2_atendimento (pma2_codigo, 
																				  pate_puericultura, 
																				  pate_pre_natal, 
																				  pate_prevencao_cancer_cervico_uterino, 
																				  pate_dst_aids, 
																				  pate_diabetes, 
																				  pate_hipertensao, 
																				  pate_hanseniase, 
																				  pate_tuberculose
																			     ) 
																		 VALUES 
																		 		 ($pma2_codigo, 
																				 ".intval($pate_puericultura).", 
																				 ".intval($pate_pre_natal).", 
																				 ".intval($pate_prevencao_cancer_cervico_uterino).", 
																				 ".intval($pate_dst_aids).", 
																				 ".intval($pate_diabetes).", 
																				 ".intval($pate_hipertensao).", 
																				 ".intval($pate_hanseniase).", 
																				 ".intval($pate_tuberculose)." 
																				 )";
						$executaInsert = pg_query($insertAtendimento) or die($insertAtendimento);
						if ($executaInsert){
							echo $common->modalMsg("OK", "Dados de atendimento foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-3");
						}else{
							echo $common->modalMsg("ERRO", "Houve um erro e os dados de atendimento n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-2");
						}
					}else{
						$pma2_codigo = $_POST['pma2_codigo'];
						$pate_puericultura = $_POST['atendimento-puericultura'];
						$pate_pre_natal = $_POST['atendimento-preNatal'];
						$pate_prevencao_cancer_cervico_uterino = $_POST['atendimento-prevencaoCancerCervicoUterino'];
						$pate_dst_aids = $_POST['atendimento-dstAids'];
						$pate_diabetes = $_POST['atendimento-diabetes'];
						$pate_hipertensao = $_POST['atendimento-hepertensaoArterial'];
						$pate_hanseniase = $_POST['atendimento-hanseniase'];
						$pate_tuberculose = $_POST['atendimento-tuberculose'];
						$stmt = "UPDATE pma2_atendimento SET 
													pma2_codigo = ".intval($pma2_codigo).", 
													pate_puericultura = '$pate_puericultura', 
													pate_pre_natal = '$pate_pre_natal', 
													pate_prevencao_cancer_cervico_uterino = '$pate_prevencao_cancer_cervico_uterino', 
													pate_dst_aids = '$pate_dst_aids', 
													pate_diabetes = '$pate_diabetes', 
													pate_hipertensao = '$pate_hipertensao', 
													pate_hanseniase = '$pate_hanseniase', 
													pate_tuberculose = '$pate_tuberculose'
													WHERE pma2_codigo = ".intval($pma2_codigo) ;
				 	   $executaInsert = pg_query($stmt) or die($stmt);
						if ($executaInsert){
							echo $common->modalMsg("OK", "Dados de atendimento foram Alterados com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-3");
						}else{
							echo $common->modalMsg("ERRO", "Houve um erro e os dados de atendimento n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-2");
						}
					} 
			}
		echo $common->closeTab();
		
		echo $common->bodyTab('3');
		
		$select = "select * from pma2_solicitacao_exames where pma2_codigo = $_GET[pma2_codigo] ";
		$query = pg_query($select);
		$res_query = pg_fetch_array($query);
		
		$psole_patologia_clinica = $res_query[psole_patologia_clinica];
		$psole_radiodiagnostico = $res_query[psole_radiodiagnostico];
		$psole_citopatologico_cervico_vaginal = $res_query[psole_citopatologico_cervico_vaginal];
		$psole_ultrassonografia_obstetrica = $res_query[psole_ultrassonografia_obstetrica];
		$psole_outros = $res_query[psole_outros];
		
			
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarSolicitExames#tabs-3");
					$nomeForm = "solicitExames";
					$formulario.= $form->hiddenForm("numlinhas", pg_num_rows($query));
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->inputText("$nomeForm-patologiaClinica","$psole_patologia_clinica","Patologia Cl&iacute;nica","");
					$formulario .= $form->inputText("$nomeForm-radiodiagnostico","$psole_radiodiagnostico","Radiodiagn&oacute;stico","");
					$formulario .= $form->inputText("$nomeForm-citopalagicoCervicoVaginal","$psole_citopatologico_cervico_vaginal","Citopal&oacute;gico c&eacute;rvico-vaginal","");
					$formulario .= $form->inputText("$nomeForm-ultrassonografiaObstetrica","$psole_ultrassonografia_obstetrica","Ultrassonografia obst&eacute;trica","");
					$formulario .= $form->inputText("$nomeForm-outros","$psole_outros","Outros","");
					if($res_query[psole_codigo ]== ""){
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}
					$formulario .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
					
				$formulario .= $form->closeForm();
				echo $table->criaLinha(array("Solicitação M&eacute;dica de Exames Complementares", $formulario), array(40, '700'));
			echo $table->closeTable();
				
			if ($acao == "salvarSolicitExames"){
				if($_POST[numlinhas] == 0){
					$pma2_codigo = $_POST['pma2_codigo'];
					$psole_patologia_clinica = $_POST['solicitExames-patologiaClinica'];
					$psole_radiodiagnostico = $_POST['solicitExames-radiodiagnostico'];
					$psole_citopatologico_cervico_vaginal = $_POST['solicitExames-citopalagicoCervicoVaginal'];
					$psole_ultrassonografia_obstetrica = $_POST['solicitExames-ultrassonografiaObstetrica'];
					$psole_outros = $_POST['solicitExames-outros'];
					
					$insertSolicitExam = "INSERT INTO pma2_solicitacao_exames (pma2_codigo, 
																					 psole_patologia_clinica, 
																					 psole_radiodiagnostico, 
																					 psole_citopatologico_cervico_vaginal, 
																					 psole_ultrassonografia_obstetrica, 
																					 psole_outros
																					) 
																			VALUES 
																					($pma2_codigo, 
																					".intval($psole_patologia_clinica).", 
																					".intval($psole_radiodiagnostico).", 
																					".intval($psole_citopatologico_cervico_vaginal).", 
																					".intval($psole_ultrassonografia_obstetrica).", 
																					".intval($psole_outros)." 
																					)";
					
					$executaInsert = pg_query($insertSolicitExam) or die($insertSolicitExam);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de solicita&ccedil;&atilde;o de exames foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-4");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de solicita&ccedil;&atilde;o de exames n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-3");
					}
				}else{
					$pma2_codigo = $_POST['pma2_codigo'];
					$psole_patologia_clinica = $_POST['solicitExames-patologiaClinica'];
					$psole_radiodiagnostico = $_POST['solicitExames-radiodiagnostico'];
					$psole_citopatologico_cervico_vaginal = $_POST['solicitExames-citopalagicoCervicoVaginal'];
					$psole_ultrassonografia_obstetrica = $_POST['solicitExames-ultrassonografiaObstetrica'];
					$psole_outros = $_POST['solicitExames-outros'];
					 $stmt = "UPDATE pma2_solicitacao_exames SET 
											pma2_codigo = ".intval($pma2_codigo).", 
											psole_patologia_clinica = '$psole_patologia_clinica', 
											psole_radiodiagnostico = '$psole_radiodiagnostico', 
											psole_citopatologico_cervico_vaginal = '$psole_citopatologico_cervico_vaginal', 
											psole_ultrassonografia_obstetrica = '$psole_ultrassonografia_obstetrica', 
											psole_outros = '$psole_outros'
											WHERE pma2_codigo = ".intval($pma2_codigo) ;
					$executaInsert = pg_query($stmt) or die($stmt);
						if ($executaInsert){
							echo $common->modalMsg("OK", "Dados de solicita&ccedil;&atilde;o de exames foram Alteradas com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-4");
						}else{
							echo $common->modalMsg("ERRO", "Houve um erro e os dados de solicita&ccedil;&atilde;o de exames n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-3");
						}
				}
			}
		echo $common->closeTab();
		
		echo $common->bodyTab('4');
		
			$select = "select * from pma2_encaminhamento where pma2_codigo = $_GET[pma2_codigo] ";
			$query = pg_query($select);
			$res_query = pg_fetch_array($query);
			
			$penc_atend_especializado = $res_query[penc_atend_especializado];
			$penc_internacao_hospitalar = $res_query[penc_internacao_hospitalar];
			$penc_urgencia_emergencia = $res_query[penc_urgencia_emergencia];
			$penc_internacao_domiciliar = $res_query[penc_internacao_domiciliar];
			
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarEncaminhamento#tabs-4");
					$nomeForm = "encaminhamento";
					$formulario.= $form->hiddenForm("numlinhas", pg_num_rows($query));
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->inputText("$nomeForm-atendEspecializado","$penc_atend_especializado","Atend. Especializado","");
					$formulario .= $form->inputText("$nomeForm-internacaoHospitalar","$penc_internacao_hospitalar","Interna&ccedil;&atilde;o Hospitalar","");
					$formulario .= $form->inputText("$nomeForm-urgenciaEmergencia","$penc_urgencia_emergencia","Urg&ecirc;ncia/Emerg&ecirc;ncia","");
					$formulario2 = $form->inputText("$nomeForm-internacaoDomiciliar","$penc_internacao_domiciliar","Interna&ccedil;&atilde;o Domiciliar","");
					if($res_query[penc_codigo ]== ""){
						$formulario2 .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario2 .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}
					$formulario2 .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
				$formulario2 .= $form->closeForm();
				echo $table->criaLinha(array("Encaminhamentos M&eacute;dicos", $formulario), array(40, '700'));
				echo $table->criaLinha(array($formulario2), array(40, '700'), array(2,1));
			echo $table->closeTable();
				
			if ($acao == "salvarEncaminhamento"){
				if($_POST[numlinhas] == 0){
					$pma2_codigo = $_POST['pma2_codigo'];
					$penc_atend_especializado = $_POST['encaminhamento-atendEspecializado'];
					$penc_internacao_hospitalar = $_POST['encaminhamento-internacaoHospitalar'];
					$penc_urgencia_emergencia = $_POST['encaminhamento-urgenciaEmergencia'];
					$penc_internacao_domiciliar = $_POST['encaminhamento-internacaoDomiciliar'];
					
					$insertEncaminhamento = "INSERT INTO pma2_encaminhamento (pma2_codigo, 
																					penc_atend_especializado, 
																					penc_internacao_hospitalar, 
																					penc_urgencia_emergencia, 
																					penc_internacao_domiciliar
																				   )
																		 	 VALUES 
																		 	 	   ($pma2_codigo, 
																				   ".intval($penc_atend_especializado).", 
																				   ".intval($penc_internacao_hospitalar).", 
																				   ".intval($penc_urgencia_emergencia).", 
																				   ".intval($penc_internacao_domiciliar)."
																				   )";
					
					
					$executaInsert = pg_query($insertEncaminhamento) or die($insertEncaminhamento);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de encaminhamento foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-5");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de encaminhamento n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-4");
					}
				}else{
					$pma2_codigo = $_POST['pma2_codigo'];
					$penc_atend_especializado = $_POST['encaminhamento-atendEspecializado'];
					$penc_internacao_hospitalar = $_POST['encaminhamento-internacaoHospitalar'];
					$penc_urgencia_emergencia = $_POST['encaminhamento-urgenciaEmergencia'];
					$penc_internacao_domiciliar = $_POST['encaminhamento-internacaoDomiciliar'];
					 $stmt = "UPDATE pma2_encaminhamento SET 
										pma2_codigo = ".intval($pma2_codigo).", 
										penc_atend_especializado = '$penc_atend_especializado', 
										penc_internacao_hospitalar = '$penc_internacao_hospitalar', 
										penc_urgencia_emergencia = '$penc_urgencia_emergencia', 
										penc_internacao_domiciliar = '$penc_internacao_domiciliar'
										WHERE pma2_codigo = ".intval($pma2_codigo) ;
					$executaInsert = pg_query($stmt) or die($stmt);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de encaminhamento foram Alterados com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-5");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de encaminhamento n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-4");
					}
				} 
			}
		echo $common->closeTab();
		
		echo $common->bodyTab('5');
			$select = "select * from pma2_procedimentos where pma2_codigo = $_GET[pma2_codigo] ";
			$query = pg_query($select);
			$res_query = pg_fetch_array($query);
			
			$pproc_atend_especifico_at = $res_query[pproc_atend_especifico_at];
			$pproc_visita_inspecao_sanitaria = $res_query[pproc_visita_inspecao_sanitaria];
			$pproc_atend_individual_enfermeiro = $res_query[pproc_atend_individual_enfermeiro];
			$pproc_atend_individual_outros_prof_nivel_sup = $res_query[pproc_atend_individual_outros_prof_nivel_sup];
			$pproc_curativos = $res_query[pproc_curativos];
			$pproc_inalacoes = $res_query[pproc_inalacoes];
			$pproc_retirada_pontos = $res_query[pproc_retirada_pontos];
			$pproc_terapia_reidratacao_oral = $res_query[pproc_terapia_reidratacao_oral];
			$pproc_sutura = $res_query[pproc_sutura];
			$pproc_atend_grupo_educacao_saude = $res_query[pproc_atend_grupo_educacao_saude];
			$pproc_procedimentos_i = $res_query[pproc_procedimentos_i];
			
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarProcedimento#tabs-5");
					$nomeForm = "procedimento";
					$formulario.= $form->hiddenForm("numlinhas", pg_num_rows($query));
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->inputText("$nomeForm-atendimentoEspecificoAT","$pproc_atend_especifico_at","Atendimento espec&iacute;fico para AT","");
					$formulario .= $form->inputText("$nomeForm-visitaInspecaoSanitaria","$pproc_visita_inspecao_sanitaria","Visita de Inspe&ccedil;&atilde;o Sanit&aacute;ria","");
					$formulario .= $form->inputText("$nomeForm-atendIndividualEnfermeiro","$pproc_atend_individual_enfermeiro","Atend. individual Enfermeiro","");
					$formulario .= $form->inputText("$nomeForm-atendIndividual","$pproc_atend_individual_outros_prof_nivel_sup","Atend. individual outros prof. n&iacute;vel superior","");
					$formulario .= $form->inputText("$nomeForm-curativos","$pproc_curativos","Curativos","");
					$formulario .= $form->inputText("$nomeForm-inalacoes","$pproc_inalacoes","Inala&ccedil;&otilde;es","");
					$formulario .= $form->inputText("$nomeForm-retiradaPontos","$pproc_retirada_pontos","Retirada de pontos","");
					$formulario .= $form->inputText("$nomeForm-terapiaReidratacaoOral","$pproc_terapia_reidratacao_oral","Terapia da Reidrata&ccedil;&atilde;o Oral","");
					$formulario .= $form->inputText("$nomeForm-sutura","$pproc_sutura","Sutura","");
					$formulario .= $form->inputText("$nomeForm-atendGrupoEducacaoSaude","$pproc_atend_grupo_educacao_saude","Atend.Grupo-Educa&ccedil;&atilde;o em Sa&uacute;de","");
					$formulario .= $form->inputText("$nomeForm-procedimento","$pproc_procedimentos_i","Procedimentos I (PC I)","");
					if($res_query[pproc_codigo ]== ""){
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}					
					$formulario .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
				$formulario .= $form->closeForm();
				echo $table->criaLinha(array("Procedimentos", $formulario), array(40, '700'));
			echo $table->closeTable();
				
			if ($acao == "salvarProcedimento"){
				if($_POST[numlinhas] == 0){
					$pma2_codigo = $_POST['pma2_codigo'];
					$pproc_atend_especifico_at = $_POST['procedimento-atendimentoEspecificoAT'];
					$pproc_visita_inspecao_sanitaria = $_POST['procedimento-visitaInspecaoSanitaria'];
					$pproc_atend_individual_enfermeiro = $_POST['procedimento-atendIndividualEnfermeiro'];
					$pproc_atend_individual_outros_prof_nivel_sup = $_POST['procedimento-atendIndividual'];
					$pproc_curativos = $_POST['procedimento-curativos'];
					$pproc_inalacoes = $_POST['procedimento-inalacoes'];
					$pproc_retirada_pontos = $_POST['procedimento-retiradaPontos'];
					$pproc_terapia_reidratacao_oral = $_POST['procedimento-terapiaReidratacaoOral'];
					$pproc_sutura = $_POST['procedimento-sutura'];
					$pproc_atend_grupo_educacao_saude = $_POST['procedimento-atendGrupoEducacaoSaude'];
					$pproc_procedimentos_i = $_POST['procedimento-procedimento'];
					
					$insertProcedimento = "INSERT INTO pma2_procedimentos (pma2_codigo, 
																				 pproc_atend_especifico_at, 
																				 pproc_visita_inspecao_sanitaria, 
																				 pproc_atend_individual_enfermeiro, 
																				 pproc_atend_individual_outros_prof_nivel_sup, 
																				 pproc_curativos, 
																				 pproc_inalacoes, 
																				 pproc_retirada_pontos, 
																				 pproc_terapia_reidratacao_oral, 
																				 pproc_sutura, 
																				 pproc_atend_grupo_educacao_saude, 
																				 pproc_procedimentos_i
																				)
																		 VALUES 
																		 		($pma2_codigo, 
																				".intval($pproc_atend_especifico_at).", 
																				".intval($pproc_visita_inspecao_sanitaria).", 
																				".intval($pproc_atend_individual_enfermeiro).", 
																				".intval($pproc_atend_individual_outros_prof_nivel_sup).", 
																				".intval($pproc_curativos).", 
																				".intval($pproc_inalacoes).", 
																				".intval($pproc_retirada_pontos).", 
																				".intval($pproc_terapia_reidratacao_oral).", 
																				".intval($pproc_sutura).", 
																				".intval($pproc_atend_grupo_educacao_saude).", 
																				".intval($pproc_procedimentos_i)." 
																				)";
					
					$executaInsert = pg_query($insertProcedimento) or die($insertProcedimento);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de procedimento foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-6");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de procedimento n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-5");
					}
				}else{
					$pma2_codigo = $_POST['pma2_codigo'];
					$pproc_atend_especifico_at = $_POST['procedimento-atendimentoEspecificoAT'];
					$pproc_visita_inspecao_sanitaria = $_POST['procedimento-visitaInspecaoSanitaria'];
					$pproc_atend_individual_enfermeiro = $_POST['procedimento-atendIndividualEnfermeiro'];
					$pproc_atend_individual_outros_prof_nivel_sup = $_POST['procedimento-atendIndividual'];
					$pproc_curativos = $_POST['procedimento-curativos'];
					$pproc_inalacoes = $_POST['procedimento-inalacoes'];
					$pproc_retirada_pontos = $_POST['procedimento-retiradaPontos'];
					$pproc_terapia_reidratacao_oral = $_POST['procedimento-terapiaReidratacaoOral'];
					$pproc_sutura = $_POST['procedimento-sutura'];
					$pproc_atend_grupo_educacao_saude = $_POST['procedimento-atendGrupoEducacaoSaude'];
					$pproc_procedimentos_i = $_POST['procedimento-procedimento'];
					
					$stmt = "UPDATE pma2_procedimentos SET 
									pma2_codigo = ".intval($pma2_codigo).", 
									pproc_atend_especifico_at = '$pproc_atend_especifico_at', 
									pproc_visita_inspecao_sanitaria = '$pproc_visita_inspecao_sanitaria', 
									pproc_atend_individual_enfermeiro = '$pproc_atend_individual_enfermeiro', 
									pproc_atend_individual_outros_prof_nivel_sup = '$pproc_atend_individual_outros_prof_nivel_sup', 
									pproc_curativos = '$pproc_curativos', 
									pproc_inalacoes = '$pproc_inalacoes', 
									pproc_retirada_pontos = '$pproc_retirada_pontos', 
									pproc_terapia_reidratacao_oral = '$pproc_terapia_reidratacao_oral', 
									pproc_sutura = '$pproc_sutura', 
									pproc_atend_grupo_educacao_saude = '$pproc_atend_grupo_educacao_saude', 
									pproc_procedimentos_i = '$pproc_procedimentos_i'
									WHERE pma2_codigo = ".intval($pma2_codigo) ;
					$executaInsert = pg_query($stmt) or die($stmt);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de procedimento foram alterados com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-6");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de procedimento n&atilde;o foram Alterados, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-5");
					}
				} 
			}
		echo $common->closeTab();
						
		echo $common->bodyTab('6');
		$select = "select * from pma2_marcadores where pma2_codigo = $_GET[pma2_codigo] ";
			$query = pg_query($select);
			$res_query = pg_fetch_array($query);
			
			$pmar_valvulopatias_rematicas_cinco_quatorze = $res_query[pmar_valvulopatias_rematicas_cinco_quatorze];
			$pmar_avc = $res_query[pmar_avc];
			$pmar_infarto_agudo_miocardio = $res_query[pmar_infarto_agudo_miocardio];
			$pmar_dheg = $res_query[pmar_dheg];
			$pmar_doenca_hemolitica_perinatal = $res_query[pmar_doenca_hemolitica_perinatal];
			$pmar_fraturas_colo_femur_maior_cinquenta_anos = $res_query[pmar_fraturas_colo_femur_maior_cinquenta_anos];
			$pmar_meningite_tuberculosa_menores_cinco_anos = $res_query[pmar_meningite_tuberculosa_menores_cinco_anos];
			$pmar_hanseniase_grau_i_e_ii = $res_query[pmar_hanseniase_grau_i_e_ii];
			$pmar_citologia_oncotica = $res_query[pmar_citologia_oncotica];
			$pmar_rn_peso_menos_2500 = $res_query[pmar_rn_peso_menos_2500];
			$pmar_gravidez_menor_vinte_anos = $res_query[pmar_gravidez_menor_vinte_anos];
			$pmar_hospitalizacao_menor_cinco_anos_pneumonia = $res_query[pmar_hospitalizacao_menor_cinco_anos_pneumonia];
			$pmar_hospitalizacao_menor_cinco_anos_desidratacao = $res_query[pmar_hospitalizacao_menor_cinco_anos_desidratacao];
			$pmar_hospitalizacao_abuso_alcool = $res_query[pmar_hospitalizacao_abuso_alcool];
			$pmar_hospitalizacao_complicacao_diabetes = $res_query[pmar_hospitalizacao_complicacao_diabetes];
			$pmar_hospitalizacao_qualquer_causa = $res_query[pmar_hospitalizacao_qualquer_causa];
			$pmar_internacao_hospital_psiquiatrico = $res_query[pmar_internacao_hospital_psiquiatrico];
			$pmar_obitos_menor_um_ano_todas_as_causas = $res_query[pmar_obitos_menor_um_ano_todas_as_causas];
			$pmar_obitos_menor_um_ano_diarreia = $res_query[pmar_obitos_menor_um_ano_diarreia];
			$pmar_obitos_menor_um_ano_infeccao_respiratoria = $res_query[pmar_obitos_menor_um_ano_infeccao_respiratoria];
			$pmar_obitos_mulheres_dez_quarenta_e_nove_anos = $res_query[pmar_obitos_mulheres_dez_quarenta_e_nove_anos];
			$pmar_obitos_adolescentes_por_violencia = $res_query[pmar_obitos_adolescentes_por_violencia];
			
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarMarcadores#tabs-6");
					$nomeForm = "marcadores";
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->hiddenForm("numlinhas", pg_num_rows($query));
					$formulario .= $form->inputText("$nomeForm-valvulopatias","$pmar_valvulopatias_rematicas_cinco_quatorze","Valvulopatias  reum&aacute;ticas em pessoas de 5 a 14 anos","");
					$formulario .= $form->inputText("$nomeForm-avc","$pmar_avc","Acidente Vascular Cerebral","");
					$formulario .= $form->inputText("$nomeForm-infarto","$pmar_infarto_agudo_miocardio","Infarto Agudo do Mioc&aacute;rdio","");
					$formulario .= $form->inputText("$nomeForm-dheg","$pmar_dheg","DHEG (forma grave)","");
					$formulario .= $form->inputText("$nomeForm-doencaHemoliaticaPerinatal","$pmar_doenca_hemolitica_perinatal","Doen&ccedil;a Hemol&iacute;tica Perinatal","");
					$formulario .= $form->inputText("$nomeForm-fraturasColoFemurMaiorCinquentaAnos","$pmar_fraturas_colo_femur_maior_cinquenta_anos","Fraturas de colo de f&ecirc;mur em > 50 anos","");
					$formulario .= $form->inputText("$nomeForm-meningiteTuberculosaMenorCincoAnos","$pmar_meningite_tuberculosa_menores_cinco_anos","Meningite tuberculosa em menores de 5 anos","");
					$formulario .= $form->inputText("$nomeForm-hanseniaseGrauIeII","$pmar_hanseniase_grau_i_e_ii","Hansen&iacute;ase com grau de incapacidade II e III","");
					$formulario .= $form->inputText("$nomeForm-citologiaOncotica","$pmar_citologia_oncotica","Citologia Onc&oacute;tica NIC III (carcinoma in situ)","");
					$formulario .= $form->inputText("$nomeForm-rnPesoMenor2500","$pmar_rn_peso_menos_2500","RN com peso < 2500g","");
					$formulario .= $form->inputText("$nomeForm-gravidezMenorVinteAnos","$pmar_gravidez_menor_vinte_anos","Gravidez em < 20 anos","");
					$formulario .= $form->inputText("$nomeForm-hospitalizacoesMenorCincoAnosPneumonia","$pmar_hospitalizacao_menor_cinco_anos_pneumonia","Hospitaliza&ccedil;&otilde;es em < 5 anos por pneumonia","");
					$formulario .= $form->inputText("$nomeForm-hospitalizacoesMenorCincoAnosDesidratacao","$pmar_hospitalizacao_menor_cinco_anos_desidratacao","Hospitaliza&ccedil;&otilde;es em < 5 anos por desidrata&ccedil;&atilde;o","");
					$formulario .= $form->inputText("$nomeForm-hospitalizacoesAbusoAlcool","$pmar_hospitalizacao_abuso_alcool","Hospitaliza&ccedil;&otilde;es por abuso de &Aacute;lcool","");
					$formulario .= $form->inputText("$nomeForm-hospitalizacoesComplicacoesDiabetes","$pmar_hospitalizacao_complicacao_diabetes","Hospitaliza&ccedil;&otilde;es por complica&ccedil;&otilde;es do Diabetes","");
					$formulario .= $form->inputText("$nomeForm-hospitalizacoesQualquerCausa","$pmar_hospitalizacao_qualquer_causa","Hospitaliza&ccedil;&otilde;es por qualquer causa","");
					$formulario .= $form->inputText("$nomeForm-internacoesHospitalPsiquiatrico","$pmar_internacao_hospital_psiquiatrico","Interna&ccedil;&otilde;es em Hospital Psiqui&aacute;trico","");
					$formulario .= $form->inputText("$nomeForm-obitosMenoresUmAnoTodasAsCausas","$pmar_obitos_menor_um_ano_todas_as_causas","&Oacute;bitos em < 1 ano por todas as causas","");
					$formulario .= $form->inputText("$nomeForm-obitosMenoresUmAnoDiarreia","$pmar_obitos_menor_um_ano_diarreia","&Oacute;bitos em < 1 ano por diarr&eacute;ia","");
					$formulario .= $form->inputText("$nomeForm-obitosMenoresUmAnoInfeccaoRespiratoria","$pmar_obitos_menor_um_ano_infeccao_respiratoria","&Oacute;bitos em < 1 ano por infec&ccedil;&atilde;o respirat&oacute;ria","");
					$formulario .= $form->inputText("$nomeForm-obitosMulheresDezAQuarentaENoveAnos","$pmar_obitos_mulheres_dez_quarenta_e_nove_anos","&Oacute;bitos de mulheres de 10 a 49 anos","");
					$formulario .= $form->inputText("$nomeForm-obitosAdolescentes","$pmar_obitos_adolescentes_por_violencia","&Oacute;bitos adolesc. por Viol&ecirc;ncia","");
					if($res_query[pmar_codigo ]== ""){
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}	
					$formulario .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
				$formulario .= $form->closeForm();
				echo $table->criaLinha(array("Marcadores", $formulario), array(40, '700'));
			echo $table->closeTable();
				
			if ($acao == "salvarMarcadores"){
				if($_POST[numlinhas] == 0){
					$pma2_codigo = $_POST['pma2_codigo'];
					$pmar_valvulopatias_rematicas_cinco_quatorze = $_POST['marcadores-valvulopatias']; 
					$pmar_avc = $_POST['marcadores-avc']; 
					$pmar_infarto_agudo_miocardio = $_POST['marcadores-infarto']; 
					$pmar_dheg = $_POST['marcadores-dheg']; 
					$pmar_doenca_hemolitica_perinatal = $_POST['marcadores-doencaHemoliaticaPerinatal']; 
					$pmar_fraturas_colo_femur_maior_cinquenta_anos = $_POST['marcadores-fraturasColoFemurMaiorCinquentaAnos']; 
					$pmar_meningite_tuberculosa_menores_cinco_anos = $_POST['marcadores-meningiteTuberculosaMenorCincoAnos']; 
					$pmar_hanseniase_grau_i_e_ii = $_POST['marcadores-hanseniaseGrauIeII']; 
					$pmar_citologia_oncotica = $_POST['marcadores-citologiaOncotica']; 
					$pmar_rn_peso_menos_2500 = $_POST['marcadores-rnPesoMenor2500']; 
					$pmar_gravidez_menor_vinte_anos = $_POST['marcadores-gravidezMenorVinteAnos']; 
					$pmar_hospitalizacao_menor_cinco_anos_pneumonia = $_POST['marcadores-hospitalizacoesMenorCincoAnosPneumonia']; 
					$pmar_hospitalizacao_menor_cinco_anos_desidratacao = $_POST['marcadores-hospitalizacoesMenorCincoAnosDesidratacao']; 
					$pmar_hospitalizacao_abuso_alcool = $_POST['marcadores-hospitalizacoesAbusoAlcool']; 
					$pmar_hospitalizacao_complicacao_diabetes = $_POST['marcadores-hospitalizacoesComplicacoesDiabetes']; 
					$pmar_hospitalizacao_qualquer_causa = $_POST['marcadores-hospitalizacoesQualquerCausa']; 
					$pmar_internacao_hospital_psiquiatrico = $_POST['marcadores-internacoesHospitalPsiquiatrico']; 
					$pmar_obitos_menor_um_ano_todas_as_causas = $_POST['marcadores-obitosMenoresUmAnoTodasAsCausas']; 
					$pmar_obitos_menor_um_ano_diarreia = $_POST['marcadores-obitosMenoresUmAnoDiarreia']; 
					$pmar_obitos_menor_um_ano_infeccao_respiratoria = $_POST['marcadores-obitosMenoresUmAnoInfeccaoRespiratoria']; 
					$pmar_obitos_mulheres_dez_quarenta_e_nove_anos = $_POST['marcadores-obitosMulheresDezAQuarentaENoveAnos']; 
					$pmar_obitos_adolescentes_por_violencia = $_POST['marcadores-obitosAdolescentes'];
					
					$insertMarcadores = "INSERT INTO pma2_marcadores (pma2_codigo, 
																			pmar_valvulopatias_rematicas_cinco_quatorze, 
																			pmar_avc, 
																			pmar_infarto_agudo_miocardio, 
																			pmar_dheg, 
																			pmar_doenca_hemolitica_perinatal, 
																			pmar_fraturas_colo_femur_maior_cinquenta_anos, 
																			pmar_meningite_tuberculosa_menores_cinco_anos, 
																			pmar_hanseniase_grau_i_e_ii, 
																			pmar_citologia_oncotica, 
																			pmar_rn_peso_menos_2500, 
																			pmar_gravidez_menor_vinte_anos, 
																			pmar_hospitalizacao_menor_cinco_anos_pneumonia, 
																			pmar_hospitalizacao_menor_cinco_anos_desidratacao, 
																			pmar_hospitalizacao_abuso_alcool, 
																			pmar_hospitalizacao_complicacao_diabetes, 
																			pmar_hospitalizacao_qualquer_causa, 
																			pmar_internacao_hospital_psiquiatrico, 
																			pmar_obitos_menor_um_ano_todas_as_causas, 
																			pmar_obitos_menor_um_ano_diarreia, 
																			pmar_obitos_menor_um_ano_infeccao_respiratoria, 
																			pmar_obitos_mulheres_dez_quarenta_e_nove_anos, 
																			pmar_obitos_adolescentes_por_violencia
																		   ) 
																	VALUES 
																		   ($pma2_codigo, 
																		   ".intval($pmar_valvulopatias_rematicas_cinco_quatorze).", 
																		   ".intval($pmar_avc).", 
																		   ".intval($pmar_infarto_agudo_miocardio).", 
																		   ".intval($pmar_dheg).", 
																		   ".intval($pmar_doenca_hemolitica_perinatal).", 
																		   ".intval($pmar_fraturas_colo_femur_maior_cinquenta_anos).", 
																		   ".intval($pmar_meningite_tuberculosa_menores_cinco_anos).", 
																		   ".intval($pmar_hanseniase_grau_i_e_ii).", 
																		   ".intval($pmar_citologia_oncotica).", 
																		   ".intval($pmar_rn_peso_menos_2500).", 
																		   ".intval($pmar_gravidez_menor_vinte_anos).", 
																		   ".intval($pmar_hospitalizacao_menor_cinco_anos_pneumonia).", 
																		   ".intval($pmar_hospitalizacao_menor_cinco_anos_desidratacao).", 
																		   ".intval($pmar_hospitalizacao_abuso_alcool).", 
																		   ".intval($pmar_hospitalizacao_complicacao_diabetes).", 
																		   ".intval($pmar_hospitalizacao_qualquer_causa).", 
																		   ".intval($pmar_internacao_hospital_psiquiatrico).", 
																		   ".intval($pmar_obitos_menor_um_ano_todas_as_causas).", 
																		   ".intval($pmar_obitos_menor_um_ano_diarreia).", 
																		   ".intval($pmar_obitos_menor_um_ano_infeccao_respiratoria).", 
																		   ".intval($pmar_obitos_mulheres_dez_quarenta_e_nove_anos).", 
																		   ".intval($pmar_obitos_adolescentes_por_violencia)."
																		   )";
					
					$executaInsert = pg_query($insertMarcadores) or die($insertMarcadores);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de marcadores foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-7");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de marcadores n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-6");
					}
				}else{
					$pma2_codigo = $_POST['pma2_codigo'];
					$pmar_valvulopatias_rematicas_cinco_quatorze = $_POST['marcadores-valvulopatias']; 
					$pmar_avc = $_POST['marcadores-avc']; 
					$pmar_infarto_agudo_miocardio = $_POST['marcadores-infarto']; 
					$pmar_dheg = $_POST['marcadores-dheg']; 
					$pmar_doenca_hemolitica_perinatal = $_POST['marcadores-doencaHemoliaticaPerinatal']; 
					$pmar_fraturas_colo_femur_maior_cinquenta_anos = $_POST['marcadores-fraturasColoFemurMaiorCinquentaAnos']; 
					$pmar_meningite_tuberculosa_menores_cinco_anos = $_POST['marcadores-meningiteTuberculosaMenorCincoAnos']; 
					$pmar_hanseniase_grau_i_e_ii = $_POST['marcadores-hanseniaseGrauIeII']; 
					$pmar_citologia_oncotica = $_POST['marcadores-citologiaOncotica']; 
					$pmar_rn_peso_menos_2500 = $_POST['marcadores-rnPesoMenor2500']; 
					$pmar_gravidez_menor_vinte_anos = $_POST['marcadores-gravidezMenorVinteAnos']; 
					$pmar_hospitalizacao_menor_cinco_anos_pneumonia = $_POST['marcadores-hospitalizacoesMenorCincoAnosPneumonia']; 
					$pmar_hospitalizacao_menor_cinco_anos_desidratacao = $_POST['marcadores-hospitalizacoesMenorCincoAnosDesidratacao']; 
					$pmar_hospitalizacao_abuso_alcool = $_POST['marcadores-hospitalizacoesAbusoAlcool']; 
					$pmar_hospitalizacao_complicacao_diabetes = $_POST['marcadores-hospitalizacoesComplicacoesDiabetes']; 
					$pmar_hospitalizacao_qualquer_causa = $_POST['marcadores-hospitalizacoesQualquerCausa']; 
					$pmar_internacao_hospital_psiquiatrico = $_POST['marcadores-internacoesHospitalPsiquiatrico']; 
					$pmar_obitos_menor_um_ano_todas_as_causas = $_POST['marcadores-obitosMenoresUmAnoTodasAsCausas']; 
					$pmar_obitos_menor_um_ano_diarreia = $_POST['marcadores-obitosMenoresUmAnoDiarreia']; 
					$pmar_obitos_menor_um_ano_infeccao_respiratoria = $_POST['marcadores-obitosMenoresUmAnoInfeccaoRespiratoria']; 
					$pmar_obitos_mulheres_dez_quarenta_e_nove_anos = $_POST['marcadores-obitosMulheresDezAQuarentaENoveAnos']; 
					$pmar_obitos_adolescentes_por_violencia = $_POST['marcadores-obitosAdolescentes'];
					$stmt = "UPDATE pma2_marcadores SET 
										pma2_codigo = ".intval($pma2_codigo).", 
										pmar_valvulopatias_rematicas_cinco_quatorze = '$pmar_valvulopatias_rematicas_cinco_quatorze', 
										pmar_avc = '$pmar_avc', 
										pmar_infarto_agudo_miocardio = '$pmar_infarto_agudo_miocardio', 
										pmar_dheg = '$pmar_dheg', 
										pmar_doenca_hemolitica_perinatal = '$pmar_doenca_hemolitica_perinatal', 
										pmar_fraturas_colo_femur_maior_cinquenta_anos = '$pmar_fraturas_colo_femur_maior_cinquenta_anos', 
										pmar_meningite_tuberculosa_menores_cinco_anos = '$pmar_meningite_tuberculosa_menores_cinco_anos', 
										pmar_hanseniase_grau_i_e_ii = '$pmar_hanseniase_grau_i_e_ii', 
										pmar_citologia_oncotica = '$pmar_citologia_oncotica', 
										pmar_rn_peso_menos_2500 = '$pmar_rn_peso_menos_2500', 
										pmar_gravidez_menor_vinte_anos = '$pmar_gravidez_menor_vinte_anos', 
										pmar_hospitalizacao_menor_cinco_anos_pneumonia = '$pmar_hospitalizacao_menor_cinco_anos_pneumonia', 
										pmar_hospitalizacao_menor_cinco_anos_desidratacao = '$pmar_hospitalizacao_menor_cinco_anos_desidratacao', 
										pmar_hospitalizacao_abuso_alcool = '$pmar_hospitalizacao_abuso_alcool', 
										pmar_hospitalizacao_complicacao_diabetes = '$pmar_hospitalizacao_complicacao_diabetes', 
										pmar_hospitalizacao_qualquer_causa = '$pmar_hospitalizacao_qualquer_causa', 
										pmar_internacao_hospital_psiquiatrico = '$pmar_internacao_hospital_psiquiatrico', 
										pmar_obitos_menor_um_ano_todas_as_causas = '$pmar_obitos_menor_um_ano_todas_as_causas', 
										pmar_obitos_menor_um_ano_diarreia = '$pmar_obitos_menor_um_ano_diarreia', 
										pmar_obitos_menor_um_ano_infeccao_respiratoria = '$pmar_obitos_menor_um_ano_infeccao_respiratoria', 
										pmar_obitos_mulheres_dez_quarenta_e_nove_anos = '$pmar_obitos_mulheres_dez_quarenta_e_nove_anos', 
										pmar_obitos_adolescentes_por_violencia = '$pmar_obitos_adolescentes_por_violencia'
										WHERE pma2_codigo = ".intval($pma2_codigo) ;
					$executaInsert = pg_query($stmt) or die($stmt);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de marcadores foram alterados com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-7");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de marcadores n&atilde;o foram alterados, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-6");
					}
				} 
			}
		echo $common->closeTab();
		
		echo $common->bodyTab('7');
		
		$select = "select * from pma2_visitas_domiciliares where pma2_codigo = $_GET[pma2_codigo] ";
			$query = pg_query($select);
			$res_query = pg_fetch_array($query);
			
			$pvisdom_medico = $res_query[pvisdom_medico];
			$pvisdom_enfermeiro = $res_query[pvisdom_enfermeiro];
			$pvisdom_outros_prof_nivel_superior = $res_query[pvisdom_outros_prof_nivel_superior];
			$pvisdom_prof_nivel_medio = $res_query[pvisdom_prof_nivel_medio];
			$pvisdom_acs = $res_query[pvisdom_acs];
			
			echo $table->openTable('form');
				echo $table->criaLinha(array("Consultas M&eacute;dicas"),array(40, 700), array(2,1),"S");
				$formulario = $form->openForm("fichaPMA2.php?acao=salvarVisitasDomiciliares#tabs-7");
					$nomeForm = "visitasDomiciliares";
					
					$formulario .= $form->hiddenForm("pma2_codigo", $pma2_codigo);
					$formulario .= $form->hiddenForm("numlinhas", pg_num_rows($query));
					$formulario .= $form->inputText("$nomeForm-medico","$pvisdom_medico","M&eacute;dico",null,null,"onBlur=somaTudo(\"$nomeForm\")");
					$formulario .= $form->inputText("$nomeForm-enfermeiro","$pvisdom_enfermeiro","Enfermeiro",null,null,"onBlur=somaTudo(\"$nomeForm\")");
					$formulario .= $form->inputText("$nomeForm-outrosProfissionaisNivelSuperior","$pvisdom_outros_prof_nivel_superior","Outros profissionais de n&iacute;vel superior",null,null,"onBlur=somaTudo(\"$nomeForm\")");
					$formulario .= $form->inputText("$nomeForm-profissionaisNivelMedico","$pvisdom_prof_nivel_medio","Profissionais de n&iacute;vel m&eacute;dio",null,null,"onBlur=somaTudo(\"$nomeForm\")");
					$formulario .= $form->inputText("$nomeForm-acs","$pvisdom_acs","ACS",null,null,"onBlur=somaTudo(\"$nomeForm\")");
					$formulario .= $form->inputText("total-$nomeForm","","Total",null,null,null, "text", "S");
					if($res_query[pvisdom_codigo ]== ""){
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");	
					}else{
						$formulario .= $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg");
					}
					$formulario .= $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
				$formulario .= $form->closeForm();
				echo $table->criaLinha(array("Visitas Domiciliares", $formulario), array(40, '700'));
			echo $table->closeTable();
				
			if ($acao == "salvarVisitasDomiciliares"){
				if($_POST[numlinhas] == 0){
					$pma2_codigo = $_POST['pma2_codigo'];
					$pvisdom_medico = $_POST['visitasDomiciliares-medico'];
					$pvisdom_enfermeiro = $_POST['visitasDomiciliares-enfermeiro'];
					$pvisdom_outros_prof_nivel_superior = $_POST['visitasDomiciliares-outrosProfissionaisNivelSuperior'];
					$pvisdom_prof_nivel_medio = $_POST['visitasDomiciliares-profissionaisNivelMedico'];
					$pvisdom_acs = $_POST['visitasDomiciliares-acs'];
					
					$insertVisitasDomiciliares = "INSERT INTO pma2_visitas_domiciliares ( pma2_codigo, 
																								pvisdom_medico, 
																								pvisdom_enfermeiro, 
																								pvisdom_outros_prof_nivel_superior, 
																								pvisdom_prof_nivel_medio, 
																								pvisdom_acs
																							  )
																					   VALUES 
																					   		  ( $pma2_codigo, 
																							   ".intval($pvisdom_medico).", 
																							   ".intval($pvisdom_enfermeiro).", 
																							   ".intval($pvisdom_outros_prof_nivel_superior).", 
																							   ".intval($pvisdom_prof_nivel_medio).", 
																							   ".intval($pvisdom_acs)." 
																							  )";
					
					$executaInsert = pg_query($insertVisitasDomiciliares) or die($insertVisitasDomiciliares);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de visitas domiciliares foram salvos com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-1");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de visitas domiciliares n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-7");
					}
				}else{
					$pma2_codigo = $_POST['pma2_codigo'];
					$pvisdom_medico = $_POST['visitasDomiciliares-medico'];
					$pvisdom_enfermeiro = $_POST['visitasDomiciliares-enfermeiro'];
					$pvisdom_outros_prof_nivel_superior = $_POST['visitasDomiciliares-outrosProfissionaisNivelSuperior'];
					$pvisdom_prof_nivel_medio = $_POST['visitasDomiciliares-profissionaisNivelMedico'];
					$pvisdom_acs = $_POST['visitasDomiciliares-acs'];
					
					 $stmt = "UPDATE pma2_visitas_domiciliares SET 
										pma2_codigo = ".intval($pma2_codigo).", 
										pvisdom_medico = '$pvisdom_medico', 
										pvisdom_enfermeiro = '$pvisdom_enfermeiro', 
										pvisdom_outros_prof_nivel_superior = '$pvisdom_outros_prof_nivel_superior', 
										pvisdom_prof_nivel_medio = '$pvisdom_prof_nivel_medio', 
										pvisdom_acs = '$pvisdom_acs'
										WHERE pma2_codigo = ".intval($pma2_codigo) ;
					$executaInsert = pg_query($stmt) or die($stmt);
					if ($executaInsert){
						echo $common->modalMsg("OK", "Dados de visitas domiciliares foram alterado com sucesso!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-1");
					}else{
						echo $common->modalMsg("ERRO", "Houve um erro e os dados de visitas domiciliares n&atilde;o foram alterado, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-7");
					}
				} 
			}
		echo $common->closeTab();
						
		echo $common->closeTab();
		?>
	</body>
</html>