<?php

/**
 * <tt>ClassLexsignExtensions_Application_Resource_AutoloaderLoader</tt>
 * prepares the general Zend
 * Autoloader.
 *
 * @license no licence yet
 * @link no documentation yet
 * @package LexsignExtensions
 * @subpackage Application
 *
 * @author Alexander Kentner <akentner@lexsign.de>
 * @since 0.1
 */
class LexsignExtensions_Application_Resource_Autoloader extends \Zend_Application_Resource_ResourceAbstract
{

    /**
     * Register namespace Application_
     * @return Zend_Application_Module_Autoloader
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Log');

        /* @var $log Zend_Log */
        $log = $bootstrap->getResource('Log');

        $log->log('Autoloader', 8);

        $autoloader = Zend_Loader_Autoloader::getInstance();

        foreach ($autoloader->getRegisteredNamespaces() as $namespace) {
            $log->log('  -- Namespace: ' . $namespace, 8);
        }
        $log->log('', 8);
        return $autoloader;
    }

}