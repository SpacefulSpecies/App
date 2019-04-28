<?php

use function DI\{autowire, create};
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Psr\Container\ContainerInterface;
use Species\App\Middleware\CsrfValidationMiddleware;

return [

    // Default settings - null values are untouched
    'settings.session.name' => null,
    'settings.session.lifetime' => null,
    'settings.session.path' => null,
    'settings.session.domain' => null,
    'settings.session.secure' => null,
    'settings.session.httponly' => null,



    // Session
    SessionFactory::class => create(),
    Session::class => function (SessionFactory $factory, ContainerInterface $container) {
        $sessionName = $container->get('settings.session.name');
        $cookieParams = [
            'lifetime' => $container->get('settings.session.lifetime'),
            'path' => $container->get('settings.session.path'),
            'domain' => $container->get('settings.session.domain'),
            'secure' => $container->get('settings.session.secure'),
            'httponly' => $container->get('settings.session.httponly'),
        ];
        $cookieParams = array_filter($cookieParams, function ($value) {
            return $value !== null;
        });

        $session = $factory->newInstance($_COOKIE);

        if ($sessionName !== null) {
            $session->setName($sessionName);
        }
        if (!empty($cookieParams)) {
            $session->setCookieParams($cookieParams);
        }

        return $session;
    },


    // Csrf validation middleware
    CsrfValidationMiddleware::class => autowire(),

];
