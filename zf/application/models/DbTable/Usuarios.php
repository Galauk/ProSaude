<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_DbTable_Usuarios extends Elotech_Db_Table_Abstract {

    protected $_name = 'usuarios';
    protected $_primary = 'usr_codigo';
    protected $_sequence = 'seq_usr_codigo_9041';

    public function executaSqlVerificaLoginExistente($termo = false) {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("usr" => "usuarios"), array("count(*) as logins"))
                ->where("retira_acentos(usr_login) = retira_acentos('$termo')");
        return $this->fetchRow($sql)->logins;
    }

}
