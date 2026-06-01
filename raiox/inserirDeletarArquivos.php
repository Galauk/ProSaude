<?php
	include "../global.php";
	
	$acao = $_GET[acao];
	$req_codigo = $_GET[req_codigo];
	$filename = $_GET[name];
	
	if($acao == "inserir"){
		$caminho = str_replace('\\', '/',SAUDE);
	 	$dir =  $caminho."raiox/server/php/files";
		file_put_contents($filename);
		$file = $dir."/".$filename;
		
		$insert = "INSERT INTO upload_arquivo (req_codigo,
									  upl_data,
									  upl_arquivo,
									  usr_codigo,
									  upl_arquivo_nome)
								VALUES($req_codigo,
									   'NOW()',
									   lo_import('$file'),
									   1,
									   '$filename')";
		$queryInsert = pg_query($insert) or die($insert.pg_last_error());
		//unlink($file);
	}else if($acao == "delete"){
		$codigo = explode("-", $filename);
		$upl_codigo = $codigo[0];
		if($codigo[1]){
			$where = "upl_codigo = $upl_codigo";
		}else{
			$where = "req_codigo = $req_codigo";
		}
		$delete = "DELETE FROM upload_arquivo WHERE $where";
		$query = pg_query($delete);
	}