<?php

namespace Opensoft\FixturesGeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fixtures_generator');
        $rootNode
            ->children()
                ->scalarNode('directory')
                    ->defaultValue('app' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'fixtures')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
