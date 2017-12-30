<?php

namespace Species\App;

use Species\App\Exception\InvalidEnvironmentRootPath;
use Species\App\Exception\InvalidEnvironmentName;
use Species\App\Exception\InvalidEnvironmentPath;

/**
 * App environment.
 */
final class AppEnvironment implements Environment
{

    /** @const string */
    const ENV_KEY_NAME = 'APP_ENV';

    /** @const string */
    const ENV_KEY_DEBUG = 'APP_DEBUG';

    /** @const string */
    const ENV_KEY_CACHE = 'APP_CACHE';



    /** @var string */
    private $rootPath;



    /** @var string */
    private $name = 'prod';

    /** @var bool */
    private $debug = false;

    /** @var bool */
    private $caching = true;



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
     * @param string $rootPath
     * @return self
     */
    public static function withRootPath(string $rootPath): self
    {
        return new self($rootPath);
    }



    /**
     * @param string $rootPath
     * @throws InvalidEnvironmentName
     * @throws InvalidEnvironmentPath
     * @throws InvalidEnvironmentRootPath
     */
    private function __construct(string $rootPath)
    {
        // test environment root path
        $this->rootPath = $this->sanitizePath($rootPath);
        if (!is_dir($this->rootPath)) {
            throw new InvalidEnvironmentRootPath($this->rootPath);
        }

        // use $_ENV for name, debug and caching
        $this->name = $this->sanitizeName(getenv(self::ENV_KEY_NAME) ?: $this->name);
        $this->debug = getenv(self::ENV_KEY_DEBUG) === '1';
        $this->caching = getenv(self::ENV_KEY_CACHE) !== '0';

        // resolve the default relative paths with root path
        $this->configPath = $this->sanitizeAbsolutePath($this->configPath);
        $this->resourcePath = $this->sanitizeAbsolutePath($this->resourcePath);
        $this->webPath = $this->sanitizeAbsolutePath($this->webPath);
        $this->varPath = $this->sanitizeAbsolutePath($this->varPath);
        $this->cachePath = $this->sanitizeAbsolutePath($this->cachePath);
        $this->logPath = $this->sanitizeAbsolutePath($this->logPath);
    }



    /**
     * @param string $name
     * @return self
     * @throws InvalidEnvironmentName
     */
    public function withName(string $name): self
    {
        $new = clone $this;
        $new->name = $this->sanitizeName($name);

        return $new;
    }

    /**
     * @param bool $debug = true
     * @return self
     */
    public function withDebug(bool $debug = true): self
    {
        $new = clone $this;
        $new->debug = $debug;

        return $new;
    }

    /**
     * @return self
     */
    public function withoutDebug(): self
    {
        $new = clone $this;
        $new->debug = false;

        return $new;
    }

    /**
     * @param bool $caching = true
     * @return self
     */
    public function withCache(bool $caching = true): self
    {
        $new = clone $this;
        $new->caching = $caching;

        return $new;
    }

    /**
     * @return self
     */
    public function withoutCache(): self
    {
        $new = clone $this;
        $new->caching = false;

        return $new;
    }

    /**
     * @param string $configPath
     * @return self
     * @throws InvalidEnvironmentPath
     */
    public function withConfigPath(string $configPath): self
    {
        $new = clone $this;
        $new->configPath = $this->sanitizeAbsolutePath($configPath);

        return $new;
    }

    /**
     * @param string $resourcePath
     * @return self
     * @throws InvalidEnvironmentPath
     */
    public function withResourcePath(string $resourcePath): self
    {
        $new = clone $this;
        $new->resourcePath = $this->sanitizeAbsolutePath($resourcePath);

        return $new;
    }

    /**
     * @param string $webPath
     * @return self
     * @throws InvalidEnvironmentPath
     */
    public function withWebPath(string $webPath): self
    {
        $new = clone $this;
        $new->webPath = $this->sanitizeAbsolutePath($webPath);

        return $new;
    }

    /**
     * @param string $varPath
     * @return self
     * @throws InvalidEnvironmentPath
     */
    public function withVarPath(string $varPath): self
    {
        $new = clone $this;
        $new->varPath = $this->sanitizeAbsolutePath($varPath);

        return $new;
    }

    /**
     * @param string $cachePath
     * @return self
     * @throws InvalidEnvironmentPath
     */
    public function withCachePath(string $cachePath): self
    {
        $new = clone $this;
        $new->cachePath = $this->sanitizeAbsolutePath($cachePath);

        return $new;
    }

    /**
     * @param string $logPath
     * @return AppEnvironment
     * @throws InvalidEnvironmentPath
     */
    public function withLogPath(string $logPath): self
    {
        $new = clone $this;
        $new->logPath = $this->sanitizeAbsolutePath($logPath);

        return $new;
    }



    /** @inheritdoc */
    public function getName(): string
    {
        return $this->name;
    }

    /** @inheritdoc */
    public function inDebug(): bool
    {
        return $this->debug;
    }

    /** @inheritdoc */
    public function hasCaching(): bool
    {
        return $this->caching;
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
    public function createVarPathFor(string $namespace): string
    {
        return sprintf('%s/%s/%s', $this->varPath, $this->name, $namespace);
    }

    /** @inheritdoc */
    public function createCachePathFor(string $namespace): string
    {
        return sprintf('%s/%s/%s', $this->cachePath, $this->name, $namespace);
    }

    /** @inheritdoc */
    public function createLogPathFor(string $namespace): string
    {
        return sprintf('%s/%s/%s', $this->logPath, $this->name, $namespace);
    }



    /**
     * @param string $name
     * @return string
     * @throws InvalidEnvironmentName
     */
    private function sanitizeName(string $name): string
    {
        $name = trim($name);

        if (preg_match('/^[\-\_a-z0-9]+$/i', $name) === 0) {
            throw new InvalidEnvironmentName($name);
        }

        return $name;
    }

    /**
     * @param string $directory
     * @return string
     * @throws InvalidEnvironmentPath
     */
    private function sanitizePath(string $directory): string
    {
        $sanitized = rtrim(trim($directory), '/');

        // do not allow empty paths and system root
        if ($sanitized === '') {
            throw new InvalidEnvironmentPath($directory);
        }

        return $sanitized;
    }

    /**
     * @param string $directory
     * @return string
     * @throws InvalidEnvironmentPath
     */
    private function sanitizeAbsolutePath(string $directory): string
    {
        $sanitized = $this->sanitizePath($directory);

        // resolve relative paths from the root path
        if (substr($sanitized, 0, 1) !== '/') {
            $sanitized = $this->rootPath . "/$sanitized";
        }

        return $sanitized;
    }

}
