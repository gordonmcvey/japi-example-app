<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use DI\ContainerBuilder as ContainerBuilderAlias;
use gordonmcvey\JAPI\ErrorToException;
use gordonmcvey\JAPI\JAPI;
use gordonmcvey\JAPI\ShutdownHandler;

require_once __DIR__ . '/../vendor/autoload.php';

// For live you don't want any error output.  You might want to use different values here for local development/testing
error_reporting(0);
ini_set('display_errors', false);
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$container = (new ContainerBuilderAlias())
    ->addDefinitions(__DIR__ . '/../containerconfig.php')
    ->build()
;
$container->set("received", $received);

register_shutdown_function($container->get(ShutdownHandler::class));

$container->get(JAPI::class);
