<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
        
        $sqlUp = "UPDATE logon SET cod_setor = $set_codigo where id_login = $usr_codigo";
        $exec_sqlUp = pg_query($sqlUp);
       
        $sqlSetor = "SELECT * FROM setor WHERE set_codigo = {$set_codigo}";
        $exec_setor = pg_query($sqlSetor);
        $res_sqlSetor = pg_fetch_array($exec_setor);
        
        $_SESSION['set_codigo'] = $set_codigo;
        $_SESSION['logon']['usr']->set_codigo = $res_sqlSetor['set_codigo'];
        $_SESSION['logon']['usr']->set_nome = $res_sqlSetor['set_nome'];
        $_SESSION['logon']['usr']->cod_setor = $res_sqlSetor['set_codigo'];
        
        $sql = "SELECT 
				set.set_codigo, 
				set.set_nome 
			FROM 
				setor AS set
			INNER JOIN 
				usuarios_setores AS uset ON set.set_codigo=uset.set_codigo
			INNER JOIN 
				usuarios AS usr ON uset.usr_codigo=usr.usr_codigo
			INNER JOIN 
				unidade AS uni ON set.uni_codigo=uni.uni_codigo 
			WHERE 
				(uni.uni_codigo =$uni_codigo) AND 
				(usr.usr_codigo =$usr_codigo)";
	$exec_sql = pg_query($sql);
	
	$option = "";
	while($row_dados=pg_fetch_array($exec_sql)) {
		if ($set_codigo_logado==$row_dados["set_codigo"]) {
			$option .= "<option value='".$row_dados["set_codigo"]."' selected>".$row_dados["set_nome"]."</option>";
		} else {
			$option .= "<option value='".$row_dados["set_codigo"]."'>".$row_dados["set_nome"]."</option>";
		}
	}
	
	echo $option;

?>