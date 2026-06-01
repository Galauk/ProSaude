<?
class Transporte_ViagemUsuarioController extends Zend_Controller_Action {

    public function init(){
        $this->view->title = "Agendamento de Paciente";
    }
    
    public function indexAction(){
        // echo "<pre>".  print_r($_POST,1);die();
        $via_data = $this->_getParam("via_data_ida",FALSE);
        $cid_codigo_origem = $this->_getParam("cid_codigo",FALSE);
        $cid_codigo_destino = $this->_getParam("cid_codigo2",FALSE);
        $vei_codigo = $this->_getParam("vei_codigo",FALSE);
        // die(var_dump($via_data));
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem-usuario/novo.js');
        $tbVia = new Application_Model_ViagemUsuario();
        $itens = $tbVia->pesquisar($via_data,$cid_codigo_origem,$cid_codigo_destino,$vei_codigo);      
        // die("teste");
        $this->view->itens = $itens;
        // die(var_dump($itens->via_codigo));
        $tbVei = new Application_Model_Veiculo();
        $this->view->veiculo = $tbVei->getVeiculos();

        $tbTransp = new Application_Model_Transporte();

        // Validação veículo cheio
        foreach ($itens as $key => $item) {
            //die(var_dump($item->via_codigo));
            $dados = $tbVei->verificaCota($item->via_codigo);
            // die(var_dump($dados));
            $total2 = ($dados->vei_capacidade - $dados->total);
            //die($total2);
            $totalCota[$item->via_codigo] = $total2;   
        }
        //die(var_dump($totalCota));
        $this->view->dados = $totalCota;
    }

    public function novoAction() {
        // error_reporting(E_ALL);
        // $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/maps.google.js');
        
        $via_codigo = $this->_getParam("cod",FALSE);

        // die ($via_codigo);
        $this->view->disponivel = $this->_getParam("disponivel",FALSE);
        
        $tbVia = new Application_Model_Viagem();
        $tbVu = new Application_Model_ViagemUsuario(); 
        $tbTransp = new Application_Model_Transporte();
        $tbUnidade = new Application_Model_Unidade();
        
        $itens = $tbVu->getViagemUsuario($via_codigo);
        
        $this->view->itens = $itens;
        
        $this->view->dados = $tbVia->getViagem($via_codigo);
        $this->view->rotas = $tbTransp->getRotaVeiculo($this->view->dados['vei_codigo']);
        // print_r($this->view->rotas); die;
        // echo 1;
        $this->view->cidadesDestino = $tbTransp->getCidadesRota($this->view->rotas['cid_codigo']);
        $this->view->unidades = $tbUnidade->getUnidade();
        
        $this->render("form");
    }

    public function editarAction() {
        // $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/maps.google.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/public/js/transporte/viagem-usuario/editar.js');
        $viausu_codigo = $this->_getParam("id",FALSE);     

        if (!$viausu_codigo){
            return $this->_redirect("/transporte/viagem-usuario");
        }
       
        $tbViaUsu = new Application_Model_ViagemUsuario();
        $tbVia = new Application_Model_Viagem();
        $tbAcom = new Application_Model_UsuarioAcompanhante();
        $tbUnidade = new Application_Model_Unidade();
        $tbTransp = new Application_Model_Transporte();

        $acompanhantes = $tbAcom->getAcompanhantes($viausu_codigo)->toArray();
        
        $this->view->rotas = $tbTransp->getRotas($tbVia->getViagem($via_codigo)['vei_codigo']);
        $this->view->dadosViagem =  $tbViaUsu->getViagemPorUsuario($viausu_codigo);
        $this->view->dados = $tbVia->getViagem($this->view->dadosViagem->via_codigo);      
        $this->view->acompanhantes = $acompanhantes;
        $this->view->unidades = $tbUnidade->getUnidade();

        return $this->render("form");
    } 

