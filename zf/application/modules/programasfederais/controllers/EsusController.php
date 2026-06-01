<?php

class ProgramasFederais_EsusController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "E-SUS Inconsistências";
    }
    
    public function indexAction() {
    
    }
    
    public function exportacaoAction(){
        $this->view->title = "Exportação E-SUS";
    }
    
    public function manualUtilizacaoAction(){
        $this->view->title = "Manual de utilização do sistema E-SUS";
    }
    
    public function manualExportacaoAction(){
        $this->view->title = "Manual de exportação E-SUS";
    }
    
    public function lerArquivoAction() {
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
            $pasta = $_SESSION["root"]."WebSocialSaude/e-sus/inconsistencias/";
            $arquivo = $_SESSION["root"]."WebSocialSaude/e-sus/inconsistencias/".$arqName;
            $upload = move_uploaded_file($arqTemp, $pasta . $arqName);
        }
        
        $zip = new ZipArchive;
        $zip->open($arquivo);
        $zip->extractTo($pasta);
        $zip->close();
        unlink($arquivo);
        $this->importaInconsistencias();
        return $this->render("index");
    }
    
    public function importaInconsistencias($arquivo=FALSE){
        $arquivo = $_SESSION["root"]."WebSocialSaude/e-sus/inconsistencias/importacao_cidadao.resultado";
        $lines = file($arquivo);
        $tbEsus = new Application_Model_EsusImportacaoResultado();
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        try{
            foreach($lines as $line) {
                $rest = substr($line, 23, -2); // remove os lixos da string
                list($cidadao,$cns,$cpf,$nome,$dtNascimento,$nomeMae,$mensagem) = explode(",", $rest);
                //echo $cidadao."-".$cns."-".$cpf."-".$nome."-".$dtNascimento."-".$nomeMae."-".$mensagem."<br/>";
                list($ind,$val) = explode("=", $cidadao);
                list($ind_cns,$val_cns) = explode("=", $cns);
                list($ind_cpf,$val_cpf) = explode("=", $cpf);
                list($ind_nome,$val_nome) = explode("=", $nome);
                list($ind_dt,$val_dt) = explode("=", $dtNascimento);
                list($ind_mae,$val_mae) = explode("=", $nomeMae);
                list($ind_msg,$val_msg) = explode("=", $mensagem);
                $mensagemOk = explode(":", $val_msg);

                if (count($tbEsus->verificaEsus($val_nome,$mensagemOk[1])->toArray())==0){
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
    
    public function importacaoResultadoAction(){
        $tbImr = new Application_Model_EsusImportacaoResultado();
        $this->view->dados = $tbImr->listar();
    }
    
    public function buscaImportacaoResultadoAction(){
        $status = $this->_request->getPost("status");
        $tbImr = new Application_Model_EsusImportacaoResultado();
        $this->view->dados = $tbImr->listar($status);
        return $this->render("importacao-resultado");
    }
    
    public function alteraStatusImportacaoAction(){
        $tbEir = new Application_Model_EsusImportacaoResultado();
        $eir_codigo = $this->_request->getPost("eir_codigo");
        $dados = array("eir_status_correcao" => 'T');
        $tbEir->atualizaStatusImp($dados, $eir_codigo);
        $this->view->dados = "";
        return $this->render("dados",NULL,TRUE);
    }
    
    public function importacaoDomicilioAction(){
        $this->view->title = "Importação de domicílios E-SUS";
    }
    
    public function historicoExportacaoAction(){
        $this->view->title = "E-SUS Histórico";
        $tbEeh = new Application_Model_EsusExportacaoHistorico();
        $this->view->itens = $tbEeh->ultimasExportacoes();
    }
    
     public function buscarAction(){
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
    
    public function estornoEsusAction(){
            
            $tbEehi = new Application_Model_EsusExportacaoHistoricoItens();
            
            $eeh_codigo = $this->_getParam("eeh_codigo");
            //die($eeh_codigo);
            
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            
            $resultado = $tbEehi->todasExportacoesDoTipoFicha($eeh_codigo);
            
            try{
                
            //retorna lista dos UUIDs dos historico itens, para removelos pelos uuids de seuas respectivas tabelas
            $resultado = $tbEehi->todasExportacoesDoTipoFicha($eeh_codigo);
            
            //Passa por todo o array para pegar linha por linha ate terminar
            
            foreach ($resultado as $row){
                
                //case verifica o tipo da ficha e chama o model correspondente

                switch ($row['tfe_codigo']){
                    case 1:
                        
                        $tbEci = new Application_Model_EsusCadastroIndividual();
                        $tbEci->anularCampoUuidPeloUuid($row['uuid_ficha']);
                           
                        break;

                    case 2:
                        
                        $tbtCdr = new Application_Model_TbCdsDomicilioResposta();
                        $tbtCdr->anularCampoUuidPeloUuid($row['uuid_ficha']);
                        
                        break;
                    
                    case 3:
                        
                        $tbEfp= new Application_Model_EsusFichaProcedimento();
                        $tbEfp->anularCampoUuidPeloUuid($row['uuid_ficha']);
                        
                        break;
                    
                    case 4:
                        
                        $tbEai = new Application_Model_EsusAtendimentoIndividual();
                        $tbEai->anularCampoUuidPeloUuid($row['uuid_ficha']);
                        
                        break;
                    
                    case 5:
                        
                        $tbEvd = new Application_Model_EsusVisitaDomiciliar();
                        $tbEvd->anularCampoUuidPeloUuid($row['uuid_ficha']);
                        
                        break;
                    
                    case 6:
                        
                        $tbEac = new Application_Model_EsusAtividadeColetiva();
                        $tbEac->anularCampoUuidPeloUuid($row['uuid_ficha']);
                        
                        break;
                    
                    case 7:
                        
                        $tbEo = new Application_Model_EsusOdonto();
                        $tbEo->anularCampoUuidPeloUuid($row['uuid']);
                        
                        break;
                    
                    default:
                        break;
                }
                    
            }
            
            $tbEehi->deletarTodosItensDoHistorico($eeh_codigo);
            // Realizando os comandos sql se não occoreu problemas
            Zend_Db_Table::getDefaultAdapter()->commit();
            
            } catch (Exception $exc) {
                Zend_Db_Table::getDefaultAdapter()->rollBack();
                
                //return $this->render("",NULL,TRUE);
            } 
            
            //$this->view->dados = "valor";
            return $this->render("dados",null,true);
            
            
            
    }
}

?>