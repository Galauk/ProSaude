<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_RequisicaoExame extends Elotech_Db_Table_Abstract {

	protected $_name = 'requisicao_exames';
	protected $_primary = 'req_codigo';
	protected $_sequence = 'seq_req_codigo';

	public function salvar(array $data,$obs=FALSE) {
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimentoMedico();

            if (is_null($data['ate_codigo']) || empty($data['ate_codigo'])){
                if(!$data['usu_codigo'])
                    $data['ate_codigo'] = $ate->ate_codigo;
            }
            if($obs != "S" && empty($data['usr_codigo_solicitante'])){
                    if (is_null($data['usu_codigo']) || empty($data['usu_codigo']))
                            $data['usu_codigo'] = $ate->usu_codigo;
            }
            if (is_null($data['dt_requisicao']) || empty($data['dt_requisicao']))
                    $data['dt_requisicao'] = date("Y-m-d");

            $campo = "";
            if($data["usr_codigo_solicitante"]){
                $campo = "usr_codigo_solicitante";
            }else if($data["med_codigo_solicitante"]){
                $campo = "med_codigo_solicitante";
            }else{
                $campo = "ate_codigo";
            }

            $this->notEmpty(array("proc_codigo","usu_codigo","$campo"), $data);
            return parent::salvar($data);
	}

	/**
	 * Retorna os exames solicitados no atendimento atual
	 * @param int $filtro req_codigo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getItens($filtro=FALSE, $ate_codigo=FALSE) {
            if(!$ate_codigo){
                    $tbAte = new Application_Model_Atendimento();
                    $ate_codigo = $tbAte->temAtendimentoMedico()->ate_codigo;
            }

            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("r" => "requisicao_exames"), array("req_codigo","data_solicitacao"=>"dt_requisicao","req_observacao","proc_solicitado","proc_avaliado"))
                            ->join(array("p" => "procedimento"), "p.proc_codigo=r.proc_codigo", array("proc_codigo", "proc_nome","proc_codigo_sus"))
                            ->join(array("a" => "atendimento"),"a.ate_codigo=r.ate_codigo","")
                            ->join(array("u" => "usuario"),"u.usu_codigo=a.usu_codigo")
                            ->order("proc_nome");
            if($ate_codigo)
                $where->where("r.ate_codigo=?", $ate_codigo);

            if($filtro)
                    $where->where ("req_codigo IN (?)", $filtro);

            return $this->fetchAll($where);
	}
        
        
        public function atualizarUsu($de, $para){
            $de = (array)$de;

            $data = array("usu_codigo" => $para);
            $where = $this->select()->where("usu_codigo IN (?)", $de)->getPart(Zend_Db_Table_Select::WHERE);
            $where = $where[0];

            Zend_Registry::get("logger")->log("Atualizando usuarios em ".$this->_name, Zend_Log::INFO);

            return $this->update($data, $where);
	}
        
		/**
	 * Retorna os exames solicitados no internação atual
	 * @param int $filtro $io_codigo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getItensInternacao($io_codigo) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("ati"=>"atendimento_internacao"),"io_codigo")
                            ->join(array("ate"=>"atendimento"),"ate.ate_codigo = ati.ate_codigo","")				
                            ->join(array("r" => "requisicao_exames"),"ate.ate_codigo=r.ate_codigo",array("req_codigo","req_observacao"))
                            ->join(array("p" => "procedimento"), "p.proc_codigo=r.proc_codigo", array("proc_codigo", "proc_nome"))
                            ->join(array("usu" => "usuario"),"usu.usu_codigo=ate.usu_codigo",array("usu_prontuario","usu_nome","usu_sexo","usu_datanasc","usu_cartao_sus","cid_nome"=>""))
                            ->joinLeft(array("dom"=>"domicilio"),"dom.dom_codigo=usu.dom_codigo",array("dom_numero"))
                            ->joinLeft("rua","rua.rua_codigo=dom.rua_codigo","rua_nome")
                            ->order("dt_requisicao")
                            ->where("io_codigo=?",$io_codigo);
            return $this->fetchAll($where);
	}

	/**
	 * Retorna uma lista de todos os exames solicitados ao paciente
	 * Se for informado um $ate_codigo, será filtrado por atendimento
	 * @param int $ate_codigo
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getHistorico($ate_codigo=NULL) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("r" => "requisicao_exames"), array("req_codigo","dt_requisicao","req_finalizada"))
                            ->join(array("p" => "procedimento"), "p.proc_codigo=r.proc_codigo", "proc_nome")
                            ->join(array("a" => "atendimento"), "a.ate_codigo=r.ate_codigo", "")
                            ->join(array("u" => "unidade"), "u.uni_codigo=a.uni_codigo", "uni_desc")
                            ->join(array("age" => "agendamento"), "age.age_codigo=a.age_codigo", "")
                            ->join(array("e" => "especialidade"), "e.esp_codigo=age.esp_codigo", "esp_nome")
                            ->join(array("usr" => "usuarios"), "usr.usr_codigo=a.med_codigo", "usr_nome")
                            ->order("dt_requisicao DESC");

            if ($ate_codigo)
                    $where->where("r.ate_codigo=?", $ate_codigo);
            else
                    $where->where("r.usu_codigo=?", Application_Model_Agendamento::usuEmAberto()->usu_codigo);

            return $this->fetchAll($where);
	}

	/**
	 * Exclui uma requisição de exame
	 * O método verifica se faz parte do atendimento atual
	 * @param int $req_codigo 
	 */
	public function excluir($req_codigo) {
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimentoMedico();

            $where = "req_codigo=$req_codigo";

            $item = $this->fetchRow($where);
            if ($item)
                    $item->delete();

            return true;
	}

    public function excluirAteCodigo($ate_codigo){
        $tbAte = new Application_Model_Atendimento();
        $ate = $tbAte->temAtendimentoMedico();

        $where = "ate_codigo=$ate_codigo";

        $item = $this->fetchAll($where);

        foreach ($item as $i)
            $i->delete();

        return true;
    }

	public function imprimir($selecionados=FALSE,$io_codigo=FALSE,$usu_codigo=FALSE,$ate_codigo=FALSE) {
            $tbAte = new Application_Model_Atendimento();
            $ate = $tbAte->temAtendimentoMedico();

            $dados = new stdClass();

            // if para ver se Ã© do prontuÃ¡rio ou do atendimento da UPA
            if($io_codigo){
                 $dados->itens = (object) $this->getItensInternacao($io_codigo)->toArray();
                 $dados->codigo = $ate_codigo;
            }else{
               $dados->itens = (object) $this->getItens($selecionados)->toArray();
               $dados->codigo = $ate->ate_codigo;
            }



            // dados do paciente
            $tbUsu = new Application_Model_Usuario();
            if($io_codigo){
                $usu = $tbUsu->find($usu_codigo)->current();
            }else{
                $usu = $tbUsu->find($ate->usu_codigo)->current();
            }
            $dados->usu_nome = $usu->usu_nome;
            $dados->usu_datanasc = $usu->usu_datanasc;
            $dados->usu_cartao_sus = $usu->usu_cartao_sus;
            $dados->usu_prontuario = $usu->usu_prontuario;
            $dados->usu_sexo = $usu->usu_sexo;
            $dados->idade = $usu->usu_datanasc;
            $dados->usu_mae = $usu->usu_mae;
            $dados->usu_rg = $usu->usu_rg;
            $dados->usu_rg_dt_emissao = $usu->usu_rg_dt_emissao;
            $dados->usu_cpf = $usu->usu_cpf;
            $dados->rac_codigo = $usu->rac_codigo;

            $tbDom = new Application_Model_Domicilio();
            $dom_dados = $tbDom->getEnderecoPorUsuario($usu->usu_codigo);

            $dados->rua_nome = $dom_dados->rua_nome;
            $dados->dom_numero = $dom_dados->dom_numero;
            /*Fim do get dados usuario*/
            $tbUsr = new Application_Model_Usuarios();
            $usr = $tbUsr->getUsrAtual();
            $usr_codigo = $usr->usr_codigo;

            $dados->usr_nome = $usr->usr_nome;
            $dados->usr_num_conselho = $usr->usr_num_conselho;
            $dados->cnes_sigla_est = $usr->cnes_sigla_est;
            $dados->con_descricao = $usr->con_descricao;

            // dados da unidade
            $tbUni = new Application_Model_Unidade();
            // if para ver se Ã© do prontuÃ¡rio ou do atendimento da UPA
            if($io_codigo){
               $tbUsr = new Application_Model_Usuarios();
               $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;

               $log = new Application_Model_Logon();
               $uni_codigo = $log->getDadosPeloUsuario($usr_codigo);                 

               $uni = $tbUni->buscarCidadeDaUnidade($uni_codigo->uni_codigo)->current();

            }else{
                $uni = $tbUni->buscarCidadeDaUnidade($ate->uni_codigo)->current();
            }


            $dados->nome_cidade = $uni->cid_nome;
            $dados->uni_desc = $uni->uni_desc;
            $dados->uni_endereco = $uni->uni_endereco;

            // dados da secretaria
            $tbSec = new Application_Model_Secretaria();
            $sec = $tbSec->fetchRow();

            $dados->secretaria = $sec->nome_secretaria;
            Zend_Registry::get("logger")->log($dados, Zend_Log::INFO);
            return $dados;
	}
	public function getRaioxPedidos($ate_codigo=NULL,$usu_codigo=FALSE,$dt_requisicao=FALSE){
            $sql = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("req"=>"requisicao_exames"))
                        ->join(array("proc"=>"procedimento"),"proc.proc_codigo=req.proc_codigo",array("proc_nome"));
            if($ate_codigo){
                $sql->where("ate_codigo=$ate_codigo");
            }else{
                $sql->where("usu_codigo=$usu_codigo")
                    ->where("dt_requisicao='$dt_requisicao'");
            }
            $sql->limit(15);
            return $this->fetchAll($sql);
	}
        
        public function getRequisicoesComAnexo($req_codigo=FALSE,$ate_codigo=FALSE){
            $sql = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("req"=>"requisicao_exames"))
                        ->join(array("upl"=>"upload_arquivo"),"upl.req_codigo=req.req_codigo");
            
            if($ate_codigo){
                $sql->where("ate_codigo=?",$ate_codigo);
            }
            
            if($req_codigo){
                 $sql->where("req.req_codigo=?",$req_codigo);
            }
            
            return $this->fetchAll($sql);
        }
        
        public function getItensPorUsuario($usu_codigo=FALSE,$dt_requisicao=FALSE){
            $where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("r" => "requisicao_exames"), array("req_codigo","dt_requisicao","req_observacao","usu_codigo"))
				->join(array("p" => "procedimento"), "p.proc_codigo=r.proc_codigo", array("proc_codigo", "proc_nome"))
                                ->where("usu_codigo=?",$usu_codigo)
				->order("proc_nome");
            if(!$dt_requisicao){
                $where->where ("dt_requisicao='NOW()'");
            }else{
                $where->where("dt_requisicao='?'",$dt_requisicao);
            }
            return $this->fetchAll($where);
        }
        
        public function getListaSolicitacoesManuais(){
            $where = $this->select()
                          ->setIntegrityCheck(FALSE)
                          ->from(array("req"=>"requisicao_exames"),array("req.usu_codigo","dt_requisicao"))
                          ->joinLeft(array("ate"=>"atendimento"),"ate.ate_codigo=req.ate_codigo","ate_codigo")
                          ->join(array("usu"=>"usuario"),"usu.usu_codigo=req.usu_codigo",array("usu_nome","usu_datanasc"))
                          ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=req.usr_codigo_solicitante OR usr.usr_codigo = ate.med_codigo",array("usr_nome","usr_codigo"))
                          ->joinLeft(array("med"=>"medico"),"med.med_codigo=req.med_codigo_solicitante",array("med_nome","med_codigo"))
                          ->where("req_encaminhamento='t'")
                          ->order("dt_requisicao desc")
                          ->limit(15);
            return $this->fetchAll($where);
        }
        
        public function pesquisar($dados) {
            $where = $this->select()
                         ->setIntegrityCheck(FALSE)
                         ->from(array("req"=>"requisicao_exames"))
                         ->joinLeft(array("ate"=>"atendimento"),"ate.ate_codigo=req.ate_codigo")
                         ->join(array("usu"=>"usuario"), "usu.usu_codigo=ate.usu_codigo OR usu.usu_codigo=req.usu_codigo",array("usu_nome","usu_codigo"))
                         ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=req.usr_codigo_solicitante OR usr.usr_codigo = ate.med_codigo",array("usr_nome","usr_codigo"))
                         ->joinLeft(array("med"=>"medico"),"med.med_codigo=req.med_codigo_solicitante",array("med_nome","med_codigo"))
                         ->where("req_encaminhamento='t'");

            $data = explode("/",$dados);
            if($data[1]){
                $verificaData = checkdate($data[1], $data[0], $data[2]);
                if($verificaData == 1){
                    $where->where("dt_requisicao='$dados'");
                }else{
                    return false;
                }
            }else{
                $where->where("usu_nome ilike '%$dados%' OR usr_nome ilike '%$dados%' OR med_nome ilike '%$dados%'");
            }

            return $this->fetchAll($where);
	}
        
}
