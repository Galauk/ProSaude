<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_EncaminhamentoExterno extends Elotech_Db_Table_Abstract {
    
    protected $_name = "encaminhamento_externo";
    protected $_primary = "enc_ext_codigo";
    
    public function salvar($dados) {
        parent::salvar($dados);
    }
    
    public function excluir($encExtCod){
        $where = "enc_ext_codigo =$encExtCod";
        $item = $this->fetchRow($where);
        if ($item)
            $item->delete();
            return true;
    }

    public function listaEncExterno($ateCodigo){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("enc_ext"=>"encaminhamento_externo"),array("enc_ext_codigo","enc_ext_agendado_para","enc_ext_descricao","enc_ext_internacao","enc_ext_urgencia","enc_ext_data"))
                    ->where("ate_codigo =?",$ateCodigo);
        return $this->fetchAll($sql);
    }
    
    public function getDadosImpEncaminhamentoExterno($ateCodigo){
        $ateCodigo = $ateCodigo;
        $sql = $this->getDefaultAdapter()->query(
            "
            SELECT enex.enc_ext_agendado_para, enex.enc_ext_contato, enex.enc_ext_data, enex.enc_ext_hora, enex.enc_ext_internacao, enex.enc_ext_urgencia, 
                enex.enc_ext_descricao, enex.enc_ext_data,enex.enc_ext_data_cad, usr.usr_nome, usu.usu_codigo,usu.usu_nome, usu.usu_datanasc, usu.usu_mae, uni.uni_desc  FROM encaminhamento_externo AS enex
                    LEFT JOIN atendimento AS ate
                        ON ate.ate_codigo = enex.ate_codigo
                    
                    INNER JOIN usuarios AS usr
                        ON usr.usr_codigo = enex.usr_codigo

                    INNER JOIN usuario AS usu
                        ON usu.usu_codigo = ate.usu_codigo

                    INNER JOIN unidade AS uni
                        ON uni.uni_codigo = ate.uni_codigo

            WHERE ate.ate_codigo = $ateCodigo
            ")->fetchAll();

        return $sql;
    }
    
    public function calculaIdade($usu_codigo){
        $usu_codigo = $usu_codigo;
        
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT extract(year from age(usuario.usu_datanasc)) FROM usuario where usu_codigo = $usu_codigo
            "
        )->fetchAll();

        return $sql;
    }

    public function getHistorico($ate_codigo) {
           // die("adasd");
		$where = $this->select(FALSE)
				->setIntegrityCheck(FALSE)
				->from(array("e" => "encaminhamento_externo"), array("enc_ext_agendado_para"=>"(enc_ext_agendado_para || ' ' || enc_ext_contato || ' ' || enc_ext_descricao )"))
				->where("ate_codigo=?", $ate_codigo);
                
		return $this->fetchAll($where);
	}

    public function recuperarTodosOsEncaminhamentos(){
        $sql = $this->getDefaultAdapter()->query(
            "
                SELECT cen.cr_entregue,enex.enc_ext_codigo, enex.enc_ext_agendado_para, enex.enc_ext_contato, enex.enc_ext_data, enex.enc_ext_hora, enex.enc_ext_internacao, enex.enc_ext_urgencia, enex.enc_ext_descricao, enex.enc_ext_data,enex.enc_ext_data_cad, usr.usr_nome, usu.usu_codigo,usu.usu_nome, usu.usu_datanasc, usu.usu_mae, uni.uni_desc,usu.usu_cartao_sus, dom.dom_telefone  
                FROM encaminhamento_externo AS enex
                    LEFT JOIN atendimento AS ate
                        ON ate.ate_codigo = enex.ate_codigo
                    
                    INNER JOIN usuarios AS usr
                        ON usr.usr_codigo = enex.usr_codigo

                    INNER JOIN usuario AS usu
                        ON usu.usu_codigo = ate.usu_codigo

                    INNER JOIN unidade AS uni
                        ON uni.uni_codigo = ate.uni_codigo

                    LEFT JOIN centro_de_regulacao as cen
                        on cen.encaminhamento_externo_codigo = enex.enc_ext_codigo

                    LEFT JOIN domicilio as dom
                    on dom.dom_codigo = usu.dom_codigo
            
                    order by enex.enc_ext_codigo desc
            "
        )->fetchAll();

        return $sql;
    }
}

?>
