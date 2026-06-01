<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_BPA extends Application_Model_DbTable_BPA {

    protected $_name = 'bpa';
    protected $_primary = 'bpa_codigo';
    protected $_dependentTables = array();

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
        //throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        // print_r($data); die();
        return parent::salvar($data);
    }

    public function relConsolidado($uni_cnes = FALSE, $data_inicial = FALSE, $data_final = FALSE, $competencia = FALSE){
        $where1 = $this->select()
                 ->setIntegrityCheck(FALSE)
                 ->from("bpa","COUNT(DISTINCT (bpa_codigo)) AS total")
                 ->join(array("usu"=>"usuario"), "usu.usu_codigo=bpa.usu_codigo","")
                 ->join(array("uni"=>"unidade"), "uni.uni_codigo=bpa.uni_codigo",array("uni_desc"))
                 ->join(array("p"=>"procedimento"),"p.proc_codigo=bpa.proc_codigo",array("p.proc_codigo_sus","p.proc_codigo","p.proc_nome"))
                 ->join(array("me"=>"medico_especialidade"), "me.med_codigo=bpa.usr_codigo","")
                 ->join(array("esp"=> "especialidade"),"esp.esp_codigo=me.esp_codigo","MIN(esp.cod_cbo) AS m_cbo")
                 ->join(array("rlr"=>"rl_procedimento_registro"),"rlr.co_procedimento=p.proc_codigo_sus","")
                 ->where("rlr.dt_competencia=?",$competencia)
                 ->where("rlr.co_registro=?",1)                
                 
                 ->where("esp.esp_codigo in (SELECT esp_codigo FROM agendamento)")
                 ->group(array("p.proc_codigo_sus","p.proc_codigo","uni_desc","p.proc_nome","esp.cod_cbo"));
        
        $where2 = $this->select()
                 ->setIntegrityCheck(FALSE)
                 ->from("bpa","COUNT(DISTINCT(bpa_codigo)) AS total")
                 ->join(array("usu"=>"usuario"), "usu.usu_codigo=bpa.usu_codigo","")
                 ->join(array("med"=>"medico"), "med.med_codigo=bpa.med_codigo",array("med_nome"))
                 ->join(array("p"=>"procedimento"),"p.proc_codigo=bpa.proc_codigo",array("p.proc_codigo_sus","p.proc_codigo","p.proc_nome"))
                 ->join(array("me"=>"medico_especialidade"), "me.med_codigo=bpa.usr_codigo","")
                 ->join(array("esp"=> "especialidade"),"esp.esp_codigo=me.esp_codigo","MIN(esp.cod_cbo) AS m_cbo")
                 ->join(array("rlr"=>"rl_procedimento_registro"),"rlr.co_procedimento=p.proc_codigo_sus","")
                 ->where("rlr.dt_competencia=?",$competencia)
                 ->where("rlr.co_registro=?",1)                
                 ->where("esp.esp_codigo in (SELECT esp_codigo FROM agendamento)")
                 ->group(array("p.proc_codigo_sus","p.proc_codigo","med_nome","p.proc_nome","esp.cod_cbo"));
        
        if ($uni_cnes) {
            $where1->where("uni.uni_codigo=?",$uni_cnes);
            $where2->where("med.med_codigo=?",$uni_cnes);
        }
        if($data_inicial){
         $where1->where("bpa_data >=?",$data_inicial);
         $where2->where("bpa_data >=?",$data_inicial);
        }
        if($data_inicial){
         $where1->where("bpa_data<=?",$data_final);
         $where2->where("bpa_data<=?",$data_final);
        }
                 
         $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->union(array($where1, $where2), Zend_Db_Select::SQL_UNION_ALL)
                                 ->order(array("uni_desc","proc_nome","m_cbo"));
         
       //die($where);
        return $this->fetchAll($where);
        
    }
    
    public function calculaControle($codProcedimento=FALSE,$qtdeProcedimento=FALSE){

        $soma = $codProcedimento + $qtdeProcedimento; 
        $restoDivisao = $soma % 1111; 
        return  $restoDivisao + 1111;
    }
    
    public function buscaPrestadorParaBpa(){
            
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("bpa" => "bpa"),"")
                        ->join(array("uni" => "unidade"), "uni.uni_codigo = bpa.uni_codigo",array("uni.uni_codigo","uni.uni_desc"))
                        ->where("uni.cnes_ativo = 'A'")
                        ->order('uni.uni_desc ASC');
        
        return $this->fetchAll($where);
            
    }

    public function buscaTipoFinanciamento(){
            
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from("tb_financiamento")
                        ->order('no_financiamento ASC');
       // die($where);
        return $this->fetchAll($where);
            
    }

    public function listagemBPA($mes, $ano,$cnes=FALSE){
        //error_reporting(E_ALL);

        // $m = explode('?', $mes)[0];
        // echo "mes: ".$mes, "ano: ".$ano;

        if($mes < 10){
            $mes = '0'.$mes;
        }

        // exit;
        $dados = $this->getDefaultAdapter()->query("
        select
            usu.usr_codigo,
            proc.proc_nome,
            proc.proc_codigo_sus,
            esp.cod_cbo,
            usu.usr_codigo,
            count(b.bpa_codigo) as quantidade,
            b.uuid
        from bpa as b
            inner join procedimento as proc on b.proc_codigo = proc.proc_codigo 
            inner join usuarios as usu on usu.usr_codigo = b.usr_codigo 
            inner join especialidade as esp on b.esp_codigo = esp.esp_codigo 
        where date_part('month', b.bpa_data) = '$mes' 
            and date_part('year', b.bpa_data) = '$ano'
            and b.uni_codigo = '$cnes'
        group by proc.proc_codigo, esp.cod_cbo, proc.proc_nome, usu.usr_codigo, uuid
            ")->fetchAll();

        return $dados;
        
    }

    public function getAnos(){
        $result = array('2018','2019');
                
        return $result;
    }

    public function getFolhasCount($id){
        return $this->getDefaultAdapter()->query("select count(bpa_codigo) as quantidade from bpa where usr_codigo = $id")->fetch()['quantidade'];
    }
    
}