<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();
	$sql = "update produto_setor set
				set_codigo = $_POST[set_codigo],
				pro_codigo = ".($pro_codigo        ? "'$pro_codigo'"        : "null") . "," .
				"prset_minimo = ".($pro_minimo        ? "'$pro_minimo'"        : "null") . "," .
				"prset_maximo = ".($pro_maximo        ? "'$pro_maximo'"        : "null") . "," .
				"prset_tempo_reposicao = ".($pro_tempo_reposicao        ? "'$pro_tempo_reposicao'"        : "null") . "," .
				"prset_seguranca = ".($pro_seguranca        ? "'$pro_seguranca'"        : "null")."
				where prset_codigo = $_POST[prset_codigo]";
	//echo $sql;
	$exec_sql = pg_query($sql);
	echo pg_last_error($db);
	if($exec_sql)
	{
		echo "<SCRIPT LANGUAGE=\"JavaScript\">alert('Cadastro alterado com sucesso')</SCRIPT>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='materiais.php?acao=form_incluir&pro_codigo=$pro_codigo&id_login=$id_login'\", 0);
           </SCRIPT>";
	}
?>