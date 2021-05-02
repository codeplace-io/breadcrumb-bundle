<?php
declare(strict_types=1);

namespace Thormeier\BreadcrumbBundle\Traits;

use Thormeier\BreadcrumbBundle\Provider\BreadcrumbProviderInterface;

trait BreadcrumbProviderAwareTrait
{
    private BreadcrumbProviderInterface $breadcrumbProvider;

    public function getBreadcrumbProvider(): BreadcrumbProviderInterface
    {
        return $this->breadcrumbProvider;
    }

    public function setBreadcrumbProvider(BreadcrumbProviderInterface $breadcrumbProvider): void
    {
        $this->breadcrumbProvider = $breadcrumbProvider;
    }
}
