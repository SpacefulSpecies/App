<?php

namespace Species\App\Exception;

/**
 * Exception thrown when an invalid app path is given.
 */
final class InvalidPath extends \InvalidArgumentException implements AppException
{

    /** @var string */
    private $invalidPath;



    /**
     * @param string $invalidRootPath
     */
    public function __construct(string $invalidRootPath)
    {
        $this->invalidPath = $invalidRootPath;
    }



    /** @return string */
    public function getInvalidPath(): string
    {
        return $this->invalidPath;
    }

}
