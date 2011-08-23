<?php
/**
 *
 * @author akentner
 * @version
 */
/**
 * ResourceInjector Action Helper
 *
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class LexsignExtensions_Controller_Action_Helper_ResourceInjector
	extends \Zend_Controller_Action_Helper_Abstract
{
    protected $_resources;
    protected $_controller;
    protected $_systemResources = array('view', 'layout');

    public function preDispatch ()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Log');

        $log = $bootstrap->getResource('Log');
       	$log->log('Resource Injector', 8);

    	$this->_controller = $this->getActionController();
        $this->_controller->bootstrap = $bootstrap;

        foreach ($this->_getDependencies() as $name) {

            if (in_array($name, $this->_systemResources)) {
	        	$log->log('  -- cannot injected plugin resource ' . $name , 8);
                continue;
            }

            if ($this->getBootstrap()->hasPluginResource($name)) {
            	$fn = 'get' . ucfirst($name);
            	$resource = $this->getBootstrap()->getPluginResource($name)->$fn();
	            $this->_controller->{$name} = $resource;
	        	$log->log('  -- injected plugin resource "$' . $name . '" as ' . get_class($resource) , 8);
            	continue;
            }
            if ($this->getBootstrap()->hasResource($name)) {
            	$resource = $this->getBootstrap()->getResource($name);
	            $this->_controller->{$name} = $resource;
	        	$log->log('  -- injected resource "$' . $name . '" as ' . get_class($resource) , 8);
            	continue;
            }
           	throw new DomainException("Unable to find dependency with name '$name'");
        }
        $log->log('', 8);
        return true;
    }

    protected function _getDependencies()
    {

        $defaultDependencies =
        	$this->getFrontController()
        	     ->getParam('defaultDependencies') ? : array();

        $this->_controller->dependencies =
        	(is_array($this->_controller->dependencies)) ? $this->_controller->dependencies : array();

        $this->_controller->dependencies = array_unique(
        	array_merge(
        		$defaultDependencies,
        		$this->_controller->dependencies
        	)
        );
        return $this->_controller->dependencies;
    }

    public function getBootstrap ()
    {
    	return \Zend_Controller_Front::getInstance()->getParam("bootstrap");
    }

}