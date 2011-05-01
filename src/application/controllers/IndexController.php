<?php
class IndexController extends Zend_Controller_Action
{

	public $dependencies = array(
        'log',
//        'navigation',
//		'doctrine'
    );

    public function indexAction ()
    {
        $test = new Application_Model_Test();
        $test->name = 'Test';
        $this->_log->init(__METHOD__);

//        $this->_doctrine->persist($test);
//        $this->_doctrine->flush();

    }
}
