<?php

namespace Species\App;

use Psr\Http\Message\RequestInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;



return [

    # Twig
    Twig::class => function (Environment $env, Paths $paths, TwigExtension $extension): Twig {

        $debug = $env->inDebug();
        $templatePath = $paths->getResourcePathFor('/twig');
        $cachePath = $env->hasCaching() ? $paths->getCachePathFor('app.twig') : false;

        $twig = new Twig($templatePath, [
            'debug' => $debug,
            'cache' => $cachePath,
            'autoescape' => 'html',
        ]);

        $twig->addExtension($extension);
        if ($debug) {
            $twig->addExtension(new Twig_Extension_Debug());
        }

        return $twig;
    },
    TwigExtension::class => function (RouterInterface $router, RequestInterface $request): TwigExtension {
        return new TwigExtension($router, $request->getUri());
    },

];
