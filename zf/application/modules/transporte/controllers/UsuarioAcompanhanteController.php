<?php
class Transporte_UsuarioAcompanhanteController extends Zend_Controller_Action {
  
   
    public function getAcompanhanteAction() {
        $this->_helper->layout->disableLayout();
         
        $tbAcom = new Application_Model_UsuarioAcompanhante();
        $viausu_codigo = $this->_getParam("viausu_codigo",FALSE);
       
        $this->view->dados =$tbAcom->getAcompanhantes($viausu_codigo)->toArray();
        
       
        //echo "<pre>".  print_r($this->view->dados,1);die();
        $this->render("dados");
       
        
    }



}
?>
