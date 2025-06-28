<?php

declare(strict_types=1);

namespace gordonmcvey\exampleapp\service;

use gordonmcvey\httpsupport\enum\factory\StatusCodeFactory;
use gordonmcvey\JAPI\error\JsonErrorHandler;
use gordonmcvey\JAPI\interface\error\ErrorHandlerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ErrorHandlerServiceProvider  extends AbstractServiceProvider
{
    private const array PROVIDED = [
        ErrorHandlerInterface::class,
    ];

    public function provides(string $id): bool
    {
        return in_array($id, self::PROVIDED);
    }


    public function register(): void
    {
        $this->container->add(ErrorHandlerInterface::class, JsonErrorHandler::class)
            ->addArguments([StatusCodeFactory::class, JSON_PRETTY_PRINT, true]);
    }
}
