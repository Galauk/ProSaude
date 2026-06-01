<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbCdsFichaAtivCol extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_cds_ficha_ativ_col';
    protected $_primary = 'co_cds_ficha_ativ_col';
    protected $_sequence = 'seq_co_cds_ficha_ativ_col';
    
    public function getDados(){
        $tbUsr = new Application_Model_Usuarios();
        $uni_codigo = $tbUsr->getUsrAtual()->uni_codigo;
        
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbf" => "tb_cds_ficha_ativ_col"))
                ->join(array("usr"=>"usuarios"),"tbf.usr_codigo=usr.usr_codigo",array("usr_nome"))
                //->where("tbf.uni_codigo=$uni_codigo")
                ->order(array("dt_ativ_col DESC","co_cds_ficha_ativ_col DESC"));
        return $this->fetchAll($sql);
    }
    
    public function busca($busca=FALSE,$tipoBusca=FALSE){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("tbf" => "tb_cds_ficha_ativ_col"))
                    ->join(array("usr"=>"usuarios"),"tbf.usr_codigo=usr.usr_codigo",array("usr_nome"));
        switch ($tipoBusca) {
            case 1: $sql->where("usr.usr_nome ILIKE '%$busca%'"); break;
            case 2: $sql->where("tbf.dt_ativ_col = '$busca'"); break;
            case 3: $sql->where("tbf.cod_cnes_unidade = '$busca'"); break;
            case 4: $sql->where("tbf.cod_equipe_ine = '$busca'"); break;
        }
        return $this->fetchAll($sql);
    }
    
    public function getDadosPorId($codFicha=FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbf" => "tb_cds_ficha_ativ_col"))
                ->join(array("usr"=>"usuarios"),"tbf.usr_codigo=usr.usr_codigo",array("usr_nome"))
                ->where("co_cds_ficha_ativ_col =?",$codFicha);
        return $this->fetchRow($sql);
    }
    
    public function salvar($dados) {
        $this->emptyToUnset($dados);
        // echo "<pre>";print_r($dados);die();
        try{
            return parent::salvar($dados);
        } catch (Exception $ex) {
            die($ex->getMessage());
            throw new Zend_Validate_Exception("Falha ao salvar Ficha: ".$ex->getMessage());
        }
        return true;
    }
    
    //ID #106475
    public function getAtividadesParaRelatorioAtividadeColetiva($dadosParaConsulta = false){
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tcfac" => "tb_cds_ficha_ativ_col"),array("co_cds_ficha_ativ_col","data_atividade" =>"to_char(dt_ativ_col, 'DD/MM/YYYY')", "hora_inicio"=>"to_char(hr_inicio, 'HH24:MI')", "hora_fim"=>"to_char(hr_fim, 'HH24:MI')","cod_equipe_ine","qt_participante_ativ","uni_codigo","(select count(uni_codigo) from tb_cds_ficha_ativ_col where dt_ativ_col=tcfac.dt_ativ_col and uni_codigo in (tcfac.uni_codigo) group by uni_codigo)as total","usr.usr_nome","uni.uni_desc","tctac.no_cds_tipo_ativ_col"
                ))
                ->join(array("usr"=>"usuarios"),"tcfac.usr_codigo = usr.usr_codigo",array("usr_nome"))
                ->join(array("uni"=>"unidade"),"tcfac.uni_codigo = uni.uni_codigo",array("uni_desc"))
                ->join(array("tctac"=>"tb_cds_tipo_ativ_col"),"tcfac.tp_cds_ativ_col = tctac.co_cds_tipo_ativ_col",array("no_cds_tipo_ativ_col"));
                
        //As informações padrões, 'Todas' e 'Todos' tem o valor '0' para facilitar a comparação
        if(!$dadosParaConsulta[responsavel] == 0){
            $sql->where("tcfac.usr_codigo = '$dadosParaConsulta[responsavel]'");
        }
        if(!$dadosParaConsulta[unidade] == 0){
            $sql->where("tcfac.uni_codigo = '$dadosParaConsulta[unidade]'");
        }else{
            //caso não selecionar unidade, assim pegando todas, ordenar por codigo da unidade
            // para facilitar a construção do relatorio
            $sql->order('tcfac.uni_codigo ASC');
        }
        if($dadosParaConsulta[dataInicial] && $dadosParaConsulta[dataFinal]){
            //Transforma a data informada em timestamp, para comprar com a data no banco
            $sql->where("tcfac.dt_ativ_col between to_timestamp('$dadosParaConsulta[dataInicial]', 'DD/MM/YYYY') and to_timestamp('$dadosParaConsulta[dataFinal]', 'DD/MM/YYYY')");
        }
        if(!$dadosParaConsulta[atividade] == 0){
            $sql->where("tcfac.tp_cds_ativ_col = '$dadosParaConsulta[atividade]'");
        }
        // die($sql);
        return $this->fetchAll($sql);
    }
    //ID #106475
}
