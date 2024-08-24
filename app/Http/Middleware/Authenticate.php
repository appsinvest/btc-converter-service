<?php

/**
 * Authenticate middleware
 * php version 8.3
 *
 * @category Middlewares
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use Override;

/**
 * Authenticate middleware
 * php version 8.3
 *
 * @category Middlewares
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{
    #[Override]
    protected function unauthenticated($request, array $guards)
    {
        throw new ForbiddenException(); // for compatibility with this test task
    }
}
