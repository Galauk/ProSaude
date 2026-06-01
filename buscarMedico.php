<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	$select = "select distinct(med_codigo) from grade_medico where uni_codigo=$uni_codigo and esp_codigo=$esp_codigo";
	$sql = pg_query($select);
	echo $select;
	$i = 0;
	$cod = pg_fetch_array($sql);
	while($cod = pg_fetch_array($sql))
	{
		$i++;
		$busca = "select m.med_nome,m.med_codigo from medico as m left join medico_especialidade as mesp on mesp.med_codigo=m.med_codigo where mesp.esp_codigo=$esp_codigo and mesp.med_codigo=$cod[0] order by med_nome";
		//echo "<br>".$busca;
		$buscaMed = pg_query($busca);
		$med = pg_fetch_array($buscaMed);
		if($i = pg_num_rows($cod))
		{
			echo "$cod[0]-$med[0]";
		} else {
			echo "$cod[0]-$med[0];";
		}
	}
?>