<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";	
	include_once $_SESSION[root].$_SESSION[modulo]."exportacao/funcoesBPA.inc.php";
	
	//Indica a versão do sistema que foi importado + versão do banco
	$versaoBpa ="Versao: ". 01.30;
	$versaoBancoBpa = "Versao banco: ". 201102;

   //DADOS DO BPA
    $nomeDoBpa= "PA123---.FEV";
	$numeroDeLinhas = '000003';
	$numeroDeFolhas = '000001' ;
	$controle = 1556;
  //DADOS RECEBIOS VIA GET PARA FAZER OS SELECT
	
	$codigo_secretaria = $_GET['codigo_secretaria'];
	$usu_codigo = $_GET['usu_codigo'];
	
	$path = "arquivos/";
	$quebra = chr(13).chr(10);//quebra de linha
	$nome = "RelatorioDeBpa";
	
	$ms = str_pad("MS/SAS/DATASUS/", 20, " ", STR_PAD_RIGHT);
	$dataComp = str_pad("DATA COMP.", 22, " ", STR_PAD_LEFT);
	
	$dataSus = str_pad("15/03/2011", 22, " ", STR_PAD_RIGHT);
	$dataCompi = str_pad("FEV/2011", 24, " ", STR_PAD_LEFT);
	
	$espaco = str_pad(" ", 10, " ", STR_PAD_LEFT);
	
	$seleciona = " SELECT * 
					 FROM secretaria
					WHERE codigo_secretaria = $codigo_secretaria ";
	
	$executa = pg_query($seleciona);
	$linha = pg_fetch_array($executa);
	
	$gestor = "SELECT * FROM usuario WHERE usu_codigo = $usu_codigo";
	$exeGestor = pg_query($gestor);
	$resGestor = pg_fetch_array($exeGestor);
	
	
	$msg = $quebra."*******************************************************************".$versaoBpa.$quebra;
	$msg .= $ms."SISTEMA DE INFORMACOES AMBULATORIAIS".$dataComp.$quebra;
	$msg .=$dataSus."RELATORIO DE CONTROLE DE REMESSA".$dataCompi.$quebra;
	$msg .= "************************************************************".$versaoBancoBpa.$quebra.$quebra.$quebra;
	
	//$msg .= "    (ENCAMINHAR ESTE RELATORIO JUNTAMENTE COM O ARQUIVO DE BPA(s) GERADO.)".$quebra;
	
	$msg .= " ORGAO RESPONSAVEL PELA INFORMACAO".$quebra.$quebra;
	
	$msg .= " NOME   : ".$linha['nome_secretaria'].$quebra.$quebra;	
	$msg .= " SIGLA  : ".$linha['sec_sigla'].$quebra.$quebra;
	$msg .= " CGC/CPF: ".$linha['cnpj_secretaria'].$quebra;	
	
	$msg .= $quebra.$quebra." Carimbo e".$quebra;
	$msg .=" Assinatura : ___________________".$quebra.$quebra.$quebra.$quebra;
	$setorDe = str_pad("Setor de", 47, " ", STR_PAD_RIGHT);
	
	$msg.=" SECRETARIA DE SAUDE DESTINO DOS B.P.A.(s)".$quebra.$quebra;

	$msg .= " NOME  : ".$resGestor['usu_nome'].$quebra;	
	$msg .= " ORGAO (M)UNICIPAL OU (E)STADUAL  :". "M".$quebra.$quebra.$quebra;
	$msg .= " $setorDe.Carimbo e".$quebra;
	$msg .= " Recebimento : ____________ Data : ___/___/___  Assinatura : ________________".$quebra.$quebra.$quebra.$quebra;

	 
	$msg .= " ARQUIVO DE BPA(s) GERADO".$quebra.$quebra;
	
	$msg .= "               NOME : ".$nomeDoBpa.$quebra.$quebra;
	$msg .= " REGISTROS GRAVADOS : ".contaLinhas().$quebra.$quebra;    
	$msg .= "               BPA(s) ".contaFolhas().$quebra.$quebra;
	$msg .= " CAMPO DE CONTROLE :".calculaControle().$quebra.$quebra.$quebra.$quebra.$quebra.$quebra;
	$msg .= "    (ENCAMINHAR ESTE RELATORIO JUNTAMENTE COM O ARQUIVO DE BPA(s) GERADO.)".$quebra.$quebra.$quebra;
	echo criaArquivo($nome, $msg, $path, ".txt");
?>