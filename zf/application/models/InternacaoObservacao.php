<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_InternacaoObservacao extends Elotech_Db_Table_Abstract {

    protected $_name = 'internacao_observacao';// nome da tabela do banco
    protected $_primary = 'io_codigo'; // pk da tabela
    protected $_dependentTables = array();

    public function salvar(array $data) {// esse método é para tratar a ação sendo ela incluir ou alterar
       
       $this->addRealName(array(
    		"io_observacao" => "observação",
                "io_status" => "Status"
    	)); // isso serve para deixar bonitinho quando der erro por falta de um campo. De: 'O campo pe_descricao deve ser preenchido' para "O campo descrição deve ser preenchido"
    	
    	if(empty($data['io_codigo'])){
    		$this->notEmpty(array("io_observacao"),$data);
    	}
    	$this->emptyToUnset($data);
    	$this->minLength(array("io_observacao" => 3),$data, array("io_observacao"=>true));// tudo eu passo como array nas validações
        
        //echo "<pre>".print_r($data);exit();
         
        return parent::salvar($data);// ele retorna para a classe extendida com o "parent" os dados para dentro do "PAI" ele executar a query
    }
    
    public function buscarAtual(){
        
    	$tbAte = new Application_Model_Atendimento();
    	$ate = $tbAte->temAtendimento()->toArray(); // descobre o atendimento que está na Session.
        return $ate[ate_codigo];        
    }
    
    public function buscar($io_codigo=FALSE){
        
    	if(!$io_codigo)
    		return $this->buscarAtual();
        
        $dados = $this->fetchRow("io_codigo = $io_codigo");
    	return $dados;
    }
    
    public function getAtual($ate_codigo){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("io" => "internacao_observacao"))
                        ->join(array("ai" => "atendimento_internacao"), "ai.io_codigo=io.io_codigo","")
                        ->where("ate_codigo=?", $ate_codigo);
        $io = $this->fetchRow($where);
    	return $io;

    }
    
    public function getLista(){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("io" => "internacao_observacao"),array("io_codigo","io_status"))
                        ->join(array("ai" => "atendimento_internacao"), "ai.io_codigo=io.io_codigo",null)
                        ->join(array("ate" => "atendimento"),"ate.ate_codigo=ai.ate_codigo")
                        ->join(array("age" => "agendamento"),"age.age_codigo=ate.age_codigo")
                        ->join(array("usu" => "usuario"),"usu.usu_codigo=age.usu_codigo")
                        ->join(array("usr" => "usuarios"),"usr.usr_codigo=age.med_codigo")
                        ->joinLeft(array("pc" => "pre_consulta"),"pc.age_codigo=age.age_codigo")
                        ->where("io_situacao_internacao=1")
                        ->where("age_atendido in ('A','F','P')")
                        ->order("pc.pc_clas_risco");
        $dados = $this->fetchAll($where);

		return $dados;
    }
    
    
