<?php

class Farmacia_FarmaciaController extends Zend_Controller_Action {
	
	public function init() {
		$this->_helper->acl->allow(NULL);
		$this->view->title = "Dispensação";
	}
	
    public function indexAction() {
        $tbUsr = new Application_Model_Usuarios();
        $tbConf = new Application_Model_Configuracao();
        $tbSet = new Application_Model_Setor();
        $this->view->set_codigo = $tbUsr->getUsrAtual()->set_codigo;
        if ($this->view->set_codigo == null) {
            $this->_redirect("default/farmacia/restrito");
        }
        if($tbSet->verificaFuncaoSetor($this->view->set_codigo)->set_farmacia != "S"){
            return $this->render("erro",null,true);
        }
        $this->view->lote_automatico = $tbConf->getConfig("LOTE_AUTOMATICO");
        $this->view->via_medicamentos = $tbConf->getConfig("VIA_FARMACIA");
        $this->view->cadastro_aise = $tbConf->getConfig("CADASTRO_AISE");
        $this->view->validade_medicamentos = $tbConf->getConfig("VALIDADE_DOS_MEDICAMENTOS");
        $cod_barras = $this->_getParam("cod_barras",FALSE);
        $this->view->cod_barras = $cod_barras;
    }

    public function listaDeAutorizacaoAction() {
        $tbUsr = new Application_Model_Usuarios();                  
        $tbConf = new Application_Model_Configuracao();
        $tbSet = new Application_Model_Setor();
        $tbCiap = new Application_Model_TbCiap();
        $this->view->ciap = $tbCiap->getCiaps($ate_dados);
        $this->view->set_codigo = $tbUsr->getUsrAtual()->set_codigo;
        if ($this->view->set_codigo == null) {
           $this->_redirect("default/farmacia/restrito");
        }
        if($tbSet->verificaFuncaoSetor($this->view->set_codigo)->set_farmacia != "S"){
            return $this->render("erro",null,true);
        }
    }

    public function listaDeAutorizacoesAction() {
        $tbUsr = new Application_Model_Usuarios();
        $tbConf = new Application_Model_Configuracao();
        $tbSet = new Application_Model_Setor();
        $tbCiap = new Application_Model_TbCiap();
        $this->view->ciap = $tbCiap->getCiaps($ate_dados);
        $this->view->set_codigo = $tbUsr->getUsrAtual()->set_codigo;
        if ($this->view->set_codigo == null) {
           $this->_redirect("default/farmacia/restrito");
        }
        // die('TESTE TESTE TESTE TESTE TESTE TESTE TESTE TESTE ');
        // salvarLaudoSolicitacao();
    }
    
    function salvarLaudoSolicitacao() {
        $tbLaudoFarmacia = new Application_Model_Farmacia();
    }

    public function erroAction() {
        $this->view->title = "Acesso restrito";
    }
    
    // public function salvarAction() {
    //     $usu_codigo = $this->_getParam("usu_codigo",FALSE);
    //     $set_codigo = $this->_getParam("set_codigo",FALSE);
    //     $usr_codigo = $this->_getParam("usr_codigo",FALSE);
    //     $data_movimento = boolval($this->_getParam("mov_data",FALSE));
    //     // echo "<pre>";var_dump($data_movimento);die();
    //     $interno = $this->_getParam("interno",FALSE);
    //     $itens = $this->_getParam("itens",FALSE);
    //     $tbMov = new Application_Model_Movimento();
    //     $tbIte = new Application_Model_ItensMovimento();
    //     Zend_Db_Table::getDefaultAdapter()->beginTransaction();
    //     $dados_mov = array(
    //         "usu_codigo"=>$usu_codigo,
    //         "set_saida"=>$set_codigo,
    //         ($interno == 0 ? "med_codigo_externo" : "med_codigo_interno") => $usr_codigo,
    //         "mov_data"=>($data_movimento == "false" ? NOW() : $data_movimento),
    //         "mov_tipo"=>"S",
    //         "mov_data_inclusao"=>"NOW()",
    //         "mov_saida"=>"D"
    //     );
        
    //     // die("aqui");
        
    //     // echo "<pre>";var_dump($dados_mov);die();


    //     try{
    //         $mov_codigo = $tbMov->salvar($dados_mov);

