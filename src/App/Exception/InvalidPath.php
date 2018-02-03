<?php

namespace Species\App\Exception;

/**
 * Exception thrown when an invalid path is given in app path structure.
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
