<?php
Zend_Loader::loadClass("Elotech_Db_Table_Abstract");
class Application_Model_UploadArquivo extends Elotech_Db_Table_Abstract {

    protected $_name = 'upload_arquivo';
    protected $_primary = 'upl_codigo';   

    public function getArquivosPorUsuario($usu_codigo=FALSE){
        $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("upl"=>"upload_arquivo"),array("upl_codigo","upl_data"))
                            ->join(array("r" => "requisicao_exames"),"upl.req_codigo=r.req_codigo",array("req_codigo","dt_requisicao","req_observacao","usu_codigo"))
                            ->join(array("p" => "procedimento"), "p.proc_codigo=r.proc_codigo", array("proc_codigo", "proc_nome"))
                            ->where("usu_codigo=?",$usu_codigo)
                            ->where("req_encaminhamento='t'")
                            ->order("proc_nome");

        //die($where);
        return $this->fetchAll($where);
    }
    
    public function getArquivosPorRequisicao($req_codigo=FALSE){
        $where = $this->select(FALSE)
                            ->setIntegrityCheck(FALSE)
                            ->from(array("upl"=>"upload_arquivo"))
                            ->join(array("r" => "requisicao_exames"),"upl.req_codigo=r.req_codigo",array("req_codigo","dt_requisicao","req_observacao","usu_codigo"))
                            ->join(array("p" => "procedimento"), "p.proc_codigo=r.proc_codigo", array("proc_codigo", "proc_nome"))
                            ->where("upl.req_codigo=?",$req_codigo)
                            ->where("req_encaminhamento='t'")
                            ->order("proc_nome");

        
        return $this->fetchAll($where);
    }
    
    public function extrairArquivosBanco($req_codigo){
        if(empty($req_codigo))
            return false;
        
        $caminho = $_SESSION[root].$_SESSION[modulo]."raiox/server/php/files/";
        $where = $this->select()
                      ->setIntegrityCheck(FALSE)
                      ->from(array("upl"=>"upload_arquivo"),"lo_export(upl_arquivo,'$caminho'||upl_codigo||'-'||upl_arquivo_nome),upl_codigo||'-'||upl_arquivo_nome as arquivos")
                      ->where("req_codigo=$req_codigo");
        return $this->fetchAll($where);
    }
    
    public function geraThumbs($arquivos=FALSE,$tamanhox=FALSE,$tamanhoy=FALSE,$pasta=FALSE){
            
        foreach($arquivos as $arquivo){
            $caminnhoFotoOriginal = $_SESSION[root].$_SESSION[modulo]."raiox\\server\\php\\files\\$arquivo->arquivos";
            $caminnhoFotoOriginal = str_replace("/", "\\", $caminnhoFotoOriginal);
            
            $caminnhoFotoMiniatura = $_SESSION[root].$_SESSION[modulo]."raiox\\server\\php\\files\\$pasta\\$arquivo->arquivos";
            $caminnhoFotoMiniatura = str_replace("/", "\\", $caminnhoFotoMiniatura);
            $formato = explode(".",$arquivo->arquivos);
            if(in_array("jpg",$formato) || in_array("jpeg",$formato)){
                $imagemOriginal = imagecreatefromjpeg( $caminnhoFotoOriginal );
            }else if(in_array("png",$formato)){
                $imagemOriginal = imagecreatefrompng( $caminnhoFotoOriginal );
            }else if(in_array("gif",$formato)){
                $imagemOriginal = imagecreatefromgif( $caminnhoFotoOriginal );
            }
            $larguraOriginal = imagesx( $imagemOriginal );
            $alturaOriginal = imagesy( $imagemOriginal );

            $imagemMiniatura = imagecreatetruecolor($tamanhox, $tamanhoy);
            imagecopyresampled($imagemMiniatura, $imagemOriginal, 0, 0, 0, 0, $tamanhox, $tamanhoy, $larguraOriginal, $alturaOriginal);
            if(in_array("jpg",$formato) || in_array("jpeg",$formato)){
                imagejpeg( $imagemMiniatura, $caminnhoFotoMiniatura);
            }else if(in_array("png",$formato)){                    
                imagepng( $imagemMiniatura, $caminnhoFotoMiniatura);
            }else if(in_array("gif",$formato)){
                imagegif( $imagemMiniatura, $caminnhoFotoMiniatura);
            }
        }               
    }
        
    public function limpaDir($diretorios=FALSE){
        $caminho = $_SESSION[root].$_SESSION[modulo]."raiox\\server\\php\\files";
        $caminho = str_replace("/", "\\", $caminho);
        $handle = opendir($caminho);
        $itensNaoPermitidos = array(".","..",".svn","small","temp","thumbnail");
        while(($file = readdir($handle)) !== false){
            if(!in_array($file,$itensNaoPermitidos)){
                unlink($caminho."\\".$file);
            }
        }

        foreach($diretorios as $diretorio){
            $handle = opendir($caminho."\\".$diretorio);
            while(($file = readdir($handle)) !== false){
                if(!in_array($file,$itensNaoPermitidos)){
                    unlink($caminho."\\".$diretorio."\\".$file);
                }
            }
        }
    }

    public function geraThumbsNovo($recebeNomeImagem=FALSE){
        session_start();
        $recebeNomeImagem =  $recebeNomeImagem;
        $caminho = $_SERVER['HTTP_HOST'].$_SESSION[CONTEXT_DOCUMENT_ROOT].'/WebSocialUpload/centroRegulador/'.$recebeNomeImagem;

        return $caminho;
    }
    
}

