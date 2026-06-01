<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/__array.php";
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();   
	if($acao == ""){
		echo $common->openModal("Compet&ecirc;ncia",700,"Gerar",null,"document.gerar.submit()",null,null,100);
			echo $form->openForm("exportacaoSisprenatal.php","POST","gerar");
				echo $form->hiddenForm(acao, "gerar");
				echo $table->openTable();
					echo $table->criaLinha(array($form->inputSelect("mes_competencia",$arrayMeses,"Data"),$form->inputText(ano_competencia, $ano_competencia,null,5,4)));
				echo $table->closeTable();
			echo $form->closeForm();				
		echo $common->closeModal();
	}
	if($_POST[acao] == 'gerar'){
		
		$mes_competencia = $_POST['mes_competencia'];
		$ano_competencia = $_POST['ano_competencia'];
		$sqlLogon = "SELECT *
					   FROM unidade as uni
					   JOIN cidade as cid
					     ON uni.uni_codigo_ibge = cid.cid_codigo_ibge
					  WHERE id_login = $id_login";
		$queryLogon = pg_query($sqlLogon);
		$linhaLogon = pg_fetch_array($queryLogon);
				
		$codioUps = str_pad($ups,7,"0",STR_PAD_LEFT);
		$nomeUnidade = str_pad($linhaLogon[uni_desc],40," ",STR_PAD_LEFT);
		$ufUnidade = str_pad($linhaLogon[uf_sigla],2,"0",STR_PAD_LEFT);
		$codigoIbge = str_pad($linhaLogon[uni_codigo_ibge],2,"0",STR_PAD_LEFT);
		$cgcUnidade = str_pad($cgc,14,"0",STR_PAD_LEFT);
		$sigla = str_pad($sigla,2,"0",STR_PAD_LEFT);
		$razaoSocial = str_pad($razaoSocial,6,"0",STR_PAD_LEFT);// Verificar se pega
		$enderecoUnidade = str_pad($linhaLogon[uni_endereco],35," ",STR_PAD_LEFT);
		$numeroEnderecoUnidade = str_pad($linhaLogon[uni_numero],6," ",STR_PAD_LEFT);
		$complementoUnidad = str_pad($complementoUnidad,15," ",STR_PAD_LEFT);// esse campo não tem em nosso Banco, porem não é obrigatório
		$bairroUnidade = str_pad($bairroUnidade,15," ",STR_PAD_LEFT);// esse campo não tem em nosso Banco, porem não é obrigatório
		$cidadeUnidade = str_pad($linhaLogon[cid_nome],40," ",STR_PAD_LEFT);
		$cepUnidade = str_pad($linhaLogon[uni_cep],8," ",STR_PAD_LEFT);
		$telefoneUnidade = str_pad($telefoneUnidade,15," ",STR_PAD_LEFT);// esse campo não tem em nosso Banco, porem não é obrigatório
		$tipoPrestador = str_pad($complementoUnidad,15," ",STR_PAD_LEFT);
		$destinoBpa = str_pad($destinoBpa,2,"0",STR_PAD_LEFT);
		$tipoDestino = str_pad($tipoDestinoBpa,1,"0",STR_PAD_LEFT); //M-Município/E-Estado
		$gestao = str_pad($gestao,1,"0",STR_PAD_LEFT); //1-Município/2-Estadostr_pad
		$situacaoUnidade = str_pad($tipoDestinoBpa,1,"0",STR_PAD_LEFT); //1-Ativa/0-Desativada
		$upsOld = str_pad($upsOld,1,"0",STR_PAD_LEFT);
		
		
		$registroUnidade = $codioUps."/".$nomeUnidade."/".$ufUnidade."/".$nomeUnidade."/".$codigoIbge."/".$cgcUnidade."/".$sigla."/".
						   $destinoBpa."/".$tipoDestino."/".$tipoDestino."/".$tipoDestino."/".$atividadeProfissional."/".$gestao."/".
						   $situacaoUnidade."/".$upsOld;
						   
		$sqlGeralPreNatal = "SELECT * 
							   FROM sis_pre_natal as sispn
							   JOIN usuario as usu
							     ON usu.usu_codigo = sispn_usu_codigo
							   JOIN domicilio as d
							     ON d.dom_codigo = usu.dom_codigo
							   JOIN rua as r
							     ON r.rua_codigo = d.rua_codigo
							   JOIN cidade as c
							     ON c.cid_codigo = r.cid_codigo
							   JOIN psf as p
							     ON p.dom_codigo = d.dom_codigo
							  WHERE to_char(sispn_data_cadastro, 'MM/YYYY') = '$mes_competencia/$ano_competencia'";
		$queryGeralPreNtal = pg_query($sqlGeralPreNatal);
		while($linhas = pg_fetch_array($queryGeralPreNtal)){
			$codigoGestante = str_pad($linhas[sispn_codigo],11,"0",STR_PAD_LEFT);
			$codigoUpsCadastramento = str_pad($codigoUpsCadastramento,7,"0",STR_PAD_LEFT);
			$nomeGestante = str_pad($linhas[usu_nome],40,"0",STR_PAD_LEFT);
			$dataNascimentoGestante = str_pad($linhas[usu_datanasc]);
			$nomeDaMae = str_pad($linhas[usu_mae],7,"0",STR_PAD_LEFT);
			$codigoArea = str_pad($linhas[psf_area],3,"0",STR_PAD_LEFT);
			$codigoMicroArea = str_pad($linhas[psf_micro_area],2,"0",STR_PAD_LEFT);
			$enderecoGestante = str_pad($linhas[rua_nome],35," ",STR_PAD_LEFT);
			$numeroDomicilio = str_pad($linhas[dom_numero],6," ",STR_PAD_LEFT);
			$complementoGestante = str_pad($linhas[dom_complemento],15," ",STR_PAD_LEFT);
			$bairroGestante = str_pad($linhas[rua_bairro],15," ",STR_PAD_LEFT);
			$cidadeGestante = str_pad($linhas[cid_nome],40," ",STR_PAD_LEFT);
			$siglaUfGestante = str_pad($linhas[uf_sigla],2," ",STR_PAD_LEFT);
			$cepGestante = str_pad($linhas[rua_cep],8," ",STR_PAD_LEFT);
			$codigoIbgeGestante = str_pad($linhas[cid_codigo_ibge],7," ",STR_PAD_LEFT);
			$cartaoSus = str_pad(($linhas[usu_cartao_sus] == "" ? "$linhas[usu_cartao_p_sus]" : "$linhas[usu_cartao_sus]"),15," ",STR_PAD_LEFT); // IF TERNARIO PARA VERIFICAR SE TEM CARTAO PROVISORIO OU DEFINITIVO
			$numeroCIC = str_pad($numeroCIC,11," ",STR_PAD_LEFT);
			$certidaoNascsGestante = str_pad(($linhas[usu_cert_cartorio_nasc] == "" ? "$linhas[usu_cert]" : "$linhas[usu_cert_cartorio_nasc]"),24," ",STR_PAD_LEFT);
			$livroCertidaoGestante = str_pad(($linhas[usu_cert_cartorio_nasc] == "" ? "$linhas[usu_cert_livro]" : "$linhas[usu_cert_livro_nasc]"),24," ",STR_PAD_LEFT);
			$folhaCertidaoGestante = str_pad(($linhas[usu_cert_lv_fls_nasc] == "" ? "$linhas[usu_cert_lv_fls_nasc]" : "$linhas[usu_cert_livro_nasc]"),4," ",STR_PAD_LEFT);
			$identidadeGestante = str_pad($linhas[usu_rg],15," ",STR_PAD_LEFT);
			$orgaoEmissorGestante = str_pad($linhas[usu_rg_emissor],5," ",STR_PAD_LEFT);
			$casteiraTrabalhoGestante = str_pad($linhas[usu_ctps],12," ",STR_PAD_LEFT);
			$serieCarteiraTrabalho = str_pad($linhas[usu_ctps_serie],3," ",STR_PAD_LEFT);
			$unidadeFederacaoCasteiraTrabalho = str_pad($unidadeFederacaoCasteiraTrabalho,2," ",STR_PAD_LEFT);
			$dataPrimeiraConsulta = "$linhas[sispn_data_cadastro]";
			$dataUltimaMenstruacao = $linhas[sispn_data_ultima_menstruacao];
			$dataAtualizacao = date('Y-m-d');
			$competencia = $ano_competencia.$mes_competencia;
			$atividadeProfissional = str_pad($atividadeProfissional,2," ",STR_PAD_LEFT);
			$telefoneGestante = str_pad($linhas[dom_telefone],11," ",STR_PAD_LEFT);
			
			if($linhas[usu_cert] == true){
				$tipoDeCertidao = "2";
			}else if($linhas[usu_cert_cartorio_nasc] == true){
				$tipoDeCertidao = "1";
			}
			$tipoDeCertidao = str_pad($linhas[dom_telefone],11," ",STR_PAD_LEFT);
			$racaGestante = str_pad($linhas[rac_codigo],1," ",STR_PAD_LEFT);
			$ocupacao = str_pad($linhas[usu_cbo_r],6," ",STR_PAD_LEFT);
			$nacionalidade = str_pad($linhas[pais_codigo],3," ",STR_PAD_LEFT);
			
			$etnia = str_pad($etinia,3," ",STR_PAD_LEFT);
			$sqlCns = "SELECT *
			             FROM atendimento as a
			            WHERE sispn_codigo = $linhas[sispn_codigo]
			            JOIN usuarios as u
			              ON a.med_codigo = u.usr_codigo
			           ORDER BY ate_data";
			$queryCns = pg_query($sqlCns);
			$linhaCns = pg_fetch_array($queryCns);
			$cnsProfissional = str_pad($linhaCns[usr_medico_cnes],15," ",STR_PAD_LEFT);
		}
		//LINHA DE INFORMAÇÃO DA GESTANTE
		
		$sqlAcompanhamentoDeGestantes = "SELECT *
										   FROM atendimento as a
										   JOIN sis_pre_natal as s
										     ON s.sispn_codigo = a.sispn_codigo
										  WHERE TO_CHAR(sispn_data_cadastro, 'MM/YYYY') = '$mes_competencia/$ano_competencia'";
		$queryAcompanhamento = pg_query($sqlAcompanhamentoDeGestantes);
		while($linhaAcompanhamento = pg_fetch_array($queryAcompanhamento)){
		
		$cod_gestacao = str_pad($linhaAcompanhamento[sispn_numero_gestacao],11);
		$codigoUps = str_pad($codigoUps,7," ",STR_PAD_LEFT);
		$dataMovimento = $linhaAcompanhamento[ate_data];
		$atividadeProfissionalAcompanhamento;
		$tipoConsulta = str_pad($linhaAcompanhamento[ate_tipo_consulta_prenatal],1," ",STR_PAD_LEFT);
		$flagAjuste = str_pad();
		}
				
		
	}
?>