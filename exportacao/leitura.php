<?
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."exportacao/funcoesBPA.inc.php";
	
	echo "primeiro teste: ".calculaControle(1033);
	/*$teste = 1721035027522;
	$t2 = recursiveMod($teste, 1111);
	echo "<br>segundo teste: ".$t2;*/
	
//	function incluiCabecalho($nome, $msg, $path, $nomeMes){
//		$quebra = chr(13).chr(10);//quebra de linha
//		$nome = "teste2";
//		$path = "./arquivos2/";
//		$nomeMes = "txt";
//		$arquivo = "./arquivos2/teste2.txt";//o arquivo
//		$linhas = file($arquivo);//pegando os valores do arquivo
//		
//		$ultimaLinha = (strlen($linhas[count($linhas)-1]) > 10 ? $linhas[count($linhas)-1] : $linhas[count($linhas)-2]); //pega a última linha. se ela for vazia, pega a penúltima
//		$qtdeFolhas = contaFolhas($ultimaLinha);
//		$qtdeLinhas = contaLinhas($ultimaLinha);
//		/*
//		 * MONTANDO O CABECALHO
//		 */
//		$numlinhas 			= str_pad($qtdeLinhas, 6, "0", STR_PAD_LEFT);  // retorno "000053"
//		$numfolhas 			= str_pad($qtdeFolhas, 6, "0", STR_PAD_LEFT);  // retorno "000003"
//		$controle 			= calculaControle();
//		$orgaoOrigem 		= str_pad("Hospital da Providencia mi", 30, " ", STR_PAD_RIGHT);
//		$siglaOrgaoOrigem 	= str_pad("HPA", 6, " ", STR_PAD_RIGHT);;
//		$cgc 				= str_pad($input, 14, "0", STR_PAD_LEFT);
//		$orgaoDestino 		= str_pad("Autarquia Municipal de Saude", 40, " ", STR_PAD_RIGHT);
//		$municipalEstadual 	= "M"; //tamanho 1
//		$versaoSistema 		= str_pad("D01.27", 10, " ", STR_PAD_RIGHT);
//	
//		$ano 	 = str_pad(date('Y'), 4, "0", STR_PAD_LEFT);
//		$mes 	 = str_pad(date('m'), 2, "0", STR_PAD_LEFT);
//		
//		$escrever = "#BPA#".$ano.$mes.$numlinhas.$numfolhas.$controle.$orgaoOrigem.$siglaOrgaoOrigem.$cgc.$nomeOrgaoSaude.$orgaoDestino.$municipalEstadual.$versaoSistema.$quebra;
//		/*
//		 * CABECALHO MONTADO
//		 */
//		$arquivoA = criaArquivo($nome, $escrever, $path, ".".$nomeMes, "w");
//		
//		for($x = 0; $x < count($linhas); $x++){
//			$msg .= $linhas[$x];//escrevendo o arquivo
//		}
//		if ($arquivoA){
//			$resposta = criaArquivo($nome, $msg, $path, ".".$nomeMes, "a");
//		}
//		return $resposta;
//	}
?>