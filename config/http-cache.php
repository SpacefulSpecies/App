<?php

use function DI\create;
use Psr\Container\ContainerInterface;
use Slim\HttpCache\Cache as HttpCacheMiddleWare;
use Slim\HttpCache\CacheProvider as HttpCacheProvider;

return [

    // Default settings
    'settings.httpCache.type' => 'private',
    'settings.httpCache.maxAge' => 86400,
    'settings.httpCache.mustRevalidate' => false,



    // Middleware
    HttpCacheMiddleWare::class => function (ContainerInterface $container) {
        return new HttpCacheMiddleWare(
            $container->get('settings.httpCache.type'),
            $container->get('settings.httpCache.maxAge'),
            $container->get('settings.httpCache.mustRevalidate')
        );
    },



    // Service
    HttpCacheProvider::class => create(),

];
