<?php

class Relatorio_BpaController extends Elotech_Controller_Action_Relatorio {

    private $tbBpa;

    public function init() {

        $this->tbBpa = new Application_Model_BPA();
    }

    public function indexAction() {

        $rel = $this->_getParam("tipo", false);
        $this->view->title = "Bpa $rel";
        $this->view->action = $rel;
        $this->render("index");
    }

    public function consolidadoAction() {
        Zend_Layout::getMvcInstance()->setLayout("relatorio");
        //echo "<pre>".print_r($_POST,1);die();
        $data_inicial = $this->_request->getPost("data_inicial", FALSE);
        $data_final = $this->_request->getPost("data_final", FALSE);
        $competencia = $this->_request->getPost("competencia", FALSE);
        $uni_cnes = $this->_request->getPost("codigo_convenio", FALSE);

        $comp_temp = explode("/", $competencia);
        $competencia = $comp_temp[1] . $comp_temp[0];
        $this->view->dados = $this->tbBpa->relConsolidado($uni_cnes, $data_inicial, $data_final, $competencia);

        $this->view->uni_cnes = $uni_cnes;
        $this->view->competencia = $this->_request->getPost("competencia", FALSE);

        $this->view->data_inicial = $data_inicial;
        $this->view->data_final = $data_final;
        $array = array('data_inicial' => $data_inicial,
            'data_final' => $data_final);
        $this->view->params = serialize($array);
        $this->view->title = "Bpa Consolidado";
    }

    public function buscarAction() {
        $term = $this->_getParam("term", FALSE);
        $tipo = $this->_getParam("tipo", FALSE);
        if (!$term) {
            return false;
        }

        if ($tipo == "consolidado") {
            $tbUni = new Application_Model_Unidade();
            $this->view->dados = $tbUni->buscarLocais($term);
        } else if ($tipo == "individualizado") {
            $tbUni = new Application_Model_Unidade();
            $this->view->dados = $tbUni->buscarLocais($term);
        }

        return $this->render("dados", NULL, TRUE);
    }

}
