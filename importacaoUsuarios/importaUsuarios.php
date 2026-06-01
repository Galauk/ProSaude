<?php
	header('Content-Type: text/html; charset=utf-8');
	// COLOQUE O ARQUIVO DE DB_CNS.GDB na pasta desse arquivo
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("display_errors", 1);
	session_start();
	//include_once "C:\desenvolvimento\elotech\WebSocialSaude\library\php\db.inc.php";
	//$db = pg_connect("host=192.168.1.62 dbname=sjcaiua user=postgres port=5433 password=123") or die(pg_last_error());
	
	//echo "localhost:$_SESSION[root]$_SESSION[modulo]importacaoUsuarios\DB_CNS.GDB","SYSDBA","masterkey";
	//$conexao = ibase_connect("localhost:$_SESSION[root]$_SESSION[modulo]importacaoUsuarios\DB_CNS.GDB","SYSDBA","masterkey");
	//die("localhost:C:\\xampp\htdocs\WebSocialSaude\importacaoUsuarios\DB_CNS.GDB");
	$conexao = ibase_connect("localhost:C:\\xampp\htdocs\WebSocialSaude\importacaoUsuarios\DB_CNS.GDB","SYSDBA","masterkey");
	
	$sql = "SELECT *
			  FROM tb_ms_usuario u
			  JOIN tb_ms_usuario_cns_elos c
			    ON c.co_usuario = u.co_usuario";
	$query = ibase_query($conexao,$sql) or die("ako");
	while($r = ibase_fetch_assoc($query)){
		foreach($r as $k => $v){
			$row[$k] = utf8_encode($v);
		}
		
		$insertUsuario = "INSERT 
						    INTO usuario(usu_nome,
						    			 usu_mae,
						    			 usu_pai,
						    			 usu_datanasc,
						    			 usu_escolaridade,
						    			 usu_st_conjugal,
						    			 usu_estado_civil,
						    			 rac_codigo,
						    			 cd_nacionalidade,
						    			 usu_freq_escolar,
						    			 cid_codigo_nasc,
						    			 usu_cbo_r,
						    			 usu_sexo,
						    			 dt_add,
						    			 usu_ibge_codigo,
						    			 usu_obito,
						    			 ".($row['tp_cartao'] == "D" ? "usu_cartao_sus" : "usu_cartao_p_sus").")
						    	  VALUES('$row[NO_USUARIO]',
						    	  		 '$row[NO_MAE]',
						    	  		 '".($row[NO_PAI] == "" ? NULL : "$row[NO_PAI]")."',
						    	  		 '$row[DT_NASCIMENTO]',
						    	  		 '$row[CO_ESCOLARIDADE]',
						    	  		 '$row[CO_SITUACAO_FAMILIAR]',
						    	  		 '$row[CO_ESTADO_CIVIL]',
						    	  		 '$row[CO_RACA]',
						    	  		 '$row[CO_PAIS]',
						    	  		 '$row[ST_FREQUENTA_ESCOLA]',
						    	  		 '".($row[CO_MUNICIPIO_NASC] == "" ? 0 : $row[CO_MUNICIPIO_NASC])."',
						    	  		 '$row[CO_CGRP_CBOA]',
						    	  		 '$row[CO_SEXO]',
						    	  		 CURRENT_DATE,
						    	  		 '$row[CO_MUNICIPIO_RESIDENCIA]',
						    	  		 '$row[ST_VIVO]',
						    	  		 '$row[CO_NUMERO_CARTAO]')";
		echo $insertUsuario.";"."<br/>";
		//pg_query($insertUsuario) or die(pg_last_error());
		//pg_query($insertUsuario) OR DIE($insertUsuario);
	}

	//echo $row["NO_USUARIO"];
	