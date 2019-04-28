<?php

use function DI\{autowire, create, get};
use Psr\Container\ContainerInterface;
use Slim\Views\Twig as TwigView;
use Species\App\Environment;
use Species\App\Middleware\TwigDebugMiddleware;
use Species\App\Middleware\TwigRouterMiddleware;
use Species\App\Paths;
use Species\App\TwigExtension\ReflectionTwigExtension;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;

return [

    // Default settings
    'settings.twig.charset' => 'utf-8',
    'settings.twig.strict_variables' => true,
    'settings.twig.autoescape' => 'html',
    'settings.twig.auto_reload' => null,
    'settings.twig.optimizations' => -1,
    'settings.twig.path' => function (Paths $paths) {
        return $paths->getResourcePathFor('twig');
    },
    'settings.twig.debug' => function (Environment $environment) {
        return $environment->inDebug();
    },
    'settings.twig.cache' => function (Environment $env, Paths $paths) {
        return $env->hasCaching() ? $paths->getCachePathFor("$env/app.twig") : false;
    },



    // Globals and extensions
    'settings.twig.globals' => [],
    'settings.twig.extensions' => [],



    // View
    TwigView::class => function (ContainerInterface $container) {

        $twig = new TwigView($container->get('settings.twig.path'), [
            'debug' => $container->get('settings.twig.debug'),
            'charset' => $container->get('settings.twig.charset'),
            'strict_variables' => $container->get('settings.twig.strict_variables'),
            'autoescape' => $container->get('settings.twig.autoescape'),
            'cache' => $container->get('settings.twig.cache'),
            'auto_reload' => $container->get('settings.twig.auto_reload'),
            'optimizations' => $container->get('settings.twig.optimizations'),
        ]);

        foreach ($container->get('settings.twig.globals') as $name => $value) {
            $twig->getEnvironment()->addGlobal($name, $value);
        }
        foreach ($container->get('settings.twig.extensions') as $routerExtension) {
            if ($routerExtension instanceof AbstractExtension) {
                $twig->addExtension($routerExtension);
            } else {
                $twig->addExtension($container->get($routerExtension));
            }
        }

        return $twig;
    },

    // Environment
    TwigEnvironment::class => function (TwigView $twigView) {
        return $twigView->getEnvironment();
    },

    // Twig middleware
    TwigDebugMiddleware::class => autowire(),
    TwigRouterMiddleware::class => autowire()->constructorParameter('baseUrl', get('settings.baseUrl')),

    // Twig extensions
    ReflectionTwigExtension::class => create(),

];
