<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

error_reporting( E_ALL | E_STRICT );

/**
LAYOUT PARA MUNICIPIOS
CARTÃO NACIONAL DE SAUDE

VERSÃO 2.2.0.0
Release 1
*/

class LayoutCritica
{
	/** Codigos */
	var $ibge 				= '';
	var $cid_codigo		= '';
	var $cid_nome		= '';
	
	
	/** Geracao do arquivo */
	var $diretorio 			= 'datasus/';
	var $separador 		= "\n";
	var $fileHandler 		= null;
	var $arquivo 			= '';
		
	/** REGISTRO TIPO  HEADER GERAL DO ARQUIVO (Tamanho do registro: 1083) */
	var $HeaderStr		= '';
	var $Header			= '';
	
	/** REGISTRO TIPO “2� – REGISTRO DETALHE DO DOMIC�LIO (Tamanho do registro: 1083) */
	var $DomicilioStr	= '';
	var $Domicilio 		= '';
	var $DomicilioCont	= 0;
	
	/** REGISTRO TIPO “3� – REGISTRO DETALHE DO USU�RIO (Tamanho do registro: 1083) */
	var $UsuarioStr		= '';
	var $Usuario 			= '';
	var $UsuarioCont 	= 0;
	
	/** REGISTRO TIPO “9� – TRAILLER DE ARQUIVO DE MUNIC�PIO (Tamanho do registro: 1083) */
	var $TraillerStr		= '';
	var $Trailler				= '';
	
	/** contador geral !  */
	var $contador = 0;
	
	/** Construtor */
	function LayoutCritica( $ibge )
	{
		$this->ibge 			= $ibge;
		$row						= db_getRow("SELECT cid_codigo, cid_nome FROM cidade WHERE cid_codigo_ibge = '{$ibge}'");
		$this->cid_codigo 	= $row[0];
		$this->cid_nome 	= $row[1];

	} // function LayoutCritica
	
