<?php

/**
 * Public bootstrap
 */


// define the application path
if (!defined('APPLICATION_PATH')) {
    define(
	    'APPLICATION_PATH',
        realpath(dirname(__FILE__) . '/../application'),
		true
	);
}

// define the environment
if (!defined('APPLICATION_ENV')) {
	$env = (getenv('APPLICATION_ENV')
			? getenv('APPLICATION_ENV') : 'production');

	define('APPLICATION_ENV', $env, true);
}

// Ensure library/ is on include_path
set_include_path(
	implode(
		PATH_SEPARATOR,
		array(
    		realpath(APPLICATION_PATH . '/../library'),
    		get_include_path(),
		)
	)
);

// load the zen app class
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// run the application! woohoo!
$application->bootstrap()->run();