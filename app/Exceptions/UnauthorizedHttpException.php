<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedHttpException extends CustomException
{
    protected $code = Response::HTTP_UNAUTHORIZED;
}
