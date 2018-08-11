<?php

namespace Species\App;

use DI\Definition\Source\SourceCache;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder as DIContainerBuilder;

/**
 * Standard container builder implementation using PHP-DI.
 */
final class StandardContainerBuilder implements ContainerBuilder
{

    /** @const string */
    const CONFIG_PATH = __DIR__ . '/../../config';



    /** @var Environment */
    private $env;

    /** @var Paths */
    private $paths;

    /** @var DIContainerBuilder */
    private $builder;



    /**
     * @param Environment $environment
     * @param Paths       $paths
     * @return self
     */
    public static function from(Environment $environment, Paths $paths): self
    {
        return new self($environment, $paths);
    }

    /**
     * @param Environment $environment
     * @param Paths       $paths
     * @return ContainerInterface
     */
    public static function buildFrom(Environment $environment, Paths $paths): ContainerInterface
    {
        return self::from($environment, $paths)->build();
    }



    /**
     * @param Environment $env
     * @param Paths       $paths
     */
    private function __construct(Environment $env, Paths $paths)
    {
        $this->env = $env;
        $this->paths = $paths;
        $this->builder = $this->createBuilder();

        $this->addDefinitions([Environment::class => $env, Paths::class => $paths]);
        $this->addDefinitionsFromPath(self::CONFIG_PATH);
        $this->addDefinitionsFromPath($paths->getConfigPath());
        $this->addDefinitionsFromPath($paths->getConfigPathFor("$env"));
    }



    /** @inheritdoc */
    public function build(): ContainerInterface
    {
        return $this->builder->build();
    }



    /**
     * @param mixed $definitions
     * @see \DI\ContainerBuilder::addDefinitions()
     */
    public function addDefinitions($definitions): void
    {
        $this->builder->addDefinitions($definitions);
    }

    /**
     * @param string $path
     */
    public function addDefinitionsFromPath(string $path): void
    {
        $path = realpath($path);
        if (!$path) {
            return;
        }

        if (is_dir($path)) {
            foreach (glob("$path/*.php") as $file) {
                $this->builder->addDefinitions($file);
            }
        } else {
            $this->builder->addDefinitions($path);
        }
    }



    /**
     * @return DIContainerBuilder
     */
    private function createBuilder(): DIContainerBuilder
    {
        $builder = (new DIContainerBuilder)
            ->useAutowiring(true)
            ->useAnnotations(false)
            ->ignorePhpDocErrors(true);

        if ($this->env->hasCaching()) {
            $cachePath = $this->paths->getCachePathFor("{$this->env}/app.container");
            $builder->enableCompilation("$cachePath.compiled");
            $builder->writeProxiesToFile(true, "$cachePath.proxies");
            if (SourceCache::isSupported()) {
                $builder->enableDefinitionCache();
            }
        }

        return $builder;
    }

}
