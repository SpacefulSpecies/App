<?php

namespace Species\App\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown when app configuration in container is invalid.
 */
final class InvalidContainerConfiguration extends \UnexpectedValueException implements AppException, ContainerExceptionInterface
{

    /**
     * @param \Throwable $e
     * @return self
     */
    public static function withReason(\Throwable $e): self
    {
        return new self($e);
    }



    /**
     * @param \Throwable $reason
     */
    private function __construct(\Throwable $reason)
    {
        parent::__construct('', 0, $reason);
    }

}
