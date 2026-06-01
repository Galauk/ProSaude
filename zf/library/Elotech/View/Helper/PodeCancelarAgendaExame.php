<?php

class Elotech_View_Helper_PodeCancelarAgendaExame extends Zend_View_Helper_Abstract {

    /**
     * Verifica se o agendamento ainda pode ser cancelado
     * @param Zend_Db_Table_Row_Abstract $item item da agenda
	 * @see Application_Model_Agenda::getHistoricoDeExames()
	 * @see Application_Model_AgendaItens::podeCancelarAgendaExame()
     * @return bool 
     */
    function podeCancelarAgendaExame($item) {
		$tbAgei = new Application_Model_AgendaItens();
		return $tbAgei->podeCancelarAgendaExame($item);
    }

} 