<?php

use function DI\{autowire, create, get};
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
use Slim\Http\Environment as SlimEnvironment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Slim\Interfaces\RouterInterface;
use Slim\Router;
use Species\App\Environment;
use Species\App\Paths;

return [

    // Default settings
    'settings.baseUrl' => function (SlimEnvironment $slimEnvironment) {
        return Uri::createFromEnvironment($slimEnvironment)->getBaseUrl();
    },

    'settings.middleware' => [],
    'settings.routes' => [],

    'settings.httpVersion' => '1.1',
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => true,
    'settings.addContentLengthHeader' => true,
    'settings.displayErrorDetails' => function (Environment $env) {
        return $env->inDebug();
    },
    'settings.routerCacheFile' => function (Environment $env, Paths $paths) {
        return $env->hasCaching() ? $paths->getCachePathFor("$env/app.router") : false;
    },



    // Aliases
    ContainerInterface::class => get(Container::class),

    RequestInterface::class => get('request'),
    ServerRequestInterface::class => get('request'),

    Router::class => get('router'),
    RouterInterface::class => get('router'),

    SlimEnvironment::class => get('environment'),



    // Slim settings
    'settings' => [
        'httpVersion' => get('settings.httpVersion'),
        'responseChunkSize' => get('settings.responseChunkSize'),
        'outputBuffering' => get('settings.outputBuffering'),
        'determineRouteBeforeAppMiddleware' => get('settings.determineRouteBeforeAppMiddleware'),
        'displayErrorDetails' => get('settings.displayErrorDetails'),
        'addContentLengthHeader' => get('settings.addContentLengthHeader'),
        'routerCacheFile' => get('settings.routerCacheFile'),
    ],

    // Router
    'router' => create(Router::class)
        ->method('setContainer', get(ContainerInterface::class))
        ->method('setCacheFile', get('settings.routerCacheFile')),

    // Error handlers
    'errorHandler' => create(Error::class)->constructor(get('settings.displayErrorDetails')),
    'phpErrorHandler' => create(PhpError::class)->constructor(get('settings.displayErrorDetails')),
    'notFoundHandler' => create(NotFound::class),
    'notAllowedHandler' => create(NotAllowed::class),

    // Slim environment
    'environment' => function () {
        return new SlimEnvironment($_SERVER);
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
    'foundHandler' => create(ControllerInvoker::class)->constructor(get('foundHandler.invoker')),

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
    'callableResolver' => autowire(CallableResolver::class),

];
