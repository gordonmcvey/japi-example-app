<?php

namespace gordonmcvey\exampleapp\controller\health;

use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\httpsupport\response\Response;
use gordonmcvey\httpsupport\response\ResponseInterface;
use gordonmcvey\JAPI\interface\controller\RequestHandlerInterface;
use gordonmcvey\JAPI\interface\middleware\MiddlewareProviderInterface;
use gordonmcvey\JAPI\middleware\MiddlewareProviderTrait;

class Ping implements RequestHandlerInterface, MiddlewareProviderInterface
{
    use MiddlewareProviderTrait;

    public function dispatch(RequestInterface $request): ?ResponseInterface
    {
        return new Response(
            responseCode: SuccessCodes::OK,
            body: (string) json_encode([
                "requestId" => $request->header(RequestMeta::HEADER_REQUEST_ID),
                'healthy'   => true,
                "received"  => $request->header(RequestMeta::HEADER_RECEIVED, "unknown"),
            ]),
            contentType: 'application/json',
            encoding: 'utf-8',
        );
    }
}
