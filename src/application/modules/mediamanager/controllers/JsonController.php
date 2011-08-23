<?php

class Mediamanager_JsonController extends Zend_Controller_Action
{

    public function preDispatch()
    {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
        parent::preDispatch();
    }

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


}

