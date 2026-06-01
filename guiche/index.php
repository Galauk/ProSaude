<?php
	include "db.inc.painel.php";
	$sql = pg_query("select * from unidade where cnes_ativo = 'A'") or die(pg_last_error());

	echo "<div><form method=post action='carrega-dados.php'>";
    echo "Selecione a Unidade:<br>
    		";

	while($rr = pg_fetch_array($sql)) {
		echo ($rr[uni_codigo]==4)?"<input type=radio name=uni_codigo checked value='".$rr[uni_codigo]."'>".$rr[uni_desc]."<br>":"<input type=radio name=uni_codigo value='".$rr[uni_codigo]."'>".$rr[uni_desc]."<br>";
	}
	echo  "<input type=submit value='entrar'>";
	echo "</form></div>";

?>