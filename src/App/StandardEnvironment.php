<?php

namespace Species\App;

use Species\App\Exception\InvalidEnvironmentName;

/**
 * Standard app environment implementation.
 */
final class StandardEnvironment implements Environment
{

    /** @const string */
    const ENV_KEY_NAME = 'APP_ENV';

    /** @const string */
    const ENV_KEY_DEBUG = 'APP_DEBUG';

    /** @const string */
    const ENV_KEY_CACHE = 'APP_CACHE';



    /** @var string */
    private $name;

    /** @var bool */
    private $debug;

    /** @var bool */
    private $cache;



    /**
     * @return self
     * @throws InvalidEnvironmentName
     */
    public static function forProduction(): self
    {
        return new self('prod', false, true);
    }

    /**
     * @return self
     * @throws InvalidEnvironmentName
     */
    public static function forDevelopment(): self
    {
        return new self('dev', true, false);
    }

    /**
     * @return self
     * @throws InvalidEnvironmentName
     */
    public static function fromPhpEnv(): self
    {
        return new self(
            getenv(self::ENV_KEY_NAME) ?: 'prod',
            getenv(self::ENV_KEY_DEBUG) === '1',
            getenv(self::ENV_KEY_CACHE) !== '0'
        );
    }



    /**
     * @param string $name
     * @param bool   $inDebug
     * @param bool   $hasCaching
     * @throws InvalidEnvironmentName
     */
    public function __construct(string $name, bool $inDebug, bool $hasCaching)
    {
        $this->name = $name;
        $this->debug = $inDebug;
        $this->cache = $hasCaching;

        $this->guardValidEnvironmentName();
    }



    /** @inheritdoc */
    public function __toString(): string
    {
        return $this->name;
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
        return $this->cache;
    }



    /**
     * @param string $name
     * @return self
     * @throws InvalidEnvironmentName
     */
    public function withName(string $name): self
    {
        $new = clone $this;
        $new->name = $name;
        $new->guardValidEnvironmentName();

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
    public function withCaching(bool $caching = true): self
    {
        $new = clone $this;
        $new->cache = $caching;

        return $new;
    }

    /**
     * @return self
     */
    public function withoutCaching(): self
    {
        $new = clone $this;
        $new->cache = false;

        return $new;
    }



    /**
     * @throws InvalidEnvironmentName
     */
    private function guardValidEnvironmentName()
    {
        if (preg_match('/^[\-\_a-z0-9]+$/i', $this->name) === 0) {
            throw new InvalidEnvironmentName($this->name);
        }
    }

}
