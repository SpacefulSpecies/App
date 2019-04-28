<?php

namespace Species\App\Exception;

/**
 * Exception thrown when the app could not run or process a request.
 */
final class AppRuntimeException extends \RuntimeException implements AppException
{

    /**
     * @param \Throwable $e
     * @return self
     */
    public static function WithReason(\Throwable $e): self
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
