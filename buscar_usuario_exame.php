<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    
	$sql = "SELECT usr_nome
			  FROM usuarios
			 WHERE usr_codigo = (SELECT usr_codigo_cad
								   FROM grade_exame
								  WHERE graex_codigo = $graex_codigo)";
    $exec_sql = pg_query($sql);
    $usr_cad = pg_fetch_array($exec_sql);
    $sql = "select usr_nome
            from usuarios
            where usr_codigo = (select usr_codigo_alt
                                from grade_exame
                                where graex_codigo = $graex_codigo)";
    $exec_sql = pg_query($sql);
    $usr_alt = pg_fetch_array($exec_sql);
    echo "[{usr_cad : '$usr_cad[0]', usr_alt : '$usr_alt[0]', codigo : $graex_codigo}]";
	?>
