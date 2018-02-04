<?php

namespace Species\App\Config;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig as TwigView;
use Slim\Views\TwigExtension as TwigViewExtension;
use Species\App\Environment;
use Species\App\Paths;
use Twig_Environment;
use Twig_Extension_Debug;

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



    // View
    TwigView::class => function (ContainerInterface $container, Environment $env, TwigViewExtension $extension) {

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

        $twig->addExtension($extension);
        if ($env->inDebug()) {
            $twig->addExtension(new Twig_Extension_Debug());
        }

        return $twig;
    },

    // View extension
    TwigViewExtension::class => function (RouterInterface $router, RequestInterface $request) {
        return new TwigViewExtension($router, $request->getUri());
    },

    // Environment
    Twig_Environment::class => function (TwigView $twigView) {
        return $twigView->getEnvironment();
    },

];
