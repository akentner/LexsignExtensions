<?php

use Wildkat\Application\Container\DoctrineContainer;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Loads the container
     *
     * @return Wildkat\Application\Container\DoctrineContainer
     */
    public function _initDoctrineMongoContainer()
    {
        $container = new DoctrineContainer($this->getOption('doctrine'));
        Zend_Registry::set('Wildkat\DoctrineContainer', $container);

        return $container;
    }
}

