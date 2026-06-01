<?php
	include_once "../db.inc.php";
	include_once '../funcoes.inc.php';
	/*
	 * Exemplo de como usar: debug($array, $PHP_SELF, $id_login);
	 */
	function debug($array, $origem, $id_login){

		$folder = explode("/", $_SERVER['REQUEST_URI']);
		$raiz = $_SERVER['DOCUMENT_ROOT']."/$folder[1]";	
		
		$select = "SELECT deb_habilitado 
					 FROM debug
					WHERE usr_codigo = $id_login";
		$executa = pg_query($select);
		$row = pg_fetch_array($executa);
		$pre = "<pre>".print_r($array, true)."</pre>";
		if ($row[0] == 'S'){
			echo $pre;
		}
		$quebra = chr(13).chr(10);
		$msg = date('d/m/Y - H:i:s')."   ".$pre."   ".$origem."   ".$id_login.$quebra;
		
		if (!is_dir("$raiz/debugLog")){
			mkdir("$raiz/debugLog", 0777);
		}
		
		//cria o nome do arquivo
		$arquivo = "$raiz/debugLog/debug_".date('Y')."_".date('m')."_".date('d')."_1.log";
		
		//verifica se o arquivo já existe. Se existir, selecionar o arquivo com numeração mais alta. Se não existir, cri-lo.
		if(is_file($arquivo)){
			$arquivo = selecionaArquivo("$raiz/debugLog/");
		}else{
			$arquivo2 = basename($arquivo,".log");
			return criaArquivo($arquivo2, $msg, "$raiz/debugLog/", ".log", "a+");
		}
		
		$arquivo2 = basename($arquivo,".log");
		
		//incrementar a numeração do arquivo
		$i = substr($arquivo2, -1, 1);
		$i++;
		//limitar o tamanho do arquivo de log em 2 MB
		if (filesize("$raiz/log/".$arquivo2.".log") < 2097152){
			criaArquivo($arquivo2, $msg, "$raiz/debugLog/", ".log", "a+");
		}else{
			criaArquivo(substr($arquivo2, 0, -1).$i, $msg, "$raiz/debugLog/", ".log", "a+");
		}
	}
	
	function selecionaArquivo($diretorio){
		$ponteiro  = opendir($diretorio);
		// monta os vetores com os itens encontrados na pasta
		while ($nome_itens = readdir($ponteiro)) {
			$itens[] = $nome_itens;
		}
		
		/*	O que fizemos aqui, foi justamente, pegar o diretério, abri-lo e l-lo.
			
			Continuando, vamos usar:
			sort: ordena os vetores (arrays), de acordo com os parâmetros informados. Aqui estou ordenando por pastas e depois arquivos
		*/	
		// ordena o vetor de itens
		arsort($itens);
		// percorre o vetor para fazer a separacao entre arquivos e pastas 
		foreach ($itens as $listar) {
			// retira "./" e "../" para que retorne apenas pastas e arquivos
			if ($listar!="." && $listar!=".."){ 
		
				// checa se o tipo de arquivo encontrado  diferente de pasta
				if (!is_dir($listar)) { 
					$arquivos[] = $listar;
				}
			}
		}
		
		/* Vimos acima, a expressão is_dir, indicando que as ações devem ento ser executadas, ali mesmo, no diretério que já foi aberto e lido.*/
		return $arquivos[0];
	}
	
?>