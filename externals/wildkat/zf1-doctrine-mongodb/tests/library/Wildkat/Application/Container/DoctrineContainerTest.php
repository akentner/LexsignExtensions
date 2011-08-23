<?php

use Wildkat\Application\Container\DoctrineContainer;

/**
 * DoctrineContainerTest
 *
 * @author   Kevin Bradwick <kevin@wildk.at>
 * @license  New BSD Licence http://www.opensource.org/licenses/bsd-license.php
 * @link     http://code.google.com/p/zf1-doctrine-mongodb/
 * @category Testing
 */
class DoctrineContainerTest extends TestHelper
{
    /**
     * Default configuration options
     * @var array
     */
    protected $_config;

    public function setUp()
    {
        parent::setUp();

        $this->_config = $this->_getConfigData();
    }

    /**
     * Returns the config data
     *
     * @return array
     */
    protected function _getConfigData()
    {
        return include TEST_DIR . '/config.php';
    }

    /**
     * Test construct
     *
     * @return null
     * @expectedException \Exception
     */
    public function testConstruct()
    {
        $container = new DoctrineContainer();
    }

    /**
     * Test the configuration of a dsn
     *
     * @return null
     */
    public function testConfigureDsn()
    {
        $container = new DoctrineContainer($this->_config);
        $method    = new ReflectionMethod($container, '_configureDsn');

        $this->assertTrue($method->isPrivate());
        $method->setAccessible(true);

        $this->assertEquals(
            'mongodb://localhost:27017',
            $method->invoke($container, array())
        );

        $this->assertEquals(
            'mongodb://test:test@localhost:27017',
            $method->invoke($container, array('username' => 'test', 'password' => 'test'))
        );

        $this->assertEquals(
            'mongodb://test:test@localhost:27017/test',
            $method->invoke($container,
                array('username' => 'test', 'password' => 'test', 'dbname' => 'test'))
        );

        $this->assertEquals(
            'mongodb://localhost:27017',
            $method->invoke($container, array('username' => 'test'))
        );

        $this->assertEquals(
            'mongodb://localhost:27017',
            $method->invoke($container, array('password' => 'test'))
        );

        $this->assertEquals(
            'mongodb://localhost:27017/test',
            $method->invoke($container, array('dbname' => 'test'))
        );
    }

    /**
     * Test we can catch all exceptions in getCacheInstance
     *
     * @return null
     * @expectedException \Exception
     */
    public function testCatchExceptionsInGetCacheInstanceWithUnknownKey()
    {
        $container = new DoctrineContainer($this->_config);
        $cache     = $container->getCacheInstance('foo');
    }

    /**
     * Test we can catch all exceptions in getCacheInstance
     *
     * @return null
     * @expectedException \Exception
     */
    public function testCatchExceptionsInGetCacheInstanceWithNoDriverSet()
    {
        unset($this->_config['cache']['default']['driver']);
        $container = new DoctrineContainer($this->_config);
        $cache     = $container->getCacheInstance('default');
    }

    /**
     * Test we can create a valid driver
     *
     * @return null
     */
    public function testCanCreateArrayCacheDriver()
    {
        $container = new DoctrineContainer($this->_config);
        $cache     = $container->getCacheInstance('default');

        $this->assertInstanceOf('Doctrine\Common\Cache\ArrayCache', $cache);
    }

    /**
     * Test we can create a valid driver
     *
     * @return null
     */
    public function testCanCreateMemcacheDriver()
    {
        $container = new DoctrineContainer($this->_config);
        $cache     = $container->getCacheInstance('memcache');
        $this->assertInstanceOf('Doctrine\Common\Cache\MemcacheCache', $cache);

        $memcache = $cache->getMemcache();
        $this->assertInstanceOf('Memcache', $memcache);

        // get the connection again but it should return a copy
        $cache2 = $container->getCacheInstance('memcache');
        $this->assertEquals($cache, $cache2);
    }

    /**
     * Test succesfull connection
     *
     * @return null
     */
    public function testGetSuccesfullConnection()
    {
        $container = new DoctrineContainer($this->_config);
        $conn = $container->getConnection('default');
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $conn);
        $conn2 = $container->getConnection('default');
        $this->assertEquals($conn, $conn2);

