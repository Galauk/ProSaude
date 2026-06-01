<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// echo time();

include('includes.php');
ini_set("memory_limit", "1024M");
session_write_close();

ini_set('error_log', 'error_log');


function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ","a A e E i I o O u U n N"), $string);
}

function convert_from_latin1_to_utf8_recursively($dat){
    if (is_string($dat)) {
        return utf8_encode($dat);
    } else if (is_array($dat)) {
        $ret = [];
        foreach ($dat as $i => $d) {
            $ret[ $i ] = convert_from_latin1_to_utf8_recursively($d);
        }

        return $ret;
    } else if (is_object($dat)) {
        foreach ($dat as $i => $d) {
            $dat->$i = convert_from_latin1_to_utf8_recursively($d);
        }

        return $dat;
    } else {
        return $dat;
    }
}


if($_SERVER['REQUEST_METHOD'] == "GET"){
    $sql = "SELECT distinct(retira_acentos(esp.esp_nome)) esp_nome, usuarios.usr_codigo, retira_acentos(usuarios.usr_nome) usr_nome, usuarios.usr_login, usuarios.usr_senha, usuarios.usr_ativo, retira_acentos(unidade.uni_desc) uni_desc, unidade.uni_codigo, esp.esp_codigo FROM usuarios INNER JOIN unidade_usuarios ON usuarios.usr_codigo = unidade_usuarios.usr_codigo INNER JOIN unidade on unidade_usuarios.uni_codigo = unidade.uni_codigo INNER JOIN medico_especialidade med_esp ON med_esp.med_codigo = usuarios.usr_codigo INNER JOIN especialidade esp ON med_esp.esp_codigo = esp.esp_codigo";
    $sqlEnde = "SELECT distinct rua.rua_codigo, logr.ds_tipo_logradouro logradouro, rua.rua_nome rua, bairro.bai_nome bairro, rua.rua_cep cep FROM rua INNER JOIN tb_ms_tipo_logradouro logr ON rua.co_tipo_logradouro = logr.co_tipo_logradouro INNER JOIN bairro ON rua.bai_codigo = bairro.bai_codigo group by rua, rua_codigo, logradouro, bairro, cep;";
    $sqlUsu = "SELECT distinct usu_cartao_sus, usu_nome, usu_mae, usu_pai, usu_sexo, usu_datanasc, cd_nacionalidade, usu_rg, usu_cpf, rac_codigo, usu_freq_escolar as usu_frequencia_escolar, usu_sit_rua, usu_deficiencia, usr_codigo FROM usuario group by  usu_cartao_sus, usu_nome, usu_mae, usu_pai, usu_sexo, usu_datanasc, cd_nacionalidade, usu_rg, usu_cpf, rac_codigo, usu_frequencia_escolar, usu_sit_rua, usu_deficiencia, usr_codigo";

    //pg_client_encoding("Latin 1");
    $result = pg_query($sql) or die(error_get_last()['message']);
    $ende = pg_query($sqlEnde) or die(error_get_last()['message']);
    $usu = pg_query($sqlUsu) or die(error_get_last()['message']);
    
    $resultado = [];
    // echo "<pre>";
    while($row = pg_fetch_object($result)){
        // print_r($row);
        $row->esp_nome = trim($row->esp_nome);
        $row->esp_nome = tirarAcentos($row->esp_nome);

        $row->usr_nome = tirarAcentos($row->usr_nome);
        $row->uni_desc = tirarAcentos($row->uni_desc);

        $ln = convert_from_latin1_to_utf8_recursively($row);
        
        $resultado['usuarios'][] = $ln;
    }
    
    while($ln = pg_fetch_object($ende)){
        // print_r($row);
        $ln->rua = tirarAcentos($ln->rua);
        $ln->rua = trim($ln->rua);
        
        $ln->bairro = tirarAcentos($ln->bairro);
        $ln->bairro = trim($ln->bairro);
        
        $ln->cep = trim($ln->cep);

        $ln = convert_from_latin1_to_utf8_recursively($ln);
        
        $resultado['enderecos'][] = $ln;
    }

    while($l = pg_fetch_object($usu)){
        // print_r($row);
        $l->usu_nome = tirarAcentos($l->usu_nome);
        $l->usu_nome = trim($l->usu_nome);
        
        $l = convert_from_latin1_to_utf8_recursively($l);
        
        $resultado['usuario'][] = $l;
    }    

    if(json_last_error()){
        header("Content-type: json");
        
        echo json_encode(json_last_error_msg(), JSON_PRETTY_PRINT);
        exit;
    } else {
        $size = strlen(json_encode($resultado, JSON_PRETTY_PRINT));
        header("Content-type: json");
        header("Content-length: $size");
        echo json_encode($resultado, JSON_PRETTY_PRINT);
        
        exit;
    }
}

/*
if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    $dados = parse();
    
    echo json_encode(
        array(
            "dados" => $dados,
            "chave" => md5(serialize($dados))
        )
    );
        
    exit;
}*/