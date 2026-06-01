<?php

function pr($array, $die = false){
    echo '<pre>';
    var_dump($array);
    echo '</pre>';
    if($die) die;
};

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	public function run() {
		// Coloca o config na session:
		$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
                Zend_Registry::set("config", $config);

		$this->loadDbInfo();

		// FrontController
		$front = Zend_Controller_Front::getInstance();

		//$aclHelper = new Elotech_Controller_Action_Helper_Acl(null, array("acl" => $acl));

		// Mudar o módulo default de lugar
		$front->setControllerDirectory(array(
			"agenda" => APPLICATION_PATH . "/modules/agenda/controllers/",
			"agendamento" => APPLICATION_PATH . "/modules/agendamento/controllers/",
			"plantao" => APPLICATION_PATH . "/modules/plantao/controllers/",
            "atendimento" => APPLICATION_PATH . "/modules/atendimento/controllers/",
			"default" => APPLICATION_PATH . "/modules/default/controllers/",
			"leito" => APPLICATION_PATH . "/modules/leito/controllers/",
			"prontuario" => APPLICATION_PATH . "/modules/prontuario/controllers/",
			"relatorio" => APPLICATION_PATH . "/modules/relatorio/controllers/",
			"materiais" => APPLICATION_PATH . "/modules/materiais/controllers/",
            "acesso" => APPLICATION_PATH . "/modules/acesso/controllers/",
            "transporte" => APPLICATION_PATH . "/modules/transporte/controllers/",
            "programasfederais" => APPLICATION_PATH . "/modules/programasfederais/controllers/",
            "domicilio" => APPLICATION_PATH . "/modules/domicilio/controllers/",
            "farmacia" => APPLICATION_PATH . "/modules/farmacia/controllers/",
            "laboratorio" => APPLICATION_PATH . "/modules/laboratorio/controllers/",
			"usuarios" => APPLICATION_PATH . "/modules/usuarios/controllers/",
			"ferramentas" => APPLICATION_PATH . "/modules/ferramentas/controllers/",
			"estratificacao" => APPLICATION_PATH . "/modules/estratificacao/controllers/"
		));
		
		//implementar futuramente
		//,"vacina" => APPLICATION_PATH . "/modules/vacina/controllers/"

        Zend_Loader::loadClass("Elotech_Acl");
        Zend_Loader::loadClass("Elotech_Controller_Action_Helper_Acl");

        $acl = new Elotech_Acl();
        $aclHelper = new Elotech_Controller_Action_Helper_Acl(null, array("acl" => $acl));
        Zend_Controller_Action_HelperBroker::addHelper($aclHelper);

		// Log do firebug
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        Zend_Registry::set("logger", $logger);
        //Zend_Registry::get("logger")->log("", Zend_Log::INFO);

        $writer->setEnabled( $config->logger->firebug );

        // Log do firebug para o banco de dados
        $profiler = new Zend_Db_Profiler_Firebug('SQL\'s');
        $profiler->setEnabled( $config->logger->db );
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setProfiler($profiler);
        Zend_Db_Table::setDefaultAdapter($db);

		parent::run();
	}

	/**
	 * Lê o arquivo de configuração do banco de dados, no diretório WebSocialResources
	 */
	private function loadDbInfo() {
		$config = Zend_Registry::get("config");
		$file = $config->WSResources->dbConfig;

		$xml = new Zend_Config_Xml($file);
		$banco = (object) array(
			"adapter" => base64_decode($xml->conexao->adapter),
			"params" => array(
				"host" => base64_decode($xml->conexao->host),
				"username" => base64_decode($xml->conexao->user),
				"password" => base64_decode($xml->conexao->password),
				"dbname" => base64_decode($xml->conexao->dbname),
				"charset" => base64_decode($xml->conexao->charset),
				"port" => (int) base64_decode($xml->conexao->porta)
			)
		);

		$db = Zend_Db::factory($banco->adapter,$banco->params);
		Zend_Db_Table::setDefaultAdapter($db);

	}


}
