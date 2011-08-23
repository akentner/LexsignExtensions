<?php

/**
 * Configuration fixtures
 */

return array(
        'connection' => array(
                         'default' => array(
                                       'host'       => 'localhost',
                                       'port'       => 27017,
                                       'connect'    => true,
                                       'username'   => 'test',
                                       'password'   => 'test',
                                       'dbname'     => 'test',
                                       'timeout'    => 3000,
                                       'replicaSet' => false,
                                       'persist'    => 'wildkat_perisist',
                                      ),
                        ),
        'cache'      => array(
                         'default'  => array(
                                        'driver'    => 'Doctrine\Common\Cache\ArrayCache',
                                        'namespace' => 'wildkat_cache',
                                       ),
                         'memcache' => array(
                                        'driver'  => 'Doctrine\Common\Cache\MemcacheCache',
                                        'servers' => array(
                                                      array(
                                                       'host' => 'localhost',
                                                       'port' => 11211,
                                                      ),
                                                     ),
                                       ),
                        ),
        'dms'        => array(
                         'default' => array(
                                       'autoGenerateHydrationClasses' => '0',
                                       'autoGenerateProxyClasses'     => false,
                                       'defaultDb'                    => 'test',
                                       'documentNamespaces'           => 'Wildkat\Documents',
                                       'hydratorDir'                  => '/tmp',
                                       'hydratorNamespace'            => 'Wildkat\Documents',
                                       'metadataCache'                => 'default',
                                       'metadataDriver'               => 'AnnotationDriver',
                                       'metadataDir'                  => '/tmp',
                                       'proxyDir'                     => '/tmp',
                                       'proxyNamespace'               => 'WildkatProxy',
                                       'connection'                   => 'default',
                                      ),
                        ),
       );