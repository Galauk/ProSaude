<?php

ob_implicit_flush(true);
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE ); // & ~E_NOTICE 
ini_set("display_errors",1);
ini_set("ignore_repeated_errors",0);

// Consulfarma
//$db = pg_connect("host=localhost dbname=import user=postgres port=5433 password=123") or die(pg_last_error());
//$conexao = ibase_connect('localhost:C:\desenvolvimento\elotech\falci\import\saoMiguel\sigsaude.fdb',"SYSDBA","masterkey") or die("??".ibase_errmsg());

// Muriqui
$db = pg_connect("host=localhost dbname=paraiso user=postgres port=5433 password=123") or die(pg_last_error());
class ms{
	private $conn;
	public function __construct(){
		$this->conn = new COM("ADODB.Connection");
		$this->conn->Open("Provider=Microsoft.Jet.OLEDB.4.0;Data Source=C:/desenvolvimento/elotech/falci/import/paraiso/paraiso.mdb");		
		//$this->conn->Open("Provider=Microsoft.Jet.OLEDB.4.0;Data Source=C:/desenvolvimento/elotech/falci/import/labplus/labpuls.mdb");		
	}	
	
	public function query($sql){
		return $this->conn->Execute($sql);
	}
	
	public function __destruct(){
		$this->conn->Close();
	}

}

$ms = new ms();
function ms_query($sql){
	global $ms;
	return $ms->query($sql);
}

function ib_query($query){
	global $conexao;
	return ibase_query($conexao,$query);
}



// $cacheMun[ PK_ANTIGA ] = $cid_codigo
$cacheMun = array();
function cid_codigo($mun_codigo,$mun_nome, $mun_estado){
	global $cacheMun;
	
	//$mun_nome = str_replace("'", "''", $mun_nome);
	
	if(!$mun_codigo)
		return NULL;
		
	//$mun_nome = utf8_decode($mun_nome);
	
	if( !isset($cacheMun[ $mun_codigo ]) ){
		// procurar no banco:
		$sql = " SELECT cid_codigo
				   FROM cidade
				  WHERE lower(retira_acentos(cid_nome)) = lower(retira_acentos('$mun_nome'))
				    AND upper(uf_sigla) = upper('$mun_estado')";
		$query = pg_query($sql);
		
		// não encontrado:
		if(pg_num_rows($query) == 0){
			// adicionar em uma cidade temporária.
			$cacheMun[ $mun_codigo ] = 0;
			
		} else {		
			$r = pg_fetch_object($query);
			$cacheMun[ $mun_codigo ] = $r->cid_codigo;
		}
	}
		
	return $cacheMun[ $mun_codigo ];
	
	
	
}

$cacheDom = array();
function dom_codigo($rua_codigo, $compl, $numero, $telefone=NULL){
	global $cacheDom;
	
	$numero = (int) $numero;
	
	$key = md5($rua_codigo.$compl.$numero);
	
	if(!$rua_codigo)
		return NULL;
	
	if( !isset($cacheDom[ $key ]) ){
		// procurar no banco:
		$sql = " SELECT dom_codigo 
				   FROM domicilio
				  WHERE rua_codigo='$rua_codigo'
				    AND dom_complemento='$compl'
				    AND dom_numero='$numero'";
		$query = pg_query($sql);
		
		if($telefone && strlen($telefone) > 14){
			$telefone = preg_replace("/[^0-9]+/", "", $telefone);
		}
		
		// não encontrado:
		if(pg_num_rows($query) == 0){
			// insert
			$dom_codigo = nextVal('domicilio_seq');
			$insert = "INSERT INTO domicilio(dom_codigo, dom_data_cadastro, rua_codigo, dom_numero, dom_complemento, dom_telefone)
						             VALUES ($dom_codigo, CURRENT_DATE, $rua_codigo, '$numero', '$compl', '$telefone');";
			$query = pg_query($insert);
			$cacheDom[ $key ] = $dom_codigo;
			
		} else {		
			$r = pg_fetch_object($query);
			$cacheDom[ $key ] = $r->dom_codigo;
		}
	}
		
	return $cacheDom[ $key ];
	
}

