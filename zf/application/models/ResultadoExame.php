<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ResultadoExame extends Elotech_Db_Table_Abstract {

    protected $_name = 'resultadoexame';
	protected $_primary = 'res_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
    public function getColetados($usu_codigo){
        $where = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("c"=>"coleta"),array("ai.agei_codigo","col_data_coleta","p.proc_nome","col_data_entrega","p.proc_codigo","p.proc_nome","a.age_codigo",""))
                        ->join(array("ai" => "agenda_itens"),"ai.agei_codigo = c.agei_codigo", "")
                        ->join(array("a" => "agenda"),"a.age_codigo = ai.age_codigo","")
                        ->join(array("ci" => "convenio_itens"),"ci.coni_codigo = ai.coni_codigo", "")
                        ->join(array("p" => "procedimento"),"p.proc_codigo = ci.proc_codigo", "")
                        ->where("usu_codigo = ?",$usu_codigo)
                        ->where("ai.agei_codigo in (select agei_codigo from resultadoexame)")
                       // ->where("ai.usr_codigo_bioquimico IS NOT NULL")
                        ->order("col_data_coleta")
                        ->distinct(true);
        //die($where);
        return $this->fetchAll($where);
    }
        
    public function getSolicitados($usu_codigo){
        $where = $this->select()
                            ->setIntegrityCheck(FALSE)
                            ->from(array("req" => "requisicao_exames"), array("dt_requisicao" => "to_char(dt_requisicao,'DD/MM/YYYY')","proc.proc_nome","usr.usr_nome","ate.ate_codigo","req.usu_codigo","proc_codigo","age.age_codigo","req_observacao"))
                            ->join(array("proc" => "procedimento"), "proc.proc_codigo=req.proc_codigo", "")
                            ->join(array("ate" => "atendimento"), "ate.ate_codigo=req.ate_codigo", "")
                            ->join(array("age" => "agendamento"), "age.age_codigo=ate.age_codigo", "")
                            ->join(array("usr" => "usuarios"),"ate.med_codigo=usr.usr_codigo", "")
                            ->where("req.usu_codigo=?",$usu_codigo)
                            ->order("dt_requisicao");
        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getResultados($agei_codigo){
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("c"=>"coleta"),"p.proc_codigo")
                            ->join(array("ai"=>"agenda_itens"),"ai.agei_codigo = c.agei_codigo")
                            ->join(array("a" => "agenda"),"a.age_codigo = ai.age_codigo")
                            ->join(array("ci" => "convenio_itens"),"ci.coni_codigo = ai.coni_codigo", "")
                            ->join(array("p" => "procedimento"),"p.proc_codigo = ci.proc_codigo", "")
                            ->join(array("usu"=>"usuario"),"usu.usu_codigo=a.usu_codigo",array("usu_sexo","idade"=>"((DATE_PART('YEAR', AGE(NOW(), usu.usu_datanasc))*12)+DATE_PART('MONTH', AGE(NOW(), usu.usu_datanasc)))"))
                            ->where("ai.agei_codigo=?",$agei_codigo);
            
            $usu = $this->fetchRow($where);
            unset($where);		

            $onValorRef = "v.ite_codigo=i.ite_codigo";
            $onValorRef .= " AND (v.vlr_sexo IS NULL OR v.vlr_sexo = '{$usu->usu_sexo}')";

            //if($usu->proc_codigo == 4629)
            $onValorRef .= " AND {$usu->idade} BETWEEN COALESCE(v.vlr_faixa_etaria_inicio,0) AND COALESCE(v.vlr_faixa_etaria_fim,9999999)";
            //$onValorRef .= "AND (vlr_faixa_etaria_inicio <= ".intval($usu->idade)." OR vlr_faixa_etaria_inicio IS NULL)";
            //$onValorRef .= "AND (vlr_faixa_etaria_fim > ".intval($usu->idade)." OR vlr_faixa_etaria_fim IS NULL)";

            $where = $this->select(FALSE)
                            ->distinct()
                            ->setIntegrityCheck(FALSE)
                            ->from(array("r"=>"resultadoexame"),array("vlr_valor","res_observacao","vlr_valor_m3"))
                            ->join(array("p"=>"procedimento"),"p.proc_codigo=r.proc_codigo","proc_nome")
                            ->join(array("i"=>"itensanalise"),"i.ite_codigo=r.ite_codigo",array("ite_itemdoexame","ite_tipo_medida","historico","ite_ordem","ite_codigo"))
                            ->joinLeft(array("v"=>"valoresdereferencia"), $onValorRef,"vlr_valordereferencia")
                            ->joinLeft(array("s"=>"subexame"),"s.sex_codigo=i.sex_codigo",array("sex_codigo","sex_subexame"))
                            ->where("r.agei_codigo=?",$agei_codigo)
                            ->order("i.ite_ordem")
                            ->order("s.sex_codigo")
                            ->order("i.ite_codigo");
            
            Zend_Registry::get("logger")->log($where->__toString(), Zend_Log::INFO);
            
            return $this->fetchAll($where);
    }
    
    public function getItensHistorico($proc_codigo=FALSE,$usu_codigo=FALSE,$age_codigo=FALSE){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array("res"=>"resultadoexame"),array("vlr_valor"))
                      ->join(array("ai"=>"agenda_itens"),"ai.agei_codigo=res.agei_codigo","")
                      ->join(array("a"=>"agenda"),"a.age_codigo=ai.age_codigo","")
                      ->join(array("ia"=>"itensanalise"),"ia.ite_codigo=res.ite_codigo",array("ite_codigo","ite_itemdoexame","ite_tipo_medida"))
                      ->join(array("col"=>"coleta"),"col.agei_codigo=ai.agei_codigo",array("col_data_coleta"))
                      ->where("usu_codigo=$usu_codigo")
                      ->where("proc_codigo=$proc_codigo")
                      ->where("a.age_codigo <> $age_codigo")
                      ->where("historico='t'")
                      ->order("ite_ordem")
                      ->order("ite_codigo")
                      ->order("col_data_coleta desc");
        
        //die($where);
        return $this->fetchAll($where);
    }


}
