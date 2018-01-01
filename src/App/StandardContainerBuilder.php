<?php

namespace Species\App;

use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder as DIContainerBuilder;

/**
 * Standard container builder implementation.
 *
 * @todo apc cache for PHP-DI
 */
final class StandardContainerBuilder implements ContainerBuilder
{

    /**
     * Slim config file relative to app root path.
     *
     * @const string
     */
    const SLIM_CONFIG_FILE = 'vendor/php-di/slim-bridge/src/config.php';

    /** @const string */
    const APP_CONFIG_FILE = __DIR__ . '/config.php';



    /** @var DIContainerBuilder */
    private $builder;

    /** @var Environment */
    private $env;

    /** @var PathStructure */
    private $paths;



    /**
     * @param Environment   $environment
     * @param PathStructure $pathStructure
     * @return self
     */
    public static function from(Environment $environment, PathStructure $pathStructure): self
    {
        return new self($environment, $pathStructure);
    }

    /**
     * @param Environment   $environment
     * @param PathStructure $pathStructure
     * @return ContainerInterface
     */
    public static function buildFrom(Environment $environment, PathStructure $pathStructure): ContainerInterface
    {
        return self::from($environment, $pathStructure)->build();
    }



    /**
     * @param Environment   $environment
     * @param PathStructure $pathStructure
     */
    public function __construct(Environment $environment, PathStructure $pathStructure)
    {
        $this->env = $environment;
        $this->paths = $pathStructure;

        $this->builder = new DIContainerBuilder();
        $this->builder->useAutowiring(true);
        $this->builder->useAnnotations(false);
        $this->builder->ignorePhpDocErrors(true);

        if ($this->env->usesCaching()) {
            $cachePath = $pathStructure->getCachePathFor($this->env->getName() . '/app.container');
            $this->builder->setDefinitionCache(new FilesystemCache($cachePath));
            $this->builder->writeProxiesToFile(true, "$cachePath/container_proxies.cache");
        }

        $this->addDefinitions([Environment::class => $this->env, PathStructure::class => $this->paths]);

        $this->provideSlimConfig();
        $this->provideAppConfig();
    }



    /** @inheritdoc */
    public function addDefinitions($definitions): void
    {
        $this->builder->addDefinitions($definitions);
    }

    /** @inheritdoc */
    public function build(): ContainerInterface
    {
        return $this->builder->build();
    }



    /**
     * Provide Slim settings and services.
     */
    private function provideSlimConfig(): void
    {
        // slim config
        $this->addDefinitions($this->paths->getRootPath() . '/' . self::SLIM_CONFIG_FILE);

        // slim router caching
        $routerCacheFile = false;
        if ($this->env->usesCaching()) {
            $routerCacheFile = $this->paths->getCachePathFor($this->env->getName() . '/app.router');
        }

        // override slim settings
        $this->addDefinitions([
            'settings.displayErrorDetails' => $this->env->inDebug(),
            'settings.routerCacheFile' => $routerCacheFile,
        ]);
    }

    /**
     * Provide app settings and services.
     */
    private function provideAppConfig(): void
    {
        // app config
        $this->addDefinitions(self::APP_CONFIG_FILE);

        // config files
        $configPath = $this->paths->getConfigPath();
        foreach (glob("$configPath/*.php") as $file) {
            $this->addDefinitions($file);
        }

        // environment specific config files
        $envConfigPath = $this->paths->getConfigPathFor($this->env->getName());
        foreach (glob("$envConfigPath/*.php") as $file) {
            $this->addDefinitions($file);
        }
    }

}
