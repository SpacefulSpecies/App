<?php

namespace Species\App;

use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder as DIContainerBuilder;

/**
 * Container builder for the app using PHP-DI.
 *
 * @todo apc cache for PHP-DI
 */
final class ContainerBuilder
{

    /** @const string */
    const SLIM_CONFIG_FILE = 'vendor/php-di/slim-bridge/src/config.php';

    /** @const string */
    const APP_CONFIG_FILE = __DIR__ . '/config.php';



    /** @var DIContainerBuilder */
    private $builder;

    /** @var Environment */
    private $environment;



    /**
     * @param Environment $environment
     * @return ContainerInterface
     */
    public static function buildForEnvironment(Environment $environment): ContainerInterface
    {
        return (new self($environment))->build();
    }



    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->builder = new DIContainerBuilder();
        $this->builder->useAutowiring(true);
        $this->builder->useAnnotations(false);
        $this->builder->ignorePhpDocErrors(true);

        if ($environment->hasCaching()) {
            $cachePath = $environment->createCachePathFor('app.container');
            $this->builder->setDefinitionCache(new FilesystemCache($cachePath));
            $this->builder->writeProxiesToFile(true, "$cachePath/container_proxies.cache");
        }

        $this->environment = $environment;
        $this->addDefinitions([Environment::class => $this->environment]);

        $this->provideSlimConfig();
        $this->provideAppConfig();
    }



    /**
     * @param mixed $definitions
     */
    public function addDefinitions($definitions): void
    {
        $this->builder->addDefinitions($definitions);
    }

    /**
     * @return ContainerInterface
     */
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
        if ($this->environment->hasCaching()) {
            $routerCacheFile = $this->environment->createCachePathFor('app.router');
        }

        $this->addDefinitions($this->environment->getRootPath() . '/' . self::SLIM_CONFIG_FILE);
        $this->addDefinitions([
            'settings.displayErrorDetails' => $this->environment->inDebug(),
            'settings.routerCacheFile' => $routerCacheFile,
        ]);
    }

    /**
     * Provide app settings and services.
     */
    private function provideAppConfig(): void
    {
        $configPath = $this->environment->getConfigPath();
        $envConfigPath = "$configPath/" . $this->environment->getName();

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
