<?php

namespace Species\App;

use Psr\Container\ContainerInterface;

/**
 * Container builder interface for the app using PHP-DI definitions.
 *
 * @note make sure all required settings and services are in the container for the app!
 */
interface ContainerBuilder
{

    /**
     * @param mixed $definitions
     */
    public function addDefinitions($definitions): void;

    /**
     * @return ContainerInterface
     */
    public function build(): ContainerInterface;

}
