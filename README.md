Species App
===========

Yet another Simple Application Framework.

Glue for [Slim](https://github.com/slimphp/Slim) and [Twig](https://github.com/twigphp/Twig/),
configured with [PHP-DI](https://github.com/PHP-DI/PHP-DI),
written in php 7.2!



## Installation

There is a [skeleton](https://github.com/SpacefulSpecies/AppSkeleton) available if you want to start a new project:
```bash
composer create-project species/app-skeleton my-project-path
```

Or use it as a library:
```bash
composer require species/app
```


## Middleware

Example how to add middleware with PHP-DI config:
````php
<?php 

use function DI\add;
use Species\App\Middleware;

return [
    'settings.middleware' => add([
        Middleware\TwigDebugMiddleware::class,
        Middleware\CsrfValidationMiddleware::class,
        Middleware\AddRouteNameToTwigMiddleware::class,
        // ...
    ]),
];
````

### AddRouteNameToTwigMiddleware

Will add `routeName` as a twig global variable, if it can find one.

### CsrfValidationMiddleware

Will test POST requests if the `csrfToken` field is valid. When invalid,
the session will be cleared, the session ID will be regenerated and an 
`InvalidCsrfToken` exception will be thrown.

### TwigDebugMiddleware

Will add the twig debug extension when twig debug is enabled.

### Slim HTTP cache

The [slim/http-cache](https://github.com/slimphp/Slim-HttpCache) library
is also packaged.

Example how to config this middleware:
````php
<?php 

use function DI\add;
use Slim\HttpCache\Cache as HttpCacheMiddleWare;

return [
    // Default settings
    'settings.httpCache.type' => 'private',
    'settings.httpCache.maxAge' => 86400,
    'settings.httpCache.mustRevalidate' => false,

    // Add middleware  
    'settings.middleware' => add([
        HttpCacheMiddleWare::class,
    ]),
];
````

## Twig extensions

Example how to add twig globals and extensions with config:
````php
<?php 

use function DI\add;
use Species\App\TwigExtension;

return [
    // Twig globals
    'settings.twig.globals' => add([
        'foo' => 'bar',
        // ...
    ]),
    
    // Twig extensions
    'settings.twig.extensions' => add([
        TwigExtension\CsrfTwigExtension::class,
        TwigExtension\ReflectionTwigExtension::class,
        TwigExtension\RouterTwigExtension::class,
        // ...
    ]),
];
````

### CsrfTwigExtension

Adds the function `csrfTokenInput()` to render the hidden input field.
Or use `csrfToken()` which returns only the token.
 
### ReflectionTwigExtension

Adds the following functions:
- `fqcn(object $object): string`
  Returns the fully qualified class name of given object.
- `className(object $object): string`
  Returns the class name of given object without namespace.
- `instanceOf(object $object, string $class): bool`
  Tests if given object is an instance of given class.

### RouterTwigExtension

Adds the global `baseUrl` and following functions:
- `pathFor(string $name, array $data = [], array $queryParams = []): string`
  Returns the path for given route.
- `urlFor(string $name, array $data = [], array $queryParams = []): string`
  Returns the full url for given route.
