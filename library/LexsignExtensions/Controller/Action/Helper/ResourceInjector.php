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
    
    public function preDispatch ()
    {

    	$log = Zend_Registry::get('log');
       	$log->log(__METHOD__, 8);
    	
    	$controller = $this->getActionController();
        $controller->_bootstrap = $this->getBootstrap();
        
        
        $defaultDependencies = 
        	$this->getFrontController()
        	     ->getParam('defaultDependencies') ? : array();
        
        $controller->dependencies = 
        	(is_array($controller->dependencies)) ? $controller->dependencies : array();
        							
        $controller->dependencies = array_unique(
        	array_merge(
        		$defaultDependencies, 
        		$controller->dependencies
        	)
        );
        
        foreach ($controller->dependencies as $name) {
        	
            if ($this->getBootstrap()->hasPluginResource($name)) {
            	$fn = 'get' . ucfirst($name); 
            	$resource = $this->getBootstrap()->getPluginResource($name)->$fn();
	            $controller->{'_' . $name} = $resource;
	        	$log->log('-- injected "$_' . $name . '" as ' . get_class($resource) , 8);
            	continue;
            }
            if ($this->getBootstrap()->hasResource($name)) {
            	$resource = $this->getBootstrap()->getResource($name); 
	            $controller->{'_' . $name} = $resource;
	        	$log->log('-- injected "$_' . $name . '" as ' . get_class($resource) , 8);
            	continue;
            }
           	throw new DomainException(
               	"Unable to find dependency by name '$name'"
            );
        }
    }
    
    public function getBootstrap ()
    {
    	return \Zend_Controller_Front::getInstance()->getParam("bootstrap");
    }
    
}