<?php

/**
 * Usado pelo plugin jQuery.buscar.js que gera o autocomplete.
 */

require_once "global.php";
setError(1);

$term = utf8_decode($_GET['term']);
$limit = isset($_GET['l'])?$_GET['l']:5;
$tipo = isset($_GET['tipo'])?$_GET['tipo']:"usuario";

if($tipo == "usuario"){
	include_once COMUM.'/library/php/funcoes.inc.php';
	if(isDate($term)){
		$where = "usu.usu_datanasc = '$term'";
	} else {
		   $int = (int) $term;
		if($int){
			$where = "usu.usu_prontuario = '$term'";
		} else {
			$where = "(usu.usu_nome ilike '$term%'
			 		    OR usu.usu_mae ilike '$term%')";
		}
	}

	$id = "usu_codigo";
	$label = "usu_nome";

$sql = "SELECT usu.usu_codigo,
				   usu_foto_nome,
			       usu.usu_nome,
			       usu.usu_mae,
			       usu.usu_pai,
			       to_char(usu.usu_datanasc,'DD/MM/YYYY') AS usu_datanasc,
			       usu.usu_prontuario,
			       COALESCE(area_desc::text,'Não definido') AS psf_area,
			       usu.usu_bloqueado,
			       usu.dom_codigo,
			       usu.usu_nome_resp as nomeres
			  FROM usuario AS usu
			  LEFT JOIN psf
			    ON psf.dom_codigo=usu.dom_codigo
			  LEFT JOIN area
			    ON area.area_codigo=psf.psf_area
			   LEFT JOIN domicilio dom
			     ON dom.dom_codigo = usu.dom_codigo
			WHERE usu.usu_ativacao='S'
			   AND $where
			 ORDER BY usu.usu_nome, 
				   usu.usu_mae
				   LIMIT $limit";
	
} 

if($tipo == "usuario_aih"){
	include_once COMUM.'/library/php/funcoes.inc.php';
	if(isDate($term)){
		$where = "usu_datanasc = '$term'";
	} else {
		$where = "(usu_nome ilike '%$term%'
			 		    OR usu_mae ilike '%$term%'
			 		    OR usu_prontuario ilike '%$term%')";
	}

	$id = "usu_codigo";
	$label = "usu_nome";
	$sql = "SELECT usu_codigo,
					   usu_nome,
		 			   usu_mae,
		 			   usu_pai,
		 			   usu_rg,
		 			   usu_cpf,
		 			   CASE WHEN usu_sexo='M' THEN 'Masculino' WHEN usu_sexo='F' THEN 'Feminino' ELSE 'Não informado' END AS usu_sexo,
		 			   to_char(usu_datanasc,'DD/MM/YYYY') AS usu_datanasc,
		 			   usu_prontuario,
		 			   usu_cartao_sus,
		 			   usu_fone_recado,	
		 			   COALESCE(area_desc::text,'Não definido') AS psf_area,
		 			   rua_nome,
		 			   rua_cep,
		 			   dom_numero,
		 			   rua_bairro,
		 			   rua_cep,
		 			   cid_nome,
		 			   cid_codigo_ibge,
		 			   uf_sigla
		 		  FROM usuario AS usu
             LEFT JOIN psf
                    ON psf.dom_codigo=usu.dom_codigo
             LEFT JOIN area
                    ON area.area_codigo=psf.psf_area
             LEFT JOIN domicilio AS dom
                    ON dom.dom_codigo=usu.dom_codigo
             LEFT JOIN rua
                    ON rua.rua_codigo=dom.rua_codigo
             LEFT JOIN cidade AS cid
                    ON cid.cid_codigo=rua.cid_codigo
		 		 WHERE usu_ativacao='S'
		 		   AND $where
		 		 ORDER BY usu_nome, 
		 		 	   usu_mae
		 		 LIMIT $limit";
	// Somente para paciente
	// correção para flórida
	die($sql);

	
} //


