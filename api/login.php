<?php

include('includes.php');

$dados = parse();

// $sql = "SELECT usuarios.*, unidade.* FROM usuarios INNER JOIN unidade_usuarios ON usuarios.usr_codigo = unidade_usuarios.usr_codigo INNER JOIN unidade on unidade_usuarios.uni_codigo = unidade.uni_codigo WHERE usuarios.usr_login = '{$dados->login}' AND usuarios.usr_senha = '{$dados->senha}'";
$sql = "SELECT distinct(retira_acentos(esp.esp_nome)) esp_nome, usuarios.*, unidade.*, esp.esp_codigo FROM usuarios INNER JOIN unidade_usuarios ON usuarios.usr_codigo = unidade_usuarios.usr_codigo INNER JOIN unidade on unidade_usuarios.uni_codigo = unidade.uni_codigo INNER JOIN medico_especialidade med_esp ON med_esp.med_codigo = usuarios.usr_codigo INNER JOIN especialidade esp ON med_esp.esp_codigo = esp.esp_codigo WHERE usuarios.usr_login = '{$dados->login}' AND usuarios.usr_senha = '{$dados->senha}'";
pg_client_encoding("LATIN 1");
$result = pg_query($sql) or die(error_get_last()['message']);


// $resultado = pg_fetch_object($result);


if($result != NULL){
    while($row = pg_fetch_object($result)){
        // print_r($row);
        unset($row->usr_senha);
        $row->esp_nome = trim($row->esp_nome);
        $resultado[] = $row;
    }
    
    echo json_encode(
        array(
            "valid" => true,
            "body" => $resultado
        )
    );
    exit;
} else {
    echo json_encode(
        array(
            "valid" => false,
            "body" => "Usuário não encontrado"
        )
    );
    exit;
}
exit;