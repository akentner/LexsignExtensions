<?php

//@TODO Autoloading with namespaces
//namespace LexsignExtensions\Application\Resource;

class LexsignExtensions_Application_Resource_Doctrine
    extends \Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initialize auto loader of Doctrine
     * @return Doctrine_Manager
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('Log');
        $this->getBootstrap()->bootstrap('Autoloader');

        /* @var $log Zend_Log */
        $log = Zend_Registry::get('log');
        $log->log(__METHOD__ . ' (START)', 8);

        $this->_initAutoloader();

        $em = \Doctrine\ORM\EntityManager::create(
                $this->_getConnectionOptions(), $this->_getConfiguration()
        );

        $log->log('  -- EntityManager successfully created', 8);

        Zend_Registry::set('doctrine', $em);

        $log->log(__METHOD__ . ' (END)', 8);
        return $em;
    }

    public function getDoctrine()
    {

        // @TODO get this from a factory, not use the Registry

        if (Zend_Registry::get('doctrine') instanceof \Doctrine\ORM\EntityManager) {
            return Zend_Registry::get('doctrine');
        }
        return $this->init();
    }

    private function _getConfiguration()
    {
        /* @var $log Zend_Log */
        $log = Zend_Registry::get('log');
        $log->log('  -- Configuration:', 8);

        $config = new \Doctrine\ORM\Configuration();

        $options = $this->getOptions();

        if (!key_exists('metadata', $options) || !key_exists('proxy', $options)) {
            throw new \Exception('metadata or proxy properties not set');
        }
        $metadata = $options['metadata'];
        $proxy = $options['proxy'];

        if (!key_exists('path', $proxy)
            || !key_exists('driver', $metadata)
        ) {
            throw new \Exception('minimum properties not set, one or missing: "proxy.path", "metadata.driver"');
        }

        switch ($metadata['driver']) {
            case 'AnnotationDriver':
                if (!key_exists('path', $metadata)) {
                    throw new \Exception('AnnotationDriver needs a path, "metadata.path" not set');
                }

                if (!file_exists($metadata['path'])) {
                    mkdir($metadata['path'], 0777, true);
                }

                $config->setMetadataDriverImpl(
                    $config->newDefaultAnnotationDriver($metadata['path'])
                );
                break;
            default:
                throw new \Exception('"' . $metadata['driver'] . '" is not implemented yet');
        }
        $log->log('    -- metadata.driver: ' . $metadata['driver'], 8);

        if (!file_exists($proxy['path'])) {
            mkdir($proxy['path'], 0777, true);
        }

        $config->setProxyDir($proxy['path']);
        $log->log('    -- proxy.path: ' . $proxy['path'], 8);

        if (!key_exists('namespace', $proxy)) {
            $proxy['namespace'] = 'Application\Proxies';
        }
        $config->setProxyNamespace($proxy['namespace']);
        $log->log('    -- proxy.namespace: ' . $proxy['namespace'], 8);


        if (key_exists('cache', $metadata)) {
            $cache = '\\Doctrine\\Common\\Cache\\' . $metadata['cache'];
            $config->setMetadataCacheImpl(new $cache());
            $log->log('    -- metadata.cache: ' . $metadata['cache'], 8);
        }

        if (key_exists('autoGenerateClasses', $proxy)) {
            $config->setAutoGenerateProxyClasses($proxy['autoGenerateClasses']);
            $log->log('    -- proxy.autoGenerateClasses: ' . $proxy['autoGenerateClasses'], 8);
        }

        if (key_exists('query', $options) && key_exists('cache', $options['query'])) {
            $cache = '\\Doctrine\\Common\\Cache\\' . $options['query']['cache'];
            $config->setQueryCacheImpl(new $cache());
            $log->log('    -- query.cache: ' . $options['query']['cache'], 8);
        }

        //$config->setSQLLogger($logger); // @TODO implement SQL Logger

        return $config;
    }

    private function _getConnectionOptions()
    {
        /* @var $log Zend_Log */
        $log = Zend_Registry::get('log');
        $log->log('  -- Connection:', 8);
        $options = $this->getOptions();
        if (!key_exists('conn', $options)) {
            throw new \Exception('connection properties not set');
        }

        $connectionOptions = array();
        foreach ($options['conn'] as $key => $value) {
            $connectionOptions[$key] = $value;
            $log->log('    -- ' . $key . ': ' . $value, 8);
        }
        return $connectionOptions;
    }

    private function _initAutoloader()
    {

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $lib = realpath(APPLICATION_PATH . '/../library');

        require_once ($lib . '/Doctrine/Common/ClassLoader.php');

        //doctrine autoloader
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', $lib);
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', $lib);
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', $lib);
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ODM', $lib);
        $classLoader->register();

        //Push the doctrine autoloader to load for the Doctrine\ namespace
        $autoloader->pushAutoloader($classLoader, 'Doctrine\\');
    }

}