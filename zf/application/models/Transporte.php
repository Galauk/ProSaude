<?

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");

class Application_Model_Transporte extends Elotech_Db_Table_Abstract{
    protected $_name = 'rotas_transporte';
    protected $_primary = 'rotcodigo';

    public function salvar(array $data){
        /*$where = $this->select(FALSE)
        ->setIntegrityCheck(FALSE)
        ->from(array("transp" => "rotas_transporte"), "rotcodigo")
        ->order("rotcodigo DESC");
        
        $id = $this->fetchRow($where);
        // echo "<pre>";
        // print_r($id->rotcodigo);
        // die();

        $data['rotdocigo'] = $id->rotcodigo+1;*/
        try{
            $retorno = parent::salvar($data);
        } catch(Exception $e){
            $retorno = $e->message();
        }
        
        return $retorno;
    }

    public function getRotas($veiculo = FALSE){
        // error_reporting(E_ALL);
        // die($veiculo);
        // $where = $this->select(false)
        //     ->setIntegrityCheck(false)
        //     ->from(array("transp" => "rotas_transporte"), array("rotcodigo", "rotdescri"))
        //     ->where("veicodigo=?", $veiculo);
        
        if($veiculo){
            $select = $this->getDefaultAdapter()->query("select rt.rotcodigo, rt.rotdescri, rt.veicodigo from rotas_transporte rt where rt.veicodigo = $veiculo")->fetchAll();
            if(count($select) == 0){
                $this->getRotas(FALSE);
            }
        } else {
            $select = $this->getDefaultAdapter()->query("select * from rotas_transporte order by rotcodigo desc")->fetchAll();
        }
        
        return $select;
    }

    public function getRota($rotcodigo = FALSE){
        // die($veiculo);
        if($rotcodigo){
            $where = $this->select(false)
                ->setIntegrityCheck(false)
                ->from(array("transp" => "rotas_transporte"))
                ->where("rotcodigo = $rotcodigo");
        } 
        
        // echo "<pre>"; print_r($select[0]); die;
        return $this->fetchRow($where);
    }

    public function getRotaVeiculo($veicodigo = FALSE){
        // die($veicodigo);
        if($veicodigo){
            $where = $this->select(false)
                ->setIntegrityCheck(false)
                ->from(array("transp" => "rotas_transporte"))
                ->where("veicodigo = $veicodigo");
        } 
        // die($where);
        // echo "<pre>"; print_r($select[0]); die;
        return $this->fetchRow($where);
    }
    
    public function getDestino($rota, $veiculo){
        // die($veiculo);
        // $where = $this->select(false)
        //     ->setIntegrityCheck(false)
        //     ->from(array("transp" => "rotas_transporte"), array("rotcodigo", "rotdescri"))
        //     ->where("veicodigo=?", $veiculo);
        
        $select = $this->getDefaultAdapter()->query("select distinct cid.cid_nome as destino, cid.cid_codigo, uf_sigla as uf from cidade cid 
        join rotas_transporte rt on rt.rotcodigo = $rota 
        join viagem v on v.vei_codigo = rt.veicodigo 
        join viagem_usuario vu on vu.via_codigo = v.via_codigo and vu.cid_codigo_destino = cid.cid_codigo
        where rt.veicodigo = $veiculo and rt.rotcodigo = $rota
        ")->fetchAll();
        
        // echo "<pre>"; print_r($select[0]); die;
        return $select;
    }

    public function getCidadesRota($rota){
        // print_r($rota);die;
        if($rota){
            $where = $this->select(false)
                ->setIntegrityCheck(false)
                ->from(array("cid" => "cidade"), array("cid_codigo", "cid_nome"))
                ->where("cid_codigo in ($rota)");
            // die($where);
            return $this->fetchAll($where);
        } else {
            return array();
        }
    }
}