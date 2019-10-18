<?php

namespace Ctrl\SQSInsightBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ctrl_sqs_insight');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('sqs_client')
                    ->isRequired()
                    ->children()
                        ->scalarNode('region')->isRequired()->end()
                        ->scalarNode('version')->isRequired()->end()
                        ->scalarNode('endpoint')->isRequired()->end()
                        ->arrayNode('credentials')
                            ->children()
                                ->scalarNode('key')->end()
                                ->scalarNode('secret')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ignoreQueues')->scalarPrototype()->end()->end()
                ->scalarNode('stripFromName')->defaultValue('')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
