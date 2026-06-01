<?php

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
  
  $gex_qtd      = $_GET['gex_qtd'];
  $gex_codigo      = $_GET['gex_codigo'];
  $proc_codigo   = $_GET['proc_codigo'];
            
   $query = pg_query("select *from grade_exame where proc_codigo = '$proc_codigo'");
    if(pg_num_rows($query)=="0") {
       $sql = pg_query("insert into grade_exame (proc_codigo,gex_status,gex_qtde) values ('$proc_codigo','S','$gex_qtde')");
    } else {
       $sql = pg_query("update grade_exame set gex_qtde = '$gex_qtde' where gex_codigo = '$gex_codigo'");
    }
        
	echo "Atualizado"; 
?>
