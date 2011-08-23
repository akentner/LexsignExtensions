<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Moduleloader
    extends \Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Log');
        $bootstrap->bootstrap('Autoloader');
        $bootstrap->bootstrap('Frontcontroller');

        /* @var $log Zend_Log */
        $log = $bootstrap->getResource('Log');

        $log->log('Moduleloader', 8);

        $fc = $bootstrap->getResource('Frontcontroller');
        $modules = $fc->getControllerDirectory();

        foreach ($modules as $module => $dir) {
            $moduleName = strtolower($module);
            $moduleName = str_replace(array('-', '.'), ' ', $moduleName);
            $moduleName = ucwords($moduleName);
            $moduleName = str_replace(' ', '', $moduleName);

            $log->log(
                '  -- init Module: ' . $moduleName . ' ('
                . $fc->getControllerDirectory(strtolower($moduleName)) . ')', 8
            );

            $loader = new Zend_Application_Module_Autoloader(
                    array(
                        'namespace' => $moduleName,
                        'basePath' => realpath($dir . "/../")
                    )
            );
            $loader->addResourceType('form', 'forms', 'Form')
                   ->addResourceType('model', 'models', 'Model')
            ;
        }

        $log->log('', 8);
        return true;
    }

}