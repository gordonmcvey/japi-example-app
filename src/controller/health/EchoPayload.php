<?php

declare(strict_types=1);

namespace gordonmcvey\exampleapp\controller\health;

use gordonmcvey\exampleapp\middleware\RequestMeta;
use gordonmcvey\httpsupport\enum\statuscodes\ClientErrorCodes;
use gordonmcvey\httpsupport\enum\statuscodes\SuccessCodes;
use gordonmcvey\httpsupport\enum\Verbs;
use gordonmcvey\httpsupport\request\RequestInterface;
use gordonmcvey\httpsupport\response\Response;
use gordonmcvey\httpsupport\response\ResponseInterface;
use gordonmcvey\JAPI\Exceptions\Routing;
use gordonmcvey\JAPI\interface\controller\RequestHandlerInterface;
use JsonException;

class EchoPayload implements RequestHandlerInterface
{
    private const array ALLOWED_METHODS = [
        Verbs::POST,
        Verbs::PUT,
    ];

    /**
     * @throws Routing
     */
    public function dispatch(RequestInterface $request): ?ResponseInterface
    {
        if (!in_array($request->verb(), self::ALLOWED_METHODS)) {
            // @todo We need more exception types
            throw new Routing("Method not allowed", ClientErrorCodes::METHOD_NOT_ALLOWED->value);
        }

        try {
            $payload = json_decode($request->body(), flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Routing("Payload is not valid JSON", ClientErrorCodes::BAD_REQUEST->value, $e);
        }

        return new Response(
            responseCode: SuccessCodes::OK,
            body: (string) json_encode([
                "requestId" => $request->header(RequestMeta::HEADER_REQUEST_ID, "unknown"),
                "received"  => $request->header(RequestMeta::HEADER_RECEIVED, "unknown"),
                "payload"   => $payload,
            ]),
            contentType: "application/json",
            encoding: "utf-8",
        );
    }
}
