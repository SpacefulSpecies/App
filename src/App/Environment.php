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
     * Whether the app environment has caching.
     *
     * @return bool
     */
    public function hasCaching(): bool;

}
