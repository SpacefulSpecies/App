<?php

namespace Species\App\Exception;

/**
 * Exception thrown when an invalid app root path is given.
 */
final class InvalidRootPath extends \InvalidArgumentException implements AppException
{

}
