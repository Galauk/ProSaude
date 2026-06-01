<script type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../preNatal/sisprenatal.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
/*TODOS O QUE INVOLVER JAVASCRIPT ESTA NO ARQUIVO SISPRENATAL.JS*/
/*TODOS O QUE INVOLVER FUNCAO AÇÕES DE BANCO COMO INSERT E UPDATE ESTA NO ARQUIVO SQLPRENATAL.PHP*/
$common = new commonClass();
$table = new tableClass();
$form = new classForm();
echo $common->incJquery();
?><script>

function abrirCarterinha(){
	var paciente = '<?php echo $usu_codigo;?>';
	url = "/WebSocialSaude/zf/vacina/imprimir-carteirinha/usu/"+paciente;
	window.open(url, null,"height=620,width=650,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}

</script><?php 
	$sqlSexo = "SELECT *
				  FROM usuario 
				 WHERE usu_codigo = $usu_codigo";
	$querySexo = pg_query($sqlSexo);
	$linhaSexo = pg_fetch_array($querySexo);
	
	if($linhaSexo["usu_sexo"] == "M"){
			echo $common->modalMsg("ERRO","O paciente escolhido e do sexo Masculino !","prontuario.php?pagina=99&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data");
	}
	
	echo $common->menuTab(array("Pre-natal","Historico"));
		echo $common->bodyTab("1");
		/*ESSE SELECT PEGA OS PRENATAIS QUE NAO FORAM INTERROMPIDOS NEM TERMINADOS.*/
			$sqlPegaTudo = " SELECT to_char(sispn_data_cadastro,'DD/MM/YYYY') AS primeira_consulta,
									to_char(sispn_data_ultima_menstruacao,'DD/MM/YYYY') as ultima_mens,
									to_char(sispn_data_provavel_parto,'DD/MM/YYYY') as data_parto,
									* 
							   FROM sis_pre_natal as sisp
							   JOIN atendimento as ate
							     ON ate.sispn_codigo = sisp.sispn_codigo
							  WHERE ate.usu_codigo = $usu_codigo
							    AND ate_tipo_consulta_prenatal NOT IN (5,9)
							  ORDER BY ate_data,ate_hora DESC";
			//die($sqlPegaTudo);
			$queryTudo = pg_query($sqlPegaTudo);
			$regTudo = pg_fetch_array($queryTudo);
			
			print "<fieldset>
			<legend>Historico de Pre Consultas</legend>
			";
			
			$stmt = "SELECT pc_codigo, TO_CHAR(pc_data,'DD/MM/YYYY as HH24:MI') as data 
					   FROM pre_consulta AS pc
					NATURAL JOIN agendamento AS ag
					  WHERE ag.usu_codigo = $usu_codigo
					    AND pc.age_codigo = $age_codigo 
					  ORDER by pc_codigo desc 
			";
			
			$qry = db_query( $stmt );
			
			if( pg_num_rows($qry) == 0 ) print "<strong>nenhuma...</strong>";
			$consultas = array();
			while( $row = pg_fetch_array($qry) )
			{
				$consultas[] = "<a href='prontuario.php?pagina=17&modal=true&id_login=$id_login&ate_codigo=$ate[ate_codigo]&age_codigo=$age_codigo&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&age_data=$age_data&codigo=$row[0]'>$row[1]</a>";
			}
			if ($modal == "true"){
				$id_login = $_GET['id_login'];
				$codigo = $_GET['codigo'];
				include "../pre_consulta_popup.php";
			}
			print join( ",&nbsp; ", $consultas );
			print "</fieldset>";
			
			echo $form->openForm("../preNatal/sqlPrenatal.php","POST","prenatal");
			/*ARRAYS  */
			$arrayTipoConsulta = array("1"=>"Pre-natal",
									   "3"=>"Parto",
									   "5"=>"Puerp&eacute;rio",
									   "9"=>"Interrup&ccedil;&atildeo");
			
			$arrayTipoParto = array("00"=>"N&atildeo Informado",
									"20"=>"Parto Domiciliar",
									"30"=>"Parto Hospitalar",
									"99"=>"Ignorado");
			
			$arrayTipoInterrupcao = array("01"=>"Abortamento",
										  "02"=>"Conv&ecirc;nio Particular",
										  "03"=>"Mudan&ccedil;a",
										  "04"=>"&Oacute;bito",
										  "99"=>"Outros");
			$arrayTipoRisco = array("00" => "N&atilde;o Informado",
									"20" => "Baixo Risco",
									"90" => "Alto Risco",
									"99" => "Risco Ignorado");
			/*fim de arrays */
			
			/*Pega numero de gestacao*/
			if($regTudo[sispn_numero_gestacao] == ""){
				$verificaPrenatal = "SELECT MAX(sispn_numero_gestacao) AS num_prenatal 
									   FROM sis_pre_natal";
				$queryVerificaPrenatal = pg_query($verificaPrenatal);
				$linhaVerificaPrenatal = pg_fetch_array($queryVerificaPrenatal);
				
				if($linhaVerificaPrenatal[num_prenatal] == ""){
					$selectBaseSequencia = "SELECT MAX(ngest_numero_inicial) as ngest_seq_inicial 
											  FROM numero_gestacao";
					$queryBaseSequencia = pg_query($selectBaseSequencia);
					$linhaBase = pg_fetch_array($queryBaseSequencia);
					$sispn_numero_gestacao = $linhaBase[ngest_seq_inicial];
				}else{
					$sispn_numero_gestacao = $linhaVerificaPrenatal[num_prenatal]+1;
				}
			}else{
				$sispn_numero_gestacao = $regTudo[sispn_numero_gestacao];
			}
			
			/*Fim de pegar o numero de getacao*/
			
			/*PEGA PESO*/
				$sqlPeso = "SELECT * 
						      FROM pre_consulta 
						     WHERE age_codigo = $age_codigo";
				$queryPeso = pg_query($sqlPeso);
				$linhaPeso = pg_fetch_array($queryPeso);
				$ate_peso = $linhaPeso["pc_peso"];

			if($ate_codigo != ""){
				$verificaDataConsulta = "SELECT *
									   FROM atendimento
									  WHERE ate_codigo = $ate_codigo";
				$queryVerificaDataConsulta = pg_query($verificaDataConsulta);
				$linhaVerificaDataConsulta = pg_fetch_array($queryVerificaDataConsulta);
				$dataConsulta = $linhaVerificaDataConsulta[ate_data];
				
				$dataAtual = date('Y-m-d');
				$ex1 = explode("-",$dataAtual);
				$ex2 = explode("-",$dataConsulta);
				
				$dataAtualCerta = $ex1[0].$ex1[1].$ex2[2];
				$dataConsultaCerta = $ex2[0].$ex2[1].$ex2[2];
				if($dataAtualCerta < $dataConsultaCerta){
					$readonly = "S";
				}
				if($regTudo[sispn_tipo_interrupcao] != "00"){
					$interrupcao = "S";	
				}
			}else{
				$readonly = "null";
				$interrupcao = "N";
			}
			
			if($ate_peso == ""){
				$pegaPesoAtendimento = "SELECT ate_codigo,
											   ate_peso
										  FROM atendimento
										 WHERE ate_codigo = $ate_codigo";
				$queryPegaPesoAtendimento = pg_query($pegaPesoAtendimento);
				$linhaPegaPesoAtendimento = pg_fetch_array($queryPegaPesoAtendimento);
				$ate_peso = $linhaPegaPesoAtendimento[ate_peso];
			}
						$parametro = "xx";
			$topo = $form->inputText("sispn_numero_gestacao","$sispn_numero_gestacao","Numero da Gesta&ccedil;&atilde;o",null,null,null,null,"S").
					$form->inputSelect("sispn_tipo_consulta",
										$arrayTipoConsulta,
										"Tipo de Consulta",
										null,
										"onChange=\"mostraParto();\"",
									   null,
									   $regTudo[ate_tipo_consulta_prenatal]).
					"<div id='oculta' style='display:none;'>".
						$form->inputSelect("sispn_tipo_parto",$arrayTipoParto,"Tipo de parto").
					"</div>".
					"<div id='interrupcao' style='display:none;'>".
						$form->inputSelect("sispn_tipo_interrupcao",$arrayTipoInterrupcao,"Tipo de Interrup&ccedil;&atilde;o").
					"</div>".
					$form->inputText("sispn_data_cadastro","$regTudo[primeira_consulta]","Data da primeira consulta",null,10,"onKeypress=\"return Ajusta_Data(this, event);\"",null,"$readonly").
					$form->inputText("sispn_data_ultima_menstruacao","$regTudo[ultima_mens]","Data ultima menstruacao",null,10,"onKeypress=\"return Ajusta_Data(this, event);\"",null,"$readonly").
					$form->inputText("sispn_data_provavel_parto","$regTudo[data_parto]","Data provavel Parto",null,10,"onKeypress=\"return Ajusta_Data(this, event);\"",null,"$readonly").
					$form->inputText("ate_peso","$ate_peso","Peso").
					$form->inputSelect("sispn_classificacao_risco",$arrayTipoRisco,"Classifica&ccedil;&atilde;o de risco",null,null,null,$regTudo[ate_classificacao_risco_prenatal]);
	   		/*Fim do topo */
				//chamarPuerperal($usu_codigo,$regTudo[sispn_codigo],$age_codigo,$ate_codigo,$med_codigo,$uni_codigo,'$age_data')			
				$sqlNomeVacina = "SELECT *
									FROM produto
								   WHERE pro_nome LIKE '%ANTI-TET%'";
				$queryNomeVacina = pg_query($sqlNomeVacina);
				$regNomeVacina = pg_fetch_array($queryNomeVacina);
				$pro_codigo = $regNomeVacina[pro_codigo];
				
				echo $form->hiddenForm("pro_codigo","$pro_codigo");
				echo $form->hiddenForm("id_login","$id_login");
				echo $form->hiddenForm("usu_codigo","$usu_codigo");
				echo $form->hiddenForm("age_codigo","$age_codigo");
				echo $form->hiddenForm("med_codigo","$med_codigo");
				echo $form->hiddenForm("age_codigo","$age_codigo");
				echo $form->hiddenForm("age_data","$age_data");
				echo $form->hiddenForm("esp_codigo","$esp_codigo");
				echo $form->hiddenForm("uni_codigo","$uni_codigo");
				echo $form->hiddenForm("sispn_codigo","$regTudo[sispn_codigo]");
				if($ate_codigo == ""){
					$ate_codigo1 = $regTudo[ate_codigo];
					echo $form->hiddenForm("ate_codigo1","$ate_codigo1");
				}else{
				echo $form->hiddenForm("ate_codigo","$ate_codigo");
				}
				$sqlVacinasTomadas = "SELECT *, 
											 to_char(vac_data,'DD/MM/YYYY') AS data
										FROM vacina_usuario
									   WHERE usu_codigo = $usu_codigo
									     AND pro_codigo = $pro_codigo
									   ORDER BY vac_data desc limit 1";
				$queryVacinasTomadas = pg_query($sqlVacinasTomadas);
				while($linha = pg_fetch_array($queryVacinasTomadas)){
					if($linha[vac_dose] == 1 && $linha[vac_acao] != 'Z'){
						$primeira = $linha[data];
					}else if($linha[vac_dose] == 2 && $linha[vac_acao] != 'Z'){
						$segunda = $linha[data];
					}else if($linha[vac_dose == 6] && $linha[vac_acao] != 'Z'){
						$reforco = $linha[data];
					}else if($linha[vac_dose == 9] && $linha[vac_acao] != 'Z'){
						$imune = $linha[data];
					}
				}
				$vacina = "<fieldset style='width: 180px; height:100px;'>
								<legend>Vacina</legend>
								".$common->commonButton("Ver Carteirinha", "javascript:abrirCarterinha();","recepcionar_calendar.png","onclick=\"abrirCarterinha();\"")."
						  </fieldset>";
				
				$exames = " <fieldset style='width:500px;'>
							<table border='0'>
									<legend>Exames </legend>
									<tr>";
										$sqlExames = "SELECT * 
														FROM procedimentos_sisprenatal 
													   ORDER BY proc_sis_nome_generico";
										$queryExames = pg_query($sqlExames);
										$cont = 1;
										
										while($line = pg_fetch_array($queryExames)){
											$sqlVerificaColetaExames = "SELECT * 
																		  FROM (SELECT proc_codigo, 
																			       to_char(max(cad_datapedido), 'DD/MM/YYYY') as data, 
																			       'C' as situacao 
																			  FROM cadastrodoexame AS cad 
																			  JOIN itensdoexame AS itx 
																			    ON itx.cad_exame = cad.cad_exame 
																			 WHERE usu_codigo = $usu_codigo 
																			   AND proc_codigo = $line[proc_codigo] 
																			   AND cad_datapedido >= CURRENT_DATE - (SELECT proc_sispn_validade
																								  FROM procedimentos_sisprenatal
																								 WHERE proc_codigo = itx.proc_codigo)::varchar::int
																			 group by proc_codigo, situacao 
																		UNION ALL 
																		        SELECT proc_codigo, 
																		               to_char(max(dt_requisicao), 'DD/MM/YYYY') as data, 
																			       'S' as situacao 
																		          FROM requisicao_exames as req 
																		          JOIN atendimento as ate 
																		            ON req.ate_codigo = ate.ate_codigo 
																		         WHERE ate.usu_codigo = $usu_codigo 
																		           AND proc_codigo = $line[proc_codigo]  
																		           AND dt_requisicao >= CURRENT_DATE - (SELECT proc_sispn_validade
																								  FROM procedimentos_sisprenatal
																								 WHERE proc_codigo = req.proc_codigo)::varchar::int
																		         GROUP BY proc_codigo, situacao) AS t 
																		 ORDER BY data desc limit 1";
											$queryVerificaColetaExames = pg_query($sqlVerificaColetaExames);
											$regVerificaColetaExames = pg_fetch_array($queryVerificaColetaExames);
											$ate_data_coleta = $regVerificaColetaExames[data];
											//$exames .= $sqlVerificaColetaExames."<br/>";
											
											
											if($ate_data_coleta != ""){
												if($regVerificaColetaExames[situacao] == "C"){
													$dataMostrar = "<font color='blue'>".$ate_data_coleta."&nbsp;Coletado</font>";
												}
												if($regVerificaColetaExames[situacao] == "S"){
													$dataMostrar = "<font color='green'>".$ate_data_coleta."&nbsp;Solicitado</font>";
												}
											}
											$exames .= "<td><input type='checkbox' name='exames[]' value='$line[proc_codigo]' ".($ate_data_coleta == "" ? "checked=checked" : "")."><b>&nbsp;$line[proc_sis_nome_generico]</b> &nbsp;$dataMostrar&nbsp;</td>";
											$dataMostrar = "";
											if($cont%2 == 0){
												$exames .= "</tr>
													  <tr>";
											}
											$cont++;
										}			
									$exames.="
									</tr>
									<tr>
										<td id='trocaBotao'>";
											
											$exames .= $common->commonButton("Marcar/Desmarcar Tudo",null,"selecionar.png","onClick=\"selecionar_tudo()\"");
											$exames .= $common->commonButton("Deletar exames solicitados",null,"excluir.png","onClick=\"deletaSolicitacaoExames()\"");
									$exames .= "
											
											
										</td>
									</tr>
							</table>
							</fieldset>";
			    if($ate_codigo == ""){
					$observacao = "";			    	
			    }else{
			    	$observacao = $regTudo[ate_observacao_prenatal];
			    }
				echo $table->openTable(null,null,null,0);
					echo $table->criaLinha(array($topo));
					echo $table->criaLinha(array($exames,$vacina),array(500));
					echo $table->criaLinha(array($form->textArea("observacao","$observacao","Observa&ccedil;&atilde;o")),array(500));
					echo $table->criaLinha(array($common->commonButton("Salvar",null,"salvar.gif","onClick=\"validadorPrenatal()\"")));
				echo $table->closeTable();
			echo $form->closeForm();
		echo $common->closeTab();
		echo $common->bodyTab("2");
			
			$sqlHistoricoPrenatal = "SELECT *
									   FROM sis_pre_natal as sispn
									   JOIN usuario as usu
									     ON usu.usu_codigo = sispn.usu_codigo
									  WHERE sispn.usu_codigo = $usu_codigo";
			$queryHistoricoPrenatal = pg_query($sqlHistoricoPrenatal);

			echo $table->openTable("lista");
				echo $table->criaLinha(array("Numero da gesta&ccedil;&atilde;o","Nome da Gestante","Situa&ccedil;&atilde;o"),null,array(1,1,2),"S");
				while($linhaHistoricoPrenatal = pg_fetch_array($queryHistoricoPrenatal)){
					$verificaStatusAtual = "SELECT *
									  FROM atendimento
									 WHERE sispn_codigo = $linhaHistoricoPrenatal[sispn_codigo]
									 ORDER BY ate_data DESC
									 LIMIT 1";
					$queryVerificaStatusAtual = pg_query($verificaStatusAtual);
					$linhaVerificaStatusAtual = pg_fetch_array($queryVerificaStatusAtual);
					if($linhaVerificaStatusAtual[ate_tipo_consulta_prenatal] == 1){
						$statusConsulta = "Pr&eacute-Natal";
					}else if($linhaVerificaStatusAtual[ate_tipo_consulta_prenatal] == 3){
						$statusConsulta = "Parto";
					}else if($linhaVerificaStatusAtual[ate_tipo_consulta_prenatal] == 5){
						$statusConsulta = "Puerperio";
					}else if($linhaVerificaStatusAtual[ate_tipo_consulta_prenatal] == 9){
						$statusConsulta = "Interrup&ccedil;&atilde;o";
					}
					
					echo $table->criaLinha(array($linhaHistoricoPrenatal[sispn_numero_gestacao],$linhaHistoricoPrenatal[usu_nome],$statusConsulta,$common->commonButton("visualizar","prontuario.php?acao=hist&pagina=17&usu_codigo=$usu_codigo&sispn_codigo=$linhaHistoricoPrenatal[sispn_codigo]&age_codigo=$age_codigo&ate_codigo=$ate_codigo&med_codigo=$med_codigo&uni_codigo=$uni_codigo&age_data=$age_data","visualizar.png")),null,null,"N");
				}
			echo $table->closeTable();
			
			if($acao == "hist"){
				
				$sqlGest = "SELECT *
							  FROM usuario_gestante
							 WHERE usu_codigo = $usu_codigo"; 
				$queryGest = pg_query($sqlGest);
				$linhaGest = pg_fetch_array($queryGest);
				
				echo $common->openModal("Historico da Gestante",700,"fechar",null,null,null,400);
					if($acaoModal == ""){
						echo $table->openTable("lista");
							echo $table->criaLinha(array("<b>Quantidade de parto normal:</b>","$linhaGest[gest_quantidade_parto_vaginal]"),array(200));
							echo $table->criaLinha(array("<b>Quantidade cesaria:</b>","$linhaGest[gest_quantidade_cesaria]"),array(200));
							echo $table->criaLinha(array("<b>Quantidade abortos(s):</b>","$linhaGest[gest_quantidade_abortos]"),array(200));
							echo $table->criaLinha(array("<b>Quantidade filhos vivos</b>","$linhaGest[gest_quantidade_rn_2500]"));
							echo $table->criaLinha(array("<b>Quantidade filhos vivos</b>","$linhaGest[gest_quantidade_filhos_vivos]"));
							echo $table->criaLinha(array("<b>Nascidos com menos 2500g</b>","$linhaGest[gest_quantidade_rn_2500]"));
							echo $table->criaLinha(array("<b>Nascidos com mais 4000g</b>","$linhaGest[gest_quantidade_rn_4000]"));
							echo $table->criaLinha(array("<b>Tabagismo</b>","$linhaGest[gest_tabagismo]"));
							echo $table->criaLinha(array("<b>Quantidade de cigarros dia</b>","$linhaGest[gest_numero_cigarros_dia]"));
							echo $table->criaLinha(array("Atendimentos"),null,array(2),"S");
						echo $table->closeTable();
						echo $table->openTable("lista");
							$sqlAtendimentos = "SELECT *,
													   to_char(ate_data,'DD/MM/YYYY') AS data_ate
												  FROM atendimento as ate
												  JOIN usuarios as usr
												    ON usr.usr_codigo = ate.med_codigo
												 WHERE sispn_codigo = $sispn_codigo";
							$queryAtendimentos = pg_query($sqlAtendimentos);
							echo $table->criaLinha(array("<b>Data atendimento</b>","<b>Nome m&eacute;dico</b>"),array(150));
							while($linhaAtendimentos = pg_fetch_array($queryAtendimentos)){
								echo $table->criaLinha(array("$linhaAtendimentos[data_ate]",
															 "$linhaAtendimentos[usr_nome]"),
													   array(100),
													   null,
													   "N",
													   "onClick=\"chamarHistorico($usu_codigo,$sispn_codigo,$age_codigo,$linhaAtendimentos[ate_codigo],$med_codigo,$uni_codigo,'$age_data')\""
													   );
							}
						echo $table->closeTable();
					}
					if($acaoModal == "atendimento"){
						$sqlAtendimentoIndividual = "SELECT * 
													   FROM atendimento as ate
													   JOIN unidade as uni
													     ON uni.uni_codigo = ate.uni_codigo
													   JOIN sis_pre_natal as sispn
													     ON sispn.sispn_codigo = ate.sispn_codigo
													  WHERE ate_codigo = $ate_codigo";
						//echo $sqlAtendimentoIndividual;
						$queryAtendimentoIndividual = pg_query($sqlAtendimentoIndividual);
						$linhaAtendimentoIndiviaual = pg_fetch_array($queryAtendimentoIndividual);
						
						echo $table->openTable("lista");
							echo $table->criaLinha(array("<b>Unidade de atendimento:</b>","$linhaAtendimentoIndiviaual[uni_desc]"),array(200));
							/*TIPO DE CONSULTaaa*/
							if($linhaAtendimentoIndiviaual[ate_tipo_consulta_prenatal] == 1){
								$statusConsulta = "Pr&eacute-Natal";
							}else if($linhaAtendimentoIndiviaual[ate_tipo_consulta_prenatal] == 3){
								$statusConsulta = "Parto";
								$mostraHistorico = $table->criaLinha(array("<b>Tipo de parto:</b>","$linhaAtendimentoIndiviaual[sispn_tipo_parto]"),array(200));
							}else if($linhaAtendimentoIndiviaual[ate_tipo_consulta_prenatal] == 5){
								$statusConsulta = "Puerperio";
							}else if($linhaAtendimentoIndiviaual[ate_tipo_consulta_prenatal] == 9){
								$statusConsulta = "Interrup&ccedil;&atilde;o";
								$mostraHistorico = $table->criaLinha(array("<b>Tipo de parto:</b>","$linhaAtendimentoIndiviaual[sispn_tipo_interrupcao]"),array(200));
							}
							/*VERIFICA TIPO DE CONSULTA*/
							echo $table->criaLinha(array("<b>Status do atendimento:</b>","$statusConsulta"),array(200));
							echo $mostraHistorico;
							echo $table->criaLinha(array("<b>Peso do atendimento:</b>","$linhaAtendimentoIndiviaual[ate_peso]"),array(200));
							echo $table->criaLinha(array("<b>Diagn&oacute;stico:</b>","$linhaAtendimentoIndiviaual[ate_observacao_prenatal]"),array(200));
							
							$verificaExamesSolicitados = "SELECT *
															FROM requisicao_exames as req
															JOIN procedimentos_sisprenatal as proc_sis
															  ON req.proc_codigo = proc_sis.proc_codigo
														   WHERE ate_codigo = $ate_codigo";
							$queryExamesSolicitados = pg_query($verificaExamesSolicitados);
							while($linhaExamesSolicitados = pg_fetch_array($queryExamesSolicitados)){
								$examesSolicitados .= $linhaExamesSolicitados[proc_sis_nome_generico]."<br/>";
							}
							echo $table->criaLinha(array("<b>Exames Solicitados:</b>",
														 $examesSolicitados
														 ),array(200));
							
														 
							$sqlUltimo = "SELECT *,
												 to_char(ate_data,'DD/MM/YYYY') AS data_ate
											FROM atendimento as ate
											JOIN usuarios as usr
											  ON usr.usr_codigo = ate.med_codigo
										   WHERE sispn_codigo = $sispn_codigo
										   ORDER BY ate_codigo DESC";
							$queryUltimo = pg_query($sqlUltimo);
							$linhaUltimo = pg_fetch_array($queryUltimo);
							
							
							$sqlPuerperal = "SELECT * 
											   FROM avaliacao_puerperal
											  WHERE ate_codigo = $linhaUltimo[ate_codigo]";
							$queryPuerperal = pg_query($sqlPuerperal);
							$linhaPuerperal = pg_fetch_array($queryPuerperal);
							$numLinhasPuerperal = pg_num_rows($queryPuerperal);
							
							$sqlPreConsulta = "SELECT * 
												 FROM pre_consulta
												WHERE age_codigo = $age_codigo";
							$queryPreConsulta = pg_query($sqlPreConsulta);
							$linhaPreConsulta = pg_fetch_array($queryPreConsulta);
							
							/*$sqlVerificaPrenatalPuerperio = "SELECT *
															   FROM ";*/
							if($numLinhasPuerperal != 0){
								echo $table->criaLinha(array("Dados do puerp&eacute;rio"),null,array(3),"S");
								echo $table->criaLinha(array("<b>Data:</b>","$linhaAtendimentoIndiviaual[ate_data]"),array(200));
								echo $table->criaLinha(array("<b>Peso:</b>","$linhaAtendimentoIndiviaual[ate_peso]"),array(200));
								echo $table->criaLinha(array("<b>Temperatura:</b>","$linhaAtendimentoIndiviaual[ate_peso]"),array(200));
								echo $table->criaLinha(array("<b>Pa:</b>","$linhaPreConsulta[pc_pressao_sistolica] / $linhaPreConsulta[pc_pressao_diastolica]"),array(200));
								echo $table->criaLinha(array("<b>Caracter&iacute;sticas dos L&oacute;quios:</b>","$linhaPuerperal[ava_caracteristicas_loquios]"),array(200));
								echo $table->criaLinha(array("<b>Hemorragia:</b>","$linhaPuerperal[ava_hemorragia]"),array(200));
								echo $table->criaLinha(array("<b>Infec&ccedil;&atilde;o:</b>","$linhaPuerperal[ava_infeccao]"),array(200));
								echo $table->criaLinha(array("<b>Amamenta&ccedil;&atildeo:</b>","$linhaPuerperal[ava_amamentacao]"),array(200));
								if($linhaAtendimentoIndiviaual[ava_amamentacao] == "N"){
									echo $table->criaLinha(array("<b>Motivo:</b>","$linhaPuerperal[ava_motivo_nao_amamentacao]"),array(200));
								}
								echo $table->criaLinha(array("<b>Caracter&iacute;sticas das mamas:</b>","$linhaPuerperal[ava_catacteristicas_das_mamas]"),array(200));
								echo $table->criaLinha(array("<b>Amamenta&ccedil;&atildeo:</b>","$linhaPuerperal[ava_amamentacao]"),array(200));
								echo $table->criaLinha(array("<b>Data provavel parto:</b>","$linhaAtendimentoIndiviaual[sispn_data_provavel_parto]"),array(200));
								if($linhaAtendimentoIndiviaual[sispn_tipo_parto] == 00){
									$statusParto = "N&atilde;o Informado";
								}else if($linhaAtendimentoIndiviaual[sispn_tipo_parto] == 20){
									$statusParto = "Domiciliar";
								}else if($linhaAtendimentoIndiviaual[sispn_tipo_parto] == 30){
									$statusParto = "Hospitalar";
								}else if($linhaAtendimentoIndiviaual[sispn_tipo_parto] == 99){
									$statusParto = "Ignorado";
								}
								echo $table->criaLinha(array("<b>Tipo de parto:</b>","$statusParto"),array(200));
								echo $table->criaLinha(array("<b>Sexo do rec&eacute;m nascido:</b>","$linhaPuerperal[ava_sexo]"),array(200));
								echo $table->criaLinha(array("<b>Peso do rec&eacute;m nascido:</b>","$linhaPuerperal[ava_peso]"),array(200));
								echo $table->criaLinha(array("<b>Tamanho do rec&eacute;m nascido:</b>","$linhaPuerperal[ava_tamanho]"),array(200));
								echo $table->criaLinha(array("<b>APGAR:</b>","$linhaPuerperal[ava_apgar]"),array(200));
								echo $table->criaLinha(array("<b>PARKIM:</b>","$linhaPuerperal[ava_parkim]"),array(200));
							}
							
							echo $common->commonButton("voltar","prontuario.php?acao=hist&pagina=17&usu_codigo=$usu_codigo&sispn_codigo=$linhaAtendimentoIndiviaual[sispn_codigo]&age_codigo=$age_codigo&ate_codigo=$ate_codigo&med_codigo=$med_codigo&uni_codigo=$uni_codigo&age_data=$age_data","voltar.png");
							
						echo $table->closeTable();
					}
				echo $common->closeModal();
			}
		echo $common->closeTab();
?>
