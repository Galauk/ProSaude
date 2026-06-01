<?php
class Domicilio_BairroController extends Zend_Controller_Action {
    public function init() {
        
        $this->view->title = "Cadastro de Bairro";
        parent::init();
    }
    
    public function indexAction(){
        $tbBai = new Application_Model_Bairro();
        $this->view->itens = $tbBai->getBairros();
    }
    
    public function novoAction(){
        $bai_codigo = $this->_getParam("id");
        $this->view->popup = $term = $this->_getParam("popup",0);
        $tbDist = new Application_Model_Distrito();
        $tbConf = new Application_Model_Configuracao();
        $tbCid = new Application_Model_Cidade();
        $tbBai = new Application_Model_Bairro();
        $cid_codigo_ibge = $tbConf->getConfig("CID_CODIGO_IBGE");
        $this->view->cidade = $tbCid->getCidadePeloCodigoIbge($cid_codigo_ibge);
        $this->view->distritos = $tbDist->fetchAll();
        if($bai_codigo)
            $this->view->dados = $tbBai->getBairro($bai_codigo);
    }
    
   
    
    public function salvarAction(){
        $tbArea = new Application_Model_Bairro();        
        $this->_helper->layout->disableLayout();
       
        $dados = array("cid_codigo"=>$this->_request->getPost("cid_codigo",FALSE),
                       "bai_nome"=>mb_strtoupper($this->_request->getPost("bai_nome",FALSE), "UTF-8"),
                       "dis_codigo"=>$this->_request->getPost("dis_codigo",FALSE));
        
        if($this->_request->getPost("possui_distrito",FALSE) == "S"){
            unset($dados["cid_codigo"]);
        }else{
            unset($dados["dis_codigo"]);
        }
        if($this->_request->getPost("bai_codigo",false))
                $dados["bai_codigo"] = $this->_request->getPost("bai_codigo",false);
        
         try{
            $bai_codigo =  $tbArea->salvar($dados);
            $this->view->dados = $tbArea->getBairro($bai_codigo)->toArray();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
        
        return $this->render("dados",null,true);
    }
    
    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $tbBairro = new Application_Model_Bairro();
            $this->view->busca = $this->_request->getPost("busca");                
            $this->view->itens = $tbBairro->pesquisar($this->view->busca);
            $this->render("index");
        } else {
             $this->_redirect("/domicilio/area/index");
        }
    }
    

    
    public function excluirAction(){
          $id = $this->_getParam("id",false);        
          $tbArea = new Application_Model_Bairro();
          $tbArea->excluir($id);
  
          $this->_redirect("domicilio/bairro/");
    }
    
    public function verificaVinculoAction(){
        $tbBai = new Application_Model_Bairro();
        $bai_codigo = $this->_getParam("bai_codigo",false);        
        $qtde = $tbBai->verificaVinculosRua($bai_codigo)->toArray();
        $this->view->dados = $qtde["qtde"];
        return $this->render("dados",null,true);
    }
}
?>
