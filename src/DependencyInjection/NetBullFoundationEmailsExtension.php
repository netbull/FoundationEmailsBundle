<?php

namespace NetBull\FoundationEmailsBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NetBullFoundationEmailsExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('netbull_foundation_emails.templates_path', $config['templates_path']);
        $container->setParameter('netbull_foundation_emails.custom_inky_path', $config['custom_inky_path']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('console.yaml');

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['WebpackEncoreBundle'])) {
            $loader->load('twig_webpack_encore.yaml');
        } else {
            $loader->load('twig.yaml');
        }
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'netbull_foundation_emails';
    }
}
