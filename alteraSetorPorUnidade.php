<?php
//error_reporting(E_ALL);
session_start();
require_once $_SESSION[root] . $_SESSION[comum] . "library/php/db.inc.php";
require_once $_SESSION[root] . $_SESSION[comum] . "library/php/funcoes.inc.php";

//echo $usr_codigo;

$sqlUp = "UPDATE logon SET uni_codigo = $uni_codigo where id_login = $usr_codigo";
$exec_sqlUp = pg_query($sqlUp);
$sqlUniS = "SELECT 
                uni_codigo,
                uni_desc,
                uni_cnpj,
                uni_codigo_ibge,
                uni_endereco,
                uni_numero,
                cnes_sigestgest,
                uni_bairro,
                uni_tipo 
            FROM unidade 
            WHERE uni_codigo = {$uni_codigo}";
$exec_sqlUniS = pg_query($sqlUniS);
$res_sqlUniS = pg_fetch_array($exec_sqlUniS);

$_SESSION['logon']['usr']->uni_codigo = $res_sqlUniS['uni_codigo'];
$_SESSION['logon']['usr']->uni_desc = $res_sqlUniS['uni_desc'];
$_SESSION['logon']['usr']->uni_cnpj = $res_sqlUniS['uni_cnpj'];
$_SESSION['logon']['usr']->uni_codigo_ibge = $res_sqlUniS['uni_codigo_ibge'];
$_SESSION['logon']['usr']->uni_endereco = $res_sqlUniS['uni_endereco'];
$_SESSION['logon']['usr']->uni_numero = $res_sqlUniS['uni_numero'];
$_SESSION['logon']['usr']->cnes_sigestgest = $res_sqlUniS['cnes_sigestgest'];
$_SESSION['logon']['usr']->uni_bairro = $res_sqlUniS['uni_bairro'];
$_SESSION['logon']['usr']->uni_tipo = $res_sqlUniS['uni_tipo'];
//die("asdfa_teste");
$_SESSION['uni_codigo'] = $uni_codigo;

$sql = "SELECT 
            set.set_codigo, 
            set.set_nome 
        FROM setor set
                INNER JOIN usuarios_setores uset ON set.set_codigo=uset.set_codigo
                INNER JOIN usuarios usr ON uset.usr_codigo=usr.usr_codigo
                INNER JOIN  unidade uni ON set.uni_codigo=uni.uni_codigo 
        WHERE (uni.uni_codigo = $uni_codigo)
        AND (usr.usr_codigo = $usr_codigo)";

$exec_sql = pg_query($sql) or die(pg_last_error());

$exec_sql2 = pg_query($sql) or die(pg_last_error());

$option = "";

// echo pg_num_rows($exec_sql) >= 1;
//         die(var_dump(pg_num_rows($exec_sql)));

if (pg_num_rows($exec_sql) >= 1) {
    while ($row_dados = pg_fetch_array($exec_sql)) {
        if ($set_codigo_logado == $row_dados["set_codigo"]) {
            $option .= "<option value='" . $row_dados["set_codigo"] . "' selected>" . $row_dados["set_nome"] . "</option>";
        } else {
            $option .= "<option value='" . $row_dados["set_codigo"] . "'>" . $row_dados["set_nome"] . "</option>";
        }
    }
    $row = pg_fetch_array($exec_sql2);
    $sqlUpS = "UPDATE logon SET cod_setor = {$row[0]} where id_login = $usr_codigo";

    
    $exec_sqlUps = pg_query($sqlUpS);
//            echo $row_dados2;die;
} else {
    $sqlUpS = "UPDATE logon SET cod_setor = 0 where id_login = $usr_codigo";
    $exec_sqlUps = pg_query($sqlUpS);
}

echo $option;

?>