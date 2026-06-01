<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    cabecario();
	
	$select = "select count(*)
					from movimento, itens_movimento
					where movimento.mov_codigo = itens_movimento.mov_codigo
					and (set_entrada = $set_codigo or set_saida = $set_codigo)";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
	if($linha[0] > 0)
	{
		$sql = "delete from produto_setor where prset_codigo = $prset_codigo";
		$exec_sql = pg_query($sql);
		if($exec_sql)
		{
			echo "<SCRIPT LANGUAGE=\"JavaScript\">alert('Registro apagado com sucesso')</SCRIPT>";
			echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='materiais.php?acao=form_incluir&pro_codigo=$pro_codigo&id_login=$id_login'\", 0);
           </SCRIPT>";
		}
	} else {
		echo "<SCRIPT LANGUAGE=\"JavaScript\">alert('Este registro não pode ser apagado!')</SCRIPT>";
			echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='materiais.php?acao=form_incluir&pro_codigo=$pro_codigo&id_login=$id_login'\", 0);
           </SCRIPT>";
	}
?>