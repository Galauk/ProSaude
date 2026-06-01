<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Setor extends Elotech_Db_Table_Abstract {

	protected $_name = "setor";
	protected $_primary = "set_codigo";
	protected $_sequence = "seq_uni_codigo";

	// Método que retorna os 15 últimos setores
	public function getSetor(){
		$where = $this->select(FALSE)
					->setIntegrityCheck()
					->from(array("set"=>"setor"),array("set_codigo","set_nome"))
					->order(array("set_codigo DESC"))
					->limit(15);
		return $this->fetchAll($where);
	}

	// Método que salva os dados em BD
	public function salvar(array $data) {
            $set_codigo = parent::salvar($data);
	}

	/**
	 * Buscar os setores
	 * usado para alimentar o plugin de busca (jquery)
	 * @return json
	 */
	public function buscar($term=FALSE,$set_logado=FALSE) {
		if ($term)
			$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("set" => "setor"), array("set_codigo", "set_nome"))
					->where("retira_acentos(set_nome) ilike retira_acentos('%$term%')", "S")
					->order(array("set_nome"))
					->limit(15);

                        if($set_logado == 1){
                            $tbUsr = new Application_Model_Usuarios();
                            $usr = $tbUsr->getUsrAtual();
                            $where->join(array("us"=>"usuarios_setores"),"us.set_codigo=set.set_codigo","")
                                  ->where("usr_codigo=$usr->usr_codigo");
                        }
                        //die($where);
			$all = $this->fetchAll($where);
			$setores = array();
		foreach ($all as $usu) {
			$setores [] = array(
				"id" => $usu->set_codigo,
				"label" => $usu->set_nome,
				"data" => $usu->toArray()
			);
		}

		if (!count($setores)) {
			$setores [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("set_codigo" => "0", "set_nome" => "")
			);
		}

		return $setores;
	}

        public function buscarUnidadeSetor($term=FALSE,$uni_codigo=FALSE) {

		if ($term)
			$where = $this->select(FALSE)
					->setIntegrityCheck(FALSE)
					->from(array("set" => "setor"), array("set_codigo", "set_nome"))
					->where("retira_acentos(set_nome) ilike retira_acentos('%$term%')", "S")
					->order(array("set_nome"))
					->limit(15);
                        if($uni_codigo){
                            $where->where("uni_codigo in ($uni_codigo)");
                        }
			$all = $this->fetchAll($where);
			$setores = array();
		foreach ($all as $usu) {
			$setores [] = array(
				"id" => $usu->set_codigo,
				"label" => $usu->set_nome,
				"data" => $usu->toArray()
			);
		}

		if (!count($setores)) {
			$setores [] = array(
				"id" => 0,
				"label" => "Nenhum item encontrado",
				"data" => array("set_codigo" => "0", "set_nome" => "")
			);
		}

		return $setores;
	}

	public function getInfoSetor($set_codigo=FALSE){
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("s" => "setor"), array("set_codigo", "set_nome"))
                ->where("set_codigo=$set_codigo");
                //die($where);
				return $this->fetchAll($where);
			}

        public function selectTag($setor_logon = FALSE, $id=FALSE, $set_codigo=FALSE) {
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("s" => "setor"), array("set_codigo", "set_nome"))
                                ->where("set_estoque = 'S'")
				->order("set_nome");


                if($setor_logon){

                    $tbUsr = new Application_Model_Usuarios();
                    $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
                    $tbUsrSet = new Application_Model_UsuariosSetores();
                    foreach($tbUsrSet->getSetoresPorUsuario($usr_codigo) as $setor){
                        $setores .= $setor[set_codigo].",";
                    }
                    $setores = substr($setores, 0, -1);
                    //die($setores);
                    $where->where("set_codigo in ($setores)");
                }

                if(!$id){
                    $id = "set_codigo";
                }

        //die();
		return parent::selectTag($where, "set_nome", NULL, TRUE, TRUE, $id, "$id", TRUE,$set_codigo);
	}

        public function buscarSetoresPorUnidade($uni_codigo=FALSE,$usr_codigo=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("set"=>"setor"),array("set.set_codigo","set.set_nome"))
                        ->join(array("uset"=>"usuarios_setores"),"set.set_codigo=uset.set_codigo",array(""))
                        ->join(array("usr"=>"usuarios"),"uset.usr_codigo=usr.usr_codigo",array(""))
                        ->join(array("uni"=>"unidade"),"set.uni_codigo=uni.uni_codigo",array(""))
                        ->where("uni.uni_codigo =?",$uni_codigo)
                        ->where("usr.usr_codigo =?",$usr_codigo);
            return $this->fetchAll($sql);
        }

        public function buscaSetorPorUsuario($usr_codigo=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("set"=>"setor"),array("set.set_codigo","set.set_nome"))
                        ->join(array("uset"=>"usuarios_setores"),"set.set_codigo=uset.set_codigo",array(""))
                        ->join(array("usr"=>"usuarios"),"uset.usr_codigo=usr.usr_codigo",array(""))
                        ->join(array("uni"=>"unidade"),"set.uni_codigo=uni.uni_codigo",array(""))
                        ->where("usr.usr_codigo =?",$usr_codigo);
            return $this->fetchAll($sql);
        }

         public function verificaFuncaoSetor($set_codigo=FALSE){
            $sql = $this->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array("set"=>"setor"))
                        ->where("set_codigo=$set_codigo");
            return $this->fetchRow($sql);
        }
}
