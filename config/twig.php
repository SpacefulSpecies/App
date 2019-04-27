<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig as TwigView;
use Slim\Views\TwigExtension as RouterTwigExtension;
use Species\App\Environment;
use Species\App\Paths;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\DebugExtension;

return [

    // Default settings
    'settings.twig.charset' => 'utf-8',
    'settings.twig.base_template_class' => 'Twig_Template',
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
    'settings.twig.globals' => [],
    'settings.twig.extensions' => [],



    // View
    TwigView::class => function (ContainerInterface $container, Environment $env, RouterTwigExtension $routerExtension) {

        $twig = new TwigView($container->get('settings.twig.path'), [
            'debug' => $container->get('settings.twig.debug'),
            'charset' => $container->get('settings.twig.charset'),
            'base_template_class' => $container->get('settings.twig.base_template_class'),
            'strict_variables' => $container->get('settings.twig.strict_variables'),
            'autoescape' => $container->get('settings.twig.autoescape'),
            'cache' => $container->get('settings.twig.cache'),
            'auto_reload' => $container->get('settings.twig.auto_reload'),
            'optimizations' => $container->get('settings.twig.optimizations'),
        ]);

        $twig->addExtension($routerExtension);
        if ($env->inDebug()) {
            $twig->addExtension(new DebugExtension());
        }

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

    // Router extension
    RouterTwigExtension::class => function (RouterInterface $router, RequestInterface $request) {
        return new RouterTwigExtension($router, $request->getUri());
    },

    // Environment
    TwigEnvironment::class => function (TwigView $twigView) {
        return $twigView->getEnvironment();
    },

];