//    public function verificaSeEstaInternado($usu_codigo=FALSE){
//        if(empty($usu_codigo)){
//            return false;
//        }
//        $where = $this->select(FALSE)
//                        ->setIntegrityCheck(FALSE)
//                        ->distinct()
//                        ->from(array("io" => "internacao_observacao"),array("qtde" => "count(*)"))
//                        ->join(array("ai" => "atendimento_internacao"), "ai.io_codigo=io.io_codigo","")
//                        ->join(array("ate" => "atendimento"),"ate.ate_codigo=ai.ate_codigo","")
//                        ->join(array("age" => "agendamento"),"age.age_codigo=ate.age_codigo","")
//                        ->join(array("usu" => "usuario"),"usu.usu_codigo=age.usu_codigo","")
//                        ->join(array("usr" => "usuarios"),"usr.usr_codigo=age.med_codigo","")
//                        ->joinLeft(array("pc" => "pre_consulta"),"pc.age_codigo=age.age_codigo","")
//                        ->where("io_situacao_internacao <> 3")
//                        ->where("age_atendido in ('A','F','P')")
//                        ->where("usu.usu_codigo=?",$usu_codigo);
//        return $this->fetchRow($where);
//    }

    public function getInternados(){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("io" => "internacao_observacao"),array("io_codigo","io_status"))
                        ->join(array("ai" => "atendimento_internacao"), "ai.io_codigo=io.io_codigo",null)
                        ->join(array("ate" => "atendimento"),"ate.ate_codigo=ai.ate_codigo")
                        ->join(array("age" => "agendamento"),"age.age_codigo=ate.age_codigo")
                        ->join(array("usu" => "usuario"),"usu.usu_codigo=age.usu_codigo")
                        ->join(array("usr" => "usuarios"),"usr.usr_codigo=age.med_codigo")
                        ->joinLeft(array("pc" => "pre_consulta"),"pc.age_codigo=age.age_codigo")
                        ->join(array("pl"=>"paciente_leito"),"pl.io_codigo=io.io_codigo")
                        ->join(array("lei"=>"leito"),"pl.lei_codigo=lei.lei_codigo")
                        ->join(array("qua"=>"quarto"),"qua.qua_codigo=lei.qua_codigo")
                        ->where("io_situacao_internacao=2")
                        ->where("age_atendido = 'A'")
                        ->order("pc.pc_clas_risco");
        $dados = $this->fetchAll($where);
         return $dados;
    }

     public function getPacAlta(){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("io" => "internacao_observacao"),array("io_codigo","io_status","io_data_alta"))
                        ->join(array("ai" => "atendimento_internacao"), "ai.io_codigo=io.io_codigo",null)
                        ->join(array("ate" => "atendimento"),"ate.ate_codigo=ai.ate_codigo")
                        ->join(array("age" => "agendamento"),"age.age_codigo=ate.age_codigo")
                        ->join(array("usu" => "usuario"),"usu.usu_codigo=age.usu_codigo")
                        ->join(array("usr" => "usuarios"),"usr.usr_codigo=age.med_codigo")
                        ->join(array("pc" => "pre_consulta"),"pc.age_codigo=age.age_codigo")
                        ->join(array("pl"=>"paciente_leito"),"pl.io_codigo=io.io_codigo")
                        ->join(array("lei"=>"leito"),"pl.lei_codigo=lei.lei_codigo")
                        ->join(array("qua"=>"quarto"),"qua.qua_codigo=lei.qua_codigo")
                        ->where("io_situacao_internacao=3")
                        ->where("age_atendido = 'A'")
                        ->order("io_data_alta desc","usu.usu_nome")
                        ->limit(300);
        $dados = $this->fetchAll($where);
        //die($where);
         return $dados;
    }
   
    
    public function getQuartos(){
        $where = $this->select(FALSE)
                      ->setIntegrityCheck(FALSE)
                      ->from(array(""));
    }
    
    public function buscaInternamentos($usu_codigo=FALSE){
        $where = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("io" => "internacao_observacao"))
                        ->join(array("ai" => "atendimento_internacao"), "ai.io_codigo=io.io_codigo","")
                        ->join(array("ate" => "atendimento"),"ate.ate_codigo=ai.ate_codigo")
                        ->join(array("age" => "agendamento"),"age.age_codigo=ate.age_codigo")
                        ->join(array("usu" => "usuario"),"usu.usu_codigo=age.usu_codigo")
                        ->join(array("pl"=>"paciente_leito"),"pl.io_codigo=io.io_codigo")
                        ->join(array("lei"=>"leito"),"pl.lei_codigo=lei.lei_codigo")
                        ->join(array("qua"=>"quarto"),"qua.qua_codigo=lei.qua_codigo")
                        ->where("io_situacao_internacao <> '1'")
                        ->where("io_situacao_internacao <> '3'")
                        ->where("usu.usu_codigo=?",$usu_codigo);
        $dados = $this->fetchRow($where);
        //echo "<pre>".print_r($dados,1);die();
        //die($where);
        return $dados;
        
    }
    
    public function getHistorico($usu_codigo=FALSE,$data_inicial=FALSE,$data_final=FALSE,$esp=FALSE){

            $var = (string)$esp; 

            $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->distinct()
            ->from(array("age"=>"agendamento"))
            ->join(array("uni"=>"unidade"),"uni.uni_codigo=age.uni_codigo","uni_desc")
            ->join(array("usr"=>"usuarios"),"usr.usr_codigo = age.med_codigo","usr_nome")
            ->join(array("usu"=>"usuario"),"usu.usu_codigo=age.usu_codigo",array("usu_nome","usu_codigo"))
            ->join(array("mde" => "medico_especialidade"), "mde.med_codigo = usr.usr_codigo","")
			->join(array("e" => "especialidade"), "e.esp_codigo=mde.esp_codigo", array("esp_nome","cod_cbo"))
            ->where("age.usu_codigo=?",$usu_codigo)
            ->where("age.tp_codigo=?",7);
            

        //die($where);
        
        if ($data_inicial)
           $where->where("ate_data >= ?", $data_inicial);

        if ($data_final)
           $where->where("ate_data <= ?", $data_final);
        if($esp)
            $where->where("e.cod_cbo=?", $var);
        
        //die($where);
       $dados = $this->fetchAll($where);
       //die($where);
       //echo"<pre>";print_r($dados);die();
       return $dados;
    }

    

}
