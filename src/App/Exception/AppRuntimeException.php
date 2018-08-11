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
    public static function withReason(\Throwable $e): self
    {
        return new self('', 0, $e);
    }



    /**
     * @return \Throwable
     */
    public function getReason(): \Throwable
    {
        return $this->getPrevious();
    }

}