$cacheRua = array();
function rua_codigo($cid_codigo,$nome, $co_tipo_logradouro, $cep){
	global $cacheRua;
	
	$key = md5($cid_codigo.$nome.$co_tipo_logradouro.$cep);
	
	if(!$cid_codigo)
		return NULL;
	
	if( !isset($cacheRua[ $key ]) ){
		// procurar no banco:
		$sql = " SELECT rua_codigo 
				   FROM rua
				  WHERE cid_codigo='$cid_codigo'
				    AND rua_nome='$nome'
				    AND rua_cep='$cep'";
		$query = pg_query($sql);
		
		// não encontrado:
		if(pg_num_rows($query) == 0){
			// insert
			$rua_codigo = nextVal('seq_rua_codigo');
			$insert = "INSERT INTO rua(rua_codigo, rua_nome, cid_codigo, co_tipo_logradouro, rua_cep)
    							VALUES ($rua_codigo, '$nome', $cid_codigo, '$co_tipo_logradouro', '$cep');";
			$query = pg_query($insert);
			$cacheRua[ $key ] = $rua_codigo;
			
		} else {		
			$r = pg_fetch_object($query);
			$cacheRua[ $key ] = $r->rua_codigo;
		}
	}
		
	return $cacheRua[ $key ];
	
}

$cacheTipoLog = array();
function co_tipo_logradouro($descricao,$sigla){
	global $cacheTipoLog;
	
	$key = md5($descricao.$sigla);
	
	if(!$descricao)
		return '081'; // rua
	
	if( !isset($cacheTipoLog[ $key ]) ){
		// procurar no banco:
		$sql = " SELECT co_tipo_logradouro 
			   FROM tb_ms_tipo_logradouro
			  WHERE ds_tipo_logradouro='$descricao'
			    AND ds_tipo_logradouro_abrev='$sigla'";
		$query = pg_query($sql);
		
		// não encontrado:
		if(pg_num_rows($query) == 0){
			// adicionar como "rua"
			$cacheTipoLog[ $key ] = '081'; // rua
			
		} else {		
			$r = pg_fetch_object($query);
			$cacheTipoLog[ $key ] = $r->co_tipo_logradouro;
		}
	}
		
	return $cacheTipoLog[ $key ];
	
}

$cacheSet = array();
function set_codigo($uni_codigo,$uni_desc){
	global $cacheSet;
	
	$key = $uni_codigo;
	
	if(!$uni_codigo){
		$uni_codigo = "492167";
		$uni_desc = "UNIDADE BASICA CENTRAL DE SAUDE";
	}
	
	if( !isset($cacheSet[ $key ]) ){
		// procurar no banco:
		$sql = "select set_codigo from setor where uni_codigo=$uni_codigo";
		$query = pg_query($sql);
		
		// não encontrado:
		if(pg_num_rows($query) == 0){
			$set_codigo = nextVal('seq_setor');
			$sql = "INSERT INTO setor(set_codigo,  set_nome,               set_estoque, uni_codigo,  set_farmacia, set_distribuidor, set_transferencia, ativo)
    				VALUES           ($set_codigo, 'Farmácia - $uni_desc', 'S',         $uni_codigo, 'S',          'S',              'S',               'S');";
			
			$query = pg_query($sql) or die($sql."<br />".pg_last_error());;
			$cacheSet[ $key ] = $set_codigo;
			
		} else {		
			$r = pg_fetch_object($query);
			$cacheSet[ $key ] = $r->set_codigo;
		}
	}
		
	return $cacheSet[ $key ];
	
}

$arrayLogin = array();
function loginUnico($nome){
	global $arrayLogin;
	$partes = explode(" ",strtolower($nome));
	$login = current($partes);
	
	while( in_array($login, $arrayLogin) ){
		$login .= substr(next($partes),0,1);
	}
	
	$arrayLogin [] = $login;
	return $login;
}

