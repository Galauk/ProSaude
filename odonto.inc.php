<?php
session_start();
// Arquivo de funcoes para 'odonto'
// mapeamento dos dentes de adultos
$DENTES_ADULTO = array(
	1 => 'Incisivo Central',
	2 => 'Incisivo Lateral',
	3 => 'Canino',
	4 => '1&ordm; Premolar',
	5 => '2&ordm; Premolar',
	6 => '1&ordm; Mmolar',
	7 => '2&ordm; Molar',
	8 => '3&ordm; Molar'
);
// mapeamento dos dentes de criancas
$DENTES_CRIANCA = array(
	1 => 'Incisivo Central',
	2 => 'Incisivo Lateral',
	3 => 'Canino',
	4 => '1&ordm; Molar',
	5 => '2&ordm; Molar'
);
// mapeamento das faces
$FACES = array (
	'O' => 'Oclusal / Incisal', 	// face que bate dente com dente. A parte que mastiga
	'V' => 'Vestibular', 			// a parte do dente que fica em contato com a bochecha
	'L' => 'Lingual / Palatina',	// a face que fica voltada pra dentro da boca. Contato com a lingua.
	'M' => 'Mesial',				// face da frente, em direcao a ponta da lingua
	'D' => 'Distal'					// face de tras, em direcao a garganta
);

/*$SITUACOES = array (
	"A" 		=> "Ausente",
	"H"		=> "Hígido",
	"CO"		=> "Restaurado",
	"TC"		=> "Trat. Concluído",
	"C"		=> "Cariado",
	"S"		=> "Cariostático",
	"SEL"		=> "Selante",
	"BAR"		=> "Extrair",
	"ATF"		=> "Apl. TÓP. Flúor",
	"X"		=> "Extraído",
	"O"		=> "..outro"
);*/
$SITUACOES = array(
	1 =>	"Restauracao a ser realizada",
	2 =>	"Restauracao realizada",
	3 =>	"Restauracao pre-existente mantida",
	4 =>	"Dente Ausente",
	5 =>	"Exodontia a ser realizada",
	6 =>	"Exodontia realizada",
	7 =>	"Selante a ser realizado",
	8 =>	"Selante realizado",
	9 =>	"Terapia pulpar realizada",
	10 =>	"Terapia pulpar pre-existente",
	11 =>	"Terapia pulpar pre-existente em boas condicoes"
			);

$_vermelho 	= '<span style="color:#F00">Vermelho</span>';
$_azul 			= '<span style="color:#00F">Azul</span>';

$LEGENDAS = array(
	1 =>	"Face circulada em $_vermelho",
	2 =>	"Face preenchida em $_vermelho",
	3 =>	"Face preenchida em $_azul",
	4 =>	"Traco vertical em $_azul",
	5 =>	"Traco diagonal em $_vermelho",
	6 =>	"X em $_vermelho",
	7 =>	"S em $_vermelho",
	8 =>	"S circulado em $_vermelho",
	9 =>	"Triangulo vazio em $_vermelho",
	10 =>	"Triangulo cheio em $_vermelho",
	11 =>	"Triangulo cheio em $_azul"

);
/** Descobre o nome do dente */
function pega_dente( $num )
{
	global $DENTES_ADULTO, $DENTES_CRIANCA;
	
	$q = $num[0];
	$d = $num[1];
	
	// adulto
	if( $q <= 4 )
	{
		$qs = ( $q == 1 || $q == 2 ? 'superior' : 'inferior' );
		$qp = ( $q == 1 || $q == 4 ? 'direito' : 'esquerdo' );
		return $DENTES_ADULTO[$d] . ' ' . $qs . ' ' . $qp;
	}
	// crianca
	else
	{
		$qs = ( $q == 5 || $q == 6 ? 'superior' : 'inferior' );
		$qp = ( $q == 5 || $q == 8 ? 'direito' : 'esquerdo' );
		return $DENTES_CRIANCA[$d] . ' ' . $qs . ' ' . $qp;
	}
}

/** imprime a imagem do dente (junto com td) **/
//function dente_row( $num, $id_login, $od_codigo, $situacao = array(), $hist_faces = '' )
function dente_row( $num, $id_login, $age_codigo, $situacao = 0 , $hist_faces = '' )
{
	$situacao_img = '';
	
	// quadrante ?
	$dente_img = $num;
	$quad = "{$num[0]}";
	if( $quad >= 1 && $quad <= 4 )
		$img = 'adulto';
	elseif( $quad >= 5 && $quad <= 8 )
		$img = 'crianca';

	// arrumando o array
	switch( $quad )
	{
		case 1:
		case 5:
			$faces = array( '', 'V', 'M', 'L', 'D', 'O' );
			break;
		case 2:
		case 6:
			$faces = array( '', 'V', 'D', 'L', 'M', 'O' );
			break;
		case 3:
		case 7:
			$faces = array( '', 'L', 'D', 'V', 'M', 'O' );
			break;
		case 4:
		case 8:
			$faces = array( '', 'L', 'M', 'V', 'D', 'O' );
			break;
	}
	
	// aplicado a faces
	if( $situacao >= 1 && $situacao <= 3 )
	{
		$imagens		= '';
		$arr 			= explode( ';', $hist_faces );
		for( $i=0; $i < count($arr); $i++ )
		{	
			$pos = array_search( $arr[$i], $faces );
			if( $pos > 0 )
				$imagens .= "
					<div style=\"position:absolute;margin:0;\">
						<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/dente_{$img}_anot_{$situacao}_face_{$pos}.gif' alt='' />
					</div>"; 	
		}

	}
	// aplicado ao dente todo
	else if( $situacao >= 3 && $situacao <= 11 )
	{
		//$face = explode(';', $hist_faces);
		$pos = array_search( $arr[$i], $hist_faces );
		$imagens = "
		<div style='background:url(".$_SESSION[linkroot].$_SESSION[comum]."imgs/dente_comum_anot_{$situacao}.gif);width:31px;position:absolute;
		height:34px;border:0px solid #000;'>&nbsp;</div>";

	}
	

	return "
		<td style='border:0px solid #000' class='c'>
			<strong>$num</strong><br />
			
			<div style=\"background:url(".$_SESSION[linkroot].$_SESSION[comum]."imgs/dente_{$img}.jpg);width:31px;height:34px;border:0px solid red;cursor:pointer;\"
				onmouseover=\"mostra_dente('{$num}',1)\" 
				onmouseout=\"mostra_dente('{$num}',0)\"
				onclick=\"mapa_dentes('{$id_login}','{$age_codigo}','{$num}')\">

				$imagens

			</div>

		</td>";
}

/** arruma as faces, devolvendo string com as letras */
function arruma_faces_l( $faces )
{
	return str_replace(';',' ',$faces);
}