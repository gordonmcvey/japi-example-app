<?php
declare(strict_types=1);

use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
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
    PathNamespaceStrategy::class      => fn(ContainerInterface $container)
    => new PathNamespaceStrategy($container->get("controllerRoot")),
    RouterInterface::class            => fn(ContainerInterface $container)
    => new Router($container->get(PathNamespaceStrategy::class)),
    RequestMeta::class                => fn(ContainerInterface $container): RequestMeta
    => new RequestMeta($container->get("received")),
    Bootstrap::class                  => fn(ContainerInterface $container)
    => new Bootstrap(
        $container->get(RouterInterface::class),
        $container->get(ControllerFactoryInterface::class),
    ),
    JAPI::class                       => function (ContainerInterface $container) {
        (new JAPI($container->get(CallStackFactory::class), $container->get(ErrorHandlerInterface::class)))
            ->addMiddleware($container->get(RequestMeta::class))
            ->bootstrap(
                $container->get(Bootstrap::class),
                Request::fromSuperGlobals($container->get(JsonPayloadHandler::class)),
            )
        ;
    },
    Ping::class                       => fn(ContainerInterface $container): Ping
    => (new Ping())->addMiddleware($container->get(ProcessedTime::class))
];