if($tipo == "usu_prontuario"){
	$id = "usu_codigo";
	$label = "usu_nome";
	$sql = "SELECT usu_codigo,
					   usu_nome,
		 			   usu_mae,
		 			   usu_pai,
		 			   to_char(usu_datanasc,'DD/MM/YYYY') AS usu_datanasc,
		 			   usu_prontuario,
		 			   COALESCE(area_desc::text,'Não definido') AS psf_area
		 		  FROM usuario AS usu
             LEFT JOIN psf
                    ON psf.dom_codigo=usu.dom_codigo
             LEFT JOIN area
                    ON area.area_codigo=psf.psf_area
		 		 WHERE usu_ativacao='S'
		 		   AND usu_prontuario='$term'
		 		 ORDER BY usu_nome, 
		 		 	   usu_mae
		 		 LIMIT $limit";
	
	// Somente para paciente
	// correção para flórida
	
}

if($tipo == "usu_cod_bio"){
	$id = "usu_codigo";
	$label = "usu_nome";
	$sql = "SELECT usu_codigo,
					   usu_nome,
		 			   usu_mae,
		 			   usu_pai,
		 			   to_char(usu_datanasc,'DD/MM/YYYY') AS usu_datanasc,
		 			   usu_prontuario,
		 			   COALESCE(area_desc::text,'Não definido') AS psf_area
		 		  FROM usuario AS usu
             LEFT JOIN psf
                    ON psf.dom_codigo=usu.dom_codigo
             LEFT JOIN area
                    ON area.area_codigo=psf.psf_area
		 		 WHERE usu_ativacao='S'
		 		   AND usu_codigo='$term'
		 		 ORDER BY usu_nome, 
		 		 	   usu_mae
		 		 LIMIT $limit";
	
// Somente para paciente
	// correção para flórida
	
}


if($tipo == "usuarios"){
	$id = "usr_codigo";
	$label = "usr_nome";
	$sql = "SELECT u.usr_codigo,
				       u.usr_nome,
				       usr_num_conselho
				  FROM usuarios AS u
				 WHERE u.usr_nome ilike '%$term%'
				 ORDER BY u.usr_nome
		 		 LIMIT $limit";
}


if($tipo == "procedimento"){
	$id = "proc_codigo";
	$label = "proc_nome";
	$sql = "SELECT proc_codigo,
				       proc_nome,
				       proc_bpa_tipo
				  FROM procedimento
				 WHERE retira_acentos(proc_nome) ilike retira_acentos('%$term%')
				 ORDER BY proc_nome
		 		 LIMIT $limit";
}


if($tipo == "medicamentos"){
	$id = "pro_codigo";
	$label = "pro_nome";
	$sql = "SELECT p.pro_codigo,
				       p.pro_nome
				  FROM produto AS p
				 WHERE p.pro_tipo IN('M','T')
				   AND p.pro_nome ilike '%$term%'
				   AND p.pro_situacao='A'
				 ORDER BY p.pro_nome
		 		 LIMIT $limit";
}

if($tipo == "medicamentos_com_saldo"){
	include_once COMUM.'library/php/funcoes.db.php';
	$set_codigo = getSetorByLogon();

	$id = "pro_codigo";
	$label = "pro_nome";
	$sql = "SELECT DISTINCT(p.pro_codigo),
				       p.pro_nome
				  FROM produto AS p
				  JOIN saldo AS s
				    ON s.set_codigo=$set_codigo
				   AND s.pro_codigo=p.pro_codigo
				   AND s.sal_qtde > 0
				 WHERE p.pro_tipo IN('M','T')
				   AND p.pro_nome ilike '%$term%'
				   AND p.pro_situacao='A'
				 ORDER BY p.pro_nome
		 		 LIMIT $limit";
}

if($tipo == "vacinas"){
	$id = "pro_codigo";
	$label = "pro_nome";
	$sql = "SELECT p.pro_codigo,
				       p.pro_nome
				  FROM produto AS p
				 WHERE p.gru_codigo=100002
				   AND p.pro_nome ilike '%$term%'
				 ORDER BY p.pro_nome
		 		 LIMIT $limit";
}

if($tipo == "especialidade"){
	$id = "esp_codigo";
	$label = "esp_nome";
	$sql = "SELECT e.esp_codigo,
				       e.esp_nome
				  FROM especialidade AS e
				 WHERE retira_acentos(e.esp_nome) ilike retira_acentos('%$term%')
				 ORDER BY e.esp_nome
		 		 LIMIT $limit";
}

