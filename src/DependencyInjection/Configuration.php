<?php
namespace Povs\ListerTwigBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('povs_lister_twig');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('view_types')
                ->requiresAtLeastOneElement()
                ->scalarPrototype()->end()
                ->defaultValue(['list'])
            ->end()
            ->arrayNode('resolvable_types')
                ->requiresAtLeastOneElement()
                ->scalarPrototype()->end()
                ->defaultValue(['list', 'export'])
            ->end()
            ->scalarNode('default_type')->defaultValue('list')->end()
            ->arrayNode('request')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('type')->defaultValue('lister_type')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}