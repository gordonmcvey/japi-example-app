<?php
declare(strict_types=1);

use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\JAPI\Bootstrap;
use gordonmcvey\JAPI\error\JsonErrorHandler;
use gordonmcvey\JAPI\interface\controller\ControllerFactoryInterface;
use gordonmcvey\JAPI\interface\error\ErrorHandlerInterface;
use gordonmcvey\JAPI\interface\routing\RouterInterface;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\middleware\CallStackFactory;
use gordonmcvey\JAPI\routing\PathNamespaceStrategy;
use gordonmcvey\JAPI\routing\Router;
use Psr\Container\ContainerInterface;

return [
    "controllerRoot"                  => DI\string("gordonmcvey\\exampleapp\\controller"),
    ErrorHandlerInterface::class      => DI\get(JsonErrorHandler::class),
    ControllerFactoryInterface::class => DI\get(DiControllerFactory::class),
    PathNamespaceStrategy::class      => DI\create(PathNamespaceStrategy::class)
        ->constructor(DI\get("controllerRoot")),
    RouterInterface::class            => DI\create(Router::class)
        ->constructor(DI\get(PathNamespaceStrategy::class)),
    RequestMeta::class                => fn(ContainerInterface $container): RequestMeta
        => new RequestMeta($container->get("received")),
    Bootstrap::class                  => DI\create(Bootstrap::class)
        ->constructor(
            DI\get(RouterInterface::class),
            DI\get(ControllerFactoryInterface::class),
        ),
    RequestInterface::class           => fn(ContainerInterface $container):Request => Request::fromSuperGlobals($container->get(JsonPayloadHandler::class)),
    JAPI::class                       => function (ContainerInterface $container): JAPI {
        return (new JAPI($container->get(CallStackFactory::class), $container->get(ErrorHandlerInterface::class)))
            ->addMiddleware($container->get(RequestMeta::class))
        ;
    },
    Ping::class                       => DI\create(Ping::class)
        ->method("addMiddleware", DI\get(ProcessedTime::class)),
];
