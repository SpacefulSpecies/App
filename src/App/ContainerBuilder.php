<?php

namespace Species\App;

use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder as DIContainerBuilder;

/**
 * Build container using PHP-DI with config provided from the environment.
 *
 * @todo apc cache for PHP-DI
 */
final class ContainerBuilder
{

    /** @const string */
    const SLIM_DI_CONFIG_FILE = 'vendor/php-di/slim-bridge/src/config.php';



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
        $this->environment = $environment;
    }



    /**
     * @return ContainerInterface
     */
    public function build(): ContainerInterface
    {
        $builder = $this->createContainerBuilder();

        $this->provideSlimConfig($builder);
        $this->provideSpeciesConfig($builder);

        return $builder->build();
    }



    /**
     * @return DIContainerBuilder
     */
    private function createContainerBuilder(): DIContainerBuilder
    {
        $builder = new DIContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAnnotations(false);
        $builder->ignorePhpDocErrors(true);

        if ($this->environment->hasCaching()) {
            $cachePath = $this->environment->createCachePathFor('app.container');
            $builder->setDefinitionCache(new FilesystemCache($cachePath));
            $builder->writeProxiesToFile(true, "$cachePath/container_proxies.cache");
        }

        return $builder;
    }

    /**
     * @param DIContainerBuilder $builder
     */
    private function provideSlimConfig(DIContainerBuilder $builder): void
    {
        $defaultSlimConfig = $this->environment->getRootPath() . '/' . self::SLIM_DI_CONFIG_FILE;

        $routerCacheFile = false;
        if ($this->environment->hasCaching()) {
            $routerCacheFile = $this->environment->createCachePathFor('app.router');
        }

        $builder->addDefinitions($defaultSlimConfig);
        $builder->addDefinitions([
            'settings.displayErrorDetails' => $this->environment->inDebug(),
            'settings.routerCacheFile' => $routerCacheFile,
        ]);
    }

    /**
     * @param DIContainerBuilder $builder
     */
    private function provideSpeciesConfig(DIContainerBuilder $builder): void
    {
        $appFile = __DIR__ . '/config.php';
        $routesFile = $this->environment->getConfigPath() . '/routes.php';
        $builder->addDefinitions($appFile);
        $builder->addDefinitions($routesFile);
        $builder->addDefinitions([
            Environment::class => $this->environment,
        ]);
    }

}
