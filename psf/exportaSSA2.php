<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."psf/funcoesBPA.inc.php";
	
	$tipoBPA = $_GET['tipoBPA'];
	$ano 	 = $_GET['ano'];
	$mes 	 = str_pad($_GET['mes'], 2, "0", STR_PAD_LEFT);
	$cnes	 = $_GET['cnes'];

	$nome = ($tipoBPA == "I" ? "PABpaIND" : "PABpaCON");
	switch ($mes){
		case "01": 
			$nomeMes = "JAN";
			break;
		case "02":
			$nomeMes = "FEV";
			break;
		case "03": 
			$nomeMes = "MAR";
			break;
		case "04":
			$nomeMes = "ABR";
			break;
		case "05": 
			$nomeMes = "MAI";
			break;
		case "06":
			$nomeMes = "JUN";
			break;
		case "07": 
			$nomeMes = "JUL";
			break;
		case "08":
			$nomeMes = "AGO";
			break;
		case "09": 
			$nomeMes = "SET";
			break;
		case "10":
			$nomeMes = "OUT";
			break;
		case "11": 
			$nomeMes = "NOV";
			break;
		case "12":
			$nomeMes = "DEZ";
			break;
	}
	$path = "arquivos/";
	$quebra = chr(13).chr(10);//quebra de linha
	
	/*
	 * MONTANDO O CABECALHO
	 */
	$valor 				= str_pad(contaLinhas(), 6, "0", STR_PAD_LEFT); //CONTAR AS LINHAS GERADAS COM O BPA
	$numlinhas 			= str_pad($valor, 6, "0", STR_PAD_LEFT);  // retorno "0000000007"
	$valor2 			= str_pad(contaFolhas(), 6, "0", STR_PAD_LEFT); //CONTAR AS LINHAS GERADAS COM O BPA
	$numfolhas 			= str_pad($valor2, 6, "0", STR_PAD_LEFT);  // retorno "0000000007"
	$controle 			= calculaControle();
	$orgaoOrigem 		= str_pad("Hospital da Providencia mi", 30, " ", STR_PAD_RIGHT);
	$siglaOrgaoOrigem 	= str_pad("HPA", 6, " ", STR_PAD_RIGHT);;
	$cgc 				= str_pad($input, 14, "0", STR_PAD_LEFT);
	$orgaoDestino 		= str_pad("Autarquia Municipal de Saude", 40, " ", STR_PAD_RIGHT);
	$municipalEstadual 	= "M"; //tamanho 1
	$versaoSistema 		= str_pad("D01.27", 10, " ", STR_PAD_RIGHT);

	$msg = "#BPA#".$ano.$mes.$numlinhas.$numfolhas.$controle.$orgaoOrigem.$siglaOrgaoOrigem.$cgc.$nomeOrgaoSaude.$orgaoDestino.$municipalEstadual.$versaoSistema.$quebra;
	/*
	 * CABECALHO MONTADO
	 */
	/*
	 * SELECT DO BPA CONSOLIDADO
	 */
	if ($tipoBPA == 'C'){
		$query = " SELECT count(*) as qtdeproc, 
						  p.proc_nome, 
						  p.proc_classificacao_sus,
						  m.med_cnes, 
						  tbo.no_ocupacao as cbo
					 FROM procedimento p
					 JOIN agendamento_exame_lista ael
					   ON p.proc_codigo = ael.proc_codigo
					 JOIN medico m
					   ON m.med_codigo = ael.med_codigo
					 LEFT JOIN rl_procedimento_ocupacao rpo
					   ON p.proc_classificacao_sus = rpo.co_procedimento
					 LEFT JOIN tb_ocupacao tbo
					   ON tbo.co_ocupacao = rpo.co_ocupacao
					WHERE to_char(ael.agexl_data, 'MM/YYYY') = '$mes/$ano'
					  AND m.med_cnes = $cnes
					GROUP BY p.proc_codigo, 
						  p.proc_nome, 
						  p.proc_classificacao_sus, 
						  m.med_cnes, 
						  tbo.no_ocupacao
					ORDER BY p.proc_nome";
	}else{
		$query = " SELECT count(*) as qtdeproc, 
						  p.proc_nome, 
						  p.proc_classificacao_sus,
						  m.med_cnes, 
						  ael.agexl_data as data_atendimento,
						  tbo.no_ocupacao as cbo
					 FROM procedimento p
					 JOIN agendamento_exame_lista ael
					   ON p.proc_codigo = ael.proc_codigo
					 JOIN medico m
					   ON m.med_codigo = ael.med_codigo
					 LEFT JOIN rl_procedimento_ocupacao rpo
					   ON p.proc_classificacao_sus = rpo.co_procedimento
					 LEFT JOIN tb_ocupacao tbo
					   ON tbo.co_ocupacao = rpo.co_ocupacao
					WHERE to_char(ael.agexl_data, 'MM/YYYY') = '$mes/$ano'
					  AND m.med_cnes = $cnes
					GROUP BY p.proc_codigo, 
						  p.proc_nome, 
						  p.proc_classificacao_sus, 
						  m.med_cnes, 
						  ael.agexl_data,
						  tbo.no_ocupacao
					ORDER BY ael.agexl_data, 
						  p.proc_nome";
	}
	/*
	 * FIM DO SELECT DO BPA CONSOLIDADO
	 */
	$result = pg_query($query);

	$folha = 1;
	$linhaBPA = 1;
	while($linha = pg_fetch_array($result)){
		$codCnes 						= str_pad($linha['med_cnes'], 7, " ", STR_PAD_RIGHT); // tamanho 7
		$competencia 					= $ano.$mes; // tamanho 6
		$cnsMedico 						= ($tipoBPA == "I" ? "CNSMEDICO" : str_pad("", 15, " ", STR_PAD_RIGHT)); // tamanho 15
		$cboMedico 						= str_pad($linha['cbo'], 6, " ", STR_PAD_RIGHT); // tamanho 6
		$dataAtendimento 				= str_replace('-', '', $linha['dataAtendimento']);
		$dataAtendimento 				= ($tipoBPA == "I" ? $data_atendimento : str_pad($data_atendimento, 8, " ", STR_PAD_RIGHT)); // tamanho 8 AAAAMMDD
		$numFolhaBpa 					= str_pad($folha, 3, "0", STR_PAD_LEFT);
		$numLinhaBpa 					= str_pad($linhaBPA, 2, "0", STR_PAD_LEFT);
		$proc_classificacao_sus 		= str_replace('.', '', $linha['proc_classificacao_sus']);
		$proc_classificacao_sus 		= str_replace('-', '', $proc_classificacao_sus);
		$codProcAmbulatorial			= str_pad($proc_classificacao_sus, 10, " ", STR_PAD_RIGHT); // tamanho 10 (ultimo caractere é o dígito verificador
		$cnsPaciente 					= ($tipoBPA == "I" ? "CNSPACIENTE" : str_pad("", 15, " ", STR_PAD_RIGHT)); // tamanho 15
		$pacSexo 						= ($tipoBPA == "I" ? "SEXO PACIENTE" : str_pad("", 1, " ", STR_PAD_RIGHT)); // tamanho 1
		$codIbge 						= ($tipoBPA == "I" ? "COD IBGE MUNICIPIO PACIENTE" : str_pad("", 6, " ", STR_PAD_RIGHT)); // tamanho 6
		$cid10 							= ($tipoBPA == "I" ? "CID 10" : str_pad("", 4, " ", STR_PAD_RIGHT)); // tamanho 4
		$idade 							= ($tipoBPA == "I" ? str_pad(calculaIdade($dataNascUsuario, $dataAtendimento), 3, "0", STR_PAD_LEFT) : str_pad("", 3, "0", STR_PAD_LEFT)); // tamanho 3
		$qtdeProc 						= str_pad($linha['qtdeproc'], 6, "0", STR_PAD_LEFT);
		$caracterAtendimento 			= ($tipoBPA == "I" ? "X" : str_pad("", 2, " ", STR_PAD_RIGHT)); // tamanho 1
		$numAutorizacaoEstabelecimento 	= ($tipoBPA == "I" ? "NUMERO AUTORIZACAO ESTABELECIMENTO" : str_pad("", 13, " ", STR_PAD_RIGHT)); // tamanho 1
		$origem 						= "BPA"; // tamanho 3 - Origem das informações (BPA - SIA/SUS; PNI -PROG. NAC. DE IMUNIZAÇÕES; SIE –SIGAE; SIB –SIGAB; MIN - MATERNO INFANTIL; PAC-PROGRAMA AÇÃO COMUNITÁRIA; SCL-SISCOLO; EXT-OUTROS SISTEMAS)
		$nomeUsuario 					= ($tipoBPA == "I" ? "NOME USUARIO" : str_pad("", 30, " ", STR_PAD_RIGHT)); // tamanho 30
		$dataNascUsuario 				= ($tipoBPA == "I" ? "YYYYMMDD" : str_pad("", 8, " ", STR_PAD_RIGHT)); // tamanho 30
		$tipoFormulario 				= $tipoBPA; // tamanho 1 - (I)ndividualizado ou (C)onsolidado
		$racaUsuario 					= ($tipoBPA == "I" ? "RACA USUARIO" : str_pad("", 2, " ", STR_PAD_RIGHT)); // tamanho 2 -- 01 Branca; 02 Preta; 03 Parda; 04 Amarela; 05 Indígena; 99 Sem informação
		$etniaUsuario 					= (($tipoBPA == "I") && ($racaUsuario == "05") ? "ETNIA USUARIO" : str_pad("", 4, " ", STR_PAD_RIGHT)); // tamanho 4 -- preencher somente se raça for indígena
				
		$msg .= $codCnes.$competencia.$cnsMedico.$cboMedico.$dataAtendimento.$numFolhaBpa.
				$numLinhaBpa.$codProcAmbulatorial.$cnsPaciente.$pacSexo.$codIbge.$cid10.$idade.
				$qtdeProc.$caracterAtendimento.$numAutorizacaoEstabelecimento.$origem.$nomeUsuario.
				$dataNascUsuario.$tipoFormulario.$racaUsuario.$etniaUsuario.$quebra;
		$linhaBPA++;
		if ($linhaBPA == 20){
			$linhaBPA = 1;
			$folha++;
		}
	}
	//echo $dataAtendimento."<br>";
	echo criaArquivo($nome, $msg, $path, ".".$nomeMes);
?>