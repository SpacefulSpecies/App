<?php

namespace Species\App\Exception;

/**
 * Exception thrown when an invalid app root path is given.
 */
final class InvalidRootPath extends \InvalidArgumentException implements AppException
{

    /** @var string */
    private $invalidRootPath;



    /**
     * @param string $invalidRootPath
     */
    public function __construct(string $invalidRootPath)
    {
        $this->invalidRootPath = $invalidRootPath;
    }



    /** @return string */
    public function getInvalidRootPath(): string
    {
        return $this->invalidRootPath;
    }

}
