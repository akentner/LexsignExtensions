<?php

/**
 * LICENCE
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * Neither the name of the Wildkat Technologies nor the names of its
 * contributors may be used to endorse or promote products derived from this
 * software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 */


namespace Wildkat\Application\Container;

use Doctrine\Common\ClassLoader,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\ODM\MongoDB\DocumentManager,
    Doctrine\MongoDB\Connection,
    Doctrine\ODM\MongoDB\Configuration as ODMConfig,
    Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver,
    Doctrine\Common\Cache\AbstractCache,
    Doctrine\Common\Cache\MemcacheCache,
    Doctrine\MongoDB\Configuration as DBALConfig;

/**
 * @package Wildkat\Application\Container
 * @author  Kevin Bradwick <kevin@wildk.at>
 * @licence New BSD (http://www.opensource.org/licenses/bsd-license.php)
 * @version @version_string@
 * @link    http://code.google.com/p/zf1-doctrine-mongodb/
 */
class DoctrineContainer
{
	/**
	 * Cache cache
	 * @var array
	 */
	private $_cache;

	/**
	 * Document Manager cache
	 * @var array
	 */
	private $_dm;

	/**
	 * Connection cache
	 * @var array
	 */
	private $_connection;

	/**
	 * Config array
	 * @var array
	 */
	private $_config;

	/**
	 * Class construct
	 *
	 * @param array $config configuration options
	 *
	 * @return null
	 */
	public function __construct(array $config=array())
	{
        if (count($config) === 0) {
            throw new \Exception(
                'No configuration options passed to container', 500
            );
        }

		$this->_config = $config;
        $this->_setUpAutoloaders();
	}

    /**
	 * Set up the doctrine autoloaders. You must structure the libraries inside
	 * your library folder e.g.
	 * <pre>
	 * /library
	 *  |-- Doctrine
	 *  |  |-- Common  (https://github.com/doctrine/common)
	 *  |  |-- MongoDB (https://github.com/doctrine/mongodb)
	 *  |  |-- ODM     (https://github.com/doctrine/mongodb-odm)
	 *  |  |-- Symfony
	 *  |-- DoctrineExtensions
	 * </pre>
	 *
	 * @return null
	 */
	protected function _setUpAutoloaders()
	{
		require_once 'Doctrine/Common/ClassLoader.php';
		$loader = \Zend_Loader_Autoloader::getInstance();

		$classLoader = new ClassLoader('Wildkat');
		$loader->pushAutoloader(array($classLoader, 'loadClass'), 'Wildkat');

		$classLoader = new ClassLoader('Doctrine');
		$loader->pushAutoloader(array($classLoader, 'loadClass'), 'Doctrine');

		$classLoader = new ClassLoader('Symfony', 'Doctrine');
		$loader->pushAutoloader(array($classLoader, 'loadClass'), 'Symfony');

		$classLoader = new ClassLoader('DoctrineExtensions');
		$loader->pushAutoloader(
			array($classLoader, 'loadClass'),
			'DoctrineExtensions'
		);
	}

	/**
	 * Returns a document manager instance
	 *
	 * @param string $key the key specified in the config
	 *
	 * @return \Doctrine\ODM\MongoDB\DocumentManager
	 * @throws \Exception
	 */
	public function getDocumentManager($key='default')
	{
		if (isset($this->_dm[$key]) === true
			&& $this->_dm[$key] instanceof DocumentManager
		) {
			return $this->_dm[$key];
		}

		if (isset($this->_config['dms'][$key]) === false) {
			throw new \Exception(
				'Unknown Document Manager key in config: ' . $key, 500
			);
		}

        $config = $this->_getOdmConfigFromArray($this->_config['dms'][$key]);
        $conn   = $this->getConnection($this->_config['dms'][$key]['connection']);
        $dm     = DocumentManager::create($conn, $config);

        $this->_dm[$key] = $dm;
        return $this->_dm[$key];
	}

