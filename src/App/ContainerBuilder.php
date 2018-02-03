<?php

namespace Species\App;

use Psr\Container\ContainerInterface;

/**
 * App container builder interface.
 */
interface ContainerBuilder
{

    /**
     * @return ContainerInterface
     */
    public function build(): ContainerInterface;

}
