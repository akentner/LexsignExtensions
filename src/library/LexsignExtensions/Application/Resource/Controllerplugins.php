<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Controllerplugins
    extends \Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Log');

        /* @var $log Zend_Log */
        $log = $bootstrap->getResource('Log');

        $log->log('Controller Plugins', 8);

        $bootstrap->bootstrap('Autoloader');
        $bootstrap->bootstrap('Frontcontroller');

        $fc = $bootstrap->getResource('Frontcontroller');

        $options = $this->getOptions();
        foreach ($options['plugins'] as $plugin) {
            $class = 'LexsignExtensions_Controller_Plugin_' . ucfirst($plugin);
            $log->log('  -- ' . $class, 8);
            $fc->registerPlugin(new $class());
        }
        $log->log('', 8);
        return true;
    }

}