    //         foreach($itens as $item){
    //             $params = array("mov_data"=>  date("d/m/Y"),"usu_codigo"=>$usu_codigo,"pro_codigo"=>$item[pro_codigo],"ite_lote"=>$item[ite_lote]);
    //             if($tbIte->verificaSeJaDispensou($params)){
    //                 $array_valores = $tbIte->getValorPorProdutoLote($item[pro_codigo], $item[ite_lote]);
    //                 $custo_medio = $this->getCustoMedio($array_valores);
    //                 $dados_itens = array("mov_codigo"=>$mov_codigo,
    //                                      "pro_codigo"=>$item[pro_codigo],
    //                                      "ite_lote"=>"$item[ite_lote]",
    //                                      "ite_quantidade"=>"$item[ite_quantidade]",
    //                                      "ite_validade"=>"$item[ite_validade]",
    //                                      "ite_duracao"=>($item[ite_duracao] == "undefined" || $item[ite_duracao] == "" ? NULL : $item[ite_duracao]),
    //                                      "ite_custo_medio" => ($custo_medio != 0 || $custo_medio != null ? $custo_medio : "0"),
    //                                      "ite_cod_receita" => ($item[ite_cod_receita] == "" || $item[ite_cod_receita] == NULL ? "0" :$item[ite_cod_receita] ));

    //                 $ite_codigo = $tbIte->salvar($dados_itens);
    //                 $this->view->dados = array("msg"=>"Dados cadastrados com sucesso","id"=>$mov_codigo);
    //             }
    //         }
    //         $rec_codigo = $this->_getParam("rec_codigo",FALSE);
    //         if($rec_codigo){
    //             $dados_rec = array("rec_codigo"=>$rec_codigo,
    //                                "rec_finalizada"=>"S");
    //             $tbRec = new Application_Model_Receita();
    //             $tbRec->alteraStatus($dados_rec);
    //         }
    //         Zend_Db_Table::getDefaultAdapter()->commit();
    //     } catch (Exception $exc){
    //         //die($exc->getMessage());
    //         $this->view->dados = $exc->getMessage();
    //         Zend_Db_Table::getDefaultAdapter()->rollBack();
    //         $this->view->dados = $exc->getMessage();
    //     }
        
    //     return $this->render("dados",NULL,TRUE);

    // }
    
    public function salvarAction(){
        $usu_codigo = $this->_getParam("usu_codigo",FALSE);
        $set_codigo = $this->_getParam("set_codigo",FALSE);
        $usr_codigo = $this->_getParam("usr_codigo",FALSE);
        $interno = $this->_getParam("interno",FALSE);
        $itens = $this->_getParam("itens",FALSE);
        $tbMov = new Application_Model_Movimento();
        $tbIte = new Application_Model_ItensMovimento();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        $dados_mov = array("usu_codigo"=>$usu_codigo,
                           "set_saida"=>$set_codigo,
                           ($interno == 0 ? "med_codigo_externo" : "med_codigo_interno") => $usr_codigo,
                            "mov_data"=>"NOW()",
                            "mov_tipo"=>"S",
                            "mov_data_inclusao"=>"NOW()",
                            "mov_saida"=>"D");
        
        try{
            $mov_codigo = $tbMov->salvar($dados_mov);

            foreach($itens as $item){
                $params = array("mov_data"=>  date("d/m/Y"),"usu_codigo"=>$usu_codigo,"pro_codigo"=>$item[pro_codigo],"ite_lote"=>$item[ite_lote]);
                if($tbIte->verificaSeJaDispensou($params)){
                    $array_valores = $tbIte->getValorPorProdutoLote($item[pro_codigo], $item[ite_lote]);
                    $custo_medio = $this->getCustoMedio($array_valores);
                    $dados_itens = array("mov_codigo"=>$mov_codigo,
                                         "pro_codigo"=>$item[pro_codigo],
                                         "ite_lote"=>"$item[ite_lote]",
                                         "ite_quantidade"=>"$item[ite_quantidade]",
                                         "ite_validade"=>"$item[ite_validade]",
                                         "ite_duracao"=>($item[ite_duracao] == "undefined" || $item[ite_duracao] == "" ? NULL : $item[ite_duracao]),
                                         "ite_custo_medio" => ($custo_medio != 0 || $custo_medio != null ? $custo_medio : "0"),
                                         "ite_cod_receita" => ($item[ite_cod_receita] == "" || $item[ite_cod_receita] == NULL ? "0" :$item[ite_cod_receita] ));

                    $ite_codigo = $tbIte->salvar($dados_itens);
                    $this->view->dados = array("msg"=>"Dados cadastrados com sucesso","id"=>$mov_codigo);
                }
            }
            $rec_codigo = $this->_getParam("rec_codigo",FALSE);
            if($rec_codigo){
                $dados_rec = array("rec_codigo"=>$rec_codigo,
                                   "rec_finalizada"=>"S");
                $tbRec = new Application_Model_Receita();
                $tbRec->alteraStatus($dados_rec);
            }
            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $exc){
            //die($exc->getMessage());
            $this->view->dados = $exc->getMessage();
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
        }
        
        return $this->render("dados",NULL,TRUE);

    }

    
    public function getCustoMedio($array_valores) {
        $valor_total_entrada = 0;
        $qtde_total_entrada = 0;
        $valor_total_saida = 0;
        $qtde_total_saida = 0;
        foreach($array_valores as $valor_geral){
            foreach($valor_geral as $valor_entrada){
                $valor_total_entrada += $valor_entrada[t_entrada];
                $qtde_total_entrada += $valor_entrada[qtde_entrada];
                $valor_total_saida += $valor_entrada[t_saida];
                $qtde_total_saida += $valor_entrada[qtde_saida];
            }
        }
        //echo $valor_total_entrada."-".$valor_total_saida. "/".$qtde_total_entrada. "-". $qtde_total_saida;
        
        $valor =  number_format($valor_total_entrada,16) - number_format($valor_total_saida,16);
        $qtde = $qtde_total_entrada - $qtde_total_saida;
        $custo_medio = $valor / $qtde;
        //die($valor. "/ ".$qtde);
        return number_format($custo_medio,16);
    }
    
