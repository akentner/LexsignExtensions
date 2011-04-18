<?php
class IndexController extends Zend_Controller_Action
{
    
	public $dependencies = array(
//        'db',
//        'navigation',
    );
	
	public function init ()
    {
        $this->_em = Zend_Registry::get('entitymanager');
    }
    public function indexAction ()
    {
        $test = new Application_Model_Test();
        $test->name = 'Test';
        
        
        /* @var \Zend_Application_Bootstrap $this->_bootstrap */ 
//        $this->_em->persist($test);
//        $this->_em->flush();
    }
}
