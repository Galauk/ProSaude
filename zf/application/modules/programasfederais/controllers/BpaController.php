<?

class ProgramasFederais_BpaController extends Zend_Controller_Action {
    
    public function init() {
        //session_start();
        $this->_helper->acl->allow(NULL,array('index'));
        $this->view->title = "BPA MAG";
        $this->dbtable = new Application_Model_DbTable_BPA();
        $this->model = new Application_Model_BPA();
        $this->unidade = new Application_Model_Unidade();
        $this->medico = new Application_Model_Medico();
        $this->quebra = chr(13) . chr(10);

    }

    public function geraBpaAction() {
       # ini_set("display_errors", 1);
        
        // error_reporting(E_ALL);
        
        $tbConfig = new Application_Model_Configuracao();
        $ibge = $tbConfig->getConfig("CID_CODIGO_IBGE");
        $tipo_finan = $this->_request->getPost("tipoBpa");
        $tipo_origem = $this->_request->getPost("tipo");
        $uni_codigo = $this->_request->getPost("codigo"); // == "" ? $_SESSION['uni_codigo'] : $this->_request->getPost("codigo");
        $data = $this->_request->getPost("competencia");
        
        // echo "<pre>";
        // print_r($uni_codigo);
        // die();

        $diaCompetencia = $tbConfig->getConfig("BPA_DIA_COMPETENCIA");        
        $dataExplode = explode('/', $data);
        $dataSerapadaPorTraco = $dataExplode[0].'-'.$dataExplode[1];
        $dataCompetencia = $diaCompetencia.'-'.$dataSerapadaPorTraco;        
        $dataCompetenciaInicio = date('d/m/Y',strtotime('-1 months', strtotime($dataCompetencia)));        
        $dataCompetenciaFim = date('d/m/Y',strtotime('-1 day', strtotime($dataCompetencia)));
        
        $msg = $this->montaCabecalho($tipo_finan,$data,$tipo_origem,$uni_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
        $msg .= $this->montaConsolidadoAction($tipo_finan,$data,$tipo_origem,$uni_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
        $msg .= $this->montaIndividualizadoAction($tipo_finan,$data,$tipo_origem,$uni_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
        
        //            die($msg);
        

        $meses = array("01"=>"JAN", "02"=>"FEV", "03"=>"MAR", "04"=>"ABR", "05"=>"MAI", "06"=>"JUN", "07"=>"JUL", "08"=>"AGO", "09"=>"SET", "10"=>"OUT", "11"=>"NOV", "12"=>"DEZ");
        $data = explode("/", $data);
        
        $path = $_SESSION["root"]."WebSocialSaude/zf/public/arqs/";
        
        $arq = $this->criaArquivo("PA".$ibge, $msg, $path, "." . $meses[$data[0]]);

        
        $nomeDoArquivo = "PA".$ibge.".".$meses[$data[0]];
        
        $this->downloadBpa($path, $nomeDoArquivo);
    }
    
    public function downloadBpa($path, $nomeDoArquivo ){
        $link = $path.$nomeDoArquivo;
        header("Content-Disposition: attachment; filename=".$nomeDoArquivo."");
        header("Content-Type: application/plain");
        readfile($link);
        
        //NECESSARIO DESABILITAR A VIEW...!!! OU O ARQUIVO IRA IMPRIMIR UM ERRO ZEND !!!!!
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function montaCabecalho($tp_financiamento=FALSE,$data=FALSE,$origem=FALSE,$origem_codigo=FALSE,$dataCompetenciaInicio=FALSE,$dataCompetenciaFim=FALSE) {
        $tbSec = new Application_Model_Secretaria();
        $dados_sec = $tbSec->getDadosSec();
        $quebra = explode("/", $data);
        $ano = $quebra[1];
        $mes = $quebra[0];
        if($tp_financiamento == '04'){
            $procedimento_qtde = $this->dbtable->getProcedimentosBPAPorPeriodo('04',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $procedimento_qtde = $procedimento_qtde + $this->dbtable->getProcedimentosBPAPorPeriodo('05',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $procedimento_qtde = $procedimento_qtde + $this->dbtable->getProcedimentosBPAPorPeriodo('06',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
        }else{
            $procedimento_qtde = $this->dbtable->getProcedimentosBPAPorPeriodo($tp_financiamento,$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
        }
        $numFolhas = $this->corta((round(($procedimento_qtde->count / 20)) + 1),6,"0");
        $numLinhaBpa = $this->corta($procedimento_qtde->count,6,"0");
        $calculaControle = $this->corta($this->model->calculaControle($procedimento_qtde->sum,$procedimento_qtde->count),4,"0");
        $array_origem = $this->origem($origem, $origem_codigo);

        $versaoSistema = $this->corta($_SESSION['versao'], 10, " ");
        $quebra = chr(13) . chr(10);
        $cabecalho = "01#BPA#".$ano.$mes.$numLinhaBpa.$numFolhas.$calculaControle.$array_origem["nome"].substr($array_origem["nome"], 0, 6).preg_replace("/[^0-9]+/", "", $array_origem["cnpj"]).$dados_sec["nome_secretaria"]."M".$versaoSistema.$quebra;
        return $cabecalho;
    }
    
    public function montaConsolidadoAction($tp_financiamento=FALSE,$data=FALSE,$origem=FALSE,$origem_codigo=FALSE,$dataCompetenciaInicio=FALSE,$dataCompetenciaFim=FALSE) {
        $linhas = NULL;
        if($tp_financiamento == '04'){
            $linhas06 = $this->dbtable->getRegistrosBpa('06',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $linhas05 = $this->dbtable->getRegistrosBpa('05',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $linhas04 = $this->dbtable->getRegistrosBpa('04',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $linhas = array_merge($linhas06, $linhas05);
            $linhas = array_merge($linhas, $linha04);
        }else{


                    $linhas = $this->dbtable->getRegistrosBpa($tp_financiamento,$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            // print_r($linhas);
            // die();
        }
        $data = explode("/", $data);
        $dataAnoMes = $data[1].$data[0];
        $codCnes = "";
        foreach($linhas as $linha) {

#echo '<pre>'.print_r($linha); die();

if(($linha['proc_codigo_sus']=='0301010064' OR $linha['proc_codigo_sus']=='0301010080' OR $linha['proc_codigo_sus']=='0301010099' OR $linha['proc_codigo_sus']=='0101010010' OR $linha['proc_codigo_sus']=='0301010030' OR $linha['proc_codigo_sus']=='0301040087' OR $linha['proc_codigo_sus']=='0301060037' OR $linha['proc_codigo_sus']=='0301060045' OR $linha['proc_codigo_sus']=='0301060053' OR $linha['proc_codigo_sus']=='0301080305' OR $linha['proc_codigo_sus']=='0301100020' OR $linha['proc_codigo_sus']=='0301100039' OR $linha['proc_codigo_sus']=='03010100064' OR $linha['proc_codigo_sus']=='03010100099' OR $linha['proc_codigo_sus']=='03010100080' OR $linha['proc_codigo_sus']=='03010100030' OR $linha['proc_codigo_sus']=='030105010' OR $linha['proc_codigo_sus']=='0101030010' OR $linha['proc_codigo_sus']=='0101030029' OR $linha['proc_codigo_sus']=='0301050104' OR $linha['proc_codigo_sus']=='0301050147')) { 

            $cnes = $this->corta($linha['cnes'], 7, " ");
            if ($codCnes <> $cnes) {
                $codCnes = $cnes;
                $numFolhaBpa = 1;
                $numLinhaBpa = 1;
            }
            if ($numLinhaBpa > 20) {
                $numFolhaBpa++;
                $numLinhaBpa = 1;
            }
//
// HOSPITAL DO TIPO MEDICO            
if($linha['cnes_tp_unid_id']=='05') {
     switch($linha['usr_tipo_medico']) {
        case 'M':
            $tp_cbo = '225125';
        break;
        case 'E':
            $tp_cbo = '223505';
        break;
        case 'A':
            $tp_cbo = '322230';
        break;
        case 'D':
            $tp_cbo = '223208';
        break;
        default:
            $tp_cbo = $linha['cod_cbo'];
        break;
     }
} else {
     switch($linha['usr_tipo_medico']) {
        case 'M':
            $tp_cbo = '225142';
        break;
        case 'E':
            $tp_cbo = '223565';
        break;
        case 'A':
            $tp_cbo = '322250';
        break;
        case 'D':
            $tp_cbo = '223293';
        break;
        case 'C':
            $tp_cbo = '515105';
        break;
        default:
            $tp_cbo = $linha['cod_cbo'];
        break;
     }
}


            $numFolhaBpa = $this->corta($numFolhaBpa,3,"0");
            $numLinhaBpa = $this->corta((strlen($numLinhaBpa) == 1 ? "0".$numLinhaBpa : $numLinhaBpa),2," ");
            
            $quebra = chr(13) . chr(10);
            $cboMedico = $this->corta($tp_cbo, 6, " ");
            $proc_codigo_sus = str_replace(array('.', '-'), array('', ''), $linha['proc_codigo_sus']);
            $codProcAmbulatorial = $this->corta($proc_codigo_sus, 10, "0");
            $qtdeProc = $this->corta($linha['qtde'], 6, "0");
            $idade = $this->corta($linha['idade'], 3, "0");            
            $consolidado .=  "02".$codCnes.$dataAnoMes.$cboMedico.$numFolhaBpa.$numLinhaBpa.$codProcAmbulatorial.$idade.$qtdeProc."EXT".$quebra;

            $numLinhaBpa++;
        }
    }
        return $consolidado;
    }
    
    public function montaIndividualizadoAction($tp_financiamento=FALSE,$data=FALSE,$origem=FALSE,$origem_codigo=FALSE,$dataCompetenciaInicio=FALSE,$dataCompetenciaFim=FALSE) {
        error_reporting(E_ALL);

        $linhas05 = null;
        $linhas06 = null;
        $linhas04 = null;
        $individualizado = null;
        
        $tbConfig = new Application_Model_Configuracao();
        $tbSec = new Application_Model_Secretaria();
        $cepSec = $tbSec->getDadosSec();

      
        if($tp_financiamento == '04'){
            $linhas05 = $this->dbtable->getRegistrosIndividualizadoBpa('05',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $linhas06 = $this->dbtable->getRegistrosIndividualizadoBpa('06',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $linhas04 = $this->dbtable->getRegistrosIndividualizadoBpa('04',$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
            $linhas = array_merge($linhas06, $linhas05);
            $linhas = array_merge($linhas, $linhas04);
        }else{
            $linhas = $this->dbtable->getRegistrosIndividualizadoBpa($tp_financiamento,$data,$origem,$origem_codigo,$dataCompetenciaInicio,$dataCompetenciaFim);
        }
        
        $data = explode("/", $data);
        $dataAnoMes = $data[1].$data[0];
        $codCnes = "";
        foreach ($linhas as $linha) {


        if (empty($linha["rua_cep"])) {
            $cep_pac = $this->corta(str_replace("-", "", $cepSec->cep),8," ");
        } else {
            $st = str_replace(".","",$linha["rua_cep"]);
            $st1 = str_replace(",","",$st);
            $st2 = str_replace("-","",$st1);
            $cep_pac = $this->corta(str_replace("-", "", $st2),8," ");
        }

            $cnes = $this->corta($linha['cnes'], 7, " "); // tamanho 15
            if ($codCnes <> $cnes) {
                $codCnes = $cnes;
                $numFolhaBpa = 1;
                $numLinhaBpa = 1;
               }
            if ($numLinhaBpa > 20) {
                $numFolhaBpa++;
                $numLinhaBpa = 1;
            }
            
if(($linha['proc_codigo_sus']!=='0301010064' AND $linha['proc_codigo_sus']!=='0301010080' AND $linha['proc_codigo_sus']!=='0301010099' AND $linha['proc_codigo_sus']!=='0101010010' AND $linha['proc_codigo_sus']!=='0301010030' AND $linha['proc_codigo_sus']!=='0301040087' AND $linha['proc_codigo_sus']!=='0301060037' AND $linha['proc_codigo_sus']!=='0301060045' AND $linha['proc_codigo_sus']!=='0301060053' AND $linha['proc_codigo_sus']!=='0301080305' AND $linha['proc_codigo_sus']!=='0301100020' AND $linha['proc_codigo_sus']!=='0301100039' AND $linha['proc_codigo_sus']!=='03010100064' AND $linha['proc_codigo_sus']!=='03010100099' AND $linha['proc_codigo_sus']!=='03010100080' AND $linha['proc_codigo_sus']!=='03010100030' AND $linha['proc_codigo_sus']!=='030105010' AND $linha['proc_codigo_sus']!=='0101030010' AND $linha['proc_codigo_sus']!=='0101030029' AND $linha['proc_codigo_sus']!=='0301050104' AND $linha['proc_codigo_sus']!=='0301050147')) { 

            $numFolhaBpa = $this->corta((strlen($numFolhaBpa) == 1 ? "00".$numFolhaBpa : $numFolhaBpa),3,"0");
            $numLinhaBpa = $this->corta((strlen($numLinhaBpa) == 1 ? "0".$numLinhaBpa : $numLinhaBpa),2," ");
            $cnsMedico = $this->corta($linha['cnes_cod_cns'], 15, " "); // tamanho 15
            $cboMedico = $this->corta($linha['cod_cbo'], 6, " ");
            $dataAtendimento = $this->corta($linha['bpa_data'],8," ");   // tamanho 8 AAAAMMDD
            $proc_codigo_sus = str_replace(array('.', '-'), array('', ''), $linha['proc_codigo_sus']);
            $codProcAmbulatorial = $this->corta($proc_codigo_sus, 10, "0");
            $cnsPaciente = $this->corta($linha['usu_cartao_sus'], 15, " "); // tamanho 15
            $pacSexo = $this->corta($linha['usu_sexo'], 1, " ");
            $cid = $this->corta($linha['cd10_codigo_cid'], 4, " ");
            $idade = $this->corta($linha['idade'], 3, "0"); // tamanho 3
            $qtdeProc = $this->corta($linha['qtde'], 6, "0");
            $caracterAtendimento = $this->corta($linha['ci_codigo'], 2, "0");
            $numAutorizacaoEstabelecimento = $this->corta($linha['bpa_autorizacao'], 13, " ");
            $nomeUsuario = $this->corta($linha['usu_nome'], 30, " "); // se menor que 30, preenche com :space:
            $dataNascUsuario = str_replace('-', '', $linha['usu_datanasc']);
            $racaUsuario = ($linha['raca']  < 9 ? "0" . $linha['raca'] : "99");
            $racaUsuario = $this->corta($this->trocaRacaAction($racaUsuario),2,"0");
            if ($racaUsuario == "00") {
                $racaUsuario = 99;
            }
            /*PARTE NAO CONTEMPLADA*/
            $codServico = $this->corta("",3," ");
            $codClassificacao = $this->corta("",3," ");
            $codSeqEquipe = $this->corta("",8," ");
            $codAreaEquipe = $this->corta($linha["ds_area"],4," ");
            $cnpj = $this->corta (str_replace("/", "", $linha["cnpj"]),14," ");
            /*----*/
            /*DOMICILIO*/
            $cep = $cep_pac;

            if(empty($linha["co_tipo_logradouro"])) {
                $codLogradouro = "081";
            } else {
                $codLogradouro = $this->corta($linha["co_tipo_logradouro"],3," ");
            }
            $nomeRua = $this->corta($linha["rua_nome"],30," ");
            $complemento = $this->corta($linha["dom_complemento"],10," ");
            $numeroEnd = $this->corta($linha["dom_numero"],5," ");
            $bairro = $this->corta($linha["bai_nome"],30," ");
            $telefone = $this->corta($this->formataTelefone($linha["dom_telefone"]),11," ");
            $email = $this->corta("",40," ");
            $quebra = chr(13) . chr(10);
            
            if ($racaUsuario == "05") {
                $etniaUsuario = $this->corta($linha['etnia'], 4, "0");
            } else {
                $etniaUsuario = $this->corta("    ", 4, " ");
            }
            $nacionalidade = $this->corta("010", 3, " ");
            $ibge = (!empty($linha["ibge_dom"]))?$this->corta($linha["ibge_dom"],6," "):$this->corta($tbConfig->getConfig("CID_CODIGO_IBGE"),6," ");

            $individualizado .= "03".$codCnes . $dataAnoMes . $cnsMedico . $cboMedico . $dataAtendimento . $numFolhaBpa .
                $numLinhaBpa . $codProcAmbulatorial . $cnsPaciente . $pacSexo . $ibge . $cid . $idade .
                $qtdeProc . $caracterAtendimento . $numAutorizacaoEstabelecimento . "EXT" . $nomeUsuario .
                $dataNascUsuario  . $racaUsuario . $etniaUsuario . $nacionalidade . $codServico. $codClassificacao.$codSeqEquipe.
                $codAreaEquipe.$cnpj.$cep.$codLogradouro.$nomeRua.$complemento.$numeroEnd.$bairro.$telefone.$email.$quebra;
                //die($dataAtendimento);
            $numLinhaBpa++;

        }
}
        
        return $individualizado;
    }
    
    public function trocaRacaAction($raca) { // A TABELA DO BPA INVERTE OS CODIGOS 03 - 04 COM OS PARÂMETROS DO ESUS
        if ($raca == "03") {
            $raca = "04";
        } else if ($raca == "04") {
            $raca = "03";
        }
        return $raca;
    }
    
    public function criaArquivo($nome, $msg, $path = "./", $ext = ".xml", $modo = "w") {
        if (!is_dir($path)) {
            die($path);
            return "DIR '$path' nao existe";
        }
        $completePath = $path.$nome.$ext; 

        $open = fopen($completePath, $modo);//pode ver os parametros do fopen no php.net
        if ($open) {
            chmod($completePath, 0777);
        }
        fwrite($open, $msg);
        return fclose($open);
                
    }

    public function origem($origem,$origem_codigo) {
         if ($origem == "U") {
            $dados_origem = $this->unidade->getUnidade($origem_codigo)->toArray();
            $origem_nome = $dados_origem[0]["uni_desc"];
            $origem_cnpj = $dados_origem[0]["uni_cnpj"];
        } else if ($origem == "M") {
            $dados_origem = $this->medico->getInfoMedico($origem_codigo)->toArray();
            $origem_nome = $dados_origem["med_nome"];
            $origem_cnpj = $dados_origem["med_cnpj"];
        } else if ($origem == "") {
            $origem_nome = "";
            $origem_cnpj = "";
        }
        return array("cnpj" => $origem_cnpj,"nome"=>$origem_nome);
    }
    
    public function corta($str, $max=10, $preencherCom="0") {
        $lado = ($preencherCom === "0" ? STR_PAD_LEFT : STR_PAD_RIGHT);

        if (strlen($str) > $max) {
            $str = substr($str, 0, $max);
        }

        return str_pad($str, $max, $preencherCom, $lado);
    }
    
    public function formataTelefone($telefone) {
        $novoTelefone='';
        for ($index = 0; $index < strlen($telefone); $index++) {
            $arrayNumero = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
            if (in_array($telefone[$index], $arrayNumero)) {
                $novoTelefone .= $telefone[$index];
            }
        }
        return $novoTelefone;
    }
    
    public function exportacaoAction() {
       // $this->view->title = "Exportação E-SUS";
    }
   
    public function lerArquivoAction() {
        // O nome original do arquivo no computador do usuário
        $arqName = $_FILES['arquivo']['name'];
        // O código de erro associado a este upload de arquivo
        $arqError = $_FILES['arquivo']['error'];
        if ($arqError == 0) {
            $pasta = $_SESSION["root"]."WebSocialSaude/e-sus/inconsistencias/";
            $arquivo = $_SESSION["root"]."WebSocialSaude/e-sus/inconsistencias/".$arqName;
        }
        
        $zip = new ZipArchive;
        $zip->open($arquivo);
        $zip->extractTo($pasta);
        $zip->close();
        unlink($arquivo);
        $this->importaInconsistencias();
        return $this->render("index");
    }
    
    public function importaInconsistencias($arquivo=FALSE) {
        $arquivo = $_SESSION["root"]."WebSocialSaude/e-sus/inconsistencias/importacao_cidadao.resultado";
        $lines = file($arquivo);
        $tbEsus = new Application_Model_EsusImportacaoResultado();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try {
            foreach($lines as $line) {
                $rest = substr($line, 23, -2); // remove os lixos da string
                list($cidadao,$cns,$cpf,$nome,$dtNascimento,$nomeMae,$mensagem) = explode(",", $rest);
                list($ind,$val) = explode("=", $cidadao);
                list($ind_cns,$val_cns) = explode("=", $cns);
                list($ind_cpf,$val_cpf) = explode("=", $cpf);
                list($ind_nome,$val_nome) = explode("=", $nome);
                list($ind_dt,$val_dt) = explode("=", $dtNascimento);
                list($ind_mae,$val_mae) = explode("=", $nomeMae);
                list($ind_msg,$val_msg) = explode("=", $mensagem);
                $mensagemOk = explode(":", $val_msg);

                if (count($tbEsus->verificaEsus($val_nome,$mensagemOk[1])->toArray())==0) {
                    $dados = array(
                        "eir_cod_cidadao_esus" => $val,
                        "eir_cns" => $val_cns,
                        "eir_cpf" => $val_cpf,
                        "eir_nome" => $val_nome,
                        "eir_data_nascimento" => $val_dt,
                        "eir_nome_mae" => $val_mae,
                        "eir_mensagem" => $mensagemOk[1]
                    );
                    $tbEsus->salvarTeste($dados);
                }
            }
            Zend_Db_Table::getDefaultAdapter()->commit();
            $this->_redirect("programasfederais/esus/importacao-resultado");
        } catch (Exception $exc) {
            Zend_Db_Table::getDefaultAdapter()->rollBack();
            $this->view->dados = $exc->getMessage();
            return $this->render("dados",NULL,TRUE);
        }
    }
    
    public function importacaoResultadoAction() {
        $tbImr = new Application_Model_EsusImportacaoResultado();
        $this->view->dados = $tbImr->listar();
    }
    
    public function buscaImportacaoResultadoAction() {
        $status = $this->_request->getPost("status");
        $tbImr = new Application_Model_EsusImportacaoResultado();
        $this->view->dados = $tbImr->listar($status);
        return $this->render("importacao-resultado");
    }
    
    public function alteraStatusImportacaoAction() {
        $tbEir = new Application_Model_EsusImportacaoResultado();
        $eir_codigo = $this->_request->getPost("eir_codigo");
        $dados = array("eir_status_correcao" => 'T');
        $tbEir->atualizaStatusImp($dados, $eir_codigo);
        $this->view->dados = "";
        return $this->render("dados",NULL,TRUE);
    }
    
    public function importacaoDomicilioAction() {
        $this->view->title = "Importação de domicílios E-SUS";
    }
    
    public function historicoExportacaoAction() {
        $tbEeh = new Application_Model_EsusExportacaoHistorico();
        $this->view->itens = $tbEeh->ultimasExportacoes();
    }
    
    public function buscarAction() {
        if ($this->_request->isPost()) {
            $tbMov = new Application_Model_Movimento();
            $this->view->busca = $this->_request->getPost("busca");
            $this->view->mov_tipo = $this->_request->getPost("mov_tipo");
            $this->view->itens = $tbMov->getMovimentos(NULL,$this->view->busca,$this->view->mov_tipo);
            $this->render("index");
        } else {
            $this->_redirect("/materiais/controle-movimentos/index");
        }
    }

    public function salvarAction(){
        // error_reporting(E_ALL);
        $dados = $_POST;
        
        $procedimentos = $dados['procedimento'];
        $funcoes = new Application_Model_Funcoes();
        $tbEsp = new Application_Model_Especialidade();
        $tbMed = new Application_Model_Medico();
        $tbMedEsp = new Application_Model_MedicoEspecialidade();

        if(isset($dados['bpa_data'])){
            $data = $dados['bpa_data'];
        }

        if(isset($dados['data_atendimento'])){
            $data = $dados['data_atendimento'];
        }
        
        unset($_POST);
        unset($dados['procedimento']);
        unset($dados['pac']);
        unset($dados['data_atendimento']);
        unset($dados['proc_nome']);
        unset($dados['proc_codigo_sus']);
        unset($dados['cid']);
        unset($dados['co_seq_ciap']);
        unset($dados['conf_ciap']);
        unset($dados['ciap-selecionados']);
        
        $dados['uni_codigo'] = $_SESSION['uni_codigo'];
        
        $dados['usu_codigo'] = isset($dados['usu_codigo']) ? $dados['usu_codigo'] : 0;
        $dados['bpa_data'] = $funcoes->converteData($data);
        $dados['bpa_origem'] = "listagem_procedimentos";
        $dados['bpa_origem_codigo'] = 0;
        $dados['esp_codigo'] = $_SESSION['logon']['usr']->esp_codigo;
        
        $uuid = round(microtime(true) * 1000);

        $dados['tipo_insercao'] = "M";
        $dados['uuid'] = $uuid;
        
        // echo "<pre>"; print_r($dados); die();
        $tbBPA = new Application_Model_DbTable_BPA();

        if(isset($dados['quantidade'])){
            $result = [];
            $quantidade = $dados['quantidade'];
            unset($dados['quantidade']);

            for($i = 0;$i < $quantidade; $i++){
                $result = $tbBPA->salvar($dados);
            }
        } else {
            $result = $tbBPA->salvar($dados);
        }
        
        // print_r($result);
        
        if($result){
            echo $result;
        } else {
            echo json_encode(error_get_last());
        }

        exit;

    }
    
    public function indexAction() {
        $this->view->title = "BPA";
        
        $TbConfig = new Application_Model_Configuracao();
        $data_competencia = $TbConfig->getConfig('DATA_COMPETENCIA');


        $dataFormatada = date("m/Y", strtotime($data_competencia));
        $this->view->dataCompetencia = $dataFormatada;
        
        $TbBpa = new Application_Model_BPA();
        $prestadorUnidade = $TbBpa->buscaPrestadorParaBpa();
        $this->view->listaUnidade = $prestadorUnidade;
        
        $tipoFinancimaneto = $TbBpa->buscaTipoFinanciamento();
        $this->view->listaFinanciamento = $tipoFinancimaneto;
        // die($tipoFinancimaneto);
        $TbMed = new Application_Model_Medico();
        $prestadorMedico = $TbMed->buscaPrestadorParaBpa();
        $this->view->listaPrestador = $prestadorMedico;

    }

    public function listagemAction(){
        $this->view->title = "Listagem e edição BPA";

        // print_r($_GET);
        //$competenciaMes = $this->_getParam("mes", FALSE) || ''.date('M').'';
        //$competenciaAno = $this->_getParam("ano", FALSE) || ''.date('Y').'';
        $competenciaMes = isset($_GET['mes']) ? $_GET['mes'] : date('m')-1;
        $competenciaAno = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

        $competenciaMes = explode("?", $competenciaMes)[0];

        //print_r(strstr($competenciaMes, "?"));
        
        // echo $competenciaAno, $competenciaMes;
        
        $tbBPA = new Application_Model_BPA();
        $tbUni = new Application_Model_Unidade();
        $tbUniUsu = new Application_Model_UnidadeUsuarios();
        $tbBPA = new Application_Model_BPA();

        $cnes = $tbUni->getUnidade($_SESSION['uni_codigo']);

        $result['profissionais'] = $tbUniUsu->getProfissionaisUnidade($cnes[0]['uni_cnes']);

        $result['unidade'] = $cnes[0];
        if($competenciaMes != "" && $competenciaAno != ""){
            $result['dados'] = $tbBPA->listagemBPA($competenciaMes, $competenciaAno,$_SESSION['uni_codigo']);
        }

        // echo "<pre>";

        // print_r($result['CNES']);
        // die();

        $this->view->anos = $tbBPA->getAnos();

        $this->view->dadosListagem = $result;
        $this->render("listagem");
    }

    public function getTableAction(){
        $dados = (object) $this->_getParam("dados", FALSE);

        $tbBPA = new Application_Model_BPA();
        $dados = $tbBPA->listagemBPA($dados->profissional, $dados->folha);

        foreach ($dados as $valor) {
            echo "<tr>
                <td>{$valor['proc_nome']}</td>
                <td>{$valor['proc_codigo_sus']}</td>
                <td>{$valor['quantidade']}</td>
                <td>{$valor['cod_cbo']}</td>
            </tr>";
        }

        exit(0);
    }

    public function deletarProcedimentoAction(){
        error_reporting(E_ALL);
        $codigo = $this->_getParam("_id", false);

        // die($codigo);

        $row = $this->dbtable->getRegistroUUIDBPA($codigo)->toArray();

        $retorno = $this->dbtable->deletarRegistros($row);

        if($retorno){
            echo json_encode(array('result'=>'ok'));
        } else {
            echo json_encode(array('result'=>'error', 'error'=>error_get_last()));
        }
        
        exit(0);
        
    }
}

?>