<?php

class ProgramasFederais_SiPniController extends Zend_Controller_Action {
    
    
    public function init() {
        //session_start();
        $this->_helper->acl->allow(NULL,array('index'));
        $this->view->title = "SIPNI";
        $this->unidade = new Application_Model_Unidade();
      
        
    }
    
    public function indexAction() {
        $this->view->listaUnidade = $this->unidade->getUnidades();
 
    //    $dataFormatada = date("m/Y", strtotime($data_competencia));
//        $this->view->dataCompetencia = $dataFormatada;
    }
    public function geraSipniAction() {
        $uni_codigo = $this->_getParam("uni_codigo", FALSE);
        $competencia = $this->_getParam("competencia", FALSE);
        
        $cnes = $this->unidade->getUnidade($this->_getParam("uni_codigo", FALSE))->toarray();
        $cnes = $cnes[0][uni_cnes];
//        echo "<pre>". $cnes;die;
//        var_dump($cnes);die;
//        die($cnes);
//       $dadosImportacao =  $this->dadosImportacao($cnes);
       $dadosVacinados  =  $this->dadosVacinados($cnes,$uni_codigo,$competencia);
       $zip = new ZipArchive();
       if($zip->open("M-".$cnes.$competencia.time().'2', ZIPARCHIVE::CREATE) == TRUE){
        $zip->addFile($diretorio.'arquivo1.txt','arquivo1.txt');
        $zip->addFile($diretorio.'arquivo2.txt','arquivo2.txt');
       }
 
    //    $dataFormatada = date("m/Y", strtotime($data_competencia));
//        $this->view->dataCompetencia = $dataFormatada;
    }
    public function dadosImportacao($cnes=false){
//        var_dump($cnes);die;
        $TbConfig = new Application_Model_Configuracao();
        $sigla = $TbConfig->getConfig('SIGLA_SISTEMA');
        $versao = $TbConfig->getConfig('VERSAO_SAUDE');
        
        $path = $_SESSION["root"].$_SESSION["modulo"]."zf/public/arqs/";
        $msg = "\"$sigla\",\"$versao\",\"$versao\"";
//        "H".$cnes
//        var_dump($_SESSION);
//        die($path);
//       var_dump("H".$cnes, $msg, $path, ".DAT");die;
       return $this->criaArquivo("H".$cnes, $msg, $path, ".DAT");
//        die($msg);
       
        
    }
    public function dadosVacinados($cnes=false,$uni_codigo,$competencia){
        $TbConfig = new Application_Model_Configuracao();
        $tbvac = new Application_Model_VacinaUsuario();
        
        $codigo_sistema = $TbConfig->getConfig('CODIGO_DO_SISTEMA');
        $codigo_ibge = $TbConfig->getConfig('CID_CODIGO_IBGE');
        
        $sql = $tbvac->dadosVacinadosSiPni($codigo_sistema,$uni_codigo,$competencia,$codigo_ibge)->toarray();
        $path = $_SESSION["root"].$_SESSION["modulo"]."zf/public/arqs/";

       return $this->criaArquivo("P".$cnes, '', $path, ".DAT",'w',$sql);       
        
    }
    public function dadosRegistroVacinacao($cnes=false,$uni_codigo,$competencia){
        $TbConfig = new Application_Model_Configuracao();
        $tbvac = new Application_Model_VacinaUsuario();
        
        $codigo_sistema = $TbConfig->getConfig('CODIGO_DO_SISTEMA');
        $codigo_ibge = $TbConfig->getConfig('CID_CODIGO_IBGE');
        
        $sql = $tbvac->dadosRegistroVacinacaoSiPni($codigo_sistema,$uni_codigo,$competencia,$codigo_ibge)->toarray();
        $path = $_SESSION["root"].$_SESSION["modulo"]."zf/public/arqs/";

       return $this->criaArquivo("P".$cnes, '', $path, ".DAT",'w',$sql);       
        
    }
    public function criaArquivo($nome, $msg, $path = "./", $ext = ".xml", $modo = "w",$sql = false) {

        if (!is_dir($path)) {
//            die($path);
            return "DIR '$path' nao existe";
        }
        $completePath = $path.$nome.$ext; 

        $open = fopen($completePath, $modo);//pode ver os parametros do fopen no php.net
        if ($open) {
            chmod($completePath, 0777);
        }
        
        if($msg){
            fwrite($open, $msg);
        }
        foreach ($sql as $row) {         
            $string = $row['codigo_sistema'].",".$row['usu_codigo'].",".$row['uni_cnes'].",".$row['usu_cartao_sus'].",".$row['usu_nome'].",".$row['usu_mae'].",".$row['usu_sexo'].",".$row['usu_datanasc'].",".$row['rac_codigo'].",".$row['zona'].",".$row['pais_codigo'].",".$row['cid_codigo_ibge']."\n";
            fwrite($open,$string);
         }
    
        return fclose($open);
                
    }
     public function downloadArquivo($path,$nomeDoArquivo ){
        $link = $path.$nomeDoArquivo;
        header("Content-Disposition: attachment; filename=".$nomeDoArquivo."");
        header("Content-Type: application/plain");
        readfile($link);        
    
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
}

?>