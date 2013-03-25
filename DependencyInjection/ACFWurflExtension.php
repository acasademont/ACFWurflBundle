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

        $container->getDefinition('acf_wurfl.config')
            ->addMethodCall('wurflFile', array($config['wurfl_file']))
            ->addMethodCall('matchMode', array($config['match_mode']))
            ->addMethodCall('persistence', $this->getCacheArguments($config['persistence']))
            ->addMethodCall('cache', $this->getCacheArguments($config['cache']));

        $container
            ->setDefinition('acf_wurfl', new Definition('WURFL_WURFLManager'))
            ->setFactory(array(new Reference('acf_wurfl.manager_factory'), 'create'));
    }

    private function getCacheArguments($config)
    {
        switch($config['type']) {
            case 'file':
                return array('file', array(
                    'dir'   => $config['dir']
                ));
                break;
            case 'apc':
                return array('apc', array());
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid handler type "%s" given', $config['type']));
        }
    }
}
