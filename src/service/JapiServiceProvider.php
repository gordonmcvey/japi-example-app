<?php

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\response\sender\ResponseSenderInterface;
use gordonmcvey\JAPI\Bootstrap;
use gordonmcvey\JAPI\interface\controller\ControllerFactoryInterface;
use gordonmcvey\JAPI\interface\error\ErrorHandlerInterface;
use gordonmcvey\JAPI\interface\routing\RouterInterface;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\middleware\CallStackFactory;
use League\Container\ServiceProvider\AbstractServiceProvider;

class JapiServiceProvider extends AbstractServiceProvider
{
    private const array PROVIDED = [
        Bootstrap::class,
        ControllerFactoryInterface::class,
        JAPI::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }

    public function register(): void
    {
        $this->container->add(ControllerFactoryInterface::class, DiControllerFactory::class)->addArgument($this->container);
        $this->container->add(Bootstrap::class)->addArguments([RouterInterface::class, ControllerFactoryInterface::class]);
        $this->container->add(JAPI::class)
            ->addArguments([CallStackFactory::class, ErrorHandlerInterface::class, ResponseSenderInterface::class])
            ->addMethodCall("addMiddleware", [RequestMeta::class])
        ;
    }
}
