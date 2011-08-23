<?php
/**
 * Description of Javascript
 *
 * @author akentner
 */
class LexsignExtensions_Controller_Plugin_Javascript extends Zend_Controller_Plugin_Abstract {
    public function  postDispatch(Zend_Controller_Request_Abstract $request) {
        $layout = \Zend_Controller_Front::getInstance()->getParam("bootstrap")->getResource('layout');

        if (!$layout->isEnabled()) {
            return;
        }

        $view = Zend_Layout::getMvcInstance()->getView();
        $view->setScriptPath(APPLICATION_PATH . "/layouts/");
        $view->request = $request;
        $rendered = $view->render('javascript.phtml');
        $this->getResponse()->setBody($rendered, 'javascript');
    }

}
