<?php
//namespace esus\banco_cidadao;
include_once $_SESSION['root'].$_SESSION['modulo']."global.php";
// include_once "../../global.php";
set_time_limit(0);
class BancoCadastroIndividual {

	public function getDados(){
            $sql = "SELECT * FROM usuario WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
            $query = pg_query($sql) or die(pg_last_error());
            return pg_fetch_all($query);
	}

	public function getQtdRegistros(){
		$sql = "SELECT * FROM usuario WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		$numRegistro = pg_num_rows($query);
		return $numRegistro;
	}

	public function atualizaStatus($uuid,$codigo){
		$sql = "UPDATE usuario SET uuid_ficha = '".$uuid."' WHERE usu_codigo = '".$codigo."'";
		$query = pg_query($sql);
	}

}

?>
