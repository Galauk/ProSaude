<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();
 //exit($pro_codigo);
	$sql = "insert into produto_setor (set_codigo, pro_codigo, prset_minimo, prset_maximo, prset_tempo_reposicao, prset_seguranca) values ( 
					$set_codigo,".
					($pro_codigo        ? "'$pro_codigo'"        : "null") . "," .
					($pro_minimo        ? "'$pro_minimo'"        : "null") . "," .
                    ($pro_maximo        ? "'$pro_maximo'"        : "null") . "," .
                    ($pro_tempo_reposicao        ? "'$pro_tempo_reposicao'"        : "null") . "," .
                    ($pro_seguranca        ? "'$pro_seguranca'"        : "null") . ")";
	//echo $sql;
	$exec_sql = pg_query($sql);
	//echo pg_last_error($db);
	if($exec_sql)
	{
		echo "<SCRIPT LANGUAGE=\"JavaScript\">alert('Cadastro efetuado com sucesso')</SCRIPT>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='materiais.php?acao=form_incluir&pro_codigo=$pro_codigo&id_login=$id_login'\", 0);
           </SCRIPT>";
	}
?>