function movSaida($mov_data, $usu_codigo, $set_codigo, $usr_codigo, $med_codigo){
	if(!$usr_codigo)
		$usr_codigo=0;
	
	$mov_codigo = nextVal('seq_mov_codigo');
	$sql = "INSERT INTO movimento(
			            mov_codigo, mov_data, mov_tipo, for_codigo, usu_codigo, 
			            mov_observacao, set_saida, 
			            usr_codigo,  
			            mov_saida,
			            med_codigo_externo)
			    VALUES ($mov_codigo, '$mov_data', 'S', 5003, $usu_codigo, 'Importação de dados', $set_codigo, $usr_codigo, 'D', $med_codigo);";
	
	$query = pg_query($sql) or die($sql."<br />".pg_last_error());;
	return $mov_codigo;
}

function movEntrada($mov_data, $usu_codigo, $set_codigo, $usr_codigo){
	if(!$usr_codigo)
		$usr_codigo=0;
		
	$mov_codigo = nextVal('seq_mov_codigo');
	$sql = "INSERT INTO movimento(
            mov_codigo, mov_data, mov_tipo, for_codigo, usu_codigo,  
            mov_observacao, set_entrada,  
            usr_codigo,
            mov_entrada)
    VALUES ($mov_codigo, '$mov_data', 'E', 5003, $usu_codigo,'Importação de dados', $set_codigo,$usr_codigo,'O');";
	
	$query = pg_query($sql) or die($sql."<br />".pg_last_error());;
	return $mov_codigo;	
}

function iteMov($mov_codigo, $pro_codigo, $ite_lote, $ite_validade, $usr_codigo, $ite_qtde){
	if(!$usr_codigo)
		$usr_codigo=0;
		
	$ite_lote = substr($ite_lote, 0, 20);
		
	$sql = "INSERT INTO itens_movimento(mov_codigo, pro_codigo, ite_lote, ite_validade, 
							            usr_codigo, ite_status, ite_quantidade, 
							            ite_observacoes,  ite_dose, ite_dose_lote)
							    VALUES ($mov_codigo, $pro_codigo, '$ite_lote', ".($ite_validade?"'$ite_validade'":"NULL").", 
							            $usr_codigo, 'S', $ite_qtde, 'Importação de Dados', 1, 1);";
	
	$query = pg_query($sql) or die($sql."<br />".pg_last_error());;
}

function for_codigo($for_codigo, $for_nome, $for_endereco, $for_cep, $cid_codigo, $for_fone, $for_cnpj, $for_email, $for_homepage, $for_responsavel, $for_fax){
	$sql = "INSERT INTO fornecedor(for_codigo, for_nome, for_endereco, for_cep, cid_codigo, for_fone, 
            for_cnpj, for_email, 
            for_homepage, for_responsavel, for_fax, for_tipo)
    VALUES ($for_codigo, '$for_nome', '$for_endereco', '$for_cep', $cid_codigo, '$for_fone', 
            '$for_cnpj', '$for_email', 
            '$for_homepage', '$for_responsavel', '$for_fax', 'J');";
	
	pg_query($sql);
}

function for_codigo2($row){
	$sql = "INSERT INTO fornecedor(
            for_codigo, for_nome, for_endereco, for_cep, for_fone, 
            for_nome_fantasia, for_cnpj, for_insc_est, for_email, 
            for_homepage, for_cidade, for_uf, for_fax, for_tipo)
    VALUES ($row->for_codigo, '".substr($row->for_nome, 0 , 60)."', '".substr($row->for_endereco, 0,60)."', '$row->for_cep', '$row->for_fone', 
            '".substr($row->for_nome_fantasia, 0,60)."', '$row->for_cnpj', '$row->for_insc_est', '$row->for_email', 
            '$row->for_homepage', '".substr($row->for_cidade, 0,60)."', '$row->for_uf', '$row->for_fax', '$row->for_tipo');";
	
	pg_query($sql);	
}

