<?php
//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;
class LexsignExtensions_Application_Resource_Resourceinjector
	extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
    	$this->getBootstrap()->bootstrap('Log');

    	\Zend_Controller_Action_HelperBroker::addHelper(
            new \LexsignExtensions_Controller_Action_Helper_ResourceInjector()
        );
    }
}