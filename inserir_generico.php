<?php
/**
 * @brief programa para inserir.
 */
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    switch($tipo)
    {
        case "programa_paciente":
            $sel_mov = "select * from movimento where mov_codigo = {$mov_codigo}";
            $exec_sel_mov = pg_query($sel_mov);
            $row = pg_fetch_array($exec_sel_mov);
            $select = "select * from itens_movimento where mov_codigo = {$mov_codigo}";
            $exec_select = pg_query($select);
            while($linha = pg_fetch_array($exec_select))
            {
                $sql = "select pro_codigo
                        from programa_produto
                        where pro_codigo = {$linha[pro_codigo]}";
                $exec_sql = pg_query($sql);
                if(pg_num_rows($exec_sql) > 0)
                {
                    $sql_prog_paciente = "select a.prgp_codigo, b.prgp_codigo, b.pro_codigo
                                        from cota_paciente a, programa_produto b
                                        where b.pro_codigo = {$linha[pro_codigo]}
                                        and a.prgp_codigo = b.prgp_codigo
                                        and usu_codigo = {$row[usu_codigo]}";
                    $exec_sql_prog_paciente = pg_query($sql_prog_paciente);
                    if(pg_num_rows($exec_sql_prog_paciente) == 0)
                    {
                        if($linha[ite_qtde_dia] < 31)
                        {
                            $ctp_periodo = "MENSAL";
                        } else if($linha[ite_qtde_dia] < 61 && $linha[ite_qtde_dia] > 30) {
                            $ctp_periodo = "BIMESTRAL";
                        } else if($linha[ite_qtde_dia] > 60 && $linha[ite_qtde_dia] < 91) {
                            $ctp_periodo = "TRIMESTRAL";
                        } else if($linha[ite_qtde_dia] > 90 && $linha[ite_qtde_dia])
                            $insert = "insert into programa";
                    }
                }
            }
        break;
    }
?>