$cacheMed = array();
function med_codigo($med_codigo, $med_nome, $med_crm, $med_cpf, $med_end_telefone, $med_end_celular, $med_end_telefone_res){
	global $cacheMed;
	
	if(!$med_codigo){
		return 0;
	}
	
	$med_crm = substr($med_crm, 0, 10);
	
	if(!in_array($med_codigo, $cacheMed)){
		// busca no banco
		$sql = "SELECT med_codigo FROM medico WHERE med_codigo=$med_codigo";
		$query = pg_query($sql);
		
		if(!pg_num_rows($query)){
				
			$sql = "INSERT INTO medico( med_codigo, med_crm, med_nome,   
							            med_cpf,  prestador_servico,   
							            med_end_telefone, med_end_celular, 
							            med_end_telefone_res, uf_codigo_crm)
							    VALUES ($med_codigo, '$med_crm', '$med_nome', '$med_cpf', 'N','$med_end_telefone', '$med_end_celular', '$med_end_telefone_res', '18');";
			
			pg_query($sql) or die($sql."<br />".pg_last_error());
				
		} 
		$cacheMed []= $med_codigo;
   }
   
   return $med_codigo;
}

function nextVal($seq){
	$query = pg_query("SELECT nextval('$seq');");
	$res = pg_fetch_array($query);
	return $res[0];		
}

function arrumaSequencia($seq,$table,$pk){
	$query = pg_query("SELECT setval('$seq',(SELECT COALESCE(MAX($pk),0)+1 FROM $table LIMIT 1),FALSE);");
	$res = pg_fetch_array($query);
	return $res[0];	
}

/**
 * Traz o 'nosso' pro_codigo de cada vacina 'deles' (consulfarma)
 * @param int $old ID da vacina no banco antigo
 * @return int pro_codigo
 */
function vac_codigo($old){
	$map = array(
		3 => 469,
		5 => 477,
		4 => 476,
		2 => 476,
		1 => 468,
		11 => 475,
		9 => 474,
		8 => 473,
		7 => 472,
		13 => 472,
		14 => 472,
		20 => 833,
		21 => 833,
		19 => 833,
		18 => 833,
		17 => 662,
		12 => 478,
		6 => 470,
		10 => 471,
		15 => 551,
		16 => 552
	);
	
	return $map[$old];
	
}

// muriki
function vac2_codigo($old){
	$map = array(
		3 => 469,
		5 => 477,
		4 => 476,
		2 => 476,
		1 => 468,
		11 => 475,
		9 => 474,
		8 => 473,
		7 => 472,
		13 => 472,
		14 => 472,
		20 => 833,
		21 => 833,
		19 => 833,
		18 => 833,
		17 => 662,
		12 => 478,
		6 => 470,
		10 => 471,
		15 => 551,
		16 => 552
	);
	
	return $map[$old];	
}

/**
 * Converte a string de dose para int
 * 2ª Dose: 2
 * 1º Reforço: 6
 * 2º Reforço: 6 // sobrepoe 
 * @param $strDose
 * @return int
 */
function vac_dose($strDose){
	list($int,$str) = explode(" ",$strDose);
	
	if($str == "Reforço") return 6;
	
	$int = substr($int,0,-1); // tira o ª ou º
	
	if($int < 6)
	  return $int;
	  
	return 6;
	
}
// muriki
function vac2_dose($strDose){
	list($int,$str) = explode(" ",$strDose);
	
	if($str == "Reforço") return 6;
	
	$str = str_replace("Dose ", "", $str);
	if($str == "unica") return 1;
	$int = (int) $str;
	
	if($int < 6)
	  return $int;
	  
	return 6;
	
}

function gru_codigo($gpr_codigo){
	$map = array(
		1 => 99482,
		2 => 99481,
		3 => 99482,
		4 => 0, // não tinha nenhem produto para comparar
		5 => 99481,
		6 => 1,
		7 => 99483,
		8 => 99480,
		9 => 99481,
		10 => 100001,
		11 => 100002
	);
	
	return $map[ $gpr_codigo ];
	
}

function sn($bool){
	return $bool?"S":"N";	
}

