<?php

namespace Species\App\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown when app configuration in container is invalid.
 */
final class InvalidContainerConfiguration extends \UnexpectedValueException implements AppException, ContainerExceptionInterface
{

    /**
     * @param ContainerExceptionInterface $previous
     */
    public function __construct(ContainerExceptionInterface $previous)
    {
        parent::__construct('', 0, $previous);
    }

}
