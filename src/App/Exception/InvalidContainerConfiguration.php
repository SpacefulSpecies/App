<?php

namespace Species\App\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown when app configuration in container is invalid.
 */
final class InvalidContainerConfiguration extends \UnexpectedValueException implements AppException, ContainerExceptionInterface
{

    /**
     * @param \Throwable|null $previous = null
     */
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);
    }

}
