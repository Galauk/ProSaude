<?php

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * ACL integration
 *
 * Places_Controller_Action_Helper_Acl provides ACL support to a
 * controller.
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @package    Controller
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2007,2008 Rob Allen
 * @license    http://framework.zend.com/license/new-bsd  New BSD License
 */
class Elotech_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract {

    /**
     * @var Zend_Controller_Action
     */
    protected $_action;
    /**
     * @var Zend_Auth
     */
    protected $_auth;
    /**
     * @var Zend_Acl
     */
    protected $_acl;
    /**
     * @var string
     */
    protected $_controllerName;
	
	protected $_copiarURL = FALSE;
	

    /**
     * Constructor
     *
     * Optionally set view object and options.
     *
     * @param  Zend_View_Interface $view
     * @param  array $options
     * @return void
     */
    public function __construct(Zend_View_Interface $view = null, array $options = array()) {
        $this->_auth = Zend_Auth::getInstance();
        $this->_acl = $options['acl'];
    }

    /**
     * Hook into action controller initialization
     *
     * @return void
     */
    public function init() {
        $this->_action = $this->getActionController();

        // add resource for this controller
        $controller = $this->_action->getRequest()->getControllerName();
        if (!$this->_acl->has($controller)) {
            $this->_acl->add(new Zend_Acl_Resource($controller));
        }
    }

	/**
	 * Copia as permissões de outra url
	 * @param string $url 
	 */
	public function copiarPermissao($url){
		$this->_copiarURL = $url;
	}
	
    /**
     * Hook into action controller preDispatch() workflow
     *
     * @return void
     */
    public function preDispatch() {
		//return;
        $request = $this->_action->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $modulo = $request->getModuleName();
        
        $resource = $controller;
        $privilege = $action;

        if (!$this->_acl->has($resource)) {
            $resource = null;
        }
        // o controller não possui exceção?

		if (!$this->_acl->isAllowed($role, $resource, $privilege)) {
			// verifica na tabela de permissão

			$tbPerus = new Application_Model_UsuariosPermissoes();
			$urls = array("zf/$modulo/$controller");
			if($this->_copiarURL){
				$urls []= $this->_copiarURL;
			}
			$permissoes = $tbPerus->getPermissoes($urls);
			if(!$permissoes || $permissoes->acessar != 'S'){
				// usuário sem permissão
				//if( APPLICATION_ENV == "development"){
					Zend_Registry::get("logger")->log("Esse usuário não tem permissão para acessar essa url (zf/$modulo/$controller)", Zend_Log::WARN);
				//} else {
					
					$request->setModuleName('default');
					$request->setControllerName('login');
					$request->setActionName('restrito');
					$request->setDispatched(false);
				//}
			}
		}
	}

    /**
     * Proxy to the underlying Zend_Acl's allow()
     *
     * We use the controller's name as the resource and the
     * action name(s) as the privilege(s)
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  string|array                             $actions
     * @uses   Zend_Acl::setRule()
     * @return Places_Controller_Action_Helper_Acl Provides a fluent interface
     */
    public function allow($roles = null, $actions = null) {
        $resource = $this->_controllerName;
        $this->_acl->allow($roles, $resource, $actions);
        return $this;
    }

    /**
     * Proxy to the underlying Zend_Acl's deny()
     *
     * We use the controller's name as the resource and the
     * action name(s) as the privilege(s)
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  string|array                             $actions
     * @uses   Zend_Acl::setRule()
     * @return Places_Controller_Action_Helper_Acl Provides a fluent interface
     */
    public function deny($roles = null, $actions = null) {
        $resource = $this->_controllerName;
        $this->_acl->deny($roles, $resource, $actions);
        return $this;
    }

}