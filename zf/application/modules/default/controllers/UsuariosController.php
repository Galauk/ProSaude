<?php

class UsuariosController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->acl->allow(NULL,array("buscar","buscar-usuarios-saude","inativa"));
    }

    public function indexAction() {
        // action body
    }
        
    public function getTipoUsuarioAction(){
        $usrCodigo = $this->_request->getPost("usr_codigo");
        $tbUsr = new Application_Model_Usuarios(); 
        $this->view->dados = $tbUsr->getDadosPeloCodigo($usrCodigo)->usr_tipo_medico;
        return $this->render("dados", NULL,TRUE); 
    }

    /**
     * Retorna os usuários (usr) em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarAction(){
        
        $tbUsr = new Application_Model_Usuarios();
        $term = $this->_getParam("term",FALSE);
        $externo = $this->_getParam("externo",FALSE); // incluir médico externo (med)?
        $conveniado = $this->_getParam("conveniado",FALSE); // incluir médico externo (med)?
        
        $this->view->dados = $tbUsr->buscar($term, $externo,$conveniado);
        return $this->render("dados", NULL, TRUE);
    }
    public function buscarUsuariosPorUnidadeAction(){    
        // error_reporting(E_ALL);
        // echo "<pre>";
        // print_r($_SESSION);
        // die();
        $tbUsu = new Application_Model_Usuarios();

        $uni = $_SESSION['uni_codigo'];
        
        $nome = $this->_getParam("term",FALSE);
        
        //$usuCod = $tbUsu->buscarUsuariosUnidadeEquipe($nome);
        
        $tbUsr = new Application_Model_UnidadeUsuarios();
        
        $retorno = $tbUsu->buscarUsuariosUnidadeEquipe($nome);

        $this->view->dados = $retorno;
        return $this->render("dados", NULL, TRUE);
    }
        
    /**
     * Retorna os usuários (usr) em JSON
     * O retorno é usado pelo plugin de busca
     */
    public function buscarProfissionaisSaudeAction(){
        $tbUsr = new Application_Model_Usuarios();
        
        $term = $this->_getParam("term",FALSE); 
        $conv_codigo = $this->_getParam("conv_codigo",FALSE);   
        
        $this->view->dados = $tbUsr->buscarProfissionaisSaude($term,$conv_codigo);
        return $this->render("dados", NULL, TRUE);
    }
    
    /**
     * Retorna os usuários (usr) em JSON
     * O retorno é usado pelo plugin de busca
     */
    
    public function buscarUsuariosSaudeAction(){
        $tbUsr = new Application_Model_Usuarios();
        
        $term = $this->_getParam("term",FALSE);                
        $this->view->dados = $tbUsr->buscarUsuariosSaude($term);
        return $this->render("dados", NULL, TRUE);
    }
    
    public function buscarProfissionaisEquipesAction(){
        $tbUsr = new Application_Model_Usuarios();
        
        $term = $this->_getParam("term",FALSE);                
        $this->view->dados = $tbUsr->buscarUsuariosUnidadeEquipe($term);
        return $this->render("dados", NULL, TRUE);
    }
    
    public function loginAction(){
        $tbUsr = new Application_Model_Usuarios();
    
        $term = $this->_getParam("term",FALSE);                
    
        $this->view->dados = $tbUsr->verificaLoginExistente($term);
        return $this->render("dados", NULL, TRUE);
    }
        
    public function jqgridAction() {

        $page = $this->_getParam("page", 1);
        $limit = $this->_getParam("rows");
        $sidx = $this->_getParam("sidx", "id");
        $sord = $this->_getParam("sord", "ASC");
        $modelo = $this->_getParam("modelo", FALSE);

        $tbGrau = new Application_Model_GrupoAcessoUsuarios();
        $tbGrau->setFields(array("usr_codigo", "usr_nome"));
        $array_join = array("tabelas"=>array("usr"=>"usuarios"),"condicoes"=>array("usr.usr_codigo=grau.usr_codigo"));
        $this->view->dados = $tbGrau->getGridResource($page, $limit, $sidx, $sord,$modelo);
        //echo "<pre>".print_r( $this->view->dados,1);die();
        
        return $this->render("dados", NULL, TRUE);
    }
        
    public function getUnidadeUsuariosAction(){
        $tbUnu = new Application_Model_UnidadeUsuarios();
        $usr_codigo = $this->_getParam("usr_codigo", null);
        $this->view->dados = $tbUnu->getUnidadeUsuarios($usr_codigo)->toArray();
        
        return $this->render("dados",null,true);
    }
        
    public function inativaAction(){
        $usr_codigo = $this->_getParam("usr_codigo",FALSE); 
        $array_usr = array("usr_codigo"=>$usr_codigo,   
                            "usr_ativo" => "N");
        $tbUsr = new Application_Model_Usuarios();
        try{
            if($tbUsr->verificaSeTemAgendamentoFuturo($usr_codigo) >= 1)
                throw new Zend_Validate_Exception("Este profissional possui consultas agendadas para datas futuras!");
            $tbUsr->salvar($array_usr);
                $this->view->dados = 1;
        } catch (Exception $ex) {
            $this->view->dados = $ex->getMessage();
        }
        return $this->render("dados",null,true);
    }
        
    public function verificaSeExisteCpfAction(){
        $cpf = $this->_getParam("cpf",FALSE);   
        if(empty($cpf))
            return false;
        
        $tbUsr = new Application_Model_Usuarios();
        $verifica = $tbUsr->verificaSeJáExiste($cpf);
        $this->view->dados = $verifica->qtd;
        return $this->render("dados",null,true);
    }
       
    public function carregaEquipesAction() {
        $uniCodigo = $this->_request->getPost("uniCodigo");
        $usrCodigo = $this->_request->getPost("usrCodigo");
        
        $tbUsr = new Application_Model_Usuarios();

        $this->view->dados = $tbUsr->usuariosEquipes($usrCodigo,$uniCodigo);
        return $this->render("dados",null,true);
    }

    public function carregaEquipesAtendimentoIndividualAction() {
        $uniCodigo = $this->_request->getPost("uniCodigo");
        $usrCodigo = $this->_request->getPost("usrCodigo");
        
        $tbUsr = new Application_Model_Usuarios();

        $this->view->dados = $tbUsr->getUsuariosEquipes($usrCodigo,$uniCodigo);
        return $this->render("dados",null,true);
    } 
}