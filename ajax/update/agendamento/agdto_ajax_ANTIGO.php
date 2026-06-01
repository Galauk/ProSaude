<?php

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
  
  $med_codigo   = $_GET['cod_medico'];
  $grm_qtd      = $_GET['grm_qtd'];
  $esp_codigo   = $_GET['cod_esp'];
  $grm_periodo  = $_GET['grm_periodo'];
  $grm_codigo   = $_GET['grm_codigo'];
  $periodo   = $_GET['periodo'];
  $id_login = $_GET['id_login']; 

          $usu = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$id_login'"));
          $sql = "update grade_mensal set " .
                 "med_codigo='$med_codigo', " .
                 "grm_qtde='$grm_qtde'," .
                 "esp_codigo='$esp_codigo', " .
                 "grm_periodo='$grm_periodo'," .
                 "usr_login_alt= '$usu[usr_nome] " . date("d/m/Y H:i:s") . "' " .
                 "where grm_codigo='$grm_codigo'";  
        $exec_sql = pg_query($sql);
    reglog($id_login,"Atualizando Quantidade do Agente Cod.: $agt_codigo Qtde: $grm_qtde");

        $sql_x = "select usr_login_alt from grade_mensal where esp_codigo = '$esp_codigo' 
                  and med_codigo = '$med_codigo' and 
                  --age_item = '$agt_item' and 
                  grm_periodo='$grm_periodo' 
                  and  grm_codigo='$grm_codigo'";
        $exec_sql_x = pg_query($sql_x); 

        while ($r = pg_fetch_array($exec_sql_x)){
         
            echo $r['usr_login_alt']; 
        }
 
?>
