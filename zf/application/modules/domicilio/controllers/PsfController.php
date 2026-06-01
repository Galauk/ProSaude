<?php
class Domicilio_PsfController extends Zend_Controller_Action {
    
    public function init() {
    }
    
    public function indexAction(){
      // die("oi");
        $dom_codigo = $this->_getParam("dom_codigo",FALSE);
        $this->view->dom_codigo = $dom_codigo;
        $this->view->title = "Cadastro de Endereço";
        $tpe = new Application_Model_TbPergunta();
        $tped = new Application_Model_TbPerguntaDetalhe();
        $tbDom = new Application_Model_Domicilio();
        $tbUsr = new Application_Model_Usuarios();
        // Pega os dados do usuário logado
        $this->view->dadosUser = $tbUsr->getUsrAtual();
        $this->view->dados_header = $tbDom->getHeaderCadDomiciliar($this->view->dom_codigo);
        //die($this->view->dom_codigo . 'aa' . $this->view->cod_equipe);
       // echo "<pre>".print_r($this->view->dados_header,1);die();
        $psf_dados = $tbDom->getDomicilioPsf($this->view->dom_codigo)->toArray();
        if(count($psf_dados)){
            $this->view->dados = $this->montaArrayEditAction($psf_dados);
        }
        $this->view->qtdMorador = $tbDom->getQtdMoradores($dom_codigo)->qtdMorador;
        $perguntas = $tpe->getPerguntasPorContexto(2)->toArray();
        $psf = array();
        foreach($perguntas as $pergunta){
            if(empty($pergunta[co_pergunta_pai])){
                $psf[$pergunta[co_seq_pergunta]] = array("ds_pergunta"=>$pergunta[ds_pergunta],
                                                         "tp_pergunta"=>$pergunta[tp_pergunta],
                                                         "detalhes"=>$tped->getPerguntaDetalhe($pergunta[co_seq_pergunta])->toArray());
            }else{
                $psf[$pergunta[co_pergunta_pai]]["pergunta_filho"][$pergunta[co_seq_pergunta]] = array("ds_pergunta"=>$pergunta[ds_pergunta],
                                                         "tp_pergunta"=>$pergunta[tp_pergunta],
                                                         "detalhes"=>$tped->getPerguntaDetalhe($pergunta[co_seq_pergunta])->toArray());
            }
            
        }
       // echo "<pre>" . print_r($psf, 1);
        //die();
        $this->view->perguntas = $psf;
    }
    
    private function montaArrayEditAction($psf_dados){
        $array_edit = array();
        //echo "<pre>".print_r($psf_dados,1);die();
        foreach($psf_dados as $psf){
            $i++;
           
            if($psf[tp_pergunta] != "3"){
                // echo $psf[co_pergunta_detalhe]."<br/>";
                $array_edit[$psf[co_pergunta]] = array("co_pergunta"=>$psf[co_pergunta],
                                                       "co_cds_cad_domiciliar"=>$psf[co_cds_cad_domiciliar],
                                                       "co_pergunta_detalhe"=>$psf[co_pergunta_detalhe],
                                                       "ds_resposta"=>$psf[ds_resposta],
                                                       "co_resposta"=>$psf[co_resposta],
                                                       "co_seq_cds_domicilio_resposta"=>$psf[co_seq_cds_domicilio_resposta]);
            }else{
                $array_multiplo[$psf[co_pergunta_detalhe]] = array("co_pergunta"=>$psf[co_pergunta],
                                                                    "co_cds_cad_domiciliar"=>$psf[co_cds_cad_domiciliar],
                                                                    "co_pergunta_detalhe"=>$psf[co_pergunta_detalhe],
                                                                    "ds_resposta"=>$psf[ds_resposta],
                                                                    "co_resposta"=>$psf[co_resposta],
                                                                    "co_seq_cds_domicilio_resposta"=>$psf[co_seq_cds_domicilio_resposta]);
               $array_edit[$psf[co_pergunta]] = $array_multiplo;
            }
        }
        //echo "<pre>".print_r($array_edit,1);die();
        return $array_edit;
    }

    public function salvarAction(){
        $tbCdr = new Application_Model_TbCdsDomicilioResposta();
        $tbDom = new Application_Model_Domicilio();
        $arr = $this->_request->getPost();
        $array_dom = array("dom_codigo"=>$arr["dom_codigo"],
                           "usr_codigo"=>$arr["prof_resp_codigo"],
                           "uni_codigo"=>$arr["cod_cnes_uni"],
                           "cod_equipe"=>$arr["cod_equipe"]);
        //echo "<pre>".print_r($array_dom,1);die();
        $tbDom->salvar($array_dom);
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        unset($arr["valid_57"],
              $arr["valid_58"],
              $arr["dom_codigo"],
              $arr["valid_66"],
              $arr["prof_resp_codigo"],
              $arr["cod_cnes_uni"],
              $arr["cod_equipe"],
              $arr["prof_resp"],
              $arr["cod_cnes_edit"],
              $arr["cod_equipe_ine"]);
        try{
            //echo "<pre>".print_r($arr,1);die();
            $data = array();
            $tbCdr->deletaTodosPorDomicilio($this->_request->getPost("dom_codigo"));
            foreach ($arr as $ind=>$val){
                if($ind == 71){
                    //para salvar os checkbox da questão 71
                    foreach($val as $checkbox){
                        $data = array("co_pergunta"=>$ind,
                                  "co_pergunta_detalhe" => $checkbox,
                                  "co_cds_cad_domiciliar" => $this->_request->getPost("dom_codigo")); 
                        $tbCdr->salvar($data);
                    }
                }else{
                    $data = array("co_pergunta"=>$ind,
                                  "co_pergunta_detalhe" => ($ind != 60 && $ind != 61 && $ind != 63 && $ind != 72 &&  $ind != 70 ? "$val" : ""),
                                  "ds_resposta" => ($ind == 60 || $ind == 61 || $ind == 63 || $ind == 72 ? "$val" : ""),
                                  "co_cds_cad_domiciliar" => $this->_request->getPost("dom_codigo"),
                                  "co_resposta" => ($ind == 63 ||  $ind == 70 ? "$val" : "")); // os ifs são p/ identificar as questoes do tipo texto, pois salvam em outra coluna

                    $tbCdr->salvar($data);

                }
            }
            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->roolBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }
        return $this->_redirect("../domicilio.php");
    }
    
    public function buscarIneAction(){
        $tbEquipe = new Application_Model_TbEquipe();       
        $this->view->dados = $tbEquipe->buscar($this->_request->getParam("term"));
        return $this->render("dados",NULL,TRUE);
    }
}
?>
