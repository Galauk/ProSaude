<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_RequisicaoMateriais extends Elotech_Db_Table_Abstract {

    protected $_name = 'requisicao_materiais';
    protected $_primary = 'rem_codigo';
    protected $_sequence = 'requisicao_materiais_rem_codigo_seq';
	
    const SOLICITADO = "S"; 
	
    public function salvar(array $data) {
        // validação:
        /*if(!in_array($data['rem_status'], array(self::SOLICITADO))){
                throw new Zend_Validate_Exception("\"mov_tipo\" precisa ser: SOLICITADO");
        }*/
        $this->emptyToUnset($data);
        if(!$data[rem_codigo])
            $this->notEmpty(array("set_codigo_sol","set_codigo_req"), $data);
	try{
            return parent::salvar($data);
        } catch(Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao inserir a requisição! "+$exc->getMessage());
        }
    }

    public function atualizaStatusRequisicao($dados){
        try{
            return parent::salvar($dados);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao atualizar o status da requisição! "+$exc->getMessage());
        }
    }
        
    public function getRequisicoes($limit=FALSE,$term=FALSE,$rem_status=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("rem"=>"requisicao_materiais"),array("rem_codigo",
                                                                        "rem_data",
                                                                        "set_codigo_sol",
                                                                        "set_codigo_req" ,
                                                                        "rem_situacao" => "(CASE WHEN rem_status = 'S' THEN 'Solicitado' WHEN rem_status = 'E' THEN 'Enviado' WHEN rem_status = 'F' THEN 'Finalizado' END)",
                                                                        "rem_status",
                                                                        "rem_observacao"))
                    ->join(array("remi"=>"requisicao_materiais_itens"),"remi.rem_codigo=rem.rem_codigo","")
                    ->join(array("set_req"=>"setor"),"set_req.set_codigo=rem.set_codigo_req","set_nome as set_nome_req")
                    ->join(array("set_sol"=>"setor"),"set_sol.set_codigo=rem.set_codigo_sol","set_nome as set_nome_sol")
                    ->join(array("usr"=>"usuarios"),"usr.usr_codigo=rem.usr_codigo","usr_nome")
                    ->order("rem_data DESC")
                    ->order("rem_codigo DESC");
        
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $tbUsrSet = new Application_Model_UsuariosSetores();
        foreach($tbUsrSet->getSetoresPorUsuario($usr_codigo) as $setor){
            $setores .= $setor[set_codigo].",";
        }
        $setores = substr($setores, 0, -1);
        
        
        if($term){
            $where->where("set_codigo_req in ($setores) OR set_codigo_sol in ($setores)");
            if($this->ValidaData($term)){
                $where->where("rem_data::date='$term'");
            }else if(is_numeric($term)){
                $where->where("rem.req_codigo=$term");
            }else{
                $where->where("rem_observacao ilike'%$term%' OR set_sol.set_nome ilike '%$term%' OR set_req.set_nome ilike '%$term%' OR usr_nome ilike '%$term%'");
            }
        }else{
            $where->where("set_codigo_req in ($setores)");
        }
        
        if($rem_status != "A" && $rem_status != ""){
            $where->where("rem_status='$rem_status'");
        }


        

        if($limit)
            $where->limit($limit);
        
        //die($where);
        return $this->fetchAll($where);

    }
        
    public function ValidaData($dat){
            $data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como referência
            $d = $data[0];
            $m = $data[1];
            $y = $data[2];

            // verifica se a data é válida!
            // 1 = true (válida)
            // 0 = false (inválida)
            if($y == ""){
                return false;
            }else{
                $res = checkdate($m,$d,$y);
                if ($res == 1){
                  return true;
                } else {
                   return false;
                }
            }
    }

    public function getRequisicao($rem_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("rem"=>"requisicao_materiais"),array("rem_data","rem_status","rem_codigo","to_char(rem_data,'dd/mm/yyyy') as rem_data","set_codigo_sol","set_codigo_req","rem_observacao"))
                      ->join(array("set_sol"=>"setor"),"rem.set_codigo_sol=set_sol.set_codigo","set_nome as set_nome_sol")
                      ->join(array("set_req"=>"setor"),"rem.set_codigo_req=set_req.set_codigo","set_nome as set_nome_req")
                      ->join(array("usr"=>"usuarios"),"usr.usr_codigo=rem.usr_codigo",array("usr_nome","usr_codigo"))
                      ->where("rem_codigo=$rem_codigo");
        return $this->fetchRow($where);
    }
    
    // Pega os dados da requisição itens e etc ...
    public function getDadosRequisicao($codRequisicao=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("rem"=>"requisicao_materiais"),array("set_codigo_sol"))
                    ->join(array("remi"=>"requisicao_materiais_itens"),"rem.rem_codigo=remi.rem_codigo",array("pro_codigo","remi_codigo"))
                    ->join(array("remil"=>"requisicao_materiais_itens_lote"),"remi.remi_codigo=remil.remi_codigo",array("remi_codigo","remil_quantidade","remil_lote"))
                    ->join(array("sal"=>"saldo"),"rem.set_codigo_sol=sal.set_codigo AND remil.remil_lote=sal.sal_lote AND remi.pro_codigo=sal.pro_codigo",array("sal_validade","sal_dose_lote"))
                    ->where("rem.rem_codigo =?",$codRequisicao)
                    ->where("remi.remi_status = 'C'");
        
        return $this->fetchAll($sql);
    }
    
    
    
    public function getRequisicoesPorEnvio($limit=FALSE,$term=FALSE,$rem_status=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->distinct()
                      ->from(array("rem"=>"requisicao_materiais"),array("rem_codigo",
                                                                        "rem_data",
                                                                        "set_codigo_sol",
                                                                        "set_codigo_req" ,
                                                                        "rem_situacao" => "(CASE WHEN rem_status = 'S' THEN 'Solicitado' WHEN rem_status = 'E' THEN 'Enviado' WHEN rem_status = 'F' THEN 'Finalizado' END)",
                                                                        "rem_status",
                                                                        "rem_observacao"))
                    ->join(array("remi"=>"requisicao_materiais_itens"),"remi.rem_codigo=rem.rem_codigo","")
                    ->join(array("set_req"=>"setor"),"set_req.set_codigo=rem.set_codigo_req","set_nome as set_nome_req")
                    ->join(array("set_sol"=>"setor"),"set_sol.set_codigo=rem.set_codigo_sol","set_nome as set_nome_sol")
                    ->join(array("usr"=>"usuarios"),"usr.usr_codigo=rem.usr_codigo","usr_nome")
                    ->order("rem_data DESC")
                    ->order("rem_codigo DESC");
        
        $tbUsr = new Application_Model_Usuarios();
        $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
        $tbUsrSet = new Application_Model_UsuariosSetores();
        foreach($tbUsrSet->getSetoresPorUsuario($usr_codigo) as $setor){
            $setores .= $setor[set_codigo].",";
        }
        $setores = substr($setores, 0, -1);
        
        
        if($term){
            $where->where("set_codigo_req in ($setores) OR set_codigo_sol in ($setores)");
            if($this->ValidaData($term)){
                $where->where("rem_data::date='$term'");
            }else if(is_numeric($term)){
                $where->where("rem.req_codigo=$term");
            }else{
                $where->where("rem_observacao ilike'%$term%' OR set_sol.set_nome ilike '%$term%' OR set_req.set_nome ilike '%$term%' OR usr_nome ilike '%$term%'");
            }
        }else{
            $where->where("set_codigo_sol in ($setores)");
        }
        
        if($rem_status != "A" && $rem_status != ""){
            $where->where("rem_status='$rem_status'");
        }


        

        if($limit)
            $where->limit($limit);
        
        //die($where);
        return $this->fetchAll($where);

    }
    
    

}
