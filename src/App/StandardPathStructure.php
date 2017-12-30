<?php

namespace Species\App;

use Species\App\Exception\InvalidRootPath;
use Species\App\Exception\InvalidPath;

/**
 * Standard path structure implementation.
 */
final class StandardPathStructure implements PathStructure
{

    /** @var string */
    private $rootPath;

    /** @var string */
    private $configPath = 'config';

    /** @var string */
    private $resourcePath = 'resources';

    /** @var string */
    private $webPath = 'web';

    /** @var string */
    private $varPath = 'var';

    /** @var string */
    private $cachePath = 'var/cache';

    /** @var string */
    private $logPath = 'var/logs';



    /**
     * Relative paths will be resolved from the root path.
     *
     * @param string $rootPath
     * @return self
     */
    public static function withRootPath(string $rootPath): self
    {
        return new self($rootPath);
    }



    /**
     * @param string $rootPath
     * @throws InvalidRootPath
     * @throws InvalidPath
     */
    private function __construct(string $rootPath)
    {
        try {
            $this->rootPath = realpath($rootPath);
            $this->guardValidPath($this->rootPath);
        } catch (\Throwable $e) {
            throw new InvalidRootPath();
        }

        $this->configPath = $this->resolvePath($this->configPath);
        $this->resourcePath = $this->resolvePath($this->resourcePath);
        $this->webPath = $this->resolvePath($this->webPath);
        $this->varPath = $this->resolvePath($this->varPath);
        $this->cachePath = $this->resolvePath($this->cachePath);
        $this->logPath = $this->resolvePath($this->logPath);
    }



    /** @inheritdoc */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /** @inheritdoc */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /** @inheritdoc */
    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    /** @inheritdoc */
    public function getWebPath(): string
    {
        return $this->webPath;
    }

    /** @inheritdoc */
    public function getVarPath(): string
    {
        return $this->varPath;
    }

    /** @inheritdoc */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    /** @inheritdoc */
    public function getLogPath(): string
    {
        return $this->logPath;
    }



    /** @inheritdoc */
    public function getConfigPathFor(string $namespace): string
    {
        return $this->configPath . "/$namespace";
    }

    /** @inheritdoc */
    public function getResourcePathFor(string $namespace): string
    {
        return $this->resourcePath . "/$namespace";
    }

    /** @inheritdoc */
    public function getWebPathFor(string $namespace): string
    {
        return $this->webPath . "/$namespace";
    }

    /** @inheritdoc */
    public function getVarPathFor(string $namespace): string
    {
        return $this->varPath . "/$namespace";
    }

    /** @inheritdoc */
    public function getCachePathFor(string $namespace): string
    {
        return $this->cachePath . "/$namespace";
    }

    /** @inheritdoc */
    public function getLogPathFor(string $namespace): string
    {
        return $this->logPath . "/$namespace";
    }



    /**
     * @param string $configPath
     * @return self
     * @throws InvalidPath
     */
    public function withConfigPath(string $configPath): self
    {
        $new = clone $this;
        $new->configPath = $this->resolvePath($configPath);

        return $new;
    }

    /**
     * @param string $resourcePath
     * @return self
     * @throws InvalidPath
     */
    public function withResourcePath(string $resourcePath): self
    {
        $new = clone $this;
        $new->resourcePath = $this->resolvePath($resourcePath);

        return $new;
    }

    /**
     * @param string $webPath
     * @return self
     * @throws InvalidPath
     */
    public function withWebPath(string $webPath): self
    {
        $new = clone $this;
        $new->webPath = $this->resolvePath($webPath);

        return $new;
    }

    /**
     * @param string $varPath
     * @return self
     * @throws InvalidPath
     */
    public function withVarPath(string $varPath): self
    {
        $new = clone $this;
        $new->varPath = $this->resolvePath($varPath);

        return $new;
    }

    /**
     * @param string $cachePath
     * @return self
     * @throws InvalidPath
     */
    public function withCachePath(string $cachePath): self
    {
        $new = clone $this;
        $new->cachePath = $this->resolvePath($cachePath);

        return $new;
    }

    /**
     * @param string $logPath
     * @return self
     * @throws InvalidPath
     */
    public function withLogPath(string $logPath): self
    {
        $new = clone $this;
        $new->logPath = $this->resolvePath($logPath);

        return $new;
    }



    /**
     * @param string $path
     * @throws InvalidPath
     */
    private function guardValidPath(string $path): void
    {
        // only allow absolute paths that are not system root
        if (substr($path, 0, 1) !== '/' || $path === '/') {
            throw new InvalidPath($path);
        }
    }

    /**
     * @param string $path
     * @return string
     * @throws InvalidPath
     */
    private function resolvePath(string $path): string
    {
        $path = rtrim(trim($path), '/');

        // relative to root path
        if (substr($path, 0, 1) !== '/') {
            $path = $this->rootPath . "/$path";
        }

        $this->guardValidPath($path);

        return $path;
    }

}
