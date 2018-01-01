Species App
===========

Yet another Simple Application Framework.

Glue for [Slim](https://github.com/slimphp/Slim) and [Twig](https://github.com/twigphp/Twig/),
configured with [PHP-DI](https://github.com/PHP-DI/PHP-DI),
written in php 7.1!



## Installation

There is a [skeleton](https://github.com/SpacefulSpecies/AppSkeleton) available if you want to start a new project:
```bash
composer create-project species/app-skeleton my-project-dir
```

Or use it as a library in your project:
```bash
composer require species/app
```



## Usage


### In short:

```php
\Species\App::runInRootPath(dirname(__DIR__);
```
... assuming you are fine with all the defaults and that your configuration files are in place.


### In long:

```php
$environment = \Species\App\StandardEnvironment::fromPhpEnv();
$pathStructure = \Species\App\StandardPathStructure::withRootPath(dirname(__DIR_));
$containerBuilder = \Species\App\StandardContainerBuilder::from($environment, $pathStructure);
$container = $containerBuilder->build();
$app = \Species\App::fromContainer($container);
$app->run();
```

Start of with a `\Species\App\Environment` to know its name and whether there's debug or caching to do.

You also need a `\Species\App\PathStructure` to know where to put or get things, like `PHP-DI configuration files`.

Those are required by `\Species\App\ContainerBuilder` to build a suitable container for the app.

Feed that `PSR-11 container` to `\Species\App` so it can process `PSR-7 HTTP message`.  

That's it! But if you really want to know more, just keep on reading... 



## Environment and PathStructure

Don't worry, there are standard implementations for both:
`\Species\App\StandardEnvironment` and `\Species\App\StandardPathStructure`.

They are immutable! Here some examples:
```php
// create a StandardEnvironment with the constructor
$env = new StandardEnvironment('my-env-name', true, false);

// to keep things nice, use its factory methods!
// this one uses the $_ENV vars APP_ENV, APP_DEBUG and APP_CACHE 
$env = StandardEnvironment::fromPhpEnv();

// build your own environment (keep in mind that they are immutable!)
$production = StandardEnvironment::forProduction();
$staging = $production
    ->withName('staging')
    ->withoutCaching();

// the same rules apply for StandardPathStructure
$paths = StandardPathStructure::withRootPath(dirname(__DIR__))
    ->withVarPath('/absolute/path/outside/app/for/writes')
    ->withWebPath('relative/path/to/public/my/hoster/forces/me/to')
    ->withResourcePath('because/assets/is/a/better/name');

// relative paths will be resolved with the root path
$paths = StandardPathStructure::withRootPath('/path-to-app')
    ->withResourcePath('data/assets');
echo $paths->getResourcePath(); // /path-to-app/data/assets
```

The default StandardPathStructure is:
```
- project root
    - config
    - resources
    - var
        - cache
        - logs
    - web
```


## ContainerBuilder

The container builder has to make sure that the necessary settings and services are available to the app.
It needs a `\Species\App\Environment` and a `\Species\App\PathStructure` to create a `PSR-11 container`.

But no stress, as you probably already could guess, ... there is an implementation available to help you out!
You can use `\Species\App\StandardContainerBuilder` which loads the required app configurations for you using `PHP-DI`.
```php
// when you only use the project config files
$container = StandardContainerBuilder::buildFrom($environment, $pathStructure);

// you can add more PHP-DI definitions on top of the project config files
$builder = StandardContainerBuilder::from($environment, $pathStructure);
$builder->addDefinitions(['some-key' => 'some-value']);
$builder->addDefinitions('/path/to/config/file');

// build the container
$container = $builder->build();
```
The `StandardContainerBuilder` will load these configurations in order:
- slim settings and services from config file used by [php-di/slim-bridge](https://github.com/PHP-DI/Slim-Bridge)
- override slim settings with environment and path variables (debug, cache) 
- app settings and services used by the framework (like Twig)
- config files from {configPath} in alphabetic order
- environment specific config files from {configPath}/{envName} in alphabetic order
- definitions added with ->addDefinition() in the order they were added



## App

This is a `Slim` wrapper, using middleware and routes that are defined in the container.

```php
$app = \Species\App::fromContainer($container);
$app->run();
```
Since it's build on Slim, it requires the slim settings and services defined in the container.
If you make your own container, take a look in `\Species\App\StandardContainerBuilder` on how easy it this.


## Twig

Twig is included, but optional to ise. There is also a helper class `\Species\App\TwigViewController` to extend from:
```php
final class ExampleController extends \Species\App\TwigViewController
{
    public function home(ResponseInterface $response, SomeService $service): ResponseInterface
    {
        return $this->render($response, 'home.twig', [
             'someTemplateData' => $service->fetchSomeValue();
        ]);
    }
}
```
The Twig environment is automatically configured for you:
- stores the templates in {resourcesPath}/twig
- cache if the environment allows it in {cachePath}/app.twig
- debug value of the environment, with debug extension
- router extension so you can use stuff like path_for('name-of-route') and base_url() in your templates
- auto escape html



## Config

Config files resides in the config path from `PathStructure` using [PHP definitions](http://php-di.org/doc/php-definitions.html).

The StandardContainerBuilder will automatically load all config files from {configPath} and {configPath}/{envName}.
You can load more files when using the container builder.


### Safety first

It's recommended that you track your config files with git,
so you know which parameters/services are required in your project without messing with .dist extensions,
but **leave sensitive data blank**.
Then create an environment specific configuration file, that you ignore in git, with the sensitive data filled in.

An example to show how easy this is:
```php
// config/parameters.php stored in git
return [
	'pdo.driver' => 'mysql',
	'pdo.username' => '',
	'pdo.password' => '',
];

// config/dev/parameters.php ignored in git
return [
	'pdo.username' => 'dev',
	'pdo.password' => '1234',
];

```
Your .gitignore rule can look like this: 
```gitignore
/config/*/
```

This also gives you the possibility to store different configurations on the same machine.

To destroy the purpose of this topic:
Ever been naughty for quickly testing production data on your local development machine?
Just switch the environment name to override your container with other configurations,
like connecting to your production database.


### Required container keys for Species\App

#### (array) app.middleware
An array of middleware used by the application. Can be left empty. Too bad it's not PSR-15... waiting on Slim 4.
Middleware can be any callable that implements the following signature:
```php
function (ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface;
```
Signature of $next:
```php
function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
```
Example:
```php
function ($request, $response, $next) {
	// do stuff with $request
	$response = $next($request, $response);
	// do stuff with $response
	return $response;
}
```

#### (array) app.routes
An array of routes. An example will explain its structure best:
```php
return [
    'app.routes' => [

        // minimal configuration
        'home' => [
            'pattern' => '/',
            'handler' => MyHomePageController::class,
        ],

        // all configuration
        'contact-form' => [
            'pattern' => '/contact-form',
            // any callable/invokable will work, and dependencies will auto-wire thanks to PHP-DI!
            'handler' => [MyContactPageController::class, 'form'],
            // one or more HTTP methods, defaults to ['GET'] when omitted
            'methods' => ['GET', 'POST'],
            // add middleware on specific routes
            'middleware' => [MyCsrfTokenMiddleware::class, MySessionMiddleware::class],
        ],

        // you can group routes
        'api' => [
            'pattern' => '/api', // prefix pattern
            'group' => [
                'post' => [
                    'pattern' => '/post/{id}',
                    // auto-wiring to the rescue, order of args doesn't even matter
                    'handler' => function (ResponseInterface $response, string $id) {
                        // do stuff with $id and $response
                        return $response;
                    },
                ],
                'post-edit' => [
                    'pattern' => '/post/{id}',
                    'methods' => ['UPDATE'],
                    'handler' => [MyRestController::class, 'updatePost'],
                ],
                // you can even nest groups ... endlessly
                'v1' => [
                    'pattern' => '/v1', // will actually match /api/v1
                    'group' => [/* routes and/or other groups */],
                    // groups can have middleware for their routes too
                    'middleware' => [MyStripHeadersMiddleware::class],
                ],
            ],
        ],

    ],
];
```
