<?php

namespace Species\App;

/**
 * Environment interface for an app.
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
     * Whether the app environment uses caching.
     *
     * @return bool
     */
    public function usesCaching(): bool;

}
