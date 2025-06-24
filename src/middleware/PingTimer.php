<?php

namespace gordonmcvey\exampleapp\middleware;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\httpsupport\response\ResponseInterface;
use gordonmcvey\JAPI\interface\controller\RequestHandlerInterface;
use gordonmcvey\JAPI\interface\middleware\MiddlewareInterface;
use JsonException;

readonly final class PingTimer implements MiddlewareInterface
{
    private const string TIMESTAMP_FORMAT = "Y-m-d\TH:i:s.uP";

    /**
     * @throws JsonException
     */
    public function handle(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->dispatch($request);
        $responseBody = (object) json_decode(json: $response->body(), flags: JSON_THROW_ON_ERROR);
        $responseBody->processed = (new DateTimeImmutable())->format(self::TIMESTAMP_FORMAT);
        $response->setBody(json_encode($responseBody));

        return $response;
    }
}
