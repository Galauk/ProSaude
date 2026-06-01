<?php  
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
$mesAno = date(m).'/'.date(Y);
$diaMesAno = date(d).'/'.date(m).'/'.date(Y);
$proc = $_GET['proc_codigo'];
$med_codigo = $_GET['lab_codigo'];
$usu_codigo = $_GET['usu_codigo'];
$id_login =$_GET['id_login'];
$uni_codigo = $_GET['uni_codigo'];
$esp_codigo = $_GET['esp_codigo'];
$proc_codigo = substr($proc,"0","-1");
$array_proc = explode(",",$proc_codigo);

$utilizado = 0;
$totalLiberados = 0;

$result = 0;
foreach($array_proc as $value){	
	$sql = "select sum(x.valor) as valor_agenda from (
				 select (select max(proc_valor )
					   from grade_exame ge 
					  where ael.proc_codigo = ge.proc_codigo 
						and ael.med_codigo = ge.med_codigo) as valor,
					*
				   from agendamento_exame ae
				   join agendamento_exame_lista ael
					 on ae.agex_codigo = ael.agex_codigo
				  where to_char(ael.agexl_dt_cadastro, 'mm/yyyy') = '$mesAno'
					and ael.med_codigo = $med_codigo) as x";
	$umSql = pg_query($sql2);
	$pegaUm = pg_fetch_array($doisSql);
	$totalAgendados = $pegaDois['valor_agenda'];
					
	$sql2 = "select sum(x.valor) as valor_final from (
				 select (select max(proc_valor )
					   from grade_exame ge 
					  where lel.proc_codigo = ge.proc_codigo 
						and lel.med_codigo = ge.med_codigo) as valor,
					*
				   from liberacao_exame le
				   join liberacao_exame_lista lel
					 on le.libex_codigo = lel.libex_codigo
				  where to_char(lel.libexl_dt_cadastro, 'mm/yyyy') = '$mesAno'
					and lel.libexl_status = 'A'
					and lel.med_codigo = $med_codigo) as x";
	$doisSql = pg_query($sql2);
	$pegaDois = pg_fetch_array($doisSql);
	$totalLiberados = $pegaDois['valor_final'];
	
	$sql3 = "select vlr_mensal as cota
					   from grade_exame_mensal
					  where to_char(gex_periodo, 'mm/yyyy') = '$mesAno' and med_codigo = $med_codigo";
	$tresSql = pg_query($sql3);
	$pegaTres = pg_fetch_array($tresSql);
	}
	
	
//echo $proc_codigo;
//echo "<br>".$sql2;
//echo "<br>".$totalLiberados;
//echo "<br>".$sql;
//echo "<br>".$liberados;
//echo "<br>".$agendados;
//exit;

if($quotaMensal < $totalLiberados)
{
	echo"<script>
			alert('Quota excedida!');
			window.close();
		</script>";
}else{
	header("LOCATION: exa_listaliberacao.php?id_login=$id_login&usu_codigo=$usu_codigo&uni_codigo=$uni_codigo&med_codigo=$med_codigo&esp_codigo=$esp_codigo&lab_codigo=$lab_codigo&proc_codigo=$proc_codigo");
	
		exit();
}

//Aqui termina as validações do valor
?>
