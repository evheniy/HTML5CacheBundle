<?php

namespace Evheniy\HTML5CacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Evheniy\HTML5CacheBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('html5_cache');
        $rootNode
            ->children()
                ->scalarNode('cdn')->defaultValue('')->end()
                ->booleanNode('http')->defaultTrue()->end()
                ->booleanNode('https')->defaultTrue()->end()
                ->arrayNode('custom_paths')->prototype('scalar')->end()->end()
            ->end();

        return $treeBuilder;
    }
}