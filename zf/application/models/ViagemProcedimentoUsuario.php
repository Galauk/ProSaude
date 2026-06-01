<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_ViagemProcedimentoUsuario extends Elotech_Db_Table_Abstract {

    protected $_name = 'viagem_procedimento_usuario';
    protected $_primary = 'viaproc_codigo';   

    /**
     * Persiste um item (insert ou update)
     * @param array $data array de chave=>valor, cada chave corresponde a um atributo
     * @return int primary key do item (nextVal para insert) 
     */
    public function salvar(array $data) {
      //  echo "<pre>".print_r($data,1);die();
       
        $this->notEmpty(array("proc_codigo","viausu_codigo"), $data);
        $this->emptyToUnset($data);     
        return parent::salvar($data);
    }
  
    /**
     * Verifica se vai  gerar procedimento de pernoite e alimentacao ou só de alimentação ou nenhum	
     * @param Array() $dados, String $acompanhante
     * @return Array() 
     */    
    public function VerificaPerNoiteAlimentacao(array $dados,$acompanhante=FALSE) {
          //echo "<pre>".print_r($dados,1);die();
        if($dados['viausu_alimentacao']){
            $tbProc = new Application_Model_Procedimento(); 
            if(!$acompanhante){
              if($dados["viausu_pernoite"] == 'TRUE' && $dados['viausu_alimentacao'] == 'TRUE' ){               
                $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010010')->proc_codigo;
              }else if($dados['viausu_alimentacao'] == 'TRUE'){          
                $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010028')->proc_codigo; 
              }
            }else{
              if($dados["viausu_pernoite"] == 'TRUE' && $dados['viausu_alimentacao'] == 'TRUE' ){               
                $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010044')->proc_codigo;
              }else if($dados['viausu_alimentacao'] == 'TRUE'){          
                $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010052')->proc_codigo; 
              }
            }
            $dados =  array("proc_codigo" => $proc_codigo,
                         "viausu_codigo"=> $dados['viausu_codigo']);
            return $dados;
        }else{
            return false;
        }
    }
    
    /**
     * Verifica se vai  gerar procedimento de pernoite e alimentacao ou só de alimentação ou nenhum	
     * @param Array() $dados, String $acompanhante
     * @return Array() 
     */    
    public function converteEmKm($km,$tipo) {
         if($tipo == "A"){
             $km = $km / 1.6;
         }elseif($tipo == "F"){
              $km = $km / 1.852;
         }
        return (int)$km;         

    }
    
       /**
     * Verifica se vai  gerar procedimento de pernoite e alimentacao ou só de alimentação ou nenhum	
     * @param Array() $dados, String $acompanhante
     * @return Array() 
     */    
    public function divideProcedimentosPorDistancia($distancia,$tipo) {
         if($tipo == "A"){
             $distancia = $distancia / 200;
         }elseif($tipo == "F"){
              $distancia = $distancia / 27;
         }elseif($tipo == "T"){
              $distancia = $distancia / 50;
         }
        // die($distancia."A");
        return (int)$distancia;         

    }
    /**
     * Exclui um veiculo	
     * @param int $vei_codigo Código da veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function VerificaQuaisProcedimentosIraGerarDeDistancia(array $dados, $acompanhante=false) {    
        $tbProc = new Application_Model_Procedimento();
        if(!$acompanhante){
            if($dados['tipo'] == "T"){
             $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010125')->proc_codigo;
            }elseif ($dados['tipo'] == "F"){
             $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010117')->proc_codigo;
            }elseif ($dados['tipo'] == "A"){
             $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010087')->proc_codigo;
            }
        }else{
           if($dados['tipo'] == "T"){
             $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010109')->proc_codigo;
            }elseif ($dados['tipo'] == "F"){
             $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010095')->proc_codigo;
            }elseif ($dados['tipo'] == "A"){
             $proc_codigo = $tbProc->getProcedimentoPeloCodigoSus('0803010079')->proc_codigo;
            }
        }
         $dados =  array("proc_codigo" => $proc_codigo,
                        "viausu_codigo"=> $dados['viausu_codigo']);
          return $dados;
        
       
            

    }
    public function excluir($viausu_codigo=FALSE) {
        $item = $this->fetchAll("viausu_codigo=$viausu_codigo");
          
        if ($item) {
            foreach ($item as $i)
                $i->delete();
        }
    }
     /**
     * Busca ViagemUsuario	
     * @param int $dados Dados poder ser o nome do setor ou a descricai da Veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function getProcedimentoUsuario($viausu_codigo) {
       // die($viausu_codigo);
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("vu"=>"viagem_usuario"),array(""))
                            ->join(array("usu" => "usuario"), "usu.usu_codigo = vu.usu_codigo",array("usu_nome"))
                            ->join(array("vpu"=>"viagem_procedimento_usuario"),"vu.viausu_codigo = vpu.viausu_codigo",array(""))
                            ->join(array("proc"=>"procedimento"), "proc.proc_codigo = vpu.proc_codigo",array("proc_nome","proc_vlsa"))
                            ->where("vu.viausu_codigo=?",$viausu_codigo);
         //die($where);
            return $this->fetchAll($where);
    }
    
    /**
     * Busca ViagemUsuario	
     * @param int $dados Dados poder ser o nome do setor ou a descricai da Veiculo
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function pesquisar($dados=FALSE, $limit=FALSE) {
            $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("vei"=>"veiculo"),array("vei_codigo","vei_descricao","vei_placa","vei_capacidade"))
                            ->join(array("veie"=>"veiculo_especie"),"vei.veie_codigo = veie.veie_codigo" ,"veie_descricao")
                            ->join(array("veic"=>"veiculo_combustivel"), "vei.veic_codigo=veic.veic_codigo","veic_descricao")
                            ->join(array("veis"=>"veiculo_situacao"), "vei.veis_codigo=veis.veis_codigo");		
            if (is_string($dados))
                    $where->where("vei_descricao ilike '%$dados%' or vei_placa ilike '%$dados%'");
            if ($limit) {
                    $where->limit(15);
            }
            //die($where);
            return $this->fetchAll($where);
    }

    public function getProcedimentosPorViagem($dataInicial=FALSE, $dataFinal=FALSE, $veiculo=FALSE, $motorista=FALSE, $cid_codigo=FALSE){
            $where = $this->select(FALSE)
                            ->distinct()
                            ->setIntegrityCheck(FALSE)
                            ->from(array("vi"=>"viagem"),array("via_data"))
                            ->join(array("ve"=>"veiculo"),"vi.vei_codigo=ve.vei_codigo", array("vei_descricao"))
                            ->join(array("vu"=>"viagem_usuario"),"vu.via_codigo=vi.via_codigo",array("viausu_despesas"))
                            ->join(array("cid"=>"cidade"), "cid.cid_codigo=vu.cid_codigo_destino", array("cid_nome"))
                            ->join(array("usr" => "usuarios"), "usr.usr_codigo = vi.usr_codigo",array("usr_nome"))
                            ->join(array("vpu"=>"viagem_procedimento_usuario"),"vu.viausu_codigo = vpu.viausu_codigo",array(""))
                            ->join(array("proc"=>"procedimento"), "proc.proc_codigo = vpu.proc_codigo",array("proc_nome","proc_vlsa"));
                if ($dataInicial)
                    $where->where("vi.via_data>=?", $dataInicial);
                if ($dataFinal)
                    $where->where("vi.via_data<=?", $dataFinal);
                if ($veiculo)
                    $where->where("ve.vei_codigo=?", $veiculo);
                if ($motorista)
                    $where->where("vi.usr_codigo=?", $motorista);
                if ($cid_codigo)
                    $where->where("vu.cid_codigo_destino=?", $cid_codigo);
        // die($where);
            return $this->fetchAll($where);
    }
  
}

