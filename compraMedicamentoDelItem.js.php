<?php
 include 'global.php';
  
  $sql = pg_query("delete from compra_produto_itens where compi_codigo = '$compi_codigo'");

  if($sql) {
  	echo "$comp_codigo|$compi_codigo";
  } else {
  	echo "0";
  }
 
 
?>