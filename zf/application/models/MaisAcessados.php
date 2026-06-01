<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_MaisAcessados extends Elotech_Db_Table_Abstract {

    protected $_name = 'mais_acessados';
    protected $_primary = 'ma_codigo';

    /**
     * Soma mais um acesso na tabela mais_acessados
     * @param string $module
     * @param string $controller
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function maisUm($module, $controller) {
        $url = "zf/$module/$controller";

        $item = $this->buscar($url);
        $item->ma_contador++;
        $item->save();
        return $item;
    }

    /**
     * Busca uma registro da tabela mais_acessados
     * @param string $url
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function buscar($url) {
        $item = $this->fetchRow("ma_url='$url'");
        if (!$item) {
            $item = $this->fetchNew();
            $item->ma_url = $url;
        }

        return $item;
    }

    public function getMaisAcessados() {
        // retorna somente os itens com titulo
        // itens cadastrados automaticamente pelo ZF podem não ter titulo
        // para ignorar um item (zf/default/index, por exemplo) retire o titulo
        return $this->fetchAll("ma_title IS NOT NULL AND ma_title <> 'Erro'", array("ma_contador DESC", new Zend_Db_Expr('ma_print ASC NULLS LAST')));
    }

}
