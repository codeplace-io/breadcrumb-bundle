<?php
declare(strict_types=1);

namespace Thormeier\BreadcrumbBundle\Contracts;

use Thormeier\BreadcrumbBundle\Provider\BreadcrumbProviderInterface;

interface BreadcrumbProviderAwareInterface
{
    public function getBreadcrumbProvider(): BreadcrumbProviderInterface;
    public function setBreadcrumbProvider(BreadcrumbProviderInterface $breadcrumbProvider): void;
}
