<?php

namespace Species\App\Exception;

/**
 * Exception thrown when an invalid app path is given.
 */
final class InvalidAbsolutePath extends \InvalidArgumentException implements AppException
{

}
