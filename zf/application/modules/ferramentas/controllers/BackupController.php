<?php

class Ferramentas_BackupController extends Zend_Controller_Action {
    
    public function init(){
        $this->view->title = "Ferramenta de Backup do Banco de Dados";
    }
    
    public function indexAction(){
    }

    public function editarBackupAction(){

    }

    public function salvarBackupAction(){
        
    }

    public function backupsAjaxAction(){
        $dir    = '../backup/';
        $files1 = scandir($dir);
        //die(var_dump($files1));

        $this->view->dados = $files1;

        return $this->render("dados", NULL, TRUE);
    }

    public function deletarAction(){
        // editar
        //     $bk_nome = $this->_request->getParam("bk_nome");
        //     $bk_nome_bk = $this->_request->getParam("bk_nome_bk");
        //     rename("../zf/public/backup-bd/".$bk_nome_bk , "../zf/public/backup-bd/".$bk_nome);

        //     return $this->_redirect("/ferramentas/backup/index");
        //die("here");
        $bk_nome = $this->_request->getParam("bk_nome");
        unlink('../backup/'.$bk_nome);

        return $this->_redirect("/ferramentas/backup/index");
    }

    public function salvarAction(){

    }

}