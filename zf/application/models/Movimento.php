<?php

Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_Movimento extends Elotech_Db_Table_Abstract {

    protected $_name = 'movimento';
    protected $_primary = 'mov_codigo';
    protected $_sequence = 'seq_mov_codigo';
    
    /**
     * mov_tipo
     */
    const ENTRADA = "E";
    const SAIDA = "S";
    const TRANSFERENCIA = "T"; 
    
    /**
     * mov_entrada
     */
    const NORMAL = "E";
    const AJUSTE = "A";
    const EMPRESTIMO = "M";
    const INVENTARIO = "I";
    const DOACAO = "D";
    const PERMUTA = "P";
    const OUTRAS = "O";
  //const TRANSFERENCIA = "T"; 
    const DEVOLUCAO = "V";
    
    /**
     * mov_saida
     */
    const CONSUMO = "S";
    const DISPENSACAO = "D";
  //const EMPRESTIMO = "M";
  //const PERMUTA = "P";
  //const INVENTARIO = "I";
  //const AJUSTE = "A";
    const PERDAS = "R";
  //const OUTRAS = "O";
  //const TRANSFERENCIA = "T"; 
    
    public function salvar(array $data) {
        // echo "<pre>";print_r($data);die();
        $this->valoresPadrao($data);
        
        // validação:
        if(!in_array($data['mov_tipo'], array(self::ENTRADA,self::SAIDA,self::TRANSFERENCIA))){
            throw new Zend_Validate_Exception("\"mov_tipo\" precisa ser: ENTRADA, SAIDA ou TRANSFERENCIA");
        }
        
        $this->emptyToUnset($data);
        $this->peloMenosUm(array("mov_entrada","mov_saida"), $data);
        $this->peloMenosUm(array("set_entrada","set_saida"), $data);
        
        return parent::salvar($data);
    }
    
    public function salvarMovimentacaoRequisicao($dados){
        try{
            return parent::salvar($dados);
        } catch (Exception $exc) {
            throw new Zend_Validate_Exception("Falha ao cadastrar a movimentação!"+$exc->getMessage());
        }
    }
    
    private function valoresPadrao(&$data){
        $tbUsr = new Application_Model_Usuarios;
        $usu = $tbUsr->getUsrAtual();
        
        if(empty($data['mov_data'])){
            $data['mov_data'] = date("Y-m-d");
        }       
        
        if(empty($data['usr_codigo'])){
            $data['usr_codigo'] = $usu->usr_codigo;
        }   
        
        if(empty($data['mov_ip'])){
            $fun = new Application_Model_Funcoes();
            $data['mov_ip'] = (string) $fun->getIp();
        }   
        
        if($data['mov_tipo'] == self::ENTRADA && empty ($data['set_entrada'])){
            $data['set_entrada'] = $usu->set_codigo;
        }
        
        if($data['mov_tipo'] == self::SAIDA && empty ($data['set_saida'])){
            $data['set_saida'] = $usu->set_codigo;
        }
    }
    
    public function getMedicamentosDispensados($usu_codigo, $data_inicial=FALSE, $data_final=FALSE,$limit=FALSE){
        // produtos "inteiros"
        $where1 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento"),array("unidade"=>"COALESCE('1','1')","mov_codigo","mov_data"))
                ->join(array("i"=>"itens_movimento"),"i.mov_codigo=m.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=m.set_saida","")
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=m.usr_codigo","usr_nome")
                ->where("mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo);
        
                if ($data_inicial) 
            $where1->where("m.mov_data >= ?", $data_inicial);
                
        if ($data_final) 
            $where1->where("m.mov_data <= ?", $data_final);
        // produtos fracionados
        $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("vac"=>"vacina_usuario"),array("unidade"=>"COALESCE('0','0')", "vac_usu_codigo", "mov_data"=>"vac_data","ite_quantidade"=>"vac_qtde"))
                ->join(array("cont"=>"controlefracionado"),"cont.cont_codigo=vac.cont_codigo","")
                ->join(array("ite"=>"itens_movimento"),"ite.ite_codigo=cont.ite_codigo","ite_lote")
                ->join(array("pro"=>"produto"),"pro.pro_codigo=ite.pro_codigo","pro_nome")
                ->join(array("mov"=>"movimento"),"mov.mov_codigo=ite.mov_codigo","")
                ->join(array("set"=>"setor"),"set.set_codigo=mov.set_saida","")
                ->join(array("uni"=>"unidade"),"uni.uni_codigo=set.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=mov.usr_codigo","usr_nome")
                ->where("vac.usu_codigo=?",$usu_codigo);
                
                if ($data_inicial) 
            $where2->where("mov.mov_data >= '$data_inicial'");
                
        if ($data_final) 
            $where2->where("mov.mov_data <= '$data_final'");
                
                
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where1,$where2), Zend_Db_Select::SQL_UNION_ALL)
                ->order(array("mov_data DESC"));
                
        //die($where);
                if($limit)
                    $where->limit($limit);
                
        return $this->fetchAll($where);
    }
        
        public function getProntuarioMedicamentosDispensados($usu_codigo, $data_inicial=FALSE, $data_final=FALSE,$limit=FALSE){
        // CONSULTAS MOVIMENTO
        $where1 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento"),array("unidade"=>"COALESCE('1','1')","m.mov_codigo","mov_data"))
                ->join(array("i"=>"itens_movimento"),"i.mov_codigo=m.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=m.set_saida","")
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=m.usr_codigo","usr_nome")
                ->where("m.mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo);
        
                if ($data_inicial) 
            $where1->where("m.mov_data >= ?", $data_inicial);
                
        if ($data_final) 
            $where1->where("m.mov_data <= ?", $data_final);
        
                // CONSULTAS MOVIMENTO BACKUP
        $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento_bkp"),array("unidade"=>"COALESCE('1','1')","mov_codigo","mov_data"))
                ->join(array("i"=>"itens_movimento_bkp"),"i.mov_codigo=m.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto_bkp"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=m.set_saida","")
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=m.usr_codigo","usr_nome")
                ->where("m.mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo);
        
                if ($data_inicial) 
                    $where2->where("m.mov_data >= ?", $data_inicial);
                
        if ($data_final) 
                    $where2->where("m.mov_data <= ?", $data_final);
                
                
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where1,$where2), Zend_Db_Select::SQL_UNION_ALL)
                ->order(array("mov_data DESC"));
                
        if($limit)
                    $where->limit($limit);
              //  die($where);
        return $this->fetchAll($where);
    }
        
        public function relDispensados($set_codigo, $data_inicial=FALSE, $data_final=FALSE){
        // produtos "inteiros"
        $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento"),array("m.mov_codigo","mov_data"))
                ->join(array("i"=>"itens_movimento"),"i.mov_codigo=m.mov_codigo",array("ite_quantidade","ite_lote","pro_codigo"))
                ->join(array("p"=>"produto"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=m.set_saida","")
                ->join(array("usu"=>"usuario"),"usu.usu_codigo=m.usu_codigo",array("usu_nome","usu_codigo"))
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=m.usr_codigo",array("usr_nome","usr_codigo"))
                ->join(array("med"=>"usuarios"),"med.usr_codigo=m.med_codigo_interno",array("med"=>"usr_nome"))
                                ->order(array("usu_nome","m.usu_codigo","mov_data","pro_nome"));
                
                if($data_inicial){
                    $where->where("mov_data >='$data_inicial'");
                }
                if($data_final){
                    $where->where("mov_data <=' $data_final'");
                }
                if($set_codigo){
                    $where->where("set_saida = $set_codigo");
                }
                // die($where);
                return $this->fetchAll($where);
        }
        
        public function getNumPacientesAtendidosPorPeriodoSetor($codUnidade=FALSE,$codSetor=FALSE,$dataInicial=FALSE,$dataFinal=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("mov"=>"movimento"),array("COUNT(DISTINCT mov.mov_codigo) AS qtd_atendimento"))
                        ->join(array("ite"=>"itens_movimento"),"mov.mov_codigo=ite.mov_codigo",array(""))
                        ->join(array("pro"=>"produto"),"ite.pro_codigo=pro.pro_codigo",array())
                        ->join(array("set"=>"setor"),"mov.set_entrada=set.set_codigo OR mov.set_saida=set.set_codigo",array(""))
                        ->where("set.uni_codigo =?",$codUnidade)
                        ->where("mov.mov_tipo = 'S'")
                        ->where("mov.mov_saida = 'D'");
            if ($codSetor)
                    $sql->where("set.set_codigo =?",$codSetor);
            if ($dataInicial)
                    $sql->where("mov.mov_data >= '$dataInicial'");
            if ($dataFinal)
                    $sql->where("mov.mov_data <= '$dataFinal'");
            return $this->fetchRow($sql);
        }
        
        public function getNumPacientesAtendidosPorMedicamentoDispensado($codUnidade=FALSE,$codProd=FALSE,$codSetor=FALSE,$dataInicial=FALSE,$dataFinal=FALSE){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->distinct()
                        ->from(array("pro"=>"produto"),array("pro_codigo","pro_nome"))
                        ->join(array("ite"=>"itens_movimento"),"pro.pro_codigo=ite.pro_codigo","")
                        ->join(array("mov"=>"movimento"),"ite.mov_codigo=mov.mov_codigo",array("COUNT(mov.mov_codigo) AS soma"))
                        ->join(array("set"=>"setor"),"mov.set_entrada=set.set_codigo OR mov.set_saida=set.set_codigo",array("set_nome"))
                        ->where("pro.pro_situacao = 'A'")
                        ->where("set.uni_codigo =?",$codUnidade)
                        ->where("mov.mov_tipo = 'S'")
                        ->where("mov.mov_saida = 'D'");
            if ($codProd)
                    $sql->where("pro.pro_codigo =?",$codProd);
            if ($codSetor)
                    $sql->where("set.set_codigo =?",$codSetor);
                    $sql->where("mov.mov_data >= '$dataInicial'");
                    $sql->where("mov.mov_data <= '$dataFinal'");
                    $sql->group(array("pro.pro_codigo","pro.pro_nome","set.set_nome"));
                    $sql->order(array("set.set_nome","pro.pro_nome"));
                    //die($sql);
            return $this->fetchAll($sql);
        }
        
        
        
        public function getMovimentos($limit=FALSE,$term=FALSE,$mov_tipo=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->distinct()
                          ->from(array("mov"=>"movimento"),array("mov_codigo",
                                                                 "mov_data",
                                                                 "usu_codigo",
                                                                 "mov_tipo" => "(CASE WHEN mov_tipo='E' THEN 'Entrada' WHEN mov_tipo='S' THEN 'Saída'  WHEN mov_tipo='T' THEN 'Transferência' END)",
                                                                 "mov_saida" => "(CASE WHEN mov_saida = 'D' THEN 'Dispensação' WHEN mov_saida = 'S-VV' THEN 'Saída por Validade Vencida' WHEN mov_saida = 'T' THEN 'Transferência' WHEN mov_saida='S-PE' THEN 'Saida por Perda' WHEN mov_saida='S-AEA' THEN 'Saída por Amostra, Exposição e Análise' WHEN mov_saida='S-DEP' THEN 'Saída por Devolução de produto' WHEN mov_saida = 'S-D' THEN 'Saída por Doação' WHEN mov_saida = 'S-TR' THEN 'Saída por Transferência e Remanejamento' WHEN mov_saida = 'S-AS' THEN 'Saída por Apreenção Sanitária' WHEN mov_saida = 'S-E' THEN 'Saída por Empréstiomo' WHEN mov_saida = 'S-P' THEN 'Saída para Paciente' WHEN mov_saida = 'S-AE' THEN 'Saída por Ajuste' WHEN mov_saida = 'S' THEN 'Saída por Consumo' WHEN mov_saida = 'O' THEN 'Outras Saídas'  END)",
                                                                 "mov_entrada" => "(CASE WHEN mov_entrada = 'T' THEN 'Transferência' WHEN mov_entrada='E-SI' THEN 'Entrada por Saldo de Implantação' WHEN mov_entrada='E-C' THEN 'Entrada por Concorrência' WHEN mov_entrada='E-DL' THEN 'Entrada por Dispensa de Licitação' WHEN mov_entrada = 'E-CONV' THEN 'Entrada por Convite' WHEN mov_entrada = 'E-D' THEN 'Entrada por doação' WHEN mov_entrada = 'E-P' THEN 'Entrada por Pregão' WHEN mov_entrada = 'E-AE' THEN 'Entrada por Ajuste de Estoque' WHEN mov_entrada = 'E-EVENTUAL' THEN 'Entrada por Entrada Eventual' WHEN mov_entrada = 'E-O' THEN 'Entrada Ordinária' WHEN mov_entrada = 'E-TP' THEN 'Entrada por Tomada de Preços' WHEN mov_entrada = 'E-INEX' THEN 'Entrada por Inexigibilidade' WHEN mov_entrada = 'E-PER' THEN 'Entrada por Permuta' WHEN mov_entrada = 'E' THEN 'Entrada por Nota Fiscal de Compra' WHEN mov_entrada = 'M' THEN 'Entrada por Emprestimo' WHEN mov_entrada= 'I' THEN 'Entrada por Inventário' WHEN mov_entrada = 'O' THEN 'Outras Entradas' WHEN mov_entrada = 'V' THEN 'Entrada por Devolução' END)",
                                                                 "mov_tipo as tipo",
                                                                 "mov_observacao",
                                                                 "mov_nr_nota"))
                        ->join(array("im"=>"itens_movimento"),"im.mov_codigo=mov.mov_codigo","")
                        ->joinLeft(array("usu"=>"usuario"),"usu.usu_codigo=mov.usu_codigo","usu_nome")
                        ->joinLeft(array("f"=>"fornecedor"),"mov.for_codigo=f.for_codigo","for_nome")
                        ->order("mov_data DESC")
                        ->order("mov_codigo DESC");
            
            if($mov_tipo != "A" && $mov_tipo != ""){
                $where->where("mov_tipo='$mov_tipo'");
            }
            if($term){
                if($this->validaDataNormal($term)){
                    $where->where("mov_data='$term'");
                }else if(is_numeric($term)){
                    $where->where("(mov.mov_codigo=$term OR mov_nr_nota='$term')");
                }else{
                    $where->where("mov_observacao ilike'%$term%' OR for_nome ilike '%$term%' OR usu_nome ilike'%$term%'");
                }
            }
                
            //die($where);
            $tbUsr = new Application_Model_Usuarios();
            $usr_codigo = $tbUsr->getUsrAtual()->usr_codigo;
            $tbUsrSet = new Application_Model_UsuariosSetores();
            foreach($tbUsrSet->getSetoresPorUsuario($usr_codigo) as $setor){
                $setores .= $setor[set_codigo].",";
            }
            $setores = substr($setores, 0, -1);
            $where->where("set_entrada in ($setores) OR set_saida in ($setores)");
           // $where->where("(mov_saida <> 'D' OR mov_saida IS NULL)");
            
            if($limit)
                $where->limit($limit);
            //die($where);
            return $this->fetchAll($where);
        }
        
        public function validaDataNormal($dat){
            $data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como referência
            $d = $data[0];
            $m = $data[1];
            $y = $data[2];
            $res = checkdate($m,$d,$y);
            if ($res == 1){
                return true;
            } else {
                return false;
            }
        }


        public function ValidaData($dat){
                $data_br = date('d/m/Y', strtotime($dat));
                $data = explode("/","$data_br"); // fatia a string $dat em pedados, usando / como referência
                $d = $data[0];
                $m = $data[1];
                $y = $data[2];
                
                // verifica se a data é válida!
                // 1 = true (válida)
                // 0 = false (inválida)
                if($y == ""){
                    return false;
                }else{
                    $res = checkdate($m,$d,$y);
                    if ($res == 1){
                      return true;
                    } else {
                       return false;
                    }
                }
        }
        
        public function getMovimento($mov_codigo=FALSE){
            if(!$mov_codigo)
                return false;
            
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("mov"=>"movimento"))
                          ->joinLeft(array("usu"=>"usuario"),"usu.usu_codigo=mov.usu_codigo",array("usu_nome","usu_datanasc","usu_cartao_sus"))
                          ->joinLeft(array("usr"=>"usuarios"),"usr.usr_codigo=mov.med_codigo_interno","usr_nome")
                          ->joinLeft(array("med"=>"medico"),"med.med_codigo=mov.med_codigo_externo","med_nome")
                          ->where("mov_codigo=$mov_codigo");
            return $this->fetchRow($where);
            
        }
        
        public function getDadosMovimento($mov_codigo=FALSE){
            $sql = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("mov"=>"movimento"),array("mov_codigo","mov_tipo","mov_data","mov_nr_nota","mov_entrada","mov_saida","mov_observacao","mov_nr_nota"))
                          ->joinLeft(array("sets"=>"setor"),"mov.set_saida=sets.set_codigo",array("set_nome AS setor_saida"))
                          ->joinLeft(array("sete"=>"setor"),"mov.set_entrada=sete.set_codigo",array("set_nome AS setor_entrada"))
                          ->joinLeft(array("forn"=>"fornecedor"),"mov.for_codigo=forn.for_codigo",array("for_nome"))
                          ->joinLeft(array("usu"=>"usuario"),"usu.usu_codigo=mov.usu_codigo",array("usu_nome"))
                          ->where("mov.mov_codigo=$mov_codigo");
            //die($sql);
            return $this->fetchRow($sql);
        }
        
        public function getEntradas($set_codigo=FALSE,$data_inicial=FALSE,$data_final=FALSE,$psi=FALSE,$portaria=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("mov"=>"movimento"),array("mov_nr_nota"))
                          ->join(array("forn"=>"fornecedor"),"forn.for_codigo=mov.for_codigo",array("for_nome","for_cnpj"))
                          ->join(array("ite"=>"itens_movimento"),"ite.mov_codigo=mov.mov_codigo","SUM(ite_quantidade) as ite_quantidade")
                          ->join(array("pro"=>"produto"),"pro.pro_codigo=ite.pro_codigo",array("pro_nome","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao","pro_tipo"))
                          ->join(array("psi"=>"psicotropicos"),"psi.psico_codigo=pro.psico_codigo",array("psico_nome"))
                          ->where("pro_tipo = 'M'")
                          ->where("mov_data >= '$data_inicial'")
                          ->where("mov_data <= '$data_final'")
                          ->where("set_entrada=$set_codigo")
                          ->where("mov_tipo='E'")
                          ->group(array("mov_nr_nota","for_nome","for_cnpj","pro_nome","pro_codigo_dcb","pro_descricao_dcb","pro_apresentacao_concentracao","pro_tipo", "psico_nome"));
            
            if($psi=="s"){
                $where->where("pro.psico_codigo is not null");
            }
            if($portaria)
                $where->where("pro.psico_codigo in ($portaria)");
                
            return $this->fetchAll($where);
        }
        
        public function getUltimosDispensados($usu_codigo=FALSE){
                $tbConf = new Application_Model_Configuracao();
                $dias = $tbConf->getConfig("FARMACIA_TEMPO_HISTORICO");
                
                // CONSULTA DISPENSADO MOVIMENTO
            $where1 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("mov"=>"movimento"),array("unidade"=>"COALESCE('1','1')","mov.mov_codigo","mov.mov_data","duracao"=>"to_char(mov.mov_data + COALESCE(i.ite_duracao,null,0),'DD/MM/YYYY')"))
                ->join(array("i"=>"itens_movimento"),"i.mov_codigo=mov.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=mov.set_saida",array("set_nome"))
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=mov.usr_codigo","usr_nome")
                ->where("mov.mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo)
                                ->where("mov.mov_saida='D'")
                                ->where("mov.mov_data > CURRENT_DATE - $dias");
                                
                
                // CONSULTA DISPENSADO BACKUP
            $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento_bkp"),array("unidade"=>"COALESCE('1','1')","m.mov_codigo","m.mov_data","duracao"=>"to_char(m.mov_data + COALESCE(i.ite_duracao,null,0),'DD/MM/YYYY')"))
                ->join(array("i"=>"itens_movimento_bkp"),"i.mov_codigo=m.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto_bkp"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=m.set_saida",array("set_nome"))
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=m.usr_codigo","usr_nome")
                ->where("m.mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo)
                                ->where("m.mov_saida='D'")
                                ->where("m.mov_data > CURRENT_DATE - $dias");
                                
                
                // REALIZANDO UNION
                
                $where = $this->select(FALSE)
                              ->setIntegrityCheck(FALSE)
                              ->union(array($where1, $where2), Zend_Db_Select::SQL_UNION_ALL)
                              ->order(array("mov_data DESC"));
                               //die($where);
                return $this->fetchAll($where);
        }
       
        public function getDispensados($usu_codigo=FALSE){
            // produtos "inteiros"
            $where1 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("mov"=>"movimento"),array("unidade"=>"COALESCE('1','1')","mov_codigo","mov_data","duracao"=>"to_char(mov.mov_data + COALESCE(i.ite_duracao,null,0),'DD/MM/YYYY')"))
                ->join(array("i"=>"itens_movimento"),"i.mov_codigo=mov.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=mov.set_saida","set_nome")
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=mov.usr_codigo","usr_nome")
                ->where("mov.mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo)
                ->where("mov_saida='D'");
                
            $where2 = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento_bkp"),array("unidade"=>"COALESCE('1','1')","mov_codigo","mov_data","duracao"=>"to_char(m.mov_data + COALESCE(i.ite_duracao,null,0),'DD/MM/YYYY')"))
                ->join(array("i"=>"itens_movimento_bkp"),"i.mov_codigo=m.mov_codigo",array("ite_quantidade","ite_lote"))
                ->join(array("p"=>"produto_bkp"),"p.pro_codigo=i.pro_codigo","pro_nome")
                ->join(array("s"=>"setor"),"s.set_codigo=m.set_saida","set_nome")
                ->join(array("u"=>"unidade"),"u.uni_codigo=s.uni_codigo","uni_desc")
                ->join(array("usr"=>"usuarios"),"usr.usr_codigo=m.usr_codigo","usr_nome")
                ->where("m.mov_tipo='S'")
                ->where("usu_codigo=?",$usu_codigo)
                ->where("m.mov_saida='D'");
        /*    
            $where = $this->select(FALSE)->setIntegrityCheck(FALSE)->union(array($where1, $where2), Zend_Db_Select::SQL_UNION_ALL);*/

  $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->union(array($where1,$where2), Zend_Db_Select::SQL_UNION_ALL)
                ->order(array("mov_data DESC"));
            //  die($where);
            return $this->fetchAll($where);
        }
        
        public function getMovimentacoesPorSetor($set_codigo=FALSE){
            $where = $this->select(FALSE)
                ->setIntegrityCheck(FALSE)
                ->from(array("m"=>"movimento"));
            if ($set_codigo)
                $where->where("set_entrada = '$set_codigo' OR set_saida = '$set_codigo'");
            
        return $this->fetchAll($where);
        }
        
        public function excluiMovimentacaoPorSetor($setores=FALSE,$data=FALSE){
            // Se data vier preenchida faz consula por data
            if($data)
                $sqlData = "AND mov_data <= '$data'";
            
            try{
                $sql = $this
                    ->getDefaultAdapter()
                    ->query("DELETE FROM social.movimento WHERE (set_entrada IN ($setores) OR set_saida IN ($setores)) $sqlData")
                    ->fetchAll();
                return $sql;
            } catch (Exception $ex) {
                throw new Zend_Validate_Exception("Falha ao excluir movimentação: ".$ex->getMessage());
            }
            /*$item = $this->getMovimentacoesPorSetor($set_codigo);
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
        
        public function getMovimentosEntrada($set_codigo=FALSE,$data_inicial=FALSE,$data_final=FALSE,$pro_codigo=FALSE,$usr_codigo,$pros_codigo){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->distinct()
                          ->from(array("m"=>"movimento"),array("mov_data",
                                                               "mov_entrada" => "(CASE WHEN mov_entrada = 'T' THEN 'Transferência' WHEN mov_entrada='E-SI' THEN 'Entrada por Saldo de Implantação' WHEN mov_entrada='E-C' THEN 'Entrada por Concorrência' WHEN mov_entrada='E-DL' THEN 'Entrada por Dispensa de Licitação' WHEN mov_entrada = 'E-CONV' THEN 'Entrada por Convite' WHEN mov_entrada = 'E-D' THEN 'Entrada por doação' WHEN mov_entrada = 'E-P' THEN 'Entrada por Pregão' WHEN mov_entrada = 'E-AE' THEN 'Entrada por Ajuste de Estoque' WHEN mov_entrada = 'E-EVENTUAL' THEN 'Entrada por Entrada Eventual' WHEN mov_entrada = 'E-O' THEN 'Entrada Ordinária' WHEN mov_entrada = 'E-TP' THEN 'Entrada por Tomada de Preços' WHEN mov_entrada = 'E-INEX' THEN 'Entrada por Inexigibilidade' WHEN mov_entrada = 'E-PER' THEN 'Entrada por Permuta' WHEN mov_entrada = 'E' THEN 'Entrada por Nota Fiscal de Compra' WHEN mov_entrada = 'M' THEN 'Entrada por Emprestimo' WHEN mov_entrada= 'I' THEN 'Entrada por Inventário' WHEN mov_entrada = 'O' THEN 'Outras Entradas' WHEN mov_entrada = 'V' THEN 'Entrada por Devolução' END)","mov_codigo"))
                          ->join(array("f"=>"fornecedor"),"f.for_codigo=m.for_codigo","for_nome")
                          ->join(array("u" => "usuarios"),"u.usr_codigo=m.usr_codigo","usr_nome")
                          ->join(array("ite"=>"itens_movimento"),"ite.mov_codigo=m.mov_codigo","")
                          ->join(array("s"=>"setor"),"s.set_codigo=m.set_entrada")
                          ->join(array("p"=>"produto"),"p.pro_codigo=ite.pro_codigo","")
                          ->where("mov_tipo = 'E'")
                          ->order("set_codigo")
                          ->order("mov_data");
            
            if($data_inicial)
                $where->where("mov_data >= '$data_inicial'");
            
            if($data_final)
                $where->where("mov_data <= '$data_final'");
            
            if($set_codigo)
                $where->where("set_entrada = $set_codigo");
            
            if($usr_codigo)
                $where->where("usr_codigo = $usr_codigo");
            
            if($pro_codigo)
                $where->where("pro_codigo = $pro_codigo");
            
            if($pros_codigo)
                $where->where("pros_codigo=$pros_codigo");
            
            return $this->fetchAll($where);
            
        }


        public function getPacientesFaltosos($usu_codigo=FALSE, $data_limite=FALSE, $set_codigo=FALSE){
            //Pega ultimo movimento de dispensação atribuido ao usuário do argumento.
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("m"=>"movimento"), array("mov_data"))
                          ->join(array("ite"=>"itens_movimento"), "m.ite_codigo=ite.ite_codigo", "")
                          ->join(array("pp"=>"programa_produto"), "pp.pro_codigo=ite.pro_codigo", "")
                          ->where("mov_saida='D'")
                          ->where("usu_codigo=?", $usu_codigo)
                          ->order("ate.ate_codigo DESC")
                            ->limit(1);
            $last_mov_data = $this->fetchAll($where);
            $data1 = new DateTime( $last_mov_data->mov_data );
            $data2 = new DateTime( $data_limite );
            $intervalo = $data1->diff( $data2 );

            if($intervalo > 30){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("m"=>"movimento"), "")
                          ->joinLeft(array("usu"=>"usu_codigo"), "usu.usu_codigo=m.usu_codigo", array("usu.usu_nome"))
                          ->join(array("ite"=>"itens_movimento"), "m.ite_codigo=ite.ite_codigo", "")
                          ->join(array("pp"=>"programa_produto"), "pp.pro_codigo=ite.pro_codigo", "")
                          ->joinLeft(array("pa"=>"programa_atendimento"), "pa.prg_codigo=pp.prg_codigo", array("pa.prg_nome"))
                          ->joinLeft(array("set"=>"setor"),"set.set_codigo=set.set_saida", array("set.set_nome"))
                          ->where("mov_saida='D'")
                          ->where("ite.usu_codigo=?", $usu_codigo)
                          ->order("ate.ate_codigo DESC")
                        ->limit(1);
                          //->where("mov_data = '$data_limite'");
                          if($set_codigo)
                                $where->where("set_entrada = $set_codigo");

                    return $this->fetchAll($where);
                        
        } else {
            return FALSE;
        }
    }

    public function verificaDispensacaoMaisRecente($set_codigo=FALSE, $dataAnterior=FALSE, $usu_codigo=FALSE, $pro_codigo=FALSE, $dataFinal=FALSE){
        $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ite"=>"itens_movimento"), array("ite_codigo", "mov_codigo", "pro_codigo", "ite_duracao", "ite_lote"))
                          ->join(array("m"=>"movimento"), "m.mov_codigo=ite.mov_codigo", array("mov_data", "usu_codigo"))
                          ->join(array("pp"=>"programa_produto"), "pp.pro_codigo=ite.pro_codigo", "")
                          ->joinLeft(array("usu"=>"usuario"), "usu.usu_codigo=m.usu_codigo", array("usu.usu_nome"))
                          ->joinLeft(array("pa"=>"programa_atendimento"), "pa.prg_codigo=pp.prg_codigo", array("pa.prg_nome"))
                          ->joinLeft(array("set"=>"setor"),"set.set_codigo=m.set_saida", array("set.set_nome"))
                          ->joinLeft(array("pro"=>"produto"),"ite.pro_codigo=pro.pro_codigo", array("pro.pro_nome"))
                          ->where("m.mov_saida='D'")
                          ->where("ite.ite_duracao is not null")
                          ->where("m.usu_codigo=$usu_codigo")
                          ->where("ite.pro_codigo=$pro_codigo")
                          ->order("ite.ite_codigo DESC")
                          ->limit(1);
                          
                            if($set_codigo){
                                $where->where("m.set_saida=$set_codigo");
                            }
                            if($dataFinal){
                                $where->where("m.mov_data <= '$dataFinal'");
                            }
                            if($dataAnterior){
                                $where->where("m.mov_data > '$dataAnterior'");
                            }
                        // echo "<pre>";
                        //     die($where);
        return $this->fetchAll($where);

    }


    public function getItensMovimentoDePrograma($set_codigo=FALSE, $dataInicial=FALSE, $dataFinal=FALSE){
            $where = $this->select(FALSE)
                          ->setIntegrityCheck(FALSE)
                          ->from(array("ite"=>"itens_movimento"), array("ite_codigo", "mov_codigo", "pro_codigo", "ite_duracao", "ite_lote"))
                          ->join(array("m"=>"movimento"), "m.mov_codigo=ite.mov_codigo", array("mov_data", "usu_codigo"))
                          ->join(array("pp"=>"programa_produto"), "pp.pro_codigo=ite.pro_codigo", "")
                          ->joinLeft(array("usu"=>"usuario"), "usu.usu_codigo=m.usu_codigo", array("usu.usu_nome"))
                          ->joinLeft(array("pa"=>"programa_atendimento"), "pa.prg_codigo=pp.prg_codigo", array("pa.prg_nome"))
                          ->joinLeft(array("set"=>"setor"),"set.set_codigo=m.set_saida", array("set.set_nome"))
                          ->joinLeft(array("pro"=>"produto"),"ite.pro_codigo=pro.pro_codigo", array("pro.pro_nome"))
                          ->where("m.mov_saida='D'")
                          ->where("ite.ite_duracao is not null");
                        if($set_codigo)
                            $where->where("m.set_saida=$set_codigo");
                        if($dataInicial)
                            $where->where("m.mov_data >= '$dataInicial'");
                        if($dataFinal)
                            $where->where("m.mov_data <= '$dataFinal'");
                        //die($where);
                        // echo "<pre>";
                        // die($where);
            return $this->fetchAll($where);
    }

    public function corrigeMovimento(){
        $sql = $this->select(FALSE)
                    ->setIntegrityCheck(FALSE)
                    ->from(array("sal"=>"saldo"), array("sal_codigo", "sal_qtde", "pro_codigo", "sal_lote", "sal_validade", "set_codigo"));
        $saldos = $this->fetchAll($sql);
        //die(var_dump($saldos));

        foreach($saldos as $saldo){
                $sql_entrada = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ite"=>"itens_movimento"), array("total"=>"SUM(ite_quantidade)"))
                        ->join(array("mov"=>"movimento"), "mov.mov_codigo=ite.mov_codigo", "")
                        ->where("ite.pro_codigo=?", $saldo->pro_codigo)                
                        ->where("ite.ite_lote=?", $saldo->sal_lote)
                        ->where("ite.ite_validade=?", $saldo->sal_validade)
                        ->where("mov.set_entrada=?", $saldo->set_codigo)
                        ->where("mov.mov_tipo in ('E', 'T')");
                $entrada = $this->fetchAll($sql_entrada);

                $sql_saida = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ite"=>"itens_movimento"), array("total"=>"SUM(ite_quantidade)"))
                        ->join(array("mov"=>"movimento"), "mov.mov_codigo=ite.mov_codigo", "")
                        ->where("ite.pro_codigo=?", $saldo->pro_codigo)
                        ->where("ite.ite_lote=?", $saldo->sal_lote)
                        ->where("ite.ite_validade=?", $saldo->sal_validade)
                        ->where("mov.set_saida=?", $saldo->set_codigo)
                        ->where("mov.mov_tipo in ('S', 'T')");
                $saida = $this->fetchAll($sql_saida);

                $diff = $entrada[0]->total - $saida[0]->total;

                /*Se diferenca for maior q saldo quantidade necessário fazer uma saída para corrigir*/
                if($diff > $saldo->sal_qtde){
                    $nova_saida = $diff - $saldo->sal_qtde;
                    $data = array(
                        "mov_tipo" => "S",
                        "mov_saida"=>"D",
                        "set_saida"=>$saldo->set_codigo
                    );
                    $mov_codigo = $this->salvar($data);
                    $dados_itens = array("mov_codigo"=>$mov_codigo,
                                         "pro_codigo"=>$saldo->pro_codigo,
                                         "ite_lote"=>$saldo->sal_lote,
                                         "ite_quantidade"=>$nova_saida,
                                         "ite_validade"=>$saldo->sal_validade
                                     );

                    $tbIte = new Application_Model_ItensMovimento();
                    $tbIte->salvar($dados_itens);
                } 
                /*Se diferenca for menor q saldo quantidade necessário fazer um entrada para corrigir*/
                else if ($diff < $saldo->sal_qtde) {
                    //die("entrada");
                    $nova_entrada = $saldo->sal_qtde - $diff;
                    //die(var_dump($nova_entrada));
                    $data = array(
                        "mov_tipo" => "E",
                        "mov_entrada"=>"T",
                        "set_entrada"=>$saldo->set_codigo
                    );
                    $mov_codigo = $this->salvar($data);
                    $dados_itens = array("mov_codigo"=>$mov_codigo,
                                         "pro_codigo"=>$saldo->pro_codigo,
                                         "ite_lote"=>$saldo->sal_lote,
                                         "ite_quantidade"=>$nova_entrada,
                                         "ite_validade"=>$saldo->sal_validade
                                     );

                    $tbIte = new Application_Model_ItensMovimento();
                    $tbIte->salvar($dados_itens);
                }
        }

        return true;

    }



 
    public function getItemPsicoDispensados($set_codigo=FALSE, $dataInicial=FALSE,$dataFinal=FALSE){
        $where = $this->select(FALSE)
          ->setIntegrityCheck(FALSE)
          ->from(array("ite"=>"itens_movimento"), array("ite_quantidade"))
          ->join(array("m"=>"movimento"), "m.mov_codigo=ite.mov_codigo", array("mov_data", "mov_saida","mov_entrada","mov_data","mov_tipo"))
          ->joinLeft(array("usu"=>"usuario"), "usu.usu_codigo=m.usu_codigo", array("usu.usu_nome"))
          ->joinLeft(array("set"=>"setor"),"set.set_codigo=m.set_saida", array("set.set_nome"))
          ->joinLeft(array("pro"=>"produto"),"ite.pro_codigo=pro.pro_codigo", array("pro_codigo_dcb","pro.pro_nome","(select sum(sal_qtde) from saldo where pro_codigo = pro.pro_codigo) as totalsaldo"))

          ->joinLeft(array("medin"=>"usuarios"),"medin.usr_codigo=m.med_codigo_interno",array("usr_nome"))
          ->joinLeft(array("medex"=>"medico"),"medex.med_codigo=m.med_codigo_externo",array("med_nome"))
          ->joinLeft(array("fo"=>"fornecedor"),"fo.for_codigo=m.for_codigo",array("for_nome"))
            ->where("pro.psico_codigo is not null")  
            ->where("psico_codigo != 2")                                             
          ->order("pro.pro_nome")
         // ->order("s_total")
          ->order("mov_data asc");
          
          /*  if($set_codigo){
                $where->where("m.set_saida=$set_codigo");
            }*/
            if($dataFinal){
                $where->where("m.mov_data <= '$dataFinal'");
            }
            if($dataInicial){
                $where->where("m.mov_data > '$dataInicial'");
            }
        // echo "<pre>";
            // die($where);
        return $this->fetchAll($where);

    }


    function getDadosRelarioEntradasSaidas($set_codigo=FALSE, $dataInicial=FALSE,$dataFinal=FALSE) {
                if($set_codigo){
                    $set_e = "and m.set_entrada=$set_codigo";
                    $set_s = "and m.set_saida=$set_codigo";
                }
                if($dataFinal){
                    $dtini = " and m.mov_data <= '$dataFinal'";
                    $dtinis = " and sal_data <= '$dataFinal'";
                }
                if($dataInicial){
                    $dtfim = "and m.mov_data > '$dataInicial'";
                    $dtfims = " and sal_data > '$dataInicial'";
                }
 $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("mov"=>"movimento"), "")
                        ->join(array("ite"=>"itens_movimento"),"mov.mov_codigo=ite.mov_codigo","")
                        ->join(array("prod"=>"produto"),"prod.pro_codigo=ite.pro_codigo",array("pro_nome","(select sum(ite_quantidade) from movimento as m join itens_movimento as ite on ite.mov_codigo = m.mov_codigo where mov_tipo = 'E' and pro_codigo = prod.pro_codigo $dtini $dtfim $set_e) as entrada","(select ite_vlrunit from movimento as m join itens_movimento as ite on ite.mov_codigo = m.mov_codigo where pro_codigo = prod.pro_codigo and ite_vlrunit>0 group by ite_vlrunit order by ite_vlrunit desc limit 1) as valor_produto","(select sum(ite_quantidade) from movimento as m join itens_movimento as ite on ite.mov_codigo = m.mov_codigo where mov_tipo = 'S' and mov_saida='S' and pro_codigo = prod.pro_codigo  $dtini $dtfim $set_s) as saida","(select sum(ite_quantidade) from movimento as m join itens_movimento as ite on ite.mov_codigo = m.mov_codigo where mov_tipo = 'S' and mov_saida='D' and pro_codigo = prod.pro_codigo  $dtini $dtfim $set_s) as dispensada","(select sum(sal_qtde) from saldo where pro_codigo = prod.pro_codigo) as saldo"))
                          ->order("pro_nome");

   $sql->group(array("prod.pro_nome","entrada","valor_produto","saida","dispensada","saldo"));                        
                if($set_codigo){
                    $sql->where("mov.set_saida=$set_codigo");
                }
                if($dataFinal){
                    $sql->where("mov.mov_data <= '$dataFinal'");
                }
                if($dataInicial){
                    $sql->where("mov.mov_data > '$dataInicial'");
                }

                    //    die($sql);  
         return  $this->fetchAll($sql);
    }


    function getFrmMinfromMov($pro_codigo=NULL,$sal_lote=NULL,$sal_validade=NULL){
            $sql = $this->select(FALSE)
                        ->setIntegrityCheck(FALSE)
                        ->from(array("ite"=>"itens_movimento"), array("pro_frmmin"))
                        ->where("ite.pro_codigo=?", $pro_codigo) 
                        ->where("ite.ite_lote=?", $sal_lote) 
                        ->where("ite.ite_validade=?", $sal_validade);

                       // die($sql); 
    return  $this->fetchAll($sql);
    }

}
