<?php

/**
 * AuthTokenPreProcess middleware
 * php version 8.3
 *
 * @category Middlewares
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthTokenPreProcess middleware
 * php version 8.3
 *
 * @category Middlewares
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
class AuthTokenPreProcess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //        if ($request->is('api/v1/*')) {
        $token = $request->bearerToken();
        if (!$token) {
            $token = $request->get('token');
        }
        $token = Redis::connection()->get($token);


        $request->headers->set('Authorization', 'Bearer ' . $token);

        //        }
        return $next($request);
    }
}
