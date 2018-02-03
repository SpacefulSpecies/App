<?php

namespace Species\App\Exception;

/**
 * Exception thrown when the app could not run or process a request.
 */
final class UnableToRunApp extends \RuntimeException implements AppException
{

    /**
     * @param \Throwable $previous
     */
    public function __construct(\Throwable $previous)
    {
        parent::__construct('', 0, $previous);
    }

}
