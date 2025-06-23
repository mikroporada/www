<?php

namespace App;

use DI\Container;
use DI\Definition\Definition;
use DI\Definition\Source\ArrayDefinitionSource;
use DI\Definition\Source\DefinitionSource;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;
use Slim\Routing\RouteContext;

class App
{
    private ContainerInterface $container;
    private App $slimApp;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->slimApp = new App($container);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getSlimApp(): App
    {
        return $this->slimApp;
    }

    public function run(): void
    {
        $this->slimApp->run();
    }

    public function get(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->get($pattern, $callable);
    }

    public function post(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->post($pattern, $callable);
    }

    public function put(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->put($pattern, $callable);
    }

    public function delete(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->delete($pattern, $callable);
    }

    public function patch(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->patch($pattern, $callable);
    }

    public function options(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->options($pattern, $callable);
    }

    public function any(string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->any($pattern, $callable);
    }

    public function map(array $methods, string $pattern, $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->map($methods, $pattern, $callable);
    }

    public function group(string $pattern, callable $callable): RouteCollectorProxyInterface
    {
        return $this->slimApp->group($pattern, $callable);
    }

    public function getRouteCollector(): RouteCollectorProxyInterface
    {
        return $this->slimApp->getRouteCollector();
    }

    public function getRouteParser(): RouteParserInterface
    {
        return $this->slimApp->getRouteParser();
    }

    public function getRouter(): RouterInterface
    {
        return $this->slimApp->getRouter();
    }

    public function getResponse(): Response
    {
        return $this->slimApp->getResponseFactory()->createResponse();
    }

    public function getUri(): Uri
    {
        return $this->slimApp->getUri();
    }

    public function getRouteContext(): RouteContext
    {
        return $this->slimApp->getRouteContext();
    }

    public function getRouteParser(): RouteParserInterface
    {
        return $this->slimApp->getRouteParser();
    }

    public function getRouteCollector(): RouteCollectorProxyInterface
    {
        return $this->slimApp->getRouteCollector();
    }

    public function getRouter(): RouterInterface
    {
        return $this->slimApp->getRouter();
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->slimApp->getResponseFactory();
    }
}
