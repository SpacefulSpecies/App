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
     * @param Environment   $environment
     * @param PathStructure $pathStructure
     * @return ContainerInterface
     */
    public static function autoBuild(Environment $environment, PathStructure $pathStructure): ContainerInterface;



    /**
     * @param mixed $definitions
     */
    public function addDefinitions($definitions): void;

    /**
     * @return ContainerInterface
     */
    public function build(): ContainerInterface;

}
