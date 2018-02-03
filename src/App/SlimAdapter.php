<?php

namespace Species\App;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as Slim;
use Species\App\Exception\InvalidContainerConfiguration;
use Species\App\Exception\UnableToRunApp;

/**
 * Slim app adapter.
 */
abstract class SlimAdapter implements Application
{

    /** @var ContainerInterface */
    private $container;

    /** @var Slim */
    private $slim;



    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        try {
            $this->slim = new Slim($container);
            $this->provideMiddleware($container->get('settings.middleware'));
            $this->provideRoutes($container->get('settings.routes'));
        } catch (ContainerExceptionInterface|\Throwable $e) {
            throw new InvalidContainerConfiguration($e);
        }
    }



    /** @inheritdoc */
    final public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /** @inheritdoc */
    final public function run(): void
    {
        try {
            $this->slim->run(false);
        } catch (\Throwable $e) {
            throw new UnableToRunApp($e);
        }
    }

    /** @inheritdoc */
    final public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            return $this->slim->process($request, $response);
        } catch (\Throwable $e) {
            throw new UnableToRunApp($e);
        }
    }



    /**
     * @param array $middlewares
     */
    final private function provideMiddleware(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->slim->add($middleware);
        }
    }

    /**
     * @param array  $routesConfig
     * @param string $prefix = ''
     */
    final private function provideRoutes(array $routesConfig, string $prefix = ''): void
    {
        $app = $this; // because Slim binds the group closure to itself
        foreach ($routesConfig as $name => $route) {
            $name = $prefix . $name;
            $pattern = $route['pattern'] ?? null;
            $group = $route['group'] ?? null;
            $methods = $route['methods'] ?? ['GET'];
            $handler = $route['handler'] ?? null;
            $middlewares = $route['middleware'] ?? [];

            if ($group !== null) {
                $stack = $this->slim->group($pattern, function () use ($app, $group, $name) {
                    $app->provideRoutes($group, "$name.");
                });
            } else {
                $stack = $this->slim->map($methods, $pattern, $handler)->setName($name);
            }

            foreach ($middlewares as $middleware) {
                $stack->add($middleware);
            }
        }
    }

}