        $mongo = $conn->getMongo();
        $this->assertInstanceOf('Mongo', $mongo);
    }

    /**
     * @return null
     */
    public function testGetConnectionWithDefaultSettings()
    {
        unset($this->_config['connection']['default']['username']);
        unset($this->_config['connection']['default']['password']);
        unset($this->_config['connection']['default']['dbname']);
        $container = new DoctrineContainer($this->_config);
        $conn = $container->getConnection('default');
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $conn);
    }

    /**
     * Test exceptions
     *
     * @return null
     * @expectedException Exception
     */
    public function testGetExceptionFromGetConnection()
    {
        $container = new DoctrineContainer($this->_config);
        $conn = $container->getConnection('foo');
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionWithBadDmConfigKey()
    {
        $container = new DoctrineContainer($this->_config);
        $dm = $container->getDocumentManager('foo');
    }

    /**
     * Test getdocumentmanager
     *
     * @return null
     */
    public function testGetDocumentManager()
    {
        $container = new DoctrineContainer($this->_config);
        $dm = $container->getDocumentManager('default');
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $dm);

        // call it again to test cached instance
        $dm2 = $container->getDocumentManager('default');
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $dm2);
        $this->assertEquals($dm, $dm2);

        // test configuration
        $config = $dm->getConfiguration();
        $conn   = $dm->getConnection();

        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Configuration', $config);
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $conn);

        $this->assertEquals($config->getAutoGenerateHydratorClasses(), false);
        $this->assertEquals($config->getAutoGenerateProxyClasses(), false);
        $this->assertEquals($config->getDefaultDB(), 'test');
        $this->assertEquals($config->getHydratorDir(), '/tmp');
        $this->assertEquals($config->getHydratorNamespace(), 'Wildkat\Documents');
        $this->assertEquals($config->getMetadataCacheImpl(), $container->getCacheInstance('default'));
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver', $config->getMetadataDriverImpl());
        $this->assertEquals($config->getProxyDir(), '/tmp');
        $this->assertEquals($config->getProxyNamespace(), 'WildkatProxy');
        $this->assertEquals('test', $config->getDefaultDB());
    }

    /**
     * If no default db is set, then the db name from the connection settings
     * should be used instead.
     *
     * @return null
     */
    public function testGetDocumentManagerWithNoDefaultDb()
    {
        unset($this->_config['dms']['default']['defaultDb']);

        $container = new DoctrineContainer($this->_config);
        $dm        = $container->getDocumentManager('default');
        $config    = $dm->getConfiguration();

        $this->assertEquals('test', $config->getDefaultDB());

    }

    /**
     * Test Yaml/Xml driver
     *
     * @return null
     */
    public function testGetDocumentManagerWithFileMetaDriver()
    {
        $this->_config['dms']['default']['metadataDriver'] = 'YamlDriver';
        $container = new DoctrineContainer($this->_config);
        $dm = $container->getDocumentManager('default');
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $dm);

        // test configuration
        $config = $dm->getConfiguration();
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver', $config->getMetadataDriverImpl());

        $this->_config['dms']['default']['metadataDriver'] = 'XmlDriver';
        $container = new DoctrineContainer($this->_config);
        $dm = $container->getDocumentManager('default');
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $dm);

        // test configuration
        $config = $dm->getConfiguration();
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver', $config->getMetadataDriverImpl());
    }

    /**
     * Test we can get a document manager with minimal settings
     */
    public function testGetDocumentManagerWithMinimalSettings()
    {
        unset($this->_config['dms']['default']['metadataDriver']);
        $container = new DoctrineContainer($this->_config);
        $dm = $container->getDocumentManager('default');
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\DocumentManager', $dm);

        $config = $dm->getConfiguration();
        $this->assertInstanceOf('Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver', $config->getMetadataDriverImpl());
    }

    /**
     * Test exception
     *
     * @excpectedException \Exception
     */
    public function testGetDocumentManagerWithBadConnection()
    {
        $this->_config['dms']['default']['connection'] = 'foo';
        $container = new DoctrineContainer($this->_config);
    }

    /**
     * Test _stringToBoolean
     *
     * @return null
     */
    public function testStringToBoolean()
    {
        $container = new DoctrineContainer($this->_config);
        $method    = new ReflectionMethod($container, '_stringToBoolean');

        $this->assertTrue($method->isPrivate());

        $method->setAccessible(true);
        $this->assertTrue($method->invoke($container, '1'));
        $this->assertFalse($method->invoke($container, '0'));
        $this->assertTrue($method->invoke($container, 'true'));
        $this->assertFalse($method->invoke($container, 'false'));
    }

    /**
     * Test getConnection
     *
     * @return null
     */
    public function testGetConnection()
    {
        $this->_config['connection']['default']['mongoCmd'] = '$';
        $container  = new DoctrineContainer($this->_config);
        $connection = $container->getConnection('default');

        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $connection);
        $mongo = $connection->getMongo();
        $this->assertInstanceOf('Mongo', $mongo);
        $this->assertTrue($mongo->connected);

        // lets add a collection to test authentication
        $db  = $connection->selectDatabase('test');
        $col = $db->createCollection('zfmongo');
        $this->assertInstanceOf('MongoCollection', $col);
        $response = $col->drop();
        $this->assertInternalType('array', $response);
        $this->assertEquals(1, $response['ok']);
    }
}