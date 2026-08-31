<?php

namespace NetBull\FoundationEmailsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('netbull_foundation_emails');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('templates_path')->defaultValue('%kernel.project_dir%/templates/Emails')->end()
                ->scalarNode('custom_inky_path')->defaultNull()->end()
                ->scalarNode('rendered_templates_path')->defaultValue('%kernel.project_dir%/var/email_previews')->end()
            ->end();

        return $treeBuilder;
    }
}