    public function salvarAction(){
        // error_reporting(E_ALL);
    
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
        
        $via_codigo = $this->_getParam("via_codigo",FALSE);

        //dados para salvar o paciente da viagem
        $dados = array(
            "viausu_codigo"=>$this->_getParam("viausu_codigo",FALSE),
            "via_codigo"=> $via_codigo,
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
            "local_embarque_do_paciente" => $localEmbarqueDoPaciente,
            "rotas_transporte" => $this->_getParam("rotas_transporte",FALSE),
        );
        
        $vei_codigo = $this->_getParam("vei_codigo", FALSE);

        $tipo_viagem = $this->_getParam("via_tipo", FALSE);

        if($tipo_viagem == "2"){
            $dataDe = explode("/", $this->_getParam('de', FALSE));
            $dataAte = explode('/', $this->_getParam('ate', FALSE));

            $de = strtotime($dataDe[2].'-'.$dataDe[1].'-'.$dataDe[0]);
            $ate = strtotime($dataAte[2].'-'.$dataAte[1].'-'.$dataAte[0]);

            $cont = $de;
            
            $array = array();

            $tbVia = new Application_Model_Viagem();

            // error_reporting(E_ALL);
            
            // echo "<pre>";
            
            while($cont <= $ate){
                if(!in_array(date("w", $cont), array(6,7))){
                    $data = "";
                    $data = date("Y-m-d", $cont);
                    // echo $data;
                    $via = $tbVia->getViagemData($data, $vei_codigo);
                    
                    // echo "<pre>";print_r(count($via));
                    // die();

                    if(count($via) > 0) {
                        $dados['via_codigo'] = $via['via_codigo'];
                        
                        $viausu_codigo = $tbViaU->salvar($dados);
                        
                        // print_r($idVIa); die;
                        $tbProc = new Application_Model_ViagemProcedimentoUsuario();         
                        // dados dos procedimentos do paciente da viagem 
                        $dadosProc = array(
                            "via_codigo"=>$via['via_codigo'],
                            "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                            "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),
                            "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                            "viausu_codigo"=>$viausu_codigo
                        );

                        //verifica qual procedimento de pernoite e alimentacao vai gerar para o paciente
                        if($dadosProc["viausu_alimentacao"] == 'TRUE'){
                            $dadosProcResul = $tbProc->VerificaPerNoiteAlimentacao($dadosProc);
                            $tbProc->salvar((array)$dadosProcResul);
                        }

                        $tbVia = new Application_Model_Viagem();
                        
                        $tipo = $tbVia->getViagem($via['via_codigo'])->vei_tipo_veiculo;
                        
                        $distancia = $tbProc->converteEmKm($this->_getParam("viausu_km",FALSE),$tipo);
                                
                        $procDistancia = array("tipo"=>$tipo,"viausu_codigo"=>$viausu_codigo);
                        //verifica qual procedimento irá gerar
                        $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia);        
                        $quantidadeDeInsert = $tbProc->divideProcedimentosPorDistancia($distancia,$tipo);        
                        for ($index = 0; $index < $quantidadeDeInsert; $index++) {
                            $tbProc->salvar((array)$dadosDistanciaResul);
                        }
                        
                        //ACOMPANHANTE
                        $tbAcom = new Application_Model_UsuarioAcompanhante();
                        //dados de quais acompanhantes tem o paciente
                        $dadosAcom = array(
                            "usu_codigo_1"=>$this->_getParam("usu_codigo_1",FALSE),
                            "usu_codigo_2"=>$this->_getParam("usu_codigo_2",FALSE),
                            "usu_codigo_3"=>$this->_getParam("usu_codigo_3",FALSE),
                            "usu_codigo_4"=>$this->_getParam("usu_codigo_4",FALSE)
                        );
                        
                        $count = 1;
                        foreach ($dadosAcom as $item){            
                            if($item != ''){
                                $insertDados = array("acom_codigo"=>$this->_getParam("acom_codigo_".$count,FALSE),"usu_codigo"=>$item,"viausu_codigo"=>$viausu_codigo);
                                $tbAcom->salvar($insertDados);
                                
                                if($dadosProc["viausu_alimentacao"]== 'TRUE'){
                                    //verifica qual procedimento vai gerar para o acompanhante do paciente
                                    $dadosProcResulAcom = $tbProc->VerificaPerNoiteAlimentacao($dadosProc,"s");
                                    $tbProc->salvar((array)$dadosProcResulAcom);
                                }
                                $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia,"S");                
                                for ($i= 0; $i < $quantidadeDeInsert; $i++){
                                    $tbProc->salvar((array)$dadosDistanciaResul);
                                }           
                            } 
                            $count++;     
                        }
                    } else {
                        $novaViagem = $tbVia->getViagemCod($dados['via_codigo']);
                        $arr = [];
                        $arr = $novaViagem->toArray();
                        unset($arr['via_codigo']);
                        // echo "<pre>"; print_r($arr); die;
                        $arr['via_data_ida'] = $data;
                        $arr['via_data_retorno'] = $data;
                        // print_r($novaViagem->toArray()); die;

                        // echo "<pre>"; print_r($arr); die;
                        
                        $idVia = $tbVia->salvarViagem($arr);

                        // echo "<pre>"; print_r($idVia); die;
                        
                        $dados['via_codigo'] = $idVia;
                        // echo "<pre>"; print_r($dados);
                        $viausu_codigo = $tbViaU->salvar($dados);
                        
                        // print_r($dados); // die;
                        $tbProc = new Application_Model_ViagemProcedimentoUsuario();         
                        // dados dos procedimentos do paciente da viagem 
                        $dadosProc = array("via_codigo"=>$idVia,
                                        "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                                        "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),
                                        "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                                        "viausu_codigo"=>$viausu_codigo);
                        
                        // verifica qual procedimento de pernoite e alimentacao vai gerar para o paciente
                        if($dadosProc["viausu_alimentacao"] == 'TRUE'){
                            $dadosProcResul = $tbProc->VerificaPerNoiteAlimentacao($dadosProc);
                            $tbProc->salvar((array)$dadosProcResul);
                        }

                        $tbVia = new Application_Model_Viagem();
                        
                        $tipo = $tbVia->getViagem($idVia)->vei_tipo_veiculo;
                        
                        $distancia = $tbProc->converteEmKm($this->_getParam("viausu_km",FALSE),$tipo);
                                
                        $procDistancia = array("tipo"=>$tipo,"viausu_codigo"=>$viausu_codigo);
                        //verifica qual procedimento irá gerar
                        $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia);        
                        $quantidadeDeInsert = $tbProc->divideProcedimentosPorDistancia($distancia,$tipo);        
                        for ($index = 0; $index < $quantidadeDeInsert; $index++) {
                            $tbProc->salvar((array)$dadosDistanciaResul);
                        }
                        
                        //ACOMPANHANTE
                        $tbAcom = new Application_Model_UsuarioAcompanhante();
                        //dados de quais acompanhantes tem o paciente
                        $dadosAcom = array(
                            "usu_codigo_1"=>$this->_getParam("usu_codigo_1",FALSE),
                            "usu_codigo_2"=>$this->_getParam("usu_codigo_2",FALSE),
                            "usu_codigo_3"=>$this->_getParam("usu_codigo_3",FALSE),
                            "usu_codigo_4"=>$this->_getParam("usu_codigo_4",FALSE)
                        );
                        
                        $count = 1;
                        foreach ($dadosAcom as $item){
                            if($item != ''){
                                $insertDados = array("acom_codigo"=>$this->_getParam("acom_codigo_".$count,FALSE),"usu_codigo"=>$item,"viausu_codigo"=>$viausu_codigo);
                                $tbAcom->salvar($insertDados);
                                
                                if($dadosProc["viausu_alimentacao"]== 'TRUE'){
                                    //verifica qual procedimento vai gerar para o acompanhante do paciente
                                    $dadosProcResulAcom = $tbProc->VerificaPerNoiteAlimentacao($dadosProc,"s");
                                    $tbProc->salvar((array)$dadosProcResulAcom);
                                }
                                
                                $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia,"S");
                                
                                for ($i= 0; $i < $quantidadeDeInsert; $i++){
                                    $tbProc->salvar((array)$dadosDistanciaResul);
                                }
                            }
                            $count++;
                        }
                    }
                    $cont = $cont+86400;
                } else {
                    $cont = $cont+172800;
                }
            }
        } else if($tipo_viagem == "3"){
            // error_reporting(E_ALL);
            $dias = $this->_getParam('dias', FALSE);

            // die;
            $tbVia = new Application_Model_Viagem();
            $tbProc = new Application_Model_ViagemProcedimentoUsuario();
            $tbAcom = new Application_Model_UsuarioAcompanhante();

            
            foreach($dias as $dia){
                $data = "";
                $data = strtotime(str_replace('/', '-', $dia));
                $data_dia = "";
                $data_dia = date("Y-m-d", $data);
                
                // echo $data;
                $via = $tbVia->getViagemData($data_dia, $vei_codigo);
                // echo "<pre>";print_r($via['via_codigo']);
                // die();
                if($via['via_codigo']) {
                    $dados['via_codigo'] = $via['via_codigo'];

                    $viausu_codigo = $tbViaU->salvar($dados);
                    
                    // print_r($idVIa); die;
                    // dados dos procedimentos do paciente da viagem 
                    $dadosProc = array(
                        "via_codigo"=>$via['via_codigo'],
                        "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                        "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),
                        "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                        "viausu_codigo"=>$viausu_codigo
                    );

                    //verifica qual procedimento de pernoite e alimentacao vai gerar para o paciente
                    if($dadosProc["viausu_alimentacao"] == 'TRUE'){
                        $dadosProcResul = $tbProc->VerificaPerNoiteAlimentacao($dadosProc);
                        $tbProc->salvar((array)$dadosProcResul);
                    }
                    
                    $tipo = $tbVia->getViagem($via['via_codigo'])->vei_tipo_veiculo;
                    
                    $distancia = $tbProc->converteEmKm($this->_getParam("viausu_km",FALSE),$tipo);

                    $procDistancia = array("tipo"=>$tipo,"viausu_codigo"=>$viausu_codigo);
                    //verifica qual procedimento irá gerar
                    $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia);
                    $quantidadeDeInsert = $tbProc->divideProcedimentosPorDistancia($distancia,$tipo);
                    for ($index = 0; $index < $quantidadeDeInsert; $index++) {
                        $tbProc->salvar((array)$dadosDistanciaResul);
                    }
                    
                    //ACOMPANHANTE
                    //dados de quais acompanhantes tem o paciente
                    $dadosAcom = array(
                        "usu_codigo_1"=>$this->_getParam("usu_codigo_1",FALSE),
                        "usu_codigo_2"=>$this->_getParam("usu_codigo_2",FALSE),
                        "usu_codigo_3"=>$this->_getParam("usu_codigo_3",FALSE),
                        "usu_codigo_4"=>$this->_getParam("usu_codigo_4",FALSE)
                    );
                    
                    $count = 1;
                    foreach ($dadosAcom as $item){
                        if($item != ''){
                            $insertDados = array("acom_codigo"=>$this->_getParam("acom_codigo_".$count,FALSE),"usu_codigo"=>$item,"viausu_codigo"=>$viausu_codigo);
                            $tbAcom->salvar($insertDados);
                            
                            if($dadosProc["viausu_alimentacao"]== 'TRUE'){
                                //verifica qual procedimento vai gerar para o acompanhante do paciente
                                $dadosProcResulAcom = $tbProc->VerificaPerNoiteAlimentacao($dadosProc,"s");
                                $tbProc->salvar((array)$dadosProcResulAcom);
                            }

                            $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia,"S");
                            for ($i= 0; $i < $quantidadeDeInsert; $i++){
                                $tbProc->salvar((array)$dadosDistanciaResul);
                            }
                        }
                        $count++;
                    }
                } else {
                    $novaViagem = $tbVia->getViagemCod($dados['via_codigo']);
                    $arr = [];
                    $arr = $novaViagem->toArray();
                    unset($arr['via_codigo']);
                    // echo "<pre>"; print_r($arr); die;
                    $arr['via_data_ida'] = $data_dia;
                    $arr['via_data_retorno'] = $data_dia;
                    // print_r($novaViagem->toArray()); die;
                    
                    $idVia = $tbVia->salvarViagem($arr);
                    
                    $dados['via_codigo'] = $idVia;
                    $viausu_codigo = $tbViaU->salvar($dados);
                    
                    // print_r($dados); // die;
                    // dados dos procedimentos do paciente da viagem
                    $dadosProc = array(
                        "via_codigo"=>$idVia,
                        "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                        "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),
                        "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                        "viausu_codigo"=>$viausu_codigo
                    );
                    
                    // verifica qual procedimento de pernoite e alimentacao vai gerar para o paciente
                    if($dadosProc["viausu_alimentacao"] == 'TRUE'){
                        $dadosProcResul = $tbProc->VerificaPerNoiteAlimentacao($dadosProc);
                        $tbProc->salvar((array)$dadosProcResul);
                    }

                    
                    $tipo = $tbVia->getViagem($idVia)->vei_tipo_veiculo;
                    
                    $distancia = $tbProc->converteEmKm($this->_getParam("viausu_km",FALSE),$tipo);

                    $procDistancia = array("tipo"=>$tipo,"viausu_codigo"=>$viausu_codigo);
                    //verifica qual procedimento irá gerar
                    $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia);
                    $quantidadeDeInsert = $tbProc->divideProcedimentosPorDistancia($distancia,$tipo);
                    
                    for ($index = 0; $index < $quantidadeDeInsert; $index++) {
                        $tbProc->salvar((array)$dadosDistanciaResul);
                    }
                    
                    //ACOMPANHANTE
                    
                    //dados de quais acompanhantes tem o paciente
                    $dadosAcom = array(
                        "usu_codigo_1"=>$this->_getParam("usu_codigo_1",FALSE),
                        "usu_codigo_2"=>$this->_getParam("usu_codigo_2",FALSE),
                        "usu_codigo_3"=>$this->_getParam("usu_codigo_3",FALSE),
                        "usu_codigo_4"=>$this->_getParam("usu_codigo_4",FALSE)
                    );
                    
                    $count = 1;
                    foreach ($dadosAcom as $item){
                        if($item != ''){
                            $insertDados = array("acom_codigo"=>$this->_getParam("acom_codigo_".$count,FALSE),"usu_codigo"=>$item,"viausu_codigo"=>$viausu_codigo);
                            $tbAcom->salvar($insertDados);
                            
                            if($dadosProc["viausu_alimentacao"]== 'TRUE'){
                                //verifica qual procedimento vai gerar para o acompanhante do paciente
                                $dadosProcResulAcom = $tbProc->VerificaPerNoiteAlimentacao($dadosProc,"s");
                                $tbProc->salvar((array)$dadosProcResulAcom);
                            }
                            
                            $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia,"S");
                            
                            for ($i= 0; $i < $quantidadeDeInsert; $i++){
                                $tbProc->salvar((array)$dadosDistanciaResul);
                            }
                        }
                        $count++;
                    }
                }
            }
        } 
        else { //salvar unica
            $dias = $this->_request->getParam("data_ida", FALSE);
            $tbVia = new Application_Model_Viagem();
            $tbProc = new Application_Model_ViagemProcedimentoUsuario();
            $tbAcom = new Application_Model_UsuarioAcompanhante();

            //die($dias);
            $data = "";
            $data = strtotime(str_replace('/', '-', $dias));
            $data_dia = "";
            $data_dia = date("Y-m-d", $data);
            //die($data_dia);
            //echo"<pre>";print_r($dias);die();
            $via = $tbVia->getViagemData($data_dia, $vei_codigo);
            
            $dados['via_codigo'] = $via['via_codigo'];
           
            //echo"<pre>";print_r($dados);die();
            $viausu_codigo = $tbViaU->salvar($dados);
                    // print_r($idVIa); die;
                    // dados dos procedimentos do paciente da viagem 
                    
            $dadosProc = array(
                "via_codigo"=>$via['via_codigo'],
                "viausu_alimentacao"=>$this->_getParam("viausu_alimentacao",FALSE),
                "viausu_pernoite"=>$this->_getParam("viausu_pernoite",FALSE),
                "viausu_km"=>$this->_getParam("viausu_km",FALSE),
                "viausu_codigo"=>$viausu_codigo
            );
            //echo"<pre>";print_r($dadosProc);die();
            // echo $data;
            $via = $tbVia->getViagemData($data_dia, $vei_codigo); 
            //die("teste");
            $tipo = $tbVia->getViagem($idVia)->vei_tipo_veiculo;
                    
            $distancia = $tbProc->converteEmKm($this->_getParam("viausu_km",FALSE),$tipo);

            $procDistancia = array("tipo"=>$tipo,"viausu_codigo"=>$viausu_codigo);
            //verifica qual procedimento irá gerar
            $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia);
            $quantidadeDeInsert = $tbProc->divideProcedimentosPorDistancia($distancia,$tipo);
            
            for ($index = 0; $index < $quantidadeDeInsert; $index++) {
                $tbProc->salvar((array)$dadosDistanciaResul);
            }
            
            //ACOMPANHANTE
            
            //dados de quais acompanhantes tem o paciente
            $dadosAcom = array(
                "usu_codigo_1"=>$this->_getParam("usu_codigo_1",FALSE),
                "usu_codigo_2"=>$this->_getParam("usu_codigo_2",FALSE),
                "usu_codigo_3"=>$this->_getParam("usu_codigo_3",FALSE),
                "usu_codigo_4"=>$this->_getParam("usu_codigo_4",FALSE)
            );
            
            $count = 1;
            foreach ($dadosAcom as $item){
                if($item != ''){
                    $insertDados = array("acom_codigo"=>$this->_getParam("acom_codigo_".$count,FALSE),"usu_codigo"=>$item,"viausu_codigo"=>$viausu_codigo);
                    $tbAcom->salvar($insertDados);
                    
                    if($dadosProc["viausu_alimentacao"]== 'TRUE'){
                        //verifica qual procedimento vai gerar para o acompanhante do paciente
                        $dadosProcResulAcom = $tbProc->VerificaPerNoiteAlimentacao($dadosProc,"s");
                        $tbProc->salvar((array)$dadosProcResulAcom);
                    }
                    
                    $dadosDistanciaResul = $tbProc->VerificaQuaisProcedimentosIraGerarDeDistancia($procDistancia,"S");
                    
                    for ($i= 0; $i < $quantidadeDeInsert; $i++){
                        $tbProc->salvar((array)$dadosDistanciaResul);
                    }
                }
                $count++;
            }

        }
        // die;
        $this->_redirect("transporte/viagem-usuario/novo/cod/".$via_codigo);
    } 

    public function excluirAction(){
        $id = $this->_getParam("id",false);  
        
        $tbAcom = new Application_Model_UsuarioAcompanhante();
        $tbAcom->excluir($id);

        $tbProcU = new Application_Model_ViagemProcedimentoUsuario();
        $tbProcU->excluir($id);
      
        $tbViaU = new Application_Model_ViagemUsuario();
        $tbViaU->excluir($id);

        $this->_redirect("transporte/viagem-usuario");
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
        //echo "<pre>";print_r($dadosP);die();
        $i = 0;
        foreach($dadosP as $item){
            $dadosP[$i]["usu_acomp"] = $tbViaU->getDadosAcompanhantesDaViagem($dadosP[$i]["viausu_codigo"])->toArray();
            $i++;
        }
        //echo "<pre>";var_dump($dadosP);die();
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

    public function getDestinoAction(){
        $rt_codigo = $this->_getParam('rota', FALSE);
        $veicodigo = $this->_getParam('veiculo', FALSE);

        $tbTransp = new Application_Model_Transporte();

        $this->view->dados = $tbTransp->getDestino($rt_codigo, $veicodigo);
        $this->render("dados");
    }
}
