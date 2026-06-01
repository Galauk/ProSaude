<?php


class ProgramasFederais_CnesController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Importação de dados CNES";
    }
    
    public function indexAction() {  }

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

        $IDEN = $xml->IDENTIFICACAO;

        unset($xml);

        // Desativando todos usuário, para ficar somente os do CNES ativo
        $dadosAtu = array("usr_ativo"=>"N", "cnes_ativo"=>"N");
        $tbUsr = new Application_Model_Usuarios();
        $tbUsr->inativaUsuarios($dadosAtu);

        $xml = $IDEN;
        // Lendo os Nos de Estabelecimentos
        foreach ($xml->ESTABELECIMENTOS as $dados) {
            // Iniciando controle de transação
            
            // Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            try{
                // Lendo por estabelecimento
                for ($i = 0; $i < count($dados->children()); $i++) {
                    // echo "<pre>";
                    // print_r($xml->PROFISSIONAIS->DADOS_PROFISSIONAIS[$i]);
                    // die();
                    $dadosEst = (array)$dados->DADOS_GERAIS_ESTABELECIMENTOS[$i];
                    
                    // Salvando dados do estabelecimento
                    $dadosCodsEst = $this->salvaDadosEstabelecimentosAction($dadosEst);
                }
                
                // Realizando a inserção dos de dados, se não deu nenhum problema
                //Zend_Db_Table::getDefaultAdapter()->commit();
            } catch (Exception $exc) {
                //Zend_Db_Table::getDefaultAdapter()->rollBack();
                $this->view->dados = $exc->getMessage();
                return $this->render("dados",NULL,TRUE);
            }        
        }
        
        $tbUni = new Application_Model_Unidade();
    
        foreach ($xml->PROFISSIONAIS as $dados) {
            // Iniciando controle de transação
            
            //Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            try{
                // Desativando todos usuário, para ficar somente os do CNES ativo
                
                // echo "<pre>";
                
                // Lendo por estabelecimento
                for ($i = 0; $i < count($dados->children()); $i++) {
                    // echo "<pre>";
                    // print_r($dados);
                    // die();
                    $dadosEst = (array)$dados->DADOS_PROFISSIONAIS[$i];
                    
                    // echo count($dadosEst['LOTACOES']->DADOS_LOTACOES);
                    // echo "<pre>";
                    
                    // print_r($dadosEst);
                    // die();
                    for($a = 0; $a < count($dadosEst['LOTACOES']->DADOS_LOTACOES); $a++){
                        $uniCod = "";
                        if(count($dadosEst['LOTACOES']->DADOS_LOTACOES) > 1){
                            //$dadosEst['@attributes']['CO_CBO'] = $dadosEst['LOTACAO'][];
                            //print_r($dadosEst['@attributes']['CO_CBO'] = );
                            //die();
                            //print_r($dadosEst['LOTACOES']->DADOS_LOTACOES[$a]['CNES']);
                            $dadosEst['@attributes']['CBO'] = (string) $dadosEst['LOTACOES']->DADOS_LOTACOES[$a]['CO_CBO'][0];
                            $dadosEst['@attributes']['CNES'] = (string) $dadosEst['LOTACOES']->DADOS_LOTACOES[$a]['CNES'][0];
                            $DADOS = $dadosEst['@attributes'];

                            // echo "<pre>"; print_r($DADOS); die();

                            $uniCod = $tbUni->getUnidadePorCnes($dadosEst['LOTACOES']->DADOS_LOTACOES[$a]['CNES'])->uni_codigo;
                            // echo $uniCod;
                            // echo "<br>";
                            $this->salvaDadosProfissionaisAction($DADOS, $uniCod);
                        } else {
                            //print_r($dadosEst['LOTACOES']->DADOS_LOTACOES);
                            
                            $dadosEst['@attributes']['CBO'] = (string) $dadosEst['LOTACOES']->DADOS_LOTACOES['CO_CBO'][0];
                            $dadosEst['@attributes']['CNES'] = (string) $dadosEst['LOTACOES']->DADOS_LOTACOES['CNES'][0];
                            $DADOS = $dadosEst['@attributes'];


                            $uniCod = $tbUni->getUnidadePorCnes($dadosEst['LOTACOES']->DADOS_LOTACOES['CNES'])->uni_codigo;
                            // echo $uniCod;
                            // echo "<br>";
                            $this->salvaDadosProfissionaisAction($DADOS, $uniCod);
                        }
                        // echo $a;
                        //echo "<br>";
                    }
                    // print_r($dadosEst['LOTACOES']->DADOS_LOTACOES); //die();
                    // die("uni: ".$dadosEst['LOTACOES']->DADOS_LOTACOES['CNES']);
                    /*if(count($dadosEst['LOTACOES']->DADOS_LOTACOES) == 1) {
                        $uniCod = $tbUni->getUnidadePorCnes($dadosEst['LOTACOES']->DADOS_LOTACOES['CNES'])->uni_codigo;
                        $this->salvaDadosProfissionaisAction($dadosEst, $uniCod);
                    } else {
                        for($j = 0; $j < count($dadosEst['LOTACOES']->DADOS_LOTACOES); $j++){
                            $uniCod = $tbUni->getUnidadePorCnes($dadosEst['LOTACOES']->DADOS_LOTACOES[$j]['CNES'])->uni_codigo;
                            $this->salvaDadosProfissionaisAction($dadosEst, $uniCod);
                        }
                    }*/

                    // print_r($uniCod); die();
                    // Salvando dados do profissional e suas especialidades
                }
                
                // Realizando a inserção dos de dados, se não deu nenhum problema
                // Zend_Db_Table::getDefaultAdapter()->commit();
            } catch (Exception $exc) {
                // Zend_Db_Table::getDefaultAdapter()->rollBack();
                $this->view->dados = $exc->getMessage();
                return $this->render("dados",NULL,TRUE);
            }   
                
        }
        $this->view->dialog = array("Confirmação","Importação de dados do CNES realizada com sucesso!",300,140);
        return $this->render("cnes/index",NULL,TRUE);
    }
    
    public function salvaDadosEstabelecimentosAction($dadosEst) {
        foreach ($dadosEst as $item => $value) {
            if (is_array($value)) { 
                // Sempre salva unidade, pela regra de negócio atual
                $arrayUni = array(
                    "cnes_unidade_id" => ($value["UNIDADE_ID"] != "" ? $value["UNIDADE_ID"] : NULL),
                    "uni_cnes" => ($value["CNES"] != "" ? $value["CNES"] : NULL),
                    "cnes_cnpj_mant" => ($value["CNPJ_MANT"] != "" ? $value["CNPJ_MANT"] : NULL),
                    "cnes_pfpj_ind" => ($value["PFPJ_IND"] != "" ? $value["PFPJ_IND"] : NULL),
                    "cnes_cod_siasus" => ($value["COD_SIASUS"] != "" ? $value["COD_SIASUS"] : NULL),
                    "cnes_nivel_dep" => ($value["NIVEL_DEP"] != "" ? $value["NIVEL_DEP"] : NULL),
                    "cnes_r_social" => ($value["R_SOCIAL"] != "" ? $value["R_SOCIAL"] : NULL),
                    "uni_desc" => ($value["NM_FANTA"] != "" ? $value["NM_FANTA"] : ""),
                    "uni_endereco" => ($value["LOGRADOURO"] != "" ? $value["LOGRADOURO"] : NULL),
                    "uni_numero" => ($value["NUMERO"] != "" ? $value["NUMERO"] : NULL),
                    "cnes_complement" => ($value["COMPLEMENT"] != "" ? $value["COMPLEMENT"] : NULL),
                    "cnes_bairro" => ($value["BAIRRO"] != "" ? $value["BAIRRO"] : NULL),
                    "uni_cep" => ($value["COD_CEP"] != "" ? $value["COD_CEP"] : NULL),
                    "cnes_reg_saude" => ($value["REG_SAUDE"] != "" ? $value["REG_SAUDE"] : NULL),
                    "cnes_micro_reg" => ($value["MICRO_REG"] != "" ? $value["MICRO_REG"] : NULL),
                    "cnes_dist_sanit" => ($value["DIST_SANIT"] != "" ? $value["DIST_SANIT"] : NULL),
                    "cnes_dist_admin" => ($value["DIST_ADMIN"] != "" ? $value["DIST_ADMIN"] : NULL),
                    "cnes_telefone" => ($value["TELEFONE"] != "" ? $value["TELEFONE"] : NULL),
                    "cnes_fax" => ($value["FAX"] != "" ? $value["FAX"] : NULL),
                    "cnes_e_mail" => ($value["E_MAIL"] != "" ? $value["E_MAIL"] : NULL),
                    "cnes_cpf" => ($value["CPF"] != "" ? $value["CPF"] : NULL),
                    "uni_cnpj" => ($value["CNPJ"] != "" ? $value["CNPJ"] : NULL),
                    "cnes_cod_esfadm" => ($value["COD_ESFADM"] != "" ? $value["COD_ESFADM"] : NULL),
                    "cnes_cod_ativ" => ($value["COD_ATIV"] != "" ? $value["COD_ATIV"] : NULL),
                    "cnes_reten_trib" => ($value["RETEN_TRIB"] != "" ? $value["RETEN_TRIB"] : NULL),
                    "cnes_cod_natorg" => ($value["COD_NATORG"] != "" ? $value["COD_NATORG"] : NULL),
                    "cnes_cod_client" => ($value["COD_CLIENT"] != "" ? $value["COD_CLIENT"] : NULL),
                    "cnes_num_alvara" => ($value["NUM_ALVARA"] != "" ? $value["NUM_ALVARA"] : NULL),
                    "cnes_data_exped" => ($value["DATA_EXPED"] != "" ? $value["DATA_EXPED"] : '01/01/1969'),
                    "cnes_ind_orgexp" => ($value["IND_ORGEXP"] != "" ? $value["IND_ORGEXP"] : NULL),
                    "cnes_tp_unid_id" => ($value["TP_UNID_ID"] != "" ? $value["TP_UNID_ID"] : NULL),
                    "cnes_cod_turnat" => ($value["COD_TURNAT"] != "" ? $value["COD_TURNAT"] : NULL),
                    "cnes_codnivhier" => ($value["CODNIVHIER"] != "" ? $value["CODNIVHIER"] : NULL),
                    "cnes_ind_uniesp" => ($value["IND_UNIESP"] != "" ? $value["IND_UNIESP"] : NULL),
                    "cnes_indvincsus" => ($value["INDVINCSUS"] != "" ? $value["INDVINCSUS"] : NULL),
                    "cnes_d_tercsih"  => ($value["D_TERCSIH"] != "" ? $value["D_TERCSIH"] : NULL),
                    "cnes_sigestgest" => ($value["SIGESTGEST"] != "" ? $value["SIGESTGEST"] : NULL),
                    "cnes_codmungest" => ($value["CODMUNGEST"] != "" ? $value["CODMUNGEST"] : NULL),
                    "cnes_statusmov"  => ($value["STATUSMOV"] != "" ? $value["STATUSMOV"] : NULL),
                    "cnes_codsiasus1" => ($value["CODSIASUS1"] != "" ? $value["CODSIASUS1"] : NULL),
                    "cnes_codsiasus2" => ($value["CODSIASUS2"] != "" ? $value["CODSIASUS2"] : NULL),
                    "cnes_codsiasus3" => ($value["CODSIASUS3"] != "" ? $value["CODSIASUS3"] : NULL),
                    "cnes_codsiasus4" => ($value["CODSIASUS4"] != "" ? $value["CODSIASUS4"] : NULL),
                    "cnes_codsiasus5" => ($value["CODSIASUS5"] != "" ? $value["CODSIASUS5"] : NULL),
                    "cnes_data_atu" => ($value["DATA_ATU"] != "" ? $value["DATA_ATU"] : date('Y-m-d')),
                    "cnes_usuario" => ($value["USUARIO"] != "" ? $value["USUARIO"] : NULL)
                );
                // Conferindo se unidade já existe pelo número do CNES, ai só atualiza
                $tbUni = new Application_Model_Unidade();
                $uniCodigo = $tbUni->getUnidadePorCnes($value["CNES"])->uni_codigo; 
                if ($uniCodigo != ""){ $arrayUni["uni_codigo"] = $uniCodigo; }
                // Salva ou atualiza a unidade

                $verifica = $tbUni->verificaSeJáExiste($value["CNES"]);
                if($verifica->qtd=='0') { 
                   $codUnid = $tbUni->salvar($arrayUni);
                } else {
                    $whe = "uni_cnes = '".$value["CNES"]."'";
                    $up = $tbUni->update($arrayUni,$whe);
                }
                //echo '<pre>' . print_r($arrayUni); die('asdfasdfasdfasdfasdfasdf');
                // Se a esfera administrativa for privada é prestador
                if ($value["COD_ESFADM"] == 4) {
                    $codPrest = $this->salvaDadosPrestadorDeServicoAction($value);
                }
                // Pega os código de unidade e prestador e retorna para vincular
                $dadosCodsEst = array(
                    "uni_codigo" => $codUnid, 
                    "med_codigo" => ($codPrest != "" ? $codPrest : "NULL") 
                );
                return $dadosCodsEst;
            }
        }
    }
    
    public function salvaDadosPrestadorDeServicoAction($value){
        $arrayPrest = array(
            "cnes_unidade_id" => ($value["UNIDADE_ID"] != "" ? $value["UNIDADE_ID"] : NULL),
            "med_crm" => 'NAOTEM',
            "uf_codigo_crm" => '18',
            "med_cnes" => ($value["CNES"] != "" ? $value["CNES"] : NULL),
            "cnes_cnpj_mant" => ($value["CNPJ_MANT"] != "" ? $value["CNPJ_MANT"] : NULL),
            "cnes_pfpj_ind" => ($value["PFPJ_IND"] != "" ? $value["PFPJ_IND"] : NULL),
            "cnes_cod_siasus" => ($value["COD_SIASUS"] != "" ? $value["COD_SIASUS"] : NULL),
            "cnes_nivel_dep" => ($value["NIVEL_DEP"] != "" ? $value["NIVEL_DEP"] : NULL),
            "cnes_r_social" => ($value["R_SOCIAL"] != "" ? $value["R_SOCIAL"] : NULL),
            "med_nome" => ($value["NOME_FANTA"] != "" ? $value["NOME_FANTA"] : NULL),
            "med_endereco" => ($value["LOGRADOURO"] != "" ? $value["LOGRADOURO"] : NULL),
            "med_end_numero" => ($value["NUMERO"] != "" ? $value["NUMERO"] : NULL),
            "med_end_complemento" => ($value["COMPLEMENT"] != "" ? $value["COMPLEMENT"] : NULL),
            "med_end_bairro" => ($value["BAIRRO"] != "" ? $value["BAIRRO"] : NULL),
            "med_end_cep" => ($value["COD_CEP"] != "" ? $value["COD_CEP"] : NULL),
            "cnes_reg_saude" => ($value["REG_SAUDE"] != "" ? $value["REG_SAUDE"] : NULL),
            "cnes_micro_reg" => ($value["MICRO_REG"] != "" ? $value["MICRO_REG"] : NULL),
            "cnes_dist_sanit" => ($value["DIST_SANIT"] != "" ? $value["DIST_SANIT"] : NULL),
            "cnes_dist_admin" => ($value["DIST_ADMIN"] != "" ? $value["DIST_ADMIN"] : NULL),
            "med_end_telefone" => ($value["TELEFONE"] != "" ? $value["TELEFONE"] : NULL),
            "cnes_fax" => ($value["FAX"] != "" ? $value["FAX"] : NULL),
            "med_email" => ($value["E_MAIL"] != "" ? $value["E_MAIL"] : NULL),
            "med_cpf" => ($value["CPF"] != "" ? $value["CPF"] : NULL),
            "med_cnpj" => ($value["CNPJ"] != "" ? $value["CNPJ"] : NULL),
            "cnes_cod_esfadm" => ($value["COD_ESFADM"] != "" ? $value["COD_ESFADM"] : NULL),
            "cnes_cod_ativ" => ($value["COD_ATIV"] != "" ? $value["COD_ATIV"] : NULL),
            "cnes_reten_trib" => ($value["RETEN_TRIB"] != "" ? $value["RETEN_TRIB"] : NULL),
            "cnes_cod_natorg" => ($value["COD_NATORG"] != "" ? $value["COD_NATORG"] : NULL),
            "cnes_cod_client" => ($value["COD_CLIENT"] != "" ? $value["COD_CLIENT"] : NULL),
            "cnes_num_alvara" => ($value["NUM_ALVARA"] != "" ? $value["NUM_ALVARA"] : NULL),
            "cnes_data_exped" => ($value["DATA_EXPED"] != "" ? $value["DATA_EXPED"] : NULL),
            "cnes_ind_orgexp" => ($value["IND_ORGEXP"] != "" ? $value["IND_ORGEXP"] : NULL),
            "cnes_tp_unid_id" => ($value["TP_UNID_ID"] != "" ? $value["TP_UNID_ID"] : NULL),
            "cnes_cod_turnat" => ($value["COD_TURNAT"] != "" ? $value["COD_TURNAT"] : NULL),
            "cnes_codnivhier" => ($value["CODNIVHIER"] != "" ? $value["CODNIVHIER"] : NULL),
            "cnes_ind_uniesp" => ($value["IND_UNIESP"] != "" ? $value["IND_UNIESP"] : NULL),
            "cnes_indvincsus" => ($value["INDVINCSUS"] != "" ? $value["INDVINCSUS"] : NULL),
            "cnes_d_tercsih"  => ($value["D_TERCSIH"] != "" ? $value["D_TERCSIH"] : NULL),
            "cnes_sigestgest" => ($value["SIGESTGEST"] != "" ? $value["SIGESTGEST"] : NULL),
            "cnes_codmungest" => ($value["CODMUNGEST"] != "" ? $value["CODMUNGEST"] : NULL),
            "cnes_statusmov"  => ($value["STATUSMOV"] != "" ? $value["STATUSMOV"] : NULL),
            "cnes_codsiasus1" => ($value["CODSIASUS1"] != "" ? $value["CODSIASUS1"] : NULL),
            "cnes_codsiasus2" => ($value["CODSIASUS2"] != "" ? $value["CODSIASUS2"] : NULL),
            "cnes_codsiasus3" => ($value["CODSIASUS3"] != "" ? $value["CODSIASUS3"] : NULL),
            "cnes_codsiasus4" => ($value["CODSIASUS4"] != "" ? $value["CODSIASUS4"] : NULL),
            "cnes_codsiasus5" => ($value["CODSIASUS5"] != "" ? $value["CODSIASUS5"] : NULL),
            "cnes_data_atu" => ($value["DATA_ATU"] != "" ? $value["DATA_ATU"] : NULL),
            "cnes_usuario" => ($value["USUARIO"] != "" ? $value["USUARIO"] : NULL)
        );
        // Conferindo se Prestador já existe pelo número do CNES, ai só atualiza
        $tbPrest = new Application_Model_Medico();
        $medCodigo = $tbPrest->getPrestadorPorCnes($value["CNES"])->med_codigo; 
        if ($medCodigo != ""){ $arrayPrest["med_codigo"] = $medCodigo; }
        // Salva ou atualiza os dados de prestador de serviço
        $codPrest = $tbPrest->salvarPrestadorDeServico($arrayPrest);
        return $codPrest;
    }

    public function salvaDadosProfissionaisAction($dadosEst, $codUni){
        // echo "<pre>";
        // print_r($dadosEst);
        // die();
    
        $usr_tipo = "";

        $tbEspecialidade = new Application_Model_Especialidade();

        $resultado = $tbEspecialidade->getEspecialidadePorCbo($dadosEst["CBO"])->toArray();

        if($resultado != "" && $resultado != NULL){
            $especialidade = explode(" ", $resultado['esp_nome'])[0];
            
            switch ($especialidade) {
                case "Médico":
                    $usr_tipo = 'M';
                    break;
                case "Enfermeiro":
                    $usr_tipo = "E";
                    break;
                case "Dentista":
                    $usr_tipo = "D";
                    break;
                case "Psicólogo":
                    $usr_tipo = "P";
                    break;
                case "Farmacêutico":
                    $usr_tipo = "F";
                    break;
                case "Bioquímico":
                    $usr_tipo = "B";
                    break;
                case "Auxiliar":
                    $usr_tipo = "A";
                    break;
                
                default:
                    $usr_tipo = "C";
                    break;
            }
        } else {
            $usr_tipo = "C";
        }


        // $tbEsp =  getEspecialidadePorCbo($dadosEst["CBO"]);


/*

                        usr_tipo_medico = 'M' THEN 'Médico'
                        WHEN usr_tipo_medico = 'E' THEN 'Enfermeiro(a)'
                        WHEN usr_tipo_medico = 'D' THEN 'Dentista'
                        WHEN usr_tipo_medico = 'P' THEN 'Psicólogo(a)'
                        WHEN usr_tipo_medico = 'F' THEN 'Farmáceutico(a)'
                        WHEN usr_tipo_medico = 'B' THEN 'Bioquímico(a)'
                        WHEN usr_tipo_medico = 'A' THEN 'Aux. Enfermagem'
else 
                        WHEN usr_tipo_medico = 'C' THEN 'Comum'
*/

        //foreach($dadosEst as $key => $value) {
            // }
            // die();
            // Lendo dados dos profissionais
            //foreach ($dadosEst as $value) {

            $arrayProf = array(
                "cnes_prof_id" => $this->trataValor($dadosEst["PROF_ID"]),
                "cnes_ativo" => 'S',
                "usr_ativo" => 'S',
                "usr_situacao" => 'A',
                "usr_tipo_medico" => $usr_tipo,
                "usr_tipo" => 'U',
                "usr_cpf" => $this->trataValor($dadosEst["CPF_PROF"]),
                "cnes_pispasep" => $this->trataValor($dadosEst["PISPASEP"]),
                "usr_login" => $this->trataValor($dadosEst["CPF_PROF"]),
                "usr_senha" => md5("123"),
                "usr_nome" => $this->trataValor($dadosEst["NM_PROF"]),
                "cnes_nome_mae" => $this->trataValor($dadosEst["NM_MAE"]),
                "cnes_data_nasc" => $this->trataValor($dadosEst["DT_NASC"]),
                "cnes_cod_mun" => $this->trataValor($dadosEst["COD_MUN"]),
                "cnes_sexo" => $this->trataValor($dadosEst["SEXO"]),
                "cnes_num_livro" => $this->trataValor($dadosEst["NUM_LIVRO"]),
                "cnes_num_folha" => $this->trataValor($dadosEst["NUM_FOLHA"]),
                "cnes_num_termo" => $this->trataValor($dadosEst["NUM_TERMO"]),
                "cnes_codorgemis" => $this->trataValor($dadosEst["CODORGEMIS"]),
                "cnes_data_emiss" => $this->trataValor($dadosEst["DATA_EMISS"]),
                "cnes_num_ident" => $this->trataValor($dadosEst["NUM_IDENT"]),
                "cnes_sigla_est" => $this->trataValor($dadosEst["SIGLA_EST"]),
                "cnes_dtemiident" => $this->trataValor($dadosEst["DTEMIIDENT"]),
                "cnes_data_entra" => $this->trataValor($dadosEst["DATA_ENTRA"]),
                "cnes_ctps_numer" => $this->trataValor($dadosEst["CTPS_NUMER"]),
                "cnes_serie" => $this->trataValor($dadosEst["SERIE"]),
                "cnes_sigestctps" => $this->trataValor($dadosEst["SIGESTCTPS"]),
                "cnes_dtemisctps" => $this->trataValor($dadosEst["DTEMISCTPS"]),
                "cnes_logradouro" => $this->trataValor($dadosEst["LOGRADOURO"]),
                "cnes_numero" => $this->trataValor($dadosEst["NUMERO"]),
                "cnes_complement" => $this->trataValor($dadosEst["COMPLEMENT"]),
                "cnes_bairrodist" => $this->trataValor($dadosEst["BAIRRODIST"]),
                "cnes_cod_cep" => $this->trataValor($dadosEst["COD_CEP"]),
                "cnes_dt_iniativ" => $this->trataValor($dadosEst["DT_INIATIV"]), 
                "cnes_sigla_uf" => $this->trataValor($dadosEst["SG_UF_EMIS"]),
                "cnes_codescolar" => $this->trataValor($dadosEst["CODESCOLAR"]),
                "cnes_cod_certid" => $this->trataValor($dadosEst["COD_CERTID"]),
                "cnes_ind_nacio" => $this->trataValor($dadosEst["IND_NACIO"]),
                "cnes_nome_carto" => $this->trataValor($dadosEst["NOME_CARTO"]),
                "cnes_cod_banco" => $this->trataValor($dadosEst["COD_BANCO"]),
                "cnes_nome_pais" => $this->trataValor($dadosEst["NOME_PAIS"]),
                "cnes_num_agenc" => $this->trataValor($dadosEst["NUM_AGENC"]),
                "cnes_conta_cc" => $this->trataValor($dadosEst["CONTA_CC"]), 
                "cnes_cod_cns" => $this->trataValor($dadosEst["CO_CNS"]),
                "cnes_d_tercsih" => $this->trataValor($dadosEst["D_TERCSIH"]),
                "cnes_status" => $this->trataValor($dadosEst["STATUS"]),
                "cnes_statusmov" => $this->trataValor($dadosEst["STATUSMOV"]),
                "cnes_data_atu" => $this->trataValor($dadosEst["DATA_ATU"]),
                "cnes_usuario" => $this->trataValor($dadosEst["USUARIO"]),
                "cnes_cd_raca" => $this->trataValor($dadosEst["CD_RACA"]),
                "cnes_telefone" => $this->trataValor($dadosEst["TELEFONE"]),
                "cnes_cd_sit_fam" => $this->trataValor($dadosEst["CD_SIT_FAM"]),
                "cnes_fr_escolar" => $this->trataValor($dadosEst["FR_ESCOLAR"]),
                "cnes_nome_pai" => $this->trataValor($dadosEst["NOME_PAI"]),
                "cnes_cd_tp_logr" => $this->trataValor($dadosEst["CD_TP_LOGR"]),
                "cnes_tit_eleit" => $this->trataValor($dadosEst["TIT_ELEIT"]),
                "cnes_zona" => $this->trataValor($dadosEst["ZONA"]),
                "cnes_secao" => $this->trataValor($dadosEst["SECAO"]),
                "cnes_portaria" => $this->trataValor($dadosEst["PORTARIA"]),
                "cnes_dt_natur" => $this->trataValor($dadosEst["DT_NATUR"]),
                "cnes_cd_pais" => $this->trataValor($dadosEst["CD_PAIS"]),
                "cnes_tp_sus_nao_sus" => $this->trataValor($dadosEst["TP_SUS_NAO_SUS"])
            );
            
            // Conferindo se profissional que esta sendo inserido já existe
            $tbUsr = new Application_Model_Usuarios();
            
            $usrCodigo = $tbUsr->getUsuariosPorCpf($this->trataValor($dadosEst["CPF_PROF"]))->usr_codigo; 
            
            if ($usrCodigo != ""){ 
                $arrayProf["usr_codigo"] = $usrCodigo; 
                unset($arrayProf["usr_login"]);
                unset($arrayProf["usr_senha"]);
            }

            // echo '<pre>'.print_r($arrayProf); 
            // die('asfasdf');
            // Salvando ou atualizando profissional

            $codMed = $tbUsr->salvar($arrayProf);
                 
            // Salvando vínculo do profissional com a unidade
            if(($codUni=='' OR $codMed=='')) {
            $tbUni = new Application_Model_Unidade();
             $uni = $tbUni->getDados("99999");
             if($uni["uni_codigo"]=='') {
                 $dadosUniImp = array(
                        "uni_codigo" => "99999",
                        "uni_desc" => "Importacao Ibitech"
                    ); 
                $unicad = $tbUni->salvar($dadosUniImp);
               // echo "CROCODILO";

             }
//             echo '<pre>'.print_r($ver);
            // die($ver."fuck");
                $dadosUniUsu = array(
                    "uni_codigo" => '99999',
                    "usr_codigo" => '1'
                );            
            } else {               
                $dadosUniUsu = array(
                    "uni_codigo" => $codUni,
                    "usr_codigo" => $codMed
                );                        
            }
          //  print_r($dadosUniUsu);
            $tbUniUsr = new Application_Model_UnidadeUsuarios();
            $unuCodigo = $tbUniUsr->getCodigoPorUsuariosComUnidades($dadosUniUsu);
            if ($unuCodigo != ""){ $dadosUniUsu["unu_codigo"] = $unuCodigo; }
                $ret = $tbUniUsr->salvar($dadosUniUsu);

            //foreach($dadosEst['LOTACOES'] as $VALOR){
                //print_r($VALOR);
                
                // Pegando especialidade pelo código do CBO
                $tbEsp = new Application_Model_Especialidade();
                $codCbo = $this->trataValor($dadosEst["CBO"]);

                // die($codCbo);

                $espCod = $tbEsp->getEspecialidadePorCbo($codCbo)->esp_codigo;
                
                $espCodVal = ($espCod != "" ? $espCod : '1055');
                if($codMed=="") { $codMed = 1; }
                if($codUni=="") { $codUni = 1; }
                $arrayVincProf = array(
                    "med_codigo" => $codMed,
                    "esp_codigo" => $espCodVal,
                    "mes_ativo" => 'A',
                    "uni_codigo" => $codUni
                );

                // Salvando Especialidade do profissional
                $tbMedEsp = new Application_Model_MedicoEspecialidade();
                //echo '<pre>'.print_r($arrayVincProf);
                //die('asfdasdf');
                $mesCodigo = $tbMedEsp->confereCadEspecialidadePorMedico($codMed, $espCodVal)->mes_codigo;
                if ($mesCodigo != "") { $arrayVincProf["mes_codigo"] = $mesCodigo; }

                $tbMedEsp->salvar($arrayVincProf); 
            //}
            //die();
            return true;
        //}
    }
    
    public function trataValor($valor){
        foreach ((array)$valor as $value){
            $valorFinal = ($value != "" ? $value : NULL); 
            return $valorFinal;
        }
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
        if ($arqError == 0) {
            $pasta = $_SESSION["root"]."WebSocialSaude/zf/public/uploads/cnes/";
            $upload = move_uploaded_file($arqTemp, $pasta . $arqName);
        }
        // Transforma o XML em objeto
        $xml = simplexml_load_file($pasta.$arqName);
        return $xml;
    }
    
    public function orientacoesAction(){
        $this->view->title = "Orientações CNES";
        $tbUni = new Application_Model_Unidade();
        $tbUsr = new Application_Model_Usuarios();
        $tbPrest = new Application_Model_Medico();
        $qtd_uni = $tbUni->getQtdUnidadesAtivasCnes()->qtd_uni;
        $qtd_usr = $tbUsr->getQtdUsuariosAtivosCnes()->qtd_usr;
        $qtd_prest = $tbPrest->getQtdPrestadorAtivosCnes()->qtd_prest;
        $this->view->qtd_uni = $qtd_uni;
        $this->view->qtd_usr = $qtd_usr;
        $this->view->qtd_prest = $qtd_prest;
        $this->liberaImportacaoCnesAction($qtd_uni,$qtd_usr,$qtd_prest);
    }
    
    public function liberaImportacaoCnesAction($qtd_uni,$qtd_usr,$qtd_prest){
        if ($qtd_uni > 0 && $qtd_usr > 0 && $qtd_prest > 0) {
            $tbConf = new Application_Model_Configuracao();
            $conf_codigo = $tbConf->getDadosConfigPelaChave("IMPORTACAO_CNES")->conf_codigo;
            $dadosConfig = array(
                "conf_codigo" => $conf_codigo,
                "conf_valor_bool" => true
            );
            $tbConf->salvarDadosConfig($dadosConfig);
        }
    }
    
    public function moduloUnidadesCnesAction(){
        $this->view->title = "Modúlo Unidades CNES";
    }
    
    public function comparaUnidadesAction(){
        $this->view->title = "Modúlo Unidades CNES";
        $xml = $this->uploadArquivoCnesAction($_FILES);
        $dadosUni = array();
        $tbUni = new Application_Model_Unidade();
        foreach ($xml->ESTABELECIMENTOS as $dados) {
            // Lendo por estabelecimento
            for ($i = 0; $i < count($dados->children()); $i++) {
                $cnes = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["CNES"]);
                $nome_fantasia = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["NOME_FANTA"]);
                $nome_fantasia_quebrado = explode(" ", $nome_fantasia);
                $razao_social = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["R_SOCIAL"]);
                $dadosUni[$i] = array(
                    "cnes" => $cnes,
                    "nome_fantasia" => $nome_fantasia,
                    "razao_social" => $razao_social
                );
                // Compara pra ver se existe alguma unidade semelhante
                $dadosUniAtuais = $tbUni->buscaUnidadePorNome($nome_fantasia,$nome_fantasia_quebrado,$razao_social)->toArray();
                if (count($dadosUniAtuais)>0) {
                    for($j=0; $j<count($dadosUniAtuais); $j++){
                        $dadosUni[$i]["unidades"][$j]["uni_codigo"] = $dadosUniAtuais[$j]["uni_codigo"];
                        $dadosUni[$i]["unidades"][$j]["uni_desc"] = $dadosUniAtuais[$j]["uni_desc"] != "" ? $dadosUniAtuais[$j]["uni_desc"] : "";
                    }
                }
                
            }
        }
        // Se não tiver nada a ser comparado seta a comparação como realizada
        if (count($dadosUni) == 0) {
            $dadosComp = array(
                "uni_desc"=>"CNES ATIVO",
                "cnes_ativo"=>"S"
            );
            $tbUni->salvar($dadosComp);
        }
        $this->view->dados = $dadosUni;
    }
    
    public function salvarComparacaoUnidadesAction(){
        $tbUni = new Application_Model_Unidade();
        foreach($_POST as $ind => $val){
            if (substr_count($val,"novo") == 0) {
                list($uni_codigo, $cnes) = explode("|",$val);
                $unidade = array(
                    "uni_codigo" => $uni_codigo,
                    "uni_cnes" => $cnes,
                    "cnes_ativo" => "S"
                );
                $tbUni->salvar($unidade);
            }  
        }
        // Se não tiver nenhum ativo depois do de-pará, insere um novo 
        if ($tbUni->getQtdUnidadesAtivasCnes()->qtd_uni == 0) {
            $dadosComp = array(
                "uni_desc"=>"CNES ATIVO",
                "cnes_ativo"=>"S"
            );
            $tbUni->salvar($dadosComp);
        }
        $this->_redirect("programas-federais/cnes/orientacoes");
    }

    public function moduloPrestadorCnesAction(){
        $this->view->title = "Modúlo Prestadores de Serviços CNES";
    }
    
    public function comparaPrestadorAction(){
        $this->view->title = "Modúlo Prestadores de Serviços CNES";
        $xml = $this->uploadArquivoCnesAction($_FILES);
        $dadosUni = array();
        $tbPrest = new Application_Model_Medico();
        foreach ($xml->ESTABELECIMENTOS as $dados) {
            // Lendo por estabelecimento
            for ($i = 0; $i < count($dados->children()); $i++) {
                $cnes = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["CNES"]);
                $nome_fantasia = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["NOME_FANTA"]);
                $nome_fantasia_quebrado = explode(" ", $nome_fantasia);
                $razao_social = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["R_SOCIAL"]);
                $esf_adm = $this->trataValor($dados->DADOS_GERAIS_ESTABELECIMENTOS[$i]["COD_ESFADM"]);
                if ($esf_adm ==4){
                    $dadosPrest[$i] = array(
                        "cnes" => $cnes,
                        "nome_fantasia" => $nome_fantasia,
                        "razao_social" => $razao_social
                    );
                    // Compara pra ver se existe alguma unidade semelhante
                    $dadosPrestAtuais = $tbPrest->buscaPrestadorPorNome($nome_fantasia,$nome_fantasia_quebrado,$razao_social)->toArray();
                    if (count($dadosPrestAtuais)>0) {
                        for($j=0; $j<count($dadosPrestAtuais); $j++){
                            $dadosPrest[$i]["prestador"][$j]["med_codigo"] = $dadosPrestAtuais[$j]["med_codigo"];
                            $dadosPrest[$i]["prestador"][$j]["med_nome"] = $dadosPrestAtuais[$j]["med_nome"];
                        }
                    }
                }
            }
        }
        // Se não tiver nada a ser comparado seta a comparação como realizada
        if (count($dadosPrest) == 0) {
            $dadosComp = array(
                "med_nome"=>"CNES ATIVO",
                "cnes_ativo"=>"S",
                "med_crm"=>"NAOTEM",
                "uf_codigo_crm"=>"1"
            );
            $tbPrest->salvarPrestadorDeServico($dadosComp);
        }
        $this->view->dados = $dadosPrest;
    }
    
    public function salvarComparacaoPrestadorAction(){
        $tbPrest = new Application_Model_Medico();
        foreach($_POST as $ind => $val){
            if (substr_count($val,"novo") == 0) {
                list($med_codigo, $cnes) = explode("|",$val);
                $prestador = array(
                    "med_codigo" => $med_codigo,
                    "med_cnes" => $cnes,
                    "cnes_ativo" => "S"
                );
                $tbPrest->salvarPrestadorDeServico($prestador);
            }  
        }
        // Se não tiver nenhum ativo depois do de-pará, insere um novo 
        if ($tbPrest->getQtdPrestadorAtivosCnes()->qtd_prest == 0) {
            $dadosComp = array(
                "med_nome"=>"CNES ATIVO",
                "cnes_ativo"=>"S",
                "med_crm"=>"NAOTEM",
                "uf_codigo_crm"=>"1"
            );
            $tbPrest->salvarPrestadorDeServico($dadosComp);
        }
        $this->_redirect("programas-federais/cnes/orientacoes");
    }
    
    public function moduloUsuariosCnesAction(){
        $this->view->title = "Modúlo Usuários CNES";
    }
    
    public function comparaUsuariosAction(){
        $this->view->title = "Modúlo Usuários CNES";
        $xml = $this->uploadArquivoCnesAction($_FILES);
        $dadosUsr = array();
        $i = 0;
        $tbUsr = new Application_Model_Usuarios();
        foreach ($xml->ESTABELECIMENTOS as $dados) {
            foreach ($dados->DADOS_GERAIS_ESTABELECIMENTOS as $dadosEst) {
                foreach ($dadosEst->PROFISSIONAIS as $dadosProf) {
                    foreach ($dadosProf->DADOS_PROFISSIONAIS as $prof) {
                        $cpf_prof = $this->trataValor($prof["CPF_PROF"]);
                        $nome_prof = $this->trataValor($prof["NOME_PROF"]);
                        $nome_prof_quebrado = explode(" ", $nome_prof);
                        if (!$this->array_search_multi($cpf_prof, $dadosUsr)) {    
                            $dadosUsr[$i] = array(
                                "cpf_prof" => trim($cpf_prof),
                                "nome_prof" => trim($nome_prof)
                            );
                        }
                        // Compara pra ver se existe alguma unidade semelhante
                        $dadosUsrAtuais = $tbUsr->buscaUsuariosPorNome($nome_prof,$nome_prof_quebrado)->toArray();
                        if (count($dadosUsrAtuais)>0) {
                            for($j=0; $j<count($dadosUsrAtuais); $j++){
                                $dadosUsr[$i]["usuarios"][$j]["usr_codigo"] = $dadosUsrAtuais[$j]["usr_codigo"];
                                $dadosUsr[$i]["usuarios"][$j]["usr_nome"] = $dadosUsrAtuais[$j]["usr_nome"];
                            }
                        }
                        $i++;
                    }
                }
            }
        }
         // Se não tiver nada a ser comparado seta a comparação como realizada
        if (count($dadosUsr) == 0) {
            $dadosComp = array(
                "usr_nome"=>"CNES ATIVO",
                "usr_login"=>"cnes",
                "usr_senha"=>"123",
                "cnes_ativo"=>"S"
            );
            $tbUsr->salvar($dadosComp);
        }
        $this->view->dados = $dadosUsr;
    }
    
    public function salvarComparacaoUsuariosAction(){
        $tbUsr = new Application_Model_Usuarios();
        foreach($_POST as $ind => $val){
            if (substr_count($val,"novo") == 0) {
                list($usr_codigo, $usr_cpf) = explode("|",$val);
                $usuarios = array(
                    "usr_codigo" => $usr_codigo,
                    "usr_cpf" => $usr_cpf,
                    "cnes_ativo" => "S"
                );

                if($usr_codigo!='') {
                    $tbUsr->salvar($usuarios);
                }
            }  
        }
        // Se não tiver nenhum ativo depois do de-pará, insere um novo 
        if ($tbUsr->getQtdUsuariosAtivosCnes()->qtd_usr == 0) {
            $dadosComp = array(
                "usr_nome"=>"CNES ATIVO",
                "usr_login"=>"cnes",
                "usr_senha"=>"123",
                "cnes_ativo"=>"S"
            );
            $tbUsr->salvar($dadosComp);
        }
        $this->_redirect("programas-federais/cnes/orientacoes");
    }
    
    public function array_search_multi($busca, $arrays){
        foreach($arrays as $array){
                if( $i = array_search($busca,$array) !== false){
                    return $i;
                }
        }
        return false;
    }
}

?>