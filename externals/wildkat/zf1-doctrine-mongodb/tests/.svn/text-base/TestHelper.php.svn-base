<?php

/**
 * TestHelper
 *
 * @author   Kevin Bradwick <kevin@wildk.at>
 * @license  New BSD Licence http://www.opensource.org/licenses/bsd-license.php
 * @link     http://code.google.com/p/zf1-doctrine-mongodb/
 * @category Testing
 */
class TestHelper extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * Setup method
     *
     * @return null
     */
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        parent::setUp();
    }
}