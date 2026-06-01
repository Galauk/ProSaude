<?php
    

class Transporte_ViagemController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Viagem";
        // error_reporting(E_ALL);

    }
    
    public function indexAction(){
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem/novo.js');
        $tbVia = new Application_Model_Viagem();      
        $this->view->itens = $tbVia->getViagens();
        $this->view->edicao = 0;
    }
    
    public function novoAction() {
        $tbVei = new Application_Model_Veiculo();
        $tbUnidade = new Application_Model_Unidade();        
        // die("dasdsad");
        $tbRotas = new Application_Model_Transporte();        
        $this->view->veiculo = $tbVei->getVeiculos();    
        $this->view->unidades = $tbUnidade->getUnidade();
        $this->view->rotas = $tbRotas->getRotas();
        $this->view->edicao = 0;
        // $this->_helper->layout->setLayout("simples");
        
        $this->render("form");
    }		

    public function salvarAction(){
        // echo "apsdag"; die;
        $tbVia = new Application_Model_Viagem();        
        $this->_helper->layout->disableLayout();
       
        $dados = array("via_codigo"=>$this->_getParam("via_codigo",FALSE),
            "via_data_ida"=>$this->_getParam("via_data_ida",FALSE),
            "via_data_retorno"=>$this->_getParam("via_data_retorno",FALSE) != "" ? $this->_getParam("via_data_retorno",FALSE) : $this->_getParam("via_data_ida",FALSE),
            "vei_codigo"=>$this->_getParam("vei_codigo",FALSE),
            "usr_codigo"=>($this->_getParam("usr_codigo",FALSE == '' ? 1 : $this->_getParam("usr_codigo",FALSE))),
            "via_local"=>($this->_getParam("via_local",FALSE) == '' ? "CENTRO DE SAUDE MARIA LUIZA SOARES" : $this->_getParam("via_local",FALSE)),
            "via_hora"=>($this->_getParam("via_hora",FALSE == '' ? "6:15" : $this->_getParam("via_hora",FALSE))),
            "via_motivo"=>$this->_getParam("via_motivo", FALSE),
            "usr_codigo_cadastro"=>$this->_getParam("usr_codigo_cadastro", FALSE),
            "via_hora_ida"=>$this->_getParam("via_hora_ida", FALSE),
            "via_hora_retorno"=>$this->_getParam("via_hora_retorno", FALSE)
        );
        // echo $tbVia->validaViagem($dados) ? 'true' : 'false';
        $fn = $tbVia->validaViagemPeriodo($dados);
        
        if($fn == FALSE){
            $this->_redirect("transporte/viagem?error=exists");
        } else {
            $this->view->edicao = 1;
            $tbVia->salvar($dados);
            $tbRotas = new Application_Model_Transporte();
            

            $rota = $this->_getParam('rotcodigo', FALSE);
            $tbRotas->update(array("veicodigo"=>$this->_getParam("vei_codigo", FALSE)), "rotcodigo = ".$rota);
            
            $this->_redirect("transporte/viagem?alert=success");
            
        }
        
        // $this->view->edicao = 1;
        // $tbVia->salvar($dados);

        

        // $this->_redirect("transporte/viagem/");
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
        $tbUnidade = new Application_Model_Unidade();        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem/novo.js');
        $this->view->edicao = 1;
        $via_codigo = $this->_getParam("id",FALSE);

        if (!$via_codigo){
            return $this->_redirect("/transporte/viagem");
        }
       
        $tbVei = new Application_Model_Veiculo();
        $tbVia = new Application_Model_Viagem();  
        
        $tbRotas = new Application_Model_Transporte();        
        
        $this->view->unidades = $tbUnidade->getUnidade();
        $this->view->veiculo = $tbVei->getVeiculos(); 
        $this->view->dados =  $tbVia->getViagem($via_codigo);
        // echo "<pre>"; print_r($tbRotas->getRotas; die;
        if($tbRotas->getRotas($this->view->dados['vei_codigo'])){
            $this->view->rotas = $tbRotas->getRotas($this->view->dados['vei_codigo']);
        } else {
            $this->view->rotas = $tbRotas->getRotas();
        }
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

    public function rotaAction(){
        $this->view->title = "Rotas";
        // die("asdsad");
        $tbTransp = new Application_Model_Transporte();

        $this->view->rotas = $tbTransp->getRotas();
        $this->render("rota");
    }

    public function novaRotaAction(){
        $this->view->title = "Cadastrar rota";
        // die("dasdsad");
        $tbEst = new Application_Model_Estado();

        $this->view->estados = $tbEst->getEstados();
    }

    public function getCidadesAction(){
        $uf = $this->_getParam('uf', FALSE);

        $tbCid = new Application_Model_Cidade();

        // print_r($tbCid->listaCidadePorEstado($uf)->toArray());
        // die;

        $this->view->dados = $tbCid->listaCidadePorEstado($uf)->toArray();
        return $this->render("dados");
    }

    public function salvarRotaAction(){
        $descricao = $this->_getParam("rotdescri", FALSE);
        $estado = $this->_getParam("est_codigo", FALSE);
        $cidade = $this->_getParam("cid_codigo", FALSE);
        // echo "<pre>";
        // print_r(array($descricao, $estado, $cidade));
        // die;
        
        $dados = array("rotdescri"=>"$descricao", "uf"=>"$estado", "cid_codigo"=>implode(",", $cidade));
        
        $tbRt = new Application_Model_Transporte();
        
        if($tbRt->salvar($dados)){
            $this->_redirect("/transporte/viagem/rota");
        } else {
            $this->view->errorMsg = "Erro ao salvar os dados";
            $this->render("rota");
        }
    }

    public function editarRotaAction() {
        error_reporting(E_ALL);
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem/novo.js');
        $this->view->edicao = 1;
        $rotcodigo = $this->_getParam("id",FALSE);

        if (!$rotcodigo){
            return $this->_redirect("/transporte/viagem/rota");
        }

        // die("inrien: ".$rotcodigo);

        $tbEst = new Application_Model_Estado();

        
        $tbRotas = new Application_Model_Transporte();        
        
        // echo "<pre>"; print_r($tbRotas->getRota($rotcodigo)); die;
        
        $this->view->estados = $tbEst->getEstados();
        $this->view->rota = $tbRotas->getRota($rotcodigo);
        return $this->render("nova-rota");
    }
}