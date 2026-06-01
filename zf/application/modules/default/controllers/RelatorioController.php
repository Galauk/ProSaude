<?php

class RelatorioController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
	}

	public function indexAction() {
		$tbRel = new Application_Model_Relatorio();
		$tbRel->relatorioGenerico("Teste", array("vac_usu_codigo"=>"Codigo","pro_codigo"=>"Produto"), "select vac_acao,pro_codigo,vac_usu_codigo FROM vacina_usuario order by vac_acao LIMIT 100","vac_acao");
	}

	public function prontuarioAction(){
		Zend_Layout::getMvcInstance()->setLayout("simples");

		$usu_codigo = $this->_getParam("id", FALSE);
		$data_inicial = $this->_getParam("de",FALSE);
		$data_final = $this->_getParam("ate",FALSE);

		if(!$usu_codigo)
			return $this->_redirect ("../"); // index do sistema

		$options = $this->_getParam("opcoes", FALSE);
		if($options)
			$options = explode(",",$options);

		$rel = new Application_Model_Relatorio();
		$this->view->dados = $rel->dadosDoPaciente($usu_codigo,$data_inicial,$data_final,$options);

		if(!$this->view->dados->usu){
			// Paciente não localizado, redirecionar
		}
	}

	public function testeIreportAction(){
		require_once($_SESSION[root].$_SESSION[modulo].'PHPJasperXML/class/tcpdf/tcpdf.php');
		require_once($_SESSION[root].$_SESSION[modulo].'PHPJasperXML/class/PHPJasperXML.inc.php');
		//require_once($_SESSION[root].$_SESSION[modulo].'PHPJasperXML/setting.php');

		$server = "localhost";
		$user = "postgres";
		$pass = "123";
		$db = "dbsocial_ipiranga_horus";

		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->connect($server,$user,$pass,$db,$cndriver="psql");

		//$PHPJasperXML->debugsql=true;
		$PHPJasperXML->arrayParameter=array("parameter"=>1);
		$PHPJasperXML->load_xml_file($_SESSION[root].$_SESSION[modulo]."PHPJasperXML/report1.jrxml");


		$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db,$cndriver="psql");
		$PHPJasperXML->outpage("D");    //page output method I:standard output  D:Download file


		//echo "<pre>" . print_r($_SESSION, 1);
		//die("aaa");
	}

}

