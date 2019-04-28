<?php

namespace Species\App\Middleware\Exception;

/**
 * Exception thrown when an invalid CSRF token is given.
 */
final class InvalidCsrfToken extends \InvalidArgumentException
{

}
