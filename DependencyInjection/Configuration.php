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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('acf_wurfl');

        $rootNode
            ->children()
                ->scalarNode('wurfl_file')->cannotBeEmpty()->defaultValue('%kernel.root_dir%/../vendor/acasademont/wurfl/examples/resources/wurfl.zip')->end()
                ->scalarNode('match_mode')
                    ->defaultValue('performance')
                    ->validate()
                        ->ifNotInArray(array('performance', 'accuracy'))
                        ->thenInvalid('The match mode has to be "performance" or "accuracy"')
                    ->end()
                ->end()
                ->booleanNode('debug')->defaultValue('%kernel.debug%')->end()
                ->append($this->getStorageDriverNode())
                ->append($this->getCacheDriverNode())
                ->arrayNode('capabilities_filter')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('storage_path')->defaultValue('%kernel.root_dir%/wurfl')->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Return a storage driver node
     *
     * @return ArrayNodeDefinition
     */
    private function getStorageDriverNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('storage');

        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifString()
                ->then(function($v) { return array('type' => $v); })
            ->end()
            ->children()
                ->scalarNode('type')->defaultValue('file')->end()
                ->scalarNode('dir')->defaultValue('%kernel.root_dir%/wurfl')->end()
            ->end()
            ->validate()
                ->ifTrue(function($v) { return 'file' === $v['type'] && empty($v['dir']); })
                ->thenInvalid('The directory of the file storage must be set if using the file storage driver')
            ->end()
        ;

        return $node;
    }

    /**
     * Return a cache driver node
     *
     * @return ArrayNodeDefinition
     */
    private function getCacheDriverNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('cache');

        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
            ->ifString()
            ->then(function($v) { return array('type' => $v); })
            ->end()
            ->children()
            ->scalarNode('type')->defaultValue('memory')->end()
            ->scalarNode('dir')->defaultValue('%kernel.root_dir%/wurfl')->end()
            ->end()
            ->validate()
            ->ifTrue(function($v) { return 'file' === $v['type'] && empty($v['dir']); })
            ->thenInvalid('The directory of the file cache must be set if using the file cache driver')
            ->end()
        ;

        return $node;
    }
}