function umed_codigo($apr_codigo){
	$apr_codigo = trim($apr_codigo);
	$map = array(
		"BNG" => 50,
		"CPR" => 49,
		"CTR" => 51,
		"CX" => 2,
		"FR" => 15,
		"L" => 3,
		"UN" => 17,
		"AMP" => 16,
		"FR/A" => 61,
		"SA" => 66,
		"PAR" => 4,
		"PCTE" => 7,
		"150" => 6,
		"BL" => 5
	);
	
	return $map[ $apr_codigo ];
}

// muriqui
function umed_codigo2($un_frac){
	$un_frac = trim(strtolower($un_frac));
	
	$map = array(
		"ampola" => 16,
		"caixa" => 2,
		"capsula" => 62,
		"comp" => 49,
		"frasco" => 15,
		"gramas" => 3,
		"kg" => 4,
		"litro" => 3,
		"ml" => 2,
		"unidade" => 17
	);
	
	if(!in_array($un_frac, $map)){
		return 17;
	}
	
	return $map[ $un_frac ];
}

function vacina_usuario($usu_codigo, $pro_codigo, $dose, $data){
	
	$sql = "INSERT INTO vacina_usuario( usu_codigo, pro_codigo, vac_acao, vac_dose, vac_data,vac_unidade)
    VALUES ($usu_codigo, $pro_codigo, 'P', $dose, '$data',1);";
	
	$query = pg_query($sql);
	
}