	/**
	 * Returns an instance of config from an array of options
	 *
	 * @param array $options the options to specify
	 *
	 * @return Doctrine\ODM\MongoDB\Configuration
	 * @throws \Exception
	 */
	private function _getOdmConfigFromArray(array $options=array())
	{
		$config = new ODMConfig();

		if (isset($options['autoGenerateHydrationClasses']) === true) {
			$config->setAutoGenerateHydratorClasses(
				$this->_stringToBoolean(
					$options['autoGenerateHydrationClasses']
				)
			);
		}

		if (isset($options['autoGenerateProxyClasses']) === true) {
			$config->setAutoGenerateProxyClasses(
				$this->_stringToBoolean(
					$options['autoGenerateProxyClasses']
				)
			);
		}

		if (isset($options['defaultDb']) === true) {
			$config->setDefaultDB($options['defaultDb']);
		} else {
            $dbName = $this->_config['connection'][$options['connection']]['dbname'];
            if (strlen($dbName) > 0) {
                $config->setDefaultDB($dbName);
            }
        }

		if (isset($options['documentNamespaces']) === true) {
            if (is_array($options['documentNamespaces']) === false) {
                $options['documentNamespaces']
                    = array($options['documentNamespaces']);
            }
			$config->setDocumentNamespaces($options['documentNamespaces']);
		}

		if (isset($options['hydratorDir']) === true) {
			$config->setHydratorDir($options['hydratorDir']);
		}

		if (isset($options['hydratorNamespace']) === true) {
			$config->setHydratorNamespace($options['hydratorNamespace']);
		}

		if (isset($options['metadataCache']) === true) {
			$config->setMetadataCacheImpl(
				$this->getCacheInstance($options['metadataCache'])
			);
		}

        $metadataDriver = $this->_getMetadataDriver($options);
        $config->setMetadataDriverImpl($metadataDriver);

        if (isset($options['proxyDir']) === true) {
			$config->setProxyDir($options['proxyDir']);
		}

        if (isset($options['proxyNamespace']) === true) {
			$config->setProxyNamespace($options['proxyNamespace']);
		}

        return $config;
	}

    /**
     *
     * @param array $config the configuration options
     *
     * @return Doctrine\ODM\MongoDB\Mapping\Driver\Driver
     */
    private function _getMetadataDriver(array $config=array())
    {
        if (isset($config['metadataDriver']) === false) {
            $config['metadataDriver'] = 'AnnotationDriver';
        }
        
        if ($config['metadataDriver'] === 'AnnotationDriver'
            || in_array($config['metadataDriver'], array('YamlDriver', 'XmlDriver'))  === false) {
            $reader = new AnnotationReader();
            $reader->setDefaultAnnotationNamespace(
                'Doctrine\ODM\MongoDB\Mapping\\'
            );
            return new AnnotationDriver($reader, $config['metadataDir']);
        }

        $driverName = 'Doctrine\ODM\MongoDB\Mapping\Driver\\' . $config['metadataDriver'];
        $driver     = new $driverName($config['metadataDir']);
        return $driver;
    }

	/**
	 * Returns a connection instance
	 *
	 * @param string $key
	 *
	 * @return \Doctrine\MongoDB\Connection
	 * @throws \Exception
	 */
	public function getConnection($key='default')
	{
		if (isset($this->_connection[$key]) === true
			&& $this->_connection[$key] instanceof Connection
		) {
			return $this->_connection[$key];
		}

		if (isset($this->_config['connection'][$key]) === false) {
			throw new \Exception('Unknown connection config: ' . $key, 500);
		}

		$options = $this->_config['connection'][$key];
		$dsn     = $this->_configureDsn($this->_config['connection'][$key]);
		$mongo   = new \Mongo($dsn, $this->_createMongoOptionsArray($options));
		$config  = new DBALCOnfig();

		if (isset($options['mongoCmd']) === true) {
			$config->setMongoCmd($options['mongoCmd']);
		}

		if (isset($options['loggerClass']) === true
			&& isset($options['loggerCallback']) === true
		) {
            //@codeCoverageIgnoreStart
			$logger = new $options['loggerClass'];
			$config->setLoggerCallable(
				array($logger, $options['loggerCallback'])
			);
		}
        //@codeCoverageIgnoreEnd

		$conn = new Connection($mongo, array(), $config);
		$this->_connection[$key] = $conn;
		return $this->_connection[$key];
	}

