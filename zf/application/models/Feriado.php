<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Feriado extends Elotech_Db_Table_Abstract {

    protected $_name = 'feriado';
	protected $_primary = 'fer_codigo';
    protected $_dependentTables = array();

    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }
	
	/**
	 * Verifica se a data informada é um feriado cadastrado
	 * @param string $data
	 * @return boolean verdadeiro se for feriado 
	 */
	public function ehFeriado($data){
		return (bool) $this->fetchAll("fer_data = '$data'")->count();
	}
	
	
	/**
	 * Seleciona o próximo dia válido apartir de $data
	 * *sábado e domingo podem ser considerados dias válidos
	 * @param string $data data inicial
	 * @param bool $sabado atende aos sábados?
	 * @param bool $domingo atende aos domingos?
	 * @param bool $incluirHoje hoje ainda é uma data válida?
	 * @return string próxima data válida
	 */
	public function proximoDiaValido($data=FALSE, $sabado=FALSE, $domingo=FALSE, $incluirHoje=TRUE) {
		if (!$data)
			$data = date("Y-m-d");

		list($a, $m, $d) = explode("-", $data);

		$i = ($incluirHoje ? -1 : 0);
		while (TRUE) {
			$i++;
			$proximo = mktime(0, 0, 0, $m, $d + $i, $a);

			// se não pode fazer no sábado:
			if (!$sabado && date("w", $proximo) == 6) {
				continue;
			}

			// se não pode fazer no domingo:
			if (!$domingo && date("w", $proximo) == 0) {
				continue;
			}

			// verificar se é um feriado cadastrado
			if ($this->ehFeriado(date("Y-m-d", $proximo))) {
				continue;
			}

			break;
		};

		return date("Y-m-d", $proximo);
	}

}
