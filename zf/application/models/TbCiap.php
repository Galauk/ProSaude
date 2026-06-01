<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_TbCiap extends Elotech_Db_Table_Abstract {

    protected $_name = 'tb_ciap';
    protected $_primary = 'co_seq_ciap';
    

    public function getCiaps($ate_dados=false) {
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("ciap"=>"tb_ciap"))
                      ->order("ds_ciap");
        
        if($ate_dados->ate_codigo)
            $where->where("co_seq_ciap not in (select co_ciap from rl_cds_atend_individual_ciap where ate_codigo = $ate_dados->ate_codigo)");
        
        if($ate_dados->usu_sexo == "M"){
            $where->where("co_sexo in (0,2)");
        }else{
            $where->where("co_sexo in (1,2)");
        }
        
        // die($where);
        return $this->fetchAll($where);
    }
    
    
    public function buscar($term=false) {
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("ciap"=>"tb_ciap"))
                      ->where("ds_ciap ilike '%$term%' OR co_ciap ilike '%$term%'")
                      ->order("ds_ciap");

        
        $out = array();
        $all = $this->fetchAll($where);
        foreach ($all as $item) {
                $data = $item->toArray();
                $out [] = array(
                        "id" => $item->co_seq_ciap,
                        "label" => $item->ds_ciap,
                        "data" => $data
                );
        }

        if (!count($out)) {
                $out [] = array(
                        "id" => 0,
                        "label" => "Nenhum item encontrado",
                        "data" => array("coni_codigo" => "0", "proc_nome" => "")
                );
        }
        
       return $out;
    }
 

    
    // public function buscarCiapDescricoes($term=false, $ciapsJaSelecionados=false) {
    //     $where = $this->select()
    //                 ->setIntegrityCheck(FALSE)
    //                 ->from(array("ciap"=>"tb_ciap"),array('ciap_codigo'=>'co_ciap','co_seq_ciap','ds_ciap'))
    //                 ->join(array("ciapms"=>"tb_ciap_ms"),"ciap.co_seq_ciap = ciapms.co_ciap")
    //                 ->where("ciap.ds_ciap ilike '%$term%' OR ciap.co_ciap ilike '%$term%'")
    //                 ->order("ciap.ds_ciap");
    //             if($ciapsJaSelecionados){
    //                 if($ciapsJaSelecionados != 'null')
    //                 $where->where('ciap.co_seq_ciap not IN ('.$ciapsJaSelecionados.')');
    //             }
        
    //     $out = array();
    //     $all = $this->fetchAll($where);
    //     // die($where);
        
    //     foreach ($all as $item) {
    //             $data = $item->toArray();
    //             $ciap = array(
    //                     "id" => $item->co_seq_ciap,
    //                     "codigoCiap" => $item->ciap_codigo,
    //                     "label" => $item->ds_ciap
    //             );
                
    //             if($item->ds_inclusao){
    //                 $ciap[ds_inclusao] = $item->ds_inclusao;
    //             }else{
    //                 $ciap[ds_inclusao] = '---';
    //             }
    //             if($item->ds_exclusao){
    //                 $ciap[ds_exclusao] = $item->ds_exclusao;
    //             }else{
    //                 $ciap[ds_exclusao] = '---';
    //             }
    //             $out [] = $ciap;
    //     }

    //     if (!count($out)) {
    //             $out [] = array(
    //                     "id" => 0,
    //                     "codigoCiap" => '-',
    //                     "label" => "Nenhum item encontrado",
    //                     "ds_inclusao" => '---',
    //                     "ds_exclusao" => '---'
                    
    //             );
    //     }
        
    //    return $out;
    // }
        
    public function buscarCiapDescricoes($term=false, $ciapsJaSelecionados=false) {
        $where = $this->select()
                    ->setIntegrityCheck(FALSE)
                    ->from(array("ciap"=>"tb_ciap"),array('ciap_codigo'=>'co_ciap','co_seq_ciap','ds_ciap'))
                    ->join(array("ciapms"=>"tb_ciap_ms"),"ciap.co_seq_ciap = ciapms.co_ciap")
                    ->where("ciap.ds_ciap ilike '%$term%' OR ciap.co_ciap ilike '%$term%'")
                    ->order("ciap.ds_ciap");
                if($ciapsJaSelecionados){
                    if($ciapsJaSelecionados != 'null')
                    $where->where('ciap.co_seq_ciap not IN ('.$ciapsJaSelecionados.')');
                }
        
        $out = array();
        $all = $this->fetchAll($where);
        // die($where);
        foreach ($all as $item) {
                $data = $item->toArray();
                $ciap = array(
                        "id" => $item->co_seq_ciap,
                        "codigoCiap" => $item->ciap_codigo,
                        "label" => $item->ds_ciap
                );
                
                if($item->ds_inclusao){
                    $ciap[ds_inclusao] = $item->ds_inclusao;
                }else{
                    $ciap[ds_inclusao] = '---';
                }
                if($item->ds_exclusao){
                    $ciap[ds_exclusao] = $item->ds_exclusao;
                }else{
                    $ciap[ds_exclusao] = '---';
                }
                $out [] = $ciap;
        }

        if (!count($out)) {
                $out [] = array(
                        "id" => 0,
                        "codigoCiap" => '-',
                        "label" => "Nenhum item encontrado",
                        "ds_inclusao" => '---',
                        "ds_exclusao" => '---'
                    
                );
        }
        
       return $out;
    }
    public function buscarCiapPreNatal($term)
    {

        $where = $this->select()
            ->setIntegrityCheck(FALSE)
            ->from(array("ciap" => "tb_ciap"))
            ->where("co_ciap = '$term'");
        $sql = $this->fetchRow($where);
        return $sql->co_seq_ciap;

    }


}
