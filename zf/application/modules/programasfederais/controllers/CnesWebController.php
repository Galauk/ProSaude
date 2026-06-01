<?php

class ProgramasFederais_CnesWebController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Importação de dados CNES";
    }
    
    public function indexAction() {
        $this->view->title = "Importar CNES WEB";
        $tbUnidade = new Application_Model_Unidade();
        $tbUsuarios = new Application_Model_Usuarios();
        
        $usrInvalidos = $tbUsuarios->UsuariosSemCpf();
        $uniInvalidas = $tbUnidade->unidadeSemCnes();
        $this->view->usuarios_invalidos = $usrInvalidos;
        $this->view->unidades_invalidas = $uniInvalidas;
        if(count($usrInvalidos) > 0){
            $this->render("atualiza-usuarios-invalidos");
            return false;
        }
        
        if(count($uniInvalidas) > 0){
            $this->render("atualiza-unidades-invalidas");
        }
    }
    
    public function atualizaUsuariosInvalidosAction(){
        $this->view->title = "Atualiza Usuários";
    }

    public function lerXmlAction() {
        // O nome original do arquivo no computador do usuário
        $arqName = $_FILES['arquivo']['name'];
        // O tipo mime do arquivo. Um exemplo pode ser "image/gif"
        $arqType = $_FILES['arquivo']['type'];
        // O tamanho, em bytes, do arquivo
        $arqSize = $_FILES['arquivo']['size'];
        // O nome temporário do arquivo, como foi guardado no servidor
        $arqTemp = $_FILES['arquivo']['tmp_name'];
        // O código de erro associado a este upload de arquivo
        $arqError = $_FILES['arquivo']['error'];
        if ($arqError == 0) {
            $pasta = $_SESSION["root"]."WebSocialSaude/zf/public/uploads/cnes/";
            $upload = move_uploaded_file($arqTemp, $pasta . $arqName);
        }
        // Transforma o XML em objeto
        $xml = simplexml_load_file($pasta.$arqName);
        // Lendo os Nos de Estabelecimentos
        foreach ($xml->ESTABELECIMENTOS as $dados) {
            // Iniciando controle de transação

            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            try{
                // Desativando todos usuário, para ficar somente os do CNES ativo
                $dadosAtu = array("usr_ativo"=>"N","cnes_ativo"=>"N");
                $tbUsr = new Application_Model_Usuarios();
                $tbUsr->inativaUsuarios($dadosAtu);
                // Lendo por estabelecimento
                for ($i = 0; $i < count($dados->children()); $i++) {
                    $dadosEst = (array)$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i];
                    // Salvando dados do estabelecimento
                    $dadosCodsEst = $this->salvaDadosEstabelecimentosAction($dadosEst);
                    // Salvando dados do profissional e suas especialidades
                    $this->salvaDadosProfissionaisAction($dadosEst,$dadosCodsEst);
                }
                // Realizando a inserção dos de dados, se não deu nenhum problema
                Zend_Db_Table::getDefaultAdapter()->commit();
            } catch (Exception $exc) {
                Zend_Db_Table::getDefaultAdapter()->rollBack();
                $this->view->dados = $exc->getMessage();
                return $this->render("dados",NULL,TRUE);
            }
        }
        $this->view->dialog = array("Confirmação","Importação de dados do CNES realizada com sucesso!",300,140);
        return $this->render("cnes/index",NULL,TRUE);
    }
    
    public function uploadArquivoCnesAction($files){
        // O nome original do arquivo no computador do usuário
        $arqName = $files['arquivo']['name'];
        // O tipo mime do arquivo. Um exemplo pode ser "image/gif"
        $arqType = $files['arquivo']['type'];
        // O tamanho, em bytes, do arquivo
        $arqSize = $files['arquivo']['size'];
        // O nome temporário do arquivo, como foi guardado no servidor
        $arqTemp = $files['arquivo']['tmp_name'];
        // O código de erro associado a este upload de arquivo
        $arqError = $files['arquivo']['error'];
        $this->_helper->viewRenderer->setNoRender(true);
        if ($arqError == 0) {
            $pasta = $_SESSION["root"]."WebSocialSaude/zf/public/uploads/cnes/";
            $upload = move_uploaded_file($arqTemp, $pasta . $arqName);
        }
        // Transforma o XML em objeto
        $xml = simplexml_load_file($pasta.$arqName);
        return $xml;
    }
    
    public function importaAction(){
        $xml = $this->uploadArquivoCnesAction($_FILES);
        if($xml->IDENTIFICACAO[ORIGEM] != "PORTAL"){
            die("Arquivo Inválido");
        }
        
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
            $this->importaEstabelecimentos($xml->IDENTIFICACAO->ESTABELECIMENTOS,1);
            Zend_Db_Table::getDefaultAdapter()->commit();
        }  catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            die($exc->getMessage());
        }
        
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
           $this->importaProfissionais($xml->IDENTIFICACAO->PROFISSIONAIS,1);
            Zend_Db_Table::getDefaultAdapter()->commit();
            $this->view->dialog = array("Confirmação", "Cnes importado com sucesso!", 300, 140);
        }  catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dialog = array("Confirmação", "Falha ao importar CNES! <br/>".$exc->getMessage(), 300, 140);
        }
        
        
        return $this->render("index");
    }
    
    public function importaEstabelecimentos($estabelecimentos){
       // echo "<pre>".print_r($estabelecimentos,1);
       $tbUni = new Application_Model_Unidade();
       $tbComp = new Application_Model_TbComplexidade();
       $tbUnc = new Application_Model_UnidadeComplexidade();
       $tbEqp = new Application_Model_TbEquipe();
       $tbUni->atualizaStatusGeral();
       $tbUnc->atualizaStatusGeral();
       $tbEqp->atualizaStatusGeral();
       foreach ($estabelecimentos as $dados) {
            // Lendo por estabelecimento
            for ($i = 0; $i < count($dados->children()); $i++) {
               //echo $dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[LOGRADOURO]."<br/>";
               $array_unidade = array("uni_desc" => $dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["NM_FANTA"],
                                      "uni_endereco" =>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[LOGRADOURO],
                                      "uni_cep" =>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[CO_CEP],
                                      "cnes_sigestgest"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[SG_UF],
                                      "uni_codigo_ibge"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[CO_IBGE_MUN],
                                      "uni_bairro" => $dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[BAIRRO],
                                      "uni_numero"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[NUMERO],
                                      "cnes_complement"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->ENDERECO->DADOS_ENDERECO[COMPLEMENT],
                                      "uni_cnpj"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["CNPJ"],
                                      "uni_cnes"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["CNES"],
                                      "cnes_cod_esfadm"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["CO_ESF_ADM"],
                                      "cnes_tp_unid_id"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["TP_UNID_ID"],
                                      "cnes_telefone"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["TELEFONE1"],
                                      "cnes_fax"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["FAX"],
                                      "cnes_e_mail"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["E_MAIL"],);
               //for que percorre as complexidades
               
               for($j = 0; $j < count($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->COMPLEXIDADE->DADOS_COMPLEXIDADE);$j++){
                 $co_complexidade = $tbComp->getComplexidadePorSigla($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->COMPLEXIDADE->DADOS_COMPLEXIDADE[$j][SG_COMPLEXIDADE])->co_complexidade;
                 $array_complexidade[$j] = array("co_complexidade"=>$co_complexidade);
               }
               
               $array_unidade[complexidade] = $array_complexidade;
               
               for($k = 0; $k < count($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->EQUIPES->DADOS_EQUIPES);$k++){
                 $array_equipe[$k] = array("tp_equipe"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->EQUIPES->DADOS_EQUIPES[$k][TP_EQUIPE],
                                            "sg_equipe" =>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->EQUIPES->DADOS_EQUIPES[$k][TP_EQUIPE],
                                            "no_equipe"=>$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->EQUIPES->DADOS_EQUIPES[$k][DS_AREA],
                                            "nu_ine" => $dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->EQUIPES->DADOS_EQUIPES[$k][CO_INE],
                                            "ds_area" => $dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]->EQUIPES->DADOS_EQUIPES[$k][CO_AREA]);
               }
               
               $array_unidade[equipes] = $array_equipe;
               
               $tbUni->importaEstabelecimentos($array_unidade);
              
            }
            
        }
        
    }
    
    public function importaProfissionais($profissionais){
        $tbUsr = new Application_Model_Usuarios();
        $tbMes = new Application_Model_MedicoEspecialidade();
        $tbUnu = new Application_Model_UnidadeUsuarios();
        $tbUeqp = new Application_Model_UsuariosEquipe();
        $tbUsr->atualizaStatusGeral();
        $tbMes->atualizaStatusGeral();
        $tbUnu->excluirTodosCnes();
        $tbUeqp->excluirTodosCnes();
        foreach ($profissionais as $dados) {
            // Lendo por estabelecimento
            for ($i = 0; $i < count($dados->children()); $i++) {
                $array_prof = array("usr_nome"=>$dados->DADOS_PROFISSIONAIS[$i]["NM_PROF"],
                                    "usr_cpf" => $dados->DADOS_PROFISSIONAIS[$i]["CPF_PROF"],
                                    "cnes_cod_cns" => $dados->DADOS_PROFISSIONAIS[$i]["CO_CNS"],
                                    "cnes_data_nasc" => $dados->DADOS_PROFISSIONAIS[$i]["DT_NASC"],
                                    "cnes_sexo" => $dados->DADOS_PROFISSIONAIS[$i]["SEXO"],
                                    "usr_tipo_medico" => $dados->DADOS_PROFISSIONAIS[$i]["CONSELHO_ID"], // fazer validação através do tipo de conselho
                                    "cnes_sigla_est" => $dados->DADOS_PROFISSIONAIS[$i]["SG_UF_EMIS"],
                                    "usr_num_conselho" => $dados->DADOS_PROFISSIONAIS[$i]["NU_REGISTRO"],
                                    "usr_email" => $dados->DADOS_PROFISSIONAIS[$i]["E_MAIL"],
                                    "cnes_telefone" => $dados->DADOS_PROFISSIONAIS[$i]["TELEFONE"],
                                    "cnes_cod_cep" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[CO_CEP],
                                    "cnes_sigla_uf" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[SG_UF],
                                    "cnes_sigla_uf" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[SG_UF],
                                    "usr_ibge" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[CO_IBGE_MUN],
                                    "usr_ibge" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[CO_IBGE_MUN],
                                    "con_codigo" => $dados->DADOS_PROFISSIONAIS[$i]["CONSELHO_ID"],
                                    "cnes_bairrodist" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[BAIRRO],
                                    "cnes_logradouro" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[LOGRADOURO],
                                    "cnes_numero" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[NUMERO],
                                    "cnes_complement" => $dados->DADOS_PROFISSIONAIS[$i]->ENDERECO->DADOS_ENDERECO[COMPLEMENT],
                                    "usr_login" => $dados->DADOS_PROFISSIONAIS[$i]["CPF_PROF"],
                                    "usr_senha" =>md5("123"),
                                    "usr_ativo" => "S",
                                    "usr_ativo" => "S",
                                    "usr_ativo" => "S",
                                    "usr_tipo_medico" => $this->setTipoPorConselho($dados->DADOS_PROFISSIONAIS[$i]["CONSELHO_ID"]));
                //$array_prof["usr_tipo_medico"] = $tp_medico;
                //echo "<pre>".print_r($array_prof,1);die();
                $array_lotacoes = "";
                for($j = 0; $j < count($dados->DADOS_PROFISSIONAIS[$i]->LOTACOES->DADOS_LOTACOES);$j++){
                 
                 $array_lotacoes[$j] = array("cnes"=>$dados->DADOS_PROFISSIONAIS[$i]->LOTACOES->DADOS_LOTACOES[$j][CNES], //FALTA PEGAR O UNI_CODIGO POR CNES DEPOIS QUE SALVAR UNIDADE
                                             "co_ine"=>$dados->DADOS_PROFISSIONAIS[$i]->LOTACOES->DADOS_LOTACOES[$j][CO_INE],
                                             "co_cbo" => $dados->DADOS_PROFISSIONAIS[$i]->LOTACOES->DADOS_LOTACOES[$j][CO_CBO]);
               }
               
               
               $array_prof["lotacoes"] = $array_lotacoes;
               //
               $tbUsr->importProfissionais($array_prof);
            }
        }
    }
    
    public function setTipoPorConselho($id_conselho){
        if($id_conselho == 17 || $id_conselho == 70 || $id_conselho == 71 || $id_conselho == 74 || $id_conselho == 83 ){
            $usr_tipo_medico = "M";
        }else if($id_conselho == 66){
            $usr_tipo_medico = "E";
        }else if($id_conselho == 69){
            $usr_tipo_medico = "F";
        }else if($id_conselho == 77){
            $usr_tipo_medico = "P";
        }else if($id_conselho == 75){
            $usr_tipo_medico = "D";
           
        }else{
            $usr_tipo_medico = "C";
        }

        return $usr_tipo_medico;
    }
    
    public function salvarAction(){
        $tbArea = new Application_Model_Usuarios();        
       
        $arr = $this->_request->getPost();
        foreach ($arr as $ind=>$val){
            $array_cpf = array("usr_codigo" => $ind,
                               "usr_cpf" => $val);
            $tbArea->salvar($array_cpf);
        }
        return $this->_redirect("programasfederais/cnes-web/index");
    }
    
    public function salvarUnidadeAction(){
        $tbUni = new Application_Model_Unidade();        
       
        $arr = $this->_request->getPost();
        foreach ($arr as $ind=>$val){
            $array_cnes = array("uni_codigo" => $ind,
                               "uni_cnes" => $val);
            $tbUni->salvar($array_cnes);
        }
        return $this->_redirect("programasfederais/cnes-web/index");
    }
    
}

?>