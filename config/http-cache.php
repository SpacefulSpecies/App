<?php

namespace Species\App\Config;

use Psr\Container\ContainerInterface;
use Slim\HttpCache\Cache as CacheMiddleWare;
use Slim\HttpCache\CacheProvider;

return [

    // Default settings
    'settings.httpCache.type' => 'private',
    'settings.httpCache.maxAge' => 86400,
    'settings.httpCache.mustRevalidate' => false,



    // Middleware
    CacheMiddleWare::class => function (ContainerInterface $container) {
        return new CacheMiddleWare(
            $container->get('settings.httpCache.type'),
            $container->get('settings.httpCache.maxAge'),
            $container->get('settings.httpCache.mustRevalidate')
        );
    },



    // Service
    CacheProvider::class => \DI\object(),

];
