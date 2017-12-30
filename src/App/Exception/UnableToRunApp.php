<?php

namespace Species\App\Exception;

/**
 * Unable to run app exception.
 */
final class UnableToRunApp extends AppException
{

    /**
     * @param \Throwable $previous
     */
    public function __construct(\Throwable $previous)
    {
        parent::__construct('', 0, $previous);
    }

}
