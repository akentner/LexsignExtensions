<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * The container
     * @var Wildkat\Application\DoctrineContainer
     */
    protected $_container;

    /**
     * The document manager
     * @var Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $_dm;

    /**
     * There are more than one way to set up the document manager. The simplest
     * is to place the following code in the controllers' init method
     *
     * @return null
     */
    public function init()
    {
        $this->_container = Zend_Registry::get('Wildkat\DoctrineContainer');
        $this->_dm        = $this->_container->getDocumentManager('default');
    }

    public function indexAction()
    {

    }


}

