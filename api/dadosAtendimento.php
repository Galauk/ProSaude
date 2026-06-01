<?

include('includes.php');

$dados = parse();

$sql = "SELECT * FROM usuarios INNER JOIN unidade_usuarios ON usuarios.usr_codigo = unidade_usuarios.usr_codigo INNER JOIN unidade on unidade_usuarios.uni_codigo = unidade.uni_codigo WHERE usuarios.usr_login = '{$dados->login}' AND usuarios.usr_senha = '{$dados->senha}'";
pg_client_encoding("LATIN 1");
$result = pg_query($sql) or die(error_get_last()['message']);


$resultado = pg_fetch_object($result);


if($resultado != NULL){
    unset($resultado->usr_senha);
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