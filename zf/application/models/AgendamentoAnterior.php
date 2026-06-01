<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_AgendamentoAnterior extends Elotech_Db_Table_Abstract {
	
    protected $_name = 'agenda_migrate';
    protected $cli_agenda_pkey = 'id_cli_agenda';

	public function recuperaAgendamentoPorPeriodo($dataInicial, $dataFinal, $recebeIdDoUsuario){
		$recebeDataInicial = $dataInicial;
		$recebeDataFinal = $dataFinal;
		$recebeIdDoUsuario = $recebeIdDoUsuario;

		$sql = $this->getDefaultAdapter()->query(

        "
                SELECT  med.nome , cli.id_cli_medicos, cli.id_cli_clientes, usu.usu_nome, cli.data FROM cli_agenda AS cli
                        INNER JOIN cli_medicos AS med
                                ON med.id_cli_medicos = cli.id_cli_medicos
                        INNER JOIN usuario AS usu
                                ON usu.usu_codigo = cli.id_cli_clientes
                        WHERE cli.id_cli_medicos = $recebeIdDoUsuario AND med.cbos = '2253'
                        AND cli.data BETWEEN '$dataInicial' AND '$dataFinal' ORDER BY cli.data
        "
		)->fetchAll();
		return $sql;
	}
	public function recuperaAgendamentoPorPeriodoPaciente($dataInicial, $dataFinal, $recebeIdDoUsuario){
		$recebeDataInicial = $dataInicial;
		$recebeDataFinal = $dataFinal;
		$recebeIdDoUsuario = $recebeIdDoUsuario;

		$sql = $this->getDefaultAdapter()->query(
                     "
                                SELECT  med.nome , cli.id_cli_medicos, cli.id_cli_clientes, usu.usu_nome, cli.data FROM cli_agenda AS cli
                                INNER JOIN cli_medicos AS med
                                        ON med.id_cli_medicos = cli.id_cli_medicos
                                INNER JOIN usuario AS usu
                                        ON usu.usu_codigo = cli.id_cli_clientes
                                WHERE cli.id_cli_clientes = $recebeIdDoUsuario AND med.cbos = '2253' AND cli.data BETWEEN '$dataInicial' AND '$dataFinal' ORDER BY cli.data
                        "
		)->fetchAll();
		return $sql;
	}

}

