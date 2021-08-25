<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('bit9_supervisor_controller');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('queues')
                ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->defaultValue('messages')->end()
                        ->scalarNode('host')->defaultValue('http://127.0.0.1:15672/api/queues/%2F/messages')->end()
                        ->scalarNode('type')->defaultValue('rabbitmq')->end()
                        ->scalarNode('consumer')->defaultValue('messages_consumer')->end()
                        ->scalarNode('numprocs')->defaultValue('30')->end()
                        ->arrayNode('thresholds')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                            ->children()
                                ->scalarNode('messages')->defaultValue('10')->end()
                                ->scalarNode('num')->defaultValue('3')->end()
                            ->end()
                            ->children()
                                ->scalarNode('messages')->defaultValue('100')->end()
                                ->scalarNode('num')->defaultValue('10')->end()
                            ->end()
                            ->children()
                                ->scalarNode('messages')->defaultValue('200')->end()
                                ->scalarNode('num')->defaultValue('20')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}