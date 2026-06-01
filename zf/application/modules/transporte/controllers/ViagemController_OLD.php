<?php
class Transporte_ViagemController extends Zend_Controller_Action {

    public function init(){
            $this->view->title = "Viagem";
            
    }

    public function indexAction(){
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem/novo.js');
        $tbVia = new Application_Model_Viagem();      
        $this->view->itens = $tbVia->getViagens();
        $this->view->edicao = 0;
    }

    public function novoAction() {
        $tbVei = new Application_Model_Veiculo();
        $this->view->veiculo = $tbVei->getVeiculos();    
        $this->view->edicao = 0;
      ///  $this->_helper->layout->setLayout("simples");
        
        $this->render("form");

    }		

    public function salvarAction(){
       $tbVia = new Application_Model_Viagem();        
       $this->_helper->layout->disableLayout();
       
        $dados = array("via_codigo"=>$this->_getParam("via_codigo",FALSE),
                       "via_data"=>$this->_getParam("via_data",FALSE),
                       "vei_codigo"=>$this->_getParam("vei_codigo",FALSE),
                       "usr_codigo"=>$this->_getParam("usr_codigo",FALSE),
                       "via_local"=>$this->_getParam("via_local",FALSE),
                       "via_hora"=>$this->_getParam("via_hora",FALSE),
                       "via_motivo"=>$this->_getParam("via_motivo",FALSE),
                       "usr_codigo_cadastro"=>$this->_getParam("usr_codigo_cadastro",FALSE) );


        $this->view->edicao = 1;
        $tbVia->salvar($dados);
        $this->_redirect("transporte/viagem/");
      }
      
    public function getViagemUsuarioAction(){
        $this->_helper->layout->disableLayout();
         
        $tbViaU = new Application_Model_ViagemUsuario();  
        $id = $this->_getParam("id",false); ;
       
        $this->view->dados = $tbViaU->getViagemPorUsuarioJson($id)->count();
        $this->view->edicao = 1;
        $this->render("dados");
        
    }
      public function editarAction() {
       // $this->_helper->layout->setLayout("simples");
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem/novo.js');
        $this->view->edicao = 1;
        $via_codigo = $this->_getParam("id",FALSE);     
           

        if (!$via_codigo)
            return $this->_redirect("/transporte/viagem");
       
        $tbVei = new Application_Model_Veiculo();
        $tbVia = new Application_Model_Viagem();        
        
        $this->view->veiculo = $tbVei->getVeiculos(); 
        $this->view->dados =  $tbVia->getViagem($via_codigo);
        return $this->render("form");
      }
      
    public function excluirAction(){
          $id = $this->_getParam("id",false);        
          $tbVia = new Application_Model_Viagem();
          $tbVia->excluir($id);
  
          $this->_redirect("transporte/viagem/");
    }
        
    public function pesquisaAction() {
        if ($this->_request->isPost()) {
            $tbVia = new Application_Model_Viagem();    
            $this->view->busca = $this->_request->getPost("busca");                
            $this->view->itens = $tbVia->pesquisar($this->view->busca);
            $this->render("index");
        } else {
             $this->_redirect("/transporte/veiculo/index");
        }
    }



}
?>