	/**
		Código IGBE dos Lotes (7 posições) +
		Data (aammdd) +
		Identificador do arquivo (6 últimas posições de um seqüencial de quantidade de arquivo) +.DTS
	*/
	function criaArquivo() 
	{
		$pk_lc = db_get("SELECT NEXTVAL( 'layout_critica_lc_codigo_seq' )");
		$this->arquivo 	= $this->diretorio .
			sprintf( "%07d" . "%6s" . "%06d" . ".DTS", 
			$this->ibge,
			date('ymd'),
			$pk_lc
		
		);
		
		$result = '';
		
		// Cabecalho
		$this->criaHeader( $this->arquivo );
		$result .= $this->Header . $this->separador;
		
		// Verifica quais sao as familias (domicilios) desta cidade ARRUMAR
		$stmt_dom0	= "SELECT DISTINCT fam_codigo FROM familia WHERE cid_codigo = {$this->cid_codigo}";
		$qry_dom0		= db_query( $stmt_dom0 );
		
		// percorre as familias 
		while( $row_dom0 = pg_fetch_row($qry_dom0) )
		{
			$stmt_dom1 	= "SELECT 
					{$rowDom['dom_codigo']},
					{$rowDom['numero_da_ficha']},
					date( 'd/m/Y' ),
					{$rowDom['data_inc']},
					seg_codigo,
					fam_codigo,
					log_tipo,
					fam_endereco,
					fam_numero_res,
					fam_complemento,
					fam_bairro,
					fam_cep,
					{$rowDom['fone_ddd']},
					{$rowDom['fone_numero']},				# 20.Número do Telefone
					fam_nr_pessoas,
					fam_comodos,
					fam_energia,
					fam_esgoto,
					fam_tipo_domicilio,
					{$rowDom['destino_lixo']},					# 26.Indica o de destino do lixo do domicílio
					{$rowDom['abast_agua']},					# 27.Indica o tipo de abastecimento de água do domicílio
					fam_tratamento_agua,
					{$rowDom['dom_progr_cobert']},		# 29. Indica o programa de cobertura do domicílio.
					{$rowDom['ibge_codigo']},					# 30. Código IBGE do município onde está localizado o domicílio.
					{$rowDom['munic_nome']},				# 31. Nome do Município, segundo IBGE, onde está localizado o domicílio.
					fam_cadastrador,
					{$rowDom['tipo']},								# 34.Indica o tipo de operação referente ao domicílio : 1 – Inclusão 4 – Correção (Para corrigir problemas retornados pelo DATASUS) 8 – Cerreção de Alteração 9 –  Alteração
					{$rowDom['domic_codigo']},				# 36.Código que identifica o domicílio no município.
					FROM familia WHERE fam_codigo=".$row_dom0[0];	
			$row_dom1 	= db_getRow( $stmt_dom1 );
			
			$this->criaDomicilio( $row_dom1 );
			$result .= $this->Domicilio . $this->separador;
			
			// percorre os usuarios desta familia/domicilio
			$stmt_usu0 = "SELECT 
						fam_nr_ficha,
						usu_nome,
						rac_codigo
						{$rowUsu['sit_conjugal_codigo']},
						usu_escolaridade,
						usu_datanasc,
						usu_sexo,
						usu_pai,
						{$rowUsu['usu_pai_nome_fon']},
						usu_mae,
						{$rowUsu['usu_mae_nome_fon']},
						usu_cartao_sus,
						usu_pis_pasep,
						usu_cpf,
						usu_tipo_certidao,
						usu_cert_cartorio,
						usu_cert_livro,
						usu_cert_lv_fls,
						usu_cert_termo,
						usu_cert_emissao,
						usu_freq_escolar,
						usu_rg_emissor,
						usu_rg,
						usu_rg_compl,
						uf_sigla_rg,
						usu_rg_dt_emissao,
						usu_ctps,
						usu_ctps_serie,
						uf_sigla_ctps,
						usu_ctps_dt_emissao,
						usu_tit_eleitor,
						usu_tit_eleitor_zona,
						usu_tit_eleitor_secao,
						cd_nacionalidade,
						usu_dt_entrada_pais,
						nr_portaria_naturalizacao,
						dt_naturalizacao,
						dt_preenchimento_form,
						dt_inclusao,
						dt_alteracao,
						{$rowUsu['usu_lote_num']},
						id_domicilio,
						usu_end_cidade,
						id_domicilio,
						muni_cd_cod_ibge_nasc,
						usu_cidade_nasc,
						usu_cbo_r,
						{$rowUsu['usu_domic_codigo']},						# 53. Identificação do cadastrador da ficha de usuário.
						{$rowUsu['usu_domic_codigo']},		# 54. Identificador de máquina
						{$rowUsu['usu_domic_codigo']},		# 55. Código do Município
						{$rowUsu['usu_domic_codigo']},		# 56. Indica se o usuário participa do programa social Bolsa Alimentação
						{$rowUsu['usu_domic_codigo']},		# 57. Indica se o usuário participa do programa social PRODEA
						{$rowUsu['usu_domic_codigo']},		# 63. Indicador de operação: 1 – Inclusão 4 – Correção (Para corrigir problemas retornados pelo DATASUS) 5 – Confirmação de Homônimo 7 – Confirmação de Homônimo com novo documento 8 – Correção de Alteração 9 – Alteração
						{$rowUsu['usu_domic_codigo']},		# 64. Numero seqüencial da pessoa no domicilio
						{$rowUsu['usu_domic_codigo']},		# 65. Indica se o usuário está vinculado  ao Domicílio
						{$rowUsu['usu_domic_codigo']},		# 66. Indica se o usuário está vinculado ao SIAB
						{$rowUsu['usu_domic_codigo']},		# 67. Nome do Operador
						{$rowUsu['usu_domic_codigo']},		# 68. Versão
						{$rowUsu['usu_domic_codigo']},		# 69. Código de Usuário Municipal
						{$rowUsu['usu_domic_codigo']}			# 70. Código de Domicilio Municipal
						FROM usuario WHERE fam_codigo=".$row_dom0[ 0 ];
			$qry_usu0 = db_query($stmt_usu0);
			while($row_usu0 = pg_fetch_array($qry_usu0)){
				$this->criaUsuario( $row_usu0 );
				$result .= $this->Usuario . $this->separador;
			}
		}
		
		// finaliza
		$this->criaTrailler();
		$result .= $this->Trailler.$this->separador;
		
		//print "String:<br /><pre>\n{$result}</pre><br />";
		//var_dump($result);
		//exit;
		
		// gera o arquivo
		$this->fileHandler = fopen( $this->arquivo, 'w+' );
		if( ! $this->fileHandler ) die("N&atilde;o foi poss&iacute;vel abrir o arquivo <strong>{$this->arquivo}</strong> !");

		if( ! fwrite( $this->fileHandler, $result ) )
		{
			print "N&atilde;o foi poss&iacute;vel escrever no o arquivo <strong>{$this->arquivo}</strong> !";
		}
		
		fclose( $this->fileHandler );
		
	}

