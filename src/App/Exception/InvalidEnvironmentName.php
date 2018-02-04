<?php

namespace Species\App\Exception;

/**
 * Exception thrown when an invalid environment name is given.
 */
final class InvalidEnvironmentName extends \InvalidArgumentException implements AppException
{

    /** @var string */
    private $invalidEnvironmentName;



    /**
     * @param string $invalidRootEnvironmentName
     */
    public function __construct(string $invalidRootEnvironmentName)
    {
        $this->invalidEnvironmentName = $invalidRootEnvironmentName;
    }



    /** @return string */
    public function getInvalidEnvironmentName(): string
    {
        return $this->invalidEnvironmentName;
    }

}
