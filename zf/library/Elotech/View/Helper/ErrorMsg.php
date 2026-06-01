<?php

class Zend_View_Helper_ErrorMsg {

    protected $_view;

    public function setView($view) {
        $this->_view = $view;
    }

    public function errorMsg($class = 'ui-state-highlight erro', $id='action-errors', $return=false) {
        $result = '';

        if (!empty($this->_view->erro)) {
            $result .= '<!-- ERROS: --><div class="' . $class . '" id="' . $id . '">' . PHP_EOL;
            foreach ((array) $this->_view->erro as $error) {
                $result .= $error . '<br />' . PHP_EOL;
            }
            $result .= '</div><!-- :ERROS -->' . PHP_EOL;
        }

        if($return)
            return $result;
        
        echo $result;
    }

} 