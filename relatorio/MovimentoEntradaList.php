<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$data_inicial       = $_GET[dt_inicial];
$data_final         = $_GET[dt_final];
$codigo_fornecedor  = $_GET[codigo_fornecedor];
$codigo_produto     = $_GET[pro_codigo];
$codigo_grupo       = $_GET[codigo_grupo];

//$sql1   = "select distinct(mov_codigo) as mov_codigo,mov_data from movimento";
       
$sql2 = "select sp.pro_nome as pro_nome, im.ite_quantidade as qtde from itens_movimento as im 
            inner join produto as sp on sp.pro_codigo=im.pro_codigo";
            
            



/*
fornecedor  = f
produto     = p
grupo       = g
###############
f   p   g
0   0   0   todos
0   0   1   apenas grupo
0   1   0   apenas produto
0   1   1   grupo e produto
1   0   0   apenas fornecedor
1   0   1   fornecedor e grupo
1   1   0   fornecedor e produto
1   1   1   fornecedor produto e grupo
*/

$resp1 = 0;
$resp2 = 0;
    echo "<table><tr>
                <td>Produto</td>
                <td>Qtde</td>
                </tr>";

if ($codigo_fornecedor==-1 && $codigo_grupo==-1 && $codigo_produto==-1){ //todos
    
    $sql1   = "select distinct(mov_codigo) as mov_codigo,mov_data from movimento    
                where mov_data between to_char('$data_inicial'::date,'yyyy-mm-dd') and to_char('$data_final'::date,'yyyy-mm-dd')
                group by mov_data,mov_codigo";

    $sql2.=" where mov_codigo = ";
    $query1 = pg_query($sql1);

//    echo pg_num_rows($query1);
    while($resp1=pg_fetch_array($query1)){
        $sql2.=$resp1[mov_codigo];
        $query2 = pg_query($sql2);
        if (pg_num_rows($query2)>0)
        {
            while($resp2=pg_fetch_array($query2))
            {
                echo "<tr><td>$resp2[pro_nome]</td><td>$resp2[qtde]</td><td>$resp1[mov_codigo]</td></tr>";
            }
        }
    }
        echo "</table>";     
}

?>