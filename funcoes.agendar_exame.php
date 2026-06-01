<?php
/**
@brief Funcao usadas para a validacao do agendamento de exames

@return int (tipo do erro) 
 - 0 : 'OK'
 - 1 : 'O Procedimento já foi cadastrado para este paciente nesta data !'
 - 2 : 'Não há mais vagas para esta data !'
 - 3 : 'A Data escolhida não pode ser reservada'

@param $usu_codigo
@param $data
@param $proc_codigo
@param $med_codigo
@param $gex_tipo 
*/

function valida_agenda_agt( $usu_codigo, $data, $proc_codigo, $med_codigo, $proc_tipo )
{
	// arruma...
	$gex_tipo = trim( $proc_tipo );
	
	$r = db_get("SELECT exame_dt_valida( $usu_codigo, '$data', '$proc_codigo', '$med_codigo','$gex_tipo')");
	
	return intval($r);
}

function valida_agenda( $usu_codigo, $data, $proc_codigo, $med_codigo, $proc_tipo )
{
	// arruma...
	$proc_tipo = trim( $proc_tipo );
	
	$r = db_get("SELECT exame_dt_valida( $usu_codigo, '$data', '$proc_codigo', '$med_codigo','$proc_tipo')");
	
	#return intval($r);
	// CHAMA A FUNCAO DO BANCO AGORA !

	// verifica se jah possui um exame desse tipo para esse cliente
// 	$stmt = "SELECT COUNT(*) FROM agendamento_exame_lista 
// 	WHERE usu_codigo = $usu_codigo AND agexl_data = '$data' AND proc_codigo = $proc_codigo
// 	AND med_codigo = $med_codigo";
// 	$jah_tem = db_get( $stmt );
// 
// 	if( intval($jah_tem) >= 1 )
// 	{
// 		return 1;
// 	}
// 	else
// 	{
// 		// verifica se ainda tem 'vaga'
// 		$total = db_get("SELECT COUNT(*) from agendamento_exame_lista
// 			WHERE proc_codigo = $proc_codigo AND med_codigo = $med_codigo AND 
// 			agexl_data = '$data'");
// 		
// 		if( $gex_tipo == 'V' )
// 		{
// 			$stmt = "SELECT proc_valor FROM procedimento WHERE proc_codigo=$proc_codigo";
// 			$valor =  db_get($stmt);
// 			$stmt = "SELECT graex_valor - ($valor * $total) >= $valor   from grade_exame
// 				WHERE proc_codigo = $proc_codigo AND med_codigo = $med_codigo 
// 				AND graex_data = '$data'";
// 				
// 			$tem_vaga =  db_get($stmt) == 't';
// 			if( ! $tem_vaga )
// 			{
// 				return 2;
// 			}
// 		}
// 		// quantidade
// 		else if( $gex_tipo == 'Q' )
// 		{
// 			$stmt = "SELECT COUNT(*) FROM agendamento_exame_lista 
// 				WHERE proc_codigo = $proc_codigo AND med_codigo = $med_codigo 
// 				AND agexl_data = '$data' ";
// 			$total = db_get($stmt);
// 			
// 			$stmt = "SELECT $total + 1 <= graex_qtde from grade_exame
// 				WHERE proc_codigo = $proc_codigo AND med_codigo = $med_codigo 
// 				AND graex_data = '$data'";
// 			$tem_vaga =  db_get($stmt) == 't';
// 			
// 			if( ! $tem_vaga )
// 			{
// 				return 2;
// 			}
// 
// 		}
// 	}
// 	return 0;
}

function calcula_melhor_data( $exames )
{
	// data => qtde_exames
	$aux = array();
	foreach( $exames as $ex => $datas )
	{
		foreach( $datas as $dt )
		{
			$aux[ $dt ]++;
		}
	}
	// ordenando pelo valor
	arsort($aux);
	reset($aux);
	$R = array();
	foreach( $aux as $dt0 => $qtde )
	{
		foreach( $exames as $ex => $dt )
		{
			if( in_array($dt0,$dt) )
			{
				if( ! in_array( $ex, array_keys($R)) )
				{
					$R[ $ex ] = $dt0;
				}	
			}	
		}	
	}
	return $R;
}


// $array = array();
// $array['Exame:A'] = array('23/01/2007','24/01/2007','25/01/2007');
// $array['Exame:B'] = array('24/01/2007','25/01/2007');
// $array['Exame:C'] = array('23/01/2007','25/01/2007');
// $array['Exame:D'] = array('27/01/2007','25/01/2007');
// $array['Exame:E'] = array('21/01/2007','29/01/2007', '25/32/2007');
// $array['Exame:F'] = array('20/01/2007','29/01/2007', '25/32/2007');
// 
// print '<pre>';
// var_dump(calcula_melhor_data( $array ));
