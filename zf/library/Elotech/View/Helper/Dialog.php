<?php

class Zend_View_Helper_Dialog {

    protected $_view;

    public function setView($view) {
        $this->_view = $view;
    }

    public function dialog() {
        $result = '';
        if (!empty($this->_view->dialog)) {
			list($title, $msg, $width, $height) = $this->_view->dialog;
			$result .= "<div class=\"auto-dialog\" title=\"$title\" style=\"width:{$width}px;height:{$height}px;display:none\">$msg</div>";			
        }
        echo $result;
    }

} 