	/** Cria a Header */
	function criaHeader($arquivo){
		$this->contador ++;
		$this->HeaderStr = 
					"%07d".
					"%1d".
					"%07d".
					"%40s".
					"%5s".
					"%3s".
					"%20s".
					"%5s".
					"%8s".
					"%38s".
					"%10s".
					"%939s";
					
		$this->Header = sprintf(  $this->HeaderStr,
					$this->contador ++,					# sequencial
					0, 												# fixo
					$this->ibge,								# Deve ser um código de município válido, segundo tabela do IBGE.
					$this->cid_nome,						# Nome do município de residência de todos os usuários do arquivo. 
					'',													# fixo
					'',													# fixo
					date( 'd/m/Y H:i:s' ),					# fixo
					'02.00',										# fixo
					'',													# fixo
					$arquivo,										# OBRIGATORIO
					'LAYOUTMUN',								# fixo
					''													# fixo
		);
		
		return $this;
		
	} // function criaHeader()
	
	/** Cria o Domicilio */
	function criaDomicilio( $rowDom )
	{
		$this->contador++;
		$this->DomicilioStr = 
					"%07d".
					"%1d".
					"%48s".
					"%16s".
					"%4s".
					"%10s".
					"%20s".
					"%20s".
					"%02d".
					"%04d".
					"%02d".
					"%03d".
					"%03d".
					"%50s".
					"%7s".
					"%15s".
					"%30s".
					"%8s".
					"%03d".
					"%09d".
					"%02d".
					"%02d".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%07d".
					"%40s".
					"%06d".
					"%20s".
					"%1d".
					"%1d".
					"%48s".
					"%687s";
		
		
		$this->Domicilio = sprintf(  $this->DomicilioStr,
					++$this->DomicilioCont,				# 01. sequencial
					2, 													# fixo! 02
					'', 													# fixo 03
					$rowDom['dom_codigo'],				# 04. Código do Domicílio do usuário
					$numero_da_ficha,							# 05. Número da Ficha
					date( 'd/m/Y' ),								# fixo
					$rowDom['data_inc'],						# 07. Data de inclusão do domicilio.
					'',														# fixo :  Data de alteração do domicilio.
					$rowDom['cod_segmento'],			# 09. Código de segmento.
					$rowDom['area_cod'],						# 10. Código de área. 
					$rowDom['micro_area_cod'],			# 11. Código de micro área. 
					$rowDom['fam_codigo'],					# 12. Código família.
					$rowDom['logr_tipo_codigo'],			# 13. Código que identifica o tipo de logradouro.
					$rowDom['logr_nome']	,				# 14. Nome do logradouro.
					$rowDom['logr_numero'], 				# 15. Número do Logradouro
					$rowDom['logr_compl'], 					# 16. Complemento do Logradouro 
					$rowDom['bairro_nome'], 				# 17. Nome do Bairro
					$rowDom['logr_cep'],						# 18. CEP do Logradouro
					$rowDom['fone_ddd'],						# 19.Número do DDD
					$rowDom['fone_numero'],				# 20.Número do Telefone
					$rowDom['qtde_domic'],					# 21.Quantidade de pessoas vinculadas ao domicílio.
					$rowDom['qtde_comodos'],			# 22.Quantidade de cômodos do domicílio
					$rowDom['tem_energia'],				# 23.Indica se o domicílio possui  Energia Elétrica
					$rowDom['tem_esgoto'],				# 24.Indica se o domicílio possui esgoto sanitário
					$rowDom['domic_tipo'],					# 25.Indica o tipo de Domicílio
					$rowDom['destino_lixo'],					# 26.Indica o de destino do lixo do domicílio
					$rowDom['abast_agua'],					# 27.Indica o tipo de abastecimento de água do domicílio
					$rowDom['trat_agua'],					# 28.Indica o tipo de tratamento de água do domicílio
					$rowDom['dom_progr_cobert'],		# 29. Indica o programa de cobertura do domicílio.
					$rowDom['ibge_codigo'],					# 30. Código IBGE do município onde está localizado o domicílio.
					$rowDom['munic_nome'],				# 31. Nome do Município, segundo IBGE, onde está localizado o domicílio.
					$rowDom['num_cadastrador'],		# 32.Número do Cadastrador.
					'',														# 33.Campo reservado para o Datasus.
					$rowDom['tipo'],								# 34.Indica o tipo de operação referente ao domicílio : 1 – Inclusão 4 – Correção (Para corrigir problemas retornados pelo DATASUS) 8 – Cerreção de Alteração 9 –  Alteração
					1,														# fixo! 35.Indicador de Arquivo
					$rowDom['domic_codigo'],				# 36.Código que identifica o domicílio no município.
					'' 														# fixo! 37. Campo reservado para o Datasus.
		);			
	} // function criaDomicilio()
	
