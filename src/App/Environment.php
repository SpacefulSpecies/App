<?php

namespace Species\App;

/**
 * App environment interface.
 */
interface Environment
{

    /**
     * Casting to string gives the app environment name.
     *
     * @return string
     * @see getName()
     */
    public function __toString(): string;



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
     * Whether the app environment has caching.
     *
     * @return bool
     */
    public function hasCaching(): bool;

    /**
     * Whether the app is running in cli.
     *
     * @return bool
     */
    public function isCommandLine(): bool;

    /**
     * @return bool
     */
    public function hasApcuCache(): bool;

}
