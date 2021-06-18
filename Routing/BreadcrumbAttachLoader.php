<?php

namespace Thormeier\BreadcrumbBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Attaches breadcrumb tree to every routes default config
 */
class BreadcrumbAttachLoader extends Loader
{
    /**
     * @var LoaderInterface
     */
    private $routerLoader;
    private ?Request $request;
    private string $defaultLocale;

    /**
     * Attaches breadcrumb tree to every routes default config
     *
     * @param LoaderInterface $routerLoader
     * @param RequestStack $requestStack
     * @param string $defaultLocale
     */
    public function __construct(LoaderInterface $routerLoader, RequestStack $requestStack, string $defaultLocale)
    {
        $this->routerLoader = $routerLoader;
        $this->request = $requestStack->getMasterRequest();
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $routeCollection = $this->routerLoader->load($resource, $type);

        foreach ($routeCollection->all() as $key => $route) {
            if ($route->hasOption('breadcrumb')) {
                $route->setDefault(
                    '_breadcrumbs',
                    $this->getBreadcrumbs($route, $key, $routeCollection)
                );
            }
        }

        return $routeCollection;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return $this->routerLoader->supports($resource, $type);
    }

    /**
     * Builds an array of breadcrumbs for the given route recursively
     *
     * @param Route           $route
     * @param string          $routeKey
     * @param RouteCollection $routeCollection
     * @param array           $rawBreadcrumbsCollection
     *
     * @return array
     */
    private function getBreadcrumbs(Route $route, $routeKey, RouteCollection $routeCollection, $rawBreadcrumbsCollection = array())
    {
        $breadcrumbOptions = $route->getOption('breadcrumb');

        // No label, no crumb.
        if (false === isset($breadcrumbOptions['label'])) {
            throw new \InvalidArgumentException(sprintf(
                'Label for breadcrumb on route "%s" must be configured',
                $routeKey
            ));
        }

        $rawCrumb = array(
            'route' => $routeKey,
            'label' => $breadcrumbOptions['label'],
        );

        // If this route already is in the raw collection, there's likely a circular breadcrumb, which will cause memory exhaustion
        if (false !== array_search($rawCrumb, $rawBreadcrumbsCollection)) {
            throw new \LogicException(sprintf(
                'Circular breadcrumbs detected at route "%s"',
                $routeKey
            ));
        }

        // Add element to beginning of breadcrumbs
        array_unshift($rawBreadcrumbsCollection, $rawCrumb);

        // If there's a parent, add it and its parents as well
        if (isset($breadcrumbOptions['parent_route'])) {
            $parentRoute = $this->replaceLocaleParameterFromRouteName($breadcrumbOptions['parent_route']);
            $rawBreadcrumbsCollection = $this->getBreadcrumbs(
                $routeCollection->get($parentRoute),
                $parentRoute,
                $routeCollection,
                $rawBreadcrumbsCollection
            );
        }

        return $rawBreadcrumbsCollection;
    }

    private function getCurrentLocale(): string
    {
        if (null === $this->request) {
            return $this->defaultLocale;
        }

        return $this->request->getLocale() ?? $this->defaultLocale;
    }

    private function replaceLocaleParameterFromRouteName(string $routeName): string
    {
        return str_replace('{_locale}', $this->getCurrentLocale(), $routeName);
    }
}
