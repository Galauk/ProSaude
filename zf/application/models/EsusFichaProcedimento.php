<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EsusFichaProcedimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'esus_ficha_procedimento';
    protected $_primary = 'efp_codigo';

    public function getDadosPorUuid($uuid = FALSE) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("efp" => "esus_ficha_procedimento"), array("age_codigo", "efp_ine", "efp_profissional_cns", "efp_num_cartao_sus", "efp_dtnascimento", "efp_sexo"))
                ->join(array("tla" => "tb_local_atend"), "efp.co_local_atend=tla.co_local_atend", array("no_local_atend", "co_local_atend"))
                ->where("uuid_ficha = ?", $uuid);
        //die($sql);
        return $this->fetchAll($sql);
    }

    public function getDadosPorId($id) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->distinct()
                ->from(array("efp" => "esus_ficha_procedimento"), array("age_codigo", "efp_ine", "efp_profissional_cns", "efp_num_cartao_sus", "efp_dtnascimento", "efp_sexo"))
                ->join(array("tla" => "tb_local_atend"), "efp.co_local_atend=tla.co_local_atend", array("no_local_atend", "co_local_atend"))
                ->where("age_codigo = ?", $id);
        //die($sql);
        return $this->fetchRow($sql);
    }

    public function atualizaDadosFicha($dados, $ageCodigo) {
        //echo "<pre>".print_r($dados)."</pre>";
        //die("aaaaaa");
        $where['age_codigo = ?'] = $ageCodigo;
        try {
            return $this->update($dados, $where);
        } catch (Exception $exc) {
            throw new Zend_Validade_Exception("Error Processing Request: " . $exc->getMessage());
        }
    }

    public function listaDadosPorProcedimento($codigoProcedimento) {
        $sql = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array("efp" => "esus_ficha_procedimento"), array("efp.proc_codigo"))
                ->where("efp.proc_codigo = $codigoProcedimento");

        return $this->fetchAll($sql);
    }

    public function atualizaProcedimentoDadosEsus($codigoProcNovo, $codigoProcAnterior) {
        $data = array("proc_codigo" => $codigoProcNovo);
        $where = $this->select()->where("proc_codigo = $codigoProcAnterior")->getPart(Zend_Db_Table_Select::WHERE);
        $where = $where[0];
        //echo"<pre>".print_r($where,1); echo"<pre>".print_r($data,1);die();
        return $this->update($data, $where);
    }
    
    public function anularCampoUuidPeloUuid($UUID){

            $data = array("uuid_ficha" => "");
            $where = $this->select()->where("uuid_ficha = '$UUID'")->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

         //   Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);
                
            return $this->update($data, $where);
        
    }
    
    public function getQuantidadeFichaProcedimento($data_ini=false,$data_fim=false,$unidade=false){
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("efp" => "esus_ficha_procedimento"), array("count(*) total"))
        ->join(array("uni" => "unidade"),"efp.efp_cnes::integer = uni.uni_cnes", "uni.uni_codigo as uni_codigo")
        ->group(array("uni.uni_codigo"));
        if($data_ini){
            $where->where("efp_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("efp_dtatendimento <= ?",$data_fim);
        }
        if($unidade){
            $where->where("uni_codigo = ?",$unidade);
        }
        return $this->fetchAll($where);
    }
    
    public function getQuantidadeFichaProcedimentoPmaq($data_ini=false,$data_fim=false,$ine=false){
        $procedimento = array('0301100020','0301060037','0301010137','0101010010','0101040024',
            '0201020033','0201020041','0301010064','0301100039','0401010015',
            '0401010023','0401010031','ABPG011','0214010015','0301100101',
            '0404010270','0404010300','0401010112','0301100152','0404010342',
            '0301100187','0201020050','0211060275');
        $where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("efp" => "esus_ficha_procedimento"), array("count(*) total"))
        ->join(array("pro" => "procedimento"),"efp.proc_codigo = pro.proc_codigo", "")
        ->joinLeft(array("ue" => "tb_equipe"),"efp.efp_ine = ue.nu_ine", "")
        ->where("pro.proc_codigo_sus IN (?)", $procedimento);
        
        
        if($data_ini){
            $where->where("efp_dtatendimento >= ?",$data_ini);
        }
        if($data_fim){
            $where->where("efp_dtatendimento <= ?",$data_fim);
        }
        if($ine){
            $where->where("nu_ine = ?",$ine);
        }
        //die($where);
        return $this->fetchRow($where);
    }

}
