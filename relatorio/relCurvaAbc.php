<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

  $dini = $_REQUEST['data_ini'];
  $dfim = $_REQUEST['data_fim'];
  $uni = $_REQUEST['uni_codigo'];

cabecario_rel('Relatorio de Curva ABC de Consumo',$dini,$dfim,null);

$sql = pg_query("select pro_nome,sum(ite_quantidade) as qtd,ite_vlrunit as total 
from movimento as mov 
join itens_movimento as ite on mov.mov_codigo=ite.mov_codigo 
join produto as prod on prod.pro_codigo = ite.pro_codigo  
where mov_data >='".$dini."' 
and mov_data <= '".$dfim."' 
and mov_saida = 'D' and mov_tipo = 'S'
group by pro_nome,ite_vlrunit order by 2 desc") or die(pg_last_error());

$tot = pg_fetch_array(pg_query("select sum(ite_quantidade) as total from movimento as mov join itens_movimento as ite on mov.mov_codigo=ite.mov_codigo join produto as prod on prod.pro_codigo = ite.pro_codigo where mov_data >='01/07/2018' and mov_data <= '30/11/2018' and mov_saida = 'D' and mov_tipo = 'S'"));
$total = $tot[total];
$total_reg = pg_num_rows($sql);
$calc_curva_a = $total_reg - (($total_reg * 20) / 100);
$calc_curva_b = $total_reg - (($total_reg * 30) / 100);
$calc_curva_c = $total_reg - (($total_reg * 50) / 100);
$curva_a = $total_reg-$calc_curva_a;
$curva_b = $total_reg-$calc_curva_b;
$curva_c = $total_reg-$calc_curva_c;

echo "<table width=100% cellspacing=5 cellpadding=5 border=1>
<tr>
   <td>Produto</td>
   <td>Quantidade</td>
   <td>Total R$</td>
   <td>Total %</td>
   <td>Acumulado %</td>
   <td>Curva</td>
</tr>";
$i=0;
while($rr=pg_fetch_array($sql)) {
  $porcent = ($rr[qtd]*100)/$total;
  $por = number_format($porcent, 2, ',', '.');
  $acumulado += $porcent;
  $acu = number_format($acumulado, 2, ',', '.');
  $i++;
  $valor = number_format($rr[total]*$rr[qtd], 2, ',', '.');
  if($i<=$curva_a) { 
      echo "<tr>
         <td>$rr[pro_nome]</td>
         <td>".number_format($rr[qtd], 0, ',', '.')."</td>
         <td>$valor</td>
         <td>".$por."</td>
         <td>".$acu."</td>
         <td align=center><font color=orange><b>A</b></font></td>
      </tr>";
  } 
  if(($i>=$curva_a and $i<=$curva_b)) { 
      echo "<tr>
         <td>$rr[pro_nome]</td>
         <td>".number_format($rr[qtd], 0, ',', '.')."</td>
         <td>$valor</td>
         <td>".$por."</td>
         <td>".$acu."</td>
         <td align=center><font color=red><b>B</b></font></td>
      </tr>";
  } 
  if(($i>=$curva_b and $i<=$curva_c)) { 
      echo "<tr>
         <td>$rr[pro_nome]</td>
         <td>".number_format($rr[qtd], 0, ',', '.')."</td>
         <td>$valor</td>
         <td>".$por."</td>
         <td>".$acu."</td>
         <td align=center><font color=grey><b>C</b></font></td>
      </tr>";
  } 



}
echo "</table>";
?>


