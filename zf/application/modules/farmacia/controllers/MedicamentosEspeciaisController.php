<?php

use Dompdf\Exception;

session_start();

class Farmacia_MedicamentosEspeciaisController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->acl->allow(NULL);
    }

    public function indexAction() {
        $this->view->title = "Medicamentos Especiais";

        $tbME = new Application_Model_MedicamentoEspecial();

        $this->view->dados = $tbME->getRelatorios();
    }
    
    public function editarAction() {     
        $this->view->title = "Medicamentos Especiais";

        $tbME = new Application_Model_MedicamentoEspecial();
    }
    
    public function posologiaAction(){
        $this->view->title = "Cadastrar Posologia";

        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();

        $id = $this->_getParam("id", FALSE);

        $recebeSolicitacoes = $tbSME->getRelatorioMedicamentos($id);

        $this->view->recebeSolicitacoes = $recebeSolicitacoes;
        // exit();
        
    }

    public function salvarPosologiaAction(){
        $dados = $this->_request->getPost();
        // echo "<pre>";print_r($dados);die();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();
        
        for ($i = 0; $i < sizeof($dados['id']); $i++) {
            $arrayName[$i] = array("id" =>$dados['id'][$i], "quantidade" =>$dados['quantidade'][$i] , "frequencia"=>$dados['frequencia'][$i]);

            $tbSME->salvarPosologia($arrayName[$i]);
        }

        return $this->_redirect('farmacia/medicamentos-especiais');

    }

    public function deletarAction() {
        $tbME = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();

        $id = $this->_getParam("id", FALSE);

        $tbME->deletarRelatorio($id);
        $tbSME->deletarRelatorio($id);

        return $this->_redirect('farmacia/medicamentos-especiais');
    }

    public function componenteEspecializadoAction() {
        $this->view->title = "Componente Especializado";
        $tbME = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();
        
        $id = $this->_getParam('id', FALSE);

        $action = $this->_getParam('action', FALSE);

        $dados = new stdClass();

        if($action == "print") {
            echo "
            <script>
                window.print()
            </script>
            ";
        }
        
        if($id != false){
            $dados->paciente = $tbME->getRelatorio($id);
            $dados->medicamentos = $tbSME->getRelatorioMedicamentos($id);
            $this->view->dados = $dados;
        }
    }

    public function solicitacaoMedicamentosAction() {
        $this->view->title = "Solicitação de Medicamento(s)";
        $tbME = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();
        $tbUsr = new Application_Model_Unidade();

        $id = $this->_getParam('id', FALSE);

        $action = $this->_getParam('action', FALSE);

        $dados = new stdClass();

        $uniCodigo = $_SESSION['logon']['usr']->uni_codigo;

        // var_dump($uniCodigo);
        $uni_cnes = $tbUsr->getUnidade($uniCodigo)->toArray()[0];
        
        $this->view->uni_cnes = $uni_cnes['uni_cnes'];

        if($action == "print") {
            echo "
            <script>
                window.print()
            </script>
            ";
        }

        if($id != false){
            $dados->paciente = $tbME->getRelatorio($id);
            $dados->medicamentos = $tbSME->getRelatorioMedicamentos($id);
            $this->view->dados = $dados;
        }
    }

    public function imprimirSolicitacaoAction() {
        $this->_helper->layout->disableLayout();
        $tbME = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();

        $id = $this->_getParam('id', FALSE);

        $dados = new stdClass();

        if($id != false){
            $dados->paciente = $tbME->getRelatorio($id);
            $dados->medicamentos = $tbSME->getRelatorioMedicamentos($id);
            $this->view->dados = $dados;
        }
    }
    
    public function receitaEspecialAction() {
        $this->_helper->layout->disableLayout();
        $id_receita = $this->_getParam("rel_id");
        //die($id_receita);

        // error_reporting(E_ALL);
        // ini_set("error_display", 1);

        $tbME = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();
        $tbUni = new Application_Model_Unidade();
        $tbSec = new Application_Model_Secretaria();
        $tbUsr = new Application_Model_Usuarios();
        

        $dados = new stdClass();

        // if($id != false) {
            $dadosPaciente = $tbME->getRelatorio($id_receita);
            $dadosMedicamentos = $tbSME->getRelatorioMedicamentos($id_receita);
            $unidade[] = $tbUni->getDados($tbUni->getUnidadePorCnes($dadosPaciente['cnesUnidade'])->uni_codigo)->toArray();
            $unidade[] = $tbSec->getDadosSec()->toArray();

            $dados->paciente = $dadosPaciente;
            
            $dados->crm = $tbUsr->getCRM($dadosPaciente['medicoSolicitante'])['crm'];
            // echo "<pre>";
            // print_r($dados->paciente); die();
            $dados->medicamentos = $dadosMedicamentos;
            $dados->unidade = $unidade;
            $this->view->dados = $dados;
        // }

    }

    public function salvarSolicitacaoAction() {
        error_reporting(E_ALL);
        ini_set("error_display", 1);
        $dados = $this->_request->getPost();
            
        $tbSolicitacao = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();

        $medicamentos = array();
        
        for($i = 0; $i < count($dados['nomeMed']); $i++){
            if($dados['nomeMed'][$i][0] != "") {
                $medicamentos['medicamentos'][$i]['nomeMed'] = $dados['nomeMed'][$i][0];
                if(isset($dados['codMed']) && $dados['codMed'][$i][0] != "") {
                    $medicamentos['medicamentos'][$i]['codMed'] = $dados['codMed'][$i][0];
                }
                
                $medicamentos['medicamentos'][$i]['med_mes_1'] = $dados['med_mes_1'][$i][0];
                $medicamentos['medicamentos'][$i]['med_mes_2'] = $dados['med_mes_2'][$i][0];
                $medicamentos['medicamentos'][$i]['med_mes_3'] = $dados['med_mes_3'][$i][0];
            }
        }
        
        $qntMed = count($medicamentos['medicamentos']);
        
        unset($dados['nomeMed']);
        unset($dados['med_mes_1']);
        unset($dados['med_mes_2']);
        unset($dados['med_mes_3']);

        $dados['tipo'] = "S";

        if($dados['id']){
            
            $idUpdate = $dados['id'];
            // echo "<pre>";
            // print_r($dados);
            // die();
            $qtdMedDb = count($tbSME->getRelatorioMedicamentos($idUpdate));

            // print_r($qtdMedDb);
            
            // echo $qntMed."<br>";
            // echo $qtdMedDb;
            
            // die();

            try {
                unset($dados['id']);
                $tbSolicitacao->update($dados, "id = $idUpdate");
            } catch (Exception $ex) {
                throw new Exception($ex, 1);
            }

            if($qntMed != $qtdMedDb){
                $tbSME->delete("rel_sol_med_id = $idUpdate");
                
                if($qntMed > 0){
                    // die("qtd é maior que zero");
                    for($j = 0; $j < $qntMed; $j++) {
                        
                        $medicamentos['medicamentos'][$j]['rel_sol_med_id'] = $idUpdate;
                        try {
                            $tbSME->salvar($medicamentos['medicamentos'][$j]);
                        } catch(Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                        
                        // print_r($medicamentos['medicamentos']);
                    }
                }
            }
        } else {
            try {
                $_id = $tbSolicitacao->salvar($dados);

                if(!$_id){
                    die("erro: ".error_get_last()['message']);
                }
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        
            for($j = 0; $j < count($medicamentos['medicamentos']); $j++){
                $medicamentos['medicamentos'][$j]['rel_sol_med_id'] = $_id;
                
                $tbSME->salvar($medicamentos['medicamentos'][$j]);
                
                // print_r($medicamentos['medicamentos']);
            }
        }
        return $this->_redirect('farmacia/medicamentos-especiais');
    }

    public function salvarComponenteAction() {
        $dados = $this->_request->getPost();
        $tbSolicitacao = new Application_Model_MedicamentoEspecial();
        $tbSME = new Application_Model_SolicitacaoMedicamentoEspecial();
        $tbUsu = new Application_Model_Usuario();
        $medicamentos = array();
        
        for($i = 0; $i < count($dados['nomeMed']); $i++){
            if($dados['nomeMed'][$i][0] != "") {
                $medicamentos['medicamentos'][$i]['nomeMed'] = $dados['nomeMed'][$i][0];
                if($dados['codMed'][$i][0] != "") {
                    $medicamentos['medicamentos'][$i]['codMed'] = $dados['codMed'][$i][0];
                }
            }
        }

        unset($dados['nomeMed']);
        unset($dados['codMed']);
        
        $dados['usu_codigo'] = $tbUsu->buscarFiltro($dados['cnsPaciente'], "C")[0]['id'];
        
        $dados['tipo'] = 'L';
        $dados['cnesUnidade'] = $_SESSION['logon']['usr']->cnes_cod_cns;
        $dados['dataSolicitacao'] = date('d/m/Y');
        
        // print_r($dados); die();

        $_id = $tbSolicitacao->salvar($dados);
        
        // echo "<pre>";
        // print_r($_id);

        // die($_id);

        for($j = 0; $j < count($medicamentos['medicamentos']); $j++){
            $medicamentos['medicamentos'][$j]['rel_sol_med_id'] = $_id;
            
            $_ids[] = $tbSME->salvar($medicamentos['medicamentos'][$j]);
            
            // print_r($medicamentos['medicamentos']);
        }
        
        // $array = array_merge($dados, $medicamentos);
        
        // echo "<pre>";
        // die();

        $this->_redirect('farmacia/medicamentos-especiais');
    }
}

?>