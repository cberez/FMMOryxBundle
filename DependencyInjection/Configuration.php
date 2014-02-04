<?php

namespace FMM\OryxBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('fmm_oryx', 'array');

        $rootNode
            ->children()
                ->scalarNode('host')
                    ->defaultValue('localhost')
                    ->example('domain.com')
                    ->info('The hostname that runs the Oryx server')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('port')
                    ->defaultValue(8091)
                    ->example(1234)
                    ->info('The port that runs the Oryx server')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('username')
                    ->defaultNull()
                    ->example('cesar')
                    ->info('The username to connect to the Oryx server')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                    ->example('pa$$word')
                    ->info('The password to connect to the Oryx server')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
