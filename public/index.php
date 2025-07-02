<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use gordonmcvey\exampleapp\service\ControllerServiceProvider;
use gordonmcvey\exampleapp\service\ErrorHandlerServiceProvider;
use gordonmcvey\exampleapp\service\JapiServiceProvider;
use gordonmcvey\exampleapp\service\MiddlewareServiceProvider;
use gordonmcvey\exampleapp\service\RouterServiceProvider;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\JAPI\Bootstrap;
use gordonmcvey\JAPI\ErrorToException;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\ShutdownHandler;
use League\Container\Container;
use League\Container\ReflectionContainer;

require_once __DIR__ . '/../vendor/autoload.php';

// For live you don't want any error output.  You might want to use different values here for local development/testing
error_reporting(0);
ini_set('display_errors', false);
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$container = new Container();

$container
    ->delegate(new ReflectionContainer(true))
    ->defaultToShared()
;

$container->addServiceProvider(new ErrorHandlerServiceProvider());
register_shutdown_function($container->get(ShutdownHandler::class));

$container->add("received", $received);
$container->add("controllerRoot", "gordonmcvey\\exampleapp\\controller");

$container
    ->addServiceProvider(new MiddlewareServiceProvider())
    ->addServiceProvider(new ControllerServiceProvider())
    ->addServiceProvider(new RouterServiceProvider())
    ->addServiceProvider(new JapiServiceProvider())
    ->get(JAPI::class)
    ->bootstrap(
        $container->get(Bootstrap::class),
        Request::fromSuperGlobals($container->get(JsonPayloadHandler::class)),
    )
;
