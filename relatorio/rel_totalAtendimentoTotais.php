<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

  $dini = $_REQUEST['data_ini'];
  $dfim = $_REQUEST['data_fim'];
  $uni = $_REQUEST['uni_codigo'];

cabecario_rel('Total de Atendimentos por Profissional',$dini,$dfim,null);

/*$sql = pg_query("select to_char(usu_datanasc,'DD/MM/YYYY') as datanasc,calcula_idade(usu.usu_codigo) as idade,* from programa_produto as pp 
join programa_atendimento as pa on pa.prg_codigo = pp.prg_codigo 
join produto as pro on pro.pro_codigo = pp.pro_codigo 
join cota_paciente as cp on cp.prgp_codigo=pp.prgp_codigo 
join usuario as usu on usu.usu_codigo = cp.usu_codigo where pp.prg_codigo = $prg") or die(pg_last_error());
*/
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
    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var config = {
      type: 'line',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
          label: 'My First dataset',
          borderColor: window.chartColors.red,
          backgroundColor: window.chartColors.red,
          data: ['10','20','30'
          ],
        }]
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: 'Total de Atendimentos por Profissional'
        },
        tooltips: {
          mode: 'index',
        },
        hover: {
          mode: 'index'
        },
        scales: {
          xAxes: [{
            scaleLabel: {
              display: true,
              labelString: 'Month'
            }
          }],
          yAxes: [{
            stacked: true,
            scaleLabel: {
              display: true,
              labelString: 'Value'
            }
          }]
        }
      }
    };

    window.onload = function() {
      var ctx = document.getElementById('canvas').getContext('2d');
      window.myLine = new Chart(ctx, config);
    };

  </script>
</body>

</html>
