<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ProgramaProduto extends Elotech_Db_Table_Abstract {

    protected $_name = 'programa_produto';
	protected $_primary = 'prg_codigo';
    protected $_dependentTables = array();

	/**
	 * Persiste um item (insert ou update)
	 * @param array $data array de chave=>valor, cada chave corresponde a um atributo
	 * @return int primary key do item (nextVal para insert) 
	 */
    public function salvar(array $data) {
		throw new Zend_Validate_Exception( "Este método ainda não possui validações", 1000);
        return parent::salvar($data);
    }

	public function getProgramaProduto($id){
		/*$sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("pa"=>"programa_atendimento"),array("nome"=>"prg_nome"))
            ->where("programa_produto.usu_codigo = $id");*/

        $sql = $this->getDefaultAdapter()->query("
            select pa.prg_nome, pp.prg_codigo from cota_paciente as cp
                inner join programa_produto as pp on cp.prgp_codigo = pp.prgp_codigo
                inner join programa_atendimento as pa on pp.prg_codigo = pa.prg_codigo
                where usu_codigo = $id
                group by  pa.prg_nome, pp.prg_codigo
            ")->fetchAll();
			
        //die($sql);
        return $sql;
	}

	public function getProdutosGrupo($id){
		$sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("prod"=>"produto"), array("nome"=>"pro_nome"))
            ->join(array("pp" => "programa_produto"), "pp.pro_codigo = prod.pro_codigo")
            ->where("pp.prg_codigo = $id");
			
			//  die($sql);
        return $this->fetchAll($sql);
    }
    
    public function getDados($prg, $usu){
        
        $sql = $this
            ->getDefaultAdapter()
            ->query("
                select 
                    distinct on (produto.pro_codigo) produto.pro_nome, 
                    produto.pro_codigo,
                    pa.prg_nome, 
                    cp.ctp_periodo, 
                    cp.prgp_codigo,
                    sal.sal_lote,
                    cp.ctp_quantidade,
                    sal.sal_qtde,
                    MAX(sal.sal_validade) as sal_validade,
                    sal.set_codigo
                from programa_produto 
                inner join programa_atendimento as pa on pa.prg_codigo = programa_produto.prg_codigo
                inner join cota_paciente as cp on cp.prgp_codigo = programa_produto.prgp_codigo
                inner join produto on produto.pro_codigo = programa_produto.pro_codigo
                inner join saldo as sal on sal.pro_codigo = produto.pro_codigo
                where cp.usu_codigo = $usu
                and programa_produto.prg_codigo = $prg
                and sal.sal_qtde > 0
                group by 
                    produto.pro_codigo,
                    pa.prg_nome,
                    cp.ctp_periodo,
                    cp.prgp_codigo,
                    sal.sal_lote,
                    cp.ctp_quantidade,
                    sal.sal_qtde,
                    sal.sal_validade,
                    sal.set_codigo
            ")
            ->fetchAll();

            /*
            select distinct(produto.pro_codigo) from programa_produto 
            inner join prama_atendimento on programa_atendimento.prg_codigo = programa_produto.prg_codigo
            inner join cota_paciente on cota_paciente.prgp_codigo = programa_produto.prgp_codigo
            inner join produto on produto.pro_codigo = programa_produto.pro_codigo
            inner join saldo on saldo.pro_codigo = produto.pro_codigo
            where cota_paciente.usu_codigo = 6074
            and programa_produto.prg_codigo = 14
            group by produto.pro_codigo
            
            */
            
        return $sql;
        
    }
}