	/**
	 * Create an array of mongo options to pass to the constructor
	 *
	 * @param array $options the options array
	 *
	 * @return array
	 */
	private function _createMongoOptionsArray(array $options=array())
	{
		$default = array();

		if (isset($options['replicaSet']) === true) {
			$default['replicaSet']
				= $this->_stringToBoolean($options['replicaSet']);
		}

		if (isset($options['connect']) === true) {
			$default['connect']
				= $this->_stringToBoolean($options['connect']);
		}

		if (isset($options['persist']) === true) {
			$default['persist'] = $options['persist'];
		}

		if (isset($options['timeout']) === true) {
			$default['timeout'] = (int) $options['timeout'];
		}

		return $default;
	}

	/**
	 * Turns a string into a boolean e.g. '1' = true, '0' = false etc.
	 *
	 * @param string $string the string to evaluate
	 *
	 * @return boolean
	 */
	private function _stringToBoolean($string='')
	{
		if (preg_match('/^(1|true)$/', $string) === 1) {
			return true;
		}

        if ($string === true || $string === false) {
            return $string;
        }

		return false;
	}

	/**
	 * Returns a cache implementation
	 *
	 * @param string $key the config key
	 *
	 * @return \Doctrine\Common\Cache\Cache
	 * @throws \Exception
	 */
	public function getCacheInstance($key='default')
	{
		if (isset($this->_cache[$key]) === true
			&& $this->_cache[$key] instanceof AbstractCache
		) {
			return $this->_cache[$key];
		}

		if (isset($this->_config['cache'][$key]) === false) {
			throw new \Exception(
				'Unable to create cache instance from config key: ' . $key, 500
			);
		}

		$options = $this->_config['cache'][$key];
		if (isset($options['driver']) === false) {
			throw new \Exception('No driver specified in config file', 500);
		}

		$driver  = new $options['driver'];
		$options =& $this->_config['cache'][$key];

		if (isset($options['namespace']) === true) {
			$driver->setNamespace($options['namespace']);
		}

		if (method_exists($driver, 'initialize')) {
            //@codeCoverageIgnoreStart
            $driver->initialize($options);
            //@codeCoverageIgnoreEnd
		} else if ($driver instanceof MemcacheCache) {
            $defaultServer = array(
                'host'          => 'localhost',
                'port'          => 11211,
                'persistent'    => true,
                'weight'        => 1,
                'timeout'       => 1,
                'retryInterval' => 15,
                'status'        => true
            );

            $memcache = new \Memcache();

			if (isset($options['servers'])) {
                foreach ($options['servers'] as $server) {
                    $server = array_replace_recursive($defaultServer, $server);
                    $memcache->addServer(
                        $server['host'],
                        $server['port'],
                        $server['persistent'],
                        $server['weight'],
                        $server['timeout'],
                        $server['retryInterval'],
                        $server['status']
                    );
                }
            }
            $driver->setMemcache($memcache);
		}

		$this->_cache[$key] = $driver;
		return $this->_cache[$key];
	}

	/**
	 * Construct a DSN
	 *
	 * @param array $options dsn options
	 *
	 * @return string
	 */
	private function _configureDsn(array $options=array())
	{
		$default = array(
			'host'       => 'localhost',
			'dbname'     => '',
			'username'   => '',
			'password'   => '',
			'port'       => 27017,
		);

		$dsnOptions = array_replace_recursive($default, $options);
		$format     = 'mongodb://%s%s%s%s%s';

		if (strlen($dsnOptions['username']) > 0
			&& strlen($dsnOptions['password']) > 0
		) {
			$username = $dsnOptions['username'];
			$password = ':' . $dsnOptions['password'] . '@';
		} else {
			$username = '';
			$password = '';
		}

		$port = ':' . $dsnOptions['port'];

		if (strlen($dsnOptions['dbname']) > 0) {
			$database = '/' . $dsnOptions['dbname'];
		} else {
			$database = '';
		}

		return sprintf(
			$format,
			$username,
			$password,
			$dsnOptions['host'],
			$port,
			$database
		);
	}

}