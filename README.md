Species App
===========

WIP!

Yet another *Simple Application Framework*.

Glue for [Slim](https://github.com/slimphp/Slim) and [Twig](https://github.com/twigphp/Twig/),
configured with [PHP-DI](https://github.com/PHP-DI/PHP-DI),
written in php 7.1!



## Installation

There is a [skeleton](https://github.com/SpacefulSpecies/AppSkeleton) if you want to start a new project:
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

// but to keep things nice, use its factory methods!
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



## ContainerBuilder

The container builder has to make sure that the necessary settings and services are available to the app.
It needs a `\Species\App\Environment` and a `\Species\App\PathStructure` to create a `PSR-11 container`.

But no stress, there is an implementation to help you out!
You can use `\Species\App\StandardContainerBuilder` which loads the required app configurations for you.
```php
// when you only use the standard config files
$container = StandardContainerBuilder::buildFrom($environment, $pathStructure);

// you can add more PHP-DI definitions on top of the standard config files
$builder = StandardContainerBuilder::from($environment, $pathStructure);
$builder->addDefinitions([ /* ... */ ]);

// StandardContainerBuilder is mutable.
$builder->addDefinitions('/path/to/config/file);

// build the container
$container = $builder->build();
```



## App

This is just a `Slim` wrapper, using middleware and routes from config found in the container.

```php
// in short
\Species\App::runInRootPath(dirname(__DIR__);

// in long
$environment = \Species\App\StandardEnvironment::fromPhpEnv();
$pathStructure = \Species\App\StandardPathStructure::withRootPath(dirname(__DIR_));
$containerBuilder = \Species\App\StandardContainerBuilder::from($environment, $pathStructure);
$container = $containerBuilder->build();
$app = \Species\App::fromContainer($container);
$app->run();
```



## Twig

@todo 



## Config

@todo
