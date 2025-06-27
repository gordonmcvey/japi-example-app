<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use gordonmcvey\exampleapp\controller\health\Ping;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\JAPI\controller\ControllerFactory;
use gordonmcvey\JAPI\error\JsonErrorHandler;
use gordonmcvey\JAPI\ErrorToException;
use gordonmcvey\JAPI\interface\controller\RequestHandlerInterface;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\middleware\CallStackFactory;
use gordonmcvey\JAPI\routing\PathNamespaceStrategy;
use gordonmcvey\JAPI\routing\Router;
use gordonmcvey\JAPI\ShutdownHandler;

require_once __DIR__ . '/../vendor/autoload.php';

// For live you don't want any error output.  You might want to use different values here for local development/testing
error_reporting(0);
ini_set('display_errors', false);
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$errorHandler = new JsonErrorHandler(new StatusCodeFactory(), exposeDetails: true);

register_shutdown_function(new ShutdownHandler($errorHandler));

(new JAPI(new CallStackFactory(), $errorHandler))
    ->addMiddleware(new RequestMeta($received))
    ->bootstrap(
        function (RequestInterface $request): RequestHandlerInterface {
            $router = new Router(new PathNamespaceStrategy("gordonmcvey\\exampleapp\\controller"));
            $controller = (new ControllerFactory())->make($router->route($request));
            $controller instanceof Ping && $controller->addMiddleware(new ProcessedTime());

            return $controller;
        },
        Request::fromSuperGlobals(new JsonPayloadHandler()),
    )
;
