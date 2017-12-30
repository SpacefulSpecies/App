<?php

namespace Species\App\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Invalid container config exception.
 */
final class InvalidContainerConfig extends AppException implements ContainerExceptionInterface
{

    /**
     * @param ContainerExceptionInterface $previous
     */
    public function __construct(ContainerExceptionInterface $previous = null)
    {
        parent::__construct('', 0, $previous);
    }

}
