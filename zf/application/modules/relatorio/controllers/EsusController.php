<?php
class Relatorio_EsusController extends Elotech_Controller_Action_Relatorio {

    private $tbAte;

    public function init() {
            $this->_helper->acl->allow(NULL);		
            $this->tbAte = new Application_Model_Atendimento();
    }

    public function indexAction() {
            $this->view->title = "Visita Domiciliar por Periodo";
            $tbUni = new Application_Model_Unidade();
            $tbUsr = new Application_Model_Usuarios();
            $unidades = $tbUni->buscar();
            $profissionais = $tbUsr->getUsuarios();
            //die(var_dump($tbUsr->getUsuarios()));
            $this->view->profissionais = $profissionais;
            //die(var_dump($unidades));
            $this->view->unidades = $unidades;
    }

    public function visitaDomiciliarAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbUni = new Application_Model_Unidade();
        $dataInicial = $this->_request->getPost("data_inicial",FALSE);
        $dataFinal = $this->_request->getPost("data_final",FALSE);
        $usr_codigo = $this->_request->getPost("med_codigo",FALSE);
        $uni_codigo = $this->_request->getPost("uni_codigo",FALSE);
        $uni_desc = $this->_request->getPost("uni_desc",FALSE);
        $params = array($data_inicial = $dataInicial,
                        $data_final = $dataFinal,
                        $uni_nome = $uni_desc
                        );
        $dados = $this->tbAte->getDadosVisitaDomiciliar($usr_codigo, $uni_codigo, $dataFinal, $dataInicial);
        //die(var_dump($dados));
        foreach($dados as $key => $dado){
            //die(var_dump($dado[ate_codigo]));
            $motivo[$dado[ate_codigo]] = $this->tbAte->getMotivoPorAtendimento($dado[ate_codigo]);
            
        }
        //die(var_dump($motivo[15]));
        $this->view->dados = $dados;
        $this->view->motivo = $motivo;
        $this->view->title = "Visita Domiciliar por Periodo";
        $this->view->params = $params;
    }

    public function formNumGestanteAction(){
        $this->view->title = "Quantidade de Gestante por Periodo";
        $tbUni = new Application_Model_Unidade();
        $unidades = $tbUni->buscar();
        $this->view->unidades = $unidades;
    }

    public function relNumGestanteAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbUni = new Application_Model_Unidade();
        $dataInicial = $this->_request->getPost("data_inicial",FALSE);
        $dataFinal = $this->_request->getPost("data_final",FALSE);
        $uni_codigo = $this->_request->getPost("uni_codigo",FALSE);     
        $uni_desc = $this->_request->getPost("uni_desc",FALSE);     
        $dados = $this->tbAte->getQuantidadeGestante($uni_codigo, $dataFinal, $dataInicial);
        
        $this->view->title = "Quantidade de Gestante por Periodo";
        $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $uni_desc
                            );
        $this->view->params = $params;
    }

    public function formNumCadastroPacienteAction(){
        $this->view->title = "Quantidade de Cadastros de Pacientes por Unidade";
        $tbUni = new Application_Model_Unidade();
        $unidades = $tbUni->buscar();
        //die(var_dump($unidades));
        $this->view->unidades = $unidades;
    }

    public function relNumCadastroPacienteAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $tbUsu = new Application_Model_Usuario();
        $tbUni = new Application_Model_Unidade();
        $dataInicial = $this->_request->getPost("data_inicial",FALSE);
        $dataFinal = $this->_request->getPost("data_final",FALSE);
        $uni_codigo = $this->_request->getPost("uni_codigo",FALSE);
        $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal,
                            $uni_nome = $tbUni->getUnidade($uni_codigo)
                            );
        $dados = $tbUsu->getCadastroPorPeriodo($uni_codigo, $dataFinal, $dataInicial);
        $this->view->dados = $dados;
        $this->view->title = "Quantidade de Cadastros de Pacientes por Unidade";
        $this->view->params = $params;
    }
    
    public function formErrosEsusAction(){
        $this->view->title = "Erros fichas ESUS";        
        
    }
    
    public function relErrosEsusAction(){
         Zend_Layout::getMvcInstance()->setLayout("relatorio");  
         $dataInicial = $this->_request->getPost("data_inicial",FALSE);
        $dataFinal = $this->_request->getPost("data_final",FALSE);
         $tbEsusC = new Application_Model_EsusCadastroIndividual();
         $tbEsusAteI = new Application_Model_EsusAtendimentoIndividual();
         $fichasCadastros = $tbEsusC->getFichaPorData($dataInicial,$dataFinal);
         $fichasAtesInds = $tbEsusAteI->getFichaPorData($dataInicial,$dataFinal);
         $fichasErrosCadInd = array();
         $fichasErrosAteInd = array();
         $ContCadInd =  0 ;
         $ContAteInd =  0 ;
         foreach ($fichasCadastros as $fichaCadastro){
             if($fichaCadastro['idade'] >= 130){                
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['idade'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"Data de nascimento inválida,Paciente não pode ter mais que 130 anos");
                
             }
            if($fichaCadastro['cd_nacionalidade'] == ""){
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['cd_nacionalidade'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"Nacionalidade é um campo obrigatório");
                
             }
             $espacosNome = explode(" ", $fichaCadastro['usu_nome']);
             if(count($espacosNome)<=1) {
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['usu_nome'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"Requerido espaço em branco para indicar o sobrenome");
             }             
             $espacosNomeMae = explode(" ", $fichaCadastro['usu_mae']);
             if($fichaCadastro['usu_mae'] != '' && count($espacosNomeMae)<=1) {
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['usu_mae'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"Requerido espaço em branco para indicar o sobrenome da mãe");
             }             
             if($fichaCadastro['rac_codigo'] == ""){
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['rac_codigo'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"Raça/Cor é um campo obrigatório");
                
             }
             if($fichaCadastro['usu_sexo'] == ""){
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['usu_sexo'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"Sexo é um campo obrigatório");
                
             }
            if($fichaCadastro['eci_usu_cns'] == ""){
                 $fichasErrosCadInd[$ContCadInd] = array("ficha"=>"Cadastro Individual","campo"=>$fichaCadastro['eci_usu_cns'],"codigo"=>$fichaCadastro['usu_codigo'],"usu_nome"=>$fichaCadastro['usu_nome'],"usu_datanasc"=>$fichaCadastro['usu_datanasc'],"mensagem"=>"CNS não esta preenchido");
                
             }
                          
            $ContCadInd++;
         }
          foreach ($fichasAtesInds as $fichasAteInd){
            if($fichasAteInd['cnes_cod_cns'] == ""){                
               $fichasErrosAteInd[$ContAteInd] = array("ficha"=>"Atendimento Individual","campo"=>$fichasAteInd['cnes_cod_cns'],"codigo"=>$fichasAteInd['ate_codigo'],"usu_nome"=>$fichasAteInd['usu_nome'],"usr_nome"=>$fichasAteInd['usr_nome'],"mensagem"=>"Código CNES inválido ou vazio");

            }
            if($fichasAteInd['usu_cartao_sus'] == ""){                
               $fichasErrosAteInd[$ContAteInd] = array("ficha"=>"Atendimento Individual","campo"=>$fichasAteInd['usu_cartao_sus'],"codigo"=>$fichasAteInd['ate_codigo'],"usu_nome"=>$fichasAteInd['usu_nome'],"usr_nome"=>$fichasAteInd['usr_nome'],"mensagem"=>"CNS não esta preenchido");

            }
            $ContAteInd++;
          }
       $this->view->title = "Relatório de erros Esus";
        $params = array($data_inicial = $dataInicial,
                            $data_final = $dataFinal
                            );
        $this->view->params = serialize($array);
// echo "<pre>";var_dump($$fichasErrosAteInd); die;
          $this->view->dadosCadind = $fichasErrosCadInd;
          $this->view->dadosAteind = $fichasErrosAteInd;
       
    }
    public function formPacientesPorAreaAcsAction(){
        $this->view->title = "Quantidade de Cadastros de Domicilio";
        
    }    
      public function relPacientesPorAreaAcsAction(){
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        $this->view->title = "Quantidade de Cadastros de Domicilio";
        
        $data_inicial	= $this->_request->getPost("data_inicial", FALSE);
        $data_final		= $this->_request->getPost("data_final", FALSE);
        $nu_ine		= $this->_request->getPost("nu_ine", FALSE);
        $usr_codigo		= $this->_request->getPost("usr_codigo", FALSE);
     
        
        $tbDom = new Application_Model_Domicilio();
        $this->view->dados = $tbDom->getPacientePorArea($data_inicial, $data_final,$usr_codigo,$nu_ine);
        
        
    }

}