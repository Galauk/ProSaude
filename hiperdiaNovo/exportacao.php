<?  
	session_start();
	//die('chegou');
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	$quebra = chr(13).chr(10);//essa eh a quebra de linha
		echo $common->incJquery();
	
	
	$ano = date("Y");
	$mes = date("m");
	$dia = date("d");
	$hora = date("H");
	$minutos = date("i");
	$segundos = date("s");
	$data = $dia."/".$mes."/".$ano;
	$nomeArquivo = "E".$codigoIbge.$ano.$mes.$dia.$hora.$minutos.$segundos;
	$versaoSistema = "0000000000";
	$header = "001".$codigoIbge.$dia.$mes.$ano." ".$hora.":".$minutos.":".$segundos."010".$versaoSistema."999".$quebra;
	
	$sqlRegistrosNaoExportados = "select * from hiperdia where hiper_status = 'H'";
	$queryRegistrosNaoExportados = pg_query($sqlRegistrosNaoExportados);
	
	while($registroNaoExportados = pg_fetch_array($queryRegistrosNaoExportados)){
	
		$hiper_codigo = $registroNaoExportados["hiper_codigo"];
		$sql = "select * 
				  from hiperdia as hip
				  join hiperdia_medicamentos as hipmed
					on hip.hiper_codigo = hipmed.hiper_codigo
				  join hiperdia_exames as hipex
					on hip.hiper_codigo = hipex.hiper_codigo
				 where hip.hiper_codigo = $hiper_codigo";
		$query = pg_query($sql);
		
		$sqlIbge = "select * from cidade where cid_nome = 'NOVA ESPERANCA'";
		$queryIbge = pg_query($sqlIbge);
		$recebeIbge = pg_fetch_array($queryIbge);
		$codigoIbge = $recebeIbge["cid_codigo_ibge"];
		
		$distritoSanitario = "01";//fazer validação ver se o municipio é dividido em distrino sanitário. Se nao estiver preencher com "00"
		$controle = "000"; // no arquivo esportado não tinha esses campos, tinha o CNES direto.
		
		// aqui vai pegar a unidade de saúde que o usuário do sistema estara logado.
	
		$pegaUniCod = "select * from logon where id_login = $id_login";
		$queryUniCod = pg_query($pegaUniCod);
		$line = pg_fetch_array($queryUniCod);
		
		$uni_cod = $line["uni_codigo"];
		$pegaCnesUnidade = "select * from unidade where uni_codigo = $uni_cod";
		$queryCnesUnidade = pg_query($pegaCnesUnidade);
		$linha = pg_fetch_array($queryCnesUnidade);
		$uni_cnes = $linha["uni_cnes"];
		
		$pegaDadosHiperdia = "select * from hiperdia where hiper_codigo = $hiper_codigo";
		$queryDadosHiperdia = pg_query($pegaDadosHiperdia);
		$registro = pg_fetch_array($queryDadosHiperdia);
		$usu_codigo = $registro["usu_codigo"];
		
		$pegaCartaoNacionalSaude = "select *,
										   to_char(usu_datanasc,'dd/mm/yyyy') as datanasc,
										   to_char(usu_rg_dt_emissao,'dd/mm/yyyy') as dataemissao,
										   to_char(usu_rg_dt_emissao,'dd/mm/yyyy') as dataemissao 
									  from usuario 
									 where usu_codigo = $usu_codigo";
		$queryCartaoNacionalSaude = pg_query($pegaCartaoNacionalSaude);
		$umRegistro = pg_fetch_array($queryCartaoNacionalSaude);
		$varCns = $umRegistro["usu_cartao_sus"];
		$cns = str_pad($varCns,15,"0",STR_PAD_LEFT);
		
		$sexo = $umRegistro["sexo"];
		$dataNascimento = $umRegistro["datanasc"];
		$nomeUsuario = $umRegistro["usu_nome"];
		$maeUsuario = $umRegistro["usu_mae"];
		$paiUsuario = $umRegistro["usu_pai"];
		$usu_raca = $umRegistro["rac_codigo"];
		$usu_st_conjugal = $umRegistro["usu_st_conjugal"];
		$usu_escolaridade = $umRegistro["usu_escolaridade"];
		$usu_cpf = $umRegistro["usu_cpf"];
		
		$usu_certidao = $umRegistro["usu_tipo_certidao"];
		$usu_cert_cartorio = $umRegistro["usu_cert_cartorio"];
		$usu_cert_livro = $umRegistro["usu_cert_livro"];
		$usu_cert_lv_fls = $umRegistro["usu_cert_lv_fls"];
		$usu_cert_emissao = $umRegistro["usu_cert_emissao"];
		$usu_cert_termo = $umRegistro["usu_cert_termo"];
		
		$usu_rg_emissor = $umRegistro["usu_rg_emissor"];
		$usu_rg = $umRegistro["usu_rg"];
		$usu_rg_compl = $umRegistro["usu_rg_compl"];
		$uf_codigo_rg = $umRegistro["uf_codigo_rg"];
		$usu_rg_dt_emissao = $umRegistro["dataemissao"];
		
		$usu_ctps = $umRegistro["usu_ctps"];
		$usu_ctps_serie = $umRegistro["usu_ctps_serie"];
		$uf_codigo_ctps = $umRegistro["uf_codigo_ctps"];
		$usu_ctps_dt_emissao = $umRegistro["usu_ctps_dt_emissao"];
		
		$usu_tit_eleitor = $umRegistro["usu_tit_eleitor"];
		$usu_tit_eleitor_zona = $umRegistro["usu_tit_eleitor_zona"];
		$usu_tit_eleitor_secao = $umRegistro["usu_tit_eleitor_secao"];
		
		$codigoNacionalidade = $umRegistro["cd_nacionalidade"];
		$nr_portaria_naturalizacao = $umRegistro["nr_portaria_naturalizacao"];
		$dt_naturalizacao = $umRegistro["dt_naturalizacao"];
		
		$muni_cd_cod_ibge_nasc = $umRegistro["muni_cd_cod_ibge_nasc"];
		$usu_dt_obito = $umRegistro["usu_dt_obito"];
		
		$unidade = $registroNaoExportados[uni_codigo];
			$cpf = str_pad(0,11,"0",STR_PAD_LEFT);	
	
			$numerosIrregulares = array("11111111111","22222222222","33333333333","","44444444444","55555555555","66666666666","77777777777","88888888888","99999999999");
				
			if(procpalavras($usu_cpf, $numerosIrregulares) == 1){
				exit;
			}
			$certidao = str_pad(0,2,"0",STR_PAD_LEFT);	
	
	
		
		$nomeCartorio = str_pad($usu_cert_cartorio,20,"0",STR_PAD_LEFT);
		$numeroFolhas = str_pad($usu_cert_lv_fls,4,"0",STR_PAD_LEFT);
		$numeroLivro = str_pad($usu_cert_livro,8,"0",STR_PAD_LEFT);
		$emissao = str_pad($usu_cert_emissao,10,"0",STR_PAD_LEFT);
		$termo = str_pad($usu_cert_termo,8,"0",STR_PAD_LEFT);
		
		$orgaoEmissor = str_pad($usu_rg_emissor,2,"0",STR_PAD_LEFT);
		$numeroIdentidade = str_pad($usu_rg,11,"0",STR_PAD_LEFT);
		$identidadeComplemento = str_pad($usu_rg_compl,4,"0",STR_PAD_LEFT);
		$ufIdentidade = str_pad($uf_codigo_rg,2,"0",STR_PAD_LEFT);
		$emissaoIdentidade = str_pad($usu_rg_dt_emissao,10,"0",STR_PAD_LEFT );
		
		$ctps = str_pad($usu_ctps,7,"0",STR_PAD_LEFT);
		$ctps_serie = str_pad($usu_ctps_serie,5,"0",STR_PAD_LEFT);
		$ufCtps = str_pad($uf_codigo_ctps,2,"0",STR_PAD_LEFT);
		$emissaoCtps = str_pad($usu_ctps_dt_emissao,10,"0",STR_PAD_LEFT);
		
		$tituloDeEleitor = str_pad($usu_tit_eleitor,13,"0",STR_PAD_LEFT);
		$tituloZona = str_pad($usu_tit_eleitor,4,"0",STR_PAD_LEFT);
		$tituloSecao = str_pad($usu_tit_eleitor_secao,4,"0",STR_PAD_LEFT);
		
		$nacionalidade = str_pad($codigoNacionalidade,3,"0",STR_PAD_LEFT);
		$numeroPortariaNaturalizacao = str_pad($nr_portaria_naturalizacao,16,"0",STR_PAD_LEFT);
		$dataNaturalizacao = str_pad($dt_naturalizacao,10,"0",STR_PAD_LEFT); 
		
		$muniNasc = str_pad($muni_cd_cod_ibge_nasc,7,"0",STR_PAD_LEFT);
	
		$dataObito = str_pad($usu_dt_obito,10,"0",STR_PAD_LEFT);
		 
		$raca = str_pad($nomeUsuario,1);
		$conjuge = str_pad($usu_st_conjugal,2,"0",STR_PAD_LEFT);
		$pis = str_pad(0,11,"0",STR_PAD_LEFT);
		$escolaridade = str_pad($usu_escolaridade,2,"0",STR_PAD_LEFT);
		$usu_nome = str_pad($nomeUsuario,70," ",STR_PAD_LEFT);
		$usu_mae = str_pad($maeUsuario,70," ",STR_PAD_LEFT);
		$usu_pai = str_pad($paiUsuario,70," ",STR_PAD_LEFT);
		
		$palavrasIrregulares = array("OMITIDA","NÃO INFORMADO","OMITIDO","","INEXISTENTE","A DECLARAR","NÃO PREENCHIDO","NÃO CONSTA","NÃO DECLARADO");
		if(procpalavras($usu_nome, $palavrasIrregulares) == 1){
			exit;
		}
		if(procpalavras($usu_mae, $palavrasIrregulares) == 1){
			exit;
		}
		if(procpalavras($usu_pai, $palavrasIrregulares) == 1){
			exit;
		}
		
		$sqlDadosEndereco = "SELECT * from integrantes_familia as integra
									 join psf as psf 
									   on psf.codigo_fam = integra.codigo_fam
									where integra.usu_codigo = $usu_codigo";
		$queryDadosEndereco = pg_query($sqlDadosEndereco);
		$linhaEndereco = pg_fetch_array($queryDadosEndereco);
		
		
		$codigoTipoDeLogradouro = str_pad($linhaEndereco["tipo_logradouro_fam"],3,"0",SRT_PAD_LEFT);//esse código vem do anexo I do arquivo de layout que está vinculado ao tipo de rua verificar de onde ele vira
		$nomeDoLogradouro = str_pad($linhaEndereco["endereco_fam"],50,"0",STR_PAD_LEFT); // Esse nome tera que ter uma validação para verificar se tem pelomenus uma letra em MAIUSCULO .
		$complementoDoLogradouro = str_pad(0,15,"0",STR_PAD_LEFT);//Mesmo caso do nome verificar de onde vai vim.
		$numeroDoLogradouro= str_pad($linhaEndereco["numero_fam"],7,"0",STR_PAD_LEFT);
		$nomeDoBairro = str_pad($linhaEndereco["bairro_fam"],30,"0",STR_PAD_LEFT);
		
		$cepDiretoBanco  = explode("-",$linhaEndereco["cep_fam"]);
		$cepTratado = $cepDiretoBanco[0].$cepDiretoBanco[1];
		
		$cep = str_pad($cepTratado,8,"0",STR_PAD_LEFT);
		$ddd = str_pad(0,3,"0",STR_PAD_LEFT);
		$telefone = str_pad(0,9,"0",STR_PAD_LEFT);
		
		$pegaDadosInclusao = "select to_char(hiper_data,'dd/mm/yyyy') as data,* from hiperdia where hiper_codigo = $hiper_codigo";
		$queryDadosInclusao = pg_query($pegaDadosInclusao);
		$execDadosInclusao = pg_fetch_array($queryDadosInclusao);
		
		$antecedentes = str_pad($execDadosInclusao["hiper_antecedentes_familiares"],1);
		$diabetesTipoUm = str_pad($execDadosInclusao["hiper_diabetes_1"],1);
		$diabetesTipoDois = str_pad($execDadosInclusao["hiper_diabetes_2"],1);
		$diabetesTabagismo = str_pad($execDadosInclusao["hiper_tabagismo"],1);
		$sedemtarismo = str_pad($execDadosInclusao["hiper_sedentarismo"],1);
		$sobrepeso = str_pad($execDadosInclusao["hiper_sobrepeso"],1);
		$hipertensao = str_pad($execDadosInclusao["hiper_hipertensao"],1);
		$infarto = str_pad($execDadosInclusao["hiper_infarto"],1);
		$outrasCoronariopatias = str_pad($execDadosInclusao["hiper_outras_coronariopatias"],1);
		$avc = str_pad($execDadosInclusao["hiper_avc"],1);
		$pe_diabetico = str_pad($execDadosInclusao["hiper_avc"],1);
		$amputacao = str_pad($execDadosInclusao["hiper_amputacao"],1);
		$doencaRenal = str_pad($execDadosInclusao["hiper_doenca_renal"],1);
		$dataConsulta = str_pad($execDadosInclusao["data"],1);
		$paSistolica = str_pad($execDadosInclusao["hiper_pa_sistolica"],3);
		$paDiastolica = str_pad($execDadosInclusao["hiper_pa_distolica"],3);
		$cintura = str_pad($execDadosInclusao["hiper_cintura"],3);
		$peso = str_pad($execDadosInclusao["hiper_peso"],7);
		$altura = str_pad($execDadosInclusao["hiper_altura"],3);
		$glicemia = str_pad($execDadosInclusao["hiper_glicemia_realizada"],3);
		
		$sqlDadosMedicamentos = "select * from hiperdia_medicamentos where hiper_codigo = $hiper_codigo";
		$queryDadosMedicamentos = pg_query($sqlDadosMedicamentos);/*
		$execDadosMedicamentos = pg_fetch_array($queryDadosMedicamentos);*/
		
		$medicamentosPrescritos = "";
		while($execDadosMedicamentos = pg_fetch_array($queryDadosMedicamentos)){
			$medicamentoso = str_pad($execDadosMedicamentos["hipermed_medicamentoso"],1);
			$alimentacao = str_pad($execDadosInclusao["hiper_glicemia_realizada"],1);
			$medicamentosPrescritos .= $execDadosMedicamentos["pro_codigo"].$execDadosMedicamentos["hipermed_dosagem"];
			$outrosMedicamentos = str_pad($execDadosMedicamentos["hipermed_outros"],1);
		}
			$medicamentosCompleto = str_pad($medicamentosPrescritos,30,"0",STR_PAD_LEFT);
		/********************************AQUI PEGA OS DADOS PARA INCLUSÃO DE MÉDICOS.******************** */
			$med_codigo = $registro["med_codigo"];
			$sqlPegaDadosMedico = "select * from hiperdia as hip
									 join medico as med
									   on hip.med_codigo = med.med_codigo
									where med.med_codigo = $med_codigo";
			$queryMedico = pg_query($sqlPegaDadosMedico);
			$regMedico = pg_fetch_array($queryMedico);
			$varMed_cnes = $regMedico["med_cnes"];
			$med_cnes = str_pad($varMed_cnes,15,"0",STR_PAD_LEFT);
			
			$varMedMatricula = $regMedico["med_matricula"];
			$matricula = str_pad($varMedMatricula,20,"0",STR_PAD_LEFT);
			
			$medCodigo = str_pad($med_codigo,20,"0",STR_PAD_LEFT);
			
			//pS: O codico do CBOS está fixo mudar após fazer a importação do SIA.
			$cbosCodigo = "00000071";
			$varMedNome = $regMedico["med_nome"];
			$med_nome = str_pad($varMedNome,50," ",STR_PAD_LEFT);
			
			$pisNumeroMedico = str_pad("0",11,"0",STR_PAD_LEFT);
			$varMedCpf = $regMedico["med_cpf"];
			
			$med_cpf = str_pad($varMedCpf,11,"0",STR_PAD_LEFT);
			
			$orgaoClasse = str_pad("0",6,"0",STR_PAD_LEFT);
			$dataDesativacao = str_pad("0",10,"0",STR_PAD_LEFT);
			
			$inclusaoProfissional = "050"."/".$codigoIbge.$distritoSanitario."/-".$uni_cnes."/-".$uni_cnes."/-".$controle."/-".$med_cnes."/".$matricula."/".$medCodigo."/".$pisNumeroMedico."/".$cbosCodigo."/".$med_nome."/".$med_cpfs."/".$dataDesativacao;
			
		/********************************AQUI PEGA OS DADOS PARA INCLUSAO PESSOA.******************** */
		$codigoEnfermeiro = str_pad($med_codigo,20,"0",STR_PAD_LEFT);// esse código ficou para se perguntar se realmente ia ser o do sistema por esse motivo foi preenchido com 00000
		$codigoUsuarioSusSistema = str_pad($id_login, 50, "0", STR_PAD_LEFT);// esse código ficou para se perguntar se realmente ia ser o do sistema por esse motivo foi preenchido com 
		
		
		$inclusaoPessoa .= "060".$codigoIbge.$distritoSanitario.$controle.$unidade.$controle.$uni_cnes.$codigoEnfermeiro.$codigoUsuarioSusSistema.$cns.$usu_nome.$codigoTipoDeLogradouro.$nomeDoLogradouro.$complementoDoLogradouro.$numeroDoLogradouro.$nomeDoBairro.$cep.$ddd.$telefone.$antecedentes.$diabetesTipoUm.$diabetesTipoDois.$diabetesTabagismo.$sedemtarismo.$sobrepeso.$hipertensao.$infarto.$outrasCoronariopatias.$avc.$pe_diabetico.$amputacao.$doencaRenal.$dataConsulta.$paSistolica.$paDiastolica.$cintura.$peso.$altura.$glicemia.$medicamentoso.$alimentacao.$medicamentosCompleto.$sexo.$dataNascimento.$usu_mae.$usu_pai.$usu_raca.$conjuge.$escolaridade.$pis.$cpf.$certidao.$nomeCartorio.$numeroLivro.$numeroFolhas.$termo.$emissao.$orgaoEmissor.$numeroIdentidade.$identidadeComplemento.$ufIdentidade.$emissao.$ctps.$ctps_serie.$ufCtps.$emissaoCtps.$tituloDeEleitor.$tituloZona.$tituloSecao.$nacionalidade.$numeroPortariaNaturalizacao.$dataNaturalizacao.$muniNasc."0000000000".$dataObito.$quebra;
		$sqlAlteraStatus = "UPDATE hiperdia S
							   SET hiper_status = 'E'
							 WHERE hiper_codigo = $hiper_codigo";
		$queryAlteraStatus = pg_query($sqlAlteraStatus);
	}
	
