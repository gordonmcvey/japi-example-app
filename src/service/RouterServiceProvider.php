<?php

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\JAPI\interface\routing\RouterInterface;
use gordonmcvey\JAPI\interface\routing\RoutingStrategyInterface;
use gordonmcvey\JAPI\routing\PathNamespaceStrategy;
use gordonmcvey\JAPI\routing\Router;
use League\Container\ServiceProvider\AbstractServiceProvider;

class RouterServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        RoutingStrategyInterface::class,
        RouterInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container
            ->add(
                RoutingStrategyInterface::class,
                PathNamespaceStrategy::class,
            )
            ->addArgument("controllerRoot")
        ;

        $this->container
            ->add(
                RouterInterface::class,
                Router::class,
            )->addArgument(RoutingStrategyInterface::class)
        ;
    }
}
