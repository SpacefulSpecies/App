<?php

namespace Species\App;

use Species\App\Exception\InvalidRootPath;
use Species\App\Exception\InvalidAbsolutePath;

/**
 * Standard app paths implementation.
 *
 * @note Relative paths will be resolved from the root path.
 */
final class StandardPaths implements Paths
{

    /** @const string */
    const ENV_KEY_CONFIG_PATH = 'APP_CONFIG_PATH';

    /** @const string */
    const ENV_KEY_RESOURCE_PATH = 'APP_RESOURCE_PATH';

    /** @const string */
    const ENV_KEY_WEB_PATH = 'APP_WEB_PATH';

    /** @const string */
    const ENV_KEY_VAR_PATH = 'APP_VAR_PATH';

    /** @const string */
    const ENV_KEY_CACHE_PATH = 'APP_CACHE_PATH';

    /** @const string */
    const ENV_KEY_LOG_PATH = 'APP_LOG_PATH';



    /** @var string */
    private $rootPath;

    /** @var string */
    private $configPath;

    /** @var string */
    private $resourcePath;

    /** @var string */
    private $webPath;

    /** @var string */
    private $varPath;

    /** @var string */
    private $cachePath;

    /** @var string */
    private $logPath;



    /**
     * @param string $rootPath
     * @return self
     * @throws InvalidRootPath
     * @throws InvalidAbsolutePath
     */
    public static function withRootPath(string $rootPath): self
    {
        return new self($rootPath);
    }

    /**
     * @param string $rootPath
     * @return StandardPaths
     * @throws InvalidRootPath
     * @throws InvalidAbsolutePath
     */
    public static function fromPhpEnvWithRootPath(string $rootPath): self
    {
        return new self(
            $rootPath,
            getenv(self::ENV_KEY_CONFIG_PATH) ?: null,
            getenv(self::ENV_KEY_RESOURCE_PATH) ?: null,
            getenv(self::ENV_KEY_WEB_PATH) ?: null,
            getenv(self::ENV_KEY_VAR_PATH) ?: null,
            getenv(self::ENV_KEY_CACHE_PATH) ?: null,
            getenv(self::ENV_KEY_LOG_PATH) ?: null
        );
    }



    /**
     * @param string      $rootPath
     * @param string|null $configPath   = null (default: '{rootPath}/config')
     * @param string|null $resourcePath = null (default: '{rootPath}/resources')
     * @param string|null $webPath      = null (default: '{rootPath}/web')
     * @param string|null $varPath      = null (default: '{rootPath}/var')
     * @param string|null $cachePath    = null (default: '{varPath}/cache')
     * @param string|null $logPath      = null (default: '{varPath}/logs')
     * @throws InvalidRootPath
     * @throws InvalidAbsolutePath
     */
    private function __construct(
        string $rootPath,
        ?string $configPath = null,
        ?string $resourcePath = null,
        ?string $webPath = null,
        ?string $varPath = null,
        ?string $cachePath = null,
        ?string $logPath = null
    )
    {
        try {
            $this->rootPath = realpath($rootPath);
            $this->assertValidAbsolutePath($this->rootPath);
        } catch (\Throwable $e) {
            throw new InvalidRootPath($rootPath);
        }

        $this->configPath = $this->resolvePath($configPath ?? 'config');
        $this->resourcePath = $this->resolvePath($resourcePath ?? 'resources');
        $this->webPath = $this->resolvePath($webPath ?? 'web');
        $this->varPath = $this->resolvePath($varPath ?? 'var');
        $this->cachePath = $this->resolvePath($cachePath ?? $this->getVarPathFor('cache'));
        $this->logPath = $this->resolvePath($logPath ?? $this->getVarPathFor('logs'));
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
    public function getConfigPathFor(string $path): string
    {
        return $this->configPath . "/$path";
    }

    /** @inheritdoc */
    public function getResourcePathFor(string $path): string
    {
        return $this->resourcePath . "/$path";
    }

    /** @inheritdoc */
    public function getWebPathFor(string $path): string
    {
        return $this->webPath . "/$path";
    }

    /** @inheritdoc */
    public function getVarPathFor(string $path): string
    {
        return $this->varPath . "/$path";
    }

    /** @inheritdoc */
    public function getCachePathFor(string $path): string
    {
        return $this->cachePath . "/$path";
    }

    /** @inheritdoc */
    public function getLogPathFor(string $path): string
    {
        return $this->logPath . "/$path";
    }



    /**
     * @param string $configPath
     * @return self
     * @throws InvalidAbsolutePath
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
     * @throws InvalidAbsolutePath
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
     * @throws InvalidAbsolutePath
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
     * @throws InvalidAbsolutePath
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
     * @throws InvalidAbsolutePath
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
     * @throws InvalidAbsolutePath
     */
    public function withLogPath(string $logPath): self
    {
        $new = clone $this;
        $new->logPath = $this->resolvePath($logPath);

        return $new;
    }



    /**
     * @param string $path
     * @return string
     * @throws InvalidAbsolutePath
     */
    private function resolvePath(string $path): string
    {
        $path = rtrim(trim($path), '/');

        // relative to root path
        if ($path{0} !== '/') {
            $path = $this->rootPath . "/$path";
        }

        $this->assertValidAbsolutePath($path);

        return $path;
    }

    /**
     * @param string $path
     * @throws InvalidAbsolutePath
     */
    private function assertValidAbsolutePath(string $path): void
    {
        // only allow absolute paths that are not system root
        if ($path{0} !== '/' || $path === '/') {
            throw new InvalidAbsolutePath($path);
        }
    }

}
