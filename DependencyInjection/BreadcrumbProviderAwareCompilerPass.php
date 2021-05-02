<?php
declare(strict_types=1);

namespace Thormeier\BreadcrumbBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class BreadcrumbProviderAwareCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $breadcrumbProvider = new Reference('Thormeier\BreadcrumbBundle\Provider\BreadcrumbProviderInterface');
        
        foreach ($container->findTaggedServiceIds(Tag::BREADCRUMB_PROVIDER_AWARE) as $id => $attrs) {
            $container->getDefinition($id)->addMethodCall('setBreadcrumbProvider', [$breadcrumbProvider]);
        }
    }
}
