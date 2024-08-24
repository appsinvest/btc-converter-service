<?php

/**
 * Api base Controller
 * php version 8.3
 *
 * @category Controllers
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

/**
 * Api base Controller
 * php version 8.3
 *
 * @category Controllers
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
#[
    OA\Info(version: '1.0.0', description: 'JSON API BTC Converter service', title: 'BTC Converter service'),
    OA\Server(url: 'http://localhost', description: 'local server'),
]
#[OA\OpenApi(
    security: [['bearerAuth' => []]]
)]
#[OA\Components(
    securitySchemes: [
        new OA\SecurityScheme(
            securityScheme: 'bearerAuth',
            type: 'http',
            scheme: 'bearer'
        ),
    ]
)]
class Controller extends \App\Http\Controllers\Controller
{
}
