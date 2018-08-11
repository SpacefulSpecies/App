<?php

use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;
use Slim\Router;
use Species\App\Environment;
use Species\App\Paths;

return [

    // Default settings
    'settings.middleware' => [],
    'settings.routes' => [],

    'settings.httpVersion' => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    'settings.addContentLengthHeader' => true,
    'settings.displayErrorDetails' => function (Environment $env) {
        return $env->inDebug();
    },
    'settings.routerCacheFile' => function (Environment $env, Paths $paths) {
        return $env->hasCaching() ? $paths->getCachePathFor("$env/app.router") : false;
    },



    // Aliases
    ContainerInterface::class => \DI\get(Container::class),

    RequestInterface::class => \DI\get('request'),
    ServerRequestInterface::class => \DI\get('request'),

    Router::class => \DI\get('router'),
    RouterInterface::class => \DI\get('router'),



    // Slim settings
    'settings' => [
        'httpVersion' => \DI\get('settings.httpVersion'),
        'responseChunkSize' => \DI\get('settings.responseChunkSize'),
        'outputBuffering' => \DI\get('settings.outputBuffering'),
        'determineRouteBeforeAppMiddleware' => \DI\get('settings.determineRouteBeforeAppMiddleware'),
        'displayErrorDetails' => \DI\get('settings.displayErrorDetails'),
        'addContentLengthHeader' => \DI\get('settings.addContentLengthHeader'),
        'routerCacheFile' => \DI\get('settings.routerCacheFile'),
    ],

    // Router
    'router' => \DI\create(Router::class)
        ->method('setContainer', \DI\get(ContainerInterface::class))
        ->method('setCacheFile', \DI\get('settings.routerCacheFile')),

    // Error handlers
    'errorHandler' => \DI\create(Error::class)->constructor(\DI\get('settings.displayErrorDetails')),
    'phpErrorHandler' => \DI\create(PhpError::class)->constructor(\DI\get('settings.displayErrorDetails')),
    'notFoundHandler' => \DI\create(NotFound::class),
    'notAllowedHandler' => \DI\create(NotAllowed::class),

    // Slim environment
    'environment' => function () {
        return new \Slim\Http\Environment($_SERVER);
    },

    // HTTP factory
    'request' => function (ContainerInterface $container) {
        return Request::createFromEnvironment($container->get('environment'));
    },

    'response' => function (ContainerInterface $container) {
        $headers = new Headers(['Content-Type' => 'text/html; charset=utf-8']);
        $response = new Response(200, $headers);

        return $response->withProtocolVersion($container->get('settings.httpVersion'));
    },

    // HTTP handler
    'foundHandler' => \DI\create(ControllerInvoker::class)->constructor(\DI\get('foundHandler.invoker')),

    'foundHandler.invoker' => function (ContainerInterface $container) {
        $resolvers = [
            // inject parameters by name first.
            new AssociativeArrayResolver,
            // then inject services by type-hints for those that weren't resolved.
            new TypeHintContainerResolver($container),
            // then fall back on parameters default values for optional route parameters.
            new DefaultValueResolver(),
        ];
        return new Invoker(new ResolverChain($resolvers), $container);
    },

    // Callable resolver
    'callableResolver' => \DI\autowire(CallableResolver::class),

];
