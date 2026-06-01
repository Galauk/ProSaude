<?php
class Usuarios_UsuariosController extends Zend_Controller_Action {
    public function init() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/usuarios/form.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/jquery.pstrength-min.1.2.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/usuarios/form.css','all');
        parent::init();
        $tbCon = new Application_Model_Conselho();
        $this->view->conselho = $tbCon->fetchAll();
        $tbEst = new Application_Model_Estado();
        $this->view->uf = $tbEst->getEstados();
    }

    public function indexAction(){
        // die("bateu Aqui");
        $page = $this->_getParam('page', 1);

       $tbUsr = new Application_Model_Usuarios();

       $tbConf = new Application_Model_Configuracao();
       $this->view->itens = $tbUsr->getUsuariosBuscaForm();

       $this->view->id_login = $tbUsr->getUsrAtual()->usr_codigo;
       $this->view->conf_valor = $tbConf->getDadosConfigPelaChave("MODULO_USUARIOS")->conf_valor_bool;
       $this->view->title = "Módulo Usuários";

       $paginator = Zend_Paginator::factory($tbUsr->getUsuariosBuscaForm());
       $paginator->setCurrentPageNumber($page)
                 ->setItemCountPerPage(15);

       $this->view->paginator = $paginator;
    }

    public function novoAction(){

        $this->_helper->layout->setLayout("simples");
        $this->render("form");
    }

    public function formAction(){
        
    }

    public function salvarAction(){
        $tbUsr = new Application_Model_Usuarios();
        $tbUnu = new Application_Model_UnidadeUsuarios();
        $tbEst = new Application_Model_Estado();
        $this->_helper->layout->disableLayout();

        $dados_usr = array(
            "usr_nome"=>mb_strtoupper($this->_getParam("usr_nome",FALSE), "UTF-8"),
            "usr_tipo_medico"=>$this->_getParam("usr_tipo_medico",FALSE),
            "con_codigo"=>($this->_getParam("con_codigo",FALSE) ? $this->_getParam("con_codigo",FALSE) : NULL),
            "usr_num_conselho"=>$this->_getParam("usr_num_conselho",FALSE),
            "cnes_sigla_est" => ($this->_getParam("usr_num_conselho",FALSE) ? $tbEst->getSiglaPorCodigo($this->_getParam("cnes_sigla_uf",FALSE))->uf_sigla : NULL),
            "usr_login"=>$this->_getParam("usr_login",FALSE),
            "usr_email"=>$this->_getParam("usr_email",FALSE),
            "usr_modulos"=>$this->_getParam("usr_modulos",FALSE),
            "usr_ativo"=>$this->_getParam("usr_ativo",FALSE),
            "cnes_cod_cns"=>$this->_getParam("cnes_cod_cns",FALSE),
            "usr_cpf"=>preg_replace('#[^0-9]#','',$this->_getParam("usr_cpf",FALSE))
        );

        // echo "<pre>";print_r($dados_usr);die();

        if($this->_request->getPost("usr_codigo", NULL)){
            $dados_usr[usr_codigo] = $this->_request->getPost("usr_codigo", NULL);
        }

        if($this->_request->getPost("usr_senha") != NULL){
            $dados_usr[usr_senha] =  md5($this->_getParam("usr_senha",FALSE));
        }
        if(!$dados_usr[usr_codigo] && !$dados_usr[usr_senha]){
            $dados_usr[usr_senha] =  md5(123);
        }


        $usr_codigo = $tbUsr->salvar($dados_usr);
        // echo "<pre>";print_r($usr_codigo);die();

        $array_codigo = array();
        if($this->_getParam("todas_unidades",FALSE) == "T"){
            $tbUni = new Application_Model_Unidade();
            $array_codigo = $tbUni->getCodUnidade()->toArray();

            $this->removeUnidadesDoUsuario($usr_codigo,$array_unidades);
        }else{
            $array_codigo = $this->_getParam("uni_codigo",FALSE);
        }
        if($usr_codigo){
            if(!empty($array_codigo)){
                if(count($array_codigo) > 1){
                    foreach($array_codigo as $uni_codigo){
                        $tbUnu->salvar(array("usr_codigo"=>$usr_codigo,
                                             "uni_codigo"=>($uni_codigo[uni_codigo] ? $uni_codigo[uni_codigo] : $uni_codigo)));
                    }
                }else{
                    $tbUnu->salvar(array("usr_codigo"=>$usr_codigo,
                                          "uni_codigo"=>($array_codigo[0] ? $array_codigo[0] : "NULL")));
                }
            }

            $esp_codigo = $this->_request->getPost("esp_codigo_u", NULL);
            if(!empty($esp_codigo)){
                $tbMesp = new Application_Model_MedicoEspecialidade();
                foreach($esp_codigo as $unidade => $especialidade){
                    $tbMesp->salvar(array("esp_codigo"=>$especialidade,
                                          "med_codigo"=>$usr_codigo,
                                          "mes_ativo" => "A",
                                          "uni_codigo" => $unidade));
                }
            }
            $set_codigo = $this->_request->getPost("set_codigo", NULL);
            if(!empty($set_codigo)){
                $tbUset = new Application_Model_UsuariosSetores();
                foreach($set_codigo as $set_codigo){
                    $tbUset->salvar(array("set_codigo"=>$set_codigo,
                                          "usr_codigo"=>$usr_codigo));
                }
            }

        }
        $this->_redirect("usuarios/usuarios/");
    }

    public function removeUnidadesDoUsuario($usr_codigo=FALSE){
       $tbUnu = new Application_Model_UnidadeUsuarios();
       $tbUnu->excluirTodos($usr_codigo);
       return true;
    }

    public function pesquisaAction() {
        $page = $this->_getParam('page', 1);
        $this->view->title = "Módulo Usuários";
        if ($this->_request->isPost()) {
            $tbUsr = new Application_Model_Usuarios();
            $tbConf = new Application_Model_Configuracao();
            $this->view->busca = $this->_request->getPost("busca");
            $this->view->itens = $tbUsr->getUsuariosBuscaForm($this->view->busca);
            $this->view->conf_valor = $tbConf->getDadosConfigPelaChave("MODULO_USUARIOS")->conf_valor_bool;

            $paginator = Zend_Paginator::factory($tbUsr->getUsuariosBuscaForm($this->view->busca));
            $paginator->setCurrentPageNumber($page)
                      ->setItemCountPerPage(15);
            $this->view->paginator = $paginator;

            $this->render("index");
        } else {
            $this->_redirect("/usuarios/usuarios/index");
        }
    }

    public function editarAction(){
        $this->_helper->layout->setLayout("simples");
        $usr_codigo = $this->_getParam("id");
        $tbUsr = new Application_Model_Usuarios();
        $tbUnu = new Application_Model_UnidadeUsuarios();
        $tbMes = new Application_Model_MedicoEspecialidade();
        $tbUset = new  Application_Model_UsuariosSetores();
        $this->view->dados = $tbUsr->getInfoUsr($usr_codigo);
        //echo"<pre>".  print_r($this->view->dados,1);
        $this->view->unidades = $tbUnu->getUnidadeUsuarios($this->view->dados->usr_codigo,1);

        $this->view->especialidades = $tbMes->getEspecialidadePorMedico($this->view->dados->usr_codigo);
        $this->view->setores = $tbUset->getSetoresPorUsuarios($this->view->dados->usr_codigo);
        return $this->render("form");
    }

    public function inativarAction(){
        $id = (int) $this->_getParam("id", 0);
        $tbUsr = new Application_Model_Usuarios();
        $dados = array("usr_codigo"=>$id,"usr_ativo"=>"N");
        $tbUsr->salvar($dados);
        return $this->_redirect ("/usuarios/usuarios");
    }

    public function excluirUnidadeAction(){
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam("id",false);
        $tbUnu = new Application_Model_UnidadeUsuarios();
        $tbUnu->excluir($id);
        $this->render("dados");
    }

    public function excluirEspecialidadeAction(){
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam("id",false);
        $tbMes = new Application_Model_MedicoEspecialidade();
        $tbMes->excluir($id);
        $this->render("dados");
    }

    public function excluirSetoresAction(){
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam("id",false);
        $tbUset = new  Application_Model_UsuariosSetores();
        $tbUset->excluir($id);
        $this->render("dados");
    }

    public function jqgridAction() {
        $page = $this->_getParam("page", 1);
        $limit = $this->_getParam("rows");
        $sidx = $this->_getParam("sidx", "id");
        $sord = $this->_getParam("sord", "ASC");

        $tbUsr = new Application_Model_Usuarios();
        $tbUsr->setFields(array("usr_codigo", "usr_nome"));
        $this->view->dados = $tbUsr->getGridResource($page, $limit, $sidx, $sord);
        //echo "<pre>".print_r( $this->view->dados,1);die();

        return $this->render("dados", NULL, TRUE);
    }

    // public function atualizarSenhaAction(){
    //     die("teste");
    //     $this->_helper->layout->disableLayout();
    //     $param = $this->_getParam("senha",null);
    //     $param = $this->_getParam("cpf",null);
    //     $tbUsr = new Application_Model_Usuarios();
    //     $this->view->dados = $tbUsr->novaSenha($senha, $cpf);
    //     // echo "<pre>";print_r($membroFamiliar);die();

    //     return $this->render("dados", NULL, TRUE);
    // }

}
?>
