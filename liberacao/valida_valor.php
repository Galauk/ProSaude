<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
//header("LOCATION: exa_listaliberacao.php?id_login=$id_login&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&lab_codigo=$lab_codigo&proc_codigo=$proc");
$mesAno = date(m).'/'.date(Y);
$proc = $_GET['proc_codigo'];
$med_codigo = $_GET['lab_codigo'];
$usu_codigo = $_GET['usu_codigo'];
$id_login =$_GET['id_login'];
$uni_codigo = $_GET['uni_codigo'];
$esp_codigo = $_GET['esp_codigo'];
$proc_codigo = substr($proc,"0","-1");
$array_proc = explode(",",$proc_codigo);

$utilizado = 0;
$valorSomado = 0;
$liberados = 0;
$agendados = 0;
$valor = 0 ;

$sqlLiberados = "select distinct(proc_codigo) from liberacao_exame_lista where med_codigo = $med_codigo and to_char(libexl_dt_cadastro, 'mm/yyyy') = '$mesAno'";
$qryLiberados = pg_query($sqlLiberados);

//for($i=0; $i<count($teste);$i++) {

while($liberados = pg_fetch_array($qryLiberados))
{
$cod_pro_lib = $liberados['proc_codigo'];
	
$sql = "select sum(liberado * valor)as total,x.liberado,x.valor from
			(select count(lel.libexl_codigo) as liberado, 
				 (select max(proc_valor) 
					from grade_exame 
				   where proc_codigo = $cod_pro_lib
  					 and med_codigo = $med_codigo
					 and to_char(graex_data, 'mm/yyyy') = '$mesAno') as valor 
			  from liberacao_exame_lista lel 
			 where to_char(lel.libexl_dt_cadastro, 'mm/yyyy') = '$mesAno' 
			   and lel.proc_codigo = $cod_pro_lib 
			   and lel.libexl_status = 'A' 
			   and lel.med_codigo = $med_codigo) as x group by liberado,valor";
			   $qry = pg_query($sql);
			   $registro = pg_fetch_array($qry);
			   $valor = $valor + $registro['total'];

	}

	
	
$sqlAgendados = "select distinct(proc_codigo) from agendamento_exame_lista where med_codigo = $med_codigo and to_char(agexl_dt_cadastro, 'mm/yyyy') = '$mesAno'";
$qryAgendados = pg_query($sqlAgendados);

while($agendados = pg_fetch_array($qryAgendados))
{
$cod_pro_age = $agendados['proc_codigo'];
	
$sqlAgendamento = "select sum(agendado * valor)as total,x.agendado,x.valor from
			(select count(ael.agexl_codigo) as agendado, 
				 (select max(proc_valor) 
					from grade_exame 
				   where proc_codigo = $cod_pro_age 
				     and med_codigo = $med_codigo
					 and to_char(graex_data, 'mm/yyyy') = '$mesAno') as valor 
			  from agendamento_exame_lista ael 
			 where to_char(ael.agexl_dt_cadastro, 'mm/yyyy') = '$mesAno' 
			   and ael.proc_codigo = $cod_pro_age 
			   and ael.med_codigo = $med_codigo) as x group by agendado,valor";
			   $qryAgendamento = pg_query($sqlAgendamento);
			   $registroAgendamento = pg_fetch_array($qryAgendamento);
			   $valorAgendamento = $valorAgendamento + $registroAgendamento['total'];
			   //$valorAgendamento = $registroAgendamento['total'];
			   

	}


/*$sql4 = "select vlr_mensal as cota
					   from grade_exame_mensal
					  where to_char(gex_periodo, 'mm/yyyy') = '$mesAno' and med_codigo = $med_codigo";
*/
$sql4 = "select vlr_mensal as cota
					   from grade_exame_mensal
					  where med_codigo = $med_codigo";
	$quatroSql = pg_query($sql4);
	$pegaQuatro = pg_fetch_array($quatroSql);
	$quotaMensal = $pegaQuatro['cota'];

	foreach($array_proc as $value){
		
		$sql3 = "select sum(liberado * valor)as total,x.liberado,x.valor from
			(select count(lel.libexl_codigo) as liberado, 
				 (select max(proc_valor) 
					from grade_exame 
				   where proc_codigo = $value 
				   	 and med_codigo = $med_codigo
					 and to_char(graex_data, 'mm/yyyy') = '$mesAno') as valor 
			  from liberacao_exame_lista lel 
			 where to_char(lel.libexl_dt_cadastro, 'mm/yyyy') = '$mesAno' 
			   and lel.proc_codigo = $value 
			   and lel.libexl_status = 'A' 
			   and lel.med_codigo = $med_codigo) as x group by liberado,valor";
			   $qry3 = pg_query($sql3);
			   $registro3 = pg_fetch_array($qry3);
			   $valor3 = $valor3 + $registro3['valor'];

					
	}
	$total = $valor + $valor3 + $valorAgendamento ;
/*
echo "TOTAL: ".$total."<br>";
echo "VALOR3: ".$valor3."<br>";
echo "VALOR: ".$valor."<br>";
echo "VLR AGEND: ".$valorAgendamento."<br>";
exit;

echo "VALOR3: ".$valor3."<br>";	
echo "VLR AGEND: ".$valorAgendamento."<br>";	
echo "QUOTA: ".$quotaMensal."<br>";	
*/
if($quotaMensal >= $total)
{

	header("LOCATION: ../exame/exa_listaliberacao.php?id_login=$id_login&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&lab_codigo=$lab_codigo&proc_codigo=$proc_codigo");
	
}
else{
	echo"<script>
			alert('Quota excedida!');
			window.close();
		</script>";
		exit();
}

//Aqui termina as validações do valor
?>
