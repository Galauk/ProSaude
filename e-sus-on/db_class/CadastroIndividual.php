<?php
//namespace esus\banco_cidadao;
include_once $_SESSION['root']."WebSocialComum/global.php";

class BancoCadastroIndividual {
	
	public function getDados(){
            $sql = "SELECT  
                        eci_codigo, 
                        usu_codigo, 
                        eci_usr_cnes, 
                        eci_usr_profissional_cns,
                        eci_usr_codigo_ibge,
                        eci_usr_dtatendimento, 
                        eci_usu_codigo_ibge,
                        eci_usu_dtnascimento,
                        eci_usu_nacionalidade, 
                        retira_acentuacao(eci_usu_nome) as eci_usu_nome, 
                        retira_acentuacao(eci_usu_mae) as eci_usu_mae,
                        eci_usu_raca, 
                        eci_usu_sexo, 
                        eci_usu_escola, 
                        eci_usu_sit_rua, 
                        eci_usu_deficiencia, 
                        eci_usu_cns, 
                        eci_tipo_dado_serializado, 
                        uuid_ficha 
                    FROM esus_cadastro_individual 
                    WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
            $query = pg_query($sql) or die(pg_last_error());
            return pg_fetch_all($query);
	}
	
	public function getQtdRegistros(){
		$sql = "SELECT * FROM esus_cadastro_individual WHERE uuid_ficha IS NULL OR uuid_ficha = ''";
		$query = pg_query($sql) or die(pg_last_error());
		$numRegistro = pg_num_rows($query);
		return $numRegistro;
	}
	
	public function atualizaStatus($uuid,$codigo){
		$sql = "UPDATE esus_cadastro_individual SET uuid_ficha = '".$uuid."' WHERE usu_codigo = '".$codigo."'";
		$query = pg_query($sql);
	}
	
}

?>
