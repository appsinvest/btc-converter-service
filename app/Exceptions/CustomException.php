<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomException extends HttpException
{
    public function __construct()
    {
        parent::__construct($this->code);
    }
}