//--//--//--//--//--//-//--//--//--//--//--//--//--//--//-//-/-/-/-/-/-/-/-/-/-/-/-/-/-
/*
********************************AQUI PEGA OS DADOS PARA EXPORTAÇÃO DE ACOMPANHAMENTO.*********************
*/

	$sqlNaoExportadosAcompanhamento = "select * from hiperdia_acompanhamentos where hiperac_status_exportacao = 'H'";
	$queryNaoExportadosAcompanhamento = pg_query($sqlNaoExportadosAcompanhamento);
	while($regNaoExportadosAcompanhamento = pg_fetch_array($queryNaoExportadosAcompanhamento)){
		$hiperac_codigo = $regNaoExportadosAcompanhamento["hiperac_codigo"];
		$unidade_acompanhamento = $regNaoExportadosAcompanhamento[uni_codigo];
		$statusAcompanhamento = $regNaoExportadosAcompanhamento["hiperac_status_exportacao"];
		$sqlAcompanhamento  = "select * from hiperdia_acompanhamentos where hiper_codigo = $hiper_codigo and hiperac_status_exportacao = 'H'";
		$querySqlAcompanhamento = pg_query($sqlAcompanhamento);
	
		$regAcompanhamento = pg_fetch_array($querySqlAcompanhamento);
		//$hiperac_codigo = $regAcompanhamento["hiperac_codigo"];
		
		$varAlimentacaoAcompanhamento = $regAcompanhamento["hiperac_tipo_exame_glicemia"];
		$alimentacao = str_pad($varAlimentacaoAcompanhamento,1,"0",STR_PAD_LEFT);
		
		$varPaSistolicaAcompanhamento = $regAcompanhamento["hiperac_pasistolica"];
		$paSistolicaAcompanhamento = str_pad($varPaSistolicaAcompanhamento,3,"0",STR_PAD_LEFT);
		
		$varPaDiastolicaAcompanhamento = $regAcompanhamento["hiperac_diastolica"];
		$paDiastolicaAcompanhamento = str_pad($varPaDiastolicaAcompanhamento,3,"0",STR_PAD_LEFT);
		
		$varCinturaAcompanhamento = $regAcompanhamento["hiperac_cintura"];
		$cinturaAcompanhamento = str_pad($varCinturaAcompanhamento,3,"0",STR_PAD_LEFT);
		
		$varPesoAcompanhamento = $regAcompanhamento["hiperac_peso"];
		$pesoAcompanhamento = str_pad($varPesoAcompanhamento,7,"0",STR_PAD_LEFT);
		
		$varAlturaAcompanhamento = $regAcompanhamento["hiperac_altura"];
		$alturaAcompanhamento = str_pad($varAlturaAcompanhamento,3,"0",STR_PAD_LEFT);
		
		$varGlicemiaAcompanhamento = $regAcompanhamento["hiperac_exame_glicemia"];
		$glicemiaAcompanhamento = str_pad($varGlicemiaAcompanhamento,3,"0",STR_PAD_LEFT);
		
		$varOutrosMedicamentosAcompanhamento = $regMedicamentoAcompanhamento["hipermedac_outros"];
		$outrosMedicamentosAcompanhamento = str_pad($varOutrosMedicamentosAcompanhamento,1,"0",STR_PAD_LEFT);
		
		
		
		$sqlMedicamentoAcompanhamento = "select * from hiperdia_medicamentos_acompanhamento where hiperac_codigo = $hiperac_codigo";
		$queryMedicamentoAcompanhamento = pg_query($sqlMedicamentoAcompanhamento);
		$medicamentosPrescritos = "";
		
		while($regMedicamentoAcompanhamento = pg_fetch_array($queryMedicamentoAcompanhamento)){
			$varMedicamentosoAcompanhamento = $regMedicamentoAcompanhamento["hipermedac_medicamentoso"];
			$medicamentosPrescritosAcompanhamento .= $regMedicamentoAcompanhamento["pro_codigo"].$regMedicamentoAcompanhamento["hipermedac_dosagem"];
			
			$varOutrosMedicamentosAcompanhamento = $regMedicamentoAcompanhamento["hipermedac_outros"];
			$outrosMedicamentosAcompanhamento = str_pad($varOutrosMedicamentosAcompanhamento,1,"0",STR_PAD_LEFT);
		}
		$tudoMedicamento = str_pad($medicamentosPrescritosAcompanhamento,30,"0",STR_PAD_LEFT);
		
		
		$varSemComplicacoes = $regAcompanhamento["hiperac_sem_complicacoes"];
		$complicacoesAcompanhamento = str_pad($varSemComplicacoes,1,"0",STR_PAD_LEFT);
		
		$varAngina = $regAcompanhamento["hiperac_angina"];
		$anginaAcompanhamento = str_pad($varAngina,1,"0",STR_PAD_LEFT);
		
		$varIam = $regAcompanhamento["hiperac_iam"];
		$iamAcompanhamento = str_pad($varIam,1,"0",STR_PAD_LEFT);
		
		$varAvc = $regAcompanhamento["hiperac_avc"];
		$avcAcompanhamento = str_pad($varAvc,1,"0",STR_PAD_LEFT);
		
		$varPeDiabetico = $regAcompanhamento["hiperac_pe_diabetico"];
		$pediabeticoAcompanhamento = str_pad($varPeDiabetico,1,"0",STR_PAD_LEFT);
		
		$varAmputacao = $regAcompanhamento["hiperac_amputacao_diabetes"];
		$amputacaoAcompanhamento = str_pad($varAmputacao,1,"0",STR_PAD_LEFT);
		
		$varDoencaRenal = $regAcompanhamento["hiperac_doenca_renal"];
		$doencaRenaAcompanhamentol = str_pad($varDoencaRenal,1,"0",STR_PAD_LEFT);
		
		$varRetinopatia = $regAcompanhamento["hiperac_retinopatia"];
		$retinopatiaAcompanhamento = str_pad($varRetinopatia,1,"0",STR_PAD_LEFT);
		
		$sqlExamesAcompanhamento = "select * from hiperdia_exames where hiperac_codigo = $hiperac_codigo";
		$queryExamesAcompanhamento = pg_query($sqlExamesAcompanhamento);
		$regExamesAcompanhamento = pg_fetch_array($queryExamesAcompanhamento);
		///////EXAMES//////
		$varHbGlicosada = $regExamesAcompanhamento["hiperac_hb_glicosada"];
		$hbGlicosada = str_pad($varHbGlicosada,1,"0",STR_PAD_LEFT);
		
		$varCreatinicaSerica = $regExamesAcompanhamento["hiperac_creatinina_serica"];
		$creatinicaSerica = str_pad($varCreatinicaSerica,1,"0",STR_PAD_LEFT);
		
		$varColesterolTotal = $regExamesAcompanhamento["hiperac_colesterol_total"];
		$colesterolTotal = str_pad($varColesterolTotal,1,"0",STR_PAD_LEFT);
		
		$varEcg = $regExamesAcompanhamento["hiperac_ecg"];
		$ecg = str_pad($varEcg,1,"0",STR_PAD_LEFT);
		
		$varTriglicerides = $regExamesAcompanhamento["hiperac_triglicerides"];
		$triglicerides = str_pad($varTriglicerides,1,"0",STR_PAD_LEFT);
		
		$varUrina = $regExamesAcompanhamento["hiperac_urina"];
		$urina = str_pad($varUrina,1,"0",STR_PAD_LEFT);
		
		$varMicroAlbuminuria = $regExamesAcompanhamento["hiperac_micro_albuminuria"];
		$microAlbuminuria = str_pad($varMicroAlbuminuria,1,"0",STR_PAD_LEFT);
		
		
		$varHipertenso = $regAcompanhamento["hiperac_hipertenso"];
		$hipertensoAcompanhamento = str_pad($varHipertenso,1,"0",STR_PAD_LEFT);
		
		$varDiabetico = $regAcompanhamento["hiperac_diabetico"];
		$diabeticoAcompanhamento = str_pad($varDiabetico,1,"0",STR_PAD_LEFT);
		
		
		$varRiscos = $regAcompanhamento["hiperac_riscos"];
		$riscos = str_pad($varRiscos,1,"0",STR_PAD_LEFT);
		
		$padraoAcompanhamento = "070";
		if($statusAcompanhamento == "A"){
			$padraoAcompanhamento = "071";
		}
		
		$inclusaoAcompanhamento .=$padraoAcompanhamento.$codigoIbge.$distritoSanitario.$unidade_acompanhamento.$unidade_acompanhamento.$codigoEnfermeiro.$codigoUsuarioSusSistema.$cns.$codigoEnfermeiro.$codigoUsuarioSusSistema.$cns.$dataConsulta.$alimentacao.$medicamentosoAcompanhamento.$paSistolicaAcompanhamento.$paDiastolicaAcompanhamento.$cinturaAcompanhamento.$pesoAcompanhamento.$alturaAcompanhamento.$glicemiaAcompanhamento.$outrosMedicamentosAcompanhamento.$tudoMedicamento.$complicacoesAcompanhamento.$anginaAcompanhamento.$iamAcompanhamento.$avcAcompanhamento.$pediabeticoAcompanhamento.$amputacaoAcompanhamento.$doencaRenal.$retinopatiaAcompanhamento.$hbGlicosada.$creatinicaSerica.$colesterolTotal.$ecg.$triglicerides.$urina.$microAlbuminuria.$hipertensoAcompanhamento.$diabeticoAcompanhamento.$riscos.$quebra;
		$sqlAlteraStatusAcompanhamento = "UPDATE hiperdia_acompanhamentos
											 SET hiperac_status_exportacao = 'E'
										   WHERE hiperac_codigo = $hiperac_codigo";
		$queryAlteraStatusAcompanhamento = pg_query($sqlAlteraStatusAcompanhamento);
	}
	
