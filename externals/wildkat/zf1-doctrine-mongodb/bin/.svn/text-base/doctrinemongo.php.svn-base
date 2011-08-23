<?php

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

$application->bootstrap();

// get the container from the bootstrap
$container
    = $application->getBootstrap()->getResource('DoctrineMongoContainer');

if (isset($_SERVER['argv'][1]) === true) {
    $dmName = $_SERVER['argv'][1];
    unset($_SERVER['argv'][1]);
} else {
    $dmName = 'default';
}

$dm = $container->getDocumentManager($dmName);
$helpers = array(
    'dm' => new Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper($dm),
);
$helperSet = new Symfony\Component\Console\Helper\HelperSet();

foreach ($helpers as $name => $helper) {
    $helperSet->set($helper, $name);
}

$cli = new \Symfony\Component\Console\Application(
    'Doctrine ODM MongoDB Command Line Interface',
    Doctrine\ODM\MongoDB\Version::VERSION
);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
$cli->addCommands(array(
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand(),
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateDocumentsCommand(),
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateRepositoriesCommand(),
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand(),
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand(),
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand(),
    new \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand(),
));
$cli->run();
