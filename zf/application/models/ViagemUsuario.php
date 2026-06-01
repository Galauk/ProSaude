<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ViagemUsuario extends Elotech_Db_Table_Abstract {

    protected $_name = 'viagem_usuario';
    protected $_primary = 'viausu_codigo';   

    /**
     * Persiste um item (insert ou update)
     * @param array $data array de chave=>valor, cada chave corresponde a um atributo
     * @return int primary key do item (nextVal para insert) 
     */
    public function salvar(array $data) {
        $this->valoresPadrao($data);
        $this->notEmpty(array("usu_codigo","viausu_alimentacao","viausu_pernoite","cid_codigo_origem","cid_codigo_destino"), $data);
        $this->emptyToUnset($data);     
        return parent::salvar($data);
    }
    
    /**
    * Valores padrão do insert/update
    * @param array $data valores do insert
    */
    private function valoresPadrao(&$data) {
        $tbUsr = new Application_Model_Usuarios();
        $data['usr_codigo_cadastro'] = $tbUsr->getUsrAtual()->usr_codigo;          
    }
   
    /**
     * Exclui um veiculo	
     * @param int $vei_codigo Código da veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function excluir($viausu_codigo=FALSE) {
        $item = $this->fetchRow("viausu_codigo=$viausu_codigo");
        if ($item) {
            $item->delete();
        }
    }

    /**
     * Busca ViagemUsuario	
     * @param int $dados Dados poder ser o nome do setor ou a descricai da Veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function getViagemUsuario($via_codigo) {
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("vu"=>"viagem_usuario"),array("viausu_codigo","uni_codigo_paciente_embarque","dom_codigo_paciente_embarque","outros_paciente_embarque","necessita_maca"))
        ->join(array("usu"=>"usuario"),"usu.usu_codigo = vu.usu_codigo",array("usu_nome","usu_celular"))
        
        ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=usu.dom_codigo","dom_telefone")

        ->joinLeft(array("rua"=>"rua"),"dom.rua_codigo = rua.rua_codigo",array("rua_nome"))
        ->joinLeft(array("bai"=>"bairro"),"dom.bai_codigo = bai.bai_codigo",array("bai_nome"))

        ->join(array("cid1"=>"cidade"), "vu.cid_codigo_origem = cid1.cid_codigo",array("cid1.cid_nome as cid_nome_origem"))
        ->join(array("cid2"=>"cidade"), "vu.cid_codigo_destino = cid2.cid_codigo",array("cid2.cid_nome as cid_nome_destino"))
        ->join(array("via"=>"viagem"), "via.via_codigo = vu.via_codigo",array("via.via_local","via.via_hora"))
        ->joinLeft(array("uni"=>"unidade"), "vu.uni_codigo_paciente_embarque = uni.uni_codigo",array("uni_desc"))		
        ->where("vu.via_codigo=?",$via_codigo);
        // die($where);
        
        return $this->fetchAll($where);
    }
    
    /**
     * Busca ViagemUsuario	
     * @param int $id código do usuario da viagem
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function getViagemPorUsuario($id) {
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("vu"=>"viagem_usuario"),array("viausu_codigo","viausu_alimentacao","viausu_pernoite","viausu_despesas","viausu_km","viausu_observacao","necessita_maca","dom_codigo_paciente_embarque","uni_codigo_paciente_embarque","outros_paciente_embarque","local_embarque_do_paciente","clinica","horario", "rotas_transporte"))
        ->join(array("usu"=>"usuario"),"usu.usu_codigo = vu.usu_codigo",array("usu.usu_nome","usu_codigo"))                            
        ->join(array("cid1"=>"cidade"), "vu.cid_codigo_origem = cid1.cid_codigo",array("cid1.cid_nome as busca1","cid1.cid_codigo as cid_codigo"))
        ->join(array("cid2"=>"cidade"), "vu.cid_codigo_destino = cid2.cid_codigo",array("cid2.cid_nome as busca2","cid2.cid_codigo as cid_codigo_2"))
        ->join(array("via"=>"viagem"), "via.via_codigo = vu.via_codigo",array("via_codigo","via_data_ida","via.via_local","via.via_hora"))
        ->join(array("vei"=>"veiculo"),"via.vei_codigo=vei.vei_codigo",array("vei_descricao"))
        ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=usu.dom_codigo","dom_numero")
        ->joinLeft(array("rua"=>"rua"),"dom.dom_codigo = rua.rua_codigo",array("rua_nome"))
        ->joinLeft(array("bai"=>"bairro"),"dom.dom_codigo = bai.bai_codigo",array("bai_nome"))
        ->joinLeft(array("uni"=>"unidade"), "vu.uni_codigo_paciente_embarque = uni.uni_codigo",array("uni_desc","uni_codigo"))
        ->where("vu.viausu_codigo=?",$id);

        return $this->fetchRow($where);
    }
    
    public function getViagemPorUsuarioJson($id) {
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("vu"=>"viagem_usuario"),array("viausu_codigo","viausu_alimentacao","viausu_pernoite","viausu_despesas","viausu_km"))                            
        ->join(array("via"=>"viagem"), "via.via_codigo = vu.via_codigo",array("via_codigo","via_data_ida","via.via_local","via.via_hora"))                            
        ->where("vu.via_codigo=?",$id);
         
        // die($where);
        return $this->fetchAll($where);
    }
    
    /**
     * Busca Usuarios daquela Viagem	
     * @param int $id codigo da viagem
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function getUsuariosDaViagem($id) {
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("vu"=>"viagem_usuario"),array("viausu_codigo"))
        ->join(array("usu"=>"usuario"),"usu.usu_codigo = vu.usu_codigo",array("usu.usu_nome","usu_codigo"))                                                       
        ->where("vu.via_codigo=?",$id);
       
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getDadosFullDaViagem($id) {
        //   $where = $this->select(FALSE)
        //                ->setIntegrityCheck(FALSE)
        //                ->distinct()
        //                ->from(array("via"=>"viagem"),"")
        //                ->join(array("vu"=>"viagem_usuario"),"vu.via_codigo = via.via_codigo",array("vu.viausu_codigo","vu.viausu_despesas","COALESCE(viausu_observacao,NULL,'---') as viausu_observacao"))
        //                ->join(array("usu"=>"usuario"),"usu.usu_codigo = vu.usu_codigo",array("usu.usu_codigo","usu.usu_nome","usu_celular","usu_rg","usu_cpf"))
        //                ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=usu.dom_codigo","COALESCE(dom_telefone,NULL,'---') as dom_telefone")
        //                ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=via.usr_codigo",array("usr.usr_nome"))
        //                ->joinLeft(array("vei"=>"veiculo"),"vei.vei_codigo=via.vei_codigo",array("vei.vei_descricao"))
        //                ->where("vu.via_codigo=?",$id)
        //                ->order(array("usu.usu_nome"));
        // Consulta antiga

        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->distinct()
        ->from(array("via"=>"viagem"),"")
        ->join(array("vu"=>"viagem_usuario"),"vu.via_codigo = via.via_codigo",array("vu.viausu_codigo","vu.viausu_despesas","necessita_maca", "dom_codigo_paciente_embarque","outros_paciente_embarque","uni_codigo_paciente_embarque","clinica","horario","COALESCE(viausu_observacao,NULL,'---') as viausu_observacao"))
        ->join(array("usu"=>"usuario"),"usu.usu_codigo = vu.usu_codigo",array("usu.usu_codigo","usu.usu_nome","usu_celular"))
        ->joinLeft(array("rt"=>"rotas_transporte"),"via.vei_codigo = rt.veicodigo", "rt.rotdescri")
        ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=usu.dom_codigo",array("dom_numero","COALESCE(dom_telefone,NULL,'---') as dom_telefone"))
        ->joinLeft(array("ua"=>"usuario_acompanhante"), "ua.viausu_codigo=vu.viausu_codigo","")
        ->joinLeft(array("usu2"=>"usuario"), "usu2.usu_codigo=ua.usu_codigo","usu2.usu_nome as nome_acompanhante")
        ->joinLeft(array("uni"=>"unidade"), "uni.uni_codigo = vu.uni_codigo_paciente_embarque", "uni_desc")
        ->joinLeft(array("rua"=>"rua"), "rua.rua_codigo = dom.rua_codigo", "rua_nome")
        ->joinLeft(array("bai"=>"bairro"), "bai.bai_codigo = dom.bai_codigo", "bai_nome")
        ->joinLeft(array("cid"=>"cidade"), "cid.cid_codigo = vu.cid_codigo_destino", "cid_nome")
        ->where("vu.via_codigo=?",$id)
        ->order(array("usu.usu_nome","usu2.usu_nome"));
        // ->group(array("cid_nome"));
                     
        // die($where);
       
       return $this->fetchAll($where);
    }
    
    public function getBalancoViagem($id){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("vu"=>"viagem_usuario"),array("(select count(*) from viagem_procedimento_usuario vpu2 where vpu2.viausu_codigo = vu.viausu_codigo and vpu2.proc_codigo = vpu.proc_codigo) as qtde","viausu_codigo","viausu_despesas"))
        ->join(array("usu"=>"usuario"),"usu.usu_codigo = vu.usu_codigo",array("usu.usu_nome","usu_codigo"))                                                       
        ->join(array("ua"=>"usuario_acompanhante"),"ua.viausu_codigo = vu.viausu_codigo", "")
        ->join(array("usua"=>"usuario"),"usua.usu_codigo = ua.usu_codigo",array("usua.usu_nome as usu_nome_acompanhante"))
        ->joinLeft(array("vpu"=>"viagem_procedimento_usuario"),"vpu.viausu_codigo = vu.viausu_codigo","")
        ->join(array("proc"=>"procedimento"),"proc.proc_codigo = vpu.proc_codigo",array("proc_nome","proc_vlsa"))
        ->where("vu.via_codigo=?",$id)
        ->order(array("usu.usu_nome","usua.usu_nome","proc_nome"));
        
        return $this->fetchAll($where);
    }
    
    public function getDadosAcompanhantesDaViagem($viausu_codigo){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("ua"=>"usuario_acompanhante"),"")
        ->join(array("usu"=>"usuario"),"ua.usu_codigo=usu.usu_codigo",array("usu.usu_nome","usu_rg","usu_cpf"))
        ->where("viausu_codigo=?",$viausu_codigo);
        return $this->fetchAll($where);
    }
    
    
    /**
     * Busca ViagemUsuario	
     * @param int $dados Dados poder ser o nome do setor ou a descricai da Veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    // public function pesquisar_old($via_data=FALSE,$cid_codigo_origem=FALSE,$cid_codigo_destino=FALSE,$vei_codigo=FALSE) {
    //     $arrayOrigem = array();
    //     $arrayDestino = array();
    //     $where = $this->select(FALSE)
    //      ->setIntegrityCheck(FALSE)
    //      ->distinct()
    //      ->from(array("via"=>"viagem"),array("via.via_codigo","via_data","via_codigo","via_local","via_hora"))
    //      ->join(array("usr"=>"usuarios"),"usr.usr_codigo=via.usr_codigo" ,"usr_nome")
    //      ->join(array("vei"=>"veiculo"), "vei.vei_codigo=via.vei_codigo",array("vei_placa", "vei_descricao", "vei_capacidade"))
    //      ->joinLeft(array("vu"=>"viagem_usuario"), "vu.via_codigo=via.via_codigo",array("capacidade_atual"=>'(count(vu.viausu_codigo) + count(ua.viausu_codigo))'))
    //      ->joinLeft(array("cid1"=>"cidade"), "cid1.cid_codigo=vu.cid_codigo_origem",$arrayOrigem)
    //      ->joinLeft(array("cid2"=>"cidade"), "cid2.cid_codigo=vu.cid_codigo_destino",$arrayDestino)
    //      ->joinLeft(array("ua"=>"usuario_acompanhante"),"vu.viausu_codigo=ua.viausu_codigo")
    //      ->order("via.via_codigo desc")
    //      ->group(array("vei_capacidade","via.via_codigo", "usr.usr_nome", "vei.vei_placa", "vei.vei_descricao","ua.acom_codigo"));
    //     
    //     if($via_data){
    //         $where->where("via_data=?",$via_data);
    //     }
    //     if($cid_codigo_origem){
    //         array_push($arrayOrigem, "cid.id_nome");
    //         $where->where("cid_codigo_origem=?",$cid_codigo_origem);
    //     }
    //     if($cid_codigo_destino){
    //         array_push($arrayOrigem, "cid.id_nome");
    //         $where->where("cid_codigo_destino=?",$cid_codigo_destino);
    //     }
    //     if($vei_codigo){
    //         $where->where("ve.ei_codigo=?",$vei_codigo);
    //     }
    //     // die($where);
    //     return $this->fetchAll($where);
    // }
    
    public function pesquisar($via_data=FALSE,$cid_codigo_origem=FALSE,$cid_codigo_destino=FALSE,$vei_codigo=FALSE) {
        
        $arrayOrigem = array();
        $arrayDestino = array();
        
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->distinct("rt.veicodigo")
        ->from(
            array("via"=>"viagem"),
            array(
                "via.via_codigo",
                "via_data_ida",
                "via_codigo",
                "via_local",
                "via_hora",
                "(select count (*) from viagem_usuario where via_codigo = via.via_codigo) AS capacidade_atual"
            )
        )
        ->join(array("usr"=>"usuarios"),"usr.usr_codigo=via.usr_codigo" ,"usr_nome")
        ->join(array("vei"=>"veiculo"), "vei.vei_codigo=via.vei_codigo",array("vei_placa", "vei_descricao", "vei_capacidade"))
        ->joinLeft(array("rt"=>"rotas_transporte"), "rt.veicodigo=via.vei_codigo", array("rotdescri"))
        ->order("via.via_data_ida asc")
        ->group(array("vei_capacidade","via.via_codigo", "usr.usr_nome", "vei.vei_placa", "vei.vei_descricao", "rt.rotdescri"));
        // die($where);

        if($via_data){
            $where->where("via_data_ida = ?",$via_data);
        } else {
            $where->where("via_data_ida >= CURRENT_DATE");
        }

        if($cid_codigo_origem){
            array_push($arrayOrigem, "cid.id_nome");
            $where->where("cid_codigo_origem=?",$cid_codigo_origem);
        }

        if($cid_codigo_destino){
            array_push($arrayOrigem, "cid.id_nome");
            $where->where("cid_codigo_destino=?",$cid_codigo_destino);
        }

        if($vei_codigo){
            $where->where("vei.ei_codigo=?",$vei_codigo);
        }
        return $this->fetchAll($where);           
    }       
}