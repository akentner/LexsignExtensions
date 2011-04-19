<?php
class IndexController extends Zend_Controller_Action
{
    
	public $dependencies = array(
//        'db',
//        'navigation',
		'doctrine'
    );
	
    public function indexAction ()
    {
        $test = new Application_Model_Test();
        $test->name = 'Test';
        
        
        /* @var \Zend_Application_Bootstrap $this->_bootstrap */ 
        $this->_doctrine->persist($test);
        $this->_doctrine->flush();
    }
}