function usu_codigo($tudo){
	
	foreach($tudo as $key=>$value){
		$$key = $value;
	}
	
	if(!isset($usu_codigo)){
		$usu_codigo = $usu_prontuario;
	}
	
	// inserir no postgres
	$sql = "INSERT INTO usuario(usu_codigo,
	                            usu_nome, 
	                            usu_observacao, 
	                            usu_sexo, 
	                            usu_datanasc, 
								cid_codigo_nasc, 
	                            usu_ocupacao, 
	                            usu_pis_pasep, 
	                            usu_cpf, 
	                            usu_cartao_sus, 
	                            usu_tipo_certidao, 
	                            usu_cert_cartorio, 
	                            usu_cert_livro, 
	                            usu_cert_termo, 
	                            usu_cert_emissao, 
	                            usu_rg, 
								usu_rg_compl, 
	                            usu_rg_emissor, 
	                            usu_rg_dt_emissao,
	                            usu_ctps, 
	                            usu_ctps_serie, 
	                            usu_ctps_dt_emissao, 
	                            usu_tit_eleitor, 
	                            usu_tit_eleitor_zona, 
	                            usu_tit_eleitor_secao, 
	                            usu_mae,
								usu_pai, 
	                            rac_codigo, 
	                            usu_end_nr, 
	                            usu_end_compl,
	                            usu_fone, 
	                            usu_prontuario, 
	                            usr_cad, 
	                            usr_alt,
	                            usr_cad_dt, 
	                            usr_alt_dt, 
	                            cd_nacionalidade, 
	                            dt_naturalizacao,  
	                            usu_estado_civil, 
	                            usu_conjuge, 
	                            usu_nis,
	                            uf_sigla_rg, 
	                            uf_sigla_ctps, 
	                            dom_codigo,
	                            usu_fator_rh,
	                            usu_tipo_sanguineo)
                        values ({$usu_codigo},
                                '".substr($usu_nome,0,60)."', 
                                ".($usu_observacao?"'".$usu_observacao."'":"null").",
                                ".($usu_sexo?"'".substr($usu_sexo,0,1)."'":"null").",
                                ".($usu_datanasc?"'".$usu_datanasc."'":"null").",       
                                ".($cid_codigo_nasc?"'".$cid_codigo_nasc."'":"null").",  
                                ".($usu_ocupacao?"'".$usu_ocupacao."'":"null").",
                                ".($usu_pis_pasep?"'".substr($usu_pis_pasep,0,20)."'":"null").",	
                                ".($usu_cpf?"'".substr($usu_cpf,0,15)."'":"null").",	  	
                                ".($usu_cartao_sus?"'".substr($usu_cartao_sus,0,30)."'":"null").",	  
                                ".($usu_tipo_certidao?"'".(int) $usu_tipo_certidao."'":"null").",
                                ".($usu_cert_cartorio?"'".substr($usu_cert_cartorio,0,60)."'":"null").",
                                ".($usu_cert_livro?"'".substr($usu_cert_livro,0,10)."'":"null").",		
                                ".($usu_cert_termo?"'".substr($usu_cert_termo,0,10)."'":"null").",		
                                ".($usu_cert_emissao?"'".$usu_cert_emissao."'":"null").",	
                                ".($usu_rg?"'".substr($usu_rg,0,15)."'":"null").",		         
                                ".($usu_rg_compl?"'".substr($usu_rg_compl,0,10)."'":"null").",	  
                                ".($usu_rg_emissor?"'".substr($usu_rg_emissor,0,30)."'":"null").",	
                                ".($usu_rg_dt_emissao?"'".$usu_rg_dt_emissao."'":"null").",	
                                ".($usu_ctps?"'".substr($usu_ctps,0,10)."'":"null").",	
                                ".($usu_ctps_serie?"'".substr($usu_ctps_serie,0,7)."'":"null").",	
                                ".($usu_ctps_dt_emissao?"'".$usu_ctps_dt_emissao."'":"null").",	
                                ".($usu_tit_eleitor?"'".substr($usu_tit_eleitor,0,14)."'":"null").",	
                                ".($usu_tit_eleitor_zona?"'".substr($usu_tit_eleitor_zona,0,4)."'":"null").",		  
                                ".($usu_tit_eleitor_secao?"'".substr($usu_tit_eleitor_secao,0,4)."'":"null").",		  
                                ".($usu_mae?"'".substr($usu_mae,0,60)."'":"null").",		  
                                ".($usu_pai?"'".substr($usu_pai,0,60)."'":"null").",		  
                                ".($rac_codigo?"'".substr($rac_codigo,0,2)."'":"null").",	 
                                ".($usu_end_nr?"'".substr($usu_end_nr,0,5)."'":"null").",	 
                                ".($usu_end_compl?"'".substr($usu_end_compl,0,20)."'":"null").",	
                                ".($usu_fone?"'".substr($usu_fone,0,15)."'":"null").",	
                                ".($usu_prontuario?"'".substr($usu_prontuario,0,15)."'":"null").",	
                                ".($usr_cad?"'".$usr_cad."'":"null").",	
                                ".($usr_cad?"'".$usr_cad."'":"null").",	
                                ".($usr_cad_dt?"'".$usr_cad_dt."'":"null").",		
                                ".($usr_cad_dt?"'".$usr_cad_dt."'":"null").",	
                                ".($cd_nacionalidade?"'".substr($cd_nacionalidade,0,3)."'":"null").",		
                                ".($dt_naturalizacao?"'".$dt_naturalizacao."'":"null").",		
                                ".($usu_estado_civil?"'".$usu_estado_civil."'":"null").",		
                                ".($usu_conjuge?"'".substr($usu_conjuge,0,60)."'":"null").",		
                                ".($usu_nis?"'".substr($usu_nis,0,11)."'":"null").",		
                                ".($uf_sigla_rg?"'".substr($uf_sigla_rg,0,2)."'":"null").",	
                                ".($uf_sigla_ctps?"'".substr($uf_sigla_ctps,0,2)."'":"null").",	
                                ".($dom_codigo?"'".$dom_codigo."'":"null").",		
                                ".($usu_fator_rh?"'".substr($usu_fator_rh,0,1)."'":"null").",	
                                ".($usu_tipo_sanguineo?"'".substr($usu_tipo_sanguineo,0,2)."'":"null").");";
	                            	                            
	                            pg_query($sql) or die("\n\n\nerro no id (antigo): {$usu_prontuario}\n\n".pg_last_error()."\n\n$sql");
	
	
}
	
