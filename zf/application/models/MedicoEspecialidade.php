<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_MedicoEspecialidade extends Elotech_Db_Table_Abstract {

	protected $_name = 'medico_especialidade';
	protected $_primary = 'mes_codigo';
    protected $_sequence = 'seq_mes_codigo';
        
    public function getEspecialidadePorMedico($usr_codigo=FALSE){     
        $where = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("usr"=>"usuarios"),array("usr_nome","cnes_sigla_est"))
                        ->join(array("mes"=>"medico_especialidade"),"mes.med_codigo=usr.usr_codigo",array("mes_codigo","uni_codigo"))
                        ->join(array("esp"=>"especialidade"),"mes.esp_codigo=esp.esp_codigo",array("esp_nome","esp_codigo"))
                        ->join(array("uni" => "unidade"),"uni.uni_codigo=mes.uni_codigo","uni_desc")
                        ->where("usr.usr_codigo=?", $usr_codigo)
                        ->where("mes_ativo = 'A'")
                        ->order("esp_nome"); 
        // die($where);
        return $this->fetchAll($where);
    }
    
    public function getEspecialidadePorConvenio($conv_codigo=FALSE,$usr_codigo=FALSE){
        $where = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("coni"=>"convenio_itens"),array("coni_codigo"))
                        ->join(array("usr"=>"usuarios"),"coni.usr_codigo=usr.usr_codigo",array("usr_tipo_medico"))
                        ->join(array("esp"=>"especialidade"),"coni.esp_codigo=esp.esp_codigo",array("esp_nome","esp_codigo"))
                        ->join(array("mes"=>"medico_especialidade"),"mes.esp_codigo=esp.esp_codigo and mes.med_codigo=usr.usr_codigo","")
                        ->where("coni.conv_codigo=?", $conv_codigo)
                        ->where("coni.usr_codigo=?", $usr_codigo)
                        ->where("mes_ativo != 'I'")
                        ->order("esp_nome"); 
//die($where);
        return $this->fetchAll($where);
    }
    
    public function salvar($data) {
        if (empty($data["uni_codigo"])) {
            $data["uni_codigo"] = 1;
        }
        //die($data[med_codigo]."===".$data[esp_codigo]."===".$data[uni_codigo]);

        // echo "<pre>";
        // print_r($data);
        // die();

        //die($data["uni_codigo"]);

        try {
            if($this->verificaSeJáExiste($data["med_codigo"], $data["esp_codigo"], $data["uni_codigo"]) >= 1){
                return false;
            }else{
                return parent::salvar($data);
            }
        } catch(Exception $exc) {
            //throw new Zend_Validate_Exception("Falha ao cadastrar a especialidade do Profissional: ".$exc->getMessage());
            print_r("Falha ao cadastrar a especialidade do Profissional: ".$exc->getMessage());
        }
        
    }
    
    public function confereCadEspecialidadePorMedico($medCodigo=FALSE,$espCodigo=FALSE){
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("mes"=>"medico_especialidade"),array("mes_codigo"))
                //->join(array("esp"=>"especialidade"),"mes.esp_codigo=esp.esp_codigo",array(""))
                ->where("mes.med_codigo = ?", $medCodigo)
                ->where("mes_ativo != 'I'")
                ->where("mes.esp_codigo = ?", $espCodigo);

        return $this->fetchRow($sql);
    }
    
    public function excluir($id=FALSE){
        $item = $this->fetchRow("mes_codigo=$id");
        if ($item){
            $item->delete();
        }
        return true;
    }
    
    public function atualizaStatusGeral(){
        $where = $this->select()->where("med_codigo in (select usr_codigo from usuarios where (usr_mestre IS NULL or usr_mestre <> 'S') AND (usr_modulos not in ('A','T') OR USR_MODULOS IS NULL))")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        $data = array('mes_ativo'=> "I");
        return $this->update($data, $where);
    }
    
    public function verificaSeJáExiste($usr_codigo=FALSE,$esp_codigo=FALSE,$uni_codigo=FALSE){
        if(empty($esp_codigo)) { $esp_codigo =1; }
        
        //die($usr_codigo." -- ".$esp_codigo." -- ".$uni_codigo);

        $uniC = $uni_codigo;

        //die($uniC);

        $this->deleteInativos($usr_codigo, $esp_codigo, $uniC);

        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("mes" => "medico_especialidade"),array("qtd"=>"count(*)","mes_codigo"))
                ->where("med_codigo = '$usr_codigo'")
                ->where("esp_codigo = '$esp_codigo'")
                ->where("uni_codigo = $uniC")
                ->where("mes_ativo != 'I'")
                ->group("mes_codigo");
        //die($where);
        return $this->fetchRow($where);
    }
    
    public function deleteInativos($usr_codigo,$esp_codigo,$uni_codigo){
        //echo "<pre>"; print_r($uni_codigo); die();
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from("medico_especialidade")
                ->where("med_codigo = $usr_codigo")
                ->where("esp_codigo = $esp_codigo")
                ->where("uni_codigo = $uni_codigo")
                ->where("mes_ativo = 'I'");
        //die($sql);
        $item = $this->fetchAll($sql);
        
        if ($item) {
            foreach ($item as $i){
                $i->delete();
            }
            
            return true;
        }
    }
}
