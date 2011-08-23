<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Jquery extends \Zend_Application_Resource_ResourceAbstract
{

    /**
     * initializes jQuery as resource
     *
     * @return Zend_View
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Log');
        $bootstrap->bootstrap('layout');

        /* @var $log Zend_Log */
        $log = $bootstrap->getResource('Log');

        $view = $bootstrap->getResource('layout')->getView();
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');

        ZendX_JQuery::enableView($view);
        $view->jQuery()->enable()->uienable();
        $view->jQuery()->addStylesheet('/css/smoothness/jquery-ui-1.8.2.custom.css');

        $log->log('jQuery Support', 8);
        $log->log('  -- initialized', 8);
        $log->log('', 8);
        return $view;
    }
}