    public function bcdiv_cust( $first, $second, $scale = 0) {
        $res = $first / $second;
        return round( $res, $scale );
    }

    public function imprimirViaAction() {
        Zend_Layout::getMvcInstance()->setLayout("simples");
        $this->view->title = "Imprimir Via de Medicamentos";
        $tbMov = new Application_Model_Movimento();
        $tbIte = new Application_Model_ItensMovimento();
        $tbSec = new Application_Model_Secretaria();
        $tbUsr = new Application_Model_Usuarios();
        $this->view->set_nome = $tbUsr->getUsrAtual()->set_nome;

        //$tbConf = new Application_Model_Configuracao();
        $this->view->usr = $tbUsr->getUsrAtual()->usr_nome;
        $mov_codigo = $this->_getParam("mov_codigo",FALSE);
        $this->view->movimento = $tbMov->getMovimento($mov_codigo);
        $itens = $tbIte->getProdutosPorMovimento($mov_codigo);
        //$this->view->valor_guia_medicamentos = $tbConf->getConfig("VALOR_VIA_MEDICAMENTOS");
        //$array_itens = $this->getValorPorProduto($itens);
        $this->view->itens_mov = $itens;
    }
    
    /*COMENTADO ATÉ ESTUDAR MELHOR A SITUAÇÃO QUE MOSTRA O VALOR*/
    /*public function getValorPorProduto($array_itens) {
        $array_itens = $array_itens->toArray();
        $tbIte = new Application_Model_ItensMovimento();
        foreach($array_itens as $item){
            $valores = $tbIte->getValorPorProdutoLote($item[], $ite_lote);
        }
        
    }*/
    
    public function getHistoricoPacienteAction() {
       $usu_codigo = $this->_getParam("usu_codigo",FALSE); 
       $tbMovimento = new Application_Model_Movimento();
       $this->view->dados = $tbMovimento->getDispensados($usu_codigo)->toArray();
       
       return $this->render("dados",NULL,TRUE);
    }
    
    public function getUltimosDispensadosAction() {
    
        $usu_codigo = $this->_getParam("usu_codigo",FALSE);
        $tbMov = new Application_Model_Movimento();
        $this->view->dados = $tbMov->getUltimosDispensados($usu_codigo)->toArray();
        return $this->render("dados",NULL,TRUE);
    }
    
    public function getReceitaAction() {
        $valor_cod_barras = substr($this->_getParam("rec_codigo",FALSE), 0, 4);
        
        if($valor_cod_barras == "0000" && $valor_cod_barras != ""){
            $rec_codigo = substr($this->_getParam("rec_codigo",FALSE), 0, -1);
        }else{
            $rec_codigo = $this->_getParam("rec_codigo",FALSE);
        }

        $tbRec = new Application_Model_Receita();
        $rec = $tbRec->getReceitaPorCodigo($rec_codigo);
        if($rec->rec_codigo){
            $rec->toArray();
            $tbIrec = new Application_Model_ReceitaItens();
            $irec = $tbIrec->getItensReceita($rec_codigo)->toArray();

            $dados = array("usu_codigo"=>$rec[usu_codigo],
                           "usu_nome"=>$rec[usu_nome],
                           "usr_codigo"=>$rec[usr_codigo],
                           "usr_nome"=>$rec[usr_nome],
                           "itens"=>$irec);

            $this->view->dados = $dados;
        }else{
            $this->view->dados = "";
        }
            
        return $this->render("dados",NULL,TRUE);
    }  
}