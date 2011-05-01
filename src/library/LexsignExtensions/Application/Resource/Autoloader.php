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
        $this->getBootstrap()->bootstrap('Log');

        $log = Zend_Registry::get('log');
        $log->log(__METHOD__ . ' (START)', 8);

        $autoloader = new Zend_Application_Module_Autoloader(
                array(
                    'namespace' => 'Application',
                    'basePath' => dirname(__FILE__)
                )
        );
        $log->log('  -- Namespace: ' . $autoloader->getNamespace(), 8);
        $log->log(__METHOD__ . ' (END)', 8);
        return $autoloader;
    }

}