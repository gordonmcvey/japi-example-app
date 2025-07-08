<?php

declare(strict_types=1);

use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\JAPI\Bootstrap;
use gordonmcvey\JAPI\error\JsonErrorHandler;
use gordonmcvey\JAPI\interface\controller\ControllerFactoryInterface;
use gordonmcvey\JAPI\interface\error\ErrorHandlerInterface;
use gordonmcvey\JAPI\interface\routing\RouterInterface;
use gordonmcvey\JAPI\interface\routing\RoutingStrategyInterface;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\middleware\CallStackFactory;
use gordonmcvey\JAPI\routing\PathNamespaceStrategy;
use gordonmcvey\JAPI\routing\Router;
use Psr\Container\ContainerInterface;

return [
    // Attributes
    "jsonFlags"                       => DI\factory(fn() => !empty($_ENV["PRETTY_PRINT_JSON"]) ? JSON_PRETTY_PRINT : 0),
    "exposeErrorDetails"              => DI\factory(fn() => !empty($_ENV["DETAILED_ERROR_OUTPUT"])),
    // HTTP request
    RequestInterface::class           => fn(ContainerInterface $container): Request
        => Request::fromSuperGlobals($container->get(JsonPayloadHandler::class)),
    // Error handlers
    ErrorHandlerInterface::class      => DI\create(JsonErrorHandler::class)
        ->constructor(
            DI\get(StatusCodeFactory::class),
            DI\get("jsonFlags"),
            DI\get("exposeErrorDetails"),
        ),
    // Middleware
    RequestMeta::class                => DI\create(RequestMeta::class)
        ->constructor(DI\get("received")),
    // Controllers
    ControllerFactoryInterface::class => DI\get(DiControllerFactory::class),
    Ping::class                       => DI\create(Ping::class)
        ->method("addMiddleware", DI\get(ProcessedTime::class)),
    // Routing
    RoutingStrategyInterface::class   => DI\create(PathNamespaceStrategy::class)
        ->constructor(DI\env("APP_CONTROLLER_NAMESPACE_ROOT")),
    RouterInterface::class            => DI\create(Router::class)
        ->constructor(DI\get(RoutingStrategyInterface::class)),
    // JAPI
    Bootstrap::class                  => DI\create(Bootstrap::class)
        ->constructor(
            DI\get(RouterInterface::class),
            DI\get(ControllerFactoryInterface::class),
        ),
    JAPI::class                       => DI\create(JAPI::class)
        ->constructor(DI\get(CallStackFactory::class), DI\get(ErrorHandlerInterface::class))
        ->method("addMiddleware", DI\get(RequestMeta::class)),
];
