<?php

session_write_close();

include_once("db.inc.painel.php");

header("charset:UTF8");

function buscar($uni_codigo) {
    $sql = "SELECT DISTINCT cha.age_codigo, cha.cha_status, cha.cha_codigo, retira_acentos(age.age_paciente) as age_paciente, retira_acentos(set.set_nome) as set_nome, (CASE WHEN pc_clas_risco=1 THEN 'red' WHEN pc_clas_risco=2 THEN 'GoldenRod' WHEN pc_clas_risco=3 THEN 'yellow' WHEN pc_clas_risco=4 THEN 'green' WHEN pc_clas_risco=5 THEN 'blue' END) AS cor FROM chamada AS cha INNER JOIN agendamento AS age ON age.age_codigo=cha.age_codigo INNER JOIN logon AS log ON log.id_login=cha.usr_codigo INNER JOIN setor AS set ON set.set_codigo=log.cod_setor LEFT JOIN pre_consulta AS pre ON pre.age_codigo=age.age_codigo WHERE (age.uni_codigo = '".$uni_codigo."') ORDER BY cha_status ASC, cha.cha_codigo DESC LIMIT 4";
    //die($sql);
    pg_client_encoding("LATIN 1");
    $result = pg_query($sql) or die(error_get_last());
    
    $dados = [];

    while($ln = pg_fetch_object($result)){
        $dados[] = $ln;
    }
    
    return $dados;
}

if($_GET['uni_codigo']){
    $cod = $_GET['uni_codigo'];

	$retorno = buscar($cod);

    echo json_encode($retorno);
}