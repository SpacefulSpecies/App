<?php

namespace Species;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as Slim;
use Species\App\StandardEnvironment;
use Species\App\StandardContainerBuilder;
use Species\App\StandardPathStructure;
use Species\App\Exception\InvalidContainerConfig;
use Species\App\Exception\UnableToRunApp;

/**
 * App using Slim.
 */
final class App
{

    /** @var ContainerInterface */
    private $container;

    /** @var Slim */
    private $slim;



    /**
     * @param string $rootPath
     * @throws UnableToRunApp
     */
    public static function runInRootPath(string $rootPath): void
    {
        self::fromContainer(
            StandardContainerBuilder::buildFrom(
                StandardEnvironment::fromPhpEnv(),
                StandardPathStructure::withRootPath($rootPath)
            )
        )->run();
    }

    /**
     * @param ContainerInterface $container
     * @return self
     */
    public static function fromContainer(ContainerInterface $container): self
    {
        return new self($container);
    }



    /**
     * @param ContainerInterface $container
     * @throws InvalidContainerConfig
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->slim = new Slim($container);

        try {

            $this->provideMiddleware($container->get('app.middleware'));
            $this->provideRoutes($container->get('app.routes'));

        } catch (ContainerExceptionInterface $e) {
            throw new InvalidContainerConfig($e);
        }
    }



    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }



    /**
     * @throws UnableToRunApp
     */
    public function run(): void
    {
        try {
            $this->slim->run();
        } catch (\Exception $e) {
            throw new UnableToRunApp($e);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     * @throws UnableToRunApp
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            return $this->slim->process($request, $response);
        } catch (\Exception $e) {
            throw new UnableToRunApp($e);
        }
    }



    /**
     * @param array $middlewares
     */
    private function provideMiddleware(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->slim->add($middleware);
        }
    }

    /**
     * @param array  $routesConfig
     * @param string $groupName = ''
     */
    private function provideRoutes(array $routesConfig, string $groupName = ''): void
    {
        $app = $this; // because Slim binds the group closure to itself
        foreach ($routesConfig as $name => $route) {

            $name = $groupName ? "$groupName.$name" : $name;
            $pattern = $route['pattern'] ?? null;
            $group = $route['group'] ?? null;
            $methods = $route['methods'] ?? ['GET'];
            $handler = $route['handler'] ?? null;
            $middlewares = $route['middleware'] ?? [];

            if ($group !== null) {
                $stack = $this->slim->group($pattern, function () use ($app, $group, $name) {
                    $app->provideRoutes($group, $name);
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
