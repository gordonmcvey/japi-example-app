<?php

namespace gordonmcvey\exampleapp\factory;

use gordonmcvey\exampleapp\controller\Health\Ping;
use gordonmcvey\exampleapp\middleware\ProcessedTime;
use gordonmcvey\JAPI\controller\ControllerFactory;
use gordonmcvey\JAPI\Exceptions\Routing;
use gordonmcvey\JAPI\interface\controller\RequestHandlerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiControllerFactory extends ControllerFactory
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function __invoke(string $path): RequestHandlerInterface
    {
        $checkedPath = $this->checkControllerExists($path);
        $controller = $this->container->get($checkedPath);
        return $this->checkIsController($controller, $checkedPath);

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Routing
     */
    public function make(string $path): RequestHandlerInterface
    {
        $checkedPath = $this->checkControllerExists($path);
        $controller = $this->container->get($checkedPath);
        return $this->checkIsController($controller, $checkedPath);
    }

    public function withArguments(...$arguments): self
    {
        // The container should already be wired with the relevant arguments
        return $this;
    }
}
