<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

  $dini = $_REQUEST['data_ini'];
  $dfim = $_REQUEST['data_fim'];
  $uni = $_REQUEST['uni_codigo'];

cabecario_rel('Ranking de Pacientes mais Atendidos por Periodo',$dini,$dfim,null);

$sql = pg_query("select calcula_idade(usu.usu_codigo) as idade, usu.usu_codigo,usu_nome,count(ate_codigo) as total,(select to_char(ate_data,'dd/mm/yyyy') from atendimento where usu_codigo = usu.usu_codigo order by ate_data desc limit 1) as dtultima from atendimento as ate 
join usuario as usu on usu.usu_codigo = ate.usu_codigo 
where ate_data >='".$dini."' and ate_data <= '".$dfim."' 
group by usu_nome,usu.usu_codigo order by 4 desc") or die(pg_last_error());



echo "<table width=100% cellspacing=5 cellpadding=5 border=1>
<tr>
   <td>Paciente</td>
   <td>Idade</td>
   <td>Quantidade de Atendimentos</td>
   <td>Data Ultima Consulta</td>
</tr>";
while($rr=pg_fetch_array($sql)) {
  echo "<tr>
     <td>$rr[usu_nome]</td>
     <td>$rr[idade]</td>
     <td>$rr[total]</td>
     <td>$rr[dtultima]</td>
  </tr>";
}
echo "</table>";
?>