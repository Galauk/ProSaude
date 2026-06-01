<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

function func_PrMed($procodigo, $codsetor, $dta) {

     $PrMed=pg_fetch_array(pg_query("select prm_custo
                                       from precomedio
                                      where pro_codigo=$procodigo
                                        and set_codigo=$codsetor
                                        and prm_data<='$dta'
                                   order by prm_data desc limit 1"));
     return $PrMed[prm_custo];
                                                                                                                             }


//  $sql="select sum(func_PrMed(pro_codigo,codsetor,mov_data)*ite_quantidade) from v_movimentacao where sinal='-'";
  $sql="select pro_codigo,codsetor,mov_data,ite_quantidade from v_movimentacao where sinal='-'";
  $query=pg_query($sql);
  while($v_mov=pg_fetch_array($query)) {
        $a=func_PrMed($v_mov[pro_codigo],$v_mov[codsetor],$v_mov[mov_data])*$v_mov[ite_quantidade]; 
        if ($a > 0) { 
         echo "<br>Codigo=$v_mov[pro_codigo]...
                    Setor=$v_mov[codsetor]...
                     Data=$v_mov[mov_data]...
                      Qtd=$v_mov[ite_quantidade] / ";
         echo $a; 
        }
        $SumPrMed += func_PrMed($v_mov[pro_codigo],$v_mov[codsetor],$v_mov[mov_data])*$v_mov[ite_quantidade];
  }
  vSQL($sql,"1");
  echo "TOTAL DO MOV= $SumPrMed";
  exit();

?> 
