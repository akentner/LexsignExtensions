<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    /**
     * Initialize auto loader of Doctrine
     * @return Doctrine_Manager
     */
    protected function _initDoctrine ()
    {
        // Fetch the global Zend Autoloader
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $lib = realpath(APPLICATION_PATH . '/../library');
        require_once ($lib .
         '/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php');
        //doctrine autoloader
        $classLoader = new \Doctrine\Common\ClassLoader(
        'Doctrine\Common', $lib . '/vendor/doctrine-common/lib');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', 
        $lib . '/vendor/doctrine-dbal/lib');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', $lib);
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ODM', $lib);
        $classLoader->register();
        //Push the doctrine autoloader to load for the Doctrine\ namespace
        $autoloader->pushAutoloader($classLoader, 'Doctrine\\');
        //init arraycache
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        //setup configuration as seen from the sandbox application from the doctrine2 docs
        //http://www.doctrine-project.org/documentation/manual/2_0/en/configuration
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver(
        APPLICATION_PATH . '/../doctrine/entities');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(APPLICATION_PATH . '/../data/doctrine/proxies');
        $config->setProxyNamespace('Application\Proxies');
        $config->setAutoGenerateProxyClasses(true);
        //        //therefore you need some entries in your config:
        //        //doctrine.conn.host = 'localhost'
        //        //doctrine.conn.user = 'someuser'
        //        //doctrine.conn.pass = 'somepwd'
        //        //doctrine.conn.driv = 'pdo_pgsql' //i use postgres
        //        //doctrine.conn.dbname = 'somedbname'
        $doctrineConfig = $this->getOption(
        'doctrine');
        // @TODO refactor connectionOptions for multiple databases and inject it into the action controller
        $connectionOptions = array(
        'driver' => $doctrineConfig['conn']['driv'], 
        'user' => $doctrineConfig['conn']['user'], 
        'password' => $doctrineConfig['conn']['pass'], 
        'dbname' => $doctrineConfig['conn']['dbname'], 
        'host' => $doctrineConfig['conn']['host']);
        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        Zend_Registry::set('entitymanager', $em);
        return $em;
    }
    
}

