<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
$libex_codigo = $_GET['cod_lib'];
	switch($_GET['tipo'])
    {
        case "cod_lib":
            $sql = "    select a.libex_codigo,
	   						   to_char (a.libex_data_cad ,'dd/mm/yyyy') as libex_data_cad,
							   a.usu_codigo,
							   b.usu_nome,
	   						   to_char (b.usu_datanasc ,'dd/mm/yyyy') as usu_datanasc,
							   b.usu_mae,
							   b.usu_end_cidade 
						  from liberacao_exame a 
						  join usuario b 
						    on a.usu_codigo = b.usu_codigo 
						 where a.libex_codigo = $libex_codigo";
            $exec_sql = pg_query($sql);
            echo $sql;
            if(pg_num_rows($exec_sql) > 0)
            {
            	$linha = pg_fetch_array($exec_sql);
                echo $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4].";".$linha[5].";".$linha[6];
            } else {
                echo "vazio";
            }
        break;
        case "prontuario_paciente":
            $sql = "select usu_prontuario
                    from usuario
                    where usu_codigo = {$_GET['usu_codigo']}";
            $exec_sql = pg_query($sql);
            $linha = pg_fetch_array($exec_sql);
            if(pg_num_rows($exec_sql) > 0)
            {
                echo $linha[0];
            } else {
                echo "vazio";
            }
        break;
        case "usuario_manutencao":
            $sql = "select usr_nome
                    from usuarios
                    where usr_codigo = (select usr_codigo_cad
                                        from grade_medico
                                        where gra_codigo = $gra_codigo)";
            $exec_sql = pg_query($sql);
            $usr_cad = pg_fetch_array($exec_sql);
            $sql = "select usr_nome
                    from usuarios
                    where usr_codigo = (select usr_codigo_alt
                                        from grade_medico
                                        where gra_codigo = $gra_codigo)";
            $exec_sql = pg_query($sql);
            $usr_alt = pg_fetch_array($exec_sql);
            echo "[{usr_cad : '$usr_cad[0]', usr_alt : '$usr_alt[0]', codigo : $gra_codigo}]";
        break;
    }
?>