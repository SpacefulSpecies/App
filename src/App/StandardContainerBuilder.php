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

    /** @const string */
    const SLIM_CONFIG_FILE = 'vendor/php-di/slim-bridge/src/config.php';

    /** @const string */
    const APP_CONFIG_FILE = __DIR__ . '/config.php';



    /** @var DIContainerBuilder */
    private $builder;

    /** @var Environment */
    private $env;

    /** @var PathStructure */
    private $paths;



    /** @inheritdoc */
    public static function buildFrom(Environment $environment, PathStructure $pathStructure): ContainerInterface
    {
        return self::from($environment, $pathStructure)->build();
    }

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
        $routerCacheFile = false;
        if ($this->env->usesCaching()) {
            $routerCacheFile = $this->paths->getCachePathFor($this->env->getName() . '/app.router');
        }

        $this->addDefinitions($this->paths->getRootPath() . '/' . self::SLIM_CONFIG_FILE);
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
        $configPath = $this->paths->getConfigPath();
        $envConfigPath = $this->paths->getConfigPathFor($this->env->getName());

        $files = [
            self::APP_CONFIG_FILE,
            "$configPath/routes.php",
            "$envConfigPath/routes.php",
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $this->addDefinitions($file);
            }
        }
    }

}
