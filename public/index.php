<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use gordonmcvey\exampleapp\controller\health\Ping;
use gordonmcvey\exampleapp\middleware\PingTimer;
use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\httpsupport\request\payload\JsonPayloadHandler;
use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\JAPI\controller\ControllerFactory;
use gordonmcvey\JAPI\error\JsonErrorHandler;
use gordonmcvey\JAPI\interface\controller\RequestHandlerInterface;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\middleware\CallStackFactory;
use gordonmcvey\JAPI\routing\PathNamespaceStrategy;
use gordonmcvey\JAPI\routing\Router;

require_once __DIR__ . '/../vendor/autoload.php';

(new JAPI(new CallStackFactory(), new JsonErrorHandler(new StatusCodeFactory(), exposeDetails: true)))
    ->addMiddleware(new RequestMeta($received))
    ->bootstrap(
        function (RequestInterface $request): RequestHandlerInterface {
            $router = new Router(new PathNamespaceStrategy("gordonmcvey\\exampleapp\\controller"));
            $controller = (new ControllerFactory())->make($router->route($request));
            $controller instanceof Ping && $controller->addMiddleware(new PingTimer());

            return $controller;
        },
        Request::fromSuperGlobals(new JsonPayloadHandler()),
    )
;
