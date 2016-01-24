<?php

/*
 * This file is part of the ACFWurflBundle.
 *
 * (c) Albert Casademont <albertcasademont@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACF\Bundle\WurflBundle\Tests;

use ACF\Bundle\WurflBundle\DependencyInjection\ACFWurflExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ACFWurflExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $config = array(
            'wurfl_file' => __DIR__ . '/Resources/wurfl/wurfl-light.xml',
        );

        $container = $this->createCompiledContainerForConfig($config);

        $this->assertInstanceOf('WURFL_Configuration_InMemoryConfig', $container->get('acf_wurfl.config'));
        $this->assertEquals(__DIR__ . '/Resources/wurfl/wurfl-light.xml', $container->get('acf_wurfl.config')->wurflFile);
        $this->assertEquals('performance', $container->get('acf_wurfl.config')->matchMode);
        $cache = $container->get('acf_wurfl.config')->cache;
        $this->assertEquals('file', $cache['provider']);
        $this->assertEquals($container->getParameter('kernel.root_dir') . '/wurfl', $cache['params']['dir']);
        $persistence = $container->get('acf_wurfl.config')->persistence;
        $this->assertEquals('file', $persistence['provider']);
        $this->assertEquals($container->getParameter('kernel.root_dir') . '/wurfl', $persistence['params']['dir']);


        $this->assertInstanceOf('WURFL_WURFLManager', $container->get('acf_wurfl'));
        $this->assertEquals('www.wurflpro.com - 2010-02-03 10:31:00', $container->get('acf_wurfl')->getWURFLInfo()->version);
        $this->assertEquals('generic_web_browser', $container->get('acf_wurfl')->getDeviceForUserAgent('Mozilla/4.0')->id);
    }

    public function testApcCache()
    {
        $config = array(
            'wurfl_file' => __DIR__ . '/Resources/wurfl/wurfl-light.xml',
            'match_mode'   => 'accuracy',
            'cache' => 'apc'
        );

        $container = $this->createCompiledContainerForConfig($config);

        $this->assertInstanceOf('WURFL_Configuration_InMemoryConfig', $container->get('acf_wurfl.config'));
        $this->assertEquals(__DIR__ . '/Resources/wurfl/wurfl-light.xml', $container->get('acf_wurfl.config')->wurflFile);
        $this->assertEquals('accuracy', $container->get('acf_wurfl.config')->matchMode);
        $cache = $container->get('acf_wurfl.config')->cache;
        $this->assertEquals('apc', $cache['provider']);
        $cache = $container->get('acf_wurfl.config')->persistence;
        $this->assertEquals('file', $cache['provider']);
        $this->assertEquals($container->getParameter('kernel.root_dir') . '/wurfl', $cache['params']['dir']);
    }

    public function testApcuCache()
    {
        $config = array(
            'wurfl_file' => __DIR__ . '/Resources/wurfl/wurfl-light.xml',
            'match_mode'   => 'accuracy',
            'cache' => 'apcu'
        );

        $container = $this->createCompiledContainerForConfig($config);

        $this->assertInstanceOf('WURFL_Configuration_InMemoryConfig', $container->get('acf_wurfl.config'));
        $this->assertEquals(__DIR__ . '/Resources/wurfl/wurfl-light.xml', $container->get('acf_wurfl.config')->wurflFile);
        $this->assertEquals('accuracy', $container->get('acf_wurfl.config')->matchMode);
        $cache = $container->get('acf_wurfl.config')->cache;
        $this->assertEquals('apcu', $cache['provider']);
        $cache = $container->get('acf_wurfl.config')->persistence;
        $this->assertEquals('file', $cache['provider']);
        $this->assertEquals($container->getParameter('kernel.root_dir') . '/wurfl', $cache['params']['dir']);
    }

    public function testInvalidMatchMode()
    {
        $config = array(
            'match_mode'   => 'not_valid',
        );

        try {
            $container = $this->createCompiledContainerForConfig($config);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException', $e);
            $this->assertEquals('Invalid configuration for path "acf_wurfl.match_mode": The match mode has to be "performance" or "accuracy"', $e->getMessage());

        }
    }

    public function testInvalidHandler()
    {
        $config = array(
            'cache'   => 'not_valid',
        );

        try {
            $container = $this->createCompiledContainerForConfig($config);
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Invalid handler type "not_valid" given', $e->getMessage());

        }
    }

    private function createCompiledContainerForConfig($config)
    {
        $container = $this->createContainer();
        $container->registerExtension(new ACFWurflExtension());
        $container->loadFromExtension('acf_wurfl', $config);
        $this->compileContainer($container);

        return $container;
    }

    private function createContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.cache_dir' => __DIR__,
            'kernel.root_dir'  => __DIR__,
            'kernel.charset'   => 'UTF-8',
            'kernel.debug'     => false,
        )));

        return $container;
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
    }
}
