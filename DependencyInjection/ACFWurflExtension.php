<?php

/*
 * This file is part of the ACFWurflBundle.
 *
 * (c) Albert Casademont <albertcasademont@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ACF\Bundle\WurflBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ACFWurflExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container
            ->setDefinition('acf_wurfl.storage', new Definition('ScientiaMobile\WURFL\Storage\StorageAdapterInterface'))
            ->setPublic(false)
            ->setFactory([new Reference('acf_wurfl.storage_factory'), 'create' . ucfirst($config['storage']['type'] . 'Storage')])
            ->setArguments($this->getCacheArguments($config['storage']))
        ;
        $container
            ->setDefinition('acf_wurfl.cache', new Definition('ScientiaMobile\WURFL\Cache\CacheAdapterInterface'))
            ->setPublic(false)
            ->setFactory([new Reference('acf_wurfl.cache_factory'), 'create' . ucfirst($config['cache']['type'] . 'Cache')])
            ->setArguments($this->getCacheArguments($config['cache']))
        ;

        $container->getDefinition('acf_wurfl.container')
            ->addMethodCall('get', ['settings'])
            ->addMethodCall('setStorageAdapter', [new Reference('acf_wurfl.storage')])
            ->addMethodCall('setCacheAdapter', [new Reference('acf_wurfl.cache')])
            ->setArguments([[
                'wurfl_db'                  => $config['wurfl_file'],
                'wurfl_debug'               => $config['debug'],
                'wurfl_capabilities_filter' => $config['capabilities_filter'],
                'wurfl_storage_path'        => $config['storage_path'],
            ]])
        ;

        if ('performance' === $config['match_mode']) {
            $container->getDefinition('acf_wurfl.engine')->addMethodCall('enablePerformanceMode');
        }

        $container->setAlias('acf_wurfl', 'acf_wurfl.engine');
    }

    private function getCacheArguments($config)
    {
        switch($config['type']) {
            case 'file':
                return [$config['dir']];
            case 'apc':
            case 'apcu':
            case 'memory':
                return [];
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid handler type "%s" given', $config['type']));
        }
    }
}
