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

$i=0;
$arr_a = array();
$arr_b = array();
$arr_c = array();
$prod = array();
while($rr=pg_fetch_array($sql)) {
      $porcent = ($rr[qtd]*100)/$total;
      $por = number_format($porcent, 2, ',', '.');
      $acumulado += $porcent;
      $acu = number_format($acumulado, 2, '.', '.');
      $valor = number_format($rr[total]*$rr[qtd], 2, ',', '.');
      
     $i++;
$p = explode(" ",$rr[pro_nome]);
  if($i<=$curva_a) { 
    array_push($arr_a, $acu);
    array_push($prod, $p[0]." - Curva A");
  } 
  if(($i>=$curva_a and $i<=$curva_b)) { 
    //array_push($arr_a, $acu);
    array_push($arr_a, $acu);
    array_push($prod, $p[0]." - Curva B");
  } 
  if(($i>=$curva_b and $i<=$curva_c)) { 
    array_push($arr_a, $acu);
    array_push($prod, $p[0]." - Curva C");
  } 
}
?>

<!doctype html>
<html>

<head>
  <title>Line Chart</title>
  <script src="../chartjs/samples/charts.bundle.js"></script>
  <script src="../chartjs/samples/utils.js"></script>
  <style>
    canvas {
      -moz-user-select: none;
      -webkit-user-select: none;
      -ms-user-select: none;
    }
  </style>
</head>

<body>
  <div style="width:100%;vertical-align: center">
    <canvas id="canvas"></canvas>
  </div>
 <script>
    var chartData = {
      labels: <?php echo json_encode($prod); ?>,
      datasets: [{
        type: 'line',
        label: 'Curva ABC',
        steppedLine: false,
        borderColor: window.chartColors.blue,
        borderWidth: 2,
        fill: true,
        data: <?php echo json_encode($arr_a); ?>
      }]

    };
    window.onload = function() {
      var ctx = document.getElementById('canvas').getContext('2d');
      window.myMixedChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Relatorio Grafico de Curva ABC por Periodo'
          },
          tooltips: {
            mode: 'index',
            intersect: true
          }
        }
      });
    };

  </script>
</body>
