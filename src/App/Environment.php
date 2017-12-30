<?php

namespace Species\App;

/**
 * App environment interface.
 */
interface Environment
{

    /**
     * The name of the app environment, eg: prod, dev, staging...
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Whether the app environment runs in debug mode.
     *
     * @return bool
     */
    public function inDebug(): bool;

    /**
     * Whether the app environment has file caching.
     *
     * @return bool
     */
    public function hasCaching(): bool;



    /**
     * An absolute path to the root directory of the app (location of composer and src dir).
     *
     * @return string
     */
    public function getRootPath(): string;



    /**
     * An absolute path to the directory where configuration files are stored.
     *
     * @return string
     */
    public function getConfigPath(): string;

    /**
     * An absolute path to the directory where static project files are stored.
     *
     * @return string
     */
    public function getResourcePath(): string;

    /**
     * An absolute path to the public web directory.
     *
     * @return string
     */
    public function getWebPath(): string;

    /**
     * An absolute path to a writable directory for files that are manipulated during runtime.
     *
     * @return string
     */
    public function getVarPath(): string;

    /**
     * An absolute path to a writable directory where cache files are stored.
     *
     * @return string
     */
    public function getCachePath(): string;

    /**
     * An absolute path to a writable directory where log files are stored.
     *
     * @return string
     */
    public function getLogPath(): string;



    /**
     * Create a namespaced environment path in the var directory.
     * /var-path/env-name/namespace
     *
     * @param string $namespace
     * @return string
     */
    public function createVarPathFor(string $namespace): string;

    /**
     * Create a namespaced environment path in the cache directory.
     * /cache-path/env-name/namespace
     *
     * @param string $namespace
     * @return string
     */
    public function createCachePathFor(string $namespace): string;

    /**
     * Create a namespaced environment path in the log directory.
     * /log-path/env-name/namespace
     *
     * @param string $namespace
     * @return string
     */
    public function createLogPathFor(string $namespace): string;

}
