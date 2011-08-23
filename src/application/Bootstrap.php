<?php

/**
 * File Doc Comment
 *
 * PHP version 5.3
 *
 * @category   Application
 * @package    Configuration
 * @subpackage Bootstrap
 * @author     Alexander Kentner <akentner@lexsign.de>
 * @copyright  2011 Alexander Kentner
 * @license    http://no-licence-yet NLY Licence
 * @version    CVS: $Id: Bootstrap.php 301632 2010-07-28 01:57:56Z squiz $
 * @link       http://no-link-yet
 */


/**
 * Application Bootstrap
 * try to configure most of the resources via application.ini
 *
 * @category   Application
 * @package    Configuration
 * @subpackage Bootstrap
 * @author     Alexander Kentner <akentner@lexsign.de>
 * @copyright  2011 Alexander Kentner
 * @license    http://no-licence-yet NLY Licence
 * @version    Release: 0.1
 * @link       http://no-link-yet
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initAutoloaderNamespaces()
    {
        require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');
    }

}