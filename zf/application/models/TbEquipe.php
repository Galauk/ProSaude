<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbEquipe extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_equipe';
    protected $_primary = 'co_seq_equipe';
    
    public function salvar(array $dados) {
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            print_r("Falha ao salvar equipe: ".$ex->getMessage());
        }
        return true;
    }
    
    public function getEquipePorIne($ine=FALSE){
        if(empty($ine)){
            return false;
        }
        
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("eqp"=>"tb_equipe"),array("co_seq_equipe","no_equipe"))
                ->where("nu_ine = '$ine'")
                ->where("st_ativo = 1");

        return $this->fetchRow($where);
    }
    
    public function atualizaStatusGeral(){
        $where = $this->select()->getPart(Zend_Db_Table_Select::WHERE);
        if($where[0]){
            $where = $where[0];
        }
        $data = array('st_ativo'=> 1);
        return $this->update($data, $where);
    }
    
    public function verificaSeJáExiste($ine=FALSE,$uni_codigo=FALSE){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("teqp" => "tb_equipe"),array("qtd"=>"count(*)","co_seq_equipe"))
                ->where("nu_ine = ?",$ine)
                ->where("uni_codigo = '$uni_codigo'")
                ->group(array("co_seq_equipe"));
        //die($where);
        
        return $this->fetchRow($where);
    }
    
    /**
    * Buscar os INE
    * usado para alimentar o plugin de busca (jquery)
    * @return json
    */
	public function buscar($term=FALSE) {
		if ($term)
			$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)->distinct()
					->from(array("ine" => "tb_equipe"), array("nu_ine"))
					->where("nu_ine ilike '%$term%'")
					->order(array("nu_ine"))
					->limit(15);
                //die($where);
        
        $all = $this->fetchAll($where);

		$out = array();
		foreach ($all as $usu) {
			$data = array();
			foreach ($usu as $key => $value) {
				$data [$key] = $value;
			}

			$out [] = array(
				"id" => $usu->nu_ine,
				"label" => $usu->nu_ine,
				"data" => $data
			);
		}

		if (!count($out)) {
			$out [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("nu_ine" => "0", "nu_ine" => "")
			);
		}

		return $out;
    }
    
    public function getTotalAteMedEnf($data_ini = false, $data_fim = false, $ine = false){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)                        
                ->from(array("ate" => "atendimento"), array("count(*) total"))
                ->join(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                ->join(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","")
                ->join(array("me" => "medico_especialidade"),"me.med_codigo =  ate.med_codigo","")
                ->join(array("esp"=>"especialidade"),"esp.esp_codigo = me.esp_codigo","")
                ->where("cod_cbo IN('2235C2','223530','2235C3','223505','223510','223515',
                '223520','223525','223535','223540','223545','223555','223560','223550','223565',
			    '2231F3','223141','225270','2231A1','225121','2231F5','2231F6',
			    '2231F7','225110','225140','225160','225170','225175','225210','223119','225105','225145',
			    '225150','225151','225245','223145','223148','223152','225240','225103','225106','225109',
			    '225118','225120','225124','225127','225136','225180','225185','225215','225220','225235',
			    '225255','225260','225265','225275','225285','225305','225315','225320','225330','225340',
			    '225135','225225','2231F8','223305','225139','2231A2','225112','225122','225130','225133',
			    '225203','225250','225290','225295','225325','225335','225345','225350','422110','223150',
			    '225142','2231G1','225280','225310','225115','225148','225165','2231F9','225155','225195','225230','225125')");
        
        if($data_ini){
            $where->where("ate_data >= ?",$data_ini);
        }
        
        if($data_fim){
            $where->where("ate_data <= ?",$data_fim);
        }
        
        if($ine){
            $where->where("nu_ine = ?",$ine);
        } 
           
        return $this->fetchRow($where);              
    }

    public function getTotalAteMedicos($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)                      
                ->from(array("ate" => "atendimento"), array("count(*) total"))
                ->join(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                ->join(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","")
                ->join(array("me" => "medico_especialidade"),"me.med_codigo =  ate.med_codigo","")
                ->join(array("esp"=>"especialidade"),"esp.esp_codigo = me.esp_codigo","")
                ->where("cod_cbo in ('2231F3','223141','225270','2231A1','225121','2231F5','2231F6','2231F7','225110','225140','225160','225170','225175','225210','223119','225105','225145','225150','225151','225245','223145','223148','223152','225240','225103','225106','225109','225118','225120','225124','225127','225136','225180','225185','225215','225220','225235','225255','225260','225265','225275','225285','225305','225315','225320','225330','225340','225135','225225','2231F8','223305','225139','2231A2','225112','225122','225130','225133','225203','225250','225290','225295','225325','225335','225345','225350','422110','223150','225142','2231G1','225280','225310','225115','225148','225165','2231F9','225155','225195','225230','225125')");
        
        if($data_ini){
            $where->where("ate_data >= ?",$data_ini);
        }
        
        if($data_fim){
            $where->where("ate_data <= ?",$data_fim);
        }
        
        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
        
        return $this->fetchRow($where);             
    }

    public function getTotalAteEnfermeiros($data_ini = false, $data_fim = false, $ine = false){
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("ate" => "atendimento"), array("count(*) total"))
            ->join(array("ue" => "usuarios_equipe"), "ue.usr_codigo = ate.med_codigo", "")
            ->join(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe", "")
            ->join(array("me" => "medico_especialidade"), "me.med_codigo =  ate.med_codigo", "")
            ->join(array("esp" => "especialidade"), "esp.esp_codigo = me.esp_codigo", "")
            ->where("cod_cbo in ('2235C2','223530','2235C3','223505','223510','223515','223520','223525','223535','223540','223545','223555','223560','223550','223565')");
        
        if ($data_ini) {
            $where->where("ate_data >= ?", $data_ini);
        }

        if ($data_fim) {
            $where->where("ate_data <= ?", $data_fim);
        }

        if ($ine) {
            $where->where("nu_ine = ?", $ine);
        }

        return $this->fetchRow($where);
    }
    
    public function getTotalRecemNascidos($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ate" => "atendimento"), array("count(*) total"))
                ->join(array("usu" => "usuario"), "usu.usu_codigo = ate.usu_codigo", "")
                ->join(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                ->join(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","");
        
        if($data_ini){
            $where->where("usu_datanasc >= ?",$data_ini);
        }
        
        if($data_fim){
            $where->where("usu_datanasc <= ?",$data_fim);
        }
        
        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
        
        //die(var_dump($where));
        return $this->fetchRow($where); 
    }
    
    public function getTotalAtendPrimeiraSemanaRecemNascidos($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usu" => "usuario"), array("count(*) total"))
                ->join(array("ate" => "atendimento"), "usu.usu_codigo = ate.usu_codigo", "")
                ->join(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                ->join(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","")
                ->where("ate.ate_data <= usu.usu_datanasc + 7");
        
        if($data_ini){
            $where->where("usu_datanasc >= ?",$data_ini);
        }
        
        if($data_fim){
            $where->where("usu_datanasc <= ?",$data_fim);
        }
        
        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
        
        return $this->fetchRow($where);
    }

    public function getTotalTratamentosConcluidosOdonto($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ot" => "odonto_tratamento"), array("count(*) total"))
                ->joinLeft(array("opc" => "odonto_procedimentos_controle"), "opc.odo_trat_codigo=ot.odo_trat_codigo", "")
                ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo = opc.ate_codigo", "")
                ->joinLeft(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                ->joinLeft(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","")
                ->where("ot.odo_trat_status='F'");

        if($data_ini){
            $where->where("ate_data >= ?",$data_ini);
        }
        
        if($data_fim){
            $where->where("ate_data <= ?",$data_fim);
        }
        
        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
                
        //die($where);
        return $this->fetchRow($where);
    }
        
    public function getTotalTratamentosAbertosOdonto($data_ini=false,$data_fim=false,$ine=false){
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("ot" => "odonto_tratamento"), array("count(*) total"))
                ->joinLeft(array("opc" => "odonto_procedimentos_controle"), "opc.odo_trat_codigo=ot.odo_trat_codigo", "")
                ->joinLeft(array("ate" => "atendimento"), "ate.ate_codigo = opc.ate_codigo", "")
                ->joinLeft(array("ue" =>"usuarios_equipe"),"ue.usr_codigo = ate.med_codigo","")
                ->joinLeft(array("ine" => "tb_equipe"), "ue.co_equipe = ine.co_seq_equipe","")
                ->where("ot.odo_trat_status='A'");

        if($data_ini){
            $where->where("ate_data >= ?",$data_ini);
        }

        if($data_fim){
            $where->where("ate_data <= ?",$data_fim);
        }

        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
                
        //die($where);
        return $this->fetchRow($where);    
    }

    public function getPacientesPorAreaAcs($usr_codigo=FALSE, $nu_ine=FALSE, $dataFinal=FALSE, $dataInicial=FALSE){
        $dados = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from("vw_rel_domicilio")
                ->order("dom_data_cadastro");
        
        if($usr_codigo){
            $dados->where("usr_codigo= ?",$usr_codigo);
        }

        if($nu_ine){
            $dados->where("nu_ine= ?",$nu_ine);
        }

        if($dataInicial){
            $dados->where("dom_data_cadastro >= ?",$dataInicial);
        }
        
        if($dataFinal){
            $dados->where("dom_data_cadastro <= ?",$dataFinal);
        }
        
        //die($dados); 
        return $this->fetchAll($dados);
    }
}