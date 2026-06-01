<?php
    
	//namespace esus\banco_cidadao;
    include_once $_SESSION['root']."WebSocialComum/global.php";
    
    class BancoOdonto {
        
		public function getDados(){
			$sql = "SELECT * FROM esus_odonto WHERE uuid IS NULL OR uuid = ''";
            $query = pg_query($sql) or die(pg_last_error());
            return pg_fetch_all($query);
        }
		
		public function getQtdRegistros(){
			$sql = "SELECT * FROM esus_odonto WHERE uuid IS NULL OR uuid = ''";
			$query = pg_query($sql) or die(pg_last_error());
			$numRegistro = pg_num_rows($query);
			return $numRegistro;
		}
		
		public function getProcedimentos($odoPconCodigo){
			$sql = "SELECT 
						proc.proc_codigo_sus 
					FROM 
						odonto_procedimentos_realizados AS opr
					INNER JOIN 
						procedimento AS proc ON opr.proc_codigo=proc.proc_codigo
					WHERE
						opr.odo_pcon_codigo = $odoPconCodigo";
			$query = pg_query($sql);
			return pg_fetch_all($query);
		}
		
		public function getCondutaEncaminhamento($odoPconCodigo){
			$sql = "SELECT 
						rlote.tp_cds_encam_odonto
					FROM 
						rl_cds_atend_odonto_tipo_encam AS  rlote
					INNER JOIN 
						atendimento AS ate ON rlote.ate_codigo=ate.ate_codigo
					INNER JOIN 
						odonto_procedimentos_controle AS opc ON ate.ate_codigo=opc.ate_codigo
					WHERE 
						opc.odo_pcon_codigo = $odoPconCodigo";
			$query = pg_query($sql);
			return pg_fetch_all($query);
		}
		
		public function getVigilanciaSaudeBucal($odoPconCodigo){
			$sql = "SELECT 
						rltvb.tp_cds_vig_saude_bucal
					FROM 
						rl_cds_atend_odont_tip_vig_buc AS  rltvb
					INNER JOIN 
						atendimento AS ate ON rltvb.ate_codigo=ate.ate_codigo
					INNER JOIN 
						odonto_procedimentos_controle AS opc ON ate.ate_codigo=opc.ate_codigo
					WHERE 
						opc.odo_pcon_codigo = $odoPconCodigo";
			$query = pg_query($sql);
			return pg_fetch_all($query);
		}
		
		public function atualizaStatus($uuid,$codigo){
			$sql = "UPDATE esus_odonto SET uuid = '".$uuid."' WHERE odo_pcon_codigo = '".$codigo."'";
			$query = pg_query($sql);
		}
		
	}

?>
