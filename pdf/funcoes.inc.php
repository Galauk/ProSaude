<?php
function abr($Nome){
	// Calcula a quantidade de caracteres do nome
	$quantidade = strlen($Nome);
	//Variavel para fazer a comparacao se passou da quantidade maxima permitida
	$maximo_caracter = 20;
	// if para fazer a comparação e decidir se é necessario fazer o tratamento do nome
	if($quantidade<$maximo_caracter){
		return $Nome;
	}

	$Nome = explode(" ", $Nome); // cria o array $nome com as partes da string
	$num = count($Nome); // conta quantas partes o nome tem
	$novo_nome = '';
	// variavel que irá concatenar as partes do nome
	$espacos = " ";

	//Variaveis para controle qual sobrenome o foreach está 
	$count = 1;
	foreach($Nome as $var) { // loop no array
		//echo "<br/> Num ".$num."Count ".$count;
		if (($count == 1) || ($count == $num)) {
			$novo_nome .= $var.' '; // Atribui o primeiro nome
			//$count++;
		}


		//Quando for para segunda posição do array, que é o primeiro sobrenome e que não 
		//seja maior do que a quantidade de sobrenome do nome
		
		if(($count >= 2) && ($count < $num)) {
			// Quando aparecer um desses entao nao atribui
			$array = array('do', 'Do', 'DO', 'da', 'Da', 'DA', 'de', 'De', 'DE', 'dos', 'Dos', 'DOS', 'das', 'Das', 'DAS');
			//Compara se a variavel var do foreach tem algum dos conteudos nao permitos
			//do array
			if(in_array($var, $array)) {
				// não Atribui para o nome novo
			}else {
				$novo_nome .= substr($var, 0, 1).'. '; // abreviou
			} // fim 
		}
		
	$count++;

	}//Final do Foreach
	return $novo_nome;

}//Final da Função
?>
