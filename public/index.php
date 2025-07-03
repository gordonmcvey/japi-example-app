<?php

declare(strict_types=1);

$received = new DateTimeImmutable();

use gordonmcvey\httpsupport\request\Request;
use gordonmcvey\JAPI\ErrorToException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

require_once __DIR__ . '/../vendor/autoload.php';

// For live you don't want any error output.  You might want to use different values here for local development/testing
error_reporting(0);
ini_set('display_errors', false);
set_error_handler(new errorToException(), E_ERROR ^ E_USER_ERROR ^ E_COMPILE_ERROR);

$container = new ContainerBuilder();

$fileLoader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/../config/"));
$fileLoader->load("errorhandler.xml");

register_shutdown_function($container->get("ShutdownHandler"));

$container->set("received", $received);
$container->set("containerObject", $container);

$fileLoader->load("middleware.xml");
$fileLoader->load("controllers.xml");
$fileLoader->load("routing.xml");
$fileLoader->load("frontcontroller.xml");

$container->get("JAPI")
    ->bootstrap(
        $container->get("Bootstrap"),
        Request::fromSuperGlobals($container->get("PayloadHandlerInterface")),
    )
;
