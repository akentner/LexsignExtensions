<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_ModuleLoader 
	extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $this->getBootstrap()->bootstrap('Autoloader');
        $this->getBootstrap()->bootstrap('Frontcontroller');
        
        /* @var $log Zend_Log */
        $log = Zend_Registry::get('log');
        $log->log(__METHOD__, 8);
        
        $fc = $this->getBootstrap()->getResource('Frontcontroller');
        $modules = $fc->getControllerDirectory();

        foreach ($modules as $module => $dir) {
            $moduleName = strtolower($module);
            $moduleName = str_replace(array('-', '.'), ' ', $moduleName);
            $moduleName = ucwords($moduleName);
            $moduleName = str_replace(' ', '', $moduleName);
            
            $log->log('-- init Module: '.$moduleName, 8);
            
            $loader = new Zend_Application_Module_Autoloader(
            	array(
            		'namespace' => $moduleName, 
            		'basePath' => realpath($dir . "/../")
            	)
            );
            $loader->addResourceType('form', 'forms', 'Form')
                   ->addResourceType('model', 'models', 'Model');
        }
        
        return true;
        
    }
}