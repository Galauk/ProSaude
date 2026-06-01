<?php

function validacoes($r){
		
		// 1: Especialidade x Procedimento
		especialidadeProcedimento($r);
		
		// 2: Procedimento x CID
		if( !is_null($r['bpa_cd10_codigo'])){
			procedimentoCid($r);
		}
		
		// Somente para individualizado
		if( $r['bpa_tipo'] == 'I'){
			
			// 3: Usu -> domicilio -> rua -> cidade.cid_codigo_ibge
			// 4: Usu -> domicilio -> rua -> cidade -> estado.pais_codigo
			ibgeEPais($r);	
			
			// 5: Etnia
			// Somente para Indígenas
			etnia($r);
			
			// 6: Número do SUS do paciente
			cnesPaciente($r);			
			
		} // Individualizado
}

function especialidadeProcedimento($r){
	global $ano,$mes;
	
	$sub_sql = "	SELECT MIN(e.esp_nome) AS esp_nome
					  FROM especialidade AS e 
					  JOIN medico_especialidade AS me
					    ON me.esp_codigo=e.esp_codigo
					   AND med_codigo={$r['usr_codigo']} 
					  JOIN rl_procedimento_ocupacao_historico AS rl
					    ON rl.co_ocupacao=e.cod_cbo 
					   AND rl.dt_competencia='{$ano}{$mes}' 
					  JOIN procedimento AS p
					    ON p.proc_codigo_sus=rl.co_procedimento
					   AND p.proc_codigo={$r['proc_codigo']};";
	//die($sub_sql);
	
	$sub_query = pg_query($sub_sql);
	$sub_row = pg_fetch_object($sub_query);
	if(is_null($sub_row->esp_nome)){
		//die("esp_procedi");
		fdebug($sub_sql);
		insereInconsistencia($r['bpa_codigo'],1);
		
	}
}

function procedimentoCid($r){
	global $ano,$mes;
		
	$sub_sql = "SELECT cd10_codigo
				  FROM cid10
				  JOIN rl_procedimento_cid_historico AS rl
				    ON rl.co_cid=cd10_codigo_cid
				   AND rl.dt_competencia='{$ano}{$mes}'
				  JOIN procedimento AS p
				    ON p.proc_codigo_sus=rl.co_procedimento
				   AND p.proc_codigo={$r['proc_codigo']}
				 WHERE cd10_codigo={$r['bpa_cd10_codigo']};";
	
	$sub_query = pg_query($sub_sql);			
	if(!pg_num_rows($sub_query)){
		//die("proc_cid");
		insereInconsistencia($r['bpa_codigo'],2);
	}
}

function ibgeEPais($r){	
	$sub_sql = "SELECT cid.cid_codigo_ibge,
					   uf.pais_codigo
				  FROM cidade AS cid
				  JOIN rua
				    ON rua.cid_codigo=cid.cid_codigo
				  JOIN domicilio AS dom
				    ON dom.rua_codigo=rua.rua_codigo
				  JOIN usuario AS usu
				    ON usu.dom_codigo=dom.dom_codigo
				  JOIN estado AS uf
				    ON uf.uf_codigo=cid.uf_codigo
				 WHERE usu.usu_codigo={$r['usu_codigo']};";
	
	$sub_query = pg_query($sub_sql);
	$num = pg_num_rows($sub_query);
	$sub_row = pg_fetch_array($sub_query);
	//die($sub_sql);
	if(!$num || is_null($sub_row['cid_codigo_ibge'])){
		//die("ibge");
		insereInconsistencia($r['bpa_codigo'],3);
	}	
	
	if(!$num || is_null($sub_row['pais_codigo'])){
		//die("ibge");
		insereInconsistencia($r['bpa_codigo'],4);
	}	
}

function etnia($r){
	$sub_sql = "SELECT rac_codigo,
				       etn_codigo
				  FROM usuario
				 WHERE usu_codigo={$r['usu_codigo']};";
	
	$sub_query = pg_query($sub_sql);
	$sub_row = pg_fetch_object($sub_query);
	
	// Somente para Indígenas
	if($sub_row->rac_codigo == 5){
		if(is_null($sub_row->etn_codigo)){
			//die("etnia");
			insereInconsistencia($r['bpa_codigo'],5);
		}
	}
}

function cnesPaciente($r){
	$sub_sql = "SELECT usu_cartao_sus
				  FROM usuario
				 WHERE usu_codigo={$r['usu_codigo']};";
	
	$sub_query = pg_query($sub_sql);
	$sub_row = pg_fetch_object($sub_query);
	if(is_null($sub_row->usu_cartao_sus)){
		//die("cnes");
		insereInconsistencia($r['bpa_codigo'],6);
	}
}

function insereInconsistencia($bpa_codigo, $bpai_codigo){
	global $erros;
	//die("aaookaokaok");
	$erros++;
	$sql = "INSERT INTO rl_bpa_inconsistencia(bpa_codigo, bpai_codigo) VALUES ('$bpa_codigo','$bpai_codigo');";
	fdebug("Adicionado incons. $bpai_codigo no item $bpa_codigo");
	return pg_query($sql);
}