function usr_codigo($tudo){
	
	foreach($tudo as $key=>$value){
		$$key = $value;
	}
	
	$sql = "INSERT INTO usuarios(usr_codigo, 
								 usr_login, 
								 usr_nome, 
								 usr_senha, 
								 usr_ativo, 
								 usr_tipo,  
								 usr_dtinsert, 
								 usr_alter, 
								 usr_dtalter, 
								 usr_tipo_medico,
								 usr_email,
								 usr_funcao)
                        VALUES ({$usr_codigo}, 
                                '".(loginUnico($usr_nome))."', 
                                ".($usr_nome?"'".substr($usr_nome,0,60)."'":"'Não informado'").", 
                                '$usr_senha', 
                                '".sn($usr_ativo)."', 
                                'U', 
                                ".($usr_dtinsert?"'".$usr_dtinsert."'":"NULL").", 
                                0, -- dilee
                                CURRENT_DATE, 
                                'C', 
                                ".($usr_email?"'".$usr_email."'":"NULL").", 
                                ".($usr_funcao?"'".$usr_funcao."'":"NULL")."
                                );";
	                            	                            
	pg_query($sql) or die("\n\n\nErro no ID (antigo): {$usr_codigo}\n\n".pg_last_error()."\n\n$sql");
}

function psf_codigo($psf_data_cadastro, $psf_area, $psf_micro_area, $dom_codigo=NULL){
	if(!$dom_codigo || (!$psf_area && !$psf_micro_area))
		return false;
	
	$sql = "SELECT psf_codigo FROM psf WHERE dom_codigo=$dom_codigo";
	$query = pg_query($sql);
	if(!pg_num_rows($query)){			
		$sql = "INSERT INTO psf(psf_data_cadastro, psf_area, psf_micro_area, psf_ativo, dom_codigo)
	    VALUES (".($psf_data_cadastro?"'".$psf_data_cadastro."'":"CURRENT_DATE").",".($psf_area?$psf_area:"NULL").",".($psf_micro_area?$psf_micro_area:"NULL").", 'S', $dom_codigo);";
		
		pg_query($sql) or die(pg_last_error()."\n".$sql);
	}
}

function pro_codigo($tudo){
	
	foreach($tudo as $key=>$value){
		$$key = $value;
	}
	
	$sql = "insert into produto ( pro_codigo,
								  gru_codigo,
								  pro_nome,
								  pro_barcode,
								  pro_custo, 
								  pro_embalagem,
								  pro_descricao_tecnica,
								  pro_observacao,
								  pro_dispensacao,
								  psico_codigo, 
								  umed_codigo, 
								  pro_situacao,
								  pro_validade,
								  pro_saida,
								  pro_entrada,
								  pro_tipo,
								  pro_fracionado)
            
                        VALUES ({$pro_codigo}, 
                                {$gru_codigo}, 
                                '{$pro_nome}', 
                                ".($pro_barcode?"'".substr($pro_barcode,0,15)."'":"NULL").", 
                                ".($pro_custo?"'".$pro_custo."'":"NULL").", 
                                ".($pro_embalagem?"'".$pro_embalagem."'":"NULL").", 
                                ".($pro_descricao_tecnica?"'".$pro_descricao_tecnica."'":"NULL").", 
                                ".($pro_observacao?"'".$pro_observacao."'":"NULL").",
                                '".sn($pro_dispensacao)."', 
                                ".($psico_codigo?"'".$psico_codigo."'":"NULL").", 
                                {$umed_codigo}, 
                                '".($pro_situacao?"A":"I")."', 
                                '".sn($pro_validade)."', 
                                'S',
                                'S',   
                                '$pro_tipo',                           
                                '$pro_fracionado');";
                                  	                            
	                            pg_query($sql) or die("\n\n\nErro no ID (antigo): {$pro_codigo}\n\n".pg_last_error()."\n\n$sql");
}

function pro_tipo($gru_codigo){
	switch ($gru_codigo) {
		case 99481:
			return "T"; // hospitalar
			break;
		case 99482:
			return "M"; // Medicamento
			break;
		
		default:
			return "D"; // Diversos
			break;
	}	
}

function pro_fracionado($gru_codigo){
	return sn($gru_codigo==100002); // vacina
}


function blobToStr($blob){
	$blob_data = ibase_blob_info($blob);
	$blob_hndl = ibase_blob_open($blob);
	return ibase_blob_get($blob_hndl, $blob_data[0]);
}