<?php
/**
 * Description of Sidebar
 *
 * @author akentner
 */
class LexsignExtensions_Controller_Plugin_Sidebar extends Zend_Controller_Plugin_Abstract {

    public function  postDispatch(Zend_Controller_Request_Abstract $request) {
        $layout = \Zend_Controller_Front::getInstance()->getParam("bootstrap")->getResource('layout');

        if (!$layout->isEnabled()) {
            return;
        }

        $view = Zend_Layout::getMvcInstance()->getView();
        $view->setScriptPath(APPLICATION_PATH . "/layouts/");
        $view->request = $request;
        $rendered = $view->render('sidebar.phtml');
        $this->getResponse()->setBody($rendered, 'sidebar');
    }

}
