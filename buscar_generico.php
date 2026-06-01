<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

	switch($_GET['tipo'])
    {
        case "dados_paciente":
            $sql = "select usu_codigo, usu_nome, to_char(usu_datanasc, 'dd/mm/yyyy') as usu_datanasc, usu_mae, usu_end_cidade
                    from usuario
                    where usu_prontuario = '{$_GET['usu_prontuario']}'";
            $exec_sql = pg_query($sql);
            $linha = pg_fetch_array($exec_sql);
            if(pg_num_rows($exec_sql) > 0)
            {
                echo $linha[0].";".$linha[1].";".$linha[2].";".$linha[3].";".$linha[4];
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