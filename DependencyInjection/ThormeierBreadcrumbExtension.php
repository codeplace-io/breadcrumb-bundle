<?php

namespace Thormeier\BreadcrumbBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Thormeier\BreadcrumbBundle\Contracts\BreadcrumbProviderAwareInterface;

/**
 * Symfony DI extension
 *
 * @codeCoverageIgnore
 */
class ThormeierBreadcrumbExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('thormeier_breadcrumb.template', $config['template']);
        $container->setParameter('thormeier_breadcrumb.class.model', $config['model_class']);
        $container->setParameter('thormeier_breadcrumb.class.collection', $config['collection_class']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->tagServices($container, BreadcrumbProviderAwareInterface::class, Tag::BREADCRUMB_PROVIDER_AWARE);
    }

    private function tagServices(ContainerBuilder $container, string $interface, $tag): void
    {
        $serviceDefinition = $container->registerForAutoconfiguration($interface);
        $serviceDefinition->addTag($tag);
    }
}
