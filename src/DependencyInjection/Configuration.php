<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('bit9_supervisor_controller');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('queues')
                ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->defaultValue('messages')->end()
                        ->scalarNode('consumer')->defaultValue('messages_consumer')->end()
                        ->scalarNode('numprocs')->defaultValue('30')->end()
                        ->arrayNode('thresholds')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                            ->children()
                                ->scalarNode('messages')->end()
                                ->scalarNode('num')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}