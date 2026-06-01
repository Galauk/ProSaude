<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_TbQstQuestao extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_qst_questao';
    protected $_primary = 'co_seq_qst_questao';

    public function getDadosPerguntasMenorSeis() {
        $sql = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("tbl" => "tb_qst_questao"))
                ->where("co_qst_questao IN (1,3,4,5,6,7,8,9,10)")
                ->where("co_qst_ficha = 1")
                ->order(array("co_qst_questao ASC"));

        //die($sql);
        return $this->fetchAll($sql);
    }

    public function getDadosPerguntasMaiorSeis() {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("tbl" => "tb_qst_questao"))
            ->where("co_qst_questao IN (21,22,23,24,25,26,28,29,30,31,32,33,34,35,36,37,38,39,40,41)")
            ->where("co_qst_ficha = 1")
            ->order(array("co_qst_questao ASC"));

        //die($sql);
        return $this->fetchAll($sql);
    }

    public function getDadosPerguntasMaiorDois() {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("tbl" => "tb_qst_questao"))
            ->where("co_qst_questao IN (11,12,14,15,16,17,18,19,20)")
            ->where("co_qst_ficha = 1")
            ->order(array("co_qst_questao ASC"));

        //die($sql);
        return $this->fetchAll($sql);
    }

    public function getDadosResposta($codQuestoes) {
        $sql = $this->select(FALSE)
            ->setIntegrityCheck(FALSE)
            ->from(array("qsr" => "tb_qst_resposta"),array("co_qst_resposta","no_qst_resposta"))
            ->join(array("qpr"=>"tb_qst_questao_resposta"),"qpr.co_qst_resposta = qsr.co_qst_resposta", "co_qst_questao")
            ->where("co_qst_questao IN ($codQuestoes)")
            ->order(array("co_qst_questao ASC", "co_qst_resposta ASC"));
       // die($sql);
        return $this->fetchAll($sql);
    }

//    public function getDadosRespostaMaiorSeis($codQuestoes) {
//        $sql = $this->select(FALSE)
//            ->setIntegrityCheck(FALSE)
//            ->from(array("qsr" => "tb_qst_resposta"),array("co_qst_resposta","no_qst_resposta"))
//            ->join(array("qpr"=>"tb_qst_questao_resposta"),"qpr.co_qst_resposta = qsr.co_qst_resposta", "co_qst_questao")
//            ->where("co_qst_questao IN ($codQuestoes)")
//            ->order(array("co_qst_questao ASC", "co_qst_resposta ASC"));
//        // die($sql);
//        return $this->fetchAll($sql);
//    }
//
//    public function getDadosRespostaMaiorDois($codQuestoes) {
//        $sql = $this->select(FALSE)
//            ->setIntegrityCheck(FALSE)
//            ->from(array("qsr" => "tb_qst_resposta"),array("co_qst_resposta","no_qst_resposta"))
//            ->join(array("qpr"=>"tb_qst_questao_resposta"),"qpr.co_qst_resposta = qsr.co_qst_resposta", "co_qst_questao")
//            ->where("co_qst_questao IN ($codQuestoes)")
//            ->order(array("co_qst_questao ASC", "co_qst_resposta ASC"));
//        // die($sql);
//        return $this->fetchAll($sql);
//    }
////
//    public function getDadosPorId($codFicha=FALSE) {
//        $sql = $this->select(FALSE)
//            ->setIntegrityCheck(FALSE)
//            ->from(array("rl" => "rl_cds_ficha_ativ_col_pub_alvo"),array("co_cds_ativ_col_publico_alvo"))
//            ->join(array("tcacpa"=>"tb_cds_ativ_col_publico_alvo"),"tcacpa.co_cds_ativ_col_publico_alvo = rl.co_cds_ativ_col_publico_alvo",array("no_cds_ativ_col_publico_alvo"))
//            ->where("co_cds_ficha_ativ_col =?",$codFicha);
//        return $this->fetchAll($sql);
//    }


}
