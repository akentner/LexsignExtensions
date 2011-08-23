<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Router
    extends Zend_Application_Resource_Router
{

    protected $_log;

    /**
     * Retrieve router object
     *
     * @return Zend_Controller_Router_Rewrite
     */
    public function getRouter()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Log');

        /* @var $log Zend_Log */
        $log = $bootstrap->getResource('Log');
        $log->log('Router', 8);

        if (null === $this->_router) {
            $bootstrap = $this->getBootstrap();
            $bootstrap->bootstrap('FrontController');
            $this->_router = $bootstrap->getContainer()->frontcontroller->getRouter();

//        $this->_router->removeDefaultRoutes();
            $file = APPLICATION_PATH . '/configs/router.ini';
            $options = new Zend_Config_Ini($file, 'router');

            $options = $options->toArray();

            if (isset($options['chainNameSeparator'])) {
                $this->_router->setChainNameSeparator($options['chainNameSeparator']);
            }

            if (isset($options['useRequestParametersAsGlobal'])) {
                $this->_router->useRequestParametersAsGlobal($options['useRequestParametersAsGlobal']);
            }

//            $this->_router->addConfig($options, 'router');
            $this->_router->addConfig(new Zend_Config($options['router']));
            $log->log('  -- Router Config: ' . $file, 8);
        }
        $log->log('', 8);
        return $this->_router;
    }
}

