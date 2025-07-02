<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\factory\DiControllerFactory;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\payload\PayloadHandlerInterface;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\JAPI\Bootstrap;
use gordonmcvey\JAPI\error\JsonErrorHandler;
use gordonmcvey\JAPI\ErrorToException;
use gordonmcvey\JAPI\interface\controller\ControllerFactoryInterface;
use gordonmcvey\JAPI\interface\error\ErrorHandlerInterface;
use gordonmcvey\JAPI\interface\routing\RouterInterface;
use gordonmcvey\JAPI\interface\routing\RoutingStrategyInterface;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\middleware\CallStackFactory;
use gordonmcvey\JAPI\routing\PathNamespaceStrategy;
use gordonmcvey\JAPI\routing\Router;
use gordonmcvey\JAPI\ShutdownHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

require_once __DIR__ . '/../vendor/autoload.php';

// For live you don't want any error output.  You might want to use different values here for local development/testing
error_reporting(0);
ini_set('display_errors', false);
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$container = new ContainerBuilder();
$container->set("received", $received);
$container->setParameter("controllerRoot", "gordonmcvey\\exampleapp\\controller");
$container->register(StatusCodeFactory::class, StatusCodeFactory::class);
$container->register(ErrorHandlerInterface::class, JsonErrorHandler::class)
    ->addArgument(new Reference(StatuscodeFactory::class))
    ->addArgument(JSON_PRETTY_PRINT)
    ->addArgument(true);

$container->register(ShutdownHandler::class, ShutdownHandler::class)
    ->addArgument(new Reference(ErrorHandlerInterface::class));

$container->register(RequestMeta::class, RequestMeta::class)
    ->addArgument(new Reference("received"));

$container->register(ProcessedTime::class, ProcessedTime::class);
$container->register(Ping::class, Ping::class)
    ->addMethodCall("addMiddleware", [new Reference(ProcessedTime::class)]);

$container->register(CallstackFactory::class, CallstackFactory::class);
$container->register(RoutingStrategyInterface::class, PathNamespaceStrategy::class)
    ->addArgument("%controllerRoot%");

$container->register(RouterInterface::class, Router::class)
    ->addArgument(new Reference(RoutingStrategyInterface::class));

$container->register(ControllerFactoryInterface::class, DiControllerFactory::class)
    ->addArgument($container);

$container->register(PayloadHandlerInterface::class, JsonPayloadHandler::class);

$container->register(Bootstrap::class, Bootstrap::class)
    ->addArgument(new Reference(RouterInterface::class))
    ->addArgument(new Reference(ControllerFactoryInterface::class));

$container->register(JAPI::class, JAPI::class)
    ->addArgument(new Reference(CallStackFactory::class))
    ->addArgument(new Reference(ErrorHandlerInterface::class))
    ->addMethodCall("addMiddleware", [new Reference(RequestMeta::class)]);

register_shutdown_function($container->get(ShutdownHandler::class));

$container->get(JAPI::class)
    ->bootstrap(
        $container->get(Bootstrap::class),
        Request::fromSuperGlobals($container->get(PayloadHandlerInterface::class)),
    )
;
