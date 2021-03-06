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
                ->append($this->getCacheDriverNode('persistence'))
                ->append($this->getCacheDriverNode('cache'))
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Return a cache driver node
     *
     * @param string $name
     *
     * @return ArrayNodeDefinition
     */
    private function getCacheDriverNode($name)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($name);

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
                ->thenInvalid('The directory of the file cache must be set if using the file cache driver')
            ->end()
        ;

        return $node;
    }
}
