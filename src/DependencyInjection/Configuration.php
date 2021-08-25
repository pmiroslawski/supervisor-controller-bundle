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
                        ->scalarNode('name')->isRequired()->defaultValue('messages')->end()
                        ->scalarNode('host')->isRequired()->defaultValue('http://127.0.0.1:15672/api/queues/%2F/messages')->end()
                        ->scalarNode('type')->isRequired()->defaultValue('rabbitmq')->end()
                        ->scalarNode('consumer')->isRequired()->defaultValue('messages_consumer')->end()
                        ->integerNode('numprocs')->isRequired()->defaultValue('30')->end()
                        ->arrayNode('thresholds')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                            ->children()
                                ->scalarNode('messages')->isRequired()->defaultValue('10')->end()
                                ->scalarNode('num')->isRequired()->defaultValue('3')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}