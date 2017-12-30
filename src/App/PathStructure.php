<?php

namespace Species\App;

/**
 * Path structure interface for the app.
 */
interface PathStructure
{

    /**
     * An absolute path to the root directory of the app (location of composer, vendor and src dir).
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
     * Get a namespaced path in the config directory.
     *
     * @param string $namespace
     * @return string
     */
    public function getConfigPathFor(string $namespace): string;

    /**
     * Get a namespaced path in the resource directory.
     *
     * @param string $namespace
     * @return string
     */
    public function getResourcePathFor(string $namespace): string;

    /**
     * Get a namespaced path in the web directory.
     *
     * @param string $namespace
     * @return string
     */
    public function getWebPathFor(string $namespace): string;

    /**
     * Get a namespaced path in the var directory.
     *
     * @param string $namespace
     * @return string
     */
    public function getVarPathFor(string $namespace): string;

    /**
     * Get a namespaced path in the cache directory.
     *
     * @param string $namespace
     * @return string
     */
    public function getCachePathFor(string $namespace): string;

    /**
     * Get a namespaced path in the log directory.
     *
     * @param string $namespace
     * @return string
     */
    public function getLogPathFor(string $namespace): string;

}
