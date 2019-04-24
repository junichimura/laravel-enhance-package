<?php


namespace Junichimura\LaravelEnhancePackage\Eloquent\Exceptions;

use \InvalidArgumentException as SuperException;
use Throwable;


class InvalidArgumentException extends SuperException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}