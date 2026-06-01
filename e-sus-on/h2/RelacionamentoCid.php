<?php
    namespace esus\banco_endereco;
    include "Bd.php"; 
    define(CONN, $connection);
    define(CONN_PG, $connectionPg);
    ini_set('display_errors', true);
    
    $bancoCid = new BancoCid();
    $bancoCid->converteRelacionamento();
    class BancoCid {
        public function converteRelacionamento(){
            $sqlCiapCid = "select * from rl_ciap_cid10";
            $queryCiapCid = pg_query(CONN,$sqlCiapCid);
            while($reg = pg_fetch_array($queryCiapCid)){
                $sqlCidH2 = "select * from tb_cid10 where co_cid10 = $reg[co_cid10]";
                $queryCidh2 = pg_query(CONN,$sqlCidH2) or die("ab");
                $cidh2 = pg_fetch_array($queryCidh2);
                $sqlCidPost = "select * from cid10 where cd10_codigo_cid = '$cidh2[nu_cid10]'";
                $queryCidPost = pg_query(CONN_PG,$sqlCidPost);
                $cidPost = pg_fetch_array($queryCidPost);
                
                $insert = "INSERT INTO rl_ciap_cid10 (CO_CIAP,CO_CID10)VALUES($reg[co_ciap],'$cidPost[cd10_codigo]')";
                $query = pg_query(CONN_PG,$insert);
            }
            echo "sucesso";
        }
    }

?>
