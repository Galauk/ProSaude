<?php
class Transporte_ViagemUsuarioController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Agendamento de Paciente";
    }
    public function indexAction(){
        //echo "<pre>".  print_r($_POST,1);die();
        $via_data = $this->_getParam("via_data",FALSE);
        $cid_codigo_origem = $this->_getParam("cid_codigo",FALSE);
        $cid_codigo_destino = $this->_getParam("cid_codigo2",FALSE);
        $vei_codigo = $this->_getParam("vei_codigo",FALSE);
        //die(var_dump($via_data));
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem-usuario/novo.js');
        $tbVia = new Application_Model_ViagemUsuario();
        $itens = $tbVia->pesquisar($via_data,$cid_codigo_origem,$cid_codigo_destino,$vei_codigo);      
        $this->view->itens = $itens;
        //die(var_dump($itens->via_codigo));
        $tbVei = new Application_Model_Veiculo();
        $this->view->veiculo = $tbVei->getVeiculos();

        //Validação veículo cheio
        foreach ($itens as $key => $item) {
            //die(var_dump($item->via_codigo));
            $dados = $tbVei->verificaCota($item->via_codigo);
            //die(var_dump($dados));
            $total2 = ($dados->vei_capacidade - $dados->total);
            //die($total2);
            $totalCota[$item->via_codigo] = $total2;   
        }
        //die(var_dump($totalCota));
        $this->view->dados = $totalCota;
        
    }
    public function novoAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/maps.google.js');
        $via_codigo = $this->_getParam("cod",FALSE);
        $this->view->disponivel = $this->_getParam("disponivel",FALSE);
        
        $tbVia = new Application_Model_Viagem();
        $tbUnidade = new Application_Model_Unidade();
        $tbVu = new Application_Model_ViagemUsuario();
    
        $itens = $tbVu->getViagemUsuario($via_codigo);
        
        $this->view->dados = $tbVia->getViagem($via_codigo);    
        $this->view->itens = $itens;
        $this->view->unidades = $tbUnidade->getUnidade();
        
        $this->render("form");
    }

    public function editarAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/maps.google.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem-usuario/editar.js');
        $viausu_codigo = $this->_getParam("id",FALSE);     

        if (!$viausu_codigo){
            return $this->_redirect("/transporte/viagem-usuario");
        }
       
        $tbViaUsu = new Application_Model_ViagemUsuario();
        $tbVia = new Application_Model_Viagem();
        $tbAcom = new Application_Model_UsuarioAcompanhante();
        $tbUnidade = new Application_Model_Unidade();
     
        $this->view->dadosViagem =  $tbViaUsu->getViagemPorUsuario($viausu_codigo);
        $this->view->dados = $tbVia->getViagem($this->view->dadosViagem->via_codigo);      
        $acompanhantes = $tbAcom->getAcompanhantes($viausu_codigo)->toArray();
        $this->view->acompanhantes = $acompanhantes;
        $this->view->unidades = $tbUnidade->getUnidade();

        return $this->render("form");
    } 

    public function salvarAction(){        
    
        $tbViaU = new Application_Model_ViagemUsuario();        

        $localEmbarqueDoPaciente = intval($this->_getParam("localEmbarqueDoPaciente", false));

        $dom_codigo_paciente_embarque = 0;
        $uni_codigo_paciente_embarque = 0;
        
        switch ($localEmbarqueDoPaciente) {
            case 1: //casa do paciente
                $dom_codigo_paciente_embarque = intval($this->_getParam("recebeDomCodigo", false));
            break;

            case 2: //unidade de saude
                $uni_codigo_paciente_embarque = intval($this->_getParam("recuperaCodigo", false));
            break;

            case 3: //outros
                $outros_paciente_embarque = $this->_getParam("outroLocalDeEmbarque", false);
            break;
            
        }

        $this->_helper->layout->disableLayout();
        //dados para salvar o paciente da viagem
        $dados = array("viausu_codigo"=>$this->_getParam("viausu_codigo",FALSE),
                       "via_codigo"=>$this->_getParam("via_codigo",FALSE),
                       "usu_codigo"=>$this->_getParam("usu_codigo",FALSE),
                       "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                       "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),                       
                       "viausu_despesas"=>$this->_getParam("viausu_despesas",FALSE),
                       "cid_codigo_origem"=>($this->_getParam("cid_codigo",FALSE) == '' ? 17593 : $this->_getParam("cid_codigo",FALSE)),
                       "cid_codigo_destino"=>$this->_getParam("cid_codigo_2",FALSE),
                       "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                       "usr_codigo_cadastro"=>$this->_getParam("usr_codigo_cadastro",FALSE),
                       "viausu_observacao"=>$this->_getParam("viausu_observacao",FALSE),
                       "dom_codigo_paciente_embarque" => $dom_codigo_paciente_embarque,
                       "uni_codigo_paciente_embarque" => $uni_codigo_paciente_embarque,
                       "outros_paciente_embarque" => $outros_paciente_embarque,
                       "clinica" => $this->_getParam("clinica",FALSE),
                       "horario" => $this->_getParam("horario",FALSE),
                       "necessita_maca" => ($this->_getParam("necessitaMaca", false) == '' ? "F" : $this->_getParam("necessitaMaca", false)),
                       "local_embarque_do_paciente" => $localEmbarqueDoPaciente

                   );
        $viausu_codigo = $tbViaU->salvar($dados);
        
        $tbProc = new Application_Model_ViagemProcedimentoUsuario();         
       //dados dos procedimentos do paciente da viagem 
        $dadosProc = array("via_codigo"=>$this->_getParam("via_codigo",FALSE),
                           "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                           "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),
                           "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                           "viausu_codigo"=>$viausu_codigo);
        //verifica qual procedimento de pernoite e alimentacao vai gerar para o paciente
        if($dadosProc["viausu_alimentacao"] == 'TRUE'){
            $dadosProcResul = $tbProc->VerificaPerNoiteAlimentacao($dadosProc);
            $tbProc->salvar($dadosProcResul);
        }
        $tbVia = new Application_Model_Viagem();
        
        $tipo = $tbVia->getViagem($this->_getParam("via_codigo",FALSE))->vei_tipo_veiculo;
        
        $distancia = $tbProc->converteEmKm($this->_getParam("viausu_km",FALSE),$tipo);
                
        $procDistancia = array("tipo"=>$tipo,"viausu_codigo"=>$viausu_codigo);
        //verifica qual procedimento irá gerar
        $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia);        
        $quantidadeDeInsert = $tbProc->divideProcedimentosPorDistancia($distancia,$tipo);        
        for ($index = 0; $index < $quantidadeDeInsert; $index++) {
            $tbProc->salvar($dadosDistanciaResul);
        }
        
        //ACOMPANHANTE
        $tbAcom = new Application_Model_UsuarioAcompanhante();
        //dados de quais acompanhantes tem o paciente
        $dadosAcom = array("usu_codigo_1"=>$this->_getParam("usu_codigo_1",FALSE),
                           "usu_codigo_2"=>$this->_getParam("usu_codigo_2",FALSE),
                           "usu_codigo_3"=>$this->_getParam("usu_codigo_3",FALSE),
                           "usu_codigo_4"=>$this->_getParam("usu_codigo_4",FALSE));
        
        $count = 1;
        foreach ($dadosAcom as $item){            
            if($item != ''){
              
                $insertDados = array("acom_codigo"=>$this->_getParam("acom_codigo_".$count,FALSE),"usu_codigo"=>$item,"viausu_codigo"=>$viausu_codigo);
                $tbAcom->salvar($insertDados);
                
                if($dadosProc["viausu_alimentacao"]== 'TRUE'){
                    //verifica qual procedimento vai gerar para o acompanhante do paciente
                     $dadosProcResulAcom = $tbProc->VerificaPerNoiteAlimentacao($dadosProc,"s");
                     $tbProc->salvar($dadosProcResulAcom);
                 }
                $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia,"S");                
                for ($i= 0; $i < $quantidadeDeInsert; $i++){
                    $tbProc->salvar($dadosDistanciaResul);
                }           
            } 
            $count++;     
        }        
        
        $this->_redirect("transporte/viagem-usuario");
    } 
    public function excluirAction(){
        $id = $this->_getParam("id",false);  
        
        $tbAcom = new Application_Model_UsuarioAcompanhante();
        $tbAcom->excluir($id);

        $tbProcU = new Application_Model_ViagemProcedimentoUsuario();
        $tbProcU->excluir($id);
      
        $tbViaU = new Application_Model_ViagemUsuario();
        $tbViaU->excluir($id);

        $this->_redirect("transporte/viagem-usuario/novo/cod/2");
    }


    public function relViagemAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/relatorio.css','all');

        $id = $this->_getParam("cod",false);  
        
        $this->view->title = "Relatorio De Viagem";
        
        $tbVia = new Application_Model_Viagem();
        $this->view->dados = $tbVia->getViagem($id);
        
        $tbViaU = new Application_Model_ViagemUsuario();
        $this->view->dadosP = $tbViaU->getBalancoViagem($id)->toArray();
      
    }
    
    public function listaAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->headLink()->appendStylesheet($this->view->baseUrl().'/public/css/relatorio.css','all');
        $id = $this->_getParam("cod",false);  
        $this->view->title = "Lista De Viagem";
        
        $tbVia = new Application_Model_Viagem();
        $this->view->dados = $tbVia->getViagem($id);
        $this->view->params = serialize(array("dados"=>$this->view->dados->usr_nome."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Hora:</strong>". $this->view->dados->via_hora));
        
        $tbViaU = new Application_Model_ViagemUsuario();
        $dadosP = $tbViaU->getDadosFullDaViagem($id)->toArray();
        // echo "<pre>";print_r($dadosP);die();
        $i = 0;
        foreach($dadosP as $item){
            $dadosP[$i]["usu_acomp"] = $tbViaU->getDadosAcompanhantesDaViagem($dadosP[$i]["viausu_codigo"])->toArray();
            $i++;
        }
        $this->view->dadosP = $dadosP; 
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
        
    public function getCodigoProcedimentoTfdAction() {
        $this->_helper->layout->disableLayout();
         
        $tbProc = new Application_Model_Procedimento();
        $proc_codigo_sus = $this->_getParam("proc_codigo_sus");
       
        $this->view->dados =$tbProc->getProcedimentoPeloCodigoSus($proc_codigo_sus)->toArray();
        
       
        //echo "<pre>".  print_r($this->view->dados,1);die();
        $this->render("dados");
       
        
    }

    public function usuarioViagemAction(){
        $via_codigo = $this->_getParam("cod",FALSE);
        $tbVu = new Application_Model_Viagem();
        //die(var_dump("here"));
        $this->view->pass = $tbVu->getPassageirosViagem($via_codigo);
        //die(var_dump($tbVu->getPassageirosViagem($via_codigo)));
    }



}
?>
