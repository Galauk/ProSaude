<?php
$debug = 0;
//die(var_dump("here"));
session_start();
set_time_limit(0);
include_once $_SESSION[root] . $_SESSION[comum] . "library/php/db.inc.php";
require_once 'functions.php';

//Pega tabelas que serão alteradas após a importação do sigtap
$sql = "SELECT relname as tableName
        FROM pg_class
        WHERE relname ~ '^(tb_|rl_)' AND 
              relname !~ '^(tb_ms_)' AND 
              relkind = 'r'
        ORDER BY tableName DESC";
$query = pg_query($sql);

$countQuery = array("u" => 0, "i" => 0, "u2" => 0, "f" => 0, "t" => array("u" => 0, "i" => 0, "u2" => 0, "f" => 0));

debug(pg_num_rows($query) . " tabelas selecionadas para atualiza&ccedil;&atilde;o.");

while ($r = pg_fetch_array($query)) { // para cada tabela do banco (que casou com a ER)
    $tableName = $r['tablename'];

    debug("Atualizando tabela '$tableName'", 1);

    // arquivo com dados existe?
    if (file_exists("tmp/" . $tableName . ".txt")) {

        debug("Arquivo de dados encontrado!", 1);

        // ler os dados e o layout
        $dados = file("tmp/" . $tableName . ".txt");
        $layout = file("tmp/" . $tableName . "_layout.txt");
        array_shift($layout); // exclui o cabe�alho do layout
        $meta = getMeta($layout);
        //echo "<pre>".print_r($meta,1);
        //exit;
        $itens = count($dados); // para cada linha do arquivo de dados
        for ($x = 0; $x < $itens; $x++) {
            $sqlUpdate = array();
            $sqlInsert = array(); 
            $metaStr = array();

            $pkDupla = (bool) preg_match("/(rl_|tb_sub_)/", $tableName);
            // essas 3 tem uma 'tabela espelho'     , essas salvam hist�rico (junto com tb_procedimento)
            if (in_array($tableName, array("tb_procedimento", "tb_cid", "tb_ocupacao", "rl_procedimento_cid", "rl_procedimento_ocupacao"))) {
                //echo $dados[$x];
                $tableName($sqlInsert, $sqlUpdate, $dados[$x], $meta);
                //echo $sqlInsert;
                //exit();
            } else {
                foreach ($meta as $key => $data) { // para cada coluna do layout
                    $col = $data['coluna'];

                    // gambi:
                    if ($col == "tp_complexidade") {
                        $col = "tp_complexibilidade";
                    }

                    $metaStr [] = $col;

                    $col2 = substr($dados[$x], $data['inicio'] - 1, $data['tamanho']);
                    $col2 = str_replace("'", "", $col2);

                    $sqlInsert [] = ($col=="co_registro")?'0':$col;
                    //echo "<pre>".print_r($sqlInsert)."<br/>";					
                    if (!$key || ($key === 1 && $pkDupla)) // n�o � preciso atualizar a PK
                        continue;

                    $sqlUpdate [] = "$col='{".$col2."}'";
                }
//echo '<pre>'.print_r($sqlUpdate);
                /* IMPORTANTE: regra para update: o primeiro campo sempre � PK, 
                 * exceto nas tabelas rl_* e tb_sub_grupo, onde a PK � feita pelas 
                 * duas primeiras colunas */

                $where = sprintf("WHERE %s='%s'", trim($meta[0]['coluna']), substr($dados[$x], $meta[0]['inicio'] - 1, $meta[0]['tamanho']));

                if ($pkDupla) {
                    $where .= sprintf(" AND %s='%s'", $meta[1]['coluna'], substr($dados[$x], $meta[1]['inicio'] - 1, $meta[1]['tamanho']));
                }
                $sqlInsert = str_replace("'", "", $sqlInsert);
                //$sqlInsert = trim($sqlInsert);
                if(($tableName =="rl_procedimento_renases" OR $tableName=="rl_procedimento_regra_cond")) {
                   $sqlInsert = "INSERT INTO $tableName (" . implode(", ", $metaStr) . ") VALUES ('" . implode("', '", $sqlInsert) . "');";
                } else {
                   $sqlUpdate = "UPDATE $tableName SET " . implode(", ", $sqlUpdate) . " " . $where . ";";
                   $sqlInsert = "INSERT INTO $tableName (" . implode(", ", $metaStr) . ") VALUES ('" . implode("', '", $sqlInsert) . "');";                    
                }
            }
           
            // tenta update:
            //die($sqlUpdate);
            if(!empty($sqlUpdate)) {
              $updateQuery = pg_query($sqlUpdate) or die("<pre>$sqlInsert\n$sqlUpdate\n" . pg_last_error());
            }
            $affected = pg_affected_rows($updateQuery);
            //echo $affected."aa<br/>";
            if ($affected === 0) {
                 //echo $sqlInsert."<br/>";
                $insertQuery = pg_query($sqlInsert) or die("<pre>$sqlInsert\n$sqlUpdate\n" . pg_last_error());
                if (!pg_affected_rows($insertQuery)) {
                    debug("UPDATE AND INSERT FAIL", 3);
                    debug($sqlInsert, 3);
                    $countQuery["f"] ++;
                } else {
                    $countQuery["i"] ++;
                    debug($sqlInsert, 2);
                }
            } elseif ($affected > 0) {
                // UPDATE afetou mais de um registro, poss�vel falha
                $countQuery["u2"] ++;
                debug("UPDATE afetou $affected registros: $sqlUpdate", 3);
                debug($sqlUpdate, 3);
            } else {
                $countQuery["u"] ++;
                debug($sqlUpdate, 2);
            }

            # descomente para teste em pequena escala
            //if($x==1) $x=$itens;
        }
    } else {
        debug($tableName . "N&atilde;o possui arquivo com dados", 1);
    }
}

// limpa os arquivos da pasta tmp

apagar();

$_SESSION['susUpdate']['countQuery'] = $countQuery;
$_SESSION['susUpdate']['cod'] = 2;
$_SESSION['susUpdate']['dhFim'] = time();

$selectProcedimento = "SELECT proc_nome,proc_codigo,proc_codigo_sus FROM procedimento";
$queryProcedimento = pg_query($selectProcedimento);
while ($regProcedimento = pg_fetch_array($queryProcedimento)) {
    $sqlRlRegi = "SELECT co_registro,
						 co_procedimento
					FROM rl_procedimento_registro 
				   WHERE co_registro IN (1,2)
				   	 AND co_procedimento = '$regProcedimento[proc_codigo_sus]'
				     order by co_procedimento";
    $querySqlRegi = pg_query($sqlRlRegi) or die(pg_last_error());
    while ($regRegi = pg_fetch_array($querySqlRegi)) {
       // echo $regRegi[co_registro]."<br/>";die("Fuck");
        if ($regRegi[co_registro] == 2) {
            $updateProc = "UPDATE procedimento SET proc_bpa_tipo = 'I' WHERE proc_codigo_sus = '".trim($regProcedimento[proc_codigo_sus])."'";
            $queryUpdate = pg_query($updateProc) or die(pg_last_error());
        }
    }
}
header("location: mensagem.php?id_login=$id_login");

