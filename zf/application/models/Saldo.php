<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
session_start();

class Application_Model_Saldo extends Elotech_Db_Table_Abstract
{

    protected $_name    = 'saldo';
    protected $_primary = 'sal_codigo';

    public function salvar(array $data)
    {
        throw new Zend_Validate_Exception("Esta tabela só pode ser manipulada via trigger", 1000);
    }

    /**
     * Informa a quantidade do produto em estoque
     * @param int $pro_codigo
     * @param int $set_codigo
     * @param string $lote
     * @param string $validade
     * @return int
     */
    public function getSaldoAtual($pro_codigo, $set_codigo = false, $lote = "SEM_LOTE", $validade = "2900-12-31")
    {
        if (!$set_codigo) {
            $tbUsr      = new Application_Model_Usuarios;
            $set_codigo = $tbUsr->getUsrAtual()->set_codigo;
        }

        $where = $this->select(true)
            ->columns("sal_qtde")
            ->where("pro_codigo=?", $pro_codigo)
            ->where("set_codigo=?", $set_codigo)
            ->where("sal_qtde > 0")
            ->where("sal_lote=?", $lote)
            ->where("sal_validade=?", $validade);
        //die($where);
        return (int) $this->fetchRow($where)->sal_qtde;
    }

    public function getLotes($pro_codigo, $set_codigo, $somenteNaoVencidos = true, $enviados = false){
        $recebeCnesUnid = $_SESSION[logon][usr]->cnes_tp_unid_id;

        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("sal" => "saldo"), array("sal_codigo", "sal_qtde", "sal_dose_lote", "sal_lote", "sal_validade", "sal_qtde as saldo_original"))
            ->join(array("pro" => "produto"), "pro.pro_codigo=sal.pro_codigo", array("pro_nome", "pro_codigo"))
            ->join(array("ite" => "itens_movimento"), "pro.pro_codigo = ite.pro_codigo", array(""))
            // ->join(array("fab" => "fabricante"), "fab.fab_codigo= ite.fab_codigo", array("fab_descricao"))
            ->group(array("sal.sal_codigo", "sal.sal_qtde", "sal.sal_dose_lote", "sal.sal_lote", "sal.sal_validade", "sal.sal_qtde", "pro.pro_nome", "pro.pro_codigo" ))
            ->where("sal.set_codigo=?", $set_codigo)
            ->where("sal.sal_qtde > 0")
            ->where("sal.pro_codigo=?", $pro_codigo)
            ->order(array("sal.sal_validade", "sal.sal_qtde"));
            // echo '<pre>';print_r($where);die();
            // die($where);

            if($recebeCnesUnid == '05'){
                return $this->fetchAll($where);
            } else{
                if ($somenteNaoVencidos) {
                    $where->where("sal_validade >= CURRENT_DATE");
                }
            }
        return $this->fetchAll($where);
	}
	
	public function getLotesProduto($prod, $set){
		$sql = $this
            ->getDefaultAdapter()
            ->query("
				SELECT
					sal_lote,
					sal_codigo,
					max(sal_validade) as sal_validade,
					sal_qtde
				FROM saldo 
				WHERE pro_codigo = $prod
				AND set_codigo = $set
				AND sal_qtde > 0
                AND sal_validade >= CURRENT_DATE
                GROUP BY sal_lote,
                sal_codigo
            ")
			->fetchAll();

		
			
		return $sql;
	}

    public function getSaldoPorSetor($set_codigo = false)
    {
        $where = $this->select(false)
            ->setIntegrityCheck(false)
            ->from(array("s" => "saldo"));
        if ($set_codigo) {
            $where->where("set_codigo =?", $set_codigo);
        }

        return $this->fetchAll($where);
    }

    public function excluiSaldoPorSetor($setores = false)
    {
        try {
            $sql = $this
                ->getDefaultAdapter()
                ->query("DELETE FROM social.saldo WHERE set_codigo IN ($setores)")
                ->fetchAll();
            return $sql;
        } catch (Exception $ex) {
            throw new Zend_Validate_Exception("Falha ao excluir saldo: " . $ex->getMessage());
        }
        /*$item = $this->getSaldoPorSetor($set_codigo);
    if ($item) {
    foreach ($item as $value){
    try{
    $value->delete();
    } catch (Exception $exc) {
    throw new Zend_Validate_Exception($exc->getMessage());
    }
    }
    }
    return true;*/
    }
}
