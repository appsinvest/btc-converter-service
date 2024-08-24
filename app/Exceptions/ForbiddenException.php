<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends CustomException
{
    protected $code = Response::HTTP_FORBIDDEN;
}
