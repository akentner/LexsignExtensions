<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Autoloader extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * Register namespace Application_
     * @return Zend_Application_Module_Autoloader
     */
    public function init()
    {
    	$this->getBootstrap()->bootstrap('Log');
    	
    	$log = Zend_Registry::get('log');
        $log->log(__METHOD__, 8);
    	
        $autoloader = new Zend_Application_Module_Autoloader(
        	array(
        		'namespace' => 'Application', 
        		'basePath' => dirname(__FILE__)
        	)
        );
        return $autoloader;
    }
}