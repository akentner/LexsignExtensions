<?php
class Default_IndexController extends Zend_Controller_Action
{

	public $dependencies = array(
        'log',
        'view',
//        'navigation',
//		'doctrine'
    );

    public function indexAction ()
    {
        $test = new Application_Model_Test();
        $test->name = 'Test bla blubbbb';
        $this->log->init(__METHOD__);
		$this->view->debug = __FILE__;

//        $this->_doctrine->persist($test);
//        $this->_doctrine->flush();

    }
}
