<?php

class Elotech_Controller_Plugin_MaisAcessados extends Zend_Controller_Plugin_Abstract {

    private $item;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();

        $tbMA = new Application_Model_MaisAcessados();
        $this->item = $tbMA->maisUm($module, $controller);
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        if (empty($this->item->ma_title) && $this->item->ma_contador == 1) {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $viewRenderer->init();
            $this->item->ma_title = $viewRenderer->view->title;
            $this->item->save();
        }
    }

}

