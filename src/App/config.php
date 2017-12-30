<?php

namespace Species\App;

use Psr\Http\Message\RequestInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;



return [

    # Aliases
    RouterInterface::class => \Di\get('router'),
    RequestInterface::class => \Di\get('request'),

    # Twig view templates
    Twig::class => function (Environment $env, RouterInterface $router, RequestInterface $request): Twig {

        $debug = $env->inDebug();
        $templatePath = $env->getResourcePath() . '/twig';
        $cachePath = $env->hasCaching() ? $env->createCachePathFor('app.twig') : false;

        $twig = new Twig($templatePath, [
            'debug' => $debug,
            'cache' => $cachePath,
            'strict_variables' => true,
            'autoescape' => 'html',
        ]);

        $twig->addExtension(new TwigExtension($router, $request->getUri()));
        $debug && $twig->addExtension(new Twig_Extension_Debug());

        return $twig;
    },

];
