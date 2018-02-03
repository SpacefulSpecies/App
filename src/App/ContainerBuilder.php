<?php

namespace Species\App;

use Psr\Container\ContainerInterface;

/**
 * App container builder interface using PHP-DI definitions.
 */
interface ContainerBuilder
{

    /**
     * @param mixed $definitions
     * @see \DI\ContainerBuilder::addDefinitions
     */
    public function addDefinitions($definitions): void;

    /**
     * @return ContainerInterface
     */
    public function build(): ContainerInterface;

}
