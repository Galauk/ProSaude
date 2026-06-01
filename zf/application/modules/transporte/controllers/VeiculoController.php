<?php
class Transporte_VeiculoController extends Zend_Controller_Action {
    private $tbVeis;
    private $tbVeie;
    private $tbVeit;
    private $tbVeic;
    private $tbvei;
    public function init(){
            $this->view->title = "Veiculo";
            $this->tbVeis = new Application_Model_VeiculoSituacao();
            $this->tbVeie = new Application_Model_VeiculoEspecie();
            $this->tbVeit = new Application_Model_VeiculoTipoTransporte();
            $this->tbVeic = new Application_Model_VeiculoCombustivel();
            $this->tbvei = New Application_Model_Veiculo();
    }

    public function indexAction(){
              
        $this->view->itens = $this->tbvei->getVeiculos();
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/veiculo/novo.js');
    }

    public function novoAction() {
        $this->_helper->layout->setLayout("simples");
        
        $this->view->situacao = $this->tbVeis->fetchAll();
        $this->view->especie = $this->tbVeie->fetchAll();
        $this->view->tipo_transporte = $this->tbVeit->fetchAll();
        $this->view->combustivel = $this->tbVeic->fetchAll();
         $this->render("form");

    }		

    public function salvarAction(){      
       $this->_helper->layout->disableLayout();

        $dados = array("vei_codigo"=>$this->_getParam("vei_codigo",FALSE),
                       "vei_data_aquisicao"=>$this->_getParam("vei_data_aquisicao",FALSE),
                       "vei_descricao"=>$this->_getParam("vei_descricao",FALSE),
                       "vei_placa"=>$this->_getParam("vei_placa",FALSE),
                       "vei_chassi"=>$this->_getParam("vei_chassi",FALSE),
                       "vei_renavan"=>$this->_getParam("vei_renavan",FALSE),
                       "veis_codigo"=>$this->_getParam("veis_situacao",FALSE),
                       "vei_placa_patrimonial"=>$this->_getParam("vei_placa_patrimonial",0),
                       "for_codigo"=>$this->_getParam("for_codigo",NULL),
                       "veie_codigo"=>$this->_getParam("veie_especie",NULL),
                       "vei_nota_fiscal"=>$this->_getParam("vei_nota_fiscal",NULL),
                       "vei_ano"=>$this->_getParam("vei_ano",0),
                       "vei_ano_modelo"=>$this->_getParam("vei_ano_modelo",0), 
                       "vei_cor"=>$this->_getParam("vei_cor",FALSE),
                       "veit_codigo"=>$this->_getParam("vei_tipo_transporte",FALSE),
                       "vei_capacidade"=>$this->_getParam("vei_capacidade",0),
                       "vei_cnh_minima"=>$this->_getParam("vei_cnh_minima",0),
                       "vei_qtde_tanque"=>$this->_getParam("vei_qtde_tanque",0),
                       "vei_media"=>$this->_getParam("vei_media",0),
                       "veic_codigo"=>$this->_getParam("vei_combustivel",NULL),
                       "vei_tipo_veiculo"=>$this->_getParam("tipo_veiculo",NULL));
        
        $this->tbvei->salvar($dados);
        $this->_redirect("transporte/veiculo/");
      }
      
    public function editarAction() {
        $this->_helper->layout->setLayout("simples");
        
        $vei_codigo = $this->_getParam("id",FALSE);     
           

        if (!$vei_codigo)
            return $this->_redirect("/transporte/veiculo");
        $this->view->edicao = 1;
        $this->view->situacao = $this->tbVeis->fetchAll();
        $this->view->especie = $this->tbVeie->fetchAll();
        $this->view->tipo_transporte = $this->tbVeit->fetchAll();
        $this->view->combustivel = $this->tbVeic->fetchAll();
        $this->view->dados =  $this->tbvei->getVeiculo($vei_codigo);
        return $this->render("form");
      }
      
    public function excluirAction(){
            $id = $this->_getParam("id",false);
            $tbVei = new Application_Model_Veiculo();
            $tbVia = new Application_Model_Viagem();  
            $vei_codigo = $tbVia->getViagemPorVeiculo($id)->count();
            if($vei_codigo){
                $this->view->dialog = array("Erro", "Esse Veículo possui viagens agendadas", 300, 140);
            }else{
               $tbVei->excluir($id); 
            }
                        
            //$this->render("index");
           $this->_redirect("transporte/veiculo/");
    }
    public function getViagemPorVeiculoAction(){
        $this->_helper->layout->disableLayout();
         
        $tbVia = new Application_Model_Viagem();  
        $id = $this->_getParam("id",false); 
        $this->view->dados = $tbVia->getViagemPorVeiculo($id)->count();
        
        $this->render("dados");
        
    }
    public function pesquisaAction() {
            if ($this->_request->isPost()) {
                $this->view->busca = $this->_request->getPost("busca");                
                $this->view->itens = $this->tbvei->pesquisar($this->view->busca);
                $this->render("index");
            } else {
                 $this->_redirect("/transporte/veiculo/index");
            }
    }
    public function verificaCotaAction() {
        $this->_helper->layout->disableLayout();
        $via_codigo = $this->_getParam("id",FALSE);
        $dados = $this->tbvei->verificaCota($via_codigo);
        //die($dados->vei_capacidade);
        $total2 = ($dados->vei_capacidade - $dados->total);
        //die($total2);
        $this->view->dados = $total2;
        
        $this->render("dados");
        
       
     

    }



}
?>
