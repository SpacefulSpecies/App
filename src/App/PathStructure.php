<?php

namespace Species\App;

/**
 * App path structure interface.
 */
interface PathStructure
{

    /**
     * An absolute path to the root directory of the app (location of composer, vendor, src, ...).
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
     * An absolute path to the directory where static resource files are stored.
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
     * Get a path inside the config directory.
     *
     * @param string $path
     * @return string
     */
    public function getConfigPathFor(string $path): string;

    /**
     * Get a path inside the resource directory.
     *
     * @param string $path
     * @return string
     */
    public function getResourcePathFor(string $path): string;

    /**
     * Get a path inside the web directory.
     *
     * @param string $path
     * @return string
     */
    public function getWebPathFor(string $path): string;

    /**
     * Get a path inside the var directory.
     *
     * @param string $path
     * @return string
     */
    public function getVarPathFor(string $path): string;

    /**
     * Get a path inside the cache directory.
     *
     * @param string $path
     * @return string
     */
    public function getCachePathFor(string $path): string;

    /**
     * Get a path inside the log directory.
     *
     * @param string $path
     * @return string
     */
    public function getLogPathFor(string $path): string;

}