// med_codigo que não seja medico ;)
if($tipo == "prestador"){
	$id = "med_codigo";
	$label = "med_nome";
	$sql = "SELECT med_codigo,
				       med_nome,
				       med_cnes
				  FROM medico
				 WHERE retira_acentos(med_nome) ilike retira_acentos('%$term%')
				   AND prestador_servico <> 'M'
				 ORDER BY med_nome
		 		 LIMIT $limit";

	//die($sql);
}

// med_codigo que seja medico ;)
if($tipo == "medico"){
	$id = "med_codigo";
	$label = "med_nome";
	$sql = "SELECT med_codigo,
				       med_nome,
				       med_cnes
				  FROM medico
				 WHERE retira_acentos(med_nome) ilike retira_acentos('%$term%')
				   AND prestador_servico = 'M'
				 ORDER BY med_nome
		 		 LIMIT $limit";

	//die($sql);
}

if($tipo == "cd10"){
	$id = "cd10_codigo";
	$label = "cd10_descricao";
	$sql = "SELECT cd10_codigo,
				       cd10_codigo_cid||' '||cd10_descricao as cd10_descricao
				  FROM cid10
				 WHERE retira_acentos(cd10_descricao) ilike retira_acentos('%$term%')
				    OR cd10_codigo_cid ilike '%$term%'
				 ORDER BY cd10_descricao
		 		 LIMIT $limit";
	//die($sql);
}
if($tipo == "domicilio"){
	$id = "dom_codigo";
	$label = "rua_nome";
	$sql = "SELECT d.dom_codigo,rua_nome,rua_cep,rua_bairro,dom_numero,usu_nome as usu_nome_resp
			  FROM domicilio d
			  JOIN rua r
			    ON r.rua_codigo = d.rua_codigo
			  LEFT JOIN usuario u
			    ON u.usu_codigo = d.usu_codigo_responsavel
			 WHERE retira_acentos(rua_nome) ilike retira_acentos('%$term%')
			    OR dom_numero::varchar ilike '%$term%'
				 ORDER BY rua_nome
		 		 LIMIT $limit";
	//die($sql);
}
if($tipo == "rua"){
	$id = "rua_codigo";
	$label = "rua_nome";
	$sql = "SELECT rua_codigo,rua_nome,rua_cep,rua_bairro,l.co_tipo_logradouro,ds_tipo_logradouro
			  FROM rua r
			  JOIN tb_ms_tipo_logradouro l
			  	ON l.co_tipo_logradouro = r.co_tipo_logradouro	
			 WHERE retira_acentos(rua_nome) ilike retira_acentos('%$term%')
			    OR retira_acentos(rua_cep) ilike retira_acentos('%$term%')
			    OR retira_acentos(rua_bairro) ilike retira_acentos('%$term%')
				 ORDER BY rua_nome
		 		 LIMIT $limit";
	//die($sql);
}
if($tipo == "tipo_logradouro"){
	$id = "co_tipo_logradouro";
	$label = "ds_tipo_logradouro";
	
	$sql = "SELECT co_tipo_logradouro,
				   ds_tipo_logradouro 
			  FROM tb_ms_tipo_logradouro
			 WHERE retira_acentos(ds_tipo_logradouro) ilike retira_acentos('%$term%')			    
				 ORDER BY ds_tipo_logradouro
		 		 LIMIT $limit";
	
}

if($tipo == "fornecedor"){
	$id = "for_codigo";
	$label = "for_nome";
	
	$sql = "SELECT for_codigo,
				   for_nome
			  FROM fornecedor
			 WHERE retira_acentos(for_nome) ilike retira_acentos('%$term%')			    
				 ORDER BY for_nome
		 		 LIMIT $limit";
	
}
if(isset($_GET['sql']))
die($sql);
$query = pg_query($sql);

// json
$out = array();
while($r = pg_fetch_assoc($query)){
	$dados = array();
	foreach($r as $key => $value){
		$dados[]= "\"$key\":\"$value\"";
	}
	$dados = "{".implode(",",$dados)."}";
	$out []= sprintf("{\"id\":\"%s\", \"label\":\"%s\", \"data\":%s}", $r[$id], trim($r[$label]), $dados);
}

if(!count($out)){
	$out []= sprintf("{\"id\":\"%s\", \"label\":\"%s\", \"data\":%s}", 0, "Nenhum dado encontrado", "\"\"");
}

@header('Content-Type: application/json; charset=ISO-8859-1');
echo "[".implode(",",$out)."]";
