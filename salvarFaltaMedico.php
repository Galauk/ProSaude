<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
		
	$select = "select age_falta_medico from agendamento where age_codigo = $_GET[age_codigo]";
	$exec_select = pg_query($select);
	$row = pg_fetch_array($exec_select);
	if($row[0] == "N" || $row[0] != "M")
	{
		$update = "update agendamento set age_falta_medico='M' where age_codigo = $_GET[age_codigo]";
		$falta = "falta";
	} else {
		$update = "update agendamento set age_falta_medico='N' where age_codigo = $_GET[age_codigo]";
		$falta = "erro";
	}
		$exec_update = pg_query($update);
		echo pg_last_error($db);
		//exit();
		if(pg_affected_rows($exec_update) > 0)
		{
			echo "$falta-true";
		} else {
			echo "$falta-false";
		}
?>