//----//--//--//--//--//--//--//--//-///-//-//-//-//-//--//--//--//--//--//-//--//--//--//--//--//--//--//--//-//-/-/-/-/-/-/-/-/-/-/-/-/-/-
	/*$arquivo = fopen ($nomeArquivo, "r");
	$num_linhas = 0; 
	while (!feof ($arquivo)) { 
		if ($linha = fgets($arquivo)){
		   $num_linhas++;
    	}
	}
	fclose ($arquivo); */

	
	$trailer = "999".$codigoIbge.$num_linhas;
//----//--//--//--//--//--//--//--//-///-//-//-//-//-//--//--//--//--//--//-//--//--//--//--//--//--//--//--//-//-/-/-/-/-/-/-/-/-/-/-/-/-/-
	$sqlArquivo = "INSERT INTO exportacoes(
												  exp_nome_modulo,
												  exp_nome_arquivo,
												  exp_data,
												  usr_codigo,
												  exp_caminho)
										   VALUES(
										   		  'HIPERDIA',
										   		  '$nomeArquivo',
												  '$data',
												  '$id_login',
												  'desenvolvimento/elotech/WebSocialSaude/hiperdiaNovo/arquivosExportacao')
												  ";
	$queryArquivo = pg_query($sqlArquivo);
	$criaArquivo = criaArquivo($nomeArquivo,$header.$inclusaoPessoa.$inclusaoAcompanhamento.$trailer,"./arquivosExportacao/",".apl");
	if($criaArquivo == 1){
		echo $common->modalMsg("OK","Arquivo de exporta&ccedil;&atilde;o gerado com sucesso!","layoutExportacao.php");
	}else{
		echo $common->modalMsg("ERRO","Erro ao gerar arquivo de exporta&ccedil;&atilde;o !","layoutExportacao.php");
	}
	
?>