<?php 
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	$common = new commonClass();
	echo $common->incJquery();
	echo $common->menuTab(array("Sucesso"));
	echo $common->bodyTab('1');
		echo "<div align=center style='height:50px; padding-top:40px;'><font size=3 color=green>Paciente agendado com sucesso.</font></div>";
	echo $common->closeTab();
?>