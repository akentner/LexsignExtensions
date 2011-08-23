<?php

/**
 * File Doc Comment
 *
 * PHP version 5.3
 *
 * @category   Application
 * @package    Default
 * @subpackage Controller
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
 * @package    Default
 * @subpackage Controller
 * @author     Alexander Kentner <akentner@lexsign.de>
 * @copyright  2011 Alexander Kentner
 * @license    http://no-licence-yet NLY Licence
 * @version    Release: 0.1
 * @link       http://no-link-yet
 */
class LexsignExtensions_Controller_Error extends Zend_Controller_Action
{

	public $dependencies = array(
        'log',
    );

    /**
     * Error Action
     *
     * @return boolean
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        // Log exception, if logger available
        $this->log->crit(
            $this->view->message, array(
                'requestUri' => $errors->request->getRequestUri()
            )
        );

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }


}

