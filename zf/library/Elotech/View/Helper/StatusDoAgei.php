<?php

class Elotech_View_Helper_StatusDoAgei extends Zend_View_Helper_Abstract {

    /**
     * Verifica se o atendimento ainda pode editado (após concluido)
     * @param Zend_Db_Table_Row_Abstract $ate atendimento ($ate->buscar())
     * @return bool 
     */
    function statusDoAgei($agei_status) {
		switch ($agei_status) {
			case Application_Model_AgendaItens::AGENDADO:
				return "Agendado";
				break;
			case Application_Model_AgendaItens::RECEPCIONADO:
				return "Recepcionado";
				break;
			case Application_Model_AgendaItens::FALTA:
				return "Faltou";
				break;
			case Application_Model_AgendaItens::CANCELADO:
				return "Cancelado";
				break;
			case Application_Model_AgendaItens::TRANSFERENCIA:
				return "Transferido";
				break;

			default:
				return "Não encontrado";
				break;
		}
    }

} 