	/** Cria o Usuario */
	function criaUsuario( $rowUsu )
	{
		$this->UsuarioStr = 
					"%07d".
					"%01d".
					"%58s".
					"%20s".
					"%04d".
					"%70s".
					"%70s".
					"%1d".
					"%02d".
					"%02d".
					"%10s".
					"%1s".
					"%70s".
					"%70s".
					"%70s".
					"%70s".
					"%015d".
					"%011d".
					"%011d".
					"%02d".
					"%20s".
					"%8s".
					"%4s".
					"%8s".
					"%10s".
					"%1s".
					"%02d".
					"%011d".
					"%4s".
					"%2s".
					"%10s".
					"%07d".
					"%05d".
					"%2s".
					"%10s".
					"%013d".
					"%04d".
					"%04d".
					"%03d".
					"%10s".
					"%16d".
					"%10s".
					"%10s".
					"%20s".
					"%20s".
					"%5s".
					"%48s".
					"%7s".
					"%16s".
					"%7s".
					
					"%40s".
					"%3s".
					"%6s".
					"%15s".					
					"%07d".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%1s".
					"%20s".
					"%1d".
					"%02d".
					"%1d".
					"%1d".
					"%8s".
					"%5s".
					"%58s".
					"%48s";

		$this->Usuario = sprintf(  $this->UsuarioStr,
					++$this->UsuarioCont,					# 01. Número seqüencial do registro dentro do arquivo
					3, 													# fixo! 02. Indicador de registro tipo USU�RIO   
					'',														# fixo! 03.Código do Usuário
					'',														# fixo! 04.Numero de Uso Municipal
					$rowUsu['ficha_numero'],				# 05.Numero da Ficha
					$rowUsu['usu_nome'],					# 06.Nome do Usuário
					'', 													# fixo! 07.Nome fonético do usuário
					$rowUsu['raca_codigo'], 					# 08.Código de raça/cor 
					$rowUsu['sit_conjugal_codigo'],		# 09.Código de situação conjugal
					$rowUsu['scolar_codigo'], 				# 10.Código de escolaridade
					$rowUsu['usu_data_nasc'], 			# 11.Data de nascimento
					$rowUsu['usu_seox'],						# 12. Sexo
					$rowUsu['usu_pai_nome'],				# 13.Nome do Pai
					'', 													# 14.Nome Fonético do Pai (Preencher com brancos.)
					$rowUsu['usu_mae_nome'] ,			# 15.Nome da Mãe
					'', 													# 16.Nome Fonético da Mãe (Preencher com brancos.)
					$rowUsu['usu_cns'], 						# 17. Número do CNS – Cartão Nacional de Saúde
					$rowUsu['usu_pis_pasep'], 				# 18. PIS/PASEP
					$rowUsu['usu_cpf'], 						# 19. CPF
					$rowUsu['usu_tipo_certidao'] ,		# 20. Código do Tipo de Certidão
					$rowUsu['usu_cartorio_nome'] ,		# 21. Nome do Cartório – Certidão
					$rowUsu['usu_livro_numero'] ,		# 22. Número do Livro – Certidão
					$rowUsu['usu_folha_numero'] ,		# 23. Número da  Folha – Certidão
					$rowUsu['usu_cert_term_num'] ,	# 24. Número do Termo – Certidão
					$rowUsu['usu_cert_data_emis'], 	# 25. Data da Emissão – Certidão
					$rowUsu['usu_freq_escola'] ,			# 26. Indica se o usuário freqüenta escola ou não
					$rowUsu['usu_rg_orgao'] ,				# 27. Identidade –  Código do Órgão Emissor
					$rowUsu['usu_rg_num'] ,				# 28. Identidade – Número
					$rowUsu['usu_rg_compl'] ,				# 29. Identidade - Complemento 
					$rowUsu['usu_rg_uf'] ,						# 30. Identidade – Sigla da UF Emissora
					$rowUsu['usu_rd_data_emiss'] ,		# 31. Identidade – Data de Emissão
					$rowUsu['usu_ctps_num'] ,				# 32. CTPS – Número
					$rowUsu['usu_ctps_num_serie'],	# 33. CTPS - Número de Série
					$rowUsu['usu_ctps_uf'] ,					# 34. CTPS - Sigla da UF Emissora
					$rowUsu['usu_ctps_data'] ,				# 35. CTPS – Data de Emissão
					$rowUsu['usu_tit_ele_num'] ,			# 36. Título de Eleitor – Número e DV
					$rowUsu['usu_tit_ele_zona'] ,			# 37. Título de Eleitor – Zona
					$rowUsu['usu_tit_ele_secao'] ,		# 38. Título de Eleitor – Seção
					$rowUsu['nac_codigo'] ,					# 39. Código da Nacionalidade
					$rowUsu['usu_dt_entrada_pais'],	# 40. Data de Entrada no País
					$rowUsu['usu_nat_port_num'],		# 41. Naturalização – Número da Portaria
					$rowUsu['usu_nat_data'],				# 42. Naturalização – Data
					$rowUsu['usu_nat_data_preenc'],	# 43. Data de Preenchimento do formulário
					$rowUsu['usu_dt_inclusao'],			# 44. Data de Inclusão do usuário
					$rowUsu['usu_dt_alteracao'],			# 45. Data de Alteração do usuário
					$rowUsu['usu_lote_num']	,			# 46. Lote – Número do lote do usuário
					$rowUsu['domic_codigo']	,			# 47. Identificador do Domicílio do usuário
					$rowUsu['res_domic'],						# 48. Município de residência do usuário
					$rowUsu['usu_domic_codigo'],		# 49.Código do Domicílio do usuário
					$rowUsu['munic_nasc_codigo'],		# 50. Município – Nascimento (Código do IBGE)

					$rowUsu['usu_domic_codigo'],		# 51. Nome do Município – Nascimento
					$rowUsu['usu_domic_codigo'],		# 52. Código do CBOR
					$rowUsu['usu_domic_codigo'],		# 53. Identificação do cadastrador da ficha de usuário.
					$rowUsu['usu_domic_codigo'],		# 54. Identificador de máquina
					$rowUsu['usu_domic_codigo'],		# 55. Código do Município
					$rowUsu['usu_domic_codigo'],		# 56. Indica se o usuário participa do programa social Bolsa Alimentação
					$rowUsu['usu_domic_codigo'],		# 57. Indica se o usuário participa do programa social PRODEA
					'',														# fixo! 58. Campo reservado  
					'',														# fixo! 59. Campo reservado  
					'',														# fixo! 60. Campo reservado  
					'',														# fixo! 61. Campo reservado  
					'',														# fixo! 62. Campo reservado  
					$rowUsu['usu_domic_codigo'],		# 63. Indicador de operação: 1 – Inclusão 4 – Correção (Para corrigir problemas retornados pelo DATASUS) 5 – Confirmação de Homônimo 7 – Confirmação de Homônimo com novo documento 8 – Correção de Alteração 9 – Alteração
					$rowUsu['usu_domic_codigo'],		# 64. Numero seqüencial da pessoa no domicilio
					$rowUsu['usu_domic_codigo'],		# 65. Indica se o usuário está vinculado  ao Domicílio
					$rowUsu['usu_domic_codigo'],		# 66. Indica se o usuário está vinculado ao SIAB
					$rowUsu['usu_domic_codigo'],		# 67. Nome do Operador
					$rowUsu['usu_domic_codigo'],		# 68. Versão
					$rowUsu['usu_domic_codigo'],		# 69. Código de Usuário Municipal
					$rowUsu['usu_domic_codigo']			# 70. Código de Domicilio Municipal
		);			

		return $this;
	
	} // function criaUsuario()
	
	/** Cria o Trailler */
	function criaTrailler( $rowTrailler )
	{
		$this->TraillerStr = 
					"%07d".
					"%01d".
					"%07d".
					"%40s".
					"%08d".
					"%1020s"
		;

		$this->Trailler = sprintf(  $this->TraillerStr,
					$rowTrailler['tra_codigo'],						# Número Seqüencial do registro
					9,															# fixo! Indicador de registro tipo trailler de arquivo de município (Valor:  “9�)
					$this->ibge,											# Código do Município (IBGE)
					$this->cid_nome,									# Nome do Municipio
					$rowTrailler['tra_codigo'],						# Quantidade de registros do município,incluindo header e trailler
					''															# fixo! Filler 

		);			

		return $this;
	
	} // function criaTrailler()
}

// castro: 4104907
// SANTA DE DE GOIAS 5219258

$C = new LayoutCritica( $ibge = 5219258 ) ;

$C->criaArquivo();

//print "String:<br /><pre>\n{$C->Trailler}</pre><br />";
//var_dump($C->Trailler);


//print "\n<pre>\n"; printf("%'?10s%'.-5s1", 'asdf','5'); print "